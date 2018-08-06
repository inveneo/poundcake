<?php
/**
 * Controller for IP Spacees.
 *
 * This is a very basic controller to add/view/update/delete IP Spacees.
 * Note: All heavy lifting (binary math) is performed in a SQL trigger.
 * 
 * Developed against CakePHP 2.3.0 and PHP 5.4.x.
 *
 * Copyright 2012, Inveneo, Inc. (http://www.inveneo.org)
 *
 * Licensed under GNU General Public License.
 * 
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2013, Inveneo, Inc. (http://www.inveneo.org)
 * @author        Inveneo Dev Team <info@inveneo.org>
 * @link          http://www.inveneo.org
 * @package       app.Controller
 * @since         IpSpaceController was introduced in Poundcake v3.0.0
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class IpSpacesController extends AppController {

    //const MAX_CIDRS = 32;    
    
    /*
     * Main listing for all IpSpaces
     */
    public function index( $id = null ) {
        $project_id = $this->Session->read('project_id' );        
        $ip_spaces = $this->IpSpace->find('threaded', array( 
           // 'order' => array('IpSpace.lft'),
           'conditions' => array('IpSpace.project_id' => $project_id),
            // sort by ip address in case some were deleted and then
            // re-added, which would otherwise make them out of sequence
           'order' => array('IpSpace.ip_address')
        ));
        
        // this will be 0 if there are no *internal* IP spaces set for this project
        $internal_space_count = $this->IpSpace->find('count', array( 
           'conditions' => array('IpSpace.project_id' => $project_id,'cidr <' => 32),
        ));
        
        $this->set(compact('ip_spaces', 'project_id','internal_space_count', 'parent_cidr')); 
    }
    
    /*
     * Find any devices that are assigned to the IP -- Note we have to search
     * all possible models
     */
    public function find( $id ) {
        $search_models = array( 'NetworkRadio', 'NetworkRouter', 'NetworkSwitch' );
        foreach ( $search_models as $model ) {
            $this->loadModel( $model );
            $item = $this->$model->findByIpSpaceId( $id );
        }
        if ( $item != null ) {
            // if found, send them on their way
            // Note: Inflector::pluralize is a great function to pluralize the
            // model name, NetworkSwitch becomes NetworkSwitches, NetworkRadio
            // becomes NetworkRadios, etc.
            $this->redirect( array( 'controller' => Inflector::pluralize( $model ), 'action' => 'view', $item[$model]['id'] ));
        }
        $this->Session->setFlash('Error!  No devices found.');
        $this->redirect(array('action' => 'index'));
    }
    
    /*
     * Add a new IpSpace
     */
    public function add( $parent_id = null, $fill = false ) {
        $project_id = $this->Session->read('project_id');
        
        $dbg = 0; // my debugging
        if ( ( $this->request->is('post') ) || ( $fill ) ) {
            
//            print_r( $this->request->data );die;
            $new_cidr = $this->request->data['IpSpace']['cidr'];
            $parent_id = $this->request->data['IpSpace']['parent_id'];
            $children = 0;
            
            // this first case is when the user has added an ISP-assigned
            // /32 that is outside the scope of the project's private IP Space
            if (( $new_cidr == 32 ) && ( $parent_id == null )) {
                $parent_ip = $this->request->data['IpSpace']['ip_address'];
            } else {
                // load the parent IP Space
                $this->IpSpace->recursive = -1;
                
                $this->IpSpace->id = $parent_id;
                $this->IpSpace->read();
                // number of existing IP Spaces that already *directly* hang
                // off the parent node
                //$children = $this->IpSpace->childCount($parent_id, true);
                $children = $this->IpSpace->childCountMatchingCidr($parent_id, true, $new_cidr);
                $parent_ip = $this->IpSpace->field('ip_address');
            }

            if ( $project_id == null ) {
                $this->IpSpace->create();
                if ($this->IpSpace->save($this->request->data)) {
                    $this->Session->setFlash('The IP Space has been saved.');
                    $this->redirect(array('action' => 'index'));
                }
             
            } elseif (( $children == 0 ) && ( $new_cidr < 32 ) ) {
                // such as adding a /29 below a /8
                $new_ip = $parent_ip;
            } elseif (( $children == 0 ) && ( $new_cidr == 32 ) ) {
                // adding a /32
                $new_ip = $parent_ip;
                $new_ip++;
            } else {                
                $parent_cidr = $this->IpSpace->field('cidr');
//                echo $parent_ip.'<br>';
                $range = $this->getIpRange( $parent_ip, $parent_cidr );
                
                // calculate the maximum possible number of network's that can
                // be created within the parent network (based on the parent's
                // CIDR)
                $pos_nets = $this->calculatePossibleNetworks( $new_cidr, $parent_cidr );
//                echo "new_cidr=$new_cidr <br>";
//                echo "children=$children <br>";
//                echo "pos_nets=$pos_nets <br>";
                // the flaw in this logic here is that if you have, say:
                // 1 /25
                // 9 /29s hanging off that /25
                // then you try to add a /27 under that /25, childCount will be
                // greater than possible networks, even though this is possible
                // need to revisit a custom childCount function that takes a
                // paremeter
//                echo '<pre>';
//                echo "children: $children<BR>";
//                echo "children2: $children2<BR>";
//                echo "pos_nets: $pos_nets<BR>";
//                echo '</pre>';
//                die;
//                
                if ( $children >= $pos_nets ) {
                    $this->Session->setFlash('Error!  Parent subnet is a /'.$parent_cidr.', Maximum possible subnets reached.');
                    $this->redirect(array('action' => 'index', $parent_id ));
                }
                
                if ( $dbg ) {
                    echo( "A /$new_cidr in a /$parent_cidr has $pos_nets possible networks<br>" );
                    echo( "There are currently $children off the parent node<br>" );
                    echo( "Start at: ".long2ip($range[0])."<br>" );
                    echo( "End at: ".long2ip($range[1])."<br><br>" );
                }
                
                $i=0;
                $start_at = $range[0];
                $end_at = $range[1];
                $jump_by = ( $end_at - $start_at ) / $pos_nets;
                $noncontig = false;
                while ( ( $start_at < $end_at ) && ( !$noncontig ) ) {   
                    if ( $dbg ) {
                        echo( " Check if ".long2ip($start_at)." is already allocated...<br>");
                    }
                            
                    // if ( $this->IpSpace->findAllByIpAddress( $start_at ) != null ) {
                    if ( $this->checkIfIpAllocated( $start_at, $project_id, $dbg ) != null ) {
                        if ( $dbg ) {
                            echo( " Jumping by: ".long2ip($jump_by)."<br>");
                        }
                        $start_at += $jump_by;
                    } else {
                        if ( $dbg ) {
                            echo "Non-contiguous block, exiting loop early<BR>";
                        }
                        $noncontig = true;
                    }
                    
                    $i++;
                }
                
                if ( $dbg ) {
                    echo( " Ending IP: ".long2ip($start_at)."<br>");
                }
                
                $new_ip = long2ip( $start_at );                
            }
            
            // /32s cannot end with .0 so add one
            if ( $this->ipEndsWithZero( $new_ip ) && ( $new_cidr == 32 )) {
                $new_ip = long2ip( ip2long( $new_ip ) + 1 );
            }
            if ( $dbg ) {
                echo( " New IP: ".$new_ip."<br>");
            }
            
            $this->request->data['IpSpace']['ip_address'] = $new_ip;
                    
            $this->IpSpace->create();
            if ( $this->IpSpace->save( $this->request->data )) {
               if ( !$fill ) {
                    $this->Session->setFlash('The IP Space has been saved.');
                    $this->redirect(array('action' => 'index/'.$parent_id));
                }
            } else {
                $this->Session->setFlash('Error!  The IP Space could not be saved. Please, try again.');
//                $log = $this->IpSpace->getDataSource()->getLog(false, false);
//                debug($log);    
            }
        }
        
        if ( $parent_id > 0 ) {
            $this->IpSpace->recursive = -1;
            $this->IpSpace->id = $parent_id;
            $this->IpSpace->read();
            $parent_ip = $this->IpSpace->field('ip_address');
            $parent_cidr = $this->IpSpace->field('cidr');            
        } else {
            $parent_cidr = 7;
        }
//        var_dump($parent_cidr);
        if ( $parent_cidr >= self::MAX_CIDRS ) {
            $this->Session->setFlash('Error!  No more spaces can be added.');
            $this->redirect(array( 'action' => 'index/'.$parent_id ));
        }
        
//        $n_gateways = $this->getGatewayAddresses( $parent_id );
//        if ( $n_gateways > 0 ) {
//            $host = true;
//        } else {
//            $host = false;
//        }
//        $this->getCidrs( $parent_cidr, $host );
        $this->getGatewayAddresses( $parent_id );
        $this->getCidrs( $parent_cidr );
        $this->set(compact( 'parent_id', 'ip_address', 'project_id' ));
    }
    
    private function getGatewayAddresses( $parent_id ) {
        $gw_addresses = $this->IpSpace->find('list', array( 
            'fields' => array('IpSpace.ip_address'),
            // 'order' => array('IpSpace.lft'),
            'conditions' => array(
                'IpSpace.parent_id' => $parent_id,
                'IpSpace.cidr' => 32,
            ),
             // sort by ip address in case some were deleted and then
             // re-added, which would otherwise make them out of sequence
            'order' => array('IpSpace.ip_address')
        ));
//        var_dump( $gw_addresses ); 
        $this->set(compact('gw_addresses'));
//        return count( $gw_addresses );
    }
    
    public function edit($id = null) {
        $this->IpSpace->id = $id;
        if (!$this->IpSpace->exists()) {
            throw new NotFoundException('Invalid IP Space');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->IpSpace->save($this->request->data)) {
                $this->Session->setFlash('The IP Space has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The IP Space could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->IpSpace->read(null, $id);
        }
        
        if ( $this->request->data['IpSpace']['cidr'] == 32 ) {
            $this->getGatewayAddresses( $this->request->data['IpSpace']['parent_id'] );
        }
        
        $ip_address = $this->request->data['IpSpace']['ip_address'];
        $this->set(compact('ip_address'));
    }
    
    public function fill( $id = null, $number = 254 ) {
        if ( $number > 254 ) {
            $number = 254;
        }
//        var_dump( $number );
//        die;
        
        $this->IpSpace->id = $id;
        if (!$this->IpSpace->exists()) {
            throw new NotFoundException('Invalid IP Space');
        }
        $ip_space = $this->IpSpace->read();
        
        /*
        // calculate the number of possible hosts in this cidr
        $pos_nets = $this->calculatePossibleNetworks( 32, $ip_space['IpSpace']['cidr'] );
        
        // if they click "fill" on a /8 it will take forever, so limit
        // it to 254 hosts
        if ( $pos_nets > 254 ) {
            $pos_nets = 254;
        }
        */
        
        $this->request->data['IpSpace']['cidr'] = '32';
        $this->request->data['IpSpace']['parent_id'] = $id;
        $this->request->data['IpSpace']['project_id'] = $this->Session->read( 'project_id' );
            
        for ( $n = 0; $n < $number; $n++ ) {
            $this->request->data['IpSpace']['name'] = 'Unused';
            $this->add( $id, true );
        }

        $this->Session->setFlash('The IP Space has been filled.');
        $this->redirect(array('action' => 'index'));
    }

//    private function getIpRange2($from, $to) {
//        $start = ip2long($from);
//        $end = ip2long($to);
//        $range = range($start, $end);
//        var_dump( $range );
//        return array_map('long2ip', $range);
//    }
    
    /*
     * Add a single /32 IP -- basically identical to the add except that we
     * have the cidr hard coded in the form, and give the user the option to
     * specify an IP
     */
    public function ip() {
        $project_id = $this->Session->read( 'project_id' );
        if ($this->request->is('post')) {
            $this->add();
        }
        $this->set(compact( 'project_id' ));
    }
    
    private function ipEndsWithZero( $ip_addr ) {
        //var_dump("Checking".$ip_addr);
        $ret = false;
        if(preg_match('/.*\.0$/', $ip_addr)) 
            $ret = true;
        return $ret;
    }
    
    private function checkIfIpAllocated( $ip_address, $project_id, $dbg ) {
        // would have liked to use findByIpAddress but it cannot take conditions
        // and we need to check by project_id
        if ( $dbg ) {
            echo "  Searching for ".long2ip($ip_address)." in project $project_id<BR>";
        }
        $ip = $this->IpSpace->find('first', array(
            'conditions' => array(
              'ip_address' => $ip_address,
              'project_id' => $project_id
            )
          ));
        $ret = false;
        if ( $ip != null ) {
            if ( $dbg ) {
                echo "*** Found!<BR>";
            }
            $ret = true;
        }        
        return $ret;
    }
    
    /*
     * Delete an existing IpSpace
     */
    public function delete($id = null) {
        /*
        We are not a post link!  This is non-standard CakePHP...
        
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        */
        
        $this->IpSpace->id = $id;
        $this->IpSpace->read();
        $parent_id = $this->IpSpace->field('parent_id');
        
        if (!$this->IpSpace->exists()) {
            throw new NotFoundException('Invalid IP Space');
        }
        if ($this->IpSpace->delete()) {
            $this->Session->setFlash('IP Space deleted.');
            $this->redirect(array('action' => 'index', $parent_id ));
        }
        $this->Session->setFlash('Error!  IP Space was not deleted.');
        $this->redirect(array('action' => 'index'));
    }
    
    public function root( $parent_id = null ) {
        $project_id = $this->Session->read( 'project_id' );
        $project_name = $this->Session->read( 'project_name' );
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->IpSpace->create();
            if ($this->IpSpace->save($this->request->data)) {
                $this->Session->setFlash('The Root IP Space has been created.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The Root IP Space could not be saved. Please, try again.');
            }
        }
        $this->getCidrs( 7 );
        $this->set(compact( 'project_id', 'project_name' ));
    }
  
    /**
    * get the first ip and last ip from cidr(network id and mask length)
    * i will integrate this function into "Rong Framework" :)
    * @author admin@wudimei.com
    * @param string $cidr 56.15.0.6/16 , [network id]/[mask length]
    * @return array $ipArray = array( 0 =>"first ip of the network", 1=>"last ip of the network" );
    *                         Each element of $ipArray's type is long int,use long2ip( $ipArray[0] ) to convert it into ip string.
    * example:
    * list( $long_startIp , $long_endIp) = getIpRange( "56.15.0.6/16" );
    * echo "start ip:" . long2ip( $long_startIp );
    * echo "<br />";
    * echo "end ip:" . long2ip( $long_endIp );
    */
    private function getIpRange( $ip, $mask ) {
        // list($ip, $mask) = explode('/', $cidr);
//        var_dump( $ip );
        $maskBinStr =str_repeat("1", $mask ) . str_repeat("0", 32-$mask );      //net mask binary string
        $inverseMaskBinStr = str_repeat("0", $mask ) . str_repeat("1",  32-$mask ); //inverse mask

        $ipLong = ip2long( $ip );
        $ipMaskLong = bindec( $maskBinStr );
        $inverseIpMaskLong = bindec( $inverseMaskBinStr );
        $netWork = $ipLong & $ipMaskLong;  

        // $start = $netWork + 1; // ignore network ID (eg: 192.168.1.0)
        // $end = ( $netWork | $inverseIpMaskLong ) -1 ; // ignore brocast IP (eg: 192.168.1.255)
        $start = $netWork; // ignore network ID (eg: 192.168.1.0)
        $end = ( $netWork | $inverseIpMaskLong ) + 1;
        
//        echo "<BR>netWork:  ";
//        print_r( long2ip( $netWork ) );
//        echo "<BR>inverseIpMaskLong:  ";
//        print_r( long2ip( $inverseIpMaskLong ) );
//        echo "<BR>Start:  ";
//        print_r( long2ip( $start ) );
//        echo "<BR>End:  ";
//        print_r( long2ip( $end ) );
//        die;
        return array( $start, $end );
  }
  
    private function calculatePossibleNetworks( $new_cidr, $parent_cidr ) {
        $n = $new_cidr - $parent_cidr;
        $pos_nets = pow( 2, $n );
        return $pos_nets;
    }
  
    public function isAuthorized($user) {
        
        $allowed = array( "index", "view" );
        if ( in_array( $this->action, $allowed )) {
            return true;
        }
        
        return parent::isAuthorized($user);
    }
}

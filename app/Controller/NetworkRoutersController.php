<?php
/**
 * Controller for network routers.
 *
 * This is a controller to add/view/update/delete network routers.
 * 
 * Developed against CakePHP 2.2.3 and PHP 5.4.x.
 *
 * Copyright 2012, Inveneo, Inc. (http://www.inveneo.org)
 *
 * Licensed under GNU General Public License.
 * 
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2012, Inveneo, Inc. (http://www.inveneo.org)
 * @author        Inveneo Dev Team <info@inveneo.org>
 * @link          http://www.inveneo.org
 * @package       app.Controller
 * @since         NetworkRoutersController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('NetworkDeviceController', 'Controller');

class NetworkRoutersController extends NetworkDeviceController {

    /*
     * PoundcakeHTML makes de-links hyperlinks for view-only users
     */
    var $helpers = array('PoundcakeHTML');
    
    /*
     * Custom pagination, sort order on index listing
     */
    public $paginate = array(
        'limit' => 20, // default limit also defined in AppController
        'order' => array(
            'NetworkRouter.name' => 'asc'
        )
    );
    
    /*
     * Main listing for all NetworkRouters
     */
    public function index() {
        // begin search stuff
        $name_arg = "";
        if ( isset($this->request->data['NetworkRouter']['name'] )) {
            $name_arg = str_replace('*','%',$this->request->data['NetworkRouter']['name']);
        }
        
        // if no argument was passed, default to a wildcard
        if ($name_arg == "") {
            $name_arg = '%';
        }
        
        $conditions = array(
            'AND' => array(
                'NetworkRouter.name LIKE' => $name_arg,
                // only show routers for the currently selected project
                // saved as a session variable
                'Site.project_id' => $this->Session->read('project_id')
            ),
        );
        
        $this->paginate = array(
            'NetworkRouter' => array(
                // limit is the number per page 
                'limit' => 20,
                'conditions' => $conditions,
                'order' => array(
                    'NetworkRouter.name' => 'asc',
                ),
            ));
        //$this->NetworkRouter->recursive = 1;
        $networkrouters = $this->paginate('NetworkRouter');
//        debug( $networkrouters );die;
        //$this->set('networkrouters',$data);
        $this->set(compact('networkrouters'));
    }

    public function view($id = null) {
        $this->NetworkRouter->id = $id;
        if (!$this->NetworkRouter->exists()) {
            throw new NotFoundException('Invalid router');
        }
         
        $this->checkSnmp(); // check if there is custom SNMP data on this item
        $networkrouter = $this->NetworkRouter->read(null, $id);
        
        // retrieve the username of the person who provisioned this device
        if (isset($this->NetworkRouter->data['NetworkRouter']['foreign_id'])) {
            $this->loadModel('User');
            $this->User->recursive = -1;
            $this->User->id = $this->NetworkRouter->data['NetworkRouter']['provisioned_by'];
            $user = $this->User->read();
            $provisioned_by_name = $user['User']['username'];
            $checked = $this->NetworkRouter->data['NetworkRouter']['checked'];
        } else {
            $provisioned_by_name = "";
            $checked = "";
        }
        
        $this->getMonitoringSystemLink( $this->NetworkRouter->data['NetworkRouter']['node_id'] );
        
        $network_interface_types = $this->NetworkRouter->RouterType->RouterTypeNetworkInterfaceTypes->find('all',
                array(
                    'conditions' => array(
                        'RouterTypeNetworkInterfaceTypes.router_type_id' => $this->NetworkRouter->field('router_type_id')
                    ),
                    'contains' => true,
                    'fields' => array(
                        'RouterTypeNetworkInterfaceTypes.id',
                        'NetworkInterfaceType.name',
                        'RouterTypeNetworkInterfaceTypes.number',
                    ) 
                )
        );
        
        $if_array = array();
        $this->loadModel('NetworkInterfaceIpSpaces');
        $interfaces_tmp = $this->NetworkInterfaceIpSpaces->findAllByNetworkRouterId( $id );
        
        $this->loadModel('IpSpace');
        foreach( $interfaces_tmp as $if ) {
            $if_name = $this->getIfName( $if['NetworkInterfaceIpSpaces']['network_interface_type_id'] );
            if ( $if_name != "" ) {
//                $ip_space = $this->IpSpaces->findById( $if['NetworkInterfaceIpSpaces']['ip_space_id'] );
                $ip_space = $this->IpSpace->read(null, $if['NetworkInterfaceIpSpaces']['ip_space_id'] );
                // show empty IP addresses as 0.0.0.0/32 unless there's a defined cidr
                $ip_address = '0.0.0.0';
                $cidr = 0;
                $parent_cidr = '32';
                if ( isset( $ip_space['IpSpace'] ) && ( $if['NetworkInterfaceIpSpaces']['ip_space_id'] > 0 ) ) {
                    // $cidr = $ip_space['IpSpace']['cidr'];
                    $parent_cidr = $ip_space['IpSpace']['parent_cidr']; // parent_cidr is virtual field
//                    echo $parent_cidr;
//                    echo '<pre>';
//                    print_r( $if['NetworkInterfaceIpSpaces']['ip_space_id'] );
//                    print_r( $ip_space['IpSpace'] );
                    $ip_address = $ip_space['IpSpace']['ip_address'];
                
//                print_r($if['NetworkInterfaceIpSpaces']);die;
                    array_push( $if_array, array (
                        'if_name' => $if_name.$if['NetworkInterfaceIpSpaces']['if_number'],                
                        'ip_address' => $ip_address.'/'.$parent_cidr,
                        'if_primary' => $if['NetworkInterfaceIpSpaces']['if_primary']
                    ) );
                }
            }
            sort( $if_array ); // so the interfaces appear in order by number and name
        }
        
        $this->set(compact( 'id','networkrouter', 'if_array','network_interface_types', 'provisioned_by_name', 'checked' ));        
    }

    public function add() {
        $this->getRouterTypes();
        $this->getAllSitesForProject();
        
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->NetworkRouter->create();
            if ($this->NetworkRouter->save($this->request->data)) {
                // see comments in edit
                $this->Site->id = $this->request->data['Site']['id']; 
                $this->Site->saveField('network_router_id', $this->NetworkRouter->id);
                
                $this->Session->setFlash('The router has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The router could not be saved. Please, try again.');
            }
        }
        $this->getSnmpTypes();
        $project_id = $this->Session->read('project_id');
        parent::getIpSpaces( $project_id );
        $this->set(compact('project_id'));
    }

    public function edit($id = null) {
        $this->NetworkRouter->id = $id;
        
        if (!$this->NetworkRouter->exists()) {
                throw new NotFoundException('Invalid router');
        }
        
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->NetworkRouter->save($this->request->data)) {
                // why do have to do this manually?
                // I have tried save/saveAll/saveAssociated/array('deep' => true)
                // either I have the relationship between Site and Router totally
                // wrong or there's some other issue
                // I am manually saving the radio
                $this->loadModel('Site');
                $this->Site->id = $this->request->data['NetworkRouter']['old_site_id'];
                $this->Site->saveField('network_router_id', null, false );
                $this->Site->id = $this->request->data['Site']['id']; 
                $this->Site->saveField('network_router_id', $this->NetworkRouter->id, false);
                
                $this->Session->setFlash('The router has been saved.');
                $this->redirect(array('action' => 'view',$this->NetworkRouter->id));
            } else {
                $this->Session->setFlash('Error!  The router could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->NetworkRouter->read(null, $id);
        }
        $old_site_id = $this->NetworkRouter->data['Site']['id'];
        $this->getAllSitesForProject();
        $this->getRouterTypes();
        $this->getSnmpTypes();
        $project_id = $this->Session->read('project_id');
        parent::getIpSpaces( $project_id );
        $this->set(compact('old_site_id','project_id','id'));
    }

    function getRouterTypes() {
        $this->set('routertypes',$this->NetworkRouter->RouterType->find('list',
            array(
                'order' => array(
                    'RouterType.manufacturer ASC'
            )))
        );
    }
    
    /*
     * Save an array of SNMP types the project may be using
     */
    private function getSnmpTypes() {
        $this->set('snmptypes',$this->NetworkRouter->SnmpType->find('list',
            array(
                'order' => array(
                    'SnmpType.name'
            )))
        );
    }
    /*
     * Delete an existing NetworkRouter
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->NetworkRouter->id = $id;
        if (!$this->NetworkRouter->exists()) {
            throw new NotFoundException('Invalid router');
        }
        if ($this->NetworkRouter->delete()) {
            $this->Session->setFlash('Router deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Router was not deleted.');
        $this->redirect(array('action' => 'index'));
    }

    /*
     * Provisions the device into the monitoring system.
     * @see Identical functions in NetworkSwitch, NetworkRouter, NetworkRadio
     */
    public function provision( $id = null ) {        
        $this->NetworkRouter->read(null, $id);
        
        // don't allow provisioning if the project is set to read-only integration
        $ro = $this->NetworkRouter->Site->Project->field('read_only');
        if ( !$ro ) {
            $name = $this->NetworkRouter->data['NetworkRouter']['name'];
            $ip_addr = $this->NetworkRouter->data['NetworkRouter']['ip_address'];
            $foreign_id = parent::provisionNode( $name, $ip_addr, true );
            if ( !is_null( $foreign_id ) ) {
                $this->NetworkRouter->saveField('foreign_id', $foreign_id);
                $this->NetworkRouter->saveField('provisioned_on', date("Y-m-d H:i:s") );
                $this->NetworkRouter->saveField('provisioned_by', $this->Auth->user('id') );
                $this->Session->setFlash('Provisioned router.  Foreign ID '.$foreign_id);
            } else {
                $this->Session->setFlash('Error!  Problem provisioning router.');
            }
        } else {
            $this->Session->setFlash('Error!  Project is set for read-only integration with monitoring system.');
        }
        
        $this->redirect(array('action' => 'view',$this->NetworkRouter->id));
    }
    
    /*
     * Save an array of alarms or alerts from the monitoring system
     */
    public function alarms( $id ) {
        $this->NetworkRouter->recursive = -1;
        $this->NetworkRouter->id = $id;
        $this->NetworkRouter->read();
        $alarms = parent::getAlarms();
        $name = $this->NetworkRouter->data['NetworkRouter']['name'];
        $this->set(compact( 'alarms', 'id', 'name' ));
    }
    
    /*
     * Save an array of events from the monitoring system -- is basically
     * identical to alarms()
     */
    public function events( $id ) {
        $this->NetworkRouter->recursive = -1;
        $this->NetworkRouter->id = $id;
        $this->NetworkRouter->read();
        $events = parent::getEvents();
        $name = $this->NetworkRouter->data['NetworkRouter']['name'];
        $this->set(compact( 'events', 'id', 'name' ));
    }
    
    /*
     * Save an array of performance graphs from the monitoring system
     */
    public function graphs( $id = null ) {
        $this->NetworkRouter->id = $id;
        $this->NetworkRouter->read();
        $name = $this->NetworkRouter->data['NetworkRouter']['name'];
        $this->getPerformanceGraphs( $this->NetworkRouter->data['NetworkRouter']['node_id'] );
        $this->set(compact( 'id', 'name' ));
    }
    
    public function interfaces( $id = null, $router_type_network_interface_type_id = null, $number = null ) {
        $this->loadModel('NetworkInterfaceIpSpace');
        $this->loadModel('RouterTypeNetworkInterfaceTypes');
        
        $conditions= array(
                'NetworkInterfaceIpSpace.network_router_id' => $id,
                'NetworkInterfaceIpSpace.network_interface_type_id' => $router_type_network_interface_type_id,
            );
        
//        $network_interface_ip_space = $this->NetworkInterfaceIpSpace->findByNetworkRouterId( $id );
//        var_dump( $network_interface_ip_space );
        
        
//        if (!$this->NetworkRouter->exists()) {
//            throw new NotFoundException('Invalid router');
//        }
        $network_interface_ip_space = array();
        if ($this->request->is('post') || $this->request->is('put')) {
            
            // get the id of the interface marked as primary
            $if_primary = $this->request->data['NetworkInterfaceIpSpace']['if_primary'];
            $this->request->data['NetworkInterfaceIpSpace'][$if_primary]['if_primary'] = 1;
            unset($this->request->data['NetworkInterfaceIpSpace']['if_primary']);
            
//            echo '<pre>';
//            print_r($this->request->data);
//            echo '</pre>';
            // clear out existing mappings
            $conditions = array(
                'AND' => array(
                    'NetworkInterfaceIpSpace.network_router_id' => $id,
                    'NetworkInterfaceIpSpace.network_interface_type_id' => $router_type_network_interface_type_id
                ),
            );
            $this->NetworkInterfaceIpSpace->deleteAll( $conditions );
            
            
            if ($this->NetworkInterfaceIpSpace->saveAll( $this->request->data['NetworkInterfaceIpSpace'] )) {
                $this->Session->setFlash('Saved interface configuration.');
                $this->redirect(array('action' => 'view',$id,null));
            } else {
                $this->Session->setFlash('Error!  The interfaces could not be saved. Please, try again.');
            }
        } else {
//            $this->NetworkRouter->contain('NetworkInterfaceIpSpace');
//            $this->request->data = $this->NetworkRouter->read(null, $id);
//            $network_interface_ip_space = $this->NetworkInterfaceIpSpace->findByNetworkRouterId( $id );
            $network_interface_ip_space = $this->NetworkInterfaceIpSpace->find('all',array('conditions'=>$conditions));
        }
        
        if ( count( $network_interface_ip_space ) == 0 ) {
            $network_interface_types = $this->RouterTypeNetworkInterfaceTypes->find('all',
                array(
                    'conditions' => array(
                        'RouterTypeNetworkInterfaceTypes.id' => $router_type_network_interface_type_id,
                    ),
                    'contains' => true
                )
            );
//            echo '<pre>';
//            var_dump( $network_interface_types );
//            echo '</pre>';
            
            $interfaces = array();
            for ( $n = 0; $n < $network_interface_types[0]['RouterTypeNetworkInterfaceTypes']['number']; $n++ ) {
                $array = array();
                array_push($interfaces, array(
                    'NetworkInterfaceIpSpace' => array(
                        'id' => null,
                        'if_number' => $n,
                        'network_router_id' => $id,
                        'network_router_id' => null,
                        'ip_space_id' => null,
                        'if_primary' => 0,
                        'network_interface_type_id' => $router_type_network_interface_type_id
                    )
                ));
            }
            
//            echo '<pre>Interfaces New:';
//            print_r($interfaces);
//            echo '</pre>';
        } else {
            $interfaces = $this->NetworkInterfaceIpSpace->find( 'all',
                    array(
                        'conditions' => $conditions,
                        'order' => 'if_number ASC'
            ));
                    
//            echo '<pre>Interfces Existing:';
//            print_r( $interfaces );
//            echo '</pre>';die;
        }
        
//        echo '<pre>';
//        print_r( $if_name );
//        echo '</pre>';
        
        // $if_name = $this->RouterTypeNetworkInterfaceTypes->findById( $router_type_network_interface_type_id )['NetworkInterfaceType']['name'];
        $if_name = $this->getIfName( $router_type_network_interface_type_id );
        parent::getIpSpaces( $this->Session->read('project_id') );
        $this->set(compact( 'id','interfaces','if_name','network_interface_ip_space','network_interface_type_id','number' ));
        
    }
    
    private function getIfName( $n ) {
        $this->loadModel('RouterTypeNetworkInterfaceTypes');
        $d = $this->RouterTypeNetworkInterfaceTypes->findById( $n );
        $name = "";
        if ( isset( $d['NetworkInterfaceType'] ) ) {
            $name = $d['NetworkInterfaceType']['name'];
        }
        return $name;
    }
    
    /*
     * Check the user's role to determine if sufficient permission to perform
     * the intended action.
     */
    public function isAuthorized($user) {
        
        $allowed = array( "index", "view", "alarms", "events", "graphs" );
        if ( in_array( $this->action, $allowed )) {
            return true;
        }
        
        $allowed = array( "add", "edit", "delete" );
        if ( in_array( $this->action, $allowed )) {
            if ( $this->Session->read('role') === 'edit') {
                return true;
            }
        }
        
        return parent::isAuthorized($user);
    }
}

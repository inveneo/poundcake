<?php
/**
 * Controller for router types.
 *
 * This is a very basic controller to add/view/update/delete router types.
 * 
 * These tasks would typically be performed by a user with administrative level
 * permissions within Poundcake.
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
 * @since         RouterTypesController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('NetworkDeviceTypeController', 'Controller');

class RouterTypesController extends NetworkDeviceTypeController {

    /*
     * Main listing for all RouterTypes
     */
    public function index() {
        $this->RouterType->recursive = 0;
        $this->set('routerTypes', $this->paginate());
    }

    /*
     * Add a new RouterType
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->RouterType->create();
            // purge empty items
            $this->request->data['RouterTypeNetworkInterfaceTypes'] = parent::purgeEmptyNetworkInterfaceTypes( $this->request->data['RouterTypeNetworkInterfaceTypes'] );
            if ($this->RouterType->saveAll($this->request->data)) { // saveAll for HABTM through the join model
                $this->Session->setFlash('The router type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The router type could not be saved. Please, try again.');
            }
        }
        parent::setModelClass( $this->modelClass );
        parent::getNetworkInterfaceTypes( $this->modelClass );
    }

    /*
     * Edit an existing RouterType
     */
    public function edit($id = null) {
        $this->RouterType->id = $id;
        if (!$this->RouterType->exists()) {
            throw new NotFoundException('Invalid router type');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            // purge empty items
            $this->request->data['RouterTypeNetworkInterfaceTypes'] = parent::purgeEmptyNetworkInterfaceTypes( $this->request->data['RouterTypeNetworkInterfaceTypes'] );
            
            // this entire bit is terrible -- we need to know if an interface has been
            // de-selected or if the quantity has changed
            $old_data = $this->RouterType->RouterTypeNetworkInterfaceTypes->findAllByRouterTypeId( $id );
//            echo '<pre>';
//            print_r($old_data);
            
            $old_router_type_network_interface_types = array();
            foreach ( $old_data as $d ) {
//                echo '<pre>';
//                print_r($d);
//                print_r($d['network_interface_type_id']);
//                echo'</pre>';
                
                $a = array(
                            'network_interface_type_id' => $d['RouterTypeNetworkInterfaceTypes']['network_interface_type_id'],
                            'number' => $d['RouterTypeNetworkInterfaceTypes']['number']
                );
                
                $old_router_type_network_interface_types[$d['RouterTypeNetworkInterfaceTypes']['network_interface_type_id']] = $a;
            }
            $dirty = false; // $this->request->data['RouterTypeNetworkInterfaceTypes']
            foreach ( $old_router_type_network_interface_types as $k1 => $v1) {
//                echo "k1:<BR>";
//                print_r( $k1 );
//                echo "v1:<BR>";
//                var_dump( $v1 );
//                echo '<br>';
//                print_r(array_diff($old_router_type_network_interface_types[$k1], $v1 ));
                if ( array_key_exists( $k1, $this->request->data['RouterTypeNetworkInterfaceTypes'] )) {
                    if ( array_diff( $this->request->data['RouterTypeNetworkInterfaceTypes'][$k1], $v1 )) {
//                        echo "Interface Qty Changed<BR>";
                        $dirty = true;
                    }
                } else {
//                    echo "Interface type DE-selected<BR>";
                    $dirty = true;
                }
            }
//            if ( $dirty ) { echo "Dirty"; } else { echo "Not dirty"; }
            
            // if a field has changed, delete any existing IP space mappings
            if ( $dirty ) {
                $this->loadModel('NetworkInterfaceIpSpaces');
                foreach( $this->request->data['RouterTypeNetworkInterfaceTypes'] as $i ) { 
                    $this->NetworkInterfaceIpSpaces->deleteAll( array( 'NetworkInterfaceIpSpaces.network_interface_type_id' => $i['network_interface_type_id'] ) );
                }
            }
//            if ( $dirty ) {
//                echo "something's changed";
//            } else {
//                echo "nothing's changed";
//            }
            
//            echo '<pre>';
//            print_r( $this->request->data );
            // we first have to clear out the join table
            $this->RouterType->RouterTypeNetworkInterfaceTypes->deleteAll(array('RouterTypeNetworkInterfaceTypes.router_type_id' => $id ));
            if ($this->RouterType->saveAll($this->request->data)) { // saveAll for HABTM through the join model
                $this->Session->setFlash('The router type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('The router type could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->RouterType->read(null, $id);
        }
        
        // this is a duplicate of RadioTypesController/edit
        $e = $this->RouterType->data['RouterTypeNetworkInterfaceTypes'];
        $existing_network_interface_types = array();
        foreach ( $e as $p ) {
            if ( $p['number'] > 0 ) {
                array_push( $existing_network_interface_types,
                        array( 
                            'network_interface_type_id' => $p['network_interface_type_id'],
                            'number' => $p['number']
                            )
                        );
            }
        }
        $this->set(compact('existing_network_interface_types'));
        parent::setModelClass( $this->modelClass );
        parent::getNetworkInterfaceTypes( $this->modelClass );
    }

    /*
     * Delete an existing RouterType
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->RouterType->id = $id;
        if (!$this->RouterType->exists()) {
            throw new NotFoundException('Invalid router type');
        }
        if ($this->RouterType->delete()) {
            $this->Session->setFlash('Router type deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Router type was not deleted.');
        $this->redirect(array('action' => 'index'));
    }

    /*
     * Uses Auth to check the ACL to see if the user is allowed to perform any
     * actions in this controller
     */
    public function isAuthorized($user) {
        return parent::isAuthorized($user);
    }
        
}

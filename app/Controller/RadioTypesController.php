<?php
/**
 * Controller for radio types.
 *
 * This is a very basic controller to add/view/update/delete radio types.
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
 * @since         RadioTypesController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('NetworkDeviceTypeController', 'Controller');

class RadioTypesController extends NetworkDeviceTypeController {

    /*
     * Main listing for all RadioTypes
     */
    public function index() {
        $this->RadioType->recursive = 0;
        $this->set('radioTypes', $this->paginate());
    }
    
    /*
     * Add a new RadioType
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->RadioType->create();
            // purge empty items
            $this->request->data['RadioTypeNetworkInterfaceTypes'] = parent::purgeEmptyNetworkInterfaceTypes( $this->request->data['RadioTypeNetworkInterfaceTypes'] );
            if ($this->RadioType->saveAll($this->request->data)) { // saveAll for HABTM through the join model
                $this->Session->setFlash('The radio type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The radio type could not be saved. Please, try again.');
            }
        }
        parent::setModelClass( $this->modelClass );
        parent::getNetworkInterfaceTypes( $this->modelClass );
        $this->getRadioBands();
        $this->getAntennaTypes();
    }
    
    /*
     * Edit an existing RadioType
     */
    public function edit($id = null) {
        $this->RadioType->id = $id;
        if (!$this->RadioType->exists()) {
            throw new NotFoundException('Invalid radio type');
        }
        
        if ($this->request->is('post') || $this->request->is('put')) {
            // purge empty items
            $this->request->data['RadioTypeNetworkInterfaceTypes'] = parent::purgeEmptyNetworkInterfaceTypes( $this->request->data['RadioTypeNetworkInterfaceTypes'] );
            
            // this entire bit is terrible -- we need to know if an interface has been
            // de-selected or if the quantity has changed
            $old_data = $this->RadioType->RadioTypeNetworkInterfaceTypes->findAllByRadioTypeId( $id );
            $old_radio_type_network_interface_types = array();
            foreach ( $old_data as $r ) {
                $a = array(
                            'network_interface_type_id' => $r['RadioTypeNetworkInterfaceTypes']['network_interface_type_id'],
                            'number' => $r['RadioTypeNetworkInterfaceTypes']['number']
                );
                $old_radio_type_network_interface_types[$r['RadioTypeNetworkInterfaceTypes']['network_interface_type_id']] = $a;
            }
//            echo '<pre>';
            $dirty = false; // $this->request->data['RadioTypeNetworkInterfaceTypes']
            foreach ( $old_radio_type_network_interface_types as $k1 => $v1) {
//                echo "k1:<BR>";
//                print_r( $k1 );
//                echo "v1:<BR>";
//                var_dump( $v1 );
//                echo '<br>';
//                print_r(array_diff($old_radio_type_network_interface_types[$k1], $v1 ));
                if ( array_key_exists( $k1, $this->request->data['RadioTypeNetworkInterfaceTypes'] )) {
                    if ( array_diff( $this->request->data['RadioTypeNetworkInterfaceTypes'][$k1], $v1 )) {
//                        echo "Interface Qty Changed<BR>";
                        $dirty = true;
                    }
                } else {
//                    echo "Interface type DE-selected<BR>";
                    $dirty = true;
                }
            }
            
            // if a field has changed, delete any existing IP space mappings
            if ( $dirty ) {
                $this->loadModel('NetworkInterfaceIpSpaces');
                foreach( $this->request->data['RadioTypeNetworkInterfaceTypes'] as $i ) { 
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
            $this->RadioType->RadioTypeNetworkInterfaceTypes->deleteAll(array('RadioTypeNetworkInterfaceTypes.radio_type_id' => $id ));
             if ($this->RadioType->saveAll( $this->request->data )) { // saveAll for HABTM through the join model
                $this->Session->setFlash('The radio type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The radio type could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->RadioType->read(null, $id);
        }
        
        // manually make an array of checked items -- this is so we can manually
        // check boxes on the edit page (dealing for HABTM through the join model)
        $e = $this->RadioType->data['RadioTypeNetworkInterfaceTypes'];
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
        $this->getRadioBands();
        $this->getAntennaTypes();
        parent::getNetworkInterfaceTypes( $this->modelClass );
    }
    
    /*
     * Save an array of antenna types
     */
    private function getAntennaTypes() {
        $antennaTypes = $this->RadioType->AntennaType->find('list',array('fields'=>array('id','name')));
        $this->set(compact('antennaTypes'));
    }
    
    /*
     * Delete an existing RadioType
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->RadioType->id = $id;
        if (!$this->RadioType->exists()) {
            throw new NotFoundException('Invalid radio type');
        }
        if ($this->RadioType->delete()) {
            $this->Session->setFlash('Radio type deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Radio type was not deleted.');
        $this->redirect(array('action' => 'index'));
    }
    
    /*
     * Save an array of radio bands
     */
    private function getRadioBands() {
        $this->set('radiobands', $this->RadioType->RadioBand->find('list'));
    }
    
    /*
     * Save an array of frequencies for chosen radio's RadioType
     * 
     * This is called by jQuery when the user changes the radio type on the
     * NetworkRadio add/edit page.
     */
    public function getFrequenciesForRadioType() {
        // get the RadioType the user selected
        $this->loadModel('RadioType');
        $this->RadioType->id = $this->request->data['NetworkRadio']['radio_type_id'];
        // call the RadioType model function getFrequencies to ge the list of
        // frequencies for the selected radio's RadioType
        $frequencies = $this->RadioType->getFrequencies( $this->RadioType->field('radio_band_id') );
        $this->set( 'frequencies', $frequencies );
        $this->layout = 'ajax';
    }
    
    /*
     * Save an array of antenna types for chosen radio's RadioType
     * 
     * This is called by jQuery when the user changes the radio type on the
     * NetworkRadio add/edit page.
     */
    public function getAntennasForRadioType() {
         // this is basiclly a duplicate of getantennaTypes in the NetworkRadio controller
        $radio_type_id = $this->request->data['NetworkRadio']['radio_type_id'];
        $antennatypes = $this->RadioType->getAntennas( $radio_type_id );
        //var_dump( $antennatypes );die;
        //$antennatypes[0] = 'foo';$antennatypes[1] = 'bar';
        $this->set(compact('antennatypes'));
        $this->layout = 'ajax';
    }
    
    /*
     * Uses Auth to check the ACL to see if the user is allowed to perform any
     * actions in this controller
     */
    public function isAuthorized($user) {
        return parent::isAuthorized($user);
    }
}

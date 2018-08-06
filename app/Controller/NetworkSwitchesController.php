<?php
/**
 * Controller for network switches.
 *
 * This is a very basic controller to add/view/update/delete antenna types.
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
 * @since         NetworkSwitchesController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('NetworkDeviceController', 'Controller');

class NetworkSwitchesController extends NetworkDeviceController {

    /*
     * PoundcakeHTML makes de-links hyperlinks for view-only users
     */
    var $helpers = array('PoundcakeHTML');
    
    /*
     * Main listing for all NetworkSwitches
     */
    public function index() {
        // begin search stuff
        $name_arg = "";
        if (isset($this->passedArgs['NetworkSwitch.name'])) {
            $name_arg = str_replace('*','%',$this->passedArgs['NetworkSwitch.name']);
        }
        
        // if no argument was passed, default to a wildcard
        if ($name_arg == "") {
            $name_arg = '%';
        }
        
        $conditions = array(
            'AND' => array(
                'NetworkSwitch.name LIKE' => $name_arg,
                // only show radios for the currently selected project
                // saved as a session variable
                'Site.project_id' => $this->Session->read('project_id')
            ),
        );
        
        $this->paginate = array(
            'NetworkSwitch' => array(
                // limit is the number per page 
                'limit' => 20,
                'conditions' => $conditions,
                'order' => array(
                    'NetworkSwitch.name' => 'asc',
                ),
            ));
        
        $this->NetworkSwitch->recursive = 1;
        $data = $this->paginate('NetworkSwitch');
        $this->set('networkswitches',$data);
    }
    
    /*
     * View an existing NetworkSwitch
     */
    public function view($id = null) {
        $this->NetworkSwitch->id = $id;
        if (!$this->NetworkSwitch->exists()) {
            throw new NotFoundException('Invalid switch');
        }
        $networkswitch = $this->NetworkSwitch->read(null, $id);        
        $this->checkSnmp(); // check if there is custom SNMP data on this item
        
        // retrieve the username of the person who provisioned this device
        if (isset($this->NetworkSwitch->data['NetworkSwitch']['foreign_id'])) {
            $this->loadModel('User');
            $this->User->recursive = -1;
            $this->User->id = $this->NetworkSwitch->data['NetworkSwitch']['provisioned_by'];
            $user = $this->User->read();
            //debug($this->NetworkSwitch->data['NetworkSwitch']);die;
            $provisioned_by_name = $user['User']['username'];
            $checked = $this->NetworkSwitch->data['NetworkSwitch']['checked'];
        } else {
            $provisioned_by_name = "";
            $checked = "";
        }
        
        $this->getMonitoringSystemLink( $this->NetworkSwitch->data['NetworkSwitch']['node_id'] );
        $this->set(compact( 'networkswitch', 'provisioned_by_name', 'checked' ));
    }

    /*
     * Add a new NetworkSwitch
     */
    public function add() {
        $this->getSwitchTypes();
        $this->getAllSitesForProject();
        
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->NetworkSwitch->create();
            if ($this->NetworkSwitch->save($this->request->data)) {
                // see comments in NetworkRouter::edit
                $this->loadModel('Site', $this->request->data['Site']['id']);
                $this->Site->id = $this->request->data['Site']['id'];
                $this->Site->saveField('network_switch_id', $this->NetworkSwitch->id);

                $this->Session->setFlash('The switch has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The switch could not be saved. Please, try again.');
            }
        }
        $this->getSnmpTypes();
        $project_id = $this->Session->read('project_id');
        parent::getIpSpaces( $project_id );
        $this->set(compact('project_id'));
    }

    /*
     * Edit an existing NetworkSwitch
     */
    public function edit($id = null) {
        $this->NetworkSwitch->id = $id;
        
        if (!$this->NetworkSwitch->exists()) {
                throw new NotFoundException('Invalid router');
        }
        
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->NetworkSwitch->save($this->request->data)) {
                // see comments in NetworkRouter::edit
                $this->loadModel('Site');
                $this->Site->id = $this->request->data['NetworkSwitch']['old_site_id'];
                $this->Site->saveField('network_switch_id', null, false );
                $this->Site->id = $this->request->data['Site']['id']; 
                $this->Site->saveField('network_switch_id', $this->NetworkSwitch->id, false);
                
                $this->Session->setFlash('The switch has been saved.');
                $this->redirect(array('action' => 'view',$this->NetworkSwitch->id));
            } else {
                $this->Session->setFlash('Error!  The switch could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->NetworkSwitch->read(null, $id);
        }
        $old_site_id = $this->NetworkSwitch->data['Site']['id'];
        $this->getAllSitesForProject();
        $this->getSwitchTypes();
        $this->getSnmpTypes();
        $project_id = $this->Session->read('project_id');
                
        parent::getIpSpaces( $project_id );
        $this->set(compact( 'old_site_id','project_id', 'id' ));
    }

    /*
     * Delete an existing NetworkSwitch
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
                throw new MethodNotAllowedException();
        }
        
        $this->NetworkSwitch->id = $id;
        if (!$this->NetworkSwitch->exists()) {
            throw new NotFoundException('Invalid switch');
        }
        if ($this->NetworkSwitch->delete()) {
            $this->Session->setFlash('Switch deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Switch was not deleted.');
        $this->redirect(array('action' => 'index'));
    }
    
    /*
     * Save an array of SwitchTypes
     */
    function getSwitchTypes() {
        $this->set('switchtypes',$this->NetworkSwitch->SwitchType->find('list',
            array(
                'order' => array(
                    'SwitchType.ports ASC'
            )))
        );
    }
    
    /*
     * Save an array of SNMP types the project may be using
     */
    private function getSnmpTypes() {
        $this->set('snmptypes',$this->NetworkSwitch->SnmpType->find('list',
            array(
                'order' => array(
                    'SnmpType.name'
            )))
        );
    }
    
    /*
     * Get the assigned Switch for a given site
     */
    public function getSwitchForSite() {
        // revisit: site_id should probably come in as an argument
        
        // See also NetworkRadiosController::getNetworkSwitch -- which is what is called
        // when the page first loads, then this one is called by jQuery
        
        // get the Site the user selected
        $this->loadModel('Site');
        $this->Site->id = $this->request->data['NetworkRadio']['site_id'];

        if ( $this->Site->field('network_switch_id') > 0 ) {
            // now get the NetworkSwitch on that site
            // $network_switch_id = $this->Site->field('network_switch_id');
            $this->loadModel('NetworkSwitch');
            $this->NetworkSwitch->id = $this->Site->field('network_switch_id');
            //$this->NetworkSwitch->read($this->NetworkSwitch->id,null);

            // now load the SwitchType
            $this->loadModel('SwitchType');
            $this->SwitchType->id = $this->NetworkSwitch->field('switch_type_id');

            $networkswitches = array();        
            if ( $this->NetworkSwitch->field('name') != null ) { 
                // now load the SwitchType
                $this->loadModel('SwitchType');
                $this->SwitchType->id = $this->NetworkSwitch->field('switch_type_id');

                $ports = $this->SwitchType->field('ports');
                // switches are labeled 1 to N
                for ($i = 1; $i <= $ports; $i++) {
                    //$switchports[$i] = $i . " switch id: ".$switch_id ." ports ".$ports."";
                    $networkswitches[$i] = $this->NetworkSwitch->field('name') . ' #'.$i;
                    //$networkswitches[$i] = 'Port '.$i;
                }
            }
        } else {
            $networkswitches[0] = $this->Site->field('name').' has no switch';            
        }
        
        $this->set('network_switch_id',$this->NetworkSwitch->id);
        $this->set('networkswitches',$networkswitches);
        $this->layout = 'ajax';
    }
    
    /*
     * Provisions the device into the monitoring system.
     * @see Identical functions in NetworkSwitch, NetworkRouter, NetworkRadio
     */
    public function provision( $id = null ) {        
        $this->NetworkSwitch->read(null, $id);
        
        // don't allow provisioning if the project is set to read-only integration
        $ro = $this->NetworkSwitch->Site->Project->field('read_only');
        if ( !$ro ) {
            $name = $this->NetworkSwitch->data['NetworkSwitch']['name'];
            $ip_addr = $this->NetworkSwitch->data['NetworkSwitch']['ip_address'];
            $foreign_id = parent::provisionNode( $name, $ip_addr, true );
            if ( !is_null( $foreign_id ) ) {
                $this->NetworkSwitch->saveField('foreign_id', $foreign_id);
                $this->NetworkSwitch->saveField('provisioned_on', date("Y-m-d H:i:s") );
                $this->NetworkSwitch->saveField('provisioned_by', $this->Auth->user('id') );
                $this->Session->setFlash('Provisioned switch.  Foreign ID '.$foreign_id);
            } else {
                $this->Session->setFlash('Error!  Problem provisioning switch.');
            }
        } else {
            $this->Session->setFlash('Error!  Project is set for read-only integration with monitoring system.');
        }
        
        $this->redirect(array('action' => 'view',$this->NetworkSwitch->id));
    }
    
    /*
     * Save an array of alarms or alerts from the monitoring system
     */
    public function alarms( $id ) {
        $this->NetworkSwitch->recursive = -1;
        $this->NetworkSwitch->id = $id;
        $this->NetworkSwitch->read();
        $alarms = parent::getAlarms();
        $name = $this->NetworkSwitch->data['NetworkSwitch']['name'];
        $this->set(compact( 'alarms', 'id', 'name' ));
    }
    
    /*
     * Save an array of events from the monitoring system -- is basically
     * identical to alarms()
     */
    public function events( $id ) {
        $this->NetworkSwitch->recursive = -1;
        $this->NetworkSwitch->id = $id;
        $this->NetworkSwitch->read();
        $events = parent::getEvents();
        $name = $this->NetworkSwitch->data['NetworkSwitch']['name'];
        $this->set(compact( 'events', 'id', 'name' ));
    }
    
    /*
     * Save an array of performance graphs from the monitoring system
     */
    public function graphs( $id = null ) {
        $this->NetworkSwitch->id = $id;
        $this->NetworkSwitch->read();
        $name = $this->NetworkSwitch->data['NetworkSwitch']['name'];
        $this->getPerformanceGraphs( $this->NetworkSwitch->data['NetworkSwitch']['node_id'] );
        $this->set(compact( 'id', 'name'));
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

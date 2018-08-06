<?php
/**
 * Controller for projects.
 *
 * This is a very basic controller to add/view/update/delete projects.  Projects
 * are the highest level categorization of sites; Sites may be long to one
 * Project.  Users are then granted permissions to access a project.
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
 * @since         ProjectsController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class ProjectsController extends AppController {

    /*
     * Main listing for all Projects
     */
    public function index() {
        $this->Project->recursive = 0;
        $this->set('projects', $this->paginate());
    }

    /*
     * View an existing project.
     */
    public function view($id = null) {
        $this->Project->id = $id;
        $this->Project->recursive = 2; // we also want users on this project and their roles
        if (!$this->Project->exists()) {
            throw new NotFoundException('Invalid project');
        }
        
        $project =  $this->Project->read(null, $id);
        
        // we're going to make it easy on the view, and prepare a nice, tidy
        // array of users on this project and their roles
        $users = $this->Project->read(null, $id);
        $project_users = array();
        foreach ( $users['ProjectMembership'] as $user ) {
            if ( count($user['User']) > 0 ) {
                $this->loadModel('Role');
                $this->Role->id = $user['role_id'];
                $role = $this->Role->read();            
                //var_dump( $role );
                $uid = $user['User']['id'];
                $u = array(
                    'username' => $user['User']['username'],
                    'role' => $role['Role']['name']
                );

                array_push( $project_users, $u );
            }
        }
        
        $this->set(compact('project','project_users'));
    }

    /*
     * Append http:// to an URL if it is missing, also remove "/" from the end
     */
    private function cleanUrl( $url ) {
        if ( $url != "" ) {
            // if there is no http, append it
            // if there is https, skip this
            if ((stripos($url, 'http://') !== 0) && (stripos($url, 'https://') !== 0)) {
                $url = 'http://' . $url;
            }
        }
        // remove / from the end of the URL
        $url = preg_replace('{/$}', '', $url);        
        return $url;
    }
    /*
     * Add a new project
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->Project->create();
            // set http if it was not set
            $this->request->data['Project']['monitoring_system_url'] = $this->cleanUrl( $this->request->data['Project']['monitoring_system_url'] );
            // see beforeSave callback in the Project model
            if ($this->Project->save($this->request->data)) {
                $this->Session->setFlash('The project has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The project could not be saved. Please, try again.');
            }
        }
        $this->getSnmpTypes();
        $this->getMonitoringSystemTypes();
        // projects default to San Francisco
        $default_lat = '37.7750';
        $default_lon = '-122.4183';
        $this->set(compact('default_lat','default_lon'));
    }
    
    /*
     * Edit an existing project
     */
    public function edit($id = null) {
        $this->Project->id = $id;
        $this->Project->recursive = 2; // we also want users on this project and their roles
        if (!$this->Project->exists()) {
            throw new NotFoundException('Invalid project');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            // set http if it was not set
            $this->request->data['Project']['monitoring_system_url'] = $this->cleanUrl( $this->request->data['Project']['monitoring_system_url'] );            
            // see beforeSave callback in the Project model
            if ($this->Project->save( $this->request->data )) {
                $this->Session->setFlash('The project has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The project could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->Project->read(null, $id);
        }
        $this->getSnmpTypes();
        $this->getMonitoringSystemTypes();
        $this->getRoles();
    }
    
    /*
     * Get all the roles defined in the system (except the admin)
     */
    public function getRoles() {
        $this->loadModel('Role');
        $roles = $this->Role->find('list');
        array_shift( $roles ); // remove the 0th element, which is the admin role
        $this->set(compact('roles'));
    }
    
    public function summary( $project_id = null ) {
        $project = $this->Project->read(null, $project_id );
        $project_name = $project['Project']['name'];
        
        $counts = array();
        
        $num_sites_total = 0;
        $router_value_total = 0;
        $watts_total = 0;
            
        $this->loadModel('Zone');
        $zones = $this->Zone->find('all');
        foreach( $zones as $zone ) {
//            echo( "Looking at sites in Zone ".$zone['Zone']['id'] . "<br>");
            
            $site_count_zone = 0;
            
            $router_watts_zone = 0;
            $router_value_zone = 0;
            $router_count_zone = 0;
            
            $switch_watts_zone = 0;
            $switch_value_zone = 0;
            $switch_count_zone = 0;
            
            $radio_watts_zone = 0;
            $radio_value_zone = 0;
            $radio_count_zone = 0;
            
            $this->loadModel('Site');
            $sites = $this->Site->find('all',
                array(
                'conditions' => array(
                    'Site.project_id' => $project_id,
                    'Site.zone_id' => $zone['Zone']['id']
                ),
                'order' => array('Site.zone_id ASC'),
    //            'group' => 'Site.zone_id'
                )
            );
            if ( count($sites) > 0 ) {
                foreach ( $sites as $site ) {
    //                print_r( $site );

                    $site_count_zone++;

                    if ( isset($site['Site']['network_router_id'] )) {
                        $router_watts_zone += $site['NetworkRouter']['watts'];
                        $router_value_zone += $site['NetworkRouter']['value'];
                        $router_count_zone++;
                    }
                    
                    if ( isset($site['Site']['network_switch_id'] )) {
                        $switch_watts_zone += $site['NetworkSwitch']['watts'];
                        $switch_value_zone += $site['NetworkSwitch']['value'];
                        $switch_count_zone++;
                    }
                    
                    $this->Site->contain('NetworkRadios');
                    
                    $s_tmp = $this->Site->read(null,$site['Site']['id']);
                    foreach( $s_tmp['NetworkRadios'] as $r ) {
                        $radio_watts_zone += $r['watts'];
                        $radio_value_zone += $r['value'];
                        $radio_count_zone++;
                    }
                }
//                echo( $num_sites_zone . " sites found<br>");

                $zone_totals = array (
                    'zone_id' => $zone['Zone']['id'],
                    'zone_name' => $zone['Zone']['name'],
                    
                    'site_count' => $site_count_zone,
                    
                    'router_watts' => $router_watts_zone,
                    'router_value' => $router_value_zone,
                    'router_count' => $router_count_zone,
                    
                    'switch_watts' => $switch_watts_zone,
                    'switch_value' => $switch_value_zone,
                    'switch_count' => $switch_count_zone,
                    
                    'radio_watts' => $radio_watts_zone,
                    'radio_value' => $radio_value_zone,
                    'radio_count' => $radio_count_zone,
                );
                array_push( $counts, $zone_totals );
            }
        }
        
        $site_count_total = 0;
        
        $router_watts_total = 0;
        $router_value_total = 0;
        $router_count_total = 0;

        $switch_watts_total = 0;
        $switch_value_total = 0;
        $switch_count_total = 0;

        $radio_watts_total = 0;
        $radio_value_total = 0;
        $radio_count_total = 0;
        
        foreach( $counts as $count ) {
            $site_count_total += $count['site_count'];
            
            $router_watts_total += $count['router_watts'];
            $router_value_total += $count['router_value'];
            $router_count_total += $count['router_count'];
            
            $switch_watts_total += $count['switch_watts'];
            $switch_value_total += $count['switch_value'];
            $switch_count_total += $count['switch_count'];
            
            $radio_watts_total += $count['radio_watts'];
            $radio_value_total += $count['radio_value'];
            $radio_count_total += $count['radio_count'];            
        }
        
        // sum up all the radios, by type
        $this->loadModel('RadioType');
        $radio_types = $this->RadioType->find('list');
        $radio_type_count = array();
        foreach( $radio_types as $key => $val ) {
            $conditions = array(
                'Site.project_id' => $project_id,
                'NetworkRadios.radio_type_id' => $key
            );
            $this->loadModel('Site');
            $this->Site->contain('NetworkRadio');
            $y = $this->Site->NetworkRadios->find('count',array('conditions' => $conditions));
            if ( $y > 0 ) {
                $radio_type_count = array_merge_recursive( $radio_type_count, array( $val => $y ) );
            }
        }
        
        // sum up all the routers, by type
        $this->loadModel('RouterType');
        $router_types = $this->RouterType->find('list');
        $router_type_count = array();
        foreach( $router_types as $key => $val ) {
            $conditions = array(
                'Site.project_id' => $project_id,
                'NetworkRouter.router_type_id' => $key
            );
            $y = $this->Site->NetworkRouter->find('count',array('conditions' => $conditions));
            if ( $y > 0 ) {
                $router_type_count = array_merge_recursive( $router_type_count, array( $val => $y ) );
            }
        }
        
        // sum up all the routers, by type
        $this->loadModel('SwitchType');
        $switch_types = $this->SwitchType->find('list');
        $switch_type_count = array();
        foreach( $switch_types as $key => $val ) {
            $conditions = array(
                'Site.project_id' => $project_id,
                'NetworkSwitch.switch_type_id' => $key
            );
            $y = $this->Site->NetworkSwitch->find('count',array('conditions' => $conditions));
            if ( $y > 0 ) {
                $switch_type_count = array_merge_recursive( $switch_type_count, array( $val => $y ) );
            }
        }
        
        $project_totals = array (
            'zone_id' => null,
            'zone_name' => 'Summary',

            'site_count' => $site_count_total,

            'router_watts' => $router_watts_total,
            'router_value' => $router_value_total,
            'router_count' => $router_count_total,

            'switch_watts' => $switch_watts_total,
            'switch_value' => $switch_value_total,
            'switch_count' => $switch_count_total,

            'radio_watts' => $radio_watts_total,
            'radio_value' => $radio_value_total,
            'radio_count' => $radio_count_total,
            
            'radio_counts' => $radio_type_count,
            'router_counts' => $router_type_count,
            'switch_counts' => $switch_type_count
        );
        // put this at the front of the array so it appears first on the view page
        array_unshift( $counts, $project_totals );
//        echo '<pre>';var_dump( $project_totals );die;
        
        $this->set(compact('counts','project_name'));
    }
    
    /*
     * Delete an existing project
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Project->id = $id;
        if (!$this->Project->exists()) {
            throw new NotFoundException('Invalid project');
        }
        if ($this->Project->delete()) {
            $this->Session->setFlash('Project deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Project was not deleted.  Check for dependencies, such as Sites on the Project.');
        $this->redirect(array('action' => 'index'));
    }
        
    /*
     * Save an array of SNMP types the project may be using
     */
    private function getSnmpTypes() {
        $this->set('snmptypes',$this->Project->SnmpType->find('list',
            array(
                'order' => array(
                    'SnmpType.name'
            )))
        );
    }
    
    /*
     * Save an array of Monitor System Types the project may be using
     */
    private function getMonitoringSystemTypes() {
        $this->set('monitoringSystemTypes',$this->Project->MonitoringSystemType->find('list',
            array(
                'order' => array(
                    'MonitoringSystemType.name'
            )))
        );
    }
    
    /*
     * Uses Auth to check the ACL to see if the user is allowed to perform any
     * actions in this controller
     */
    public function isAuthorized($user) {
        
        $allowed = array( "summary" );
        if ( in_array( $this->action, $allowed )) {
            return true;
        }
        
        return parent::isAuthorized($user);
    }
}
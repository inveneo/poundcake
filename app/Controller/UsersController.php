<?php
/**
 * Controller for users.
 *
 * This controller is used by both the admin interface, to add/update/delete/edit
 * users within the system, but also performs some user-level functions like
 * login, logout and switching project.  An admin can also assign permissions
 * with functions in this class.
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
 * @since         UsersController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

class UsersController extends AppController {
    
    public $components = array('Cookie');
    
    /*
     * Custom pagination, sort order on index listing
     */
    public $paginate = array(
        'limit' => 20, // default limit also defined in AppController
        'order' => array(
            'User.username' => 'asc'
        )
    );
    
    /*
     * Main listing for all Users
     */
    public function index() {
        $name_arg = "";
        if (isset($this->passedArgs['User.username'])) {
            $name_arg = str_replace('*','%',$this->passedArgs['User.username']);
        }
        
        // if no argument was passed, default to a wildcard
        if ($name_arg == "") {
            $name_arg = '%';
        }
        
        $conditions = array(
            'AND' => array(
                'User.username LIKE' => $name_arg,
            ),
        );
        
        $this->paginate = array(
            'User' => array(
                // limit is the number per page 
                // 'limit' => 20,
                'conditions' => $conditions,
                'order' => array(
                    'User.username' => 'asc',
                ),
            ));
        
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    /*
     * Get all the roles defined in the system
     */
    function getRoles() {
//        $roles = $this->User->Role->find('list');
//        $this->set('roles',$roles);
        $this->loadModel('Role');
        $roles = $this->Role->find('list');
        array_shift( $roles ); // remove the 0th element, which is the admin role
        $this->set(compact('roles'));
    }
    
    /*
     * Save an array of projects this user is assigned to
     */
    function getUsersProjects() {
        
        $uid = $this->Auth->user('id');
        $this->User->id = $uid;
        $this->User->read();
//        var_dump( $this->User->field('admin') );
        
        // if the user is an administrator, then show them all projects
        if ( $this->User->field('admin') ) {
            $projects = $this->User->ProjectMembership->Project->find('list');            
        } else {
            // otherwise they get the list of projects to which they are assigned,
            // and this is done by joining on the project_memberships table
//            $projects = $this->User->ProjectMembership->Project->find('list', array( 
//                $conditions = array(
//                    'ProjectMembership.user_id' => $uid
//                ),
//                'joins' => array( 
//                    array(
//                        'table' => 'project_memberships', 
//                        'alias' => 'ProjectMembership', 
//                        'type' => 'inner',
//                        'conditions'=> array(
//                            'ProjectMembership.project_id = Project.id'
//                            )
//                        ) 
//                ),
//                'order' => array('Project.name ASC'),
//                ));
            $projects_all = $this->User->ProjectMembership->findAllByUserId( $uid );
            $projects = array();
            foreach ( $projects_all as $p ) {
//                array_push( $projects, array( $id => $p['Project']['name'] ));
                $projects[$p['Project']['id']] = $p['Project']['name'];
            }
        }
//        var_dump( $projects );
//        $log = $this->User->ProjectMembership->Project->getDataSource()->getLog(false, false);
//        debug($log);
//        die;
        $this->set(compact('projects'));
    }
    
    /*
     * Save an array of all projects in the system
     */
    function getAllProjects( $all = false ) {
        if ( !$all ) {
            // return all projects
            $projects = $this->User->ProjectMembership->Project->find('list');
        } else {
            $this->User->ProjectMembership->Project->recursive = -1;
            $projects = $this->User->ProjectMembership->Project->find('all');
        }
        
        //$projects = $this->User->Project->find('list');
        $this->set('projects',$projects);
    }
    
    /*
     * Switch a user's project context to a new project
     */
    public function project($id = null) {
        // this is true when the user has switched projects
        if ($this->request->is('post') || $this->request->is('put')) {
            $project_id = $this->request->data['Project']['Project'];
            
            $this->loadModel('Project',$project_id);
            $project = $this->Project->read();
            $this->Session->write('project_id', $project_id);
            $this->Session->write('project_name', $project['Project']['name']);
            
            // save the newly selected project id, name to a session variable
            // allows us to filter sites/radios/routers/switches to the currently
            // chosen project
            if ($this->Session->write('project_id',$project_id) &&
                $this->Session->write('project_name',$project['Project']['name'])
                ) {
                // also save the selected project id the database -- which
                // becomes the project the next time the user logs in
                // see login in this controller
                // $uid = CakeSession::read("Auth.User.id");
                if (!is_null($id)) {
                    $this->User->id = $id;
                    $this->User->saveField('project_id',$project_id);
//                    echo "Just saved $project_id!<BR>";
                    // clear any saved searches ("sticky search" so the main site
                    // listing page goes to sites in the newly selected project
                    $this->Session->write( 'conditions', null );
                }
                
                $this->setRole( $id );
                
                $this->Session->setFlash('The project has been set.');
                $this->redirect(array('controller' => 'sites','action' => 'overview'));
            } else {
                $this->Session->setFlash('Error!  The project could not be set.');
            }
        }
        $this->getUsersProjects();
    }
    
    /*
     * Add a new User
     */
    public function add() {
        $this->getRoles();
        if ($this->request->is('post')) {
            $this->User->create();
            // project_id is the project the user defaults to when they login
            // this should be getting set by the login routine but we can set
            // it here just the same -- give them the first project to which they are assigned
            // if the admin didn't assign them to any projects this will return an error
            //debug($this->request->data,false);
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash('The user has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The user could not be saved. Please, try again.');
            }
        }
        $this->getAllProjects();
    }
    
    /*
     * This password() function is for when a user changes their own password
     */
    function password($id = null) {
        $this->User->id = $id;  

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->validates(array('fieldList' => array('pwd_current')))) {
                if ($this->User->validates(array('fieldList' => array('pwd_current','password')))) {
                    unset($this->request->data['User']['pwd_current']);
                    if ($this->User->saveAll($this->request->data)) {
                        $this->Session->setFlash('Password succssfully updated.  Please logout and login again.');
                        
                    } else {
                        $this->Session->setFlash('Error!  Problem updating password.');
                    }
                } else {
                     // echo "pwd FAILED validation<br>";
                }
            } else {
                 // echo "pwd_current FAILED validation<br>";
            }
        } else {
            $this->getAllProjects();
            $this->request->data = $this->User->read(null, $id);
        }
    }
    
    /*
     * Grant a user permissions in other projects
     */  
//    public function permissions_orig($id = null) {
//        $this->User->id = $id;
//        $this->getRoles();
//        $this->getAllProjects();
//        
//        if (!$this->User->exists()) {
//            throw new NotFoundException('Invalid user');
//        }
//        if ($this->request->is('post') || $this->request->is('put')) {
//            //unset($this->User->validate['password']);
//            
//            // we need to check if the user has been removed from a project
//            // they were previously assigned to and possibly clear/change
//            // their project_id field -- otherwise the user will still have access
//            
//            // get the id of the last project the user accessed
//            $last_project_id = $this->User->field('project_id');
//            
//            // get an array of projects the user is now currently assigned to
//            $new_projects = $this->request->data['Project']['Project'];
//            if ( in_array( $last_project_id,  $new_projects ) == 0 ) {
//                // just assign them a default of the first item in the set of
//                // new projects
//                $this->User->saveField('project_id',$new_projects[0]);              
//            }
//            
//            // $blackList is also used above
//            $blackList = array('password', 'username');
//            if ($this->User->save($this->request->data, true, array_diff(array_keys($this->User->schema()), $blackList))) {
//                $this->Session->setFlash('The user has been saved.');
//                $this->redirect(array('action' => 'index'));
//            } else {
//                $this->Session->setFlash('Error!  The user could not be saved. Please, try again.');
//            }
//        } else {
//            $this->request->data = $this->User->read(null, $id);
//            unset($this->request->data['User']['password']);
//        }
//    }
    
    /*
     * Grant a user permissions in other projects
     */
    public function permissions($id = null) {
        $this->User->recursive = 2;
        
        $this->User->id = $id;
        $this->User->read();
        $username = $this->User->data['User']['username'];

        // $this->getRoles();
        $this->loadModel('Role');
        $roles = $this->Role->find('all'); // we need all role info
        
        $this->getAllProjects( true );
        
        if (!$this->User->exists()) {
            throw new NotFoundException('Invalid user');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->User->ProjectMembership->deleteAll(array('ProjectMembership.user_id' => $id ));
            
            $row_id = 0;
            foreach ( $this->request->data['ProjectMembership'] as $project ) {
                if ( is_array($project) && array_key_exists('project_id', $project) && array_key_exists('role_id', $project) ) {
                    
                    $pm['ProjectMembership']['user_id'] = $id;
                    $pm['ProjectMembership']['project_id'] = $project['project_id'];
                    $pm['ProjectMembership']['role_id'] = $project['role_id'];                   
                    $this->User->ProjectMembership->create();
                    $this->User->ProjectMembership->save( $pm );      
                    $row_id = $this->User->ProjectMembership->id;                    
                    // the user may have been removed from a project they were on
                    // this is a little sloppy but just assign them a new default
                    // project -- this should probably be outside this loop, too
                    $this->User->saveField( 'project_id', $pm['ProjectMembership']['project_id']  );                      
                }
            }
            
            if ( $row_id > 0 ) {
                $this->Session->setFlash('The permissions have been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The permissions could not be saved. Please, try again.');
            }
            
        }

        // we need a simple array of project_ids the user us alrady assigned to
        // so that we can manually check the box on the edit page -- this is stuff
        // HABTM would do for us, but we have to do it manually
        $existing_projects = $this->User->data['ProjectMembership'];
        $assigned_projects = array();
        foreach ( $existing_projects as $p ) {
            array_push( $assigned_projects,
                    array( 
                        'project_id' => $p['Project']['id'],
                        'role_id' => $p['role_id']
                        )
                    );
        }
        $this->set(compact('assigned_projects','roles','username', 'id'));
    }
    
    /*
     * Edit a details for a user
     */
    public function edit($id = null) {
        $this->User->id = $id;
        $this->User->read(null,$id);
        $this->set('username',$this->User->field('username'));
        
        if (!$this->User->exists()) {
            throw new NotFoundException('Invalid user');
        }
        
        $ret = true;
        if ($this->request->is('post') || $this->request->is('put')) {
            $orig_password = $this->User->field('password');
            $new_password = "";
            $tmp_password = $this->request->data['User']['password'];
            if ( $tmp_password != '' ) {
                $new_password = AuthComponent::password( $this->request->data['User']['password'] );
            }
//            var_dump( $orig_password );
//            var_dump( $tmp_password );
//            var_dump( $new_password );
            
            if (( $orig_password != $new_password ) && ( $new_password != "" )) {
//                echo "changing password";
                $ret = $this->User->saveField( 'password',$this->request->data['User']['password'] );
                // I assume $ret will be null if the save failed?
            }
            if ( $ret && $this->User->saveField('admin',$this->request->data['User']['admin'] ) ) {
                $this->Session->setFlash('The user has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The user could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
    }
    
    /*
     * Delete a user
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException('Invalid user');
        }
        if ($this->User->delete()) {
            $this->Session->setFlash('User deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  User was not deleted.');
        $this->redirect(array('action' => 'index'));
    }
    
    /*
     * Login is our main login routine
     */
    public function login() {
        
        // if the user is already logged in (maybe opening the site in a new tab)
        // don't send them to the login page
        if ($this->Session->check('Auth.User')) {
            $this->redirect($this->Auth->redirect());
        }
        
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {             
                $uid = CakeSession::read("Auth.User.id");
                $this->loadModel('User',$uid);
                $this->User->id = $uid;
                $user = $this->User->read( null, $uid );
//                echo '<pre>';
                // get the ID of the last project thi user was assigned
                $last_project_id = $this->User->field('project_id');
//                debug($user);
                
                // get all the projects to which this user is assigned
                $projects = $this->User->ProjectMembership->findAllByUserId( $uid );
                
//                echo "Last project ID:  $last_project_id";
//                var_dump( $last_project_id );
//                var_dump( $projects );
                
                // check that the user is still assigned to their last accessed project
                // as they may have been revoked access to a project
                $ok_to_login = false;
                if ( $user['User']['admin'] == 1 ) {
                    $ok_to_login = true;
                } else {
                    foreach ( $projects as $project ) {
//                        echo '<pre>'; print_r( $project['Project']['id'] );
                        if ( $last_project_id == $project['Project']['id'] ) {
                            $ok_to_login = true;
                        }
                    }
                }
                
                if ( !$ok_to_login && ( count($projects) == 0 )) {
                    $this->Session->setFlash('Error!  User has no assigned projects.  Contact an Administrator.'); 
                    return;
                } elseif ( !$ok_to_login && ( count($projects) > 0 )) { 
                    // if the user is not assigned to their last project, but is still assigned
                    // to other projects, let's use one of those as the project to send them to
                    $project = $this->User->ProjectMembership->findByUserId( $uid );
                    // set this project as their new last accessed project
                    $this->User->saveField( 'project_id', $project['Project']['id'] );                    
                } else {
                    // otherwise, give them their last accessed project
                    $project = $this->User->ProjectMembership->Project->findById( $last_project_id );
                }
//                var_dump( $projects );
//                die;

                if ( sizeof($project) > 0 ) { 
                    $project_id = $project['Project']['id'];
                    $project_name = $project['Project']['name'];

                    // save the project ID and name as session variables
                    // see also projects() in this controller
                    $this->Session->write('project_id', $project_id);
                    $this->Session->write('project_name', $project_name);
                    
                    // log the user's login time and IP address
                    $this->User->saveField('last_login', date( "Y-m-d H:i:s", time() ));
                    $this->User->saveField('ip_address', ip2long($this->request->clientIp()));                    
                    // send them on their way
                    $this->setRole( $this->User->id );
                    $this->redirect($this->Auth->redirect());
                }
            } else {
                $this->Session->setFlash('Error!  Invalid username or password, try again.');
            }
        }
    }
    
    /*
     * This function sets a session variable named "role" to the user's rolealias
     * for the current project -- whereas this used to come from User->Role now
     * we have to query for it via the ProjectMembership join table
     */
    private function setRole( $id ) {
        $role = null;
        $this->User->id = $id;
        $this->User->read();
        if ( $this->User->data['User']['admin'] ) {
            $role = 'admin';
        } else {
            // get current project ID
            $project_id = $this->Session->read('project_id');
            // get all the projects the user is assigned to
            $projects = $this->User->data['ProjectMembership'];
            foreach ( $projects as $project ) {
                // when we've found the current project
                if ( $project['project_id'] == $project_id ) {
                    $this->loadModel('Role');
                    $this->Role->id = $project['role_id'];
                    $this->Role->read();
                    $role = $this->Role->data['Role']['rolealias'];
                }
            }
            
        }
        $this->Session->write( 'role', $role );
    }
    
    /*
     * Logout is our main logout routine
     */
    public function logout() {
        // setFlash doesn't actually work here, maybe we should redirect them to a page
        // confirming they've been logged out (which then redirects to the main page?
        // $this->Session->setFlash(__('Logout complete')); // a white lie? :-)
        //$this->redirect($this->Auth->logout());
        
        // having problems with the logout function sending them to a non-authorized page
        // this workaround seems to work, just send them back to the login page
        // problem described here:
        // see:  http://stackoverflow.com/questions/8262720/cakephp-2-0-logout
        if($this->Auth->user()) {
            //$this->Session->delete('banner');
            $this->Session->destroy();
            $this->redirect($this->Auth->logout());
        } else {
            $this->redirect(array('controller'=>'sites','action' => 'overview'));
            $this->Session->setFlash('Error!  Not logged in.', 'default', array(), 'auth');
        }
    }
    
    public function dialog() {
        $this->Session->write( 'dialog_closed', true );
    }
    
    /*
     * Uses Auth to check the ACL to see if the user is allowed to perform any
     * actions in this controller
     */
    public function isAuthorized($user) {
        // override isAuthorized in AppController by allowing specific functionality

        // these are the pages the user is allowed to access in this controller
        $available_actions = array(
            'logout',
            'password',
            'cron',
            'change_password',
            'project'
        );
        
        if (array_search($this->action, $available_actions)) {
            return true;
        }
        
        return parent::isAuthorized($user);
    }
}
?>
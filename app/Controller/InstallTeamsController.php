<?php
/**
 * Controller for install teams.
 *
 * This is a very basic controller to add/view/update/delete install teams.
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
 * @since         InstallTeamsController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class InstallTeamsController extends AppController {

    /*
     * Custom pagination, sort order on index listing
     */
    public $paginate = array(
        'limit' => 20, // default limit also defined in AppController
        'order' => array(
            'InstallTeam.name' => 'asc'
        )
    );
    
    /*
     * Main listing for all InstallTeams
     */
    public function index() {
        $this->InstallTeam->recursive = 0;
        $this->set('installteams', $this->paginate());
    }

    /*
     * View an InstallTeam
     */
    public function view($id = null) {
        $this->InstallTeam->id = $id;        
        if (!$this->InstallTeam->exists()) {
            throw new NotFoundException('Invalid install team');
        }
        $this->set('installteam', $this->InstallTeam->read(null, $id));
    }

    /*
     * Add a new InstallTeam
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->InstallTeam->create();
            if ($this->InstallTeam->save($this->request->data)) {
                $this->Session->setFlash('The install team has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The install team could not be saved. Please, try again.');
            }
        }
        $this->getProjects();
    }

    /*
     * Edit an existing InstallTeam
     */
    public function edit($id = null) {
        $this->InstallTeam->id = $id;        
        if (!$this->InstallTeam->exists()) {
            throw new NotFoundException('Invalid install team');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->InstallTeam->save($this->request->data)) {
                $this->Session->setFlash('The install team has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The install team could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->InstallTeam->read(null, $id);
        }
        $this->getProjects();
    }
    
    /*
     * Delete an existing InstallTeam
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->InstallTeam->id = $id;
        if (!$this->InstallTeam->exists()) {
            throw new NotFoundException('Invalid install team');
        }
        if ($this->InstallTeam->delete()) {
            $this->Session->setFlash('Install team deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Install Team was not deleted.');
        $this->redirect(array('action' => 'index'));
    }
    
    /*
     * Check the user's role to determine if sufficient permission to perform
     * the intended action.
     */
    public function isAuthorized($user) {
        
        $allowed = array( "index", "view" );
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

<?php
/**
 * Controller for organizations.
 *
 * This is a very basic controller to add/view/update/delete organizations.
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
 * @since         OrganizationsController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class OrganizationsController extends AppController {

    /*
     * Main listing for all Organizations
     */
    public function index() {
        $this->Organization->recursive = 0;
        $this->set('organizations', $this->paginate());
    }
    
    /*
     * Save an array of contacts for an Organization.
     */
    function getContacts() {
        $this->set('contacts',$this->Organization->Contact->find('list'));        
    }

    /*
     * View an existing Organization
     */
    public function view($id = null) {
        $this->Organization->id = $id;        
        if (!$this->Organization->exists()) {
            throw new NotFoundException('Invalid organization');
        }
        $this->set('organization', $this->Organization->read(null, $id));
        
    }

    /*
     * Add a new Organization
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->Organization->create();
            if ($this->Organization->save($this->request->data)) {
                $this->Session->setFlash('The organization has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The organization could not be saved. Please, try again.');
            }
        }
        parent::getProjects();
    }

    /*
     * Edit an existing Organization
     */
    public function edit($id = null) {
        $this->Organization->id = $id;
        if (!$this->Organization->exists()) {
            throw new NotFoundException('Invalid organization');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Organization->save($this->request->data)) {
                $this->Session->setFlash('The organization has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The organization could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->Organization->read(null, $id);
        }
        parent::getProjects();
    }
    
    /*
     * Delete an existing Organization
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Organization->id = $id;
        if (!$this->Organization->exists()) {
            throw new NotFoundException('Invalid organization');
        }
        if ($this->Organization->delete()) {
            $this->Session->setFlash('Organization deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Organization was not deleted.');
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

<?php
/**
 * Controller for roles
 *
 * This is a very basic controller to add/view/update/delete roles.
 * 
 * These tasks would typically be performed by a user with administrative level
 * permissions within Poundcake.
 * 
 * It's not likely that an admin would be managing Roles very often, since Roles
 * are pretty tightly coupled to the way ACLs work with Auth, and thus adding
 * a new role is likely to mean changes to the code to add/remove/change
 * permissions in some way.
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
 * @since         RolesController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class RolesController extends AppController {

    /*
     * Main listing for all Roles
     */
    public function index() {
        $this->Role->recursive = 0;
        $this->set('roles', $this->paginate());
    }

    /*
     * View an existing Role.
     */
    public function view($id = null) {
        $this->Role->id = $id;
        if (!$this->Role->exists()) {
                throw new NotFoundException('Invalid role');
        }
        $this->set('role', $this->Role->read(null, $id));
    }

    /*
     * Add a new Role.
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->Role->create();
            if ($this->Role->save($this->request->data)) {
                $this->Session->setFlash('The role has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The role could not be saved. Please, try again.');
            }
        }
    }

    /*
     * Edit an existing Role.
     */
    public function edit($id = null) {
        $this->Role->id = $id;
        if (!$this->Role->exists()) {
            throw new NotFoundException('Invalid role');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Role->save($this->request->data)) {
                $this->Session->setFlash('The role has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The role could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->Role->read(null, $id);
        }
    }

    /*
     * Delete an existing Role.
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Role->id = $id;
        if (!$this->Role->exists()) {
            throw new NotFoundException('Invalid role');
        }
        if ($this->Role->delete()) {
            $this->Session->setFlash('Role deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Role was not deleted.');
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

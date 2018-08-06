<?php
/**
 * Controller for power types.
 *
 * This is a very basic controller to add/view/update/delete power types.
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
 * @since         PowerTypesController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class PowerTypesController extends AppController {

    /*
     * Main listing for all PowerTypes
     */
    public function index() {
        $this->PowerType->recursive = 0;
        $this->set('powerTypes', $this->paginate());
    }

    /*
     * View a PowerType
     */
    public function view($id = null) {
        $this->PowerType->id = $id;
        if (!$this->PowerType->exists()) {
                throw new NotFoundException('Invalid power type');
        }
        $this->set('powerType', $this->PowerType->read(null, $id));
    }

    /*
     * Add a new PowerType
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->PowerType->create();
            if ($this->PowerType->save($this->request->data)) {
                $this->Session->setFlash('The power type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The power type could not be saved. Please, try again.');
            }
        }
    }

    /*
     * Edit an existing PowerType
     */
    public function edit($id = null) {
        $this->PowerType->id = $id;
        if (!$this->PowerType->exists()) {
            throw new NotFoundException('Invalid power type');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->PowerType->save($this->request->data)) {
                $this->Session->setFlash('The power type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The power type could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->PowerType->read(null, $id);
        }
    }

    /*
     * Delete an existing PowerType
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->PowerType->id = $id;
        if (!$this->PowerType->exists()) {
            throw new NotFoundException('Invalid power type');
        }
        if ($this->PowerType->delete()) {
            $this->Session->setFlash('Power type deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Power type was not deleted.');
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

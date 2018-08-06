<?php
/**
 * Controller for equipment spaces.
 *
 * This is a very basic controller to add/view/update/delete antenna types.
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
 * @since         EquipmentSpacesController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class EquipmentSpacesController extends AppController {

    /*
     * Main listing for all EquipmentSpaces
     */
    public function index() {
        $this->EquipmentSpace->recursive = 0;
        $this->set('equipmentspaces', $this->paginate());
    }

    /*
     * View an EquipmentSpace
     */
    public function view($id = null) {
        $this->EquipmentSpace->id = $id;
        if (!$this->EquipmentSpace->exists()) {
                throw new NotFoundException('Invalid equipment space');
        }
        $this->set('equipmentspace', $this->EquipmentSpace->read(null, $id));
    }

    /*
     * Add a new EquipmentSpace
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->EquipmentSpace->create();
            if ($this->EquipmentSpace->save($this->request->data)) {
                $this->Session->setFlash('The equipment space has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The equipment space could not be saved. Please, try again.');
            }
        }
    }

    /*
     * Edit an existing EquipmentSpace
     */
    public function edit($id = null) {
        $this->EquipmentSpace->id = $id;
        if (!$this->EquipmentSpace->exists()) {
            throw new NotFoundException('Invalid equipment space');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->EquipmentSpace->save($this->request->data)) {
                $this->Session->setFlash('The equipment space has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The equipment space could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->EquipmentSpace->read(null, $id);
        }
    }

    /*
     * Delete an existing EquipmentSpace
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->EquipmentSpace->id = $id;
        if (!$this->EquipmentSpace->exists()) {
            throw new NotFoundException('Invalid equipment space');
        }
        if ($this->EquipmentSpace->delete()) {
            $this->Session->setFlash('Equipment space deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Equipment space was not deleted.');
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

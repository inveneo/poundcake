<?php
/**
 * Controller for tower types.
 *
 * This is a very basic controller to add/view/update/delete tower types.
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
 * @since         TowerTypesController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class TowerTypesController extends AppController {

    /*
     * Main listing for all TowerrTypes
     */
    public function index() {
        $this->TowerType->recursive = 0;
        // yes, I do realize that the plural of equipment is equipment
        $this->set('towertypes', $this->paginate());
    }

    /*
     * View a TowerType
     */
    public function view($id = null) {
        $this->TowerType->id = $id;
        if (!$this->TowerType->exists()) {
            throw new NotFoundException('Invalid tower type');
        }
        $this->set('towertype', $this->TowerType->read(null, $id));
    }

    /*
     * Add a new TowerType
     */
    public function add() {
        $this->getProjects();
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->TowerType->create();
            if ($this->TowerType->save($this->request->data)) {
                $this->Session->setFlash('The tower type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The tower type could not be saved. Please, try again.');
            }
        }
    }

    /*
     * Edit an existing TowerrType
     */
    public function edit($id = null) {
        $this->TowerType->id = $id;
        if (!$this->TowerType->exists()) {
            throw new NotFoundException('Invalid tower type');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->TowerType->save($this->request->data)) {
                $this->Session->setFlash('The tower type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The tower type could not be saved. Please, try again.');
            }
        } else {
            $this->getProjects();
            $this->request->data = $this->TowerType->read(null, $id);
        }
    }

    /*
     * Delete an existing TowerType
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->TowerType->id = $id;
        if (!$this->TowerType->exists()) {
            throw new NotFoundException('Invalid tower type');
        }
        if ($this->TowerType->delete()) {
            $this->Session->setFlash('Tower type deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Tower type was not deleted.');
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

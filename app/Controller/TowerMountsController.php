<?php
/**
 * Controller for tower mounts.
 *
 * This is a very basic controller to add/view/update/delete tower mounts.
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
 * @since         TowerMountsController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class TowerMountsController extends AppController {

    /*
     * Main listing for all TowerMounts
     */
    public function index() {
        $this->TowerMount->recursive = 0;
        $this->set('towermounts', $this->paginate());
    }

    /*
     * View an existing TowerMount
     */
    public function view($id = null) {
        $this->TowerMount->id = $id;
        if (!$this->TowerMount->exists()) {
                throw new NotFoundException('Invalid tower mount');
        }
        $this->set('towermount', $this->TowerMount->read(null, $id));
    }

    /*
     * Add a new TowerMount
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->TowerMount->create();
            if ($this->TowerMount->save($this->request->data)) {
                $this->Session->setFlash('The tower mount has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The tower mount could not be saved. Please, try again.');
            }
        }
    }

    /*
     * Edit an existing TowerMount
     */
    public function edit($id = null) {
        $this->TowerMount->id = $id;
        if (!$this->TowerMount->exists()) {
                throw new NotFoundException('Invalid tower mount');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->TowerMount->save($this->request->data)) {
                $this->Session->setFlash('The tower mount has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The tower mount could not be saved. Please, try again.');
            }
        } else {
                $this->request->data = $this->TowerMount->read(null, $id);
        }
    }

    /*
     * Delete an existing TowerMount
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->TowerMount->id = $id;
        if (!$this->TowerMount->exists()) {
            throw new NotFoundException('Invalid tower mount');
        }
        if ($this->TowerMount->delete()) {
            $this->Session->setFlash('TowerMount deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  TowerMount was not deleted.');
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

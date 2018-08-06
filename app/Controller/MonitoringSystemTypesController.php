<?php
/**
 * Controller for monitoring system types.
 *
 * This is a very basic controller to add/view/update/delete monitoring system types.
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
 * @since         MonitoringSystemTypesController was introduced in Poundcake v2.3
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class MonitoringSystemTypesController extends AppController {

    /*
     * Main listing for all MonitoringSystemTypes
     */
    public function index() {
        $this->MonitoringSystemType->recursive = 0;
        $this->set('monitoringSystemTypes', $this->paginate());
    }

    /*
     * View an MonitoringSystemType
     */
    public function view($id = null) {
        $this->MonitoringSystemType->id = $id;
        if (!$this->MonitoringSystemType->exists()) {
            throw new NotFoundException('Invalid monitoring system type');
        }
        $this->set('monitoringSystemType', $this->MonitoringSystemType->read(null, $id));
    }

    /*
     * Add a new MonitoringSystemType
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->MonitoringSystemType->create();
            if ($this->MonitoringSystemType->save($this->request->data)) {
                $this->Session->setFlash('The monitoring system type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The monitoring system type could not be saved. Please, try again.');
            }
        }
    }

    /*
     * Edit an existing MonitoringSystemType
     */
    public function edit($id = null) {
        $this->MonitoringSystemType->id = $id;
        if (!$this->MonitoringSystemType->exists()) {
            throw new NotFoundException('Invalid monitoring system type');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->MonitoringSystemType->save($this->request->data)) {
                $this->Session->setFlash('The monitoring system type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The monitoring system type could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->MonitoringSystemType->read(null, $id);
        }
    }

    /*
     * Delete an existing MonitoringSystemType
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->MonitoringSystemType->id = $id;
        if (!$this->MonitoringSystemType->exists()) {
            throw new NotFoundException('Invalid monitoring system type.');
        }
        if ($this->MonitoringSystemType->delete()) {
            $this->Session->setFlash('Monitoring system type deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Monitoring system type was not deleted.');
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

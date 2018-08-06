<?php
/**
 * Controller for switch types.
 *
 * This is a very basic controller to add/view/update/delete switch types.
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
 * @since         SwitchTypesController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('NetworkDeviceTypeController', 'Controller');

class SwitchTypesController extends NetworkDeviceTypeController {

    /*
     * Main listing for all SwitchTypes
     */
    public function index() {
        $this->SwitchType->recursive = 0;
        $this->set('switchTypes', $this->paginate());
    }

    /*
     * Add a new SwitchType
     */
    public function add() {
        if ($this->request->is('post')) {
//            var_dump( $this->request->data ); die;
            $this->SwitchType->create();
            if ($this->SwitchType->save($this->request->data)) {
                $this->Session->setFlash('The switch type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The switch type could not be saved. Please, try again.');
            }
        }
    }
    
    /*
     * Edit an existing SwitchType
     */
    public function edit($id = null) {
        $this->SwitchType->id = $id;
        if (!$this->SwitchType->exists()) {
            throw new NotFoundException('Invalid switch type');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->SwitchType->save($this->request->data)) { // saveAll for the HABTM through the join model
                $this->Session->setFlash('The switch type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The switch type could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->SwitchType->read(null, $id);
        }
    }
    
    /*
     * Delete an existing SwitchType
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->SwitchType->id = $id;
        if (!$this->SwitchType->exists()) {
            throw new NotFoundException('Invalid switch type');
        }
        if ($this->SwitchType->delete()) {
            $this->Session->setFlash('Switch type deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Switch type was not deleted.');
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

<?php
/**
 * Controller for contact types.
 *
 * This is a very basic controller to add/view/update/delete contact types.
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
 * @since         ContactTypesController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class ContactTypesController extends AppController {

    /*
     * Main listing for all ContactTypes
     */
    public function index() {
        $this->ContactType->recursive = 0;
        $this->set('contactTypes', $this->paginate());
    }

    /*
     * View a ContactType
     */
    public function view($id = null) {
        $this->ContactType->id = $id;
        if (!$this->ContactType->exists()) {
                throw new NotFoundException('Invalid contact type');
        }
        $this->set('roadType', $this->ContactType->read(null, $id));
    }

    /*
     * Add a new ContactType
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->ContactType->create();
            if ($this->ContactType->save($this->request->data)) {
                $this->Session->setFlash('The contact type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The contact type could not be saved. Please, try again.');
            }
        }
    }

    /*
     * Edit an existing ContactType
     */
    public function edit($id = null) {
        $this->ContactType->id = $id;
        if (!$this->ContactType->exists()) {
                throw new NotFoundException('Invalid contact type');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->ContactType->save($this->request->data)) {
                $this->Session->setFlash('The contact type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The contact type could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->ContactType->read(null, $id);
        }
    }

    /*
     * Delete an existing ContactType
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
                throw new MethodNotAllowedException();
        }
        $this->ContactType->id = $id;
        if (!$this->ContactType->exists()) {
                throw new NotFoundException('Invalid contact type.');
        }
        if ($this->ContactType->delete()) {
                $this->Session->setFlash('Contact type deleted.');
                $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Contact type was not deleted.');
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

<?php
/**
 * Controller for SNMP types (should be named SNMP versions - but named types
 * for general consistency).
 *
 * This is a very basic controller to add/view/update/delete SNMP types.
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
 * @since         SnmpTypesController introduced in Poundcake v2.2.4
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class SnmpTypesController extends AppController {

    /*
     * Custom pagination, sort order on index listing
     */
    public $paginate = array(
        'limit' => 20, // default limit also defined in AppController
        'order' => array(
            'SnmpType.name' => 'asc'
        )
    );
        
    /*
     * Main listing for all SnmpTypes
     */
    public function index() {
        $this->SnmpType->recursive = 0;
        $this->set('snmptypes', $this->paginate());
    }

    /*
     * View an existing SnmpType
     */
    public function view($id = null) {
        $this->SnmpType->id = $id;
        if (!$this->SnmpType->exists()) {
            throw new NotFoundException('Invalid SNMP version');
        }
        $this->set('snmptypes', $this->SnmpType->read(null, $id));
    }

    /*
     * Add a new SnmpType
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->SnmpType->create();
            if ($this->SnmpType->save($this->request->data)) {
                $this->Session->setFlash('The SNMP version has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The SNMP version could not be saved. Please, try again.');
            }
        }
    }

    /*
     * Edit an existing SnmpType
     */
    public function edit($id = null) {
        $this->SnmpType->id = $id;
        
        if (!$this->SnmpType->exists()) {
            throw new NotFoundException('Invalid SNMP version');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->SnmpType->save($this->request->data)) {
                    $this->Session->setFlash('The SNMP version has been saved.');
                    $this->redirect(array('action' => 'index'));
            } else {
                    $this->Session->setFlash('Error!  The SNMP version could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->SnmpType->read(null, $id);
        }
    }
    
    /*
     * Delete an existing SnmpType
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->SnmpType->id = $id;
        if (!$this->SnmpType->exists()) {
            throw new NotFoundException('Invalid SNMP version');
        }
        if ($this->SnmpType->delete()) {
            $this->Session->setFlash('SNMP version deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  SNMP version was not deleted.');
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

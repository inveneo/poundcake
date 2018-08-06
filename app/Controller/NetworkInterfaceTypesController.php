<?php
/**
 * Controller for network interface types.
 *
 * This is a very basic controller to add/view/update/delete interface names.
 * 
 * These tasks would typically be performed by a user with administrative level
 * permissions within Poundcake.
 *
 * Developed against CakePHP 2.3.0 and PHP 5.4.x.
 *
 * Copyright 2013, Inveneo, Inc. (http://www.inveneo.org)
 *
 * Licensed under GNU General Public License.
 * 
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2013, Inveneo, Inc. (http://www.inveneo.org)
 * @author        Inveneo Dev Team <info@inveneo.org>
 * @link          http://www.inveneo.org
 * @package       app.Controller
 * @since         NetworkInterfacesController introduced in Poundcake v3.1.0
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class NetworkInterfaceTypesController extends AppController {

    /*
     * Custom pagination, sort order on index listing
     */
    public $paginate = array(
        'limit' => 20, // default limit also defined in AppController
        'order' => array(
            'NetworkInterfaceType.name' => 'asc'
        )
    );
        
    /*
     * Main listing for all NetworkInterfaces
     */
    public function index() {
        $this->NetworkInterfaceType->recursive = 0;
        $this->set('networkInterfaceTypes', $this->paginate());
    }

    /*
     * View an existing NetworkInterface
     */
    public function view($id = null) {
        $this->NetworkInterfaceType->id = $id;
        if (!$this->NetworkInterfaceType->exists()) {
            throw new NotFoundException('Invalid network interface type');
        }
        $this->set('networkInterfaceTypes', $this->NetworkInterfaceType->read(null, $id));
    }

    /*
     * Add a new NetworkInterface
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->NetworkInterfaceType->create();
            if ($this->NetworkInterfaceType->save($this->request->data)) {
                $this->Session->setFlash('The network interface type has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The network interface type could not be saved. Please, try again.');
            }
        }
    }

    /*
     * Edit an existing NetworkInterface
     */
    public function edit($id = null) {
        $this->NetworkInterfaceType->id = $id;
        
        if (!$this->NetworkInterfaceType->exists()) {
            throw new NotFoundException('Invalid network interface type');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->NetworkInterfaceType->save($this->request->data)) {
                    $this->Session->setFlash('The network interface type has been saved.');
                    $this->redirect(array('action' => 'index'));
            } else {
                    $this->Session->setFlash('Error!  The network interface type could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->NetworkInterfaceType->read(null, $id);
        }
    }
    
    /*
     * Delete an existing NetworkInterface
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->NetworkInterfaceType->id = $id;
        if (!$this->NetworkInterfaceType->exists()) {
            throw new NotFoundException('Invalid network interface type');
        }
        if ($this->NetworkInterfaceType->delete()) {
            $this->Session->setFlash('Network interface type deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Network interface type was not deleted.');
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

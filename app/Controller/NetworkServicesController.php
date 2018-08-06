<?php
/**
 * Controller for network services (ICMP, SNMP, etc.).
 *
 * This is a very basic controller to add/view/update/delete network services.
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
 * @copyright     Copyright 2013, Inveneo, Inc. (http://www.inveneo.org)
 * @author        Inveneo Dev Team <info@inveneo.org>
 * @link          http://www.inveneo.org
 * @package       app.Controller
 * @since         NetworkServicesController was introduced in Poundcake v2.3
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class NetworkServicesController extends AppController {

    /*
     * Custom pagination, sort order on index listing
     */
    public $paginate = array(
        'limit' => 20, // default limit also defined in AppController
        'order' => array(
            'NetworkService.name' => 'asc'
        )
    );
        
    /*
     * Main listing for all NetworkServices
     */
    public function index() {
        $this->NetworkService->recursive = 0;
        $this->set('networkservices', $this->paginate());
    }

    /*
     * View an existing NetworkService
     */
    public function view($id = null) {
        $this->NetworkService->id = $id;
        if (!$this->NetworkService->exists()) {
            throw new NotFoundException('Invalid network service');
        }
        $this->set('networkservices', $this->NetworkService->read(null, $id));
    }

    /*
     * Add a new NetworkService
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->NetworkService->create();
            if ($this->NetworkService->save($this->request->data)) {
                $this->Session->setFlash('The network service has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The network service could not be saved. Please, try again.');
            }
        }
    }

    /*
     * Edit an existing NetworkService
     */
    public function edit($id = null) {
        $this->NetworkService->id = $id;
        
        if (!$this->NetworkService->exists()) {
            throw new NotFoundException('Invalid network service');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->NetworkService->save($this->request->data)) {
                    $this->Session->setFlash('The network service has been saved.');
                    $this->redirect(array('action' => 'index'));
            } else {
                    $this->Session->setFlash('Error!  The network service could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->NetworkService->read(null, $id);
        }
    }
    
    /*
     * Delete an existing NetworkService
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->NetworkService->id = $id;
        if (!$this->NetworkService->exists()) {
            throw new NotFoundException('Invalid network service');
        }
        if ($this->NetworkService->delete()) {
            $this->Session->setFlash('network service deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  network service was not deleted.');
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

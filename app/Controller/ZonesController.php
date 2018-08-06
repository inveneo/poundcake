<?php
/**
 * Controller for zones.
 *
 * This is a very basic controller to add/view/update/delete zones.
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
 * @since         ZonesController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class ZonesController extends AppController {

    /*
     * Custom pagination, sort order on index listing
     */
    public $paginate = array(
        'limit' => 20, // default limit also defined in AppController
        'order' => array(
            'Zone.name' => 'asc'
        )
    );
    
    /*
     * Main listing for all Zones
     */
    public function index() {
        $this->Zone->recursive = 0;
        $this->set('zones', $this->paginate());
    }

    /*
     * View a Zone
     */
    public function view($id = null) {
        $this->Zone->id = $id;
        if (!$this->Zone->exists()) {
            throw new NotFoundException('Invalid zone');
        }
        $this->set('zone', $this->Zone->read(null, $id));
    }

    /*
     * Add a new Zone
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->Zone->create();
            if ($this->Zone->save($this->request->data)) {
                $this->Session->setFlash('The zone has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The zone could not be saved. Please, try again.');
            }
        }
    }

    /*
     * Edit an existing Zone
     */
    public function edit($id = null) {
        $this->Zone->id = $id;
        if (!$this->Zone->exists()) {
            throw new NotFoundException('Invalid province');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Zone->save($this->request->data)) {
                $this->Session->setFlash('The zone has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The zone could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->Zone->read(null, $id);
        }
    }

    /*
     * Delete an existing Zone
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Zone->id = $id;
        if (!$this->Zone->exists()) {
            throw new NotFoundException('Invalid zone');
        }
        if ($this->Zone->delete()) {
            $this->Session->setFlash('Zone deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Zone was not deleted.');
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

<?php
/**
 * Controller for build items.
 *
 * This is a very basic controller to add/view/update/delete build items, what
 * appears within an equipment list.
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
 * @since         BuildItemsController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class BuildItemsController extends AppController {

    /*
     * Custom pagination, sort order on index listing
     */
    public $paginate = array(
        'limit' => 20, // default limit also defined in AppController
        'order' => array(
            'BuildItem.name' => 'asc'
        )
    );
    
    /*
     * Main listing for all BuildItemss
     */
    public function index() {
        $this->BuildItem->recursive = 0;
        $this->set('builditems', $this->paginate());
    }

    /*
     * View a BuildItem
     */
    public function view($id = null) {
        $this->BuildItem->id = $id;
        if (!$this->BuildItem->exists()) {
            throw new NotFoundException('Invalid build item');
        }
        $this->set('builditem', $this->BuildItem->read(null, $id));
    }

    /*
     * Add a new BuildItem
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->BuildItem->create();
            if ($this->BuildItem->save($this->request->data)) {
                $this->Session->setFlash('The build item has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The build item could not be saved. Please, try again.');
            }
        }
        parent::getProjects();
        $this->getBuildItemTypes();
    }
    
    /*
     * Edit an existing BuildItem
     */
    public function edit($id = null) {
        $this->BuildItem->id = $id;
        if (!$this->BuildItem->exists()) {
            throw new NotFoundException('Invalid build item');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->BuildItem->save($this->request->data)) {
                    $this->Session->setFlash('The build item has been saved.');
                    $this->redirect(array('action' => 'index'));
            } else {
                    $this->Session->setFlash('Error!  The build item could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->BuildItem->read(null, $id);
        }
        parent::getProjects();
        $this->getBuildItemTypes();
    }
    
    /*
     * Delete an existing BuildItem
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->BuildItem->id = $id;
        if (!$this->BuildItem->exists()) {
            throw new NotFoundException('Invalid build item');
        }
        if ($this->BuildItem->delete()) {
            $this->Session->setFlash('Build item deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Build item was not deleted.');
        $this->redirect(array('action' => 'index'));
    }
    
    /*
     * Save an array of sites the build item may be assigned to
     */
    function getSites() {
        $this->set('sites',$this->BuildItem->Site->find('list',
            array(
                'order' => array(
                    'Site.code',
                    'Site.name ASC'
            )))
        );
    }
    
    /*
     * Save an array of build item types the build item may be defined as
     */
    function getBuildItemTypes() {
        $this->set('builditemtypes',$this->BuildItem->BuildItemType->find('list'));
    }
    
    /*
     * Check the user's role to determine if sufficient permission to perform
     * the intended action.
     */
    public function isAuthorized($user) {
        
        $allowed = array( "index", "view" );
        if ( in_array( $this->action, $allowed )) {
            return true;
        }
        
        $allowed = array( "add", "edit", "delete" );
        if ( in_array( $this->action, $allowed )) {
            if ( $this->Session->read('role') === 'edit') {
                return true;
            }
        }
        
        return parent::isAuthorized($user);
    }
}

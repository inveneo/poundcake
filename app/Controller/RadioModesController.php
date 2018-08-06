<?php
/**
 * Controller for radio modes.
 *
 * This is a very basic controller to add/view/update/delete radio modes.
 * 
 * RadioModes can have inverse mode -- for example if radios A and B are linked,
 * and radio A is set to the radio mode of "Access Point", radio B is a "Station"
 * -- thus, Station is the inverse of Access Point.
 * 
 * This would not be true for mesh radio setups, and thus that field is optional.
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
 * @since         RadioModesController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class RadioModesController extends AppController {

    
    /*
     * Main listing for all RadioModes
     */
    public function index() {
        $this->RadioMode->recursive = 0;
        $radiomodes = $this->paginate();
        // this is really for display purposes only, we need to get and set the
        // name of the inverse radio mode, if there is one
        foreach ($radiomodes as $key => $value) {
            $radiomodes[$key]['RadioMode']['inverse_mode_name'] = $this->getRadioModeName( $value['RadioMode']['inverse_mode_id'] );
        }
        $this->set('radiomodes', $radiomodes);
    }

    
    /*
     * View an existing RadioMode
     */
    public function view($id = null) {
        $this->RadioMode->id = $id;
        if (!$this->RadioMode->exists()) {
                throw new NotFoundException('Invalid radio mode');
        }
        $this->set('radiomode', $this->RadioMode->read(null, $id));
    }

    /*
     * Add a new RadioMode
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->RadioMode->create();
            if ($this->RadioMode->save($this->request->data)) {
                $this->Session->setFlash('The radio mode has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The radio mode could not be saved. Please, try again.');
            }
        }
        $this->getRadioModes();
    }

    
    /*
     * Edit an existing RadioMode
     */
    public function edit($id = null) {
        $this->RadioMode->id = $id;
        if (!$this->RadioMode->exists()) {
                throw new NotFoundException('Invalid radio mode');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->RadioMode->save($this->request->data)) {
                $this->Session->setFlash('The radio mode has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The radio mode could not be saved. Please, try again.');
            }
        } else {
                $this->request->data = $this->RadioMode->read(null, $id);
        }
        $this->getRadioModes();
    }

    /*
     * Save an array of RadioModes to -- used in the add/edit select list for
     * picking the inverse mode
     */
    public function getRadioModes() {
        $radiomodes = $this->RadioMode->find('list');
        $this->set('radiomodes', $radiomodes);
    }
    
    /*
     * Return the name for a given RadioMode
     */
    private function getRadioModeName($id) {
        $name = '';
        if ( $id > 0 ) {
            $data = $this->RadioMode->findById($id);
            $name = $data['RadioMode']['name'];
        }
        return $name;
    }
    
    /*
     * Delete na existing RadioMode
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->RadioMode->id = $id;
        if (!$this->RadioMode->exists()) {
            throw new NotFoundException('Invalid radio mode');
        }
        if ($this->RadioMode->delete()) {
            $this->Session->setFlash('Tower equipment deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Tower equipment was not deleted.');
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

<?php
/**
 * Controller for frequencies.
 *
 * This is a very basic controller to add/view/update/delete frequencies.
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
 * @since         FrequenciesController was introduced in Poundcake v2.7.0
 * @license       GNU General Public License
 */

class FrequenciesController extends AppController {

    /*
     * Main listing for all TowerrTypes
     */
    public function index() {
        $this->Frequency->recursive = 0;
        // yes, I do realize that the plural of equipment is equipment
        $this->set('frequencies', $this->paginate());
    }

    /*
     * View a Frequency
     */
    public function view($id = null) {
        $this->Frequency->id = $id;
        if (!$this->Frequency->exists()) {
                throw new NotFoundException('Invalid frequency');
        }
        $this->set('frequency', $this->Frequency->read(null, $id));
    }

    /*
     * Add a new Frequency
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->Frequency->create();
            if ($this->Frequency->save($this->request->data)) {
                $this->Session->setFlash('The frequency has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The frequency could not be saved. Please, try again.');
            }
        }
        $this->getRadioBands();
    }

    /*
     * Edit an existing TowerrType
     */
    public function edit($id = null) {
        $this->Frequency->id = $id;
        if (!$this->Frequency->exists()) {
            throw new NotFoundException('Invalid frequency');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Frequency->save($this->request->data)) {
                $this->Session->setFlash('The frequency has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The frequency could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->Frequency->read(null, $id);
        }
        $this->getRadioBands();
    }

    /*
     * Delete an existing Frequency
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Frequency->id = $id;
        if (!$this->Frequency->exists()) {
            throw new NotFoundException('Invalid frequency');
        }
        if ($this->Frequency->delete()) {
            $this->Session->setFlash('Frequency deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Frequency was not deleted.');
        $this->redirect(array('action' => 'index'));
    }
    
    /*
     * Set an array of radio bands
     */
    private function getRadioBands() {  
        $this->set('radiobands',$this->Frequency->RadioBand->find('list'));
        
    }
    /*
     * Uses Auth to check the ACL to see if the user is allowed to perform any
     * actions in this controller
     */
    public function isAuthorized($user) {
        return parent::isAuthorized($user);
    }
}

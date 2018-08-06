<?php
/**
 * Controller for configuration templates.
 *
 * This is a very basic controller to add/view/update/delete configuration templates.
 * 
 * These tasks would typically be performed by a user with administrative level
 * permissions within Poundcake.
 *
 * Developed against CakePHP 2.3.5 and PHP 5.4.x.
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
 * @since         ConfigurationTemplatesController was introduced in Poundcake v3.1.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class ConfigurationTemplatesController extends AppController {

    /*
     * Main listing for all Configuration Templates
     */
    public function index() {
        $this->ConfigurationTemplate->recursive = 0;
        $this->set('configuration_templates', $this->paginate());
    }

    /*
     * View an existing Configuration Template
     */
    public function view($id = null) {
        $this->ConfigurationTemplate->id = $id;        
        if (!$this->ConfigurationTemplate->exists()) {
            throw new NotFoundException('Invalid configuration template');
        }
        $this->set('configuration_templates', $this->ConfigurationTemplate->read(null, $id));
        
    }

    /*
     * Add a new Configuration Template
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->ConfigurationTemplate->create();
            if ($this->ConfigurationTemplate->save($this->request->data)) {
                $this->Session->setFlash('The configuration template has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The configuration template could not be saved. Please, try again.');
            }
        }
        parent::getProjects();
        $this->getAppliesToOptions();
    }

    /*
     * Edit an existing Configuration Template
     */
    public function edit($id = null) {
        $this->ConfigurationTemplate->id = $id;
        if (!$this->ConfigurationTemplate->exists()) {
            throw new NotFoundException('Invalid configuration template');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
//            echo '<pre>';
//            var_dump($this->request->data);
//            die;
            if ($this->ConfigurationTemplate->save($this->request->data)) {
                $this->Session->setFlash('The configuration template has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The configuration template could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->ConfigurationTemplate->read(null, $id);
        }
        parent::getProjects();
        $this->getAppliesToOptions( $this->ConfigurationTemplate->data['ConfigurationTemplate']['type'] );        
    }
    
    /*
     * Delete an existing Configuration Template
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->ConfigurationTemplate->id = $id;
        if (!$this->ConfigurationTemplate->exists()) {
            throw new NotFoundException('Invalid configuration template');
        }
        if ($this->ConfigurationTemplate->delete()) {
            $this->Session->setFlash('Configuration Template deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Configuration Template was not deleted.');
        $this->redirect(array('action' => 'index'));
    }

    private function getAppliesToOptions( $selected = null ) {
        $options = array(
            // these are constants defined in AppController
            parent::NETWORKRADIO => 'Radio',
            parent::NETWORKROUTER => 'Router',
            parent::NETWORKSWITCH => 'Switch',
        );
        if ( !isset($selected )) {
            $selected = parent::NETWORKRADIO;
        }
        $this->set(compact('options','selected'));
    }
    /*
     * Uses Auth to check the ACL to see if the user is allowed to perform any
     * actions in this controller
     */
    public function isAuthorized($user) {
        return parent::isAuthorized($user);
    }
}

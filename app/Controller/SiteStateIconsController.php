<?php
/**
 * Controller for site state icon icons.
 *
 * This is a very basic controller to add/view/update/delete site icons.
 * 
 * These tasks would typically be performed by a user with administrative level
 * permissions within Poundcake.
 *
 * Developed against CakePHP 2.3.0 and PHP 5.4.10.
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
 * @since         SiteStateIconIconsController was introduced in Poundcake v3.0.0
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class SiteStateIconsController extends AppController {

    public $paginate = array(
        'limit' => 20, // default limit also defined in AppController
    );
    
    /*
     * Main listing for all SiteStateIcons
     */
    public function index() {
        $this->SiteStateIcon->recursive = -1;
        $this->set('siteStateIcons', $this->paginate());
    }

    /*
    public function view($id = null) {
        $this->SiteStateIcon->id = $id;
        if (!$this->SiteStateIcon->exists()) {
                throw new NotFoundException('Invalid site state icon');
        }
        $this->set('siteState', $this->SiteStateIcon->read(null, $id));
    }
    */
    
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->SiteStateIcon->create();
            //echo "<pre>";
            //print_r($this->request->data['SiteStateIcon']);
            //echo "</pre>";
            
            // http://cakebaker.42dh.com/2006/04/15/file-upload-with-cakephp/
            if (is_uploaded_file($this->request->data['SiteStateIcon']['File']['tmp_name'])) {
                    $fileData = fread(fopen($this->request->data['SiteStateIcon']['File']['tmp_name'], "r"), $this->request->data['SiteStateIcon']['File']['size']);

                    $this->request->data['SiteStateIcon']['img_name'] = $this->request->data['SiteStateIcon']['File']['name'];
                    $this->request->data['SiteStateIcon']['img_type'] = $this->request->data['SiteStateIcon']['File']['type'];
                    $this->request->data['SiteStateIcon']['img_size'] = $this->request->data['SiteStateIcon']['File']['size'];
                    $this->request->data['SiteStateIcon']['img_data'] = $fileData;
                
                //echo "img_name " . $this->request->data['SiteStateIcon']['img_name'] . "<BR>";
                //echo "img_type " . $this->request->data['SiteStateIcon']['img_type'] . "<BR>";
                //echo "img_size " . $this->request->data['SiteStateIcon']['img_size'] . "<BR>";
                //echo "img_data " . $this->request->data['SiteStateIcon']['img_data'];
                }        
            if ($this->SiteStateIcon->save($this->request->data)) {
                $this->Session->setFlash('The site state icon has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The site state icon could not be saved. Please, try again.');
            }
        }        
    }
    
    public function edit($id = null) {
        $this->SiteStateIcon->id = $id;
        if (!$this->SiteStateIcon->exists()) {
            throw new NotFoundException('Invalid site state icon');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            
            // debug( $this->request->data['SiteStateIcon'] );
            
            if (is_uploaded_file($this->request->data['SiteStateIcon']['File']['tmp_name'])) {
                    $fileData = fread(fopen($this->request->data['SiteStateIcon']['File']['tmp_name'], "r"), $this->request->data['SiteStateIcon']['File']['size']);

                    $this->request->data['SiteStateIcon']['img_name'] = $this->request->data['SiteStateIcon']['File']['name'];
                    $this->request->data['SiteStateIcon']['img_type'] = $this->request->data['SiteStateIcon']['File']['type'];
                    $this->request->data['SiteStateIcon']['img_size'] = $this->request->data['SiteStateIcon']['File']['size'];
                    $this->request->data['SiteStateIcon']['img_data'] = $fileData;
            }
            if ($this->SiteStateIcon->save($this->request->data)) {
                $this->Session->setFlash('The site state icon has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The site state icon could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->SiteStateIcon->read(null, $id);
        }
    }
    
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->SiteStateIcon->id = $id;
        if (!$this->SiteStateIcon->exists()) {
            throw new NotFoundException('Invalid site state icon');
        }
        if ($this->SiteStateIcon->delete()) {
            $this->Session->setFlash('Site state icon deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Site state icon was not deleted.');
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

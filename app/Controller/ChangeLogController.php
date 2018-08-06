<?php
/**
 * Controller for change log entries.
 *
 * This is a very basic controller to add/view/update/delete change log entries.
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
 * @since         ChangeLogController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class ChangeLogController extends AppController {

    /*
     * Uses our custom HTML helper to handle hyperlinks based on ACL permissions
     */
    var $helpers = array(
        'PoundcakeHTML',
    );
    
    /*
     * Main listing for all ChangeLogs
     */
    public function index() {
        $this->ChangeLog->recursive = 0;
        $this->set('changeLogs', $this->paginate());
    }

    /*
     * View a ChangeLog
     */
    public function view($id = null) {
        $this->ChangeLog->id = $id;
        if (!$this->ChangeLog->exists()) {
            throw new NotFoundException('Invalid change log');
        }
        $changeLog = $this->ChangeLog->read(null, $id);
        $changeLog['ChangeLog']['description'] =nl2br( $changeLog['ChangeLog']['description'] );
        $this->set('changeLog', $changeLog);
    }

    /*
     * Add a new ChangeLog
     */
    public function add() {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->ChangeLog->create();
            if ($this->ChangeLog->save($this->request->data)) {
                $this->Session->setFlash('The change log has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The change log could not be saved. Please, try again.');
            }
        }
    }
    
    /*
     * Edit an existing ChangeLog
     */
    public function edit($id = null) {
        $this->ChangeLog->id = $id;
        if (!$this->ChangeLog->exists()) {
            throw new NotFoundException('Invalid change log');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->ChangeLog->save($this->request->data)) {
                $this->Session->setFlash('The change log has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The change log could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->ChangeLog->read(null, $id);
        }
    }

    /*
     * Delete an existing ChangeLog
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->ChangeLog->id = $id;
        if (!$this->ChangeLog->exists()) {
            throw new NotFoundException('Invalid change log');
        }
        if ($this->ChangeLog->delete()) {
            $this->Session->setFlash('Change log deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Change log was not deleted.');
        $this->redirect(array('action' => 'index'));
    }

    /*
     * Uses Auth to check the ACL to see if the user is allowed to perform any
     * actions in this controller
     */
    public function isAuthorized($user) {
        // allow users to view the changelog
        if ($this->action === 'index' || $this->action === 'view') {
            return true;
        }
        return parent::isAuthorized($user);
    }
}

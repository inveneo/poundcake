<?php
/**
 * Controller for site states.
 *
 * This is a very basic controller to add/view/update/delete site states.
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
 * @since         SiteStatesController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class SiteStatesController extends AppController {

    /*
     * Main listing for all SiteStates
     */
    public function index() {
        // bind to the projects model so we can sort by project name (first)
        $this->SiteState->bindModel(array('belongsTo' => array('Project')), false);
        $this->paginate = array(
            // limit is the number per page 
            //'limit' => 20,
           // 'conditions' => $conditions,
            'order' => array(
                'Project.name' => 'asc',
                'SiteState.sequence' => 'asc',
            ),
        );
        
        $this->set('siteStates', $this->paginate());
    }

    
    public function view($id = null) {
        $this->SiteState->id = $id;
        if (!$this->SiteState->exists()) {
                throw new NotFoundException('Invalid site state');
        }
        $this->set('siteState', $this->SiteState->read(null, $id));
    }

    public function add() {
        $this->getProjects();
        
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $this->SiteState->create();
            if ($this->SiteState->save($this->request->data)) {
                $this->Session->setFlash('The site state has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The site state could not be saved. Please, try again.');
            }
        }
        $this->getExistingSiteStateIcons();
    }

    public function copy() {
        if ($this->request->is('post') || $this->request->is('put')) {
            $src = $this->request->data['SiteState']['project_src'];
            $dest = $this->request->data['SiteState']['project_dest'];
//            $src = 1; // HRBN project
//            $dest = 19; // Test project
            if ( $src != $dest ) {
                $conditions = array(
                    'SiteState.project_id' => $dest
                );
//                var_dump($conditions);die;
                // first, delete any site states on the target project
                $this->SiteState->deleteAll( $conditions );
                // now iterate through the ones on the soruce project and copy them
//                $this->SiteState->recursive = -1;
                $site_states = $this->SiteState->findAllByProjectId( $src );
//                var_dump($site_states);
//                echo '<pre>';
                if ( count($site_states) > 0 ) {
                    foreach( $site_states as $ss ) {
//                        var_dump( $ss );die;
                        $ss['SiteState']['id'] = null; // set ID to null so it saves a new record
                        $ss['SiteState']['project_id'] = $dest; // set new project ID
                        $this->SiteState->save( $ss ); // and save it                        
                    }
                    // now we need to set any existing sites in the destination project
                    // to the SiteState that has the lowest sequence number
                    $sequence = $this->SiteState->find('first', array(
                        'conditions' => array('SiteState.project_id' => $dest ),
                        'order' => array('SiteState.sequence' => 'asc')                       
                    ));
                    $this->loadModel('Site');
                    $this->Site->updateAll(
                            array('Site.site_state_id' => $sequence['SiteState']['id']),
                            array('Site.project_id =' => $dest)
                    );
                } else {
                    $this->Session->setFlash('Error!  No site states on source project.');
                    $this->redirect(array('action' => 'index'));
                }
//                echo '</pre>';
                $this->Session->setFlash('Site states successfully copied to destination project.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  Cannot copy site states between the same project.  Please, try again.');
            }
        }
        $this->getProjects();
    }
    
    private function getExistingSiteStateIcons() {
//        $conditions = array(
//            'fields' => array('DISTINCT SiteState.img_data AS img_data')
//        );
        $all_icons = $this->SiteState->SiteStateIcon->find('all');
        $this->set(compact('all_icons'));
        
    }
    
    public function edit($id = null) {
        $this->SiteState->id = $id;
        if (!$this->SiteState->exists()) {
            throw new NotFoundException('Invalid site state');
        }
        
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->SiteState->save($this->request->data)) {
                $this->Session->setFlash('The site state has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error!  The site state could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->SiteState->read(null, $id);
            $this->getProjects();
        }
        $this->getExistingSiteStateIcons();
    }

    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->SiteState->id = $id;
        if (!$this->SiteState->exists()) {
            throw new NotFoundException('Invalid site state');
        }
        if ($this->SiteState->delete()) {
            $this->Session->setFlash('Site state deleted.');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Error!  Site state was not deleted.');
        $this->redirect(array('action' => 'index'));
    }
    
    /* used for downloading files
    function download($id) {
        Configure::write('debug', 0);
        $file = $this->MyFile->findById($id);

        header('Content-type: ' . $file['MyFile']['type']);
        header('Content-length: ' . $file['MyFile']['size']); // some people reported problems with this line (see the comments), commenting out this line helped in those cases
        header('Content-Disposition: attachment; filename="'.$file['MyFile']['name'].'"');
        echo $file['MyFile']['data'];

        exit();
    }
    */
    
    /*
     * Uses Auth to check the ACL to see if the user is allowed to perform any
     * actions in this controller
     */
    public function isAuthorized($user) {
        return parent::isAuthorized($user);
    }
}

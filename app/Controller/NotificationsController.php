<?php
/**
 * Controller for notifications.
 *
 * This is a ultra basic controller to edit system notifications, which appear
 * in the header to give users a heads up to downtimes, etc.  Available to
 * an administrator only and with one function -- edit.
 *
 * Developed against CakePHP 2.2.5 and PHP 5.4.x.
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
 * @since         NotificationsController was introduced in Poundcake v2.5.2
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class NotificationsController extends AppController {

    
    /*
     * Edit the notification -- there should only be one!
     */
    public function edit($id = 1) { // hard coded to 1
        $this->Notification->id = $id;
        
        if (!$this->Notification->exists()) {
            debug("2");
            throw new NotFoundException('Invalid notification');
        }
        
        if ($this->request->is('post') || $this->request->is('put')) {
//            echo '<pre>';
//            var_dump( $this->request->data );
            // '<i class="icon-search"></i>';
//            $icon = $this->request->data['icons'];
//           
//            if ( $icon != 'None') {                 
////                $icon_f = '<i class="'.$icon.'"></i>';
////                echo "Icon:";
////                print_r( $icon_f );
//                $icon = "<i class\"$icon\"></i>";
//                $msg = $this->request->data['Notification']['message'];
//                $this->request->data['Notification']['message'] = $icon . $msg;
//            }
//            echo "Data:";
//            print_r( $this->request->data['Notification']['message'] );
//            echo "done";
//            die;
            if ($this->Notification->save($this->request->data)) {
                    $this->Session->setFlash('The notification has been saved.');
                    $this->redirect(array('controller' => 'admin', 'action' => 'setup'));
            } else {
                    $this->Session->setFlash('Error!  The notification could not be saved. Please, try again.');
            }
        } else {
            $this->request->data = $this->Notification->read(null, $id);
        }
//        $this->getIcons();
    }
    
//    private function getIcons() {
//        // http://twitter.github.com/bootstrap/base-css.html#images
//        $icons[ 'none' ] = 'None';
//        $icons[ 'icon-hand-right' ] = '<i class="icon-hand-right"></i>';
//        $icons[ 'icon-star' ] = '<i class="icon-star"></i>';
//        $icons[ 'icon-wrench' ] = '<i class="icon-wrench"></i>';
//        
//        $this->set(compact('icons'));
//    }
    
    /*
     * Uses Auth to check the ACL to see if the user is allowed to perform any
     * actions in this controller
     */
    public function isAuthorized($user) {
        return parent::isAuthorized($user);
    }
}

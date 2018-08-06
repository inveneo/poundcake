<?php
/**
 * Super class controller for network device types (radiotypes, routertypes, switchetypes).
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
 * @since         NetworkDeviceTypeController was introduced in Poundcake v3.1.5
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class NetworkDeviceTypeController extends AppController {
    
    /*
     * Remove elements from the NetworkInterfaceTypes array that are have number of 0
     * (by default the array of NetworkInterfaceTypes that comes back from the add/edit
     * screen includes items with quanity of 0)
     */
    protected function purgeEmptyNetworkInterfaceTypes( $array ) {
        // nice trick, see http://stackoverflow.com/questions/14759647/remove-array-if-any-element-is-empty
        return array_filter( $array, function($item) {
//            echo '<pre>';
//            print_r( $item );
//            echo '</pre>';
//            return ( $item['number'] > 0 && $item['network_interface_type_id'] > 0 );
            if ( isset( $item['number'] ) && ( $item['network_interface_type_id'] ) ) {
                return ( $item['number'] > 0 && $item['network_interface_type_id'] > 0 );
            }
        });
    }
    
    protected function getNetworkInterfaceTypes( $model ) {
        // get all network interface types
        $x = $model.'NetworkInterfaceTypes';
        $y = $this->$model->$x->NetworkInterfaceType->find('all');
        $this->set('networkInterfaceTypes',$this->$model->$x->NetworkInterfaceType->find('all'));
    }
    
    protected function setModelClass( $modelClass ) {
        $this->set(compact('modelClass'));
    }
}
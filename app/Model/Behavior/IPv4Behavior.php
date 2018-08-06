<?php
/**
 * Behavior to encode/decode IPv4 addresses.
 *
 * Developed against CakePHP 2.2.3 and PHP 5.4.x.
 *
 * Copyright 2013, Inveneo, Inc. (http://www.inveneo.org)
 *
 * Licensed under GNU General Public License.
 * 
 * This file is part of Poundcake.
 * 
 * Poundcake is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Poundcake is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2013, Inveneo, Inc. (http://www.inveneo.org)
 * @author        Inveneo Dev Team <info@inveneo.org>
 * @link          http://www.inveneo.org
 * @package       app.Model.Behavior
 * @since         IPv4Behavior was introduced in Poundcake v2.3
 * @license       GNU General Public License
 */

class IPv4Behavior extends ModelBehavior { 
    var $settings = array(); 
    
    function setup(Model $model, $settings = array()) { 
        if (!isset($this->settings[$model->alias])) { 
            $this->settings[$model->alias] = array( 
                'fields' => array() 
            ); 
        } 

        $this->settings[$model->alias] = array_merge($this->settings[$model->alias], $settings); 
    } 
    
    /*
     * Encode the IPv4 address to an unsigned in before saving to the database
     */
    function beforeSave(Model $model, $options = array()) { 
        foreach ($this->settings[$model->alias]['fields'] AS $field) { 
            if (isset($model->data[$model->alias][$field])) {
                $model->data[$model->alias]['d_'.$field] = $model->data[$model->alias][$field]; 
                $model->data[$model->alias][$field] = ip2long( $model->data[$model->alias][$field] ); 
            } 
        }
        return true; 
    }
    
    /*
     * Decode the IPv4 address to a human readable "dotted quad" format
     */
    function afterFind(Model $model, $results, $primary) { 
        foreach ($this->settings[$model->alias]['fields'] AS $field) {
            if ($primary) { 
                foreach ($results AS $key => $value) { 
                    if (isset($value[$model->alias][$field])) {
                        $results[$key][$model->alias][$field] = long2ip( $value[$model->alias][$field] ); 
                    } 
                } 
            } else { 
                if (isset($results[$field])) { 
                    $results[$field] = long2ip( $results[$field] ); 
                } 
            }
        }
        
        return $results; 
    }
} 
?>

<?php
/**
 * Behavior to save senstivie data.  This is not used for login authentication.
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
 * @since         CryptableBehavior was introduced in Poundcake v2.3
 * @license       GNU General Public License
 */

class CryptableBehavior extends ModelBehavior { 
    var $settings = array(); 

    /*
     * 
     */
    function setup(Model $model, $settings = array()) { 
        if (!isset($this->settings[$model->alias])) { 
            $this->settings[$model->alias] = array( 
                'fields' => array() 
            ); 
        } 

        $this->settings[$model->alias] = array_merge($this->settings[$model->alias], $settings); 
    } 

    /*
     * 
     */
    function beforeFind(Model $model, $query) { 
        $queryData = null;
        foreach ($this->settings[$model->alias]['fields'] AS $field) { 
            if (isset($queryData['conditions'][$model->alias.'.'.$field])) { 
                $queryData['conditions'][$model->alias.'.'.$field] = $this->encrypt($queryData['conditions'][$model->alias.'.'.$field]); 
            } 
        } 
        return $queryData; 
    } 

    /*
     * 
     */
    function afterFind(Model $model, $results, $primary) { 
        foreach ($this->settings[$model->alias]['fields'] AS $field) { 
            if ($primary) { 
                foreach ($results AS $key => $value) { 
                    if (isset($value[$model->alias][$field])) { 
                        $results[$key][$model->alias][$field] = $this->decrypt($value[$model->alias][$field]); 
                    } 
                } 
            } else { 
                if (isset($results[$field])) { 
                    $results[$field] = $this->decrypt($results[$field]); 
                } 
            } 
        } 

        return $results; 
    } 

    /*
     * 
     */
    function beforeSave(Model $model, $options = array()) { 
        foreach ($this->settings[$model->alias]['fields'] AS $field) { 
            if (isset($model->data[$model->alias][$field])) { 
                $model->data[$model->alias]['cleartext_'.$field] = $model->data[$model->alias][$field]; 
                $model->data[$model->alias][$field] = $this->encrypt($model->data[$model->alias][$field]); 
            } 
        } 
        return true; 
    } 

    /*
     * 
     */
    public function encrypt($data) { 
        if ($data !== '') { 
            $td = mcrypt_module_open('tripledes', '', 'ecb', ''); 
            mcrypt_generic_init($td, Configure::read('Cryptable.key'), Configure::read('Cryptable.iv')); 
            $encrypted_data = mcrypt_generic($td, $data); 
            mcrypt_generic_deinit($td); 
            mcrypt_module_close($td); 
            return base64_encode($encrypted_data); 
        } else { 
            return ''; 
        } 
    }
    
    /*
     * 
     */
    public function decrypt($data, $data2 = null) { 
        if (is_object($data)) { 
            unset($data); 
            $data = $data2; 
        } 

        if ($data != '') { 
            $td = mcrypt_module_open(Configure::read('Cryptable.cipher'), '', 'ecb', ''); 
            mcrypt_generic_init($td, Configure::read('Cryptable.key'), Configure::read('Cryptable.iv')); 
            $decrypted_data = mdecrypt_generic($td, base64_decode($data)); 
            mcrypt_generic_deinit($td); 
            mcrypt_module_close($td); 
            return trim($decrypted_data); 
        } else { 
            return ''; 
        } 
    }
} 
?>

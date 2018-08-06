<?php
/**
 * Model for user.
 *
 * Developed against CakePHP 2.2.3 and PHP 5.4.x.
 *
 * Copyright 2012, Inveneo, Inc. (http://www.inveneo.org)
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
 * @copyright     Copyright 2012, Inveneo, Inc. (http://www.inveneo.org)
 * @author        Inveneo Dev Team <info@inveneo.org>
 * @link          http://www.inveneo.org
 * @package       app.Model
 * @since         User precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

// following this example:
// http://book.cakephp.org/2.0/en/tutorials-and-examples/blog-auth-example/auth.html

App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel {
    
    /*
     * Relations
     */
    // var $belongsTo = array( 'Role' );
    
    /*
     * Default sort order
     */
    var $order = 'User.username ASC';
    
    /*
     * Relations
     * @see HABTM example: http://mrphp.com.au/code/working-habtm-form-data-cakephp
     */
    // public $hasAndBelongsToMany = array('Project');
    
    /*
     * Relations
     * @see hasMany through (The Join Model)
     * http://book.cakephp.org/1.3/en/The-Manual/Developing-with-CakePHP/Models.html
     */
    public $hasMany = array( 'ProjectMembership' ); // this is a "hasMany through" relation, similar to HABTM
    
    /*
     * Field-level validation rules
     */
    public $validate = array(
        'username' => array(
            'rule'    => '/^[a-z0-9_]{3,}$/i',
            'required' => true,
            'message' => 'Username is required, must be 3 characters or longer, and must be only letters, digits or underscores.'
        ),
        'pwd_current' => array(
            'rule'     => array('checkCurrentPassword'),
            'required' => false,
            'message' => 'Current password does not match.'
        ),
        'password' => array(
            //'rule'     => 'alphaNumeric',
            'rule'    => array('minLength', 5),
            //'required' => true,
            'message' => 'Password must be at least 5 characters.'
        )
    );
    
    /*
     * Callback function to handle password hashing
     */
    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password'])) {
            // passworld hashing is done here
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }
        return true;
    }
   
    /*
     * Returns true or false if current password matches
     */
    public function checkCurrentPassword($check) {
        //print_r($check);
        $pwd_current = $check['pwd_current'];
        
        // return true if the hash of the password the user thinks is their current
        // password (from the change password form) versus what's in the database
        $this->id = AuthComponent::user('id');;
        $this->read();
        $db_pass = $this->data['User']['password'];
        //echo "Current (pw) in db: " . $db_pass."<BR>";
        //echo "Current (pw) in form: " . AuthComponent::password($pwd_current)."<BR>";
        
        return (AuthComponent::password($pwd_current) == $db_pass);
    }
    
    function beforeValidate($options = array()) {
	foreach($this->hasAndBelongsToMany as $k=>$v) {
            if(isset($this->data[$k][$k]))
            {
                $this->data[$this->alias][$k] = $this->data[$k][$k];
            }
	}
    }
}
?>
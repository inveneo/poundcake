<?php
/**
 * Model for project.
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
 * @since         Project precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */


App::uses('AppModel', 'Model');

class Project extends AppModel {

    /*
     * Display field for select lists
     */
    public $displayField = 'name';
    
    /*
     * Relations
     */
    public $belongsTo = array(
        'SnmpType',
        'MonitoringSystemType'
    );
    
    public $hasMany = array(
        'SiteStates',
        'TowerTypes',
        'InstallTeams',
        'ProjectMembership', // NOTE:  this is a "hasMany through" relation, similar to HABTM
        'IpAddress',
        'IpSpace'
    );
    
//    public $hasAndBelongsToMany = array('BuildItem');
    
    /*
     * Default sort order
     */
    var $order = 'Project.name ASC';
    
    /*
     * CakePHP behavior to handle encrypting/decrypting sensitive fields when
     * readingor writing to the database.
     * 
     * @see http://bakery.cakephp.org/articles/utoxin/2009/08/01/cryptable-behavior
     */
    var $actsAs = array( 
        'Cryptable' => array( 
            'fields' => array( 
                'monitoring_system_password',
                'snmp_community_name',
                'secure_password',
                'insecure_password'
            ) 
        ),
        'IPv4' => array( 
            'fields' => array( 
                'dns1',
                'dns2',
            )
        )
    ); 
   
    /*
     * Field-level validation rules
     */
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'This field cannot be blank.',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            )
        ),
        'default_lat' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'This field cannot be blank.'
            ),
            'format' => array(
                'rule' => '/^-?\d{1,3}\.\d{1,14}$/',
                'message' => 'Expecting XX.XXXXX or -XX.XXXXX'
            )
        ),
        'default_lon' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'This field cannot be blank.'
            ),
            'format' => array(
                'rule' => '/^-?\d{1,3}\.\d{1,14}$/',
                'message' => 'Expecting XX.XXXXX or -XX.XXXXX'
            )
        ),
    );
    
    /*
     * Standard call back function -- if there are Sites that match this
     * project's id, return false to prevent the delete.
     */
    public function beforeDelete($cascade = true) {
        // loadModel returning an error here
       $i = ClassRegistry::init('Site')->findByProjectId( $this->id );      
       if ( !is_null( $i['Site'] ) ) {
            return false;
       } else {
           return parent::beforeDelete($cascade);
       }
    }    
}

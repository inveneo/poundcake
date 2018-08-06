<?php
/**
 * Model for build item.
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
 * @package       app.Model
 * @since         BuildItem precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */


App::uses('AppModel', 'Model');

class BuildItem extends AppModel {

    /*
     * Display field for select lists
     */
    public $displayField = 'name';

    /*
     * Relations
     */
    var $belongsTo = array(
        'BuildItemType'
    );
    
    var $hasAndBelongsToMany = array('Project');
    
    /*
     * Default sort order
     */
    var $order = 'BuildItem.name ASC';
    
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
    );
}

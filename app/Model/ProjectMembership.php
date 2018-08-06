<?php
/**
 * Model for mapping Projects to Roles -- is like a HABTM relation
 * 
 * @see hasMany through (The Join Model)
 * http://book.cakephp.org/1.3/en/The-Manual/Developing-with-CakePHP/Models.html
 *
 * Developed against CakePHP 2.2.5 and PHP 5.4.x.
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
 * @package       app.Model
 * @since         ProjectRole ws introduced in Poundcake v2.6.1
 * @license       GNU General Public License
 */


App::uses('AppModel', 'Model');

class ProjectMembership extends AppModel {

    /*
     * hasMany through (The Join Model) is a lot like HABTM except that you can
     * have additional attributes in the join table -- something that HABTM does
     * not support
     * 
     * Unfortunatley it's a poorly documented relationship.  Here are some references:
     * 
     * @see
     * http://book.cakephp.org/1.3/en/The-Manual/Developing-with-CakePHP/Models.html
     * 
     * http://bitfluxx.com/2008/05/27/cakephp-best-practices-rethinking-the-hasandbelongstomany-association.html
     * 
     */
    
    //public $belongsTo = array( 'User', 'Project' );
    public $belongsTo = array(
        'User' => array(
            'className'    => 'User',
            'foreignKey'   => 'user_id'
        ),
       'Project' => array(
            'className'    => 'Project',
            'foreignKey'   => 'project_id'
        )
    );
    
}

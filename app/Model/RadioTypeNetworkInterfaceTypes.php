<?php
/**
 * Model for mapping router types to network interface types.  This is a HABTM
 * Through the Join Model relation.
 *
 * Developed against CakePHP 2.3.5 and PHP 5.4.10.
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
 * @copyright     Copyright 2013, Inveneo, Inc. (http://www.inveneo.org)
 * @author        Inveneo Dev Team <info@inveneo.org>
 * @link          http://www.inveneo.org
 * @package       app.Model
 * @since         ChangeLog was introduced in Poundcake v2.3.1 (or so)
 * @license       GNU General Public License
 */

class RadioTypeNetworkInterfaceTypes extends AppModel {
    public $belongsTo = array(
        'RadioType' => array(
            'className'    => 'RadioType',
            'foreignKey'   => 'radio_type_id'
        ),
       'NetworkInterfaceType' => array(
            'className'    => 'NetworkInterfaceType',
            'foreignKey'   => 'network_interface_type_id'
        )
    );
}
?>

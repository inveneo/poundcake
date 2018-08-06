<?php
/**
 * Model for single IP address.
 *
 * Developed against CakePHP 2.3.0 and PHP 5.4.x.
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
 * @since         NetworkAddress was introduced in Poundcake v3.0.0
 * @license       GNU General Public License
 */


App::uses('AppModel', 'Model');

class IpSpace extends AppModel {

    /*
     * Display field for select lists
     */
    public $displayField = 'label';//'ip_address';

    /*
     * Default sort order
     */
    // var $order = 'IpSpace.ip_address ASC';
    
    /*
     * We're using a virtual field to point to the CIDR of the parent IpSpace
     * For a host record, we would not say 10.1.2.4/32 -- rather we'd say 10.1.2.4/24
     * and the "/24" comes from its parent IpSpace.
     */
    public $virtualFields = array(
        'parent_cidr' => 'SELECT cidr FROM ip_spaces WHERE id=IpSpace.parent_id',
        'gw_address' => 'SELECT INET_NTOA(ip_address) FROM ip_spaces WHERE id=IpSpace.gateway_id',
        'label' => 'CONCAT(INET_NTOA(IpSpace.ip_address)," ",IpSpace.name)'
    );
    
    /*
     * Relations
     */
    var $belongsTo = array(
        'Project'
    );
    
    
    var $actsAs = array( 
        'IPv4' => array( 
            'fields' => array( 
                'ip_address'
            ) 
        ),
        'Tree' // @see http://book.cakephp.org/2.0/en/core-libraries/behaviors/tree.html
    );
    
    public function childCountMatchingCidr($id = null, $direct = false, $cidr ) {
        $directChildren = $this->children($id, true);
        $n = 0;
        foreach ( $directChildren as $dc ) {
            if ($dc['IpSpace']['cidr'] != $cidr ) {
                unset($directChildren[$n]);
            }
            $n++;
        }
        return count($directChildren);
    }
    
    public function filterToHosts( $list ) {
        foreach ($list as $key=>$value ) {
            $this->read(null,$key);
            if($this->data["IpSpace"]["cidr"] != 32) {
                unset($list[$key]);
            }
        }
        return $list;
    }
}
?>
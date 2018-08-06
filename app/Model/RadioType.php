<?php
/**
 * Model for radio type.
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
 * @since         RadioType precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */


App::uses('AppModel', 'Model');

class RadioType extends AppModel {

    /*
     * Display field for select lists
     */
    public $displayField = 'name';

    /*
     * Default sort order
     */
    var $order = 'RadioType.name ASC';
    
    /*
     * Relations
     */
    var $belongsTo = array(
       'RadioBand'
    );
    
    public $hasMany = array(
       'NetworkRadios',
       'RadioTypeNetworkInterfaceTypes' // HABTM through the join model
    );
    
    public $hasAndBelongsToMany = array(
        'AntennaType'
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
        'manufacturer' => array(
            'notempty' => array(
                'rule' => 'alphaNumeric',
                'message' => 'Manufacturer name may not contain spaces.'
            )
        ),
        'watts' => array(
            'notempty' => array(
                'rule' => 'numeric',
                'message' => 'Watts are a required value.'
            )
        )
    );
    
    /*
     * 
     */
    public function getFrequencies( $radio_band_id = null ) {
        // this was originally in NetworkRadiosController (called when the NetworkRadio edit page
        // was displayed -- and also in the RadioTypes controller (called via jQuery when the
        // user pieced a different RadioType) -- cominging here into one model function used by
        // both
        if ( $radio_band_id == null ) {
            $radio_band_id = $this->field('radio_band_id');
        }
        
        $this->RadioBand->Frequency->recursive = -1;
        $frequencies_list = $this->RadioBand->Frequency->findAllByRadioBandId( $radio_band_id );
        $frequencies = array();
        foreach ( $frequencies_list as $f ) {
            $label = $f['Frequency']['name'].' ('.$f['Frequency']['frequency'].' MHz)';
            $frequencies[ $f['Frequency']['frequency'] ] = $label;
        }
        return $frequencies;
    }
    
    public function getAntennas( $radio_type_id = null ) {
        
        if ( $radio_type_id == null ) {
            $radio_type_id = $this->field('radio_type_id');
        }
        
        $antennatypes_tmp = $this->find('all', array(
            'conditions' => array('RadioType.id' => $radio_type_id ),
            'contain' => array('AntennaType'),
        ));
        
        $antennatypes = array();
        foreach ( $antennatypes_tmp[0]['AntennaType'] as $at ) {
            $antennatypes[ $at['id'] ] = $at['name'];            
        }
        
        return $antennatypes;
    }
}
?>
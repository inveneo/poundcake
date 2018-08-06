<?php
/**
 * Model for site.
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
 * @since         Site precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

//define('UPLOAD_DIR', WWW_ROOT . '/files/Site/196');

class Site extends AppModel {
        
    /*
     * Display field for select lists
     */
    public $displayField = 'site_vf';
    
    public $actsAs = array(
         // also see beforeUpload callback for this setting
	'Uploader.Attachment' => array(
		'file' => array(
//			'nameCallback' => '',
//			'append' => '',
//			'prepend' => '',
			'tempDir' => TMP,
//			'uploadDir' => UPLOAD_DIR,
//			'transportDir' => '',
//			'finalPath' => '',
			'dbColumn' => '',
//			'metaColumns' => array(),
//			'defaultPath' => '',
//			'overwrite' => false,
//			'stopSave' => true,
//			'allowEmpty' => true,
//			'transforms' => array(),
//			'transport' => array(),
//			'curl' => array()
		)
	)
);
    
    /*
     * Relations
     */
    var $belongsTo = array(
        'Zone',
        'SiteState',
        'Organization',
        'PowerType',
        'NetworkSwitch',
        'NetworkRouter',
        'EquipmentSpace',
        'TowerMember',
        'TowerType',
        'TowerMount',
        'InstallTeam',
        'Project'
    );
   
    /*
     * Relations
     */
    public $hasMany = array(
        'NetworkRadios' => array(
            'className' => 'NetworkRadio',
            'foreignKey' => 'site_id',
            'order' => 'switch_port'
        )
    );
    
    /*
     * Field-level validation rules
     */
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'This field cannot be blank',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            )
        ),
        'code' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'This field cannot be blank',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            )            
        )
        ,
        'lat' => array(
//            RegEx for GPS field:
//            -? allows for, but does not require, a negative sign
//            \d{1,2} requires 1 or 2 decimal digits
//            \. requires a decimal point
//            \d{5} requires exactly 5 decimal digits
//            , matches a single comma
//            (repeat the first 5 bullets)
//            $ anchors at the end of input
            'rule' => '/^-?\d{1,3}\.\d{1,14}$/',
            'message' => 'Expecting XX.XXXXX or -XX.XXXXX'
        ),
        'lon' => array(
            // same as above
            'rule' => '/^-?\d{1,3}\.\d{1,14}$/',
            'message' => 'Expecting XX.XXXXX or -XX.XXXXX'
            )
        
    );
    
    /*
     * Use CakePHP virtual fields to combine code and name
     * Note: this virtualField is also defined in the sp_nearby stored procedure
     * - that version is used to place placemarkers for nearby sites on the site
     * view page map
     */
    var $virtualFields = array('site_vf' => 'CONCAT(code, " ", Site.name)');
    
    /*
     * Default sort order
     */
    var $order = 'Site.code ASC';

    /*
     * Constructor - Note: I cannot remember why I defined a constructor here
     */
    public function __construct($id = false,$table = null,$ids = null) {
        parent::__construct($id,$table,$ids);
    }
    
    /*
     * Returns true if a site is owned by a user (was created by)
     */
    public function isOwnedBy($site, $user) {
        return $this->field('id', array('id' => $site, 'user_id' => $user)) === $site;
    }
    
    /*
     * Returns the declination for a given lat/lon pair using NOAA web service
     */
    function getDeclination($lat, $lon) {
        // old_lat/old_lon are the original lat/lon fields
        $this->old = $this->findById( $this->id );
        $old_lat = null;
        $old_lon = null;
        $dec = null;
        
        if ( array_key_exists('Site',$this->old)) {
            $old_lat = $this->old['Site']['lat'];
            $old_lon = $this->old['Site']['lon'];

            // we will return the original declination if neither the lat or lon
            // fields changed
            $dec = $this->old['Site']['declination'];
        }
        
        $dirty = false;
        // if they are different, than we need to get the declination
        if (($lat != $old_lat) || ( $lon != $old_lon)) {
            $dirty = true;
        }
        
        // OR... if lat/lon are defined and there is no declination for this
        // Site, let's try and get it -- so set dirty to true
        if ( isset($lat) && isset($lon) && ($dec == null) ) {
            $dirty = true;
        }
        
        if ( $dirty ) {
            // since Jan 2013 they appear to want the month now as part of the URL
            // so just get the current month number
            $month = date('m');
            $url = 'http://www.ngdc.noaa.gov/geomag-web/calculators/calculateDeclination?lat1='.$lat.'&startMonth='.$month.'&lon1='.$lon.'&resultFormat=csv';
            
            // for testing error codes
            // $url = 'http://httpstat.us/503';
            
            $ch = curl_init();  
            // set URL and other appropriate options  
            curl_setopt($ch, CURLOPT_URL, $url);  
            curl_setopt($ch, CURLOPT_HEADER, 0);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  

            /*
            if( curl_exec($ch) === false ) {
                echo 'Curl error: ' . curl_error($ch);
            } else {
                echo 'Operation completed without any errors';
            }
            */
            
            $output = curl_exec($ch);
            // the web service returned successfully, the string
            // "Declination Values" should appear within the output
            if ( preg_match("/.*Declination\sValues.*/", $output ) ) {
                $y = str_getcsv( $output );      
                if (count($y) > 1) {
                    $dec = $y[3];
                }
            }   
            curl_close( $ch );
            
        }
        return $dec;
    }  
    
    public function beforeUpload($options) {
        // see also move_uploaded_file in the Site controller's edit function
	$options['finalPath'] = '/files/Site/'.$this->id;
	$options['uploadDir'] = WWW_ROOT . $options['finalPath'];
        return $options;
    }
}
?>
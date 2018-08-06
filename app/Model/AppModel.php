<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
    
    var $components= array('Session');
    
    var $actsAs = array('Containable');
    
    function getDistance($lat1, $lon1, $lat2, $lon2) {
        // uses Haversine formula
        // http://sgowtham.net/blog/2009/08/04/php-calculating-distance-between-two-locations-given-their-gps-coordinates/
        
        $distance = "";
        $earth_radius = 3960.00; # in miles
        
        //global $earth_radius;
        $delta_lat = $lat2 - $lat1;
        $delta_lon = $lon2 - $lon1;

        $alpha  = $delta_lat/2;
        $beta = $delta_lon/2;
        $a = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin(deg2rad($beta)) * sin(deg2rad($beta)) ;
        $c = asin(min(1, sqrt($a)));
        $distance = 2*$earth_radius * $c;
        $distance = round($distance, 4);

        // return the distance as kilometers
        return $distance * 1.60934;
    }
    
    function getBearing($lat1, $lon1, $lat2, $lon2) {
        $b = 0.000;
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
        
        // http://mathforum.org/library/drmath/view/60398.html
        // http://mathforum.org/library/drmath/view/55417.html
        // The algorithm it gives for bearing (or course) between two points is this:
        // tc1=mod(atan2(sin(lon2-lon1)*cos(lat2),cos(lat1)*sin(lat2)-sin(lat1)*cos(lat2)*cos(lon2-lon1)),2*pi)
        // The formula gives the initial heading for a great-circle route from point A to point B. The heading will change in the course of the trip. The quantities in the formula have these meanings:
        // lon1 = longitude of point A
        // lat1 = latitude of point A
        // lon2 = longitude of point B
        // lat2 = latitude of point B
        // tc1 = direction of point B from point A (angle east of north)
        // $pi = 3.141596;
        // The magnetic poles drift over years. The current location of the magnetic north pole is somewhere around 78.3 deg N, 104.0 deg W. Use 
        // these coordinates for point B and your own location for point A when using the formula on the page above.

        //$b  = atan2(sin($lon2-$lon1)*cos($lat2),cos($lat1)*sin($lat2)-sin($lat1)*cos($lat2)*cos($lon2-$lon1)) % (2*pi());
        // http://www.ig.utexas.edu/outreach/googleearth/latlong.html
        $b  = atan2(sin($lon2-$lon1)*cos($lat2),cos($lat1)*sin($lat2)-sin($lat1)*cos($lat2)*cos($lon2-$lon1));
        $b = rad2deg($b);
        //$b = $b * (180/pi());
        //$b = ($b + 360) % 360;
        if ($b < 0) {
            $b += 360;
        }
        //echo "B is $b";
        return $b;
    }
    
    // for debugging -- print last SQL query
    function getLastQuery() {
        $dbo = $this->getDatasource();
        $logs = $dbo->getLog();
        $lastLog = end($logs['log']);
        return $lastLog['query'];
    }
    
    public function alphaNumericDashUnderscore($check) {
        // $data array is passed using the form field name as the key
        // have to extract the value to make the function generic
        $value = array_values($check);
        $value = $value[0];

        return preg_match('|^[0-9a-zA-Z_-]*$|', $value);
    }
}

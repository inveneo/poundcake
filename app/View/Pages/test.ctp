<?php
// a JS library:  https://github.com/cmweiss/geomagJS
// 
// http://www.ngdc.noaa.gov/geomag-web/calculators/declinationHelp
// http://www.ngdc.noaa.gov/geomag-web/calculators/calculateDeclination?lat1=40&lon1=-105.25&resultFormat=xml

// http://www.ngdc.noaa.gov/geomagmodels/struts/calcDeclination
// http://www.ngdc.noaa.gov/geomagmodels/struts/calcDeclination
$lat=45.53704;
$lon=-122.599793;
$url='http://www.ngdc.noaa.gov/geomag-web/calculators/calculateDeclination?lat1='.$lat.'&lon1='.$lon.'&resultFormat=csv';
//http://www.ngdc.noaa.gov/geomag-web/calculators/calculateDeclination?lat1=45.53704&lon1=122.599793&resultFormat=csv
//$x = readfile ("http://www.ngdc.noaa.gov/geomag-web/calculators/calculateDeclination?lat1='.$lat.'&lon1='.$lon.'&resultFormat=xml");
echo "URL is ".$url.'<br><br>';
//$x = readfile($url);
$x = file_get_contents($url);
$y = str_getcsv($x);
echo '<br><br><pre>';
print_r($y[3]);
echo '</pre>';

?>

<?php
    //header('Content-type: application/vnd.google-earth.kml+xml');
    header("Content-disposition: attachment; filename=".$filename.".kml");
    header("Content-Type: application/vnd.google-earth.kml+xml kml; charset=utf8");
    header("Content-Transfer-Encoding: binary");
    header("Pragma: no-cache");
    header("Expires: 0");
    /*echo '<?xml version="1.0" encoding="UTF-8"?>';*/
    echo $data;
?>
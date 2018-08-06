<?php
    header("Content-Type: application/txt; charset=utf8");
    header("Content-disposition: attachment; filename=".$filename.".cfg");
//    header("Content-Transfer-Encoding: binary");
//    header("Pragma: no-cache");
//    header("Expires: 0");
    /*echo '<?xml version="1.0" encoding="UTF-8"?>';*/
    echo $data;
?>
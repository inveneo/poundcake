<?php
require_once(APP . 'Vendor' . DS . 'dompdf' . DS . 'dompdf_config.inc.php');
spl_autoload_register('DOMPDF_autoload');
$dompdf = new DOMPDF();
$dompdf->load_html(utf8_decode($content_for_layout), Configure::read('App.encoding'));
$dompdf->render();
echo $dompdf->output();

/*
//App::import('Vendor', 'dompdf/dompdf.php');
//App::import('Vendor', 'dompdf/dompdf');
require_once(APP . 'Vendor' . DS . 'dompdf' . DS . 'dompdf.php');
//require_once(APP . 'Vendor' . DS . 'dompdf' . DS . 'dompdf_config.inc.php');

$dompdf = new DOMPDF();
$dompdf->load_html(utf8_decode($content_for_layout), Configure::read('App.encoding'));
$dompdf->render();
echo $dompdf->output();
*/
?>

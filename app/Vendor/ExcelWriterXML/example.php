<?php
/**
 * Example page for how to use the Excel Writer XML
 * @package ExcelWriterXML
 * @subpackage examples
 * @filesource
 */

/**
 * Include the required Class file
 */
include('ExcelWriterXML.php');

/**
 * @source
 */
$xml = new ExcelWriterXML;
$xml->docAuthor('Robert F Greer');

$format = $xml->addStyle('StyleHeader');
$format->fontBold();
$format->alignRotate(45);
$sheet = $xml->addSheet('My Sheet');

$sheet->writeString(1,1,'Header','StyleHeader');
$sheet->writeString(2,1,'Test String','StyleHeader');

$xml->sendHeaders();
$xml->writeData();
?>
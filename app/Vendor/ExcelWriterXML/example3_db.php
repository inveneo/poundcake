<?php
/**
 * Example using built in MYSQL DB functions
 * @package ExcelWriterXML
 * @subpackage examples
 * @filesource
 */

include('ExcelWriterXML.php');
$xml = new ExcelWriterXML('my file.xml');
$xml->showErrorSheet(true);
/*****************  ADD multiple tables all at once ***********/
$tables = array(
	'test',
	'desks',
);
$xml->mysqlTableDump('surveys.web.alcatel-lucent.com','ghdweb','ghdweb','ghdweb_survey',$tables);

/*****************  ADD one table at a time ***********/
$xml->mysqlTableDump('surveys.web.alcatel-lucent.com','ghdweb','ghdweb','ghdweb_survey','test','test2');
$xml->mysqlTableDump('surveys.web.alcatel-lucent.com','ghdweb','ghdweb','ghdweb_survey','desks','desk2');

/*****************  ADD a sheet, execute a query against that sheet ***********/
$qSheet = $xml->addSheet('My Query');
$query = '
	SELECT
		datetime as "Date and Time"
		,`int` as "Integer"
	FROM
		test
';
$qSheet->mysqlQueryToTable('surveys.web.alcatel-lucent.com','ghdweb','ghdweb',$query);
$xml->sendHeaders();
$xml->writeData();
?>
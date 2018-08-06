<?php
/**
 * Detailed example for creating Excel XML docs
 * @package ExcelWriterXML
 * @subpackage examples
 * @filesource
 */

/**
 * Include the class for creating Excel XML docs
 */
include('ExcelWriterXML.php');

/**
 * Create a new instance of the Excel Writer
 */
$xml = new ExcelWriterXML('my file.xml');

/**
 * Add some general properties to the document
 */
$xml->docTitle('My Demo Doc');
$xml->docAuthor('Robert F Greer');
$xml->docCompany('Greers.Org');
$xml->docManager('Wife');

/**
 * Choose to show any formatting/input errors on a seperate sheet
 */
$xml->showErrorSheet(true);

/**
 * Show the style options
 */
$format1 = $xml->addStyle('left_rotate60_big');
$format1->alignRotate(60);
$format1->alignHorizontal('Left');
$format1->fontSize('18');

$format3 = $xml->addStyle('verticaltext_left');
$format3->alignVerticaltext(45);
$format3->alignHorizontal('Left');

$format4 = $xml->addStyle('wraptext_top');
$format4->alignWraptext();
$format4->alignVertical('Top');

/**
 * Create a new sheet with the XML document
 */
$sheet1 = $xml->addSheet('Alignment');
/**
 * Add three new cells of type String with difference alignment values.
 * Notice that the style of the each cell can be explicity named or the style
 * reference can be passed.
 */
$sheet1->writeString(1,1,'left_rotate45',$format1);
$sheet1->writeString(1,2,'vertical left','verticaltext_left');
$sheet1->writeString(1,3,'this text has been wrapped and is aligned at the top','wraptext_top');
$sheet1->writeString(1,4,'No style applied');


$sheet2 = $xml->addSheet('Formulas');
/**
 * Wrote three numbers.
 * Rows 4 and 5 show the formulas in R1C1 notation using the writeFormula()
 * function.
 * Also see how comments are added.
 */
$sheet2->columnWidth(1,'100');
$sheet2->writeString(1,1,'Number');
$sheet2->writeNumber(1,2,50);
$sheet2->writeString(2,1,'Number');
$sheet2->writeNumber(2,2,30);
$sheet2->writeString(3,1,'Number');
$sheet2->writeNumber(3,2,20);
$sheet2->writeString(4,1,'=SUM(R[-3]C:R[-1]C)');
$sheet2->writeFormula('Number',4,2,'=SUM(R[-3]C:R[-1]C)');
$sheet2->addComment(4,2,'Here is my formula: =SUM(R[-3]C:R[-1]C)','My NAME');
$sheet2->writeString(5,1,'=SUM(R1C2:R3C2)');
$sheet2->writeFormula('Number',5,2,'=SUM(R1C1:R3C2)');
$sheet2->addComment(5,2,'Here is my formula: =SUM(R1C1:R3C2)');

$sheet4 = $xml->addSheet('more formatting');
$format4 = $xml->addStyle('my style');
$format4->fontBold();
$format4->fontItalic();
$format4->fontUnderline('DoubleAccounting');
$format4->bgColor('Black');
$format4->fontColor('White');
$format4->numberFormatDateTime();
$mydate = $sheet4->convertMysqlDateTime('2008-02-14 19:30:00');
$sheet4->writeDateTime(1,1,$mydate,$format4);
// Change the row1 height to 30 pixels
$sheet4->rowHeight(1,'30');
$sheet4->writeString(2,1,'formatted text + cell color + merged + underlined',$format4);
// Merge (2,1) with 4 columns to the right and 2 rows down
$sheet4->cellMerge(2,1,4,2);

/**
 * Send the headers, then output the data
 */
$xml->sendHeaders();
$xml->writeData();


?>
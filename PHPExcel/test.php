<?php
/** Error reporting */
error_reporting(E_ALL);

require_once('../functions/functions.php');

/** Include path **/
ini_set('include_path', ini_get('include_path').'Classes/');

/** PHPExcel */
include 'Classes/PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
include 'Classes/PHPExcel/Writer/Excel2007.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("KWD Software Solutions");
$objPHPExcel->getProperties()->setLastModifiedBy("Karsten Kaminski");
$objPHPExcel->getProperties()->setTitle("Eurovets Order");
$objPHPExcel->getProperties()->setSubject("Eurovets Order");
$objPHPExcel->getProperties()->setDescription("Eurovets Order");


// Add some data

$objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Product');
$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'SKU');
$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Qty');

// Initiate counter
$i = 1;

$query_list = "
  SELECT
	  tbl_orders.Id,
	  tbl_orders.Date,
	  tbl_order_items.Id AS `Delete`,
	  tbl_order_items.ItemId,
	  tbl_order_items.Item,
	  tbl_order_items.SKU,
	  tbl_order_items.Company,
	  tbl_order_items.Price,
	  tbl_order_items.Qty,
	  tbl_order_items.Total
  FROM
	  tbl_orders
  INNER JOIN tbl_order_items ON tbl_orders.Id = tbl_order_items.OrderId
  WHERE tbl_orders.Id = '$orderid'";	  
$query_list = mysqli_query($con, $query_list)or die(mysqli_error($con));
$rows = mysqli_num_rows($query_list);
while($row_list = mysqli_fetch_array($query_list)){

	$i++;
	$date = $row['new_date'];
	
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getStyle('A1'.$i.':C'. $i)->getFill()->getStartColor()->setARGB('000000');
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $row_list['Item']);
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i , $row_list['SKU']);
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i , $row_list['Qty']);
	

}

// Rename sheet
echo date('H:i:s') . " Rename sheet\n";
$objPHPExcel->getActiveSheet()->setTitle('Eurovets');

		
// Save Excel 2007 file
echo date('H:i:s') . " Write to Excel2007 format\n";
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('123.xlsx');

// Echo done
echo date('H:i:s') . " Done writing file.\r\n";

?>
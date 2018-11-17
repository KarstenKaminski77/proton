<?php
session_start();

/** Error reporting */
error_reporting(E_ALL);

// Order Status

  // 0 = Awaiting ON
  // 1 = Selected to send to engineer
  // 2 = Sent to engineer

$con = mysqli_connect('dedi133.jnb2.host-h.net','seavest001','nFT4nwk8','seavest');
  
require_once('../functions/functions.php');

select_db();

// Unset Previous Seesion
unset($_SESSION['success']);
unset($_SESSION['mail_to']);
unset($_SESSION['jobids']);

// Initialise array
$mail_to = array();
$jobids = array();

if(isset($_POST['upload']) && !empty($_FILES['xls']['name'])){
	
	$target_path = basename($_FILES['xls']['name']);
	
	if(move_uploaded_file($_FILES['xls']['tmp_name'], $target_path)){
		
		$filename = $target_path;
		$handle = fopen("$filename", "r");
		
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){
			
			$jobid = addslashes($data[0]);
			$inv_date = addslashes($data[3]);
			$orderno = addslashes($data[6]);
			$today = date('Y-m-j');
			
			$date = strtotime($inv_date) - 60 * 60 * 24 * 14;
			$invoice_date = date('d M Y', $date);
			$new_invoice_date = date('Y-m-d', $date);
			
			// Update Total2
			$query_invno = "
			  SELECT
				  tbl_jc.SubTotal,
				  tbl_jc.Total2,
				  tbl_jc.VAT,
				  tbl_sites.VAT AS VAT_1
			  FROM
				  tbl_jc
			  INNER JOIN tbl_sites ON tbl_jc.SiteId = tbl_sites.Id
			  WHERE
				  JobId = '$jobid'";
			
			$query_invno = mysqli_query($con, $query_invno)or die(mysqli_error($con));
			while($row_invno = mysqli_fetch_array($query_invno)){
				
				$subtotal = $row_invno['SubTotal'];
				
				if($row_invno['VAT_1'] == 1){
					
					$vat = $row_invno['VAT'];
				}
				
				$total = $subtotal + $vat;
				
				mysqli_query($con, "UPDATE tbl_jc SET Total2 = '$total' WHERE JobId = '$jobid'")or die(mysqli_error($con));
			}
			
			mysqli_query($con, "UPDATE tbl_jc SET RefNo = '$orderno', Days = '$today', InvoiceDate = '$invoice_date', 
			NewInvoiceDate = '$new_invoice_date' Status = '12', OrderNoStatus = '2' 
			WHERE JobId = '$jobid'") or die(mysqli_error());
		
			$query_sent = mysqli_query($con, "SELECT tbl_sites.Name AS Name_1, tbl_jc.InvoiceNo, tbl_companies.Name, tbl_jc.JobId 
			FROM ((tbl_jc LEFT JOIN tbl_companies ON tbl_companies.Id=tbl_jc.CompanyId) LEFT JOIN tbl_sites ON tbl_sites.Id=tbl_jc.SiteId) 
			WHERE tbl_jc.JobId = '$jobid'")or die(mysqli_error($con));
			$row_sent = mysqli_fetch_array($query_sent);
			
			$invoice = $row_sent['InvoiceNo'];
			$company = $row_sent['Name'];
			$site = $row_sent['Name_1'];
			$pdf = 'Seavest Invoice '.$invoice.'.pdf';
			$sent = date('d M Y H:i:s');
			
			mysqli_query($con, "INSERT INTO tbl_sent_invoices (InvoiceNo,CompanyId,SiteId,JobId,PDF,DateSent) 
			VALUES ('$invoice','$company','$site','$jobid','$pdf','$sent')")or die(mysqli_error($con));

		}
		
		fclose($handle);
		
		$_SESSION['success'] = '<div id="banner-success-mail"><span class="success-header">'. $_FILES['xls']['name'] .' Successfully Uploaded.</span></div>';
	}
	
	header('Location: ../outbox-new.php');
	
	exit();
	
}

for($i=0;$i<count($_POST['jobid']);$i++){
	
	$jobid = $_POST['jobid'][$i];
	
	mysqli_query($con, "UPDATE tbl_jc SET OrderNoStatus = '1' WHERE JobId = '$jobid'")or die(mysqli_error($con));
}

/** Include path **/
ini_set('include_path', ini_get('include_path').'Classes/');

/** PHPExcel */
include 'Classes/PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
include 'Classes/PHPExcel/Writer/Excel2007.php';

$query_engineer = "
SELECT
tbl_jc.InvoiceNo,
STR_TO_DATE(
		tbl_jc.InvoiceDate,
		'%d %M %Y'
	) AS InvDate,
tbl_jc.JobNo,
tbl_jc.Total2,
tbl_sites.`Name`,
tbl_engineers.`Name` AS EngineerName,
tbl_engineers.Email,
tbl_jc.JobId,
tbl_engineers.Id
FROM
tbl_jc
INNER JOIN tbl_sites ON tbl_jc.SiteId = tbl_sites.Id
INNER JOIN tbl_engineers ON tbl_sites.EngineerId = tbl_engineers.Id
WHERE
	tbl_jc.OrderNoStatus = '1'
GROUP BY
	tbl_engineers.Id
ORDER BY 
    tbl_engineers.Email ASC";
	
$query_engineer = mysqli_query($con, $query_engineer)or die(mysqli_error($con));
while($row_engineer = mysqli_fetch_array($query_engineer)){
	
	$engineerid = $row_engineer['Id'];
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	// Set properties
	$objPHPExcel->getProperties()->setCreator("KWD");
	$objPHPExcel->getProperties()->setLastModifiedBy("KWD");
	$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
	
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	
	$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);	
	
	// Add some data
	
	$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFill()->getStartColor()->setARGB('FFFF00');
	$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
	$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
	$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
	$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
	$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
	
	$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'JobId');
	$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Job No.');
	$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Invoice No.');
	$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Invoice Date');
	$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Site');
	$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Total');
	$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Order No.');
	//$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Engineer');
	
	
	
	// Initiate counter
	$i = 1;
	
	$query_on = "
	SELECT
	tbl_jc.InvoiceNo,
	STR_TO_DATE(
			tbl_jc.InvoiceDate,
			'%d %M %Y'
		) AS InvDate,
	tbl_jc.JobNo,
	tbl_jc.Total2,
	tbl_sites.`Name`,
	tbl_engineers.`Name` AS EngineerName,
	tbl_engineers.Email,
	tbl_jc.JobId
	FROM
	tbl_jc
	INNER JOIN tbl_sites ON tbl_jc.SiteId = tbl_sites.Id
	INNER JOIN tbl_engineers ON tbl_sites.EngineerId = tbl_engineers.Id
	WHERE
		tbl_jc.OrderNoStatus = '1' AND tbl_engineers.Id = '$engineerid' AND tbl_jc.Total2 > '0'
	GROUP BY
		tbl_jc.JobId
	ORDER BY 
		InvDate ASC";
		
	$query_on = mysqli_query($con, $query_on)or die(mysqli_error($con));
	while($row_on = mysqli_fetch_array($query_on)){
		
	$i++;
	$date = $row['new_date'];
	
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $row_on['JobId']);
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i , $row_on['JobNo']);
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i , $row_on['InvoiceNo']);
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i , $row_on['InvDate']);
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i , $row_on['Name']);
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i , $row_on['Total2']);
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i , '');
	//$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i , $row_on['EngineerName']);
	
	// Update as sent
	if(isset($_POST['test'])){
		
		$jobid = $row_on['JobId'];
		
		mysqli_query($con, "UPDATE tbl_jc SET OrderNoStatus = '0' WHERE JobId = '$jobid'")or die(mysq1li_error($con));
	}
	
	array_push($jobids, $row_on['JobId']);
	
	}
	
	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Simple');
	
			
	// Save Excel 2007 file
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$objWriter->save('Seavest-ON-'. $row_engineer['EngineerName'] .'-'.date('Y-m-d').'.xlsx');
		
	
	// Email
	$message = 'Dear ' .$row_engineer['EngineerName'] .'<br><br>
	'. $_SESSION['content'] .'
	<br><br>
	<img src="http://www.seavest.co.za/inv/images/signature-new.jpg" />';
	
	if(isset($_POST['engineer'])){
		
	    $to = $row_engineer['Email'];
		//$to = 'test@kwd.co.za';
		
	} else {
		
		$to = $_POST['email'];
	}
	$from = "Seavest Africa <info@seavest.co.za>"; 
	$subject ="Seavest Africa Order No."; 
	$message = "<body style=\"font-family:Arial; font-size:12px; margin: 20px; line-height:18px; color:#333333\">". $message ."</body>";
	$headers = "From: $from";
		
	// boundary 
	$semi_rand = md5(time()); 
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
		
	// headers for attachment 
	$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
		
	// multipart boundary 
	$message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-type:text/html; charset=utf8\r\n" . 
	"Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
	$message .= "--{$mime_boundary}\n";
		
	// preparing attachments
	$file = fopen('Seavest-ON-'. $row_engineer['EngineerName'] .'-'.date('Y-m-d').'.xlsx',"rb");
	$data = fread($file,filesize('Seavest-ON-'. $row_engineer['EngineerName'] .'-'.date('Y-m-d').'.xlsx'));
	fclose($file);
	$data = chunk_split(base64_encode($data));
	$pdf = 'Seavest-ON-'. $row_engineer['EngineerName'] .'-'.date('Y-m-d').'.xlsx';
	$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$pdf\"\n" . 
			    "Content-Disposition: attachment;\n" . " filename=\"$pdf\"\n" . 
			    "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
	$message .= "--{$mime_boundary}\n";
	
	if($ok = @mail($to, $subject, $message, $headers)){
					
		array_push($mail_to, $to);
		
	}
}

$_SESSION['mail_to'] = $mail_to;
$_SESSION['jobids'] = $jobids;

header('Location: ../outbox-new.php');
?>
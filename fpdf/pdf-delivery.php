<?php
//include fpdf class
require_once("tfpdf.php");

// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

$query_proton = mysqli_query($con, "SELECT * FROM tbl_proton")or die(mysqli_error($con));
$row_proton = mysqli_fetch_array($query_proton);

/**
 * myfpdf extends fpdf class, it is used to draw the header and footer
 */
require_once ("mypdf-table.php");

//Tag Based Multicell Class
require_once ("classes/tfpdftable.php");

$pono = $_GET['Id'];

$query_po = mysqli_query($con, "SELECT * FROM tbl_po WHERE RFQNo = '$pono'")or die(mysqli_error($con));
$row_po = mysqli_fetch_array($query_po);
	
$quoteno = $row_po['QuoteNo'];
$supplierid = $row_po['CompanyId'];
	
$query_products = "
SELECT
	tbl_products.`Name`,
	tbl_products.PackSize,
	tbl_po_items.Qty,
	tbl_po_items.Unit,
	tbl_po_items.POId
FROM
	tbl_po_items
INNER JOIN tbl_products ON tbl_po_items.ProductId = tbl_products.Id
WHERE
	tbl_po_items.POId = '$pono'";
	
$query_products = mysqli_query($con, $query_products)or die(mysqli_error($con));

$query_pickup = "
SELECT
	tbl_po.Id,
	tbl_po.QuoteNo,
	tbl_po.DeliveryDate,
	tbl_po_items.Qty,
	tbl_po_items.Unit,
	tbl_products.`Name`,
	tbl_products.PackSize,
	tbl_companies.CompanyName,
	tbl_companies.DeliveryAddress,
	tbl_companies.Telephone,
	tbl_companies.Mobile,
	tbl_companies.BuyerName,
	tbl_qs.PO
FROM
	tbl_po
INNER JOIN tbl_po_items ON tbl_po.Id = tbl_po_items.POId
INNER JOIN tbl_products ON tbl_po_items.ProductId = tbl_products.Id
INNER JOIN tbl_qs ON tbl_po.QuoteNo = tbl_qs.Id
INNER JOIN tbl_companies ON tbl_qs.CompanyId = tbl_companies.Id
WHERE
	tbl_po.Id = '$pono'";

$query_pickup = mysqli_query($con, $query_pickup)or die(mysqli_error($con));
$row_pickup = mysqli_fetch_array($query_pickup);
	
$query_supplier = mysqli_query($con, "SELECT * FROM tbl_companies WHERE Id = '$supplierid'")or die(mysqli_error($con));
$row_supplier = mysqli_fetch_array($query_supplier);

$query_transport = mysqli_query($con, "SELECT * FROM tbl_transport WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
$row_transport = mysqli_fetch_array($query_transport);

$transport = $row_transport['Price'];

sourcing_pdf_subtotal($con, $pono,$supplierid);
sourcing_pdf_vat($con, $pono,$supplierid);
sourcing_pdf_total($con, $pono,$supplierid);

//create the fpdf object and do some initialization
$oPdf = new myPdf();
$oPdf->Open();
$oPdf->SetAutoPageBreak(true, 20);
$oPdf->SetMargins(10, 20, 20);

$oPdf->AddFont('dejavusans',   '',     'DejaVuSans.ttf',       true);
$oPdf->AddFont('dejavusans',   'B',    'DejaVuSans-Bold.ttf',  true);
$oPdf->AddFont('dejavusans',   'BI',   'DejaVuSans-BoldOblique.ttf', true);
$oPdf->AddFont('dejavuserif',  '',     'DejaVuSerif.ttf',      true);
$oPdf->AddFont('dejavuserif',  'B',    'DejaVuSerif-Bold.ttf', true);
$oPdf->AddFont('dejavuserif',  'BI',   'DejaVuSerif-BoldItalic.ttf', true);

$oPdf->AddPage();
$oPdf->AliasNbPages();
	
$oTable = new TfpdfTable($oPdf);

	$aCustomConfiguration = array(
        'TABLE' => array(
                'TABLE_ALIGN'       => 'L',                 //left align
                'BORDER_COLOR'      => array(166,202,240),      //border color
                'BORDER_SIZE'       => '0.1',               //border size
				'BORDER_TYPE'       => 'LRTB',
        ),
    
        'HEADER' => array(
                'TEXT_COLOR'        => array(0,102,170),   //text color
                'TEXT_SIZE'         => 9,                   //font size
                'LINE_SIZE'         => 6,                   //line size for one row
                'BACKGROUND_COLOR'  => array(255,255,255),  //background color
                'BORDER_SIZE'       => '0.1',                 //border size
                'BORDER_TYPE'       => 'LRTB',                 //border type, can be: 0, 1 or a combination of: "LRTB"
                'BORDER_COLOR'      => array(166,202,240),      //border color
        ),

        'ROW' => array(
                'TEXT_COLOR'        => array(0,0,0),        //text color
                'TEXT_SIZE'         => 8,                   //font size
                'BACKGROUND_COLOR'  => array(255,255,255),  //background color
                'BORDER_COLOR'      => array(166,202,240),     //border color
				'PADDING_TOP'       => 1,
				'PADDING_BOTTOM'       => 1,
				'PADDING_LEFT'       => 1,
				'PADDING_RIGHT'       => 1,
				'BORDER_SIZE'       => '0.1',
        ),
);
	
$oPdf->SetDrawColor(166,202,240);
$oPdf->SetTextColor(1,131,186);
$oPdf->Image('../images/logo.jpg',10,10);
$oPdf->SetFont('Arial','B',16);
$oPdf->Cell(190,10,'DELIVERY NOTE','','','R');
$oPdf->Ln(10);

$oPdf->SetDrawColor(166,202,240);
$oPdf->SetTextColor(0,0,0);

$oPdf->SetFont('Arial','',9);
$oPdf->Multicell(190,'5',$row_proton['Address'],'','R');
$oPdf->Ln(1);

$oPdf->Cell(95,5,'Tel: '. $row_proton['Telephone'],'','','L');
$oPdf->Cell(95,5,'Reg No: 2009/029148/23','','','R');
$oPdf->Ln(5);

$oPdf->Cell(95,5,'Fax: '. $row_proton['Fax'],'','','L');
$oPdf->Cell(95,5,'VAT No: '. $row_proton['VAT'],'','','R');
$oPdf->Ln(5);

$oPdf->Cell(95,5,'Email: ' .$row_proton['Email'],'','','L');

$oPdf->Ln(20);

$nColumns = 4;

//Initialize the table class, 3 columns
$oTable->initialize(array(30,65,30,65),$aCustomConfiguration);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['TEXT'] = 'No.'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['TEXT'] = $pono; 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = '';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'L';

$aRow[2]['BORDER_TYPE'] = 'LRTB';
$aRow[2]['TEXT'] = 'Date'; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'L';

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = $row_pickup['DeliveryDate']; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

//close the table
$oTable->close();
$oPdf->Ln(15);

$oTable->initialize(array(30,65,30,65),$aCustomConfiguration);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['TEXT'] = 'Delivery Address';
$aRow[0]['VERTICAL_ALIGN'] = "T";
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['TEXT'] = $row_pickup['CompanyName'].'
'.$row_pickup['DeliveryAddress']; 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = '';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'L';

$aRow[2]['BORDER_TYPE'] = 'LRTB';
$aRow[2]['VERTICAL_ALIGN'] = "T";
$aRow[2]['TEXT'] = 'Contact Number'; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'L';

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['VERTICAL_ALIGN'] = "T";
$aRow[3]['TEXT'] = $row_pickup['Telephone'].' 
'.$row_pickup['Mobile']; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['TEXT'] = 'Attention'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['TEXT'] = $row_pickup['BuyerName']; 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = '';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'L';

$aRow[2]['BORDER_TYPE'] = 'LRTB';
$aRow[2]['TEXT'] = 'Order Number'; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'L';

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = $row_pickup['PO']; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

//close the table
$oTable->close();
$oPdf->Ln(15);

$nColumns = 4;

//Initialize the table class, 3 columns
$oTable->initialize(array(90,33,33,33),$aCustomConfiguration);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['TEXT'] = 'Product'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['TEXT'] = 'Qty'; 
$aRow[1]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = 'B';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'C';

$aRow[2]['BORDER_TYPE'] = 'LRTB';
$aRow[2]['TEXT'] = 'Packaging'; 
$aRow[2]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'C';

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = 'Number Of Packs'; 
$aRow[3]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = 'B';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'R';

$oTable->addRow($aRow);

//close the table
$oTable->close();

$oTable->initialize(array(90,33,33,33),$aCustomConfiguration);

while($row_products = mysqli_fetch_array($query_products)){
	
	$ms=$row_products['PackSize'];
	$string = ereg_replace("[^0-9]", "",$ms);
	
	$packs = $row_products['Qty'] / $string;
			
	$aRow = array();
	
	$aRow[0]['BORDER_TYPE'] = 'LRTB';
	$aRow[0]['TEXT'] = $row_products['Name'].$option; 
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['BORDER_COLOR'] = array(102,102,102);
	
	$aRow[1]['BORDER_TYPE'] = 'LRTB';
	$aRow[1]['TEXT'] = $row_products['Qty'].$row_products['Unit']; 
	$aRow[1]['BORDER_COLOR'] = array(102,102,102);
	$aRow[1]['TEXT_ALIGN'] = 'C';
	
	$aRow[2]['BORDER_TYPE'] = 'LRTB';
	$aRow[2]['TEXT'] = $row_products['PackSize']; 
	$aRow[2]['TEXT_ALIGN'] = "C";
	$aRow[2]['BORDER_COLOR'] = array(102,102,102);

	$aRow[3]['BORDER_TYPE'] = 'LRTB';
	$aRow[3]['TEXT'] = $packs; 
	$aRow[3]['BORDER_COLOR'] = array(102,102,102);
	$aRow[3]['TEXT_ALIGN'] = 'R';

	$oTable->addRow($aRow);
		
}
//close the table
$oTable->close();

$oPdf->Ln(15);

$oTable->initialize(array(190),$aCustomConfiguration);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = '';
$aRow[0]['TEXT'] = 'Received in Good Order and Condition'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = '';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$oTable->addRow($aRow);

$oTable->close();

$oPdf->Ln(15);

//Initialize the table class, 3 columns
$oTable->initialize(array(15,65,30,15,65),$aCustomConfiguration);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = '';
$aRow[0]['VERTICAL_ALIGN'] = 'B'; 
$aRow[0]['TEXT'] = 'Name'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'B';
$aRow[1]['TEXT'] = '&nbsp;'; 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = 'B';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'C';

$aRow[2]['BORDER_TYPE'] = '';
$aRow[2]['TEXT'] = ''; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = '';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'C';

$aRow[3]['BORDER_TYPE'] = '';
$aRow[3]['TEXT'] = 'Date'; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = 'B';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'C';

$aRow[4]['BORDER_TYPE'] = 'B';
$aRow[4]['TEXT'] = '&nbsp;'; 
$aRow[4]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[4]['TEXT_COLOR'] = array(0,0,0);
$aRow[4]['TEXT_TYPE'] = 'B';
$aRow[4]['BORDER_COLOR'] = array(102,102,102);
$aRow[4]['TEXT_ALIGN'] = 'R';

$oTable->addRow($aRow);
//close the table
$oTable->close();

$oPdf->Ln(10);

$oTable->initialize(array(15,65,30,15,65),$aCustomConfiguration);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = '';
$aRow[0]['VERTICAL_ALIGN'] = 'B'; 
$aRow[0]['TEXT'] = 'Sign'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'B';
$aRow[1]['TEXT'] = '&nbsp;'; 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = 'B';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'C';

$aRow[2]['BORDER_TYPE'] = '';
$aRow[2]['TEXT'] = ''; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = '';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'C';

$aRow[3]['BORDER_TYPE'] = '';
$aRow[3]['TEXT'] = ''; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'C';

$aRow[4]['BORDER_TYPE'] = '';
$aRow[4]['TEXT'] = '&nbsp;'; 
$aRow[4]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[4]['TEXT_COLOR'] = array(0,0,0);
$aRow[4]['TEXT_TYPE'] = 'B';
$aRow[4]['BORDER_COLOR'] = array(102,102,102);
$aRow[4]['TEXT_ALIGN'] = 'R';

$oTable->addRow($aRow);

//close the table
$oTable->close();


//send the pdf to the browser

if(isset($_GET['Preview'])){
	
	$oPdf->Output();
		
} else {
	
	$pdf = 'Proton Chem Delivery Note #'.$pono.'.pdf';
	$date = date('Y-m-d');
		
	mysqli_query($con, "UPDATE tbl_notes SET DeliveryNote = '$pdf', Date = '$date', Status = '1' WHERE POId = '$pono'")or die(mysqli_error($con));
	mysqli_query($con, "UPDATE tbl_po SET Status = '2' WHERE Id = '$pono'")or die(mysqli_error($con));
		
	//$oPdf->Output();
	$oPdf->Output('pdf/'.$pdf);
				
}

header('Location:pdf/mail-notes.php?Id='. $pono);
	


?>

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

$query_po = mysqli_query($con, "SELECT * FROM tbl_po WHERE Id = '$pono'")or die(mysqli_error($con));
$row_po = mysqli_fetch_array($query_po);
	
$quoteno = $row_po['QuoteNo'];
$supplierid = $row_po['CompanyId'];
$rfqno = $row_po['RFQNo'];
	
$query_products = "
SELECT
	tbl_rfq_items.SourceId,
	tbl_rfq_items.SupplierId,
	tbl_rfq_items.ProductId,
	tbl_rfq_items.Qty,
	tbl_rfq_items.Unit,
	tbl_rfq_items.Price,
	tbl_rfq_items.Total,
	tbl_rfq_items.Currency,
	tbl_products.`Name`
FROM
	tbl_rfq_items
INNER JOIN tbl_products ON tbl_rfq_items.ProductId = tbl_products.Id
WHERE
	tbl_rfq_items.SourceId = '$rfqno' AND tbl_rfq_items.SupplierId = '$supplierid' AND Approved = '1'";
	
$query_products = mysqli_query($con, $query_products)or die(mysqli_error($con));

$query_quote_to = "
SELECT
	tbl_po.Date,
	tbl_po.DeliveryDate,
	tbl_po.DeliveryTerms,
	tbl_po.PaymentTerms,
	tbl_po.RFQNo,
	tbl_companies.CompanyName,
	tbl_companies.Telephone,
	tbl_companies.Mobile,
	tbl_companies.SalesName,
	tbl_companies.SalesEmail,
	tbl_companies.DeliveryAddress,
	tbl_po.CompanyId
FROM
	tbl_companies
INNER JOIN tbl_po ON tbl_po.CompanyId = tbl_companies.Id
WHERE
	tbl_po.RFQNo = '$rfqno'
AND tbl_po.CompanyId = '$supplierid'";

$query_quote_to = mysqli_query($con, $query_quote_to)or die(mysqli_error($con));
$row_quote_to = mysqli_fetch_array($query_quote_to);
	
$query_supplier = mysqli_query($con, "SELECT * FROM tbl_companies WHERE Id = '$supplierid'")or die(mysqli_error($con));
$row_supplier = mysqli_fetch_array($query_supplier);

$query_transport = mysqli_query($con, "SELECT * FROM tbl_transport WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
$row_transport = mysqli_fetch_array($query_transport);

$transport = $row_transport['Price'];

sourcing_pdf_subtotal($con, $rfqno,$supplierid);
sourcing_pdf_vat($con, $rfqno,$supplierid);
sourcing_pdf_total($con, $rfqno,$supplierid);

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
$oPdf->Cell(190,10,'PURCHASE ORDER','','','R');
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
$aRow[0]['TEXT'] = 'PO No.'; 
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
$aRow[3]['TEXT'] = $row_quote_to['Date']; 
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
$aRow[1]['TEXT'] = $row_quote_to['SalesName']; 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = '';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'L';

$aRow[2]['BORDER_TYPE'] = 'LRTB';
$aRow[2]['TEXT'] = 'Telephone'; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'L';

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = $row_quote_to['Telephone']; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['TEXT'] = 'Company'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['TEXT'] = $row_quote_to['CompanyName']; 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = '';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'L';

$aRow[2]['BORDER_TYPE'] = 'LRTB';
$aRow[2]['TEXT'] = 'Email'; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'L';

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = $row_quote_to['SalesEmail']; 
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
$oTable->initialize(array(30,65,30,65),$aCustomConfiguration);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['TEXT'] = 'Delivery Date'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['TEXT'] = $row_quote_to['DeliveryDate']; 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = '';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'L';

$aRow[2]['BORDER_TYPE'] = 'LRTB';
$aRow[2]['TEXT'] = 'Payment Terms'; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'L';

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = $row_quote_to['PaymentTerms']; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['VERTICAL_ALIGN'] = 'T';
$aRow[0]['TEXT'] = 'Delivery Terms'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['VERTICAL_ALIGN'] = 'T';
$aRow[1]['TEXT'] = $row_quote_to['DeliveryTerms']; 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = '';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'L';

$aRow[2]['BORDER_TYPE'] = 'LRTB';
$aRow[2]['VERTICAL_ALIGN'] = 'T';
$aRow[2]['TEXT'] = 'Delivery Address'; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'L';

$aRow[3]['BORDER_TYPE'] = 'RTB';
$aRow[3]['TEXT'] = $row_quote_to['DeliveryAddress']; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

//close the table
$oTable->close();
$oPdf->Ln(15);

$nColumns = 5;

//Initialize the table class, 3 columns
$oTable->initialize(array(90,25,25,25,25),$aCustomConfiguration);

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
$aRow[2]['TEXT'] = 'Unit'; 
$aRow[2]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'C';

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = 'Price'; 
$aRow[3]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = 'B';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'R';

$aRow[4]['BORDER_TYPE'] = 'LRTB';
$aRow[4]['TEXT'] = 'Total'; 
$aRow[4]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[4]['TEXT_COLOR'] = array(0,0,0);
$aRow[4]['TEXT_TYPE'] = 'B';
$aRow[4]['BORDER_COLOR'] = array(102,102,102);
$aRow[4]['TEXT_ALIGN'] = 'R';

$oTable->addRow($aRow);

//close the table
$oTable->close();

$oTable->initialize(array(90,25,25,25,25),$aCustomConfiguration);

while($row_products = mysqli_fetch_array($query_products)){
			
	$aRow = array();
	
	$aRow[0]['BORDER_TYPE'] = 'LRTB';
	$aRow[0]['TEXT'] = $row_products['Name'].$option; 
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['BORDER_COLOR'] = array(102,102,102);
	
	$aRow[1]['BORDER_TYPE'] = 'LRTB';
	$aRow[1]['TEXT'] = $row_products['Qty']; 
	$aRow[1]['BORDER_COLOR'] = array(102,102,102);
	$aRow[1]['TEXT_ALIGN'] = 'C';
	
	$aRow[2]['BORDER_TYPE'] = 'LRTB';
	$aRow[2]['TEXT'] = $row_products['Unit']; 
	$aRow[2]['TEXT_ALIGN'] = "C";
	$aRow[2]['BORDER_COLOR'] = array(102,102,102);

	$aRow[3]['BORDER_TYPE'] = 'LRTB';
	$aRow[3]['TEXT'] = $row_products['Currency'] .' '. $row_products['Price']; 
	$aRow[3]['BORDER_COLOR'] = array(102,102,102);
	$aRow[3]['TEXT_ALIGN'] = 'R';

	$aRow[4]['BORDER_TYPE'] = 'LRTB';
	$aRow[4]['TEXT'] = $row_products['Currency'] .' '. $row_products['Total']; 
	$aRow[4]['BORDER_COLOR'] = array(102,102,102);
	$aRow[4]['TEXT_ALIGN'] = 'R';

	$oTable->addRow($aRow);
	
	$_SESSION['currency'] = $row_products['Currency'];
		
	}

// Totals
$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'T';
$aRow[0]['TEXT'] = ''; 
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'T';
$aRow[1]['TEXT'] = ''; 
$aRow[1]['BORDER_COLOR'] = array(102,102,102);

$aRow[2]['BORDER_TYPE'] = 'T';
$aRow[2]['TEXT'] = ''; 
$aRow[2]['BORDER_COLOR'] = array(102,102,102);

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = 'Sub Total'; 
$aRow[3]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = 'B';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'R';

$aRow[4]['BORDER_TYPE'] = 'LRTB';
$aRow[4]['TEXT'] = $_SESSION['currency'] .' '. number_format($_SESSION['subtotal'],2); 
$aRow[4]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[4]['TEXT_COLOR'] = array(0,0,0);
$aRow[4]['TEXT_TYPE'] = '';
$aRow[4]['BORDER_COLOR'] = array(102,102,102);
$aRow[4]['TEXT_ALIGN'] = 'R';

$oTable->addRow($aRow);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = '';
$aRow[0]['TEXT'] = ''; 
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = '';
$aRow[1]['TEXT'] = ''; 
$aRow[1]['BORDER_COLOR'] = array(102,102,102);

$aRow[2]['BORDER_TYPE'] = '';
$aRow[2]['TEXT'] = ''; 
$aRow[2]['BORDER_COLOR'] = array(102,102,102);

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = 'VAT'; 
$aRow[3]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = 'B';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'R';

$aRow[4]['BORDER_TYPE'] = 'LRTB';
$aRow[4]['TEXT'] = $_SESSION['currency'] .' '. number_format($_SESSION['vat'],2); 
$aRow[4]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[4]['TEXT_COLOR'] = array(0,0,0);
$aRow[4]['TEXT_TYPE'] = '';
$aRow[4]['BORDER_COLOR'] = array(102,102,102);
$aRow[4]['TEXT_ALIGN'] = 'R';

$oTable->addRow($aRow);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = '';
$aRow[0]['TEXT'] = ''; 
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = '';
$aRow[1]['TEXT'] = ''; 
$aRow[1]['BORDER_COLOR'] = array(102,102,102);

$aRow[2]['BORDER_TYPE'] = '';
$aRow[2]['TEXT'] = ''; 
$aRow[2]['BORDER_COLOR'] = array(102,102,102);

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = 'Total'; 
$aRow[3]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = 'B';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'R';

$aRow[4]['BORDER_TYPE'] = 'LRTB';
$aRow[4]['TEXT'] = $_SESSION['currency'] .' '. number_format($_SESSION['total'],2); 
$aRow[4]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[4]['TEXT_COLOR'] = array(0,0,0);
$aRow[4]['TEXT_TYPE'] = '';
$aRow[4]['BORDER_COLOR'] = array(102,102,102);
$aRow[4]['TEXT_ALIGN'] = 'R';

$oTable->addRow($aRow);

//close the table
$oTable->close();

$oPdf->Ln(20);
$oPdf->SetDrawColor(166,202,240);
$oPdf->SetTextColor(0,0,0);
$oPdf->SetFont('Arial','',9);
$oPdf->Multicell(190,'6','Subject to our Standard Trading Terms and Conditions.','','C');


//send the pdf to the browser
$oPdf->Output();
		

	


?>

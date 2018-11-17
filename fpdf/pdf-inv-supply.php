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

$invoiceno = $_GET['Id'];
	
$query_products = "
SELECT
	tbl_products.`Name`,
	tbl_inv_items.Id,
	tbl_inv_items.Qty,
	tbl_inv_items.Unit,
	tbl_inv_items.Price,
	tbl_inv_items.Retail,
	tbl_inv_items.Total
FROM
	tbl_inv_items
INNER JOIN tbl_products ON tbl_inv_items.ProductId = tbl_products.Id
WHERE
	tbl_inv_items.InvoiceNo = '$invoiceno'";
	
$query_products = mysqli_query($con, $query_products)or die(mysqli_error($con));

$query_invoice_to = "
SELECT
	tbl_companies.Id AS CompanyId,
	tbl_companies.CompanyName,
	tbl_companies.Telephone,
	tbl_companies.AccountsName,
	tbl_companies.AccountsEmail,
	tbl_companies.PostalAddress,
	tbl_companies.VAT,
	tbl_inv.Date,
	tbl_inv.Currency,
	tbl_inv.DueDate,
	tbl_inv.PaymentTerms,
	tbl_qs.PO
FROM
	tbl_inv
INNER JOIN tbl_companies ON tbl_inv.CompanyId = tbl_companies.Id
INNER JOIN tbl_qs ON tbl_inv.QuoteNo = tbl_qs.Id
WHERE
	tbl_inv.Id = '$invoiceno'";
	
$query_invoice_to = mysqli_query($con, $query_invoice_to)or die(mysqli_error($con));
$row_invoice_to = mysqli_fetch_array($query_invoice_to);
	
$query_supplier = mysqli_query($con, "SELECT * FROM tbl_companies WHERE Id = '$supplierid'")or die(mysqli_error($con));
$row_supplier = mysqli_fetch_array($query_supplier);

$query_transport = mysqli_query($con, "SELECT * FROM tbl_transport WHERE InvoiceNo = '$invoiceno'")or die(mysqli_error($con));
$row_transport = mysqli_fetch_array($query_transport);

$transport = $row_transport['Price'];

inv_pdf_subtotal($con, $invoiceno);
inv_pdf_vat($con, $invoiceno, $transport);
inv_pdf_total($con, $invoiceno, $transport);

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
$oPdf->Cell(190,10,'TAX INVOICE','','','R');
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
$aRow[0]['TEXT'] = 'Invoice No'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['TEXT'] = $_GET['Id']; 
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
$aRow[3]['TEXT'] = $row_invoice_to['PaymentTerms']; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['TEXT'] = 'Invoice Date'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['TEXT'] = date('Y-m-d'); 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = '';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'L';

$aRow[2]['BORDER_TYPE'] = 'LRTB';
$aRow[2]['TEXT'] = 'Due Date'; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'L';

if($row_invoice_to['DueDate'] == '0000-00-00'){
	
	$duedate = 'NA';
	
} else {
	
	$duedate = $row_invoice_to['DueDate'];
}
$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = $duedate; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

//close the table
$oTable->close();
$oPdf->Ln(10);

$nColumns = 4;

//Initialize the table class, 3 columns
$oTable->initialize(array(30,65,30,65),$aCustomConfiguration);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['TEXT'] = 'Attention'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['TEXT'] = $row_invoice_to['AccountsName']; 
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
$aRow[3]['TEXT'] = $row_invoice_to['Telephone']; 
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
$aRow[1]['TEXT'] = $row_invoice_to['CompanyName']; 
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
$aRow[3]['TEXT'] = $row_invoice_to['AccountsEmail']; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['ROWSPAN'] = 2;
$aRow[0]['VERTICAL_ALIGN'] = 'T';
$aRow[0]['TEXT'] = 'Address'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['ROWSPAN'] = 2;
$aRow[1]['TEXT'] = $row_invoice_to['PostalAddress']; 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = '';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'L';

$aRow[2]['BORDER_TYPE'] = 'LRTB';
$aRow[2]['TEXT'] = 'VAT No'; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'L';

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = $row_invoice_to['VAT']; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

$aRow = array();

$aRow[2]['BORDER_TYPE'] = 'LRTB';
$aRow[2]['TEXT'] = 'Order No'; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'L';

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = $row_invoice_to['PO']; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

//close the table
$oTable->close();

$oPdf->Ln(15);

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
	$aRow[3]['TEXT'] = $row_invoice_to['Currency'] .' '. $row_products['Retail']; 
	$aRow[3]['BORDER_COLOR'] = array(102,102,102);
	$aRow[3]['TEXT_ALIGN'] = 'R';

	$aRow[4]['BORDER_TYPE'] = 'LRTB';
	$aRow[4]['TEXT'] = $row_invoice_to['Currency'] .' '. $row_products['Total']; 
	$aRow[4]['BORDER_COLOR'] = array(102,102,102);
	$aRow[4]['TEXT_ALIGN'] = 'R';

	$oTable->addRow($aRow);
		
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
$aRow[4]['TEXT'] = $row_invoice_to['Currency'] .' '. $_SESSION['subtotal']; 
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
$aRow[4]['TEXT'] = $row_invoice_to['Currency'] .' '. $_SESSION['vat']; 
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
$aRow[4]['TEXT'] = $row_invoice_to['Currency'] .' '. $_SESSION['total']; 
$aRow[4]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[4]['TEXT_COLOR'] = array(0,0,0);
$aRow[4]['TEXT_TYPE'] = '';
$aRow[4]['BORDER_COLOR'] = array(102,102,102);
$aRow[4]['TEXT_ALIGN'] = 'R';

$oTable->addRow($aRow);

//close the table
$oTable->close();

$oPdf->Ln(15);

$oTable->initialize(array(190),$aCustomConfiguration);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['TEXT'] = 'Account Deatails'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(227,227,227);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$oTable->addRow($aRow);

//close the table
$oTable->close();

$nColumns = 4;

//Initialize the table class, 3 columns
$oTable->initialize(array(30,65,30,65),$aCustomConfiguration);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['TEXT'] = 'Bank'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['TEXT'] = 'Standard Bank'; 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = '';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'L';

$aRow[2]['BORDER_TYPE'] = 'LRTB';
$aRow[2]['TEXT'] = 'Account Name'; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'L';

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = 'Proton Chem cc'; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['TEXT'] = 'Account No'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['TEXT'] = '051363909'; 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = '';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'L';

$aRow[2]['BORDER_TYPE'] = 'LRTB';
$aRow[2]['TEXT'] = 'Branch'; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'L';

$aRow[3]['BORDER_TYPE'] = 'LRTB';
$aRow[3]['TEXT'] = 'Durban North'; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

$aRow = array();

$aRow[0]['BORDER_TYPE'] = 'LRTB';
$aRow[0]['TEXT'] = 'Branch Code'; 
$aRow[0]['TEXT_ALIGN'] = "L";
$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[0]['TEXT_COLOR'] = array(0,0,0);
$aRow[0]['TEXT_TYPE'] = 'B';
$aRow[0]['BORDER_COLOR'] = array(102,102,102);

$aRow[1]['BORDER_TYPE'] = 'LRTB';
$aRow[1]['TEXT'] = '042-826'; 
$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[1]['TEXT_COLOR'] = array(0,0,0);
$aRow[1]['TEXT_TYPE'] = '';
$aRow[1]['BORDER_COLOR'] = array(102,102,102);
$aRow[1]['TEXT_ALIGN'] = 'L';

$aRow[2]['BORDER_TYPE'] = 'LTB';
$aRow[2]['TEXT'] = '&nbsp;'; 
$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[2]['TEXT_COLOR'] = array(0,0,0);
$aRow[2]['TEXT_TYPE'] = 'B';
$aRow[2]['BORDER_COLOR'] = array(102,102,102);
$aRow[2]['TEXT_ALIGN'] = 'L';

$aRow[3]['BORDER_TYPE'] = 'RTB';
$aRow[3]['TEXT'] = '&nbsp;'; 
$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
$aRow[3]['TEXT_COLOR'] = array(0,0,0);
$aRow[3]['TEXT_TYPE'] = '';
$aRow[3]['BORDER_COLOR'] = array(102,102,102);
$aRow[3]['TEXT_ALIGN'] = 'L';

$oTable->addRow($aRow);

//close the table
$oTable->close();

$oPdf->Ln(20);
$oPdf->SetDrawColor(166,202,240);
$oPdf->SetTextColor(0,0,0);
$oPdf->SetFont('Arial','',9);
$oPdf->Multicell(190,'6','Subject to our Standard Trading terms and conditions','','C');

//send the pdf to the browser

if(isset($_GET['Preview'])){
	
	$oPdf->Output();
		
} else {
	
	$pdf = 'Proton Chem Invoice #'.$invoiceno.'.pdf';
	$quote = $_GET['Quote'];
		
	mysqli_query($con, "UPDATE tbl_inv SET PDF = '$pdf' WHERE Id = '$invoiceno'")or die(mysqli_error($con));
	mysqli_query($con, "UPDATE tbl_notes SET Status = '2' WHERE QuoteNo = '$quote'")or die(mysqli_error($con));
		
	//$oPdf->Output();
	$oPdf->Output('pdf/'.$pdf);
	
	header('Location:pdf/mail-inv-supply.php?Id='. $invoiceno .'&Company='. $row_invoice_to['Id']);
				
}
	


?>

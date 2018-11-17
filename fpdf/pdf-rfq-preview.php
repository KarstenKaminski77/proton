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

//define some background colors
$aBgColor1 = array(0, 100, 67);
$aBgColor2 = array(165, 250, 220);
$aBgColor3 = array(255, 252, 249);
$aBgColor4 = array(86, 155, 225);
$aBgColor5 = array(207, 247, 239);
$aBgColor6 = array(246, 211, 207);
$bg_color7 = array(216, 243, 228);
$bg_color8 = array(255, 255, 255);

$supplier = array();

	
	$supplierid = $_GET['Supplier'];
	$sourceid = $_GET['Id'];
	
	array_push($supplier, $supplierid);

$query_products = "
SELECT
	tbl_rfq.Date,
	tbl_rfq.Id,
	tbl_rfq_items.Qty,
	tbl_rfq_items.ProductId,
	tbl_rfq_items.SupplierId,
	tbl_rfq_items.RFQ,
	tbl_rfq_items.SourceId,
	tbl_products.`Name`,
	tbl_products.Grade,
	tbl_products.`Code`,
	tbl_products.`PackSize`,
	tbl_companies.CompanyName,
	tbl_companies.Mobile,
	tbl_companies.SalesName,
	tbl_companies.SalesEmail,
	tbl_companies.Telephone
FROM
	tbl_rfq
INNER JOIN tbl_rfq_items ON tbl_rfq.Id = tbl_rfq_items.SourceId
INNER JOIN tbl_products ON tbl_rfq_items.ProductId = tbl_products.Id
INNER JOIN tbl_companies ON tbl_rfq_items.SupplierId = tbl_companies.Id
WHERE
	tbl_rfq.Id = '$sourceid' AND
	tbl_rfq_items.SupplierId = '$supplierid'";
	
$query_products = mysqli_query($con, $query_products)or die(mysqli_error($con));

$query_supplier = mysqli_query($con, "SELECT * FROM tbl_companies WHERE Id = '$supplierid'")or die(mysqli_error($con));
$row_supplier = mysqli_fetch_array($query_supplier);

pdf_content($con,1);

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
	$oPdf->Cell(190,10,'REQUEST FOR QUOTATION','','','R');
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
	
	$oPdf->MultiCell(190,7, 'Dear ' .$row_supplier['SalesName'].'

'. $_SESSION['content'],'','L');
	$oPdf->Ln(15);
	
	$nColumns = 4;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(115,25,25,25),$aCustomConfiguration);
	
	$aRow = array();

	$aRow[0]['BORDER_TYPE'] = 'LRTB';
	$aRow[0]['TEXT'] = 'Product'; 
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['BACKGROUND_COLOR'] = array(227,227,227);
	$aRow[0]['TEXT_COLOR'] = array(102,102,102);
	$aRow[0]['TEXT_TYPE'] = 'B';
	$aRow[0]['BORDER_COLOR'] = array(102,102,102);
	
	$aRow[1]['BORDER_TYPE'] = 'LRTB';
	$aRow[1]['TEXT'] = 'Qty'; 
	$aRow[1]['BACKGROUND_COLOR'] = array(227,227,227);
	$aRow[1]['TEXT_COLOR'] = array(102,102,102);
	$aRow[1]['TEXT_TYPE'] = 'B';
	$aRow[1]['BORDER_COLOR'] = array(102,102,102);
	$aRow[1]['TEXT_ALIGN'] = 'C';
	
	$aRow[2]['BORDER_TYPE'] = 'LRTB';
	$aRow[2]['TEXT'] = 'Pack Size'; 
	$aRow[2]['BACKGROUND_COLOR'] = array(227,227,227);
	$aRow[2]['TEXT_COLOR'] = array(102,102,102);
	$aRow[2]['TEXT_TYPE'] = 'B';
	$aRow[2]['BORDER_COLOR'] = array(102,102,102);
	$aRow[2]['TEXT_ALIGN'] = 'L';
	
	$aRow[3]['BORDER_TYPE'] = 'LRTB';
	$aRow[3]['TEXT'] = 'Grade'; 
	$aRow[3]['BACKGROUND_COLOR'] = array(227,227,227);
	$aRow[3]['TEXT_COLOR'] = array(102,102,102);
	$aRow[3]['TEXT_TYPE'] = 'B';
	$aRow[3]['BORDER_COLOR'] = array(102,102,102);
	$aRow[3]['TEXT_ALIGN'] = 'L';

	$oTable->addRow($aRow);	
	
	//close the table
	$oTable->close();
	
	$oTable->initialize(array(115,25,25,25),$aCustomConfiguration);
	
	while($row_products = mysqli_fetch_array($query_products)){
			
	$aRow = array();
	
	$aRow[0]['BORDER_TYPE'] = 'LRTB';
	$aRow[0]['TEXT'] = $row_products['Name'].$option; 
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['BORDER_COLOR'] = array(102,102,102);
	
	$aRow[1]['BORDER_TYPE'] = 'LRTB';
	$aRow[1]['TEXT'] = $row_products['Qty'].'Kg'; 
	$aRow[1]['BORDER_COLOR'] = array(102,102,102);
	$aRow[1]['TEXT_ALIGN'] = 'C';
	
	$aRow[2]['BORDER_TYPE'] = 'LRTB';
	$aRow[2]['TEXT'] = $row_products['PackSize']; 
	$aRow[2]['TEXT_ALIGN'] = "L";
	$aRow[2]['BORDER_COLOR'] = array(102,102,102);

	$aRow[3]['BORDER_TYPE'] = 'LRTB';
	$aRow[3]['TEXT'] = $row_products['Grade']; 
	$aRow[3]['BORDER_COLOR'] = array(102,102,102);
	$aRow[3]['TEXT_ALIGN'] = 'L';

	$oTable->addRow($aRow);
		
	}

	//close the table
	$oTable->close();
	
	//send the pdf to the browser
	$oPdf->Output();
		
?>

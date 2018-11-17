<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

session_start();

//include fpdf class
require_once("tfpdf.php");

require_once('../functions/functions.php');

$con = mysqli_connect('sql6.jnb1.host-h.net','chevron_db','kwd001','chevron_db');

$id = $_GET['Id'];

$query = mysqli_query($con, "SELECT tbl_sites.Name AS Name_1, tbl_qs.Id, tbl_qs.RefNo, tbl_qs.Date, tbl_qs.CategoryId, tbl_qs.Description, tbl_qs.Feedback, tbl_users.Name, tbl_sites.Address, tbl_sites.Suburb, tbl_sites.Telephone, tbl_sites.Fax, tbl_sites.Email, tbl_qs.CompanyId FROM ((tbl_qs LEFT JOIN tbl_sites ON tbl_sites.Id=tbl_qs.SiteId) LEFT JOIN tbl_users ON tbl_users.Id=tbl_qs.CompanyId) WHERE tbl_qs.Id = '$id'")or die(mysqli_error($con));
$row = mysqli_fetch_assoc($query);

$date = explode(' ', $row['Date']);
$quoteno = $row['Id'];

total_labour_qs($quoteno);
total_material_qs($quoteno);
total_transport_qs($quoteno);

total_qs_pdf($quoteno);
vat_qs_pdf($quoteno);
sub_total_qs_pdf($quoteno);

$query_Recordset3 = "SELECT * FROM tbl_labour_qs WHERE QuoteNo = '$id' ORDER BY Id ASC";
$Recordset3 = mysqli_query($con, $query_Recordset3) or die(mysqli_error($con));
$row_Recordset3 = mysqli_fetch_assoc($Recordset3);
$totalRows_Recordset3 = mysqli_num_rows($Recordset3);

$query_Recordset4 = "SELECT * FROM tbl_material_qs WHERE QuoteNo = '$id' ORDER BY Id ASC";
$Recordset4 = mysqli_query($con, $query_Recordset4) or die(mysqli_error($con));
$row_Recordset4 = mysqli_fetch_assoc($Recordset4);
$totalRows_Recordset4 = mysqli_num_rows($Recordset4);

$query_Recordset7 = "SELECT * FROM tbl_travel_qs WHERE QuoteNo = '$id' ORDER BY Id ASC";
$Recordset7 = mysqli_query($con, $query_Recordset7) or die(mysqli_error($con));
$row_Recordset7 = mysqli_fetch_assoc($Recordset7);
$totalRows_Recordset7 = mysqli_num_rows($Recordset7);

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
	$oPdf->SetTextColor(237,28,36);
	$oPdf->Image('../images/sealink-logo.jpg',10,10);
	$oPdf->SetFont('Arial','B',20);
	$oPdf->Cell(190,10,'QUOTATION NO: '. $row['Id'] .'','','','R');
	$oPdf->Ln(25);
	
	$nColumns = 2;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(25,167),$aCustomConfiguration);
	
	$aRow = array();

	$aRow[0]['BORDER_TYPE'] = 'T';
	$aRow[1]['BORDER_TYPE'] = 'T';
	$aRow[0]['BORDER_TYPE'] = '1';
	$aRow[0]['BORDER_COLOR'] = array(166,202,240);
	$aRow[1]['TEXT_SIZE'] = '8';
	$aRow[1]['BORDER_TYPE'] = '1';
	$aRow[1]['BORDER_COLOR'] = array(166,202,240);
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 

	$aRow[0]['TEXT'] = 'Contractor'; 
	$aRow[1]['TEXT'] =  $row['Name'];
	
	$oTable->addRow($aRow);	

	$aRow[0]['TEXT'] = 'Site'; 
	$aRow[1]['TEXT'] =  $row['Name_1'];
	
	$oTable->addRow($aRow);	

	$aRow[0]['TEXT'] = 'Reference No'; 
	$aRow[1]['TEXT'] =  $row['RefNo'];
	
	$oTable->addRow($aRow);	

	$aRow[0]['TEXT'] = 'Date'; 
	$aRow[1]['TEXT'] =  $date[0];
	
	$oTable->addRow($aRow);	
	
	//close the table
	$oTable->close();
	
	$oPdf->Ln(15);
	
	$nColumns = 1;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(170,22),$aCustomConfiguration);
	
	$aHeader = array();
	
	//Table Header
		
	$aHeader[0]['TEXT_ALIGN'] = "L";
	$aHeader[0]['COLSPAN'] = '2';
	$aHeader[0]['TEXT'] = 'Overview';
	$aRow[0]['BORDER_TYPE'] = 'T';
	$aRow[0]['BORDER_SIZE'] = '0.1';
		
	//add the header
	$oTable->addHeader($aHeader);
	
	$aRow = array();
	
	$aRow[0]['COLSPAN'] = '2';
	$aRow[0]['TEXT_ALIGN'] = "L";
	//$aRow[0]['BACKGROUND_COLOR'] = array(250,250,250);
	$aRow[0]['TEXT'] = $row['Description'];
	
	$oTable->addRow($aRow);
	
	$quoteno = $_GET['Id'];
	
	$query_images = mysqli_query($con, "SELECT * FROM tbl_qs_images WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
	$numrow_images = mysqli_num_rows($query_images);

		
	if($numrow_images >= 1){
		
				
		if($numrow_images >= 1){
			
			$photos = '<a href="http://www.sealink.co.za/view.php?Id='. $quoteno .'" target="_parent">'. $numrow_images . ' photos to view</a>';
		
		} else {
			
			$photos = '<a href="http://www.sealink.co.za/view.php?Id='. $quoteno .'" target="_parent">'. $numrow_images . ' photo to view</a>';
		}
		
	$aRow = array();

	$aRow[0]['TEXT'] = '';
	$aRow[1]['TEXT'] = $photos;
	$aRow[0]['BORDER_TYPE'] = 'T';
	$aRow[1]['BORDER_TYPE'] = 'LRTB';
	$aRow[0]['BORDER_SIZE'] = '0.1';
	$aRow[1]['BACKGROUND_COLOR'] = array(166,202,240);
	
	$oTable->addRow($aRow);
		
	}
	
	//close the table
	$oTable->close();
	
	$oPdf->Ln(15);
	
	$nColumns = 5;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(110,20,20,20,22),$aCustomConfiguration);
	
	$aHeader = array();
	
	//Table Header
	
	$names = array('Labour','Unit','Qty','Unit Price','Total');
	for ($i = 0; $i < $nColumns; $i ++) {
		
		$aHeader[0]['TEXT_ALIGN'] = "L";
		$aHeader[1]['TEXT_ALIGN'] = "L";
		$aHeader[2]['TEXT_ALIGN'] = "C";
		$aHeader[3]['TEXT_ALIGN'] = "R";
		$aHeader[4]['TEXT_ALIGN'] = "R";
		
		$headings = $names[$i];
		$aHeader[$i]['TEXT'] = $headings;
	}
		
	//add the header
	$oTable->addHeader($aHeader);
	
	$aRow = array();
	$x = 0;
	
	do {
		
		$x++;
		
		//if($x % 2 == 0){
			
			//$aRow[0]['BACKGROUND_COLOR'] = array(233,233,233);
			//$aRow[1]['BACKGROUND_COLOR'] = array(233,233,233);
			//$aRow[2]['BACKGROUND_COLOR'] = array(233,233,233);
			//$aRow[3]['BACKGROUND_COLOR'] = array(233,233,233);
			//$aRow[4]['BACKGROUND_COLOR'] = array(233,233,233);
			
		//} else {
			
			//$aRow[0]['BACKGROUND_COLOR'] = array(250,250,250);
			//$aRow[1]['BACKGROUND_COLOR'] = array(250,250,250);
			//$aRow[2]['BACKGROUND_COLOR'] = array(250,250,250);
			//$aRow[3]['BACKGROUND_COLOR'] = array(250,250,250);
			//$aRow[4]['BACKGROUND_COLOR'] = array(250,250,250);

		//}

		$aRow[0]['TEXT_ALIGN'] = "L";
		$aRow[0]['TEXT'] = $row_Recordset3['Description'];

		$aRow[1]['TEXT_ALIGN'] = "L";
		$aRow[1]['TEXT'] = 'Hours';

		$aRow[2]['TEXT_ALIGN'] = "C";
		$aRow[2]['TEXT'] = $row_Recordset3['Qty'];

		$aRow[3]['TEXT_ALIGN'] = "R";
		$aRow[3]['TEXT'] = 'R'. $row_Recordset3['Price'];

		$aRow[4]['TEXT_ALIGN'] = "R";
		$aRow[4]['TEXT'] = 'R'. $row_Recordset3['Total'];
		
		$oTable->addRow($aRow);
		
	} while($row_Recordset3 = mysqli_fetch_assoc($Recordset3));
	
	$aRow = array();
	
	$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[4]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[0]['BORDER_TYPE'] = 'T';
	$aRow[0]['COLSPAN'] = '4';
	$aRow[4]['TEXT_ALIGN'] = "R"; 
	$aRow[4]['TEXT_COLOR'] = array(0,0,0);
	$aRow[4]['TEXT_TYPE'] = 'B';
	$aRow[4]['TEXT'] = $_SESSION['labour']; 
	
	$oTable->addRow($aRow);
	
	//close the table
	$oTable->close();
	
	$oPdf->Ln(15);
	
	$nColumns = 5;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(110,20,20,20,22),$aCustomConfiguration);
	
	$aHeader = array();
	
	//Table Header
		
	$names = array('Material','Unit','Qty','Unit Price','Total');
	for ($i = 0; $i < $nColumns; $i ++) {
		
		$aHeader[0]['TEXT_ALIGN'] = "L";
		$aHeader[1]['TEXT_ALIGN'] = "L";
		$aHeader[2]['TEXT_ALIGN'] = "C";
		$aHeader[3]['TEXT_ALIGN'] = "R";
		$aHeader[4]['TEXT_ALIGN'] = "R";
		
		$headings = $names[$i];
		$aHeader[$i]['TEXT'] = $headings;
	}
		
	//add the header
	$oTable->addHeader($aHeader);
	
	$aRow = array();
	
	do {
		
		$x++;
		
		//if($x % 2 == 0){
			
			//$aRow[0]['BACKGROUND_COLOR'] = array(233,233,233);
			//$aRow[1]['BACKGROUND_COLOR'] = array(233,233,233);
			//$aRow[2]['BACKGROUND_COLOR'] = array(233,233,233);
			//$aRow[3]['BACKGROUND_COLOR'] = array(233,233,233);
			//$aRow[4]['BACKGROUND_COLOR'] = array(233,233,233);
			
		//} else {
			
			//$aRow[0]['BACKGROUND_COLOR'] = array(250,250,250);
			//$aRow[1]['BACKGROUND_COLOR'] = array(250,250,250);
			//$aRow[2]['BACKGROUND_COLOR'] = array(250,250,250);
			//$aRow[3]['BACKGROUND_COLOR'] = array(250,250,250);
			//$aRow[4]['BACKGROUND_COLOR'] = array(250,250,250);

		//}

		$aRow[0]['TEXT_ALIGN'] = "L";
		$aRow[0]['TEXT'] = $row_Recordset4['Description'];

		$aRow[1]['TEXT_ALIGN'] = "L";
		$aRow[1]['TEXT'] = 'R';

		$aRow[2]['TEXT_ALIGN'] = "C";
		$aRow[2]['TEXT'] = $row_Recordset4['Qty'];

		$aRow[3]['TEXT_ALIGN'] = "R";
		$aRow[3]['TEXT'] = 'R'. $row_Recordset4['Price'];

		$aRow[4]['TEXT_ALIGN'] = "R";
		$aRow[4]['TEXT'] = 'R'. $row_Recordset4['Total'];
		
		$oTable->addRow($aRow);
		
	} while($row_Recordset4 = mysqli_fetch_assoc($Recordset4));
	
	$aRow = array();
	
	$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[4]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[0]['BORDER_TYPE'] = 'T';
	$aRow[0]['COLSPAN'] = '4';
	$aRow[4]['TEXT_ALIGN'] = "R"; 
	$aRow[4]['TEXT_COLOR'] = array(0,0,0);
	$aRow[4]['TEXT_TYPE'] = 'B';
	$aRow[4]['TEXT'] = $_SESSION['material']; 
	
	$oTable->addRow($aRow);
	
	//close the table
	$oTable->close();
	
	$oPdf->Ln(15);
	
	$nColumns = 5;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(110,20,20,20,22),$aCustomConfiguration);
	
	$aHeader = array();
	
	//Table Header
		
	$names = array('Material','Unit','Qty','Unit Price','Total');
	for ($i = 0; $i < $nColumns; $i ++) {
		
		$aHeader[0]['TEXT_ALIGN'] = "L";
		$aHeader[1]['TEXT_ALIGN'] = "L";
		$aHeader[2]['TEXT_ALIGN'] = "C";
		$aHeader[3]['TEXT_ALIGN'] = "R";
		$aHeader[4]['TEXT_ALIGN'] = "R";
		
		$headings = $names[$i];
		$aHeader[$i]['TEXT'] = $headings;
	}
		
	//add the header
	$oTable->addHeader($aHeader);
	
	$aRow = array();
	
	do {
		
		$x++;
		
		//if($x % 2 == 0){
			
			//$aRow[0]['BACKGROUND_COLOR'] = array(233,233,233);
			//$aRow[1]['BACKGROUND_COLOR'] = array(233,233,233);
			//$aRow[2]['BACKGROUND_COLOR'] = array(233,233,233);
			//$aRow[3]['BACKGROUND_COLOR'] = array(233,233,233);
			//$aRow[4]['BACKGROUND_COLOR'] = array(233,233,233);
			
		//} else {
			
			//$aRow[0]['BACKGROUND_COLOR'] = array(250,250,250);
			//$aRow[1]['BACKGROUND_COLOR'] = array(250,250,250);
			//$aRow[2]['BACKGROUND_COLOR'] = array(250,250,250);
			//$aRow[3]['BACKGROUND_COLOR'] = array(250,250,250);
			//$aRow[4]['BACKGROUND_COLOR'] = array(250,250,250);

		//}

		$aRow[0]['TEXT_ALIGN'] = "L";
		$aRow[0]['TEXT'] = $row_Recordset7['Description'];

		$aRow[1]['TEXT_ALIGN'] = "L";
		$aRow[1]['TEXT'] = 'Km';

		$aRow[2]['TEXT_ALIGN'] = "C";
		$aRow[2]['TEXT'] = $row_Recordset7['Qty'];

		$aRow[3]['TEXT_ALIGN'] = "R";
		$aRow[3]['TEXT'] = 'R'. $row_Recordset7['Price'];

		$aRow[4]['TEXT_ALIGN'] = "R";
		$aRow[4]['TEXT'] = 'R'. $row_Recordset7['Total'];
		
		$oTable->addRow($aRow);
		
	} while($row_Recordset7 = mysqli_fetch_assoc($Recordset7));
	
	$aRow = array();
	
	$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[4]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[0]['BORDER_TYPE'] = 'T';
	$aRow[0]['COLSPAN'] = '4';
	$aRow[4]['TEXT_ALIGN'] = "R"; 
	$aRow[4]['TEXT_COLOR'] = array(0,0,0);
	$aRow[4]['TEXT_TYPE'] = 'B';
	$aRow[4]['TEXT'] = $_SESSION['transport']; 
	
	$oTable->addRow($aRow);
	
	//close the table
	$oTable->close();
	
	$oPdf->Ln(15);
	
	$nColumns = 5;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(110,20,20,20,22),$aCustomConfiguration);
					
	$aRow = array();
	
	$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[4]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[3]['BORDER_TYPE'] = 'LRTB';
	$aRow[4]['BORDER_TYPE'] = 'LRTB';
	$aRow[0]['BORDER_TYPE'] = '0';
	$aRow[0]['COLSPAN'] = '3';
	$aRow[3]['TEXT_ALIGN'] = "R"; 
	$aRow[4]['TEXT_ALIGN'] = "R"; 
	$aRow[3]['TEXT_COLOR'] = array(0,0,0);
	$aRow[4]['TEXT_COLOR'] = array(0,0,0);
	$aRow[3]['TEXT_TYPE'] = 'B';
	$aRow[4]['TEXT_TYPE'] = 'B';
	
	$aRow[3]['TEXT'] = 'Sub Total'; 
	$aRow[4]['TEXT'] = $_SESSION['subtotal']; 
	
	$oTable->addRow($aRow);
	
	$aRow[3]['TEXT'] = 'VAT'; 
	$aRow[4]['TEXT'] = $_SESSION['vat']; 
	
	$oTable->addRow($aRow);
	
	$aRow[3]['TEXT'] = 'Total'; 
	$aRow[4]['TEXT'] = $_SESSION['total']; 
	
	$oTable->addRow($aRow);
	
	//close the table
	$oTable->close();
	
	//send the pdf to the browser
	
	if(isset($_GET['Preview'])){
		
		$oPdf->Output();
		
	} else {
		
		//$oPdf->Output('pdf/Sealink Quotation #'. $_GET['Id'] .'.pdf');
		$oPdf->Output();
	
	    //header('Location: ../qs-select.php?Status='. $_GET['Status']);
		
	}

?>

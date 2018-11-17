<?php
session_start();

//include fpdf class
require_once("tfpdf.php");

$con = mysqli_connect('sql6.jnb1.host-h.net','chevron_db','kwd001','chevron_db');

$id = $_GET['Id'];

$query = mysqli_query($con, "SELECT tbl_users.Name AS Name_2, tbl_sites.Name AS Name_1, tbl_remittance_details.Amount AS Amount_1, tbl_remittance_details.JobNo, tbl_remittance_details.InvoiceNo, tbl_remittance_details.InvoiceDate, tbl_remittance.Amount, tbl_remittance.Discount, tbl_remittance.Date, tbl_remittance.Id, tbl_remittance.UserId, tbl_remittance.DateSubmitted, tbl_remittance_details.JobId
FROM (((tbl_remittance
LEFT JOIN tbl_remittance_details ON tbl_remittance_details.RemittanceId=tbl_remittance.Id)
LEFT JOIN tbl_users ON tbl_users.Id=tbl_remittance.UserId)
LEFT JOIN tbl_sites ON tbl_sites.Id=tbl_remittance_details.SiteId)
WHERE tbl_remittance.Id = '$id'")or die(mysqli_error($con));
$row = mysqli_fetch_array($query);

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
	
	$oTable->setStyle("p","dejavusans","",9,"130,0,30");
	$oTable->setStyle("b","dejavusans","",9,"80,80,260");
	$oTable->setStyle("t1","dejavuserif","",10,"0,151,200");
	$oTable->setStyle("bi","dejavusans","BI",12,"0,0,120");
	
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
	$oPdf->SetTextColor(0, 132, 181);
	$oPdf->Image('logo.jpg',10,10,33);
	$oPdf->SetFont('Arial','B',16);
	$oPdf->Cell(190,10,'Remittance Advice: '. $row['Id'] .'','','','R');
	$oPdf->Ln(40);
		
	$nColumns = 2;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(40,40),$aCustomConfiguration);
	
	$aRow[0]['TEXT_COLOR'] = array(0,0,0);
	$aRow[0]['TEXT_SIZE'] = '8';
	$aRow[0]['TEXT_TYPE'] = 'B';
	$aRow[0]['BORDER_TYPE'] = 'TL';
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['TEXT'] = 'Client:';
	
	$aRow[1]['TEXT_COLOR'] = array(0,0,0);
	$aRow[1]['TEXT_SIZE'] = '8';
	$aRow[1]['TEXT_TYPE'] = '';
	$aRow[1]['BORDER_TYPE'] = 'TLR';
	$aRow[1]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT'] = 'Chevron South Africa (Pty) Limited';
	
	$oTable->addRow($aRow);
	
	$aRow[0]['TEXT_COLOR'] = array(0,0,0);
	$aRow[0]['TEXT_SIZE'] = '8';
	$aRow[0]['TEXT_TYPE'] = 'B';
	$aRow[0]['BORDER_TYPE'] = 'TL';
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['TEXT'] = 'Payment Received:';
	
	$aRow[1]['TEXT_COLOR'] = array(0,0,0);
	$aRow[1]['TEXT_SIZE'] = '8';
	$aRow[1]['TEXT_TYPE'] = '';
	$aRow[1]['BORDER_TYPE'] = 'TLR';
	$aRow[1]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT'] = $row['Date'];
	
	$oTable->addRow($aRow);
	
	$aRow[0]['TEXT_COLOR'] = array(0,0,0);
	$aRow[0]['TEXT_SIZE'] = '8';
	$aRow[0]['TEXT_TYPE'] = 'B';
	$aRow[0]['BORDER_TYPE'] = 'TL';
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['TEXT'] = 'Date Submitted:';
	
	$aRow[1]['TEXT_COLOR'] = array(0,0,0);
	$aRow[1]['TEXT_SIZE'] = '8';
	$aRow[1]['TEXT_TYPE'] = '';
	$aRow[1]['BORDER_TYPE'] = 'TLR';
	$aRow[1]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT'] = $row['DateSubmitted'];
	
	$oTable->addRow($aRow);
	
	$aRow[0]['TEXT_COLOR'] = array(0,0,0);
	$aRow[0]['TEXT_SIZE'] = '8';
	$aRow[0]['TEXT_TYPE'] = 'B';
	$aRow[0]['BORDER_TYPE'] = 'TL';
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['TEXT'] = 'Batch Amount:';
	
	$aRow[1]['TEXT_COLOR'] = array(0,0,0);
	$aRow[1]['TEXT_SIZE'] = '8';
	$aRow[1]['TEXT_TYPE'] = '';
	$aRow[1]['BORDER_TYPE'] = 'TLR';
	$aRow[1]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT'] = 'R'.$row['Amount'];
	
	$oTable->addRow($aRow);
	
	$aRow[0]['TEXT_COLOR'] = array(0,0,0);
	$aRow[0]['TEXT_SIZE'] = '8';
	$aRow[0]['TEXT_TYPE'] = 'B';
	$aRow[0]['BORDER_TYPE'] = 'TLB';
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['TEXT'] = 'Remitted By:';
	
	$aRow[1]['TEXT_COLOR'] = array(0,0,0);
	$aRow[1]['TEXT_SIZE'] = '8';
	$aRow[1]['TEXT_TYPE'] = '';
	$aRow[1]['BORDER_TYPE'] = 'TLRB';
	$aRow[1]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT'] = $row['Name_2'];
	
	$oTable->addRow($aRow);
		
	//close the table
	$oTable->close();
	
	$oPdf->Ln(15);
		
	$nColumns = 4;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(25,25,90,25,25),$aCustomConfiguration);
		
	$aRow = array();
		
	$aRow[0]['TEXT_COLOR'] = array(255,255,255);
	$aRow[0]['TEXT_SIZE'] = '8';
	$aRow[0]['TEXT_TYPE'] = 'B';
	$aRow[0]['BORDER_TYPE'] = '1';
	$aRow[0]['BORDER_SIZE'] = 0.1;
	$aRow[0]['BACKGROUND_COLOR'] = array(0,132,181);
	$aRow[0]['BORDER_COLOR'] = array(0,132,181);
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['TEXT'] = 'Inv No.';
		
	$aRow[1]['TEXT_COLOR'] = array(255,255,255);
	$aRow[1]['TEXT_SIZE'] = '8';
	$aRow[1]['TEXT_TYPE'] = 'B';
	$aRow[1]['BORDER_TYPE'] = '1';
	$aRow[1]['BORDER_SIZE'] = 0.1;
	$aRow[1]['BACKGROUND_COLOR'] = array(0,132,181);
	$aRow[1]['BORDER_COLOR'] = array(0,132,181);
	$aRow[1]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT'] = 'Job No';
		
	$aRow[2]['TEXT_COLOR'] = array(255,255,255);
	$aRow[2]['TEXT_SIZE'] = '8';
	$aRow[2]['TEXT_TYPE'] = 'B';
	$aRow[2]['BORDER_TYPE'] = '1';
	$aRow[2]['BORDER_SIZE'] = 0.1;
	$aRow[2]['BACKGROUND_COLOR'] = array(0,132,181);
	$aRow[2]['BORDER_COLOR'] = array(0,132,181);
	$aRow[2]['TEXT_ALIGN'] = "L";
	$aRow[2]['TEXT'] = 'Site';
		
	$aRow[3]['TEXT_COLOR'] = array(255,255,255);
	$aRow[3]['TEXT_SIZE'] = '8';
	$aRow[3]['TEXT_TYPE'] = 'B';
	$aRow[3]['BORDER_TYPE'] = '1';
	$aRow[3]['BORDER_SIZE'] = 0.1;
	$aRow[3]['BACKGROUND_COLOR'] = array(0,132,181);
	$aRow[3]['BORDER_COLOR'] = array(0,132,181);
	$aRow[3]['TEXT_ALIGN'] = "L";
	$aRow[3]['TEXT'] = 'Date';
		
	$aRow[4]['TEXT_COLOR'] = array(255,255,255);
	$aRow[4]['TEXT_SIZE'] = '8';
	$aRow[4]['TEXT_TYPE'] = 'B';
	$aRow[4]['BORDER_TYPE'] = '1';
	$aRow[4]['BORDER_SIZE'] = 0.1;
	$aRow[4]['BACKGROUND_COLOR'] = array(0,132,181);
	$aRow[4]['BORDER_COLOR'] = array(0,132,181);
	$aRow[4]['TEXT_ALIGN'] = "R";
	$aRow[4]['TEXT'] = 'Total';
	
	$oTable->addRow($aRow);
	
	$aRow = array();
	
	$id = $_GET['Id'];
	
	$query2 = mysqli_query($con, "SELECT tbl_sites.Name AS Name_1, tbl_remittance_details.Amount AS Amount_1, tbl_remittance.Id, tbl_remittance.Date, tbl_remittance.Amount, 
	tbl_remittance_details.InvoiceNo, tbl_remittance_details.JobNo, tbl_remittance_details.JobId
	FROM ((tbl_remittance
    LEFT JOIN tbl_remittance_details ON tbl_remittance_details.RemittanceId=tbl_remittance.Id)
	LEFT JOIN tbl_sites ON tbl_sites.Id=tbl_remittance_details.SiteId)
	WHERE tbl_remittance.Id = '$id'")or die(mysqli_error($con));
	while($row2 = mysqli_fetch_array($query2)){
	
	$aRow[0]['TEXT_COLOR'] = array(0,0,0);
	$aRow[0]['TEXT_SIZE'] = '8';
	$aRow[0]['TEXT_TYPE'] = '';
	$aRow[0]['BORDER_TYPE'] = '1';
	$aRow[0]['BORDER_SIZE'] = 0.1;
	$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[0]['BORDER_COLOR'] = array(0,132,181);
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['TEXT'] = $row2['InvoiceNo'];
		
	$aRow[1]['TEXT_COLOR'] = array(0,0,0);
	$aRow[1]['TEXT_SIZE'] = '8';
	$aRow[1]['TEXT_TYPE'] = '';
	$aRow[1]['BORDER_TYPE'] = '1';
	$aRow[1]['BORDER_SIZE'] = 0.1;
	$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[1]['BORDER_COLOR'] = array(0,132,181);
	$aRow[1]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT'] = $row2['JobNo'];
		
	$aRow[2]['TEXT_COLOR'] = array(0,0,0);
	$aRow[2]['TEXT_SIZE'] = '8';
	$aRow[2]['TEXT_TYPE'] = '';
	$aRow[2]['BORDER_TYPE'] = '1';
	$aRow[2]['BORDER_SIZE'] = 0.1;
	$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[2]['BORDER_COLOR'] = array(0,132,181);
	$aRow[2]['TEXT_ALIGN'] = "L";
	$aRow[2]['TEXT'] = $row2['Name_1'];
		
	$aRow[3]['TEXT_COLOR'] = array(0,0,0);
	$aRow[3]['TEXT_SIZE'] = '8';
	$aRow[3]['TEXT_TYPE'] = '';
	$aRow[3]['BORDER_TYPE'] = '1';
	$aRow[3]['BORDER_SIZE'] = 0.1;
	$aRow[3]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[3]['BORDER_COLOR'] = array(0,132,181);
	$aRow[3]['TEXT_ALIGN'] = "L";
	$aRow[3]['TEXT'] = $row2['Date'];
		
	$aRow[4]['TEXT_COLOR'] = array(0,0,0);
	$aRow[4]['TEXT_SIZE'] = '8';
	$aRow[4]['TEXT_TYPE'] = '';
	$aRow[4]['BORDER_TYPE'] = '1';
	$aRow[4]['BORDER_SIZE'] = 0.1;
	$aRow[4]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[4]['BORDER_COLOR'] = array(0,132,181);
	$aRow[4]['TEXT_ALIGN'] = "R";
	$aRow[4]['TEXT'] = 'R'.$row2['Amount_1'];
			
	$oTable->addRow($aRow);
	
	}
	
	$aRow = array();
		
	$aRow[0]['TEXT_COLOR'] = array(255,255,255);
	$aRow[0]['BORDER_TYPE'] = 'T';
	$aRow[0]['BORDER_SIZE'] = 0.1;
	$aRow[0]['BORDER_COLOR'] = array(0,132,181);
	$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[0]['TEXT'] = '';
		
	$aRow[1]['TEXT_COLOR'] = array(255,255,255);
	$aRow[1]['BORDER_TYPE'] = 'T';
	$aRow[1]['BORDER_SIZE'] = 0.1;
	$aRow[1]['BORDER_COLOR'] = array(0,132,181);
	$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[1]['TEXT'] = '';
		
	$aRow[2]['TEXT_COLOR'] = array(255,255,255);
	$aRow[2]['BORDER_TYPE'] = 'T';
	$aRow[2]['BORDER_SIZE'] = 0.1;
	$aRow[2]['BORDER_COLOR'] = array(0,132,181);
	$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[2]['TEXT'] = '';
		
	$aRow[3]['TEXT_COLOR'] = array(255,255,255);
	$aRow[3]['TEXT_SIZE'] = '8';
	$aRow[3]['TEXT_TYPE'] = 'B';
	$aRow[3]['BORDER_TYPE'] = 'RB';
	$aRow[3]['BORDER_SIZE'] = 0.1;
	$aRow[3]['BORDER_COLOR'] = array(46,185,237);
	$aRow[3]['BACKGROUND_COLOR'] = array(0,132,181);
	$aRow[3]['TEXT_ALIGN'] = "R";
	$aRow[3]['TEXT'] = 'Total';
		
	$aRow[4]['TEXT_COLOR'] = array(255,255,255);
	$aRow[4]['TEXT_SIZE'] = '8';
	$aRow[4]['TEXT_TYPE'] = 'B';
	$aRow[4]['BORDER_TYPE'] = 'BL';
	$aRow[4]['BORDER_SIZE'] = 0.1;
	$aRow[4]['BACKGROUND_COLOR'] = array(0,132,181);
	$aRow[4]['BORDER_COLOR'] = array(46,185,237);
	$aRow[4]['TEXT_ALIGN'] = "R";
	$aRow[4]['TEXT'] = 'R'.number_format($_SESSION['total'],2);
	
	$oTable->addRow($aRow);
	
	$aRow = array();
		
	$aRow[0]['TEXT_COLOR'] = array(255,255,255);
	$aRow[0]['BORDER_TYPE'] = '';
	$aRow[0]['BORDER_SIZE'] = 0.1;
	$aRow[0]['BORDER_COLOR'] = array(0,132,181);
	$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[0]['TEXT'] = '';
		
	$aRow[1]['TEXT_COLOR'] = array(255,255,255);
	$aRow[1]['BORDER_TYPE'] = '';
	$aRow[1]['BORDER_SIZE'] = 0.1;
	$aRow[1]['BORDER_COLOR'] = array(0,132,181);
	$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[1]['TEXT'] = '';
		
	$aRow[2]['TEXT_COLOR'] = array(255,255,255);
	$aRow[2]['BORDER_TYPE'] = '';
	$aRow[2]['BORDER_SIZE'] = 0.1;
	$aRow[2]['BORDER_COLOR'] = array(0,132,181);
	$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[2]['TEXT'] = '';
		
	$aRow[3]['TEXT_COLOR'] = array(255,255,255);
	$aRow[3]['TEXT_SIZE'] = '8';
	$aRow[3]['TEXT_TYPE'] = 'B';
	$aRow[3]['BORDER_TYPE'] = 'TBR';
	$aRow[3]['BORDER_SIZE'] = 0.1;
	$aRow[3]['BORDER_COLOR'] = array(46,185,237);
	$aRow[3]['BACKGROUND_COLOR'] = array(0,132,181);
	$aRow[3]['TEXT_ALIGN'] = "R";
	$aRow[3]['TEXT'] = 'Discount';
		
	$aRow[4]['TEXT_COLOR'] = array(255,255,255);
	$aRow[4]['TEXT_SIZE'] = '8';
	$aRow[4]['TEXT_TYPE'] = 'B';
	$aRow[4]['BORDER_TYPE'] = 'TLB';
	$aRow[4]['BORDER_SIZE'] = 0.1;
	$aRow[4]['BACKGROUND_COLOR'] = array(0,132,181);
	$aRow[4]['BORDER_COLOR'] = array(46,185,237);
	$aRow[4]['TEXT_ALIGN'] = "R";
	$aRow[4]['TEXT'] = 'R'.number_format($row['Discount'],2);
	
	$oTable->addRow($aRow);
	
	$aRow = array();
		
	$aRow[0]['TEXT_COLOR'] = array(255,255,255);
	$aRow[0]['BORDER_TYPE'] = '';
	$aRow[0]['BORDER_SIZE'] = 0.1;
	$aRow[0]['BORDER_COLOR'] = array(0,132,181);
	$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[0]['TEXT'] = '';
		
	$aRow[1]['TEXT_COLOR'] = array(255,255,255);
	$aRow[1]['BORDER_TYPE'] = '';
	$aRow[1]['BORDER_SIZE'] = 0.1;
	$aRow[1]['BORDER_COLOR'] = array(0,132,181);
	$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[1]['TEXT'] = '';
		
	$aRow[2]['TEXT_COLOR'] = array(255,255,255);
	$aRow[2]['BORDER_TYPE'] = '';
	$aRow[2]['BORDER_SIZE'] = 0.1;
	$aRow[2]['BORDER_COLOR'] = array(0,132,181);
	$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[2]['TEXT'] = '';
		
	$aRow[3]['TEXT_COLOR'] = array(255,255,255);
	$aRow[3]['TEXT_SIZE'] = '8';
	$aRow[3]['TEXT_TYPE'] = 'B';
	$aRow[3]['BORDER_TYPE'] = 'TR';
	$aRow[3]['BORDER_SIZE'] = 0.1;
	$aRow[3]['BORDER_COLOR'] = array(46,185,237);
	$aRow[3]['BACKGROUND_COLOR'] = array(0,132,181);
	$aRow[3]['TEXT_ALIGN'] = "R";
	$aRow[3]['TEXT'] = 'Total Paid';
		
	$aRow[4]['TEXT_COLOR'] = array(255,255,255);
	$aRow[4]['TEXT_SIZE'] = '8';
	$aRow[4]['TEXT_TYPE'] = 'B';
	$aRow[4]['BORDER_TYPE'] = 'LT';
	$aRow[4]['BORDER_SIZE'] = 0.1;
	$aRow[4]['BACKGROUND_COLOR'] = array(0,132,181);
	$aRow[4]['BORDER_COLOR'] = array(46,185,237);
	$aRow[4]['TEXT_ALIGN'] = "R";
	$aRow[4]['TEXT'] = 'R'.number_format(($_SESSION['total'] - $row['Discount']),2);
	
	$oTable->addRow($aRow);
	
	$aRow = array();
		
	//close the table
	$oTable->close();
	
	//send the pdf to the browser
			
		//$oPdf->Output();
				
		$oPdf->Output('pdf/Seavest Remittance '. $id .'.pdf');
	
	    header('Location: ../../accounts/index.php?Id='. $_GET['Id']);
	

?>

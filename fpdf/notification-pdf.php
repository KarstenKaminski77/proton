<?php require_once('../Connections/tender.php'); ?>
<?php require_once('../Connections/tender.php'); ?>
<?php 
session_start();

require_once('../Connections/tender.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}


mysql_select_db($database_tender, $tender);
$query_Recordset2 = "SELECT * FROM tbl_invitations_sent WHERE BidderId = BidderId";
$Recordset2 = mysql_query($query_Recordset2, $tender) or die(mysql_error());
$row_Recordset2 = mysql_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysql_num_rows($Recordset2);

//include fpdf class
require_once("tfpdf.php");

/**
 * myfpdf extends fpdf class, it is used to draw the header and footer
 */
require_once ("mypdf-table.php");

//Tag Based Multicell Class
require_once ("classes/tfpdftable.php");

//define some background colors
$aBgColor1 = array(234, 255, 218);
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
$oPdf->SetMargins(20, 20, 20);

$oPdf->AddFont('dejavusans',   '',     'DejaVuSans.ttf',       true);
$oPdf->AddFont('dejavusans',   'B',    'DejaVuSans-Bold.ttf',  true);
$oPdf->AddFont('dejavusans',   'BI',   'DejaVuSans-BoldOblique.ttf', true);
$oPdf->AddFont('dejavuserif',  '',     'DejaVuSerif.ttf',      true);
$oPdf->AddFont('dejavuserif',  'B',    'DejaVuSerif-Bold.ttf', true);
$oPdf->AddFont('dejavuserif',  'BI',   'DejaVuSerif-BoldItalic.ttf', true);

$count = count($_SESSION['winner_id']);
$winner_id = $_SESSION['winner_id'];

for($c=0;$c<$count;$c++){
	
	$id = $winner_id[$c];
	
	mysql_select_db($database_tender, $tender);
	$query_Recordset1 = "SELECT tbl_tenders.TenderName, tbl_tender_fields.Description, tbl_tender_fields.Qty, tbl_tender_fields.Price, tbl_tender_fields.OverallTotal, tbl_comments.Comments, tbl_winning_bids.WinnerId, tbl_tender_fields.Total FROM (((tbl_tenders LEFT JOIN tbl_winning_bids ON tbl_winning_bids.TenderId=tbl_tenders.Id) LEFT JOIN tbl_tender_fields ON tbl_tender_fields.BidderId=tbl_winning_bids.WinnerId) LEFT JOIN tbl_comments ON tbl_comments.BidderId=tbl_tender_fields.BidderId) WHERE tbl_winning_bids.WinnerId = '$id' ORDER BY tbl_tender_fields.Id ASC";
$Recordset1 = mysql_query($query_Recordset1, $tender) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
	
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
                'BORDER_COLOR'      => array(6,32,89),      //border color
                'BORDER_SIZE'       => '0.1',               //border size
				'BORDER_TYPE'       => 'LRT',
        ),
    
        'HEADER' => array(
                'TEXT_COLOR'        => array(255,255,255),   //text color
                'TEXT_SIZE'         => 9,                   //font size
                'LINE_SIZE'         => 6,                   //line size for one row
                'BACKGROUND_COLOR'  => array(6,32,89),  //background color
                'BORDER_SIZE'       => 0.1,                 //border size
                'BORDER_TYPE'       => 'B',                 //border type, can be: 0, 1 or a combination of: "LRTB"
                'BORDER_COLOR'      => array(6,32,89),      //border color
        ),

        'ROW' => array(
                'TEXT_COLOR'        => array(0,0,0),        //text color
                'TEXT_SIZE'         => 8,                   //font size
                'BACKGROUND_COLOR'  => array(255,255,255),  //background color
                'BORDER_COLOR'      => array(6,32,89),     //border color
				'PADDING_TOP'       => 1,
				'PADDING_BOTTOM'       => 1,
				'PADDING_LEFT'       => 1,
				'PADDING_RIGHT'       => 1,
        ),
);
	
	$nColumns = 2;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(85,85),$aCustomConfiguration);
	
	$aRow = array();

	$aRow[0]['BORDER_TYPE'] = '0';
	$aRow[0]['TEXT_SIZE'] = '14';
	$aRow[1]['TEXT_SIZE'] = '14';
	$aRow[1]['TEXT_TYPE'] = '';
	$aRow[0]['TEXT_COLOR'] = array(6,32,89);
	$aRow[1]['TEXT_COLOR'] = array(6,32,89);
	$aRow[1]['BORDER_TYPE'] = '0';
	$aRow[0]['TEXT'] = 'Tender Ref No: '. $row_Recordset2['TenderId']; 
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT'] = $row_Recordset2['CompanyName'];
	$aRow[1]['TEXT_ALIGN'] = "R"; 
	
	$oTable->addRow($aRow);	

	$aRow[0]['BORDER_TYPE'] = '0';
	$aRow[1]['TEXT_SIZE'] = '8';
	$aRow[1]['TEXT_TYPE'] = '';
	$aRow[1]['TEXT_COLOR'] = array(6,32,89);
	$aRow[1]['BORDER_TYPE'] = '0';
	$aRow[0]['TEXT'] = ''; 
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT'] = $row_Recordset2['ContactPerson'] .'
	'. $row_Recordset2['Email'];
	$aRow[1]['TEXT_ALIGN'] = "R"; 
	
	$oTable->addRow($aRow);	
	
	//close the table
	$oTable->close();
	
	$oPdf->Ln(15);
		
	//default text color
	$oPdf->SetTextColor(0, 0, 0);
	$oPdf->SetFont('dejavusans','',8);
	
	//create an advanced multicell    
	$oMulticell = tfpdfMulticell::getInstance($oPdf);
	$oMulticell->SetStyle("s1","dejavusans","",8,"118,0,3");
	$oMulticell->SetStyle("s2","dejavusans","",6,"0,49,159");
	
	$oMulticell->multiCell(170, 4, $row_Recordset1['Comments'], 0);
	$oPdf->Ln(15);
	
	$nColumns = 4;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(110,20,20,20),$aCustomConfiguration);
	
	$aHeader = array();
	
	//Table Header
	$names = array('Description','Qty','Price','Total');
	for ($i = 0; $i < $nColumns; $i ++) {
		
		$aHeader[0]['TEXT_ALIGN'] = "L";
		$aHeader[2]['TEXT_ALIGN'] = "R";
		$aHeader[3]['TEXT_ALIGN'] = "R";
		
		$headings = $names[$i];
		$aHeader[$i]['TEXT'] = $headings;
	}
		
	//add the header
	$oTable->addHeader($aHeader);
	
	$aRow = array();
	
	$x = 0;
	
	do{
		
		$x++;
		
		if($x % 2 == 0){
			
			$aRow[0]['BACKGROUND_COLOR'] = array(233,233,233);
			$aRow[1]['BACKGROUND_COLOR'] = array(233,233,233);
			$aRow[2]['BACKGROUND_COLOR'] = array(233,233,233);
			$aRow[3]['BACKGROUND_COLOR'] = array(233,233,233);
			
		} else {
			
			$aRow[0]['BACKGROUND_COLOR'] = array(250,250,250);
			$aRow[1]['BACKGROUND_COLOR'] = array(250,250,250);
			$aRow[2]['BACKGROUND_COLOR'] = array(250,250,250);
			$aRow[3]['BACKGROUND_COLOR'] = array(250,250,250);

		}
		
		$total  = $row_Recordset1['Qty'] * $row_Recordset1['Price'];
		$aRow[0]['TEXT'] = $row_Recordset1['Description']; 
		$aRow[0]['TEXT_ALIGN'] = "L";
		$aRow[1]['TEXT'] = $row_Recordset1['Qty'];
		$aRow[2]['TEXT_ALIGN'] = "R"; 
		$aRow[2]['TEXT'] = 'R'.$row_Recordset1['Price']; 
		$aRow[3]['TEXT_ALIGN'] = "R"; 
		$aRow[3]['TEXT'] = 'R'.number_format($total,2); 
		
		$_SESSION['overall'] = $row_Recordset1['OverallTotal'];
				
		//add the row
		$oTable->addRow($aRow);
				
	} while ($row_Recordset1 = mysql_fetch_assoc($Recordset1));
	
	$aRow[0]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[1]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[2]['BACKGROUND_COLOR'] = array(255,255,255);
	$aRow[2]['BACKGROUND_COLOR'] = array(6,32,89);
	$aRow[3]['BACKGROUND_COLOR'] = array(6,32,89);
	$aRow[0]['BORDER_TYPE'] = 'T';
	$aRow[0]['COLSPAN'] = 2;
	$aRow[0]['TEXT'] = ''; 
	$aRow[2]['TEXT_ALIGN'] = "R"; 
	$aRow[2]['TEXT_COLOR'] = array(255,255,255);
	$aRow[3]['TEXT_COLOR'] = array(255,255,255);; 

	// Sub Total
	
	$aRow[2]['TEXT'] = 'Sub Total:'; 
	$aRow[3]['TEXT'] = 'R'.number_format($_SESSION['overall'],2); 
		
	$oTable->addRow($aRow);	
	
	$aRow[0]['BORDER_TYPE'] = '0';

	// VAT
	
	$vat = $_SESSION['overall'] * 0.14;
	$aRow[2]['TEXT'] = 'VAT:'; 
	$aRow[3]['TEXT'] = 'R'.number_format($vat,2); 
		
	$oTable->addRow($aRow);	

	// Total
	
	$total = $vat + $_SESSION['overall'];
	$aRow[2]['TEXT'] = 'Total:'; 
	$aRow[3]['TEXT'] = 'R'.number_format($total,2); 
		
	$oTable->addRow($aRow);	
	
	//close the table
	$oTable->close();
	
	//send the pdf to the browser
	$oPdf->Output();
	$oPdf->Output('tender.pdf');
	
}

mysql_free_result($Recordset1);

mysql_free_result($Recordset2);
?>

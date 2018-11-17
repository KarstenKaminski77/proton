<?php
session_start();

//include fpdf class
require_once("tfpdf.php");

require_once('../functions/functions.php');

$con = mysqli_connect('sql6.jnb1.host-h.net','chevron_db','kwd001','chevron_db');

$jobid = $_GET['Id'];
refno($jobid);

$query = mysqli_query($con, "SELECT tbl_sites.Name AS Name_1, tbl_sites.Telephone AS SiteTel, tbl_jc.Priority AS PriorityId, tbl_jc.ClientFeedback, tbl_jc.StartDate, tbl_jc.StartTime, tbl_jc.EndDate, tbl_jc.WorkUndertaken, tbl_jc.ScopeOrWorkAsPerAbove, tbl_users.Username AS RMC_Mail, tbl_users_0.Username AS Contractor_Mail, tbl_jc.Id, tbl_jc.DateLogged, tbl_jc.ScopeOfWork, tbl_sites.Email, tbl_users.Name, tbl_users_0.Name AS Name_2, tbl_users_1.Name AS CallCentreName, tbl_users_0.Telephone, tbl_sites.Address, tbl_sites.Suburb, tbl_areas.Area, tbl_asset_categories.Category, tbl_fault_types.Fault, tbl_priorities.Priority, tbl_site_contacts.Name AS Name_c, tbl_site_contacts.Email AS Email_c, tbl_site_contacts.Telephone AS Telephone_c, tbl_contractor_equipment.EquipmentDescription, tbl_contractor_equipment.EquipmentNo, tbl_contractor_equipment.WarrantyExpiry, tbl_jc.ServiceRating, tbl_jc.AdditionalComments, tbl_jc.SignOffName, tbl_jc.SignOffDate, tbl_jc.SignOffTime, tbl_jc.StatusContractor, tbl_jc.FaultType, tbl_jc.DueDate, tbl_jc.SiteId, tbl_jc.FaultType
FROM ((((((((((tbl_jc
LEFT JOIN tbl_contractor_equipment ON tbl_contractor_equipment.JobId=tbl_jc.Id)
LEFT JOIN tbl_site_contacts ON tbl_site_contacts.SiteId=tbl_jc.SiteId)
LEFT JOIN tbl_priorities ON tbl_priorities.Id=tbl_jc.Priority)
LEFT JOIN tbl_areas ON tbl_areas.Id=tbl_jc.AreaId)
LEFT JOIN tbl_users AS tbl_users_0 ON tbl_users_0.Id=tbl_jc.CompanyId)
LEFT JOIN tbl_users AS tbl_users_1 ON tbl_users_1.Id=tbl_jc.OperatorId)
LEFT JOIN tbl_asset_categories ON tbl_asset_categories.Id=tbl_jc.CategoryId)
LEFT JOIN tbl_fault_types ON tbl_fault_types.Id=tbl_jc.FaultCategory)
LEFT JOIN tbl_sites ON tbl_sites.Id=tbl_jc.SiteId)
LEFT JOIN tbl_users ON tbl_users.Id=tbl_jc.RMCId) WHERE tbl_site_contacts.Active = '1' AND tbl_jc.Id = '$jobid' ") or die(mysqli_error($con));
$row = mysqli_fetch_assoc($query);

date_time_pdf($jobid);

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
$oPdf = new myPdf('L', 'mm', 'Letter');
$oPdf->Open();
$oPdf->SetAutoPageBreak(true, 20);
$oPdf->SetMargins(10, 10, 10);

$oPdf->AddFont('dejavusans',   '',     'DejaVuSans.ttf',       true);
$oPdf->AddFont('dejavusans',   'B',    'DejaVuSans-Bold.ttf',  true);
$oPdf->AddFont('dejavusans',   'BI',   'DejaVuSans-BoldOblique.ttf', true);
$oPdf->AddFont('dejavuserif',  '',     'DejaVuSerif.ttf',      true);
$oPdf->AddFont('dejavuserif',  'B',    'DejaVuSerif-Bold.ttf', true);
$oPdf->AddFont('dejavuserif',  'BI',   'DejaVuSerif-BoldItalic.ttf', true);
$oPdf->AddFont('dejavuserif',  'I',    'DejaVuSerif-Italic.ttf', true);
	
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
                'BORDER_COLOR'      => array(0,0,0),      //border color
                'BORDER_SIZE'       => '0.1',               //border size
				'BORDER_TYPE'       => 'LRTB',
        ),
    
        'HEADER' => array(
                'TEXT_COLOR'        => array(0,102,170),   //text color
                'TEXT_SIZE'         => 9,                   //font size
                'LINE_SIZE'         => 6,                   //line size for one row
                'BORDER_SIZE'       => '0.1',                 //border size
                'BORDER_TYPE'       => 'LRTB',                 //border type, can be: 0, 1 or a combination of: "LRTB"
                'BORDER_COLOR'      => array(0,0,0),      //border color
        ),

        'ROW' => array(
                'TEXT_COLOR'        => array(0,0,0),        //text color
                'TEXT_SIZE'         => 8,                   //font size
                'BORDER_COLOR'      => array(0,0,0),     //border color
				'PADDING_TOP'       => 0.325,
				'PADDING_BOTTOM'       => 0.32,
				'PADDING_LEFT'       => 1,
				'PADDING_RIGHT'       => 1,
				'BORDER_SIZE'       => '0.1',
        ),
);
	
	$oPdf->SetDrawColor(0,0,0);
	$oPdf->SetFont('Arial','B',14);
	$oPdf->Cell(180,10,'SEAVEST ASSET MANAGEMENT - JOB CARD','LTBR','','L');
	
	$oPdf->SetDrawColor(0,0,0);
	$oPdf->SetTextColor(228,20,33);
		
	$oPdf->SetFont('Arial','',9);
	$oPdf->Cell(40,10,'JOB CARD NO','LTBR','','C');
	
	$oPdf->SetFont('Arial','',9);
	$oPdf->Cell(40,10,$_SESSION['ref-no'],'LTBR','','C');
	$oPdf->Ln();
	
	$nColumns = 6;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(45,45,45,45,40,40),$aCustomConfiguration);
	
	$aRow = array();
	
	$aRow[0]['BORDER_TYPE'] = 'TLRB';
	$aRow[1]['BORDER_TYPE'] = 'TLRB';
	$aRow[2]['BORDER_TYPE'] = 'TLRB';
	$aRow[3]['BORDER_TYPE'] = 'TLRB';
	$aRow[4]['BORDER_TYPE'] = 'TLRB';
	
	$aRow[0]['BORDER_COLOR'] = array(0,0,0);
	$aRow[1]['BORDER_COLOR'] = array(0,0,0);
	$aRow[2]['BORDER_COLOR'] = array(0,0,0);
	$aRow[3]['BORDER_COLOR'] = array(0,0,0);
	$aRow[4]['BORDER_COLOR'] = array(0,0,0);
	
	$aRow[4]['COLSPAN'] =  2;
	$aRow[4]['ROWSPAN'] =  8;
	
	$aRow[0]['TEXT_TYPE'] = 'BI';
	$aRow[2]['TEXT_TYPE'] = 'BI';
		
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	$aRow[2]['TEXT_ALIGN'] = "L"; 
	$aRow[3]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_ALIGN'] = "L"; 

	$aRow[0]['TEXT'] = 'CALL CENTRE AGENT'; 
	$aRow[1]['TEXT'] =  $row['CallCentreName'];
	$aRow[2]['TEXT'] =  'CONTRACTOR';
	$aRow[3]['TEXT'] =  $row['Name_2'];
	
	$aRow[4] = array(
	'COLSPAN' => 2,
	'ROWSPAN' => 7,
    'TYPE' => 'IMAGE',
    'FILE' => 'http://www.chevron.sealink.co.za/images/sealink-logo.jpg',
    'WIDTH' => 10
);	
	
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	$aRow[2]['TEXT_ALIGN'] = "L"; 
	$aRow[3]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	
	$aRow[0]['TEXT_TYPE'] = 'BI';
	$aRow[2]['TEXT_TYPE'] = 'BI';

	$aRow[0]['TEXT'] = 'LOCATION'; 
	$aRow[1]['TEXT'] =  $row['Area'];
	$aRow[2]['TEXT'] =  'ASSET CATEGORY';
	$aRow[3]['TEXT'] =  $row['Fault'];
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	$aRow[2]['TEXT_ALIGN'] = "L"; 
	$aRow[3]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	
	$aRow[0]['TEXT_TYPE'] = 'BI';
	$aRow[2]['TEXT_TYPE'] = 'BI';

	$aRow[0]['TEXT'] = 'SITE NAME'; 
	$aRow[1]['TEXT'] =  $row['Name_1'];
	$aRow[2]['TEXT'] =  'FAULT CATEGORY';
	$aRow[3]['TEXT'] =  $row['Category'];
	
	$oTable->addRow($aRow);	
		
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	$aRow[2]['TEXT_ALIGN'] = "L"; 
	$aRow[3]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	
	$aRow[0]['TEXT_TYPE'] = 'BI';
	$aRow[2]['TEXT_TYPE'] = 'BI';

	$aRow[0]['TEXT'] = 'CUSTOMER ADDRESS'; 
	$aRow[1]['TEXT'] =  $row['Address'];
	$aRow[2]['TEXT'] =  'FAULT TYPE';
	$aRow[3]['TEXT'] =  $row['FaultType'] .'&nbsp;';
	
	$oTable->addRow($aRow);	
		
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	$aRow[2]['TEXT_ALIGN'] = "L"; 
	$aRow[3]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	
	$aRow[0]['TEXT_TYPE'] = 'BI';
	$aRow[2]['TEXT_TYPE'] = 'BI';

	$aRow[0]['TEXT'] = 'TEL NO'; 
	$aRow[1]['TEXT'] =  $row['SiteTel'];
	$aRow[2]['TEXT'] =  'EQUIPMENT DESCRIPTION';
	$aRow[3]['TEXT'] =  $row['EquipmentDescription'] .'&nbsp;';
	
	$oTable->addRow($aRow);	
		
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	$aRow[2]['TEXT_ALIGN'] = "L"; 
	$aRow[3]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	
	$aRow[0]['TEXT_TYPE'] = 'BI';
	$aRow[2]['TEXT_TYPE'] = 'BI';

	$aRow[0]['TEXT'] = 'CELL NO'; 
	$aRow[1]['TEXT'] =  $row['Telephone_c'] .'&nbsp;';
	$aRow[2]['TEXT'] =  'EQUIPMENT NUMBER';
	$aRow[3]['TEXT'] =  $row['EquipmentNo'] .'&nbsp;';
	
	$oTable->addRow($aRow);	
		
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	$aRow[2]['TEXT_ALIGN'] = "L"; 
	$aRow[3]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	
	$aRow[0]['TEXT_TYPE'] = 'BI';
	$aRow[2]['TEXT_TYPE'] = 'BI';

	$aRow[0]['TEXT'] = 'CONTACT PERSON'; 
	$aRow[1]['TEXT'] =  $row['Name_c'] .'&nbsp;';
	$aRow[2]['TEXT'] =  'WARRANTY EXPIRY DATE';
	$aRow[3]['TEXT'] =  $row['WarrantyExpiry'] .'&nbsp;';
	
	$oTable->addRow($aRow);	
		
	$aRow = array();
	
	$aRow[0]['COLSPAN'] = 6;

	$aRow[0]['TEXT'] = '&nbsp;'; 
	
	$oTable->addRow($aRow);	
		
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	$aRow[5]['TEXT_ALIGN'] = "L"; 	
	
	$aRow[0]['COLSPAN'] = 4;
	$aRow[0]['TEXT_TYPE'] = 'BI';
	$aRow[4]['TEXT_TYPE'] = 'BI';

	$aRow[0]['TEXT'] = 'DESCRIPTION OF WORK REQUESTED'; 
	$aRow[4]['TEXT'] = 'LOGGED BY'; 
	$aRow[5]['TEXT'] =  $row['Name'];
	
	$oTable->addRow($aRow);	
		
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	$aRow[5]['TEXT_ALIGN'] = "L";
	
	$aRow[0]['VERTICAL_ALIGN'] = 'T';
	
	$aRow[0]['COLSPAN'] = 4;
	$aRow[0]['ROWSPAN'] = 5;
	$aRow[4]['TEXT_TYPE'] = 'BI';

	$aRow[0]['TEXT'] = $row['ScopeOfWork']; 
	$aRow[4]['TEXT'] = 'DATE LOGGED'; 
	$aRow[5]['TEXT'] =  $_SESSION['date'];
	
	$oTable->addRow($aRow);
	
	$aRow = array();
	
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	$aRow[5]['TEXT_ALIGN'] = "L"; 	
	$aRow[4]['TEXT_TYPE'] = 'BI';

	$aRow[4]['TEXT'] = 'TIME LOGGED'; 
	$aRow[5]['TEXT'] =  $_SESSION['time'];
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	$aRow[5]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_TYPE'] = 'BI';

	$aRow[4]['TEXT'] =  'DUE DATE';
	$aRow[5]['TEXT'] =  $row['DueDate'];
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	$aRow[5]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_TYPE'] = 'BI';

	$aRow[4]['TEXT'] =  'SITE NO';
	$aRow[5]['TEXT'] =  $row['SiteId'];
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	$aRow[5]['TEXT_ALIGN'] = "L"; 	
	$aRow[4]['TEXT_TYPE'] = 'BI';

	$aRow[4]['TEXT'] =  'PRIORITY';
	$aRow[5]['TEXT'] =  $row['Priority'];
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	$aRow[5]['TEXT_ALIGN'] = "L"; 	
	
	$aRow[0]['COLSPAN'] = 4;
	$aRow[4]['COLSPAN'] = 2;
	$aRow[0]['TEXT_TYPE'] = 'BI';
	$aRow[4]['TEXT_TYPE'] = 'BI';

	$aRow[0]['TEXT'] = 'WORK UNDERTAKEN BY CONTRACTOR'; 
	$aRow[5]['TEXT'] = '&nbsp;'; 
	
	$oTable->addRow($aRow);	
		
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	$aRow[5]['TEXT_ALIGN'] = "L";
	
	$aRow[0]['VERTICAL_ALIGN'] = 'T';
	
	$aRow[0]['COLSPAN'] = 4;
	$aRow[0]['ROWSPAN'] = 5;
	$aRow[4]['TEXT_TYPE'] = 'BI';

	$aRow[0]['TEXT'] = '&nbsp;'; 
	$aRow[4]['TEXT'] = 'ACTUAL START DATE'; 
	$aRow[5]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);
	
	$aRow = array();
	
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	$aRow[5]['TEXT_ALIGN'] = "L"; 	
	$aRow[4]['TEXT_TYPE'] = 'BI';

	$aRow[4]['TEXT'] = 'ACTUAL START TIME'; 
	$aRow[5]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	$aRow[5]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_TYPE'] = 'BI';

	$aRow[4]['TEXT'] =  'WORK COMPLETED DATE & TIME';
	$aRow[5]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	$aRow[5]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_TYPE'] = 'BI';

	$aRow[4]['TEXT'] =  'TECHNICIAN NAME & SURNAME';
	$aRow[5]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	$aRow[5]['TEXT_ALIGN'] = "L"; 	
	$aRow[4]['TEXT_TYPE'] = 'BI';

	$aRow[4]['TEXT'] =  'TECHNICIAN SIGNATURE';
	$aRow[5]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L"; 
	$aRow[0]['COLSPAN'] = 6; 	
	$aRow[0]['TEXT_TYPE'] = 'BI';

	$aRow[0]['TEXT'] =  'CLIENT FEEDBACK';
	
	$oTable->addRow($aRow);	
	
	//close the table
	$oTable->close();
	
	$oPdf->Image('../images/icons/logo.jpg',193,23,72);
	
	$nColumns = 5;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(45,15,15,45,140),$aCustomConfiguration);
	
	$aRow = array();
	
	$aRow[0]['BORDER_TYPE'] = 'TLRB';
	$aRow[1]['BORDER_TYPE'] = 'TLRB';
	$aRow[2]['BORDER_TYPE'] = 'TLRB';
	$aRow[3]['BORDER_TYPE'] = 'TLRB';
	$aRow[4]['BORDER_TYPE'] = 'TLRB';
	
	$aRow[0]['BORDER_COLOR'] = array(0,0,0);
	$aRow[1]['BORDER_COLOR'] = array(0,0,0);
	$aRow[2]['BORDER_COLOR'] = array(0,0,0);
	$aRow[3]['BORDER_COLOR'] = array(0,0,0);
	$aRow[4]['BORDER_COLOR'] = array(0,0,0);
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	$aRow[2]['TEXT_ALIGN'] = "L"; 
	$aRow[3]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	
	$aRow[4]['ROWSPAN'] = 4;

	$aRow[0]['TEXT'] = 'Scope or work as per above<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>'; 
	$aRow[1]['TEXT'] =  'Yes';
	$aRow[2]['TEXT'] =  'No';
	$aRow[3]['TEXT'] =  'If No , Please give details';
	$aRow[4]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[0]['BORDER_TYPE'] = 'LR';
	$aRow[1]['BORDER_TYPE'] = 'LR';
	$aRow[2]['BORDER_TYPE'] = 'LR';
	$aRow[3]['BORDER_TYPE'] = 'LR';
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	$aRow[2]['TEXT_ALIGN'] = "L"; 
	$aRow[3]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_ALIGN'] = "L"; 

	$aRow[0]['TEXT'] = '&nbsp;'; 
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[0]['BORDER_TYPE'] = 'LR';
	$aRow[1]['BORDER_TYPE'] = 'LR';
	$aRow[2]['BORDER_TYPE'] = 'LR';
	$aRow[3]['BORDER_TYPE'] = 'LR';
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	$aRow[2]['TEXT_ALIGN'] = "L"; 
	$aRow[3]['TEXT_ALIGN'] = "L"; 

	$aRow[0]['TEXT'] = '&nbsp;'; 
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[0]['BORDER_TYPE'] = 'LRB';
	$aRow[1]['BORDER_TYPE'] = 'LRB';
	$aRow[2]['BORDER_TYPE'] = 'LRB';
	$aRow[3]['BORDER_TYPE'] = 'LRB';
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	$aRow[2]['TEXT_ALIGN'] = "L"; 
	$aRow[3]['TEXT_ALIGN'] = "L"; 

	$aRow[0]['TEXT'] = '&nbsp;'; 
	
	$oTable->addRow($aRow);	
	
	//close the table
	$oTable->close();
	
	$nColumns = 5;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(45,15,15,15,15,155),$aCustomConfiguration);
	
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	$aRow[2]['TEXT_ALIGN'] = "L"; 
	$aRow[3]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_ALIGN'] = "L"; 
	
	$aRow[0]['ROWSPAN'] = 2;
	$aRow[5]['ROWSPAN'] = 2;
	$aRow[0]['VERTICAL_ALIGN'] = 'T';

	$aRow[0]['TEXT'] = 'Please rate Service Delivered'; 
	$aRow[1]['TEXT'] =  'Poor';
	$aRow[2]['TEXT'] =  'Fair';
	$aRow[3]['TEXT'] =  'Good';
	$aRow[4]['TEXT'] =  'Excellent';
	$aRow[5]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	$aRow[2]['TEXT_ALIGN'] = "L"; 
	$aRow[3]['TEXT_ALIGN'] = "L"; 
	$aRow[4]['TEXT_ALIGN'] = "L"; 

	$aRow[1]['TEXT'] =  '&nbsp;';
	$aRow[2]['TEXT'] =  '&nbsp;';
	$aRow[3]['TEXT'] =  '&nbsp;';
	$aRow[4]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	//close the table
	$oTable->close();
	
	$nColumns = 2;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(45,215),$aCustomConfiguration);
	
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	
	$aRow[1]['ROWSPAN'] = 4;
	$aRow[0]['VERTICAL_ALIGN'] = 'T';
	
	$aRow[0]['BORDER_TYPE'] = 'LTR';

	$aRow[0]['TEXT'] = 'Any Additional Comments'; 
	$aRow[1]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['BORDER_TYPE'] = 'LR';

	$aRow[0]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['BORDER_TYPE'] = 'LR';

	$aRow[0]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[0]['BORDER_TYPE'] = 'LRB';

	$aRow[0]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	//close the table
	$oTable->close();
	
	$nColumns = 3;
	
	//Initialize the table class, 3 columns
	$oTable->initialize(array(45,75,140),$aCustomConfiguration);
	
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	
	$aRow[2]['ROWSPAN'] = 4; 
	
	$aRow[0]['TEXT'] = 'Customer Signature'; 
	$aRow[1]['TEXT'] =  '&nbsp;';
	$aRow[2]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	
	$aRow[0]['TEXT'] = 'Customer Name and Surname'; 
	$aRow[1]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	
	$aRow[0]['TEXT'] = 'Date'; 
	$aRow[1]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
	
	$aRow = array();
	
	$aRow[0]['TEXT_ALIGN'] = "L";
	$aRow[1]['TEXT_ALIGN'] = "L"; 
	
	$aRow[0]['TEXT'] = 'Time'; 
	$aRow[1]['TEXT'] =  '&nbsp;';
	
	$oTable->addRow($aRow);	
		
	//close the table
	$oTable->close();
	
	//send the pdf to the browser
	
	if(isset($_GET['Preview'])){
	
		$oPdf->Output();
		
	} else {
		
		$oPdf->Output('pdf/Seavest Asset Management #'. $_GET['Id'] .'.pdf');
	}
	
	    //header('Location: ../qs-select.php?Status='. $_GET['Status']);

?>

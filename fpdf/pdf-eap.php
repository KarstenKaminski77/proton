<?php 
session_start();

require_once('../Connections/seavest.php');

require_once('../functions/functions.php');

require_once('../Connections/inv.php');

select_db();

$id = $_GET['Id'];

mysql_query("UPDATE tbl_jsa SET Status = '1' WHERE Id = '$id'")or die(mysql_error());

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

$KTColParam1_Recordset1 = "0";
if (isset($_GET["Id"])) {
  $KTColParam1_Recordset1 = $_GET["Id"];
}
mysql_select_db($database_seavest, $seavest);
$query_Recordset1 = sprintf("SELECT tbl_sites.Name AS Name_1, tbl_companies.Name, tbl_sites.Address, tbl_sites.Suburb, tbl_sites.Id FROM (tbl_companies LEFT JOIN tbl_sites ON tbl_sites.Company=tbl_companies.Id) WHERE tbl_sites.Id=%s ", GetSQLValueString($KTColParam1_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $seavest) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

$KTColParam1_Recordset2 = "0";
if (isset($_GET["Id"])) {
  $KTColParam1_Recordset2 = $_GET["Id"];
}
mysql_select_db($database_inv, $inv);
$query_Recordset2 = sprintf("SELECT * FROM tbl_eap WHERE SiteId = %s ", GetSQLValueString($KTColParam1_Recordset2, "int"));
$Recordset2 = mysql_query($query_Recordset2, $inv) or die(mysql_error());
$row_Recordset2 = mysql_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysql_num_rows($Recordset2);

$colname_Recordset3 = "-1";
if (isset($_GET['Id'])) {
  $colname_Recordset3 = $_GET['Id'];
}
mysql_select_db($database_inv, $inv);
$query_Recordset3 = sprintf("SELECT * FROM tbl_job_steps WHERE HESId = %s AND JMS = '0'", GetSQLValueString($colname_Recordset3, "int"));
$Recordset3 = mysql_query($query_Recordset3, $inv) or die(mysql_error());
$row_Recordset3 = mysql_fetch_assoc($Recordset3);
$totalRows_Recordset3 = mysql_num_rows($Recordset3);


$KTColParam1_Recordset4 = "0";
if (isset($_GET["Id"])) {
  $KTColParam1_Recordset4 = $_GET["Id"];
}
mysql_select_db($database_inv, $inv);
$query_Recordset4 = sprintf("SELECT tbl_hes.Id, tbl_hes_jsa_relation.JSAId, tbl_far_high_risk_classification.Risk FROM ((tbl_hes LEFT JOIN tbl_hes_jsa_relation ON tbl_hes_jsa_relation.JobNo=tbl_hes.JobNo) LEFT JOIN tbl_far_high_risk_classification ON tbl_far_high_risk_classification.Id=tbl_hes_jsa_relation.JSAId) WHERE tbl_hes.Id=%s ", GetSQLValueString($KTColParam1_Recordset4, "int"));
$Recordset4 = mysql_query($query_Recordset4, $inv) or die(mysql_error());
$row_Recordset4 = mysql_fetch_assoc($Recordset4);
$totalRows_Recordset4 = mysql_num_rows($Recordset4);

require('mc_table.php');

$pdf=new PDF_MC_Table('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetTextColor(237,28,36);
$pdf->SetFont('Arial','B',16);
$pdf->Image('hes-banner.jpg',10,4,190);
$pdf->Ln(18);
$pdf->Cell(140,10,'');
$pdf->Ln(25);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,10,'Emergency Action Plan','','','C');
$pdf->SetTextColor(0,0,0);
$pdf->Ln(20);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(23,5,'Site:','','','L');
$pdf->SetFont('Arial','',9);
$pdf ->Cell(167,5,$row_Recordset1['Name_1'],'','','L');
$pdf->Ln(4);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(23,5,'Location:','','','L');
$pdf->SetFont('Arial','',9);
$pdf ->Cell(167,5,$row_Recordset1['Address'],'','','L');
$pdf->Ln(4);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(23,5,'','','','L');
$pdf->SetFont('Arial','',9);
$pdf ->Cell(167,5,$row_Recordset1['Suburb'],'','','L');
$pdf->Ln(4);

$pdf->Cell(140,10,'');
$pdf->Ln(1);
$pdf->Cell(140,10,'');
$pdf->Ln(4);
$pdf->Cell(140,10,'');
$pdf->Ln(7);

// Emergency Telephone Numbers

$pdf->SetFont('Arial','B',12);
$pdf ->Cell(190,5,'Emergency Telephone Numbers','','','L');
$pdf->Ln(10);

$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(226,226,226);

// Header

$pdf->Cell(45,5,'Contact','LRTB','','','1','');
$pdf->Cell(50,5,'Telephone Number','LRTB','','','1','');
$pdf->Cell(45,5,'Contact','LRTB','','','1','');
$pdf->Cell(50,5,'Telephone Number','LRTB','','','1','');

$pdf->Ln();

// Emergency Numbers

$pdf->SetFont('Arial','',9);

$pdf->Cell(45,5,'Ambulance','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['Ambulance'],'LRTB','','','','');
$pdf->Cell(45,5,'Fire','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['Fire'],'LRTB','','','','');
$pdf->Ln();

$pdf->Cell(45,5,'Police','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['Police'],'LRTB','','','','');
$pdf->Cell(45,5,'Hospital Name','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['HospitalName'],'LRTB','','','','');
$pdf->Ln();

$pdf->Cell(45,5,'Hospital Phone','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['HospitalPhone'],'LRTB','','','','');
$pdf->Cell(45,5,'Responsible RMC','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['ResponsibleRMC'],'LRTB','','','','');
$pdf->Ln();

$pdf->Cell(45,5,'CHES Specialist','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['SafetySpecialist'],'LRTB','','','','');
$pdf->Cell(45,5,'Client Contact','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['ClientContact'],'LRTB','','','','');
$pdf->Ln();

$pdf->Cell(45,5,'Maintenance Specialist','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['MaintenanceSpecialist'],'LRTB','','','','');
$pdf->Cell(45,5,'','LRTB','','','','');
$pdf->Cell(50,5,'','LRTB','','','','');
$pdf->Ln();

$pdf->Ln(15);

// Utility Emergency Telephone Numbers

$pdf->SetFont('Arial','B',12);
$pdf ->Cell(190,5,'Utility Emergency Telephone Numbers','','','L');
$pdf->Ln(10);

// Header

$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(226,226,226);

$pdf->Cell(45,5,'Utility','LRTB','','','1','');
$pdf->Cell(50,5,'Telephone Number','LRTB','','','1','');
$pdf->Cell(45,5,'Utility','LRTB','','','1','');
$pdf->Cell(50,5,'Telephone Number','LRTB','','','1','');

$pdf->Ln();
$pdf->SetFont('Arial','',9);

// Utility Numbers

$pdf->Cell(45,5,'Water','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['Water'],'LRTB','','','','');
$pdf->Cell(45,5,'Gas','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['Gas'],'LRTB','','','','');
$pdf->Ln();

$pdf->Cell(45,5,'Electric','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['Electric'],'LRTB','','','','');
$pdf->Cell(45,5,'Telephone | Cable','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['TelephoneCable'],'LRTB','','','','');
$pdf->Ln();

$pdf->Cell(45,5,'Sewer','LRTB','','','','');
$pdf->Cell(50,5,$row_Recordset2['Sewer'],'LRTB','','','','');
$pdf->Cell(45,5,'','LRTB','','','','');
$pdf->Cell(50,5,'','LRTB','','','','');
$pdf->Ln();

$pdf->Ln(15);

// Directions To Hospital

$pdf->SetFont('Arial','B',12);
$pdf ->Cell(190,5,'Directions To Hospital','','','L');
$pdf->Ln(10);

$pdf->SetFont('Arial','',9);
$pdf->SetDrawColor(255,255,255);

$pdf->SetWidths(array(190));
$pdf->Row(array($row_Recordset2['Directions']));

$pdf->AddPage();

// General Instructions

$pdf->SetFont('Arial','B',12);
$pdf ->Cell(190,5,'Critical Hazards','','','L');
$pdf->Ln(5);
$pdf ->Cell(190,5,'Working On Scaffold','','','L');
$pdf->Ln(10);
$pdf ->Cell(190,5,'Minimum Personal Protective Equipment','','','L');
$pdf->Ln(10);

$pdf->SetFont('Arial','',9);
$pdf->Cell(190,5,'Full Overalls','LRTB','','','','');
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$pdf->Cell(190,5,'Safety Shoes','LRTB','','','','');
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$pdf->Cell(190,5,'High Visibility Vests','LRTB','','','','');
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$pdf->Cell(190,5,'Gloves','LRTB','','','','');
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$pdf->Cell(190,5,'Safety Harness with double lanyard','LRTB','','','','');
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$pdf->Cell(190,5,'Hard Hat with chin strap','LRTB','','','','');
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$pdf->Cell(190,5,'Life Line','LRTB','','','','');
$pdf->Ln();


$pdf->Ln(15);

$pdf->SetFont('Arial','B',12);
$pdf ->Cell(190,5,'Onsite Location Of Fist Aid Kit And Safety Supplies','','','L');
$pdf->Ln(10);

$pdf->SetFont('Arial','',9);
$pdf->SetWidths(array(190));
$pdf->Row(array('First aid kit and safety supplies are located in the foreman’s vehicle behind the driver’s seat.'));
$pdf->Ln(3);

$pdf->SetFont('Arial','B',10);
$pdf->SetWidths(array(190));
$pdf->Row(array('Emergency First Aid'));
$pdf->Ln(3);

$pdf->SetFont('Arial','',9);
$pdf->SetWidths(array(190));
$pdf->Row(array('Ensure first aid kit is stocked with minimum requirements as per Annexure to General Safety Regulations in OHSAct.'));
$pdf->Ln(10);

$pdf->SetFont('Arial','B',10);
$pdf->SetWidths(array(190));
$pdf->Row(array('First Aid For Petoleum Hydrocarbo Emergencies'));
$pdf->Ln(3);

$pdf->SetDrawColor(0,0,0);

$pdf->SetFont('Arial','',9);
$pdf->SetWidths(array(50,140));
$pdf->Row(array('
 Ingestion','
DO NOT INDUCE VOMITING.  Call Poison Control; follow instructions.  Administer cardiopulmonary resuscitation (CPR), if necessary.  Seek medical attention

'));
$pdf->Ln(0);

$pdf->SetFont('Arial','',9);
$pdf->SetWidths(array(50,140));
$pdf->Row(array('
 Inhalation','
Remove person from contaminated environment.  DO NOT ENTER A CONFINED SPACE TO RESCUE SOMEONE WHO HAS BEEN OVERCOME UNLESS PROPERLY EQUIPPED, TRAINED, AND A STANDBY PERSON PRESENT. Administer CPR if necessary.  Seek medical attention.

'));
$pdf->Ln(0);

$pdf->SetFont('Arial','',9);
$pdf->SetWidths(array(50,140));
$pdf->Row(array('
 Skin Contact','
Brush off dry material, remove wet or contaminated clothing.  Flush skin thoroughly with water.  Seek medical attention if irritation persists.

'));
$pdf->Ln(0);

$pdf->SetFont('Arial','',9);
$pdf->SetWidths(array(50,140));
$pdf->Row(array('
 Eye Contact','
Flush eyes with water for 15 minutes.  Seek medical attention.

'));
$pdf->Ln(0);

$pdf->SetFont('Arial','',9);
$pdf->SetWidths(array(50,140));
$pdf->Row(array('
 Exposure Symptoms','
Headache, dizziness, nausea, drowsiness, irritation of the eyes, nose throat, skin, breathing difficulties.

'));
$pdf->Ln(0);

$pdf->SetFont('Arial','',9);
$pdf->SetWidths(array(50,140));
$pdf->Row(array('
 Contingency Plan','
Report incidents to HSE Department after emergency procedures have been implemented.

'));

$pdf->AddPage();

$pdf->SetFont('Arial','B',10);
$pdf->SetDrawColor(255,255,255);
$pdf->SetWidths(array(190));
$pdf->Row(array('Response To Emergency Steps'));
$pdf->Ln(3);

$pdf->SetFont('Arial','',9);
$pdf->SetWidths(array(190));
$pdf->Row(array('1.	Survey the situation.  Do not endanger your own life.  DO NOT ENTER A CONFINED SPACE TO RESCUE SOMEONE WHO HAS BEEN OVERCOME UNLESS PROPERLY EQUIPPED, TRAINED, AND A STANDBY PERSON IS PRESENT.

2.	Call Net care 911 (if available) for paramedics or the fire department IMMEDIATELY.  Explain the physical injury, chemical exposure, fire, or release situation.

3.	Decontaminate victim without delaying life-saving procedures.

4.	If victim’s condition appears to be non-critical, but seems to be more severe than minor cuts, he/she should be transported to the nearest hospital by trained Emergency Medical Services (EMS) personnel: let the doctor assume the responsibility for determining the severity of the injury.  If the condition is obviously serious, EMS must transport the victim.

5.	Notify the HSE Department.  Complete the applicable Incident Reporting Forms within 24 hours.'));

$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(226,226,226);
$pdf->Ln(15);

// Header

$pdf->Cell(190,5,'Emergency First Aid Procedures','LRTB','','C','1','');
$pdf->Ln();
$pdf->Cell(95,5,'Stop Bleeding','LRTB','','C','','');
$pdf->Cell(95,5,'CPR','LRTB','','C','','');

$pdf->Ln();

$pdf->SetFont('Arial','',9);
$pdf->SetWidths(array(95,95));
$pdf->Row(array('
1.	Give medical statement.
2.	Assure airway, breathing, circulation.
3.	Use DIRECT PRESSURE over the wound with clean dressing or your hand (use no permeable gloves).  Direct pressure will control most bleeding.
4.	Bleeding from an artery or several injury sites may require DIRECT PRESSURE on a PRESSURE POINT.  Use pressure points for 30 - 60 seconds to help control severe bleeding.
5.	Continue primary care and seek medical aid as needed.
','
1.	Call for help.
2.	Arousal:  check for consciousness.
3.	Open airway with chin-lift.
4.	Look, listen, and feel for breathing.
5.	If breathing is absent, give 2 slow, full rescue breaths.
6.	Check the pulse for 5 to 10 seconds.
7.	If pulse is present, continue rescue breathing: 1 breath every 5 seconds.
8.	If pulse is absent, start CPR:  15 compressions, 2 breaths (1 man).

'));

$pdf->SetTextColor(255, 0, 0);
$pdf->SetFont('Arial','B',13);
$pdf->Ln(20);

$pdf->MultiCell(190,'7', 'If you are uncertain about any safety procedures, action and control stop and call for assistance. Control centre 0861474705. Nicky Jamun 0837849532', '', 'C');


if(isset($_GET['Preview'])){
	
	$pdf->Output();
	
} elseif(isset($_GET['Close'])){
	
	$id = $_GET['HESId'];
	$document = 'Seavest EAP #'.$id.'.pdf';
	
	$pdf->Output('pdf/'.$document);
	
	mysql_query("UPDATE tbl_hes_documents_relation SET PDF = '$document', Active = '1', New = '0' WHERE HESId = '$id' AND DocumentId = '1'")or die(mysql_error());
	
	header('Location: ../health-safety/hes/pending.php');
	
} else {
	
	$id = $_GET['HESId'];
	$document = 'Seavest EAP #'.$id.'.pdf';
	
	$pdf->Output('pdf/'.$document);
	
	mysql_query("UPDATE tbl_hes_documents_relation SET PDF = '$document' WHERE HESId = '$id' AND DocumentId = '1'")or die(mysql_error());
	
	header('Location: ../health-safety/eap/eap.php?Id='. $_GET['Id'] .'&HESId='. $_GET['HESId']);
}

?>

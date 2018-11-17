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
$query_Recordset1 = sprintf("SELECT tbl_sites.Id AS Id_2, tbl_sites.Name AS Name_1, tbl_far_high_risk_classification.Risk, tbl_hes.JobNo, tbl_hes.Date, tbl_hes.ScopeOfWork, tbl_hes_jsa_relation.JSAId, tbl_sites.FirstName, tbl_sites.LastName, tbl_sites.Address, tbl_sites.Suburb, tbl_sites.Telephone, tbl_hes.Id, tbl_sites.Email, tbl_companies.Name FROM ((((tbl_hes LEFT JOIN tbl_hes_jsa_relation ON tbl_hes_jsa_relation.JobNo=tbl_hes.JobNo) LEFT JOIN tbl_companies ON tbl_companies.Id=tbl_hes.CompanyId) LEFT JOIN tbl_far_high_risk_classification ON tbl_far_high_risk_classification.Id=tbl_hes_jsa_relation.JSAId) LEFT JOIN tbl_sites ON tbl_sites.Id=tbl_hes.SiteId) WHERE tbl_hes.Id=%s ", GetSQLValueString($KTColParam1_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $seavest) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

$KTColParam1_Recordset2 = "0";
if (isset($_GET["Id"])) {
  $KTColParam1_Recordset2 = $_GET["Id"];
}
mysql_select_db($database_inv, $inv);
$query_Recordset2 = sprintf("SELECT tbl_ppe_list.PPE, tbl_ppe_relation.PPEId, tbl_ppe_relation.Selected, tbl_ppe_relation.Comments, tbl_ppe_relation.HESId, tbl_ppe_relation.Id FROM (tbl_ppe_relation LEFT JOIN tbl_ppe_list ON tbl_ppe_list.Id=tbl_ppe_relation.PPEId) WHERE tbl_ppe_relation.HESId=%s AND tbl_ppe_relation.WorkType = '1'", GetSQLValueString($KTColParam1_Recordset2, "int"));
$Recordset2 = mysql_query($query_Recordset2, $inv) or die(mysql_error());
$row_Recordset2 = mysql_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysql_num_rows($Recordset2);

$colname_Recordset3 = "-1";
if (isset($_GET['Id'])) {
  $colname_Recordset3 = $_GET['Id'];
}
mysql_select_db($database_inv, $inv);
$query_Recordset3 = sprintf("SELECT * FROM tbl_job_steps WHERE HESId = %s AND JMS = '0' AND WorkType = '1'", GetSQLValueString($colname_Recordset3, "int"));
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

// $pdf->Image('../images/no.jpg',10,10);
// $pdf->Ln(10);
$pdf->SetTextColor(237,28,36);
$pdf->SetFont('Arial','B',16);
$pdf->Image('hes-banner.jpg',10,4,190);
$pdf->Ln(18);
$pdf->Cell(140,10,'');
$pdf->Ln(25);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,10,'Job Safety Analysis','','','C');
$pdf->SetFont('Arial','',9);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(190,10,'','','','R');
$pdf->Cell(140,10,'');
$pdf->Ln(20);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(23,5,'Site:','','','L');
$pdf->SetFont('Arial','',9);
$pdf ->Cell(167,5,$row_Recordset1['Name_1'],'','','L');
$pdf->Ln(4);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(23,5,'Refernce:','','','L');
$pdf->SetFont('Arial','',9);
$pdf ->Cell(167,5,$row_Recordset1['JobNo'],'','','L');
$pdf->Ln(4);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(23,5,'Work Activity:','','','L');
$pdf->SetFont('Arial','',9);
$pdf ->Cell(167,5,$row_Recordset1['ScopeOfWork'],'','','L');
$pdf->Ln(4);

$i = 1;

do {
	
	$i++;
	
	if($i == 2){
		
		$type = 'JSA Type:';
		
	} else {
		
		$type = '';
	}
	
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(23,5,$type,'','','L');
	$pdf->SetFont('Arial','',9);
	$pdf ->Cell(167,5,$row_Recordset4['Risk'],'','','L');
	$pdf->Ln(4);
	
} while ($row_Recordset4 = mysql_fetch_assoc($Recordset4));

$pdf->Cell(140,10,'');
$pdf->Ln(1);
$pdf->Cell(140,10,'');
$pdf->Ln(4);
$pdf->Cell(140,10,'');
$pdf->Ln(7);

//Personal Protective Equipment (PPE)

$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(226,226,226);

$pdf->Cell(85,5,'Personal Protective Equipment (PPE)','LRTB','','0','1','');
$pdf->Cell(20,5,'Selected','LRTB','','C','1','');
$pdf->Cell(85,5,'Comments','LRTB','','0','1','');

$pdf->Ln();

$pdf->SetFont('Arial','',9);

do {
	
	if($row_Recordset2['Selected'] == 1){
		
		$selected = 'Yes';
		
	} else {
		
		$selected = '';
		
	}
	
	$pdf ->Cell(85,5,$row_Recordset2['PPE'],'LRTB','0','L','','');
	$pdf ->Cell(20,5,$selected,'LRTB','','C','');
	$pdf ->Cell(85,5,$row_Recordset2['Comments'],'LRTB','','L','');
	
	$pdf->Ln();
	
} while ($row_Recordset2 = mysql_fetch_assoc($Recordset2));	

$pdf->AddPage();

//Job Steps

$pdf->SetFont('Arial','B',9);

$pdf->Cell(63,5,'Job Step','LRTB','','0','1','');
$pdf->Cell(63,5,'Potential Hazard','LRTB','','0','1','C');
$pdf->Cell(63,5,'Critical Actions','LRTB','','0','1','');

$pdf->Ln();

$pdf->SetFont('Arial','',9);

do {
	
	$pdf->SetWidths(array(63,63,63));
	$pdf->Row(array($row_Recordset3['JobSteps'],$row_Recordset3['PotentialHazard'],$row_Recordset3['CriticalActions']));
	
} while ($row_Recordset3 = mysql_fetch_assoc($Recordset3));	

$pdf->Ln(15);

// Signature

$pdf->SetFont('Arial','B',9);

$pdf->Cell(50,5,'Contractor HES Representative:');
$pdf->Cell(50,5,'','B');
$pdf->Ln(18);
$pdf->Cell(50,5,'Signatue:');
$pdf->Cell(50,5,'','B');

$pdf->Cell(50,5,'','B');

if(isset($_GET['Preview'])){
	
	$pdf->Output();
	
} elseif(isset($_GET['Close'])){
	
	$document = 'Seavest JSA #'.$id.'.pdf';
	
	$pdf->Output('pdf/'. $document);
	
	mysql_query("UPDATE tbl_hes_documents_relation SET PDF = '$document', Active = '1', New = '0' WHERE HESId = '$id' AND DocumentId = '2'")or die(mysql_error());
	
	header('Location: ../health-safety/hes/pending.php');
	
} else {
	
	$document = 'Seavest JSA #'.$id.'.pdf';
	
	$pdf->Output('pdf/'. $document);
	
	mysql_query("UPDATE tbl_hes_documents_relation SET PDF = '$document' WHERE HESId = '$id' AND DocumentId = '2'")or die(mysql_error());
	
	header('Location: ../health-safety/jsa/jsa.php?Id='. $_GET['Id']);
}

?>

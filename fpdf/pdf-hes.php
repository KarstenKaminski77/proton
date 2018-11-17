<?php 
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

session_start();

$con = mysqli_connect('sql18.jnb2.host-h.net','sealink','kwd001','sealink');

$id = $_GET['Id'];

mysqli_query($con, "UPDATE tbl_jsa SET Status = '1' WHERE Id = '$id'")or die(mysqli_error($con));

$id = $_GET["Id"];

$query_Recordset1 = "SELECT tbl_sites.Name AS Name_1, tbl_hes.JobNo, tbl_hes.Date, tbl_companies.Name, tbl_hes.ScopeOfWork, tbl_hes.Equipment, tbl_hes.RiskRanking, tbl_hes.Contractor, tbl_hes.ContractorRating, tbl_hes.PermitIssuer, tbl_hes.SafetyOfficer, tbl_hes.FallProtecionPlan, tbl_hes.Id FROM ((tbl_hes LEFT JOIN tbl_sites ON tbl_sites.Id=tbl_hes.SiteId) LEFT JOIN tbl_companies ON tbl_companies.Id=tbl_hes.CompanyId) WHERE tbl_hes.Id='$id' ";
$Recordset1 = mysqli_query($con, $query_Recordset1) or die(mysqli_error($con));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

$query_Recordset2 = "SELECT * FROM tbl_far_high_risk_classification ORDER BY Risk ASC";
$Recordset2 = mysqli_query($con, $query_Recordset2) or die(mysqli_error($con));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);

$query_Recordset3 = "SELECT * FROM tbl_hes_jsa_relation";
$Recordset3 = mysqli_query($con, $query_Recordset3) or die(mysqli_error($con));
$row_Recordset3 = mysqli_fetch_assoc($Recordset3);
$totalRows_Recordset3 = mysqli_num_rows($Recordset3);

$jobno = $row_Recordset1['JobNo'];

require('mc_table.php');

$pdf=new PDF_MC_Table('P', 'mm', 'A4');
$pdf->AddPage();

// Header

$pdf->SetTextColor(237,28,36);
$pdf->SetFont('Arial','B',16);
$pdf->Image('hes-banner.jpg',10,4,190);
$pdf->Ln(18);
$pdf->Cell(140,10,'');
$pdf->Ln(25);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,10,'Delegation of Authority','','','C');
$pdf->SetTextColor(0,0,0);
$pdf->Ln(15);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(20,5,'Date:','','','L');
$pdf->SetFont('Arial','',9);
$pdf ->Cell(170,5,$row_Recordset1['Date'],'','','L');
$pdf->Ln(4);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(20,5,'Job No:','','','L');
$pdf->SetFont('Arial','',9);
$pdf ->Cell(170,5,$row_Recordset1['JobNo'],'','','L');
$pdf->Ln(4);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(20,5,'Site:','','','L');
$pdf->SetFont('Arial','',9);
$pdf ->Cell(170,5,$row_Recordset1['Name_1'],'','','L');
$pdf->Ln(4);
$pdf->Cell(140,10,'');
$pdf->Ln(1);
$pdf->Cell(140,10,'');
$pdf->Ln(4);
$pdf->Cell(140,10,'');
$pdf->Ln(1);
$pdf->Cell(140,10,'');
$pdf->Ln(4);

$pdf->Ln(1);
$pdf->Cell(140,10,'The following documents have been attached:');
$pdf->Ln(4);
$pdf->Cell(140,10,'');
$pdf->Ln(4);

// End Header

// Declare Which Attachments Are Being Sent

	$hesid = $_GET['Id'];
	$documents = array();
	
	$query = mysqli_query($con, "SELECT tbl_hes_documents_relation.HESId, tbl_hes_documents_relation.DocumentId, tbl_hes_documents.Document FROM (tbl_hes_documents_relation LEFT JOIN tbl_hes_documents ON tbl_hes_documents.Id=tbl_hes_documents_relation.DocumentId) WHERE tbl_hes_documents_relation.HESId = '$hesid' AND tbl_hes_documents_relation.Active = '1'")or die(mysqli_error($con));
	while($row = mysqli_fetch_array($query)){
		
		array_push($documents, $row['Document']);

		
	}
	
		function attachments($documents){
			
			$count = count($documents);
			
			for($i=1;$i<=$count;$i++){
				
				$docs = $documents[$i];
				
				if($i == $count){
					
					$seperator = '';
				
				} else {
				
				$seperator = ', ';
				
				}
			
			$attachments .= $docs.$seperator;
			
			if($i == $count){
				
				return $attachments;
				
			}
			
			}
		}
		
		
		$pdf ->Cell(190,5,attachments($documents) ,'','','L','');
		$pdf->Ln(4);
	
$pdf->Ln(10);

// Risk Types

$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(226,226,226);

$pdf->Cell(60,5,'JSA Type','LRTB','','0','1','');
$pdf->Cell(60,5,'Yes','LRTB','','0','1','');
$pdf->Cell(60,5,'No','LRTB','','0','1','');

$pdf->Ln();

$pdf->SetFont('Arial','',9);

do {
		
	$pdf ->Cell(60,5,$row_Recordset2['Risk'],'LRTB','0','L','','');
	
	$riskid = $row_Recordset2['Id'];
	
	$query = mysqli_query($con, "SELECT * FROM tbl_hes_jsa_relation WHERE JSAId = '$riskid' AND JobNo = '$jobno'")or die(mysqli_error($con));
	$numrows = mysqli_num_rows($query);
	
	if($numrows == 1){
		
		$yes = 'x';
		$no = '';
		
	} else {
		
		$yes = '';
		$no = 'x';
		
	}
	
	$pdf ->Cell(60,5,' '.$yes,'LRTB','0','L','','');
	$pdf ->Cell(60,5,' '. $no,'LRTB','0','L','','');
	
	$pdf->Ln();
	
} while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2));	

$pdf->Ln(10);

// Risk Matrix

$pdf->Cell(60,5,'The risk matrix is as follows:','','','0','0','');
$pdf->Ln(8);

$pdf->Cell(60,5,'Scope of work','LRTB','','0','1','');
$pdf->Cell(120,5,$row_Recordset1['ScopeOfWork'],'LRTB','','0','0','');
$pdf->Ln();

$pdf->Cell(60,5,'Equipment to be used','LRTB','','0','1','');
$pdf->Cell(120,5,$row_Recordset1['Equipment'],'LRTB','','0','0','');
$pdf->Ln();

$pdf->Cell(60,5,'Risk Ranking of job','LRTB','','0','1','');
$pdf->Cell(120,5,$row_Recordset1['RiskRanking'],'LRTB','','0','0','');
$pdf->Ln();

$pdf->Cell(60,5,'Contractor doing job','LRTB','','0','1','');
$pdf->Cell(120,5,$row_Recordset1['Contractor'],'LRTB','','0','0','');
$pdf->Ln();

$pdf->Cell(60,5,'Contractor CHESM rating','LRTB','','0','1','');
$pdf->Cell(120,5,$row_Recordset1['ContractorRating'],'LRTB','','0','0','');
$pdf->Ln();

$pdf->Cell(60,5,'Permit Issuer','LRTB','','0','1','');
$pdf->Cell(120,5,$row_Recordset1['PermitIssuer'],'LRTB','','0','0','');
$pdf->Ln();

$pdf->Cell(60,5,'Safety Officer','LRTB','','0','1','');
$pdf->Cell(120,5,$row_Recordset1['SafetyOfficer'],'LRTB','','0','0','');
$pdf->Ln();

$pdf->Ln(11);
$pdf->SetDrawColor(255,255,255);

$pdf->SetWidths(array(180));
$pdf->Row(array('Nicky Jamun has been delegated approval authority as the Contractor Management Representative for Seavest by the Chevron Contract Owner as set forth in the CHESM. I have reviewed the HeS Job Safety Plan and JSAs submitted by the Seavest Team for the execution of High Risk work. I approve and confirm that it is satisfactory to allow the work to proceed and will ensure that the team maintain and uphold the standards set out by Chevron for the execution of High Risk at all times'));

$pdf->Ln(5);
$pdf->SetDrawColor(0,0,0);

$pdf->Cell(63,5,'Approved','','','0','0','');
$pdf->Ln(15);

	$a = $pdf->GetX();
	$b = $pdf->GetY();
	$pdf->Image('../images/signature.jpg',$a,($b - 10),'30','','JPG');

$pdf->Ln();
$pdf->Cell(60,5,'','B','','0','0','');
$pdf->Ln(8);
$pdf->Cell(60,5,'Nicky Jamun','','','0','0','');
$pdf->Ln();
$pdf->Cell(60,5,'Contract Management Representitive','','','0','0','');

if(isset($_GET['Preview'])){
	
	$pdf->Output();
	
} elseif(isset($_GET['New'])){
	
	$document = 'Seavest DOA #'.$id.'.pdf';
	
	$pdf->Output('pdf/'. $document);
	
	mysqli_query($con, "UPDATE tbl_hes_documents_relation SET PDF = '$document' WHERE HESId = '$id' AND DocumentId = '5'")or die(mysqli_error($con));

	header('Location: ../health-safety/hes/hes.php?Id='. $id .'&Proceed');
	
} elseif(isset($_GET['Close'])){
	
	$document = 'Seavest DOA #'.$id.'.pdf';
	
	$pdf->Output('pdf/'. $document);
	
	mysqli_query($con, "UPDATE tbl_hes_documents_relation SET PDF = '$document' WHERE HESId = '$id' AND DocumentId = '5'")or die(mysqli_error($con));

	header('Location: ../health-safety/hes/pending.php');
	
} else {
	
	$document = 'Seavest DOA #'.$id.'.pdf';
	
	$pdf->Output('pdf/'. $document);
	
	mysqli_query($con, "UPDATE tbl_hes_documents_relation SET PDF = '$document' WHERE HESId = '$id' AND DocumentId = '5'")or die(mysqli_error($con));

	header('Location: ../health-safety/hes/hes.php?Id='. $id);
}


mysqli_free_result($Recordset1);

mysqli_free_result($Recordset2);

mysqli_free_result($Recordset3);
?>

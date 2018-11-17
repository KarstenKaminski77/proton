<?php
require_once('../../functions/functions.php');

$_SESSION['insert'] = 0;
$_SESSION['update'] = 0;

$con = mysqli_connect('sql10.jnb1.host-h.net','proton_db','kwd001','proton_db');
		
$target_path = 'imports/' . basename( $_FILES['csv']['name']);
	
if(move_uploaded_file($_FILES['csv']['tmp_name'], $target_path)){

	require_once('../../PHPExcel/Classes/PHPExcel.php');
	
	$file = 'imports/import_'. date('Y-m-d_H-i-s');
 
	convertXLStoCSV('imports/'. $_FILES['csv']['name'],$file .'.csv');
	 
	$filename = $file .'.csv';
	$handle = fopen("$filename", "r");
	
	$_SESSION['insert'] = 0;
	$_SESSION['update'] = 0;
	
	while (($data = fgetcsv($handle, 0, ",")) !== FALSE){
		
		$company = $data[0];
		$industry = $data[1];
		
		$query_company = mysqli_query($con, "SELECT * FROM tbl_companies WHERE CompanyName = '$company'")or die(mysqli_error($con));
		$row_company = mysqli_fetch_array($query_company);
		
		$query_industry = mysqli_query($con, "SELECT * FROM tbl_industries WHERE Industry = '$industry'")or die(mysqli_error($con));
		$row_industry = mysqli_fetch_array($query_industry);
		
		$xl_data = array(
		
		  'CompanyId' => $row_company['Id'],
		  'IndustryId' => $row_industry['Id']
		);
	
		if(!empty($data[0])){
			
			$query_relation = mysqli_query($con, "SELECT * FROM tbl_company_industry_relation WHERE CompanyId = '". $row_company['Id'] ."' AND IndustryId = '". $row_industry['Id'] ."'")or die(mysqli_error($con));
			$row_relation = mysqli_fetch_array($query_relation);
			$numrows = mysqli_num_rows($query_relation);
				  
			if($numrows >= 1 && $data[0] != 'Company Name'){
				
				$u = $_SESSION['update'] + 1;
				$_SESSION['update'] = $u;
				
				dbUpdate('tbl_company_industry_relation', $xl_data, $where_clause="Id = '". $row_relation['Id'] ."'",$con);
			}
				  
			if($numrows == 0 && $data[0] != 'Company Name'){
				
				$i = $_SESSION['insert'] + 1;
				$_SESSION['insert'] = $i;
	
				dbInsert('tbl_company_industry_relation', $xl_data, $con);
			}
		}
	}

	fclose($handle);
}

header('Location: index.php?IndustrySuccess');
?>
<?php
require_once('../../functions/functions.php');

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
		
		$xl_data = array(
		
		  'CompanyName' => $data[0],
		  'Telephone' => $data[1],
		  'BuyerName' => $data[2],
		  'BuyerEmail' => $data[3],
		  'SalesName' => $data[4],
		  'SalesEmail' => $data[5],
		);
	
		if(!empty($data[0])){
			
			$company = $data[0];
			
			$query_client = mysqli_query($con, "SELECT * FROM tbl_companies WHERE CompanyName = '$company'")or die(mysqli_error($con));
			$row_client = mysqli_fetch_array($query_client);
			$numrows = mysqli_num_rows($query_client);
				  
			if($numrows >= 1 && $data[0] != 'Company Name'){
				
				$u = $_SESSION['update'] + 1;
				$_SESSION['update'] = $u;
				
				dbUpdate('tbl_companies', $xl_data, $where_clause="Id = '". $row_itemno['Id'] ."'",$con);
			}
				  
			if($numrows == 0 && $data[0] != 'Company Name'){
				
				$i = $_SESSION['insert'] + 1;
				$_SESSION['insert'] = $i;
	
				dbInsert('tbl_companies', $xl_data, $con);
			}
		}
	}

	fclose($handle);
}

header('Location: index.php?Success');
?>
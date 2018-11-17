<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

$id = $_GET['Id'];

mysqli_query($con, "UPDATE tbl_offers SET Closed = '1' WHERE RFQId = '$id'")or die(mysqli_error($con));

	$query = mysqli_query($con, "SELECT * FROM tbl_rfq_items WHERE Id = '$id'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$sourceid = $row['SourceId'];

	$query = "
	SELECT
		tbl_rfq.Id,
		tbl_offers.Closed
	FROM
		tbl_rfq
	INNER JOIN tbl_rfq_items ON tbl_rfq.Id = tbl_rfq_items.SourceId
	INNER JOIN tbl_offers ON tbl_rfq_items.Id = tbl_offers.RFQId
	WHERE
		tbl_rfq.Id = '$sourceid' AND tbl_offers.Closed = '0'";
		
	$query = mysqli_query($con, $query)or die(mysqli_query($con));
	$rows = mysqli_num_rows($query);
	
	if($rows == 0){
		
		mysqli_query($con, "UPDATE tbl_rfq SET Status = '3' WHERE Id = '$sourceid'")or die(mysqli_error($con));
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Proton Chem</title>
<link href="../css/layout.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="header">
  <div id="logo"></div>
</div>
<div id="container">
  <div align="center" class="welcome" id="right-container-closed"><br />
  Your RFQ has been successfully submitted.</div>
</div>
<div id="footer"></div>
</body>
</html>
<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

$id = $_GET['Id'];

mysqli_query($con, "UPDATE tbl_offers SET Interested = 'No', Closed = '1' WHERE RFQId = '$id'")or die(mysqli_error($con));
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
  You have been successfully removed from this offer.</div>
</div>
<div id="footer"></div>
</body>
</html>
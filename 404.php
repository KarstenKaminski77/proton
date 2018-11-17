<?php
// Connect To The Database
require_once('functions/db-connect.php');

require_once('functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Proton Chem</title>
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<link href="fonts/3543835926.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="sdmenu/blue/sdmenu.css" />
<script type="text/javascript" src="sdmenu/sdmenu.js"></script>
<script type="text/javascript">
<!--
var myMenu;
	window.onload = function() {
		myMenu = new SDMenu("my_menu");
		myMenu.init();
	};

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>

</head>

<body>
<div id="header">
  <div id="logo"> <a class="close" href="<?php echo $_SERVER['../../REQUEST_URI'] .'?Logout'; ?>"></a>
    <div id="tab-user">Last Login: <span class="font-normal"><?php echo date('d-m-Y H:i', strtotime($row_user['LastLogin'])); ?></span></div>
    <div id="tab-user"><?php echo $row_user['User']; ?></div>
  </div>
</div>
<div id="container">
  <?php include('menu.php'); ?>
  <div align="center" class="welcome" id="right-container"><br />
    <br />
  Page Not Found...</div>
</div>
<div id="footer">Proton Chemicals | Developed By <a href="http://www.kwd.co.za" class="footer-link">KWD</a></div>
</body>
</html>
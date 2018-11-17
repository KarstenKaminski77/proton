<?php
// Connect To The Database
require_once('../../functions/db-connect.php');

require_once('../../functions/functions.php');

restrict();

logout($con);

$id = $_GET['Edit'];

if(isset($_POST)){
	
	$email = $_POST['mail'];
	$pdf = $_POST['pdf'];
		
	if(isset($_POST['update'])){
		
		$id = $_GET['Edit'];
		
		mysqli_query($con, "UPDATE tbl_email_content SET Email = '$email', Pdf = '$pdf' WHERE Id = '$id'")or die(mysqli_error($con));
	}
}

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

$query_list = mysqli_query($con, "SELECT * FROM tbl_email_content ORDER BY Page ASC")or die(mysqli_error($con));
$numrows = mysqli_num_rows($query_list);

$query_form = mysqli_query($con, "SELECT * FROM tbl_email_content WHERE Id = '$id'")or die(mysqli_error($con));
$row_form = mysqli_fetch_array($query_form);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Proton Chem</title>
<link href="../../css/layout.css" rel="stylesheet" type="text/css" />
<link href="../../fonts/3543835926.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../../sdmenu/blue/sdmenu.css" />
<script type="text/javascript" src="../../sdmenu/sdmenu.js"></script>
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
  <div id="logo">
   <a class="close" href="<?php echo $_SERVER['../../REQUEST_URI'] .'?Logout'; ?>"></a>
   <div id="tab-user">Last Login: <span class="font-normal"><?php echo date('d-m-Y H:i', strtotime($row_user['LastLogin'])); ?></span></div>
   <div id="tab-user"><?php echo $row_user['User']; ?></div>
  </div>
</div>
<div id="container">
  <?php include('../../menu.php'); ?>
  <div id="right-container">
    <form id="form1" name="form1" method="post" action="">
      <div id="breadcrumbs">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td colspan="2" class="tab"><table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><a href="../../index.php" class="breadcumbs">Home</a></td>
                  <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td>Administration</td>
                  <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td><a href="index.php" class="breadcumbs">Email | Pdf Content</a></td>
                  <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
          </table>
      </div>
        <table border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="130" valign="top" class="td-left"><em><strong>Email</strong></em></td>
          <td width="757" class="td-right"><textarea name="mail" rows="5" class="tarea-100" id="mail"><?php default_value($_POST['mail'], $row_form['Email']); ?></textarea></td>
        </tr>
        <?php if($_GET['Edit'] == 1 || $_GET['Edit'] == 2 || $_GET['Edit'] == 7){ ?>
        <tr>
          <td valign="top" class="td-left"><em>Pdf</em></td>
          <td class="td-right"><textarea name="pdf" rows="5" class="tarea-100" id="pdf"><?php default_value($_POST['pdf'], $row_form['Pdf']); ?></textarea></td>
        </tr>
        <?php } ?>
        <tr>
          <td colspan="2" align="right"><?php if(isset($_GET['Edit'])){ ?>
            <input name="update" type="submit" class="btn" id="update" value="Update" />
            <?php } else { ?>
            <input name="insert" type="submit" class="btn" id="insert" value="Insert" />
            <?php } ?></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <?php if($numrows >= 1){ ?>
        <tr>
          <td colspan="2"><div id="list-border">
            <table width="100%" border="0" cellpadding="4" cellspacing="1">
              <tr>
                <td class="td-header">Name</td>
                <td class="td-header">&nbsp;</td>
                <td width="20" class="td-header">&nbsp;</td>
                </tr>
              <?php while($row_list = mysqli_fetch_array($query_list)){ ?>
              <tr class="<?php echo ($ac_sw1++%2==0)?"even":"odd"; ?>" onmouseover="this.oldClassName = this.className; this.className='over';" onmouseout="this.className = this.oldClassName;">
                <td><?php echo $row_list['Page']; ?></td>
                <?php 
		  
		  if(isset($_GET['Page']) || isset($_GET['Edit'])){
			  
			  $var = '&';
			  
		  } else {
			  
			  $var = '?';
		  }
		  ?>
                <td width="20"><a href="index.php?Delete=<?php echo $row_list['Id']; ?>" class="delete"></a></td>
                <td width="20"><a href="index.php?Edit=<?php echo $row_list['Id']; ?>" class="edit"></a></td>
                </tr>
              <?php } ?>
              </table>
          </div>
              <table border="0" align="center" cellpadding="0" cellspacing="1">
              <tr>
                <td colspan="3" align="center"></td>
              </tr>
            </table>
          </td>
        </tr>
        <?php } ?>
        <tr>
          <td colspan="2"><p>&nbsp;</p></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>
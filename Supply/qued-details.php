<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

if(!isset($_POST['notes'])){
	
	unset($_SESSION['notes']);
}

if(isset($_POST['rfqid'])){
	
	for($i=0;$i<count($_POST['rfqid']);$i++){
		
		$id = $_POST['rfqid'][$i];
		
		$old_notes = $_SESSION['notes'][$i];
		
		if($_POST['notes'][$i] != trim($_SESSION['notes'][$i])){
			
			$pieces = explode($old_notes,$_POST['notes'][$i]);
			
			$key = count($pieces)-1;
					 
			$notes =  trim($old_notes .' '. date('Y-m-d') .': '. $pieces[1] .' ');
			
			$var = ", Notes = '$notes'";
		}
			
		$date = $_POST['date'][$i];
		
		mysqli_query($con, "UPDATE tbl_offers SET FollowUpDate = '$date' $var WHERE RFQId = '$id'")or die(mysqli_error($con));
	}
}

if(isset($_GET['Approve'])){
	
	$id = $_GET['Approve'];
	
	mysqli_query($con, "UPDATE tbl_offers SET Interested = 'Yes' WHERE RFQId = '$id'")or die(mysqli_error($con));
}

if(isset($_GET['Reject'])){
	
	$id = $_GET['Reject'];
	
	mysqli_query($con, "UPDATE tbl_offers SET Interested = 'No' WHERE RFQId = '$id'")or die(mysqli_error($con));
}

if(isset($_GET['Closed'])){
	
	$id = $_GET['Closed'];
	$rfqid = $_GET['Id'];
	
	mysqli_query($con, "UPDATE tbl_offers SET Closed = '1' WHERE RFQId = '$id'")or die(mysqli_error($con));
	
	$query = "
	SELECT
		tbl_rfq.Id,
		tbl_offers.Closed
	FROM
		tbl_rfq
	INNER JOIN tbl_rfq_items ON tbl_rfq.Id = tbl_rfq_items.SourceId
	INNER JOIN tbl_offers ON tbl_rfq_items.Id = tbl_offers.RFQId
	WHERE
		tbl_rfq.Id = '$rfqid' AND tbl_offers.Closed = '0'";
		
	$query = mysqli_query($con, $query)or die(mysqli_query($con));
	$rows = mysqli_num_rows($query);
	
	if($rows == 0){
		
		mysqli_query($con, "UPDATE tbl_rfq SET Status = '3' WHERE Id = '$rfqid'")or die(mysqli_error($con));
	}
	
	header('Location: qued-details.php');
}

$rfqid = $_GET['Id'];

// Suppliers Query
$query_qued = "
SELECT
	tbl_rfq_items.SourceId,
	tbl_rfq_items.SupplierId,
	tbl_rfq_items.Id,
	tbl_products.`Name`,
	tbl_rfq.Date,
	tbl_companies.BuyerName,
	tbl_companies.CompanyName,
	tbl_companies.Telephone,
	tbl_companies.Mobile,
	tbl_companies.BuyerEmail,
	tbl_offers.RFQId,
	tbl_offers.Notes,
	tbl_offers.Interested,
	tbl_offers.FollowUpDate,
	tbl_offers.Sent
FROM
	tbl_rfq_items
INNER JOIN tbl_rfq ON tbl_rfq_items.SourceId = tbl_rfq.Id
INNER JOIN tbl_products ON tbl_rfq_items.ProductId = tbl_products.Id
INNER JOIN tbl_companies ON tbl_rfq_items.SupplierId = tbl_companies.Id
INNER JOIN tbl_offers ON tbl_rfq_items.Id = tbl_offers.RFQId
WHERE
	tbl_rfq.Type = '2' AND tbl_rfq.`Status` = '1' AND tbl_offers.Closed = '0' AND tbl_rfq.Id = '$rfqid'
ORDER BY
	tbl_rfq.Date DESC";
	
$query_qued = mysqli_query($con, $query_qued)or die(mysqli_error($con));
$numrows = mysqli_num_rows($query_qued);

if($numrows == 0){
	
	header('Location: qued.php');
	
	exit();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Proton Chem</title>
<link href="../css/layout.css" rel="stylesheet" type="text/css" />
<link href="../fonts/3543835926.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../sdmenu/blue/sdmenu.css" />
<script type="text/javascript" src="../sdmenu/sdmenu.js"></script>
<script type="text/javascript">
<!--
//-->
</script>

<script type="text/javascript" src="../scripts/custom-form-elements.js"></script>

<link href="../css/form-elements.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" media="all" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" media="all" type="text/css" href="jquery-ui-timepicker-addon.css" />
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="jquery-ui-sliderAccess.js"></script>

<link href="date.css" rel="stylesheet" type="text/css" />

</head>

<body>
<div id="header">
  <div id="logo">
   <a class="close" href="<?php echo $_SERVER['../REQUEST_URI'] .'?Logout'; ?>"></a>
   <div id="tab-user">Last Login: <span class="font-normal"><?php echo date('d-m-Y H:i', strtotime($row_user['LastLogin'])); ?></span></div>
   <div id="tab-user"><?php echo $row_user['User']; ?></div>
  </div>
</div>
<div id="container">
  <?php include('../menu.php'); ?>
  <div id="right-container">
    <form id="form1" name="form1" method="post" action="">
      <div id="breadcrumbs">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td colspan="2" class="tab"><table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><a href="../index.php" class="breadcumbs">Home</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td>Supply Products</td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td><a href="../Admin/Industries/index.php" class="breadcumbs">Qued</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
        </table>
      </div>
      <?php if(isset($_GET['Success'])){ ?>
      <div id="banner-success">Sales Offer Successfully Sent.....</div>
      <?php } ?>
        <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td class="td-header-blue-dark">Company Name</td>
          <td width="172" class="td-header-blue-dark">Contact Name</td>
          <td width="150" class="td-header-blue-dark">Telephone</td>
          <td width="82" class="td-header-blue-dark">Date</td>
          <td width="82" class="td-header-blue-dark">Status</td>
          <td width="22" align="center" class="td-header-blue-dark">&nbsp;</td>
          <td width="22" align="center" class="td-header-blue-dark">&nbsp;</td>
          <td width="22" align="center" class="td-header-blue-dark">&nbsp;</td>
          <td width="22" align="center" class="td-header-blue-dark">&nbsp;</td>
          </tr>
        <?php 
		$notes = array();
		$i = 0;
		while($row_qued = mysqli_fetch_array($query_qued)){ 
		
		if(empty($row_qued['Interested'])){
			
			$status = 'Pending';
			
		} else {
			
			if($row_qued['Interested'] == 'Yes'){
				
				$status = 'Interested';
				
			} else {
				
				$status = 'Not Interested';
			}
		}
		
		$class = '';
		
		if($row_qued['FollowUpDate'] <= date('Y-m-d')){
			
			$class = 'style="color:#FF0000"';
		}
		
		$tomorrow = strtotime(date('Y-m-d')) + 86400;
		
		if($row_qued['FollowUpDate'] == date('Y-m-d', $tomorrow)){
			
			$class = 'style="color:#FF6600"';
		}
		
		$title = 'Send Offer & Data Sheet &#013;'. $row_qued['Sent'] .' sent.';
		$i++;
		?>
        <tr>
          <td class="td-header-blue" <?php echo $class; ?>><?php echo $row_qued['CompanyName']; ?></td>
          <td class="td-header-blue" <?php echo $class; ?>><?php echo $row_qued['BuyerName']; ?></td>
          <td class="td-header-blue" <?php echo $class; ?>><?php echo $row_qued['Telephone']; ?></td>
          <td class="td-header-blue" <?php echo $class; ?>><?php echo $row_qued['Date']; ?></td>
          <td class="td-header-blue" <?php echo $class; ?>><?php echo $status; ?></td>
          <td align="center" class="td-header-blue <?php echo $class; ?>" title="Not Interested"><?php qued_notinterested($row_qued['Interested'], $row_qued['Id'], $_GET['Id']); ?></td>
          <td align="center" class="td-header-blue <?php echo $class; ?>" title="Interested"><?php qued_interested($row_qued['Interested'], $row_qued['Id'], $_GET['Id']); ?></td>
          <td align="center" class="td-header-blue <?php echo $class; ?>" title="<?php echo $title; ?>"><?php qued_mail($row_qued['Interested'], $row_qued['SourceId'], $row_qued['SupplierId'], $row_qued['Id'], $_GET['Id']); ?></td>
          <script>
		  function myFunction<?php echo $i; ?>() {
			  var x;
			  if (confirm("Close Off Enquiry") == true) {
				  window.location = "qued-details.php?Id=<?php echo $_GET['Id']; ?>&Closed=<?php echo $row_qued['Id']; ?>";
			  } else {
				  window.location = "qued-details.php?Id=<?php echo $_GET['Id']; ?>";
			  }
		  }
          </script>
          <td width="22" align="center" class="td-header-blue" title="Close Off Enquiry"><?php qued_close($row_qued['Interested'], $row_qued['Id'],$i); ?></td>
          </tr>
        <tr>
          <td colspan="3" class="td-grey">
          <?php 
		  if(empty($row_qued['Notes'])){
			  
			  $value = 'Notes';
			  
		  } else {
			  
			  $value = stripslashes($row_qued['Notes']);
		  }
		  
		  array_push($notes, $row_qued['Notes']);
		  ?>
          <textarea name="notes[]" cols="45" rows="3" class="tarea-100" id="notes[]" onfocus="if(this.value=='Notes'){this.value=''}" onblur="if(this.value==''){this.value='Notes'}" <?php echo $class; ?>><?php echo $value; ?></textarea></td>
          <td valign="top" class="td-grey">
          <input name="date[]" type="text" class="tarea-100" id="date-<?php echo $i; ?>" value="<?php echo $row_qued['FollowUpDate']; ?>" title="Follow Up Date" <?php echo $class; ?> />
          <script type="text/javascript">
		    $('#date-<?php echo $i; ?>').datepicker({
			  dateFormat: "yy-mm-dd"
			});
          </script>
          </td>
          <td valign="top" class="td-grey">&nbsp;</td>
          <td align="center" class="td-grey" title="View"><input name="productid" type="hidden" id="productid" value="<?php echo $row_qued['Name']; ?>" />            <input name="rfqid[]" type="hidden" id="rfqid[]" value="<?php echo $row_qued['Id']; ?>" /></td>
          <td align="center" class="td-grey" title="View">&nbsp;</td>
          <td align="center" valign="top" class="td-grey" title="View">&nbsp;</td>
          <td align="center" class="td-grey" title="View">&nbsp;</td>
        </tr>
        <?php 
		} 
		
		$_SESSION['notes'] = $notes;
		?>
        <tr>
          <td colspan="10" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="10" align="right"><input name="button" type="submit" class="btn" id="button" value="Save" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>
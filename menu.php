<?php
// Connect To The Database
require_once('functions/db-connect.php');

require_once('functions/functions.php');
?>

<div style="float: left" id="my_menu" class="sdmenu">

<?php
$i = 0;
$query_cat = mysqli_query($con, "SELECT * FROM tbl_menu_cat ORDER BY OrderBy ASC")or die(mysqli_error($con));
while($row_cat = mysqli_fetch_array($query_cat)){
?>

  <div>
    <span><?php echo $row_cat['Category']; ?></span>
    
    <?php
	$catid = $row_cat['Id'];
	
	$query_sub = mysqli_query($con, "SELECT * FROM tbl_menu_sub_categories WHERE CategoryId = '$catid' ORDER BY OrderBy ASC")or die(mysqli_error($con));
	while($row_sub = mysqli_fetch_array($query_sub)){ 
	?>
    
    <a href="<?php echo $row_sub['URL']; ?>"><?php echo $row_sub['SubCategory']; ?> <?php counter($con, $row_sub['Tbl'], $row_sub['Status'], $row_sub['Type'], $row_sub['Id'],$row_sub['GroupBy']); ?></a>
    
    <?php } ?>
    
  </div>
  
  <?php } ?>
  <div class="collapsed">
   <span>
	<a class="logout" href="logout.php">Logout</a>
	</span>
   </div>

</div>
<?php
require_once('functions/functions.php');

function clear_db($con, $table){
	
	mysqli_query($con, "DELETE FROM $table")or die(mysqli_error($con));
}

$tbl = array('tbl_inv','tbl_inv_items','tbl_notes','tbl_po','tbl_po_items','tbl_proforma','tbl_proforma_items','tbl_qs','tbl_qs_items','tbl_rfq','tbl_rfq_items','tbl_transport','tbl_offers');

for($i=0;$i<count($tbl);$i++){
	
	$table = $tbl[$i];
	
	clear_db($con, $table);
}

mysqli_close($con);
?>
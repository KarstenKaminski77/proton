<?php
session_start();

require_once('db-connect.php');

function dbInsert($table_name, $form_data,$con){
	
	// retrieve the keys of the array (column titles)
	$fields = array_keys($form_data);

	// build the query
	mysqli_query($con, "INSERT INTO ".$table_name."
	(".implode(',', $fields).") 
	VALUES ('".implode("','", $form_data)."')")or die(mysqli_error($con));

}

function dbUpdate($table_name, $form_data, $where_clause='',$con)
{
    // check for optional where clause
    $whereSQL = '';
    if(!empty($where_clause))
    {
        // check to see if the 'where' keyword exists
        if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
        {
            // not found, add key word
            $whereSQL = " WHERE ".$where_clause;
        } else
        {
            $whereSQL = " ".trim($where_clause);
        }
    }
    // start the actual SQL statement
    $sql = "UPDATE ".$table_name." SET ";

    // loop and build the column /
    $sets = array();
    foreach($form_data as $column => $value)
    {
         if(!empty($value) || $value == 0){
			 
			 $sets[] = "`".$column."` = '".$value."'";
		 }
    }
    $sql .= implode(', ', $sets);

    // append the where statement
    $sql .= $whereSQL;

    // run and return the query result
	//echo $sql .'<br>';
    return mysqli_query($con, $sql)or die(mysqli_error($con));
}

function dbDelete($table_name, $where_clause='',$con)
{
    // check for optional where clause
    $whereSQL = '';
    if(!empty($where_clause))
    {
        // check to see if the 'where' keyword exists
        if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
        {
            // not found, add keyword
            $whereSQL = " WHERE ".$where_clause;
        } else
        {
            $whereSQL = " ".trim($where_clause);
        }
    }
    // build the query
    $sql = "DELETE FROM ".$table_name.$whereSQL;

    // run and return the query result resource
    return mysqli_query($con, $sql)or die(mysqli_error($con));
	
}

function login($con){
	
	if(isset($_POST['login'])){
		
		$username = mysqli_real_escape_string($con, $_POST['username']);
		$password = mysqli_real_escape_string($con, md5($_POST['password']));
		
		$query = mysqli_query($con, "SELECT * FROM tbl_users WHERE Username = '$username' AND Password = '$password'")or die(mysqli_error($con));
		$row = mysqli_fetch_array($query);
		$numrows = mysqli_num_rows($query);
		
		if($numrows == 1){ 
			
			// Set User Login Cookies
			setcookie("userid", $row['Id'], 60 * 60 * 24 * 365 + time(), '/', '.kwd.co.za');
			setcookie("username", $row['User'], 60 * 60 * 24 * 365 + time(), '/', '.kwd.co.za');
			setcookie("lastlogin", $row['LastLogin'], 60 * 60 * 24 * 365 + time(), '/', '.kwd.co.za');
			
			header('Location: http://www.kwd.co.za/proton/index.php');
			
			exit();
			
		} else {
			
			// Trim the URL for any unwanted parameters
			$url = explode('?', $_SERVER['HTTP_REFERER']);
			
			// Trigger the error message
			header('Location: '. $url[0] .'?Error');
			
			exit();
		}
	}
}

function logout($con){
	
	if(isset($_GET['Logout'])){
		
		$time = date('Y-m-d H:i');
		$id = $_COOKIE['userid'];
		
		mysqli_query($con, "UPDATE tbl_users SET LastLogin = '$time' WHERE Id = '$id'")or die(mysqli_error($con));
		
		setcookie("userid", '0', time() - 3600, '/', '.kwd.co.za');
		setcookie("username", '0', time() - 3600, '/', '.kwd.co.za');
		setcookie("lastlogin", '0', time() - 3600, '/', '.kwd.co.za');
		
		header('Location: http://www.kwd.co.za/proton/login.php?');
	}
}

function restrict(){
	
	if(!isset($_COOKIE['userid'])){
		
		header('Location: http://www.kwd.co.za/proton/login.php');
	}
}

function default_value($post,$query){
	
	if(isset($_GET['Edit'])){
		
		echo $query;
		
	} else {
		
		echo $post;
	}
}

function offset($total_items, $per_page){
	
	$_SESSION['pages'] = ceil($total_items / $per_page);
	
	if(isset($_GET['Page'])){
		
		$_SESSION['offset'] = $_GET['Page'] * $per_page;
		
	} else {
		
		$_SESSION['offset'] = '0';
		
	}
}

function pager($pages){
	
	if($pages > 1){
	
	echo '<table border="0" align="center" cellpadding="0" cellspacing="0">
           <tr>
            <td>';
			
	// First Page
	if($_GET['Page'] != 0){
		
		echo '<a href="'. $url[0] .'?Page=0" class="pager-text"'. $style .'><< First</a>';
		
	} else {
		
		echo '<span class="pagerinactive"><<  First</span>';
		
	}
	
	// Previous Link
	if(isset($_GET['Page']) && $_GET['Page'] >= 1){
		
		$previous = $_GET['Page'] - 1;
		$url = explode("?Page", $_SERVER['REQUEST_URI']);
		
		echo '<a href="'. $url[0] .'?Page='. $previous .'" class="pager-text"><  Previous</a>';
		
	} else {
		
		echo '<span class="pagerinactive"><  Previous</span>';
		
	}
		
	// Middle Page Numbers
	for($i=1;$i<$pages;$i++){
		
		$c = $i - 1;
		$url = explode("?Page", $_SERVER['REQUEST_URI']);
		
		if(($i - 1) == $_GET['Page']){
			
			$style = ' style="font-weight: bold; background-color: #d7effc; color:#0183BA"';
			
		}
		
		if(isset($_GET['Page'])){
			
			if($i >= ($_GET['Page'] - 2) && $i <= ($_GET['Page'] + 2)){
				
				echo '<a href="'. $url[0] .'?Page='. $c .'" class="pager"'. $style .'>'. $i .'</a>';
				
			}
			
		} else {
			
			if($i <= 5){
				
				echo '<a href="'. $url[0] .'?Page='. $c .'" class="pager"'. $style .'>'. $i .'</a>';
		
			}
		}
			
		$style = '';
	}
	
	// Last Page
	if($_GET['Page'] != ($pages - 1)){
		
		echo '<a href="'. $url[0] .'?Page='. ($pages - 1) .'" class="pager"'. $style .'>'. $pages  .'</a>';
		
	}
						
	// Next Link
	if(!isset($_GET['Page']) || $_GET['Page'] < ($pages - 1)){
		
		$next = $_GET['Page'] + 1;
		$url = explode("?Page", $_SERVER['REQUEST_URI']);
		
		echo '<a href="'. $url[0] .'?Page='. $next .'" class="pager-text">Next  ></a>';
		
	} else {
		
		echo '<span class="pagerinactive">Next ></span>';
		
	}
						
	// Last Page
	if($_GET['Page'] != ($pages - 1)){
		
		$url = explode("?Page", $_SERVER['REQUEST_URI']);
		
		echo '<a href="'. $url[0] .'?Page='. ($pages - 1) .'" class="pager-text">Last  >></a>';
		
	} else {
		
		echo '<span class="pagerinactive">Last  >></span>';
		
	}
	
	echo '</td>
        </tr>
  </table>';
	
	}
}

function dd_list($light,$dark,$font,$i){
	
	if($i % 2){
		
		echo 'style="background-color:'. $light .'; color:'. $font .'"';
		
	} else {
		
		echo 'style="background-color:'. $dark .'; color:'. $font .'"';
	}
}

function edit($status, $sourcingid, $companyid){
	
	if($status == 1){
		
		echo '<a href="rfq.php?Id='. $sourcingid .'&Company='. $companyid .'" class="search"></a>';
	}
	
	if($status == 2){
		
		echo '<a href="awaiting-quotations.php?Id='. $sourcingid .'&Company='. $companyid .'" class="search"></a>';
	}
}

function sourcing_subtotal($con, $sourceid, $supplierid){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_rfq_items WHERE SourceId = '$sourceid' AND SupplierId = '$supplierid' And Approved = '1'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	echo number_format($row['Total'],2);
}

function sourcing_vat($con, $sourceid, $supplierid){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_rfq_items WHERE SourceId = '$sourceid' AND SupplierId = '$supplierid' And Approved = '1'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	echo number_format(($row['Total'] * 0.14),2);
}

function sourcing_total($con, $sourceid, $supplierid){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_rfq_items WHERE SourceId = '$sourceid' AND SupplierId = '$supplierid' And Approved = '1'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	echo number_format(($row['Total'] * 0.14) + $row['Total'],2);
}

function sourcing_pdf_subtotal($con, $sourceid, $supplierid){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_rfq_items WHERE SourceId = '$sourceid' AND SupplierId = '$supplierid' And Approved = '1'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$_SESSION['subtotal'] = $row['Total'];
}

function sourcing_pdf_vat($con, $sourceid, $supplierid){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_rfq_items WHERE SourceId = '$sourceid' AND SupplierId = '$supplierid' And Approved = '1'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$_SESSION['vat'] = $row['Total'] * 0.14;
}

function sourcing_pdf_total($con, $sourceid, $supplierid){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_rfq_items WHERE SourceId = '$sourceid' AND SupplierId = '$supplierid' And Approved = '1'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$_SESSION['total'] = ($row['Total'] * 0.14) + $row['Total'];
}

function qs_subtotal($con, $quoteno){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_qs_items WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	echo number_format($row['Total'],2);
}

function qs_vat($con, $quoteno, $transport){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_qs_items WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	echo number_format(($row['Total'] * 0.14),2);
}

function qs_total($con, $quoteno, $transport){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_qs_items WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	echo number_format(($row['Total'] * 0.14) + $row['Total'],2);
}

function qs_pdf_subtotal($con, $quoteno){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_qs_items WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$_SESSION['subtotal'] = number_format($row['Total'],2);
}

function qs_pdf_vat($con, $quoteno, $transport){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total_1 FROM tbl_qs_items WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$_SESSION['vat'] = number_format(($row['Total_1'] * 0.14),2);
}

function qs_pdf_total($con, $quoteno, $transport){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_qs_items WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$_SESSION['total'] = number_format(($row['Total'] * 0.14) + $row['Total'],2);
}

function inv_pdf_subtotal($con, $invoiceno){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_inv_items WHERE InvoiceNo = '$invoiceno'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$_SESSION['subtotal'] = number_format($row['Total'],2);
}

function inv_pdf_vat($con, $invoiceno, $transport){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_inv_items WHERE InvoiceNo = '$invoiceno'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$_SESSION['vat'] = number_format(($row['Total'] * 0.14),2);
}

function inv_pdf_total($con, $invoiceno, $transport){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_inv_items WHERE InvoiceNo = '$invoiceno'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$_SESSION['total'] = number_format(($row['Total'] * 0.14) + $row['Total'],2);
}

function proforma_pdf_subtotal($con, $proformano){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_proforma_items WHERE ProformaNo = '$proformano'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$_SESSION['subtotal'] = number_format($row['Total'],2);
}

function proforma_pdf_vat($con, $proformano, $transport){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_proforma_items WHERE ProformaNo = '$proformano'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$_SESSION['vat'] = number_format(($row['Total'] * 0.14),2);
}

function proforma_pdf_total($con, $proformano, $transport){
	
	$query = mysqli_query($con, "SELECT SUM(Total) AS Total FROM tbl_proforma_items WHERE ProformaNo = '$proformano'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$_SESSION['total'] = number_format(($row['Total'] * 0.14) + $row['Total'],2);
}

function source_Reccomended($con, $sourceid){
	
	mysqli_query($con, "UPDATE tbl_rfq_items SET Reccomended = '0' WHERE SourceId = '$sourceid'")or die(mysqli_error($con));
	
	$query = mysqli_query($con, "SELECT * FROM tbl_rfq_items WHERE SourceId = '$sourceid' GROUP BY ProductId")or die(mysqli_error($con));
	while($row = mysqli_fetch_array($query)){
		
		$item = $row['ProductId'];
		
		$query2 = mysqli_query($con, "SELECT * FROM tbl_rfq_items WHERE ProductId = '$item' AND SourceId = '$sourceid' AND RFQ = '1' ORDER BY Price ASC LIMIT 1")or die(mysqli_error($con));
		$row2 = mysqli_fetch_array($query2);
		
		$price = $row2['Price'];
		
		if($price >= '0.01'){
			
			$itemid = $row2['Id'];
			
			mysqli_query($con, "UPDATE tbl_rfq_items SET Reccomended  = '1' WHERE Id = '$itemid'")or die(mysqli_error($con));
		}
	}
}

function approve($con,$quoteno){

$query = "
SELECT
	tbl_companies.Account,
	tbl_qs.Id
FROM
	tbl_companies
INNER JOIN tbl_qs ON tbl_qs.CompanyId = tbl_companies.Id
WHERE
	tbl_qs.Id = '$quoteno'";
	
	$query = mysqli_query($con, $query)or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	if($row['Account'] == '1'){
		
		echo 'po-no.php?Id='. $row['Id'];
		
	} else {
		
		$query_proforma = mysqli_query($con, "SELECT * FROM tbl_proforma WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
		$row_proforma = mysqli_fetch_array($query_proforma);
		
		if($row_proforma['Status'] == 2){
			
			echo 'po-no.php?Id='. $row['Id'];
			
		} else {
			
			echo 'po-no-proforma.php?Id='. $row['Id'];
		}
	}
}

function counter($con, $table, $status, $category,$id,$group){
	
	if(!empty($table)){
		
		if($id == 24){
			
			$clause = "AND Offer = '1'";
		}
		
		if(!empty($group)){
			
			$group = " GROUP BY ". $group;
		}
		
		$query_counter = mysqli_query($con, "SELECT * FROM $table WHERE Status = '$status' AND Type = '$category' $clause $group")or die(mysqli_error($con));
		$rows_counter = mysqli_num_rows($query_counter);
		
		if($rows_counter >= 1){
			
			echo '('. $rows_counter .')';
		}
	}
}

function qued_notinterested($interested, $id, $rfqid){
	
	if($interested == 'Yes'){
		
		echo '<a href="qued-details.php?Reject='. $id .'&Id='. $rfqid .'" class="reject-g"></a>';
		
	} elseif($interested == 'No'){
		
		echo '<a href="qued-details.php?Reject='. $id .'&Id='. $rfqid .'" class="reject-disabled"></a>';
		
	} else {
		
		echo '<a href="qued-details.php?Reject='. $id .'&Id='. $rfqid .'" class="reject"></a>';
	
	}
}

function qued_interested($interested, $id, $rfqid){
	
	if($interested == 'Yes'){
		
		echo '<a href="qued-details.php?Approve='. $id .'&Id='. $rfqid .'" class="approve-g"></a>';
		
	} elseif($interested == 'No'){
		
		echo '<a href="qued-details.php?Approve='. $id .'&Id='. $rfqid .'" class="approve-disabled"></a>';
		
	} else {
		
		echo '<a href="qued-details.php?Approve='. $id .'&Id='. $rfqid .'" class="approve"></a>';
	}
}

function qued_mail($interested, $id, $customer, $rfq_item, $rfqid){
	
	if($interested == 'Yes'){
		
		echo '<a href="../fpdf/pdf-offer.php?Id='. $id .'&Customer='. $customer .'&Item='. $rfq_item .'" class="mail-g"></a>';
		
	} elseif($interested == 'No'){
		
		echo '<a class="mail-disabled"></a>';
		
	} else {
		
		echo '<a href="../fpdf/pdf-offer.php?Id='. $id .'&Customer='. $customer .'&Item='. $rfq_item .'" class="mail"></a>';
	}
}

function qued_close($interested, $id,$i){
	
	if($interested == 'Yes'){
		
		echo '<inout type="submit" class="power-g" onclick="myFunction'.$i.'()">';
		
	} elseif($interested == 'No'){
		
		echo '<inout type="submit" class="power-disabled" onclick="myFunction'.$i.'()">';
		
	} else {
		
		echo '<inout type="submit" class="power" onclick="myFunction'.$i.'()">';
	}
}

function pdf_content($con,$id){
	
	$query_content = mysqli_query($con, "SELECT * FROM tbl_email_content WHERE Id = '$id'")or die(mysqli_error($con));
	$row_content = mysqli_fetch_array($query_content);
	
	$_SESSION['content'] = $row_content['Pdf'];
}

function email_content($con,$id){
	
	$query_content = mysqli_query($con, "SELECT * FROM tbl_email_content WHERE Id = '$id'")or die(mysqli_error($con));
	$row_content = mysqli_fetch_array($query_content);
	
	$_SESSION['content'] = $row_content['Email'];
}

function word_limit($string, $limit){
	
	$dots = '...';
	
	$string = strip_tags($string);
	
	if (strlen($string) > $limit) {
		
		// truncate string
		$stringCut = substr($string, 0, $limit).$dots;
		
	} else {
		
		$stringCut = $string;
	}
		
		return $stringCut;		
}

function convertXLStoCSV($infile,$outfile){
	
	$fileType = PHPExcel_IOFactory::identify($infile);
	$objReader = PHPExcel_IOFactory::createReader($fileType);
 
	$objReader->setReadDataOnly(true);   
	$objPHPExcel = $objReader->load($infile);    
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
	$objWriter->save($outfile);
}

?>
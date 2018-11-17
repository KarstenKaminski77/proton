<?php
$result = mysql_query("SHOW processlist");
while ($myrow = mysql_fetch_assoc($result)) {
	if ($myrow['Command'] == "Sleep") {
		mysql_query("KILL {$myrow['Id']}");
	}
}
?>
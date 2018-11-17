<?php
mysql_connect('sql10.jnb1.host-h.net', 'proton_db', 'kwd001');
$res = mysql_query("SHOW FULL PROCESSLIST");
while ($row=mysql_fetch_array($res)) {
  $pid=$row["Id"];
  if ($row['Command']=='Sleep') {
      if ($row["Time"] > 3 ) { //any sleeping process more than 3 secs
         $sql="KILL $pid";
         echo "\n$sql"; //added for log file
         mysql_query($sql);
      }
  }
}
?>
<?php

$con = mysqli_connect('sql10.jnb1.host-h.net','proton_db','kwd001','proton_db');

$result = mysqli_query($con, "SHOW FULL PROCESSLIST");
while ($row=mysqli_fetch_array($result)) {
  $process_id=$row["Id"];
  if ($row["Time"] > 0 ) {
    $sql="KILL $process_id";
    mysqli_query($sql);
  }
}

$tomorrow = strtotime(date('Y-m-d')) + 86400;

echo date('Y-m-d', $tomorrow);

mysqli_close($con);
?>
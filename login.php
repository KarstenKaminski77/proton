<?php
// Connect To The Database
require_once('functions/db-connect.php');

require_once('functions/functions.php');

login($con);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link href="css/layout.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="header">
  <div id="logo"></div>
</div>
<div id="container">
  <form id="form1" name="form1" method="post" action="">
    <table border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td><?php if(isset($_GET['Error'])){ ?>
          <div id="banner-error">The username or password you entered is incorrect. Please try logging in again.</div>
          <?php } else { ?>
          <div id="banner-info">Please enter your username and password to login and access your account.</div>
          <?php } ?></td>
      </tr>
    </table>
    <table border="0" align="center" cellpadding="2" cellspacing="3">
      <tr>
        <td><input name="username" type="text" class="tarea" id="username" onfocus="if(this.value=='Username'){this.value=''}" onblur="if(this.value==''){this.value='Username'}" value="Username" /></td>
      </tr>
      <tr>
        <td><input name="password" type="text" class="tarea" id="password" onfocus="if(this.value=='Password'){this.value=''}" onblur="if(this.value==''){this.value='Password'}" value="Password" /></td>
      </tr>
      <tr>
        <td align="right"><input name="login" type="submit" class="btn" id="login" value="Login" /></td>
      </tr>
      <tr>
        <td align="center" class="red">&nbsp;</td>
      </tr>
      <tr>
        <td align="center"><a href="forgot.php" class="forgot">Forgot Password</a></td>
      </tr>
    </table>
  </form>
  
<div id="footer"></div>
</div>
</body>
</html>
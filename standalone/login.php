<?php

require_once 'auth_common.php';  
require_once "CRM/Core/BAO/UFMatch.php";

if ( CRM_Core_BAO_UFMatch::isEmptyTable( ) ) {
    header("Location: new_install.php");
    exit(0);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
  <title>CiviCRM User Authentication</title>
  <link rel="stylesheet" type="text/css" href="../install/template.css" />
</head>
<body>
<div id="All">
   <h1 class="title">CiviCRM Login</h1>
   <h3>Please enter your OpenID</h3>

   <div id="verify-form">
   <form method="get" action="try_auth.php">
        Identity&nbsp;URL:
        <input type="hidden" name="action" value="verify" />
        <input id="openid_identifier" type="text" name="openid_identifier" value="" />
        <input type="submit" value="Verify" />
   </form>
   </div>
   <p>If you don't have an OpenID yet, go to <a href="http://www.myopenid.com/">MyOpenID to get one</a>.</p>
</div>
</body>
</html>
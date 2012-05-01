<?php 
session_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="English" />
<title>WIFI Location Management</title>
</head>
<body>
账户名称：<?php echo $_SESSION['LoginName']?></br>
密码：<?php echo $_SESSION['LoginPassword']?></br>
</body>
</html>
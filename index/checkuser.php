<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="English" />
<title>WIFI Location Management</title>
</head>
<?php 
 if($_POST[LoginName]=="admin"&&$_POST[LoginPassword]=="123"&&$_POST[code]=="8495")
{ 
 
echo("<script>location.href='main.php'</script>"); 
}else{
echo ("<script>alert('用户名或密码错误！')</script>"); 
echo("<script>location.href='index.php'</script>"); 
}
?> 

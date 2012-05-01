<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="English" />
<title>WIFI Location Management</title>
</head>
<?php 
include_once("../lib/Class/Dba.php");
include_once("../lib/config/db.cfg.php");
 /*if($_POST[LoginName]=="admin"&&$_POST[LoginPassword]=="123"&&$_POST[code]=="8495")
{ 
 
echo("<script>location.href='main.php'</script>"); 
}else{

}*/
$db=new DBA();
$username=trim($_POST[LoginName]);
$password=trim($_POST[LoginPassword]);
$sql = "SELECT username, password FROM user WHERE username='$username' AND password='$password'";  
$result = mysql_query( $sql ); 
$userInfo = @mysql_fetch_array($result); 
if (!empty($userInfo)) {  
if ($userInfo["username"] == $username) {  
// 当验证通过后，启动 Session  
   session_start();  
echo("<script>location.href='main.php'</script>"); 
// 注册登陆成功的 admin 变量，并赋值 true  
$_SESSION["LoginName"] = $username; 
$_SESSION['LoginPassword']=$password;
} else {  
echo ("<script>alert('用户名或密码错误！')</script>"); 
echo("<script>location.href='index.php'</script>"); 
}  
} else {  
echo ("<script>alert('用户名或密码错误！')</script>"); 
echo("<script>location.href='index.php'</script>");   
}  
?> 
?> 

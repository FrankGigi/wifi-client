<?php  
    mysql_connect("localhost","root","yangfannifeng");  
    mysql_select_db("yangfan");  
    $q=mysql_query("SELECT * FROM wifi WHERE RSS ='".$_REQUEST['RSS']."'");  
    while($e=mysql_fetch_assoc($q))  
            $output[]=$e;  
        print(json_encode($output));  
    mysql_close();  
?>  
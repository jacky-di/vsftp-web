<?php  
    if(isset($_POST["submit"]) && $_POST["submit"] == "立即登陆")  
    {  
        $user = $_POST["username"];  
        $psw = $_POST["password"];  
        if($user == "" || $psw == "")  
        {  
            echo "<script>alert('请输入用户名或密码！'); history.go(-1);</script>";  
        }  
        else  
        {  
            $conn = include 'db.php';
            $sql = "select username,password from user where username = '$_POST[username]' and password = md5('$_POST[password]')";  
            $result = mysql_query($sql);  
            $num = mysql_num_rows($result);  
            if($num)  
            {  
                $row = mysql_fetch_array($result);  //将数据以索引方式储存在数组中  
                echo  "<script>alert(' 登 陆 成 功 ！ ');window.location='admin.php';</script>";//跳转管理页（admin.php) }  
            }  
            else  
            {  
                echo "<script>alert('用户名或密码不正确！');history.go(-1);</script>";  
            }  
        }  
    }  
    else  
    {  
        echo "<script>alert('提交未成功！'); history.go(-1);</script>";  
    }  
  
?>  
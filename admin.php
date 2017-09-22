<?php
$fromurl="http://192.168.191.49/"; //禁止直接访问跳转往这个地址。
if( $_SERVER['HTTP_REFERER'] == "" )
{
header("Location:".$fromurl); exit;
}
error_reporting(E_ALL & ~ E_NOTICE);
//提示窗
function alertExit($msg,$flush=0){
    // echo "<script language='javascript'>alert($msg);</script>";
    echo "<script type='text/javascript' language='javascript'>alert('$msg');</script>";
    if ($flush == 1) {
        echo "<script type='text/javascript' language='javascript'>window.location.href='admin.php'</script>";
    }elseif ($flush == 2) {
        echo "<script type='text/javascript' language='javascript'>history.back();self.location.reload();</script>";
    }
}
//输出头部
function htmlheader($title){
echo <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"  content="text/html; charset=utf-8">
<title>{$title}</title>
<meta name="description" content="vsftp管理工具">
<meta name="keywords" content="linux,ftp,vsftp,mysql,pam_mysql,php">
<style type="text/css">
*{margin:0; padding:0; font-size:12px;}
ul,li{ list-style:none;}
a {text-decoration:none;color:#37a}
a:hover { background:#37a;color:white;padding:2px;}
img { border:0;}
.clear{ clear:both;}
.right{ float:right;}
.left{ float:left;}
#content{width:560px;margin:0 auto;text-align:center;margin-top:40px;}
.admin{border:1px solid #ccc; padding:30px;text-align:left;}
.admin h4{margin:10px 0;}
.admin p{margin:10px 0;}
.admin p .btn{padding:2px 4px;}
</style>
</head>
<body>
<div id="content">
    <div class="admin">
        <h4>{$title}</h4>
        <p class="admin_btn">管理菜单：<a href="?ac=ftp">ftp状态</a>&nbsp;&nbsp;<a href="admin.php">用户列表</a>&nbsp;&nbsp;<a href="?ac=add">添加用户</a></p>
EOF;
}
//输出尾部
function htmlfooter(){
echo <<<EOF
    </div>
</div>
</body>
</html>
EOF;
}
//查询
function query($sql){
                 
    $result = mysql_query($sql)  or die ("SQL语句查询错误: " . mysql_error());
    if (mysql_num_rows($result) == 0) {
            die('SQL: '.$sql.'<br>未查询到相关数据');
    }
    $arrReturn = array();
    $index = 0;
    while($arr = mysql_fetch_assoc($result)){
        $arrReturn[$index] = $arr;
        $index++;
    }
    return $arrReturn;
}
//添加
function adduser($name,$password){
    $sql = "select * from users where name='$name'";
    $result = mysql_query($sql)  or die ("SQL语句查询错误: " . mysql_error());
    if (mysql_num_rows($result) > 0) {
            alertExit('用户已存在',1);
    }else{
        $sql = "insert into users(name,password) values('$name',md5('$password'))";
        $result = mysql_query($sql)  or die ("添加角户出错: " . mysql_error());
        // echo mysql_affected_rows();
        if(mysql_affected_rows()==1){
            alertExit("添加用户成功!",2);
        }
    } 
}
//删除
function deluser($id){
    $sql = 'select * from users where id='.$id;
    $result = mysql_query($sql)  or die ("SQL语句查询错误: " . mysql_error());
    if (mysql_num_rows($result) == 0) {
            alertExit('用户不存在',1);
    }else{
        $sql = 'delete from users where id='.$id;
        $result = mysql_query($sql)  or die ("删除角户出错: " . mysql_error());
        if(mysql_affected_rows()==1){
            alertExit("删除用户成功!",1);
        }
    }
}
//修改
function moduser($id,$name,$password){
    $sql = "select * from users where id=$id";
    $result = mysql_query($sql)  or die ("SQL语句查询错误: " . mysql_error());
    if (mysql_num_rows($result) == 0) {
            alertExit('用户不存在',1);
    }else{
        $sql = "select * from users where name='$name' and id!=$id";
        $result = mysql_query($sql)  or die ("SQL语句查询错误: " . mysql_error());
        if (mysql_num_rows($result) > 0) {
                alertExit('用户已存在',1);
        }else{    
            $sql = "update users set name='$name', password='$password' where id=$id";
            $result = mysql_query($sql)  or die ("修改角户出错: " . mysql_error());
            if(mysql_affected_rows()==1){
                alertExit("修改用户成功!",1);
            }
            alertExit("未作任何操作!",1);
        }
    }
}
//ftp状态管理
function ftpadmin($service='status'){
                 
    $arrFtp = array();
    $result = mysql_query("SELECT id FROM users");
    $num_rows = mysql_num_rows($result);
    $arrFtp['usercount'] = $num_rows;
                 
    if($service=='status'){
                     
        $arrFtp['status'] = `service vsftpd status`;
                     
    }elseif($service=='restart'){
                     
        $arrFtp['status'] = `service vsftpd restart`;
                     
    }elseif ($service=='stop') {
                     
        $arrFtp['status'] = `service vsftpd stop`;
                     
    }
                 
    if(empty($arrFtp['status'])){
        $arrFtp['status'] = 'Unknow';
    }
    return $arrFtp;
                 
}
$conn = include 'db.php';
$strAction = htmlspecialchars($_GET['ac']);
$arrAction = array('del', 'mod', 'add', 'ftp');
if (! in_array($strAction, $arrAction)) {
        htmlheader('Vsftp 管理');
        $arrUserList = query('select * from users');
        foreach ($arrUserList as $key => $value) {
        ?>
        <form action='./admin.php?ac=mod' method='post'>
        <p><label>name:</label> <input type="text" name="name" value="<?php echo $value['name'];?>">
            <label>password:</label> <input type="password" name="password" value="<?php echo $value["password"];?>">
            <input type='hidden' name='id' value="<?php echo $value["id"];?>">
             <input type="submit" value="修改" class="btn">&nbsp;<input type="button" onclick="window.location.href='?id=<?php echo $value["id"];?>&ac=del'" value="删除" class="btn"></p>
        </form>
        <?php }
        htmlfooter();
}else{
                 
    if ($strAction=='add') {
                     
        if ($_SERVER['REQUEST_METHOD']=='POST') {
                         
            $name = htmlspecialchars($_POST['name']);
            $password = htmlspecialchars($_POST['password']);
                         
            adduser($name,$password);
                         
        }else{
            htmlheader('添加用户');
            ?>
        <form action='./admin.php?ac=add' method='post'>
        <p><label>name:</label> <input type="text" name="name" value="">
            <label>password:</label> <input type="password" name="password" value="">
            <input type="submit" value="提交" class="btn">&nbsp;<input type="reset" value="重置" class="btn"></p>
        </form>
<?php      
            htmlfooter();
        }
    }elseif ($strAction=='del') {
                     
        $intId = intval($_GET['id']);
                     
        if($intId!=''){
            deluser($intId);
        }else{
            alertExit('参数错误！',1);
        }
                     
    }elseif ($strAction=='mod') {
                     
        $intId = intval($_POST['id']);
                     
        $name = htmlspecialchars($_POST['name']);
        $password = htmlspecialchars($_POST['password']);
                                 
        if($name!='' && $password!=''){
            moduser($intId,$name,$password);
        }else{
            alertExit('参数错误！',1);
        }
    }elseif($strAction=='ftp'){
            htmlheader('FTP状态');
            $arrFtp = array();
            $status = htmlspecialchars($_GET['status']);
            if($status=='restart')$arrFtp = ftpadmin('restart');
            elseif($status=='stop')$arrFtp = ftpadmin('stop');
            else $arrFtp = ftpadmin();
            ?>
        <p><label>用户总数：</label> <?php echo $arrFtp['usercount'];?> <br>
            <label>vsftp状态：</label> <?php echo $arrFtp['status'];?>
            <input type="button" onclick="window.location.href='?ac=ftp&status=restart'" value="重启" class="btn">&nbsp;<input type="button" onclick="window.location.href='?ac&status=stop'" value="停止" class="btn"></p>
<?php      
            htmlfooter();
                     
    }else{
        die('无效的地址！');
    }
}
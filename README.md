                               Vsftp-web管理说明文档

                               
此系统为简单开发版，实现功能仅为创建用户及管理VSFTP服务，此系统仅在CENTOS6.7及doaker 

centos6.7镜像下测试使用过由于VSFTP软件原因本软件并不能更改或添加映射虚拟目录。

此系统需要vsftp+apache+php+mysql+pam_mysql使用PHP+mysql开发其中mysql需要支持pam_mysql模块。

在使用本系统之前需要完成的工作有

1.	搭建一套apache+php+mysql环境

2.	下载安装VSftp

3.	更改VSFTP验证为MYSQL验证

1、	搭建apache+php+mysql环境 可以使用yum安装，其他方案安装请注意安装路径，由于篇幅有限本文暂不涉及安装方法。

2、	下载VSFTP及pam_mysql方法如下：

yum -y install vsftpd 

pam-mysql

需要安装特定源安装包为：el-release-6-8.noarch.rpm

下载地址：http://rpmfind.net/linux/rpm2html/search.php?query=epel-release 

（注意下载适合自己linux版本的）

rpm -ivh epel-release-6-8.noarch.rpm

yum -y install pam_mysql

rpm -ql pam_mysql

3、	更改VSFTP验证方法

1、	建立所需认证文件

vi /etc/pam.d/vsftpd.mysql

添加如下两行：

auth required /lib64/security/pam_mysql.so user=root passwd=root host=172.16.36.1 db=vsftpd table=users usercolumn=name passwdcolumn=password crypt=3

account required /lib64/security/pam_mysql.so user=root passwd=root host=172.16.36.1 db=vsftpd table=users usercolumn=name passwdcolumn=password crypt=3

----------------------------------------------------------------
user: 数据库用户名

passwd: 数据库用户密码

host: 为数据库主机IP地址，如果是编译安装的数据库，只能填写远程IP地址，并给把这个IP地址授权给用户

db: 数据库名

table: 表名

usercolumn: 用户字段名

passwdcolumn: 用户密码字段名

crypt: 密码保存方式，0为明文，1为encrypt函数加密，2为password函数加密，3为MD5加密注意：由于mysql的安装方式不同，pam_mysql.so基于unix 

sock连接mysql服务器时可能会出问题，此时，建议授权一个可远程连接的mysql并访问数据库的root用户。(例子中填写的是默认用户名root密码root)

2、	建立虚拟用户映射的系统用户及对应的目录

useradd -s /sbin/nologin -d /var/ftproot vuser
chmod go+rx /var/ftproot

请确保/etc/vsftpd/vsftpd.conf中已经启用了以下选项

anonymous_enable=NO   #禁止匿名用户登录

local_enable=YES      #允许本地用户登录

write_enable=YES      #本地用户有写权限

anon_upload_enable=NO #匿名用户没有上传权限

anon_mkdir_write_enable=NO #匿名用户没有创建权限

chroot_local_user=YES #禁锢用户家目录

而后添加以下选项

guest_enable=YES
guest_username=vuser
virtual_use_local_privs=YES

并确保pam_service_name选项的值如下所示

pam_service_name=vsftpd.mysql

3、	重新启动服务，测试一下

service vsftpd restart

然后把安装目录文件上传至html目录下(apache默认www路径如更改自行更改。）

运行安装文件夹install

至此本文结束如遇到问题请联系作者32867255@qq.com

                                          Vsftp-web Management manual  
What you need to do before using this system is:
This system is a simple version of the development, implementation function is only to create users and manage VSFTP service, this test system used only in CENTOS 6.7 and doaker centos 6.7 image, the VSFTP software lead to this software does not change or add virtual directory mapping. 
This system requires vsftp+apache+php+mysql+pam_mysql to be developed using PHP+mysql, where mysql needs to support the pam_mysql module. 
What you need to do before using this system is:

1.	build a apache+php+mysql environment.
2.	download and install VSftp.
3. change VSFTP validation to MYSQL verification.

Firstly, build apache+php+mysql environment.
You can use Yum installation, other programs installation, please pay attention to installation path, because the space is limited, this article temporarily does not involve installation method.

Secondly, download the VSFTP and pam_mysql methods are as follows:
yum -y install vsftpd 
pam-mysql

You need to install a specific source installation package: el-release-6-8.noarch.rpm

Download address: 
http://rpmfind.net/linux/rpm2html/search.php?query=epel-release (pay attention to download for your Linux version).
rpm -ivh epel-release-6-8.noarch.rpm
yum -y install pam_mysql
rpm -ql pam_mysql

1.	change the VSFTP authentication method.
2.	Establish required certification documents.
vi /etc/pam.d/vsftpd.mysql

Add the following two lines:
auth required /lib64/security/pam_mysql.so user=root passwd=root host=172.16.36.1 db=vsftpd table=users usercolumn=name passwdcolumn=password crypt=3
account required /lib64/security/pam_mysql.so user=root passwd=root host=172.16.36.1 db=vsftpd table=users usercolumn=name passwdcolumn=password crypt=3

user: database user name.
passwd: database user password.
host: it is the database host IP address. If it is compiled and installed database, it can only fill out the remote IP address and authorize the IP address to the user.
db: database name.
table: table name.
usercolumn: user field name.
Passwdcolumn: user password field name.

Crypt: password save mode.0 is plain text.1 encryption for encrypt functions.2 encryption for password functions.3 encryption for MD5
(Note: because of mysql installed way is different, pam_mysql.so based on the unix sock connecting the mysql server may be having problems. At this point, it is recommended to authorize a remote connection mysql and access the database's root users. )
(Fill in the default user name root and password root in the example) 

1.	establish virtual user mapped system users and corresponding directories 
useradd -s /sbin/nologin -d /var/ftproot vuser
chmod go+rx /var/ftproot
Please make sure that the following options are enabled in /etc/vsftpd/vsftpd.conf
anonymous_enable=NO   # prohibit anonymous user login
local_enable=YES      # allows a local user login
write_enable=YES      # local user has write permissions
anon_upload_enable=YES # anonymous users do not upload permissions
anon_mkdir_write_enable=NO # anonymous user does not have permissions to create
chroot_local_user=YES # imprisons the users home directory
Then add the following options 
guest_enable=YES
guest_username=vuser
virtual_use_local_privs=YES
And make sure the value of the pam_service_name option is as follows:
Pam_service_name=vsftpd.mysql 

2. Restart the service and test it
Service vsftpd restart 
Then upload the installation directory file to the html directory (apache defaults to the www path, such as the change, change by yourself) 
Run setup folder install 

So far, if you have any questions, please contact the author 32867255@qq.com

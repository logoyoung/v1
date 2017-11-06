#!/usr/bin/expect -f
set timeout 5000
#目步文件目录 如/home/work/huanpeng/www/
set dev_dir "*******"
#op /home/work/huanpeng/op/
set op_dev_dir "*******"

#ssh用户名
set user_name "********"
#ssh 密码
set password "********"

set dev_host "122.70.146.49"
spawn /usr/bin/rsync -avz --exclude "hpFrame/config/system/system_conf.php" --exclude "include/init.php" --exclude "htdocs/init.php" --exclude "htdocs/initCookie.php" --exclude "htdocs/tpl/commSource.php" --exclude "tools/rsync_dev.sh" --exclude "tools/dp.sh" --exclude "html/js/common.js" --exclude ".svn/" --exclude ".git/" -e "/usr/bin/ssh -p 7710" $dev_dir "$user_name@$dev_host:/data/www/$user_name"
set spawn_id
expect {
    "(yes/no)" { send "yes\r"; exp_continue }
    "password:" { send "$password\n" }
}
send "exit\n"
interact

spawn /usr/bin/rsync -avz --exclude "include/init.php" --exclude "htdocs/init.php" --exclude "htdocs/initCookie.php" --exclude "htdocs/tpl/commSource.php" --exclude "tools/rsync_dev.sh" --exclude "tools/dp.sh" --exclude "html/js/common.js" --exclude ".svn/" -e "/usr/bin/ssh -p 7710" $op_dev_dir "$user_name@$dev_host:/data/www/$user_name/op"
set spawn_id
expect {
    "(yes/no)" { send "yes\r"; exp_continue }
    "password:" { send "$password\n" }
}
send "exit\n"
interact
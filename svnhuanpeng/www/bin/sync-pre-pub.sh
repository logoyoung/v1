#!/usr/bin/env bash

/usr/bin/rsync -avrn --delete /usr/local/huanpeng/htdocs/main/ /usr/local/huanpeng-pub/htdocs
/usr/bin/rsync -avrn --delete /usr/local/huanpeng/include /usr/local/huanpeng-pub
/usr/bin/rsync -avrn --delete /usr/local/huanpeng/record-lua /usr/local/huanpeng-pub
/usr/bin/rsync -avrn --delete /usr/local/huanpeng/bin /usr/local/huanpeng-pub
/usr/bin/rsync -avrn --delete /usr/local/huanpeng/docs /usr/local/huanpeng-pub
/usr/bin/rsync -avrn --delete /usr/local/huanpeng/ulink /usr/local/huanpeng-pub

#/usr/bin/rsync -avr --delete /usr/local/huanpeng/ulink /usr/local/huanpeng-pub
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/htdocs/main/ /usr/local/huanpeng-pub/htdocs
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/include/init.php /usr/local/huanpeng-pub/include
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/include/bussiness_flow.fun.php /usr/local/huanpeng-pub/include
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/include/commonFunction.php /usr/local/huanpeng-pub/include
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/include/functions.php /usr/local/huanpeng-pub/include
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/include/roomid.txt /usr/local/huanpeng-pub/include
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/include/wcSDK/src/Wcs/PersistentFops/Fops_huanpeng.class.php /usr/local/huanpeng-pub/include/wcSDK/src/Wcs/PersistentFops

#/usr/bin/rsync -avr --delete /usr/local/huanpeng/htdocs/main/downloadPc.php /usr/local/huanpeng-pub/htdocs
#/usr/bin/rsync -avrn --delete /usr/local/huanpeng/htdocs/main/footerSub.php /usr/local/huanpeng-pub/htdocs
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/htdocs/main/initCookie.php /usr/local/huanpeng-pub/htdocs
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/htdocs/main/reportModal.php /usr/local/huanpeng-pub/htdocs
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/htdocs/main/activity/ /usr/local/huanpeng-pub/htdocs/activity
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/htdocs/main/help/ /usr/local/huanpeng-pub/htdocs/help
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/htdocs/main/personal/ /usr/local/huanpeng-pub/htdocs/personal
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/htdocs/main/static/css/ /usr/local/huanpeng-pub/htdocs/static/css
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/htdocs/main/static/img/ /usr/local/huanpeng-pub/htdocs/static/img
#/usr/bin/rsync -avr --delete /usr/local/huanpeng/htdocs/main/tpl/ /usr/local/huanpeng-pub/htdocs/tpl
#/usr/bin/rsync -avrn --delete /usr/local/huanpeng/include/commonFunction.php /usr/local/huanpeng-pub/include
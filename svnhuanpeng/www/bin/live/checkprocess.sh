#!/bin/bash

exe='/usr/bin/php'
base='/data/huanpeng/bin/live/'
timeout=3600

#process[0]="${base}process1.php"
#process[0]="${base}process1.php"
#process[1]="${base}process2.php"
#process[2]="${base}process3.php"
#process[3]="${base}process4.php"

process[0]="${base}bucketlist.php"


#检测进程状态()

function check_process_status(){
	processscript=$1
	pid=`ps aux|grep ${processscript}|grep -v grep|awk '{print $2}'`
	#echo $pid
	if [ $pid  ]
		then
		echo -e "${processscript} are working\n" >> /dev/null
		#echo $pid
		return $pid
	else
		#echo -e "${processscript} has stoped\n"
		#echo $pid
		return 0
	fi

}

function start_process(){
	processscript=$1
	#echo "test start"
	#check_process_status $processscript
	pid=`ps aux|grep ${processscript}|grep -v grep|awk '{print $2}'`
      # echo $pid
	if [ $pid  ]
		then
			echo -e "${processscript} are working\n" >> /dev/null
			#todo stop or restart
	else
		echo -e "start ${processscript}\n"
		#todo start
		${exe} ${processscript} >> /dev/null &
	fi

}

function stop_process(){
	processscript=$1
	#check_process_status $processscript
	#pid=$?
	pid=`ps aux|grep ${processscript}|grep -v grep|awk '{print $2}'`
	#echo $pid
	if [ $pid  ]
		then
			echo -e "stop ${processscript}\n" 
			#todo stop
			#echo $pid
			kill -TERM $pid > /dev/null 2>&1

	else
		echo -e "${processscript} has stoped" >> /dev/null

	fi
}
function restart_process(){
	stop_process $1
	sleep 2
	start_process $1
}

function get_valid(){
	#heck_process_status $1
	pid=`ps aux|grep $1|grep -v grep|awk '{print $2}'`
	#pid=$?
	#echo $pid
	if [ $pid ]
		then
			echo $pid >> /dev/null 
			#return 0
	else
		#echo $pid >> /dev/null
		return 0
	fi
	#echo 4444
	timeout=$2
	if [ $timeout = '0' ]
		then
		return 1
	fi
	#echo $2
	user_hz=$(getconf CLK_TCK)
	systime=$(cat /proc/$pid/stat|cut -d" " -f22)
	uptime=$(cat /proc/uptime |cut -d" " -f1)
	lasttime=$((${uptime%.*}-$systime/$user_hz))
	#echo $lasttime
	if [ $lasttime -ge $timeout ] 

		then
			return 0
	else
		return 1
	fi

}

function main(){
	for script in ${process[@]}
		do
		get_valid  $script $timeout
		if [ $? -eq 0 ]
			then
				echo -e "restart ${script}\n"
				restart_process ${script}
		else
			echo -e "${script} checked"
		fi
	done

}

main







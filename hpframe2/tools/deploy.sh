#!/bin/bash
#
#
app_name="frame"
#网站文件所有者,不是nginx或fpm进程用户，切记使用同一用户
code_owner=$USER
git_repo="ssh://git@git.huanpeng.com:7710/www/hpframe.git"
#默认部署代码分支
default_git_branch="master"
#默认部署环境
default_env_name="dev"
#默认部署代码目录
default_deploy_dir="/data/www/"
default_deploy_dev_dir="/data/www/frame/"
default_deploy_pre_dir="/data/frame/"
default_deploy_pro_dir="/data/frame/"
nginx_conf_dir="/usr/local/openresty/nginx/conf/vhosts/"
dp_tools_dir="/data/deploy_frame/"
dp_nginx_dir="${dp_tools_dir}config/"
dp_nginx_conf_ct="frame_nginx_ct.conf"
dp_nginx_conf_dev="frame_nginx_dev.conf"
dp_nginx_conf_pre="frame_nginx_pre.conf"
dp_nginx_conf_pro="frame_nginx_pro.conf"

#默认日志目录
default_log_dir="/data/logs/"
default_backup_dir="${dp_tools_dir}code/backup/"
backup_regex=$(date "+%Y%m%d_%H%M%S")
default_git_code="${dp_tools_dir}code/git/"

#部暑环境
if [[ $1 != '' ]]; then
    env_name=$1
    log_dir=$default_log_dir
else
    env_name=$code_owner
    log_dir=$default_log_dir$env_name'/'
fi

#代码部署目录
if [[ $env_name == "dev" ]]; then
    deploy_dir=$default_deploy_dev_dir
    #待部署分支
    git_branch="master"
    dp_nginx_conf=$dp_nginx_conf_dev
elif [[ $env_name == "pre" ]]; then
    deploy_dir=$default_deploy_pre_dir
    #待部署分支
    git_branch="master"
    dp_nginx_conf=$dp_nginx_conf_pre
elif [[ $env_name == "pro" ]]; then
    deploy_dir=$default_deploy_pro_dir
    #待部署分支
    git_branch="master"
    dp_nginx_conf=$dp_nginx_conf_pro
else
    deploy_dir="${default_deploy_dir}${code_owner}/${app_name}/"
    #待部署分支
    git_branch="master"
    dp_nginx_conf=$dp_nginx_conf_ct
fi

#代码备分目录
backup_dir="${default_backup_dir}${env_name}/"
#待部署代码存放目录
backup_rsync_coede_dir="${backup_dir}rsync_code/"

#初始日志目录
if [ ! -d $log_dir ]; then
    sudo mkdir -p $log_dir
    if [ $? -ne 0 ]; then
        echo "create log_dir error; ${log_dir}"
        exit
    fi
    sudo chmod 777 $log_dir
fi

#初始部署代码目录
if [ ! -d $deploy_dir ]; then
    sudo mkdir -p $deploy_dir
    if [ $? -ne 0 ]; then
        echo "create deploy_dir error; ${$deploy_dir}"
        exit
    fi
    sudo chmod 755 $deploy_dir
fi

#初始代码备分目录
if [ ! -d $backup_dir ]  ; then
    sudo mkdir -p $backup_dir
    if [ $? -ne 0 ]; then
        echo "create backup_dir error; ${backup_dir}"
        exit
    fi
fi

#初始化待部署代码存放目录
if [ ! -d $backup_rsync_coede_dir ]  ; then
    sudo mkdir -p $backup_rsync_coede_dir
    if [ $? -ne 0 ]; then
        echo "create backup_rsync_coede_dir error; ${backup_rsync_coede_dir}"
        exit
    fi
fi

#备分代码
pre_code_tar="${backup_dir}${backup_regex}.tar.gz"
sudo tar -zcPf $pre_code_tar $deploy_dir
if [ $? -ne 0 ]; then
    echo "backup code error"
    exit
fi

if [ ! -d $default_git_code ]  ; then
    sudo mkdir -p $default_git_code
    if [ $? -ne 0 ]; then
        echo "create default_git_code error; ${$default_git_code}"
        exit
    fi
fi

cd $default_git_code
sudo git checkout $git_branch >> "/tmp/git_frame_${backup_regex}.log" 2>&1
sudo git fetch --all >> "/tmp/git_frame_${backup_regex}.log" 2>&1
sudo git reset --hard origin/$git_branch >> "/tmp/git_frame_${backup_regex}.log" 2>&1
sudo git fetch >>/tmp/git_test.log 2>&1
sudo git checkout $git_branch >>"/tmp/git_frame_${backup_regex}.log" 2>&1

tmp_code_tar="${default_git_code}code.tar"
if [ -f $tmp_code_tar ]; then
    sudo rm -rf $tmp_code_tar
fi

#从git打包代码
sudo git archive --format tar --output $tmp_code_tar $git_branch >> "/tmp/git_frame_${backup_regex}.log" 2>&1
#解压待部署代码
sudo tar -xPf $tmp_code_tar -C $backup_rsync_coede_dir

#替换个人开发环境的配置
if [[ $env_name != 'dev' ]] || [[ $env_name != 'pre' ]] || [[ $env_name != 'pro' ]]; then
    ct_nginx_conf_file="${backup_rsync_coede_dir}tools/config/frame_nginx_ct.conf"
    d_nginx_conf_file="${nginx_conf_dir}frame_nginx_${env_name}.conf"
    system_conf="${backup_rsync_coede_dir}config/system/system_conf.ini"
    static_conf="${backup_rsync_coede_dir}config/system/view_conf.ini"
    sudo cp -f "${dp_tools_dir}config/system_conf.ini" $system_conf
    sudo cp -f "${dp_tools_dir}config/view_conf.ini" $static_conf
    sudo sed -i "s#user_name#${env_name}#g" $ct_nginx_conf_file
    sudo sed -i "s#user_name#${env_name}#g" $system_conf
    sudo sed -i "s#user_name#${env_name}#g" $static_conf
    sudo cp -f $ct_nginx_conf_file $d_nginx_conf_file
else
    #部署nginx
    sudo cp -f "${dp_nginx_dir}${dp_nginx_conf}" $nginx_conf_dir
fi

sudo rm -rf "${backup_rsync_coede_dir}.gitignore"
sudo rm -rf "${backup_rsync_coede_dir}.svn/"
sudo rm -rf "${backup_rsync_coede_dir}.git/"
find $backup_rsync_coede_dir -type d -exec sudo chmod 755 {} \;
find $backup_rsync_coede_dir -type f -exec sudo chmod 644 {} \;

#同步代码
sudo rsync -az --exclude "${backup_rsync_coede_dir}.gitignore" --delete $backup_rsync_coede_dir $deploy_dir

#重启服务
sh /data/deploy_frame/server.sh > /dev/null 2>&1

echo ;echo ;echo ;echo ;

dev_api_domain="${env_name}.frame.huanpeng.com"
dev_static_domain="${env_name}.static.frame.huanpeng.com"
dev_download="${env_name}.download.frame.huanpeng.com"

echo "开发环境部署完成"
echo -e "\t\033[31m api域名:\033[0m${dev_api_domain}"
echo -e "\t\033[31m 静态资源域名:\033[0m${dev_static_domain}"
echo -e "\t\033[31m 文件下载域名:\033[0m${dev_download}"
echo -e "\t\033[31m 代码目录:\033[0m${deploy_dir}"
echo -e "\t\033[31m 日志目录:\033[0m${log_dir}"
echo -e "\t\033[31m 备分代码:\033[0m${pre_code_tar}"
echo -e "\t\033[31m 绑定host:\033[0m122.70.146.49 ${dev_api_domain} ${dev_static_domain} ${dev_download}"
echo ;echo ;echo ;echo ;
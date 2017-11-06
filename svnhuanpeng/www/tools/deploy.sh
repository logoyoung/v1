#!/bin/bash
# dev pre pro

#环境名 暂时写死吧
app_env='dev'
#需要部署的开发者目录名
if [[ $1 != '' ]]; then
    app_dev_name=$1
else
    app_dev_name=$USER
fi

#dev 开发目录环境
app_dev_code_dir="/data/www/"
#dev nginx配置目录
app_dev_nginx_conf_dir='/usr/local/openresty/nginx/conf/'
#dev 开发者nginx 配置目录
app_dev_nginx_conf_vhost_dir='/usr/local/openresty/nginx/conf/vhosts/'
#php 配置替换变量名
app_dev_php_var='deploy_dev_app_name'
app_code_dir='/usr/local/huanpeng/'
app_code_op_dir='/data/op/'
app_tools='/data/tools/'
nginx_user='www'
app_dev_log_dir='/data/logs/'
app_dev_user_log_dir="${app_dev_log_dir}${app_dev_name}"
if [[ $app_env == 'dev' ]]; then

    if [[ $app_dev_name == '' ]]; then
        echo 'app_dev_name'
        exit
    fi

    if [ ! -d $app_dev_user_log_dir ]; then
        #statements
        sudo mkdir -p $app_dev_user_log_dir
        sudo chmod 777 $app_dev_user_log_dir
    fi

    #开发者目录是否存在 不存在就创建
    app_dev_user_dir="$app_dev_code_dir$app_dev_name"
    if [ ! -d $app_dev_user_dir ]; then
        sudo mkdir -p $app_dev_user_dir
        if [ $? -ne 0 ]; then
           echo "mkdir $app_dev_user_dir error "
           exit
        fi
    fi

    #找到源代码复制源代码目录
    if [ ! -d $app_code_dir ]; then
        echo "empty code $app_code_dir"
        exit
    fi

    #同步代码目录
    sudo rsync -avz $app_code_dir $app_dev_user_dir
    user_dev_op_dir="${app_dev_user_dir}/op"
    #op code dir
    sudo mkdir -p $user_dev_op_dir
    sudo rsync -avz $app_code_op_dir $user_dev_op_dir

    if [ $? -ne 0 ]; then
        echo "rsync error "
        exit
    fi

    dev_user_system_init="${app_dev_user_dir}/include/config/system/"
    #复制init.php 到开发者目录
    sudo cp "${app_tools}system/init.php" $dev_user_system_init
    dev_user_init_file="${dev_user_system_init}init.php"
    #替换开发者目录 init.php里的代码路径
    sudo sed -i "s#dev_user_code_dir#$app_dev_user_dir#g" $dev_user_init_file
    #替换开发者目录include/init.php里的日志目录名
    sudo sed -i "s#dev_user_name#$app_dev_name#g" $dev_user_init_file
    user_domain="http://${app_dev_name}.huanpeng.com/"
    #替换域名前端js css加载需要
    sudo sed -i "s#dev_user_domain#${user_domain}#g" $dev_user_init_file
    sudo rm -rf "${app_dev_user_dir}/include/init.php"
    #替换include/init.php
    sudo cp $dev_user_init_file "${app_dev_user_dir}/include/"
    #替换开发者目录 htdocs init.php里的日志目录名
    sudo sed -i "s#/usr/local/huanpeng/#${app_dev_user_dir}/#g" "${app_dev_user_dir}/htdocs/init.php"

    #历史遗留加载init.php问题，解决好了可去掉
    user_js_dir="${app_dev_user_dir}/html/js/"
    sudo rm -rf "${user_js_dir}common.js"
    sudo cp "${app_tools}/js/common.js" $user_js_dir

    sudo sed -i "s#/usr/local/huanpeng/#${app_dev_user_dir}/#g" "${app_dev_user_dir}/htdocs/tpl/commSource.php"
    sudo sed -i "s#<?php echo STATIC_CSS_PATH; ?>#http://${app_dev_name}.huanpeng.com/static/css/#g" "${app_dev_user_dir}/htdocs/tpl/commSource.php"
    sudo sed -i "s#<?php echo STATIC_JS_PATH; ?>#http://${app_dev_name}.huanpeng.com/static/js/#g" "${app_dev_user_dir}/htdocs/tpl/commSource.php"
    sudo sed -i "s#/usr/local/huanpeng/#${app_dev_user_dir}/#g" "${app_dev_user_dir}/htdocs/initCookie.php"

    #更改代码权限
    sudo chown -R $app_dev_name:dev $app_dev_user_dir
    sudo chmod o+x -R $app_dev_user_dir

    #替换dev_nginx
    dev_nginx_conf_file="${app_dev_nginx_conf_vhost_dir}${app_dev_name}_dev_huanpeng.conf"
    sudo cp "${app_tools}nginx/nginx_dev.conf" $dev_nginx_conf_file
    #替换开发者目录 nginx 里的域名
    sudo sed -i "s#dev_user_name#$app_dev_name#g" $dev_nginx_conf_file

    #op nginx
    dev_op_conf="${app_dev_nginx_conf_vhost_dir}${app_dev_name}_op_huanpeng.conf"
    sudo cp "${app_tools}nginx/op_dev.conf" $dev_op_conf
    sudo sed -i "s#dev_user_name#$app_dev_name#g" $dev_op_conf

    #dota nginx
    dev_dota_conf="${app_dev_nginx_conf_vhost_dir}${app_dev_name}_dota_huanpeng.conf"
    sudo cp "${app_tools}nginx/nginx_dota.conf" $dev_dota_conf
    sudo sed -i "s#dev_user_name#$app_dev_name#g" $dev_dota_conf

    dev_hpFrame_conf="${app_dev_nginx_conf_vhost_dir}${app_dev_name}_hpFrame_huanpeng.conf"
    sudo cp "${app_tools}nginx/nginx_hpFrame.conf" $dev_hpFrame_conf
    sudo sed -i "s#dev_user_name#$app_dev_name#g" $dev_hpFrame_conf
    hpframe_system_conf="${app_tools}system/hpFrame_conf.php"
    dev_hpFrame_system_conf="${app_dev_user_dir}/hpFrame/config/system/"
    dev_hpFrame_system_conf_file="${dev_hpFrame_system_conf}system_conf.php"
    sudo rm -rf $dev_hpFrame_system_conf_file
    sudo cp $hpframe_system_conf $dev_hpFrame_system_conf
    sudo mv "${dev_hpFrame_system_conf}hpFrame_conf.php" $dev_hpFrame_system_conf_file
    sudo sed -i "s#dev_user_name#${app_dev_name}#g" $dev_hpFrame_system_conf_file

fi

server="${app_tools}server.sh"
sudo /bin/bash $server
dev_user_domain="${app_dev_name}.huanpeng.com"
dev_user_op_domian="${app_dev_name}.op.huanpeng.com"
dev_user_dota_domain="${app_dev_name}.dota.huanpeng.com"
dev_user_hpFrame_domin="${app_dev_name}.hpframe.huanpeng.com"
dev_user_hpFrame_statoc_domin="${app_dev_name}.static.hpframe.huanpeng.com"
echo ;echo ;echo ;echo ;
echo "开发环境部署完成"
echo -e "\t\033[31mwww域名:\033[0m${dev_user_domain}"
echo -e "\t\033[31mdota域名:\033[0m${dev_user_dota_domain}"
echo -e "\t\033[31mop域名:\033[0m${dev_user_op_domian}"
echo -e "\t\033[31mwww绑定host:\033[0m122.70.146.49 ${dev_user_domain}"
echo -e "\t\033[31mdota绑定host:\033[0m122.70.146.49 ${dev_user_dota_domain}"
echo -e "\t\033[31mhpframe绑定host:\033[0m122.70.146.49 ${dev_user_hpFrame_domin}"
echo -e "\t\033[31mhpframe static:绑定host:\033[0m122.70.146.49 ${dev_user_hpFrame_statoc_domin}"
echo -e "\t\033[31mop绑定host:\033[0m122.70.146.49 ${dev_user_op_domian}"
echo -e "\t\033[31m代码目录:\033[0m${app_dev_user_dir}"
echo -e "\t\033[31mwww nginx配置:\033[0m${dev_nginx_conf_file}"
echo -e "\t\033[31mdota nginx配置:\033[0m${dev_dota_conf}"
echo -e "\t\033[31mhpframe nginx配置:\033[0m${dev_hpFrame_conf}"
echo -e "\t\033[31mop nginx配置:\033[0m${dev_op_conf}"
echo -e "\t\033[31m日志目录:\033[0m${app_dev_user_log_dir}"
echo ;
echo  "开发注意事项,由于历史的原因以下文件暂时不能同步"
echo -e "\033[31m\t ${app_dev_user_dir}/include/init.php\033[0m"
echo -e "\033[31m\t ${app_dev_user_dir}/htdocs/init.php\033[0m"
echo -e "\033[31m\t ${app_dev_user_dir}/htdocs/initCookie.php\033[0m"
echo -e "\033[31m\t ${app_dev_user_dir}/htdocs/tpl/commSource.php\033[0m"
echo -e "\033[31m\t ${user_js_dir}common.js\033[0m"

echo ;
exit
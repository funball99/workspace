#!/bin/sh
#
# Copyright (c) 1999,2013 by Tencent, Inc.
#
# Simple installer of runtime environment for Tencent OpenCloud API PHP-SDK

RED=\\e[1m\\e[31m
DARKRED=\\e[31m
GREEN=\\e[1m\\e[32m
DARKGREEN=\\e[32m
BLUE=\\e[1m\\e[34m
DARKBLUE=\\e[34m
YELLOW=\\e[1m\\e[33m
DARKYELLOW=\\e[33m
MAGENTA=\\e[1m\\e[35m
DARKMAGENTA=\\e[35m
CYAN=\\e[1m\\e[36m
DARKCYAN=\\e[36m
RESET=\\e[m

function sdk_help()
{
    echo -e "\nUsage:\n"
    echo -e "--endpoint=The URL of Tencent Cloud API. For example: --endpoint=api.yun.qq.com\n"
    echo -e "--secret_id=Your secretId for API\n"
    echo -e "--secret_key=Your secretKey for API\n"
    echo -e "--php_path=The PHP executable file path in your device. For example: --php_path=/usr/local/php/bin/php\n"
    echo -e "--cmd_path=Tencent Cloud API Cmd path in your device. For example: --cmd_path=/data/release/cmd\n"
    echo -e "--temporary_installation : Optional. No parameters. Support for temporary installation. Must using ${YELLOW}source$RESET to run the installation script with this parameter. If you quit or reboot the system this installation will be invalid. \n"
}

for((i=1;i<=$#;i++))
do
    arg=`echo ${!i}`
    name=`echo $arg|awk -F "=" '{print $1}'|tr -d '-'`
    value=`echo $arg|awk -F "=" '{print $2}'`
    if [ "x${value}" == "x" ] ; then
    	eval "$name=$name"
    else
    	eval "$name=$value"
    fi
done

CUR_DIR=$( dirname $(which $0) )
VAR_ENV_FILE="/etc/profile"
#VAR_ENV_FILE="pro.log"

if [ "x${help}" != "x" ] ; then
    sdk_help
    exit -1
fi

if [ "x${secret_id}" == "x" -o "x${secret_key}" == "x" -o "x${cmd_path}" == "x" -o "x${php_path}" == "x" -o "x${endpoint}" == "x" ] ; then
    sdk_help
    exit -1
fi

function updateEnv()
{
	RET=`egrep -v "export TC_OPENCLOUD_ENDPOINT|export TC_OPENCLOUD_SDK_PHP_HOME|export TC_OPENCLOUD_PHP_PATH|export TC_OPENCLOUD_SECRETID|export TC_OPENCLOUD_SECRETKEY" ${VAR_ENV_FILE} > "${VAR_ENV_FILE}.tmp"`
    RET=`mv ${VAR_ENV_FILE}.tmp ${VAR_ENV_FILE}`
	
	echo "export TC_OPENCLOUD_SDK_PHP_HOME=${cmd_path}"     		>> ${VAR_ENV_FILE}
    echo "export TC_OPENCLOUD_PHP_PATH=${php_path}"     			>> ${VAR_ENV_FILE}
	echo "export TC_OPENCLOUD_ENDPOINT=${endpoint}"       			>> ${VAR_ENV_FILE}
    echo "export TC_OPENCLOUD_SECRETID=${secret_id}"        		>> ${VAR_ENV_FILE}
    echo "export TC_OPENCLOUD_SECRETKEY=${secret_key}"        		>> ${VAR_ENV_FILE}
	
    CHECKPATH=`grep ':$TC_OPENCLOUD_SDK_PHP_HOME/bin:$TC_OPENCLOUD_PHP_PATH' ${VAR_ENV_FILE}`
    if [ "x${CHECKPATH}" == "x" ];then
        echo 'PATH=$PATH:$TC_OPENCLOUD_SDK_PHP_HOME/bin:$TC_OPENCLOUD_PHP_PATH' >> ${VAR_ENV_FILE}
    else
        RET=`grep -v ':$TC_OPENCLOUD_SDK_PHP_HOME/bin:$TC_OPENCLOUD_PHP_PATH' ${VAR_ENV_FILE} > "${VAR_ENV_FILE}.tmp"`
        RET=`mv ${VAR_ENV_FILE}.tmp ${VAR_ENV_FILE}`
        echo "$CHECKPATH" >> ${VAR_ENV_FILE}
    fi
}

function checkPara()
{
	PHP_CHECK=`${php_path} -v`
	PHP_VERSION=`echo $PHP_CHECK | grep -i 'PHP' | cut -d' ' -f2`
	if [ "x${PHP_VERSION}" == "x" ];then
		echo -e "${RED}PHP path error!Please check!$RESET\n"
		exit -1
	fi
	
	VERSION_CHECK=`echo $PHP_VERSION | tr -d '.' `
	if [[ "${VERSION_CHECK}" < "531" ]];then
		echo -e "${RED}PHP version is not available!Please update!$RESET\n"
		exit -1
	fi

	CMD_CHECK=`${php_path} ${cmd_path}/lib/CloudCmd.class.php`
    CMD_SEC=`echo $CMD_CHECK | grep -i 'secretid'`
    if [ "x${CMD_CHECK}" == "x" ];then
        echo -e "${RED}cmd path error!Please check!$RESET\n"
        exit -1
    fi
}

function installToEnv()
{
    echo -e "Adding to Env(${VAR_ENV_FILE}) : \n"
    echo -e "\tTC_OPENCLOUD_SDK_PHP_HOME=${cmd_path}"
    echo -e "\tTC_OPENCLOUD_PHP_PATH=${php_path}"
	echo -e "\tTC_OPENCLOUD_ENDPOINT=${endpoint}"
    echo -e "\tTC_OPENCLOUD_SECRETID=${secret_id}"
    echo -e "\tTC_OPENCLOUD_SECRETKEY=${secret_key}"
    echo -e '\tPATH=$PATH:$TC_OPENCLOUD_SDK_PHP_HOME/bin:$TC_OPENCLOUD_PHP_PATH'
	echo -e '\nInstallation will write your SecretId and SecretKey to the Environment Variables. Are you sure you wish to continue? (y/n)'
	read ANSWER;
	if [ "x${ANSWER}" != "xy" -a "x${ANSWER}" != "xY" ];then
		echo -e "${YELLOW}Install Cancel!$RESET\n"
		exit -1
	fi
    
    checkPara
    echo -e "\nNOTICE: Please run ${YELLOW}'source /etc/profile'$RESET after install!\n"
	updateEnv
}

function installTemporary()
{
	echo -e "Install temporarily : \n"
    echo -e "\tTC_OPENCLOUD_SDK_PHP_HOME=${cmd_path}"
    echo -e "\tTC_OPENCLOUD_PHP_PATH=${php_path}"
	echo -e "\tTC_OPENCLOUD_ENDPOINT=${endpoint}"
    echo -e "\tTC_OPENCLOUD_SECRETID=${secret_id}"
    echo -e "\tTC_OPENCLOUD_SECRETKEY=${secret_key}"
    echo -e '\tPATH=$PATH:$TC_OPENCLOUD_SDK_PHP_HOME/bin:$TC_OPENCLOUD_PHP_PATH\n'
	checkPara
	export TC_OPENCLOUD_SDK_PHP_HOME=${cmd_path}
    export TC_OPENCLOUD_PHP_PATH=${php_path} 
	export TC_OPENCLOUD_ENDPOINT=${endpoint} 
    export TC_OPENCLOUD_SECRETID=${secret_id} 
    export TC_OPENCLOUD_SECRETKEY=${secret_key}
    PATH=$PATH:$TC_OPENCLOUD_SDK_PHP_HOME/bin:$TC_OPENCLOUD_PHP_PATH
}

function checkPath()
{
	cmd=`echo $cmd_path | cut -c ${#cmd_path}-`
	if [ "x${cmd}" == "x/" ] ; then
		echo -e "cmd_path don't need '/' in the end!\n"
		exit -1
	fi
	php=`echo $php_path | cut -c ${#php_path}-`
	if [ "x${php}" == "x/" ] ; then
		echo -e "php_path don't need '/' in the end!\n"
		exit -1
	fi	
}

echo -e "\nAdd sdk runtime-setup to enviroment\n"
checkPath

if [ "x${temporary_installation}" != "x" ] ; then
    installTemporary
else
	installToEnv
fi

chmod +x ${cmd_path}/lib/CloudCmd.class.php
chmod +x ${cmd_path}/lib/reconfirmation
chmod +x ${cmd_path}/bin/*
echo -e "${YELLOW}Install Success!$RESET\n"
cd ${CUR_DIR}




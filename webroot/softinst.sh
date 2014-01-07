#!/bin/sh
# Version 1.0
# Author alvahuang@tencent.com
PATH=/sbin:/usr/sbin:/usr/local/sbin:/bin:/usr/bin:/usr/local/bin
dir=$(dirname $(which $0))
script=`basename $0`
eth1=`/sbin/ifconfig eth1 | grep "inet addr:" | awk '{print $2}' | awk -F':' '{print $2}'`
day=`date +%Y%m%d`

if [ -f /etc/tlinux-release ];then
    OS=tlinux
elif [ -f /etc/centos-release ];then
    OS=centos
elif [ -f /etc/os-release ];then
    grep Ubuntu /etc/os-release > /dev/null
    [ $? -eq 0 ] && OS=ubuntu
elif [ -f /etc/SuSE-release ];then
    OS=SuSE
fi

if [ -z "$OS" ];then
echo "OS type error"
exit
fi

setup_tlinux()
{
for file in RPM-GPG-KEY-EPEL-6 RPM-GPG-KEY-rpmforge-dag epel.repo rpmforge.repo tlinux.repo;do
wget -q http://mirrors.tencentyun.com/install/tlinux/$file
if [ $? -ne 0 ];then
echo "Setup error, download fail"
exit
fi
done
rm /etc/yum.repos.d/*.repo
cp RPM-GPG-KEY-* /etc/pki/rpm-gpg/
cp *.repo /etc/yum.repos.d
yum clean all >/dev/null
echo "Setup ok"
}

setup_centos()
{
for file in RPM-GPG-KEY-EPEL-6 RPM-GPG-KEY-rpmforge-dag epel.repo rpmforge.repo centos.repo;do
wget -q http://mirrors.tencentyun.com/install/centos/$file
if [ $? -ne 0 ];then
echo "Setup error, download fail"
exit
fi
done
rm /etc/yum.repos.d/*.repo
cp RPM-GPG-KEY-* /etc/pki/rpm-gpg/
cp *.repo /etc/yum.repos.d
yum clean all >/dev/null
echo "Setup ok"
}

setup_ubuntu()
{
for file in sources.list;do
wget -q http://mirrors.tencentyun.com/install/ubuntu/$file
if [ $? -ne 0 ];then
echo "Setup error, download fail"
exit
fi
done
cp /etc/apt/sources.list /etc/apt/sources.list.`date +%F`
cp sources.list /etc/apt/
apt-get update
}

setup_suse()
{
echo "SuSE10"
#do nothing
}


mkdir -p /tmp/softinst
cd /tmp/softinst
rm /tmp/softinst/* 2>/dev/null

echo "$OS software install..."
if [ $OS = "tlinux" ];then
setup_tlinux
elif [ $OS = "centos" ];then
setup_centos
elif [ $OS = "ubuntu" ];then
setup_ubuntu
elif [ $OS = "SuSE" ];then
setup_suse
fi

[ -d /tmp/softinst ] && rm -rf /tmp/softinst*


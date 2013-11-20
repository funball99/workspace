#!/bin/bash

##author weisongli

##v1.0 fix PS1
echo "export PS1=\"[\u@\h \W]\\\\$ \"" >> /etc/profile

##v1.1 fix RC_ALL
#export LC_ALL="zh_CN.UTF-8"
echo "export LC_ALL=\"zh_CN.UTF-8\"" >> /etc/profile

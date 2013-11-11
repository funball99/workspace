#!/bin/bash

##author weisongli

##v1.0 fix PS1
echo "export PS1=\"\[\e[32m\][\u@\h \W]\\\\$\[\e[m\] \"" >> /etc/profile

##v1.1 fix RC_ALL
#export LC_ALL="zh_CN.UTF-8"
echo "export LC_ALL=\"zh_CN.UTF-8\"" >> /etc/profile

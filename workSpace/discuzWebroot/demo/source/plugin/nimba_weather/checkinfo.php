<?php
/*
 * �����޸ı�ҳ�κ����ݣ��������Ը���
 * ���� ���������� �˹�����ʵ���� ��Ʒ��Made By Nimba, Team From AiLab.cn)
 */
$info=array();
$info['name']='nimba_weather';
$info['version']='v1.5.1';
require_once DISCUZ_ROOT.'./source/discuz_version.php';
$info['siteversion']=DISCUZ_VERSION;
$info['siterelease']=DISCUZ_RELEASE;
$info['timestamp']=TIMESTAMP;
$info['nowurl']=$_G['siteurl'];
$info['siteurl']='http://discuz.gz.1251000002.clb.myqcloud.com/';
$info['clienturl']='http://discuz.gz.1251000002.clb.myqcloud.com/';
$info['siteid']='89A46E8D-04DC-8F65-86AF-5F63E9AEEDCB';
$info['sn']='2013120814TybXYayBXR';
$info['adminemail']=$_G['setting']['adminemail'];
$infobase=base64_encode(serialize($info));
?>
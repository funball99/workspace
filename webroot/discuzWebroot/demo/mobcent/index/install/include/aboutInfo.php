<?php
/**
 * ������Ϣ
 * 
 */
class aboutInfo
{
	private static $_instance = '';
	private static $_username = '';
	private function __construct()
	{

	}
	//����__clone������ֹ���󱻸��ƿ�¡
	private  function  __clone()
	{
		trigger_error('Clone is not allow', E_USER_ERROR);
	}
	
	public static function getInstance()
	{
		if(!self::$_instance instanceof  self)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	public static function ChangeValue($user)
	{
		self::$_username = $user['name'];
	}
	public static function getAllValue()
	{
		$_info = array();
		$_info['username'] = self::$_username;
		return $_info;
	}
}


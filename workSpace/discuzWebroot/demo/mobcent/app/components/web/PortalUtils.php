<?php

/**
 * 文章相关工具类
 *
 * @author 任兴
 */
class PortalUtils
{
	public static function getNewsInfo($aid)
	{
		return DbUtils::getDiscuzCommand()
		->select('*')
		->from('{{portal_article_title}}')
		->where('aid=:aid', array(':aid' => $aid))
		->queryRow();
	}
	
	
	
	/*
	 * 门户 2013年10月30日
	 */
	public static function getPicList()
	{
		return DbUtils::getDiscuzCommand()
		->select('*')
		->from('{{add_portal_module}}')
		->where('isimage=:isimage', array(':isimage' => '1'))
		->order('display DESC')
		->limit(5)
		->queryAll();
	}
	 
}
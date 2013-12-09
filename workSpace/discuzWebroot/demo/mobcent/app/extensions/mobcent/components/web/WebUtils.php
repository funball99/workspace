<?php

class WebUtils {

	public static function initWebApiArray() {
		return array(
			'head' => array(
				'errCode' => 0,
				'errInfo' => '',
			),
			'body' => array(
				'externInfo' => array(),
			),
		);
	}

	/**
	 * 返回兼容老版本的分页格式
	 */
	public static function getWebApiArrayWithPage_oldVersion($page, $pageSize, $count)
	{
		$res = array();
		$res['page'] = (int)$page;
        $res['has_next'] = $count > $page * $pageSize ? 1 : 0; 
        $res['total_num'] = (int)$count;

        return $res;
	}

	public static function jsonEncode($var, $charset='') {
		$oldCharset = Yii::app()->charset;
		if ($charset != '') {
			Yii::app()->charset = $charset;
		}
		
		$res = CJSON::encode($var);

		Yii::app()->charset = $oldCharset;

		return $res;
	}

	public static function jsonDecode($str, $useArray=true) {
		return CJSON::decode($str, $useArray);
	}

	public static function subString($str, $start, $length=100) {
		return mb_substr($str, $start, $length, Yii::app()->charset);
	}

	public static function replaceLineMark($str) {
		return str_replace("\n", '\\n', $str);
	}

	public static function emptyReturnLine($str) {
		$str = str_replace("\r", '', $str);
		$str = str_replace("\n", '', $str);
		return $str;
	}
}
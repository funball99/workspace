<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_nimba_weather {
	function global_cpnav_extra1() {
		loadcache('plugin');
		global $_G;
		$return='';
		$var = $_G['cache']['plugin']['nimba_weather'];
		$open=$var['poen'];
		$style=$var['style'];
		if($open==1){
			$cno=array('日','一','二','三','四','五','六'); 
			$date="<div id=\"weather\" style=\"line-height: 22px;float:left;padding-right:8px;\">".date('Y年n月j日').' 星期'.$cno[date('w')]."</div>";
			if(strtolower(CHARSET) == 'utf-8') $date=$this->auto_charset($date,'GBK', 'UTF-8');//转utf-8
			$weather="<div id=\"weather\" style=\"float:left;\"><script>if(top.location == self.location){document.write('<iframe width=\"450\" height=\"22\" frameborder=\"0\" scrolling=\"no\" allowtransparency=\"true\" src=\"http://i.tianqi.com/index.php?c=code&id=1&icon=1&wind=1&num=2\"></iframe>')} </script> </div>";
			if($style==1) $return=$date.$weather;
			if($style==2) $return=$weather;
		}
		return $return;
	}	

	function auto_charset($fContents, $from='gbk', $to='utf-8'){
		$from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
		$to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
		if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
			return $fContents;
		}
		if (is_string($fContents)) {
			if (function_exists('mb_convert_encoding')) {
				return mb_convert_encoding($fContents, $to, $from);
			} elseif (function_exists('iconv')) {
				return iconv($from, $to, $fContents);
			} else {
				return $fContents;
			}
		} elseif (is_array($fContents)) {
			foreach ($fContents as $key => $val) {
				$_key = auto_charset($key, $from, $to);
				$fContents[$_key] = auto_charset($val, $from, $to);
				if ($key != $_key)
					unset($fContents[$key]);
			}
			return $fContents;
		}
		else {
			return $fContents;
		}
		}
}

class plugin_nimba_weather_forum extends plugin_nimba_weather{ 
} 
 ?>
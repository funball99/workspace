<?php if (!defined('THINK_PATH')) exit();?><!--矩形广告位--><?php if(is_array($ad_list)): $i = 0; $__LIST__ = $ad_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ad): $mod = ($i % 2 );++$i; echo ($ad["html"]); endforeach; endif; else: echo "" ;endif; ?>
<?php if (!defined('THINK_PATH')) exit();?><!doctype html><html><head><meta charset="utf-8" /><title><?php echo ($page_seo["title"]); ?> - Powered by PinPHP</title><meta name="keywords" content="<?php echo ($page_seo["keywords"]); ?>" /><meta name="description" content="<?php echo ($page_seo["description"]); ?>" /><link rel="stylesheet" type="text/css" href="__STATIC__/css/default/base.css" /><link rel="stylesheet" type="text/css" href="__STATIC__/css/default/style.css" /><script src="__STATIC__/js/jquery/jquery.js"></script><link rel="stylesheet" type="text/css" href="__STATIC__/css/default/space.css" /></head><body><!--头部开始--><div class="header_wrap pt10"><div id="J_m_head" class="m_head clearfix"><div class="head_logo fl"><a href="__ROOT__/" class="logo_b fl" title="<?php echo C('pin_site_name');?>"><?php echo C('pin_site_name');?></a></div><div class="head_user fr"><?php if(!empty($visitor)): ?><ul class="head_user_op"><li class="mr10"><a class="J_shareitem_btn share_btn" href="javascript:;" title="<?php echo L('share');?>"><?php echo L('share');?></a></li><li class="J_down_menu_box mb_info pos_r"><a href="<?php echo U('space/index', array('uid'=>$visitor['id']));?>" class="mb_name"><img class="mb_avt r3" src="<?php echo avatar($visitor['id'], 24);?>"><?php echo ($visitor["username"]); ?></a><ul class="J_down_menu s_m pos_a"><li><a href="<?php echo U('space/index');?>"><?php echo L('cover');?></a></li><li><a href="<?php echo U('user/index');?>"><?php echo L('personal_settings');?></a></li><li><a href="<?php echo U('user/bind');?>"><?php echo L('user_bind');?></a></li><li><a href="<?php echo U('user/logout');?>"><?php echo L('logout');?></a></li></ul></li><li><a class="libg feed" href="<?php echo U('space/me');?>"><?php echo L('feed');?></a></li><li><a class="libg album" href="<?php echo U('space/album');?>"><?php echo L('album');?></a></li><li><a class="libg like" href="<?php echo U('space/like');?>"><?php echo L('like');?></a></li><li class="J_down_menu_box my_shotcuts pos_r"><a class="libg msg" href="javascript:;"><?php echo L('message');?><span id="J_msgtip"></span></a><ul class="J_down_menu s_m n_m pos_a"><li><a href="<?php echo U('space/atme');?>"><?php echo L('talk');?><span id="J_atme"></span></a></li><li><a href="<?php echo U('message/index');?>"><?php echo L('my_msg');?><span id="J_msg"></span></a></li><li><a href="<?php echo U('message/system');?>"><?php echo L('system_msg');?><span id="J_system"></span></a></li><li><a href="<?php echo U('space/fans');?>"><?php echo L('my_fans');?><span id="J_fans"></span></a></li></ul></li></ul><?php else: ?><ul class="login_mod"><li class="local fl"><a href="<?php echo U('user/register');?>"><?php echo L('register');?></a><a href="<?php echo U('user/login');?>"><?php echo L('login');?></a></li><li class="other_login fl"><?php if(is_array($oauth_list)): $i = 0; $__LIST__ = $oauth_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><a href="<?php echo U('oauth/index', array('mod'=>$val['code']));?>" class="login_bg weibo_login"><img src="__STATIC__/images/oauth/<?php echo ($val["code"]); ?>/icon.png" /><?php echo ($val["name"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?></li></ul><?php endif; ?></div></div><div id="J_m_nav" class="clearfix"><ul class="nav_list fl"><li <?php if($nav_curr == 'index'): ?>class="current"<?php endif; ?>><a href="__ROOT__/"><?php echo L('index_page');?></a></li><?php $tag_nav_class = new navTag;$data = $tag_nav_class->lists(array('type'=>'lists','style'=>'main','cache'=>'0','return'=>'data',)); if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><li class="split <?php if($nav_curr == $val['alias']): ?>current<?php endif; ?>"><a href="<?php echo ($val["link"]); ?>" <?php if($val["target"] == 1): ?>target="_blank"<?php endif; ?>><?php echo ($val["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?><li class="top_search"><form action="__ROOT__/" method="get" target="_blank"><input type="hidden" name="m" value="search"><input type="text" autocomplete="off" def-val="<?php echo C('pin_default_keyword');?>" value="<?php echo C('pin_default_keyword');?>" class="ts_txt fl" name="q"><input type="submit" class="ts_btn" value="<?php echo L('search');?>"></form></li></ul></div></div><div class="main_wrap"><div class="pt25"><div class="space_top mb25"><div class="space_info clearfix"><?php if($user['cover'] != '' AND ACTION_NAME == 'index'): ?><div class="space_cover" style="height:190px;background-image:url(<?php echo attach($user['cover'], 'cover');?>);"><div class="iwc_ct"><h1><?php echo ($user["username"]); ?></h1></div></div><?php else: ?><div class="left fl"><a href="<?php echo U('space/index', array('uid'=>$user['id']));?>" target="_blank"><img src="<?php echo avatar($user['id'], 100);?>" alt="" class="avatar fl r5"></a><div class="user_profile fl"><span class="uname"><?php echo ($user["username"]); ?></span><br><div class="home_follow"><?php if($visitor['id'] == $user['id']): ?><div class="see_more_info fl"><a target="_blank" href="<?php echo U('space/info');?>"><?php echo L('user_info');?></a>(<a target="_blank" href="<?php echo U('user/index');?>"><?php echo L('setting');?></a>)</div><?php else: ?><div class="J_follow_bar fl" data-uid="<?php echo ($user["id"]); ?>"><?php switch($user["ship"]): case "0": ?><a href="javascript:;" class="J_fo_u fo_u_btn"><?php echo L('follow');?></a><?php break; case "1": ?><span class="fo_u_ok"><?php echo L('followed');?></span><a href="javascript:;" class="J_unfo_u green"><?php echo L('cancel');?></a><?php break; case "2": ?><span class="fo_u_all"><?php echo L('follow_mutually');?></span><a href="javascript:;" class="J_unfo_u green"><?php echo L('cancel');?></a><?php break; endswitch;?></div><div class="see_more_info fl ml10"><a href="<?php echo U('space/info', array('uid'=>$user['id']));?>"><?php echo L('see_user_info');?></a></div><?php endif; ?></div></div></div><div class="right fr"><div class="collect_list"><a href="<?php echo U('space/follow', array('uid'=>$user['id']));?>" class="ft18"><?php echo ($user["follows"]); ?></a><br><span><?php echo L('follow');?></span></div><div class="collect_list"><a href="<?php echo U('space/fans', array('uid'=>$user['id']));?>" class="ft18"><?php echo ($user["fans"]); ?></a><br><span><?php echo L('fans');?></span></div><div class="collect_list bd_none"><a class="ft18"><?php echo ($user["likes"]); ?></a><br><span><?php echo L('belike');?></span></div></div><?php endif; ?></div><div class="space_nav"><?php if(is_array($space_nav_list)): $i = 0; $__LIST__ = $space_nav_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$nav): $mod = ($i % 2 );++$i;?><a <?php if($key == $space_nav_curr): ?>class="current"<?php endif; ?> href="<?php echo ($nav["url"]); ?>"><?php echo ($nav["text"]); ?></a><?php endforeach; endif; else: echo "" ;endif; if($visitor['id'] == $user['id']): ?><a class="cus_cover fr" href="<?php echo U('user/custom');?>"><?php echo L('custom_cover');?></a><?php endif; ?></div></div></div><div class="followfans_main"><div class="followfans_info clearfix"><h1 class="fl"><?php if($visitor['id'] == $user['id']): echo L('me'); else: ?>Ta<?php endif; echo L('space_fans_title');?></h1></div><ul class="space_tab clearfix"><li <?php if($tab_current == 'follow'): ?>class="current"<?php endif; ?>><a href="<?php echo U('space/follow', array('uid'=>$user['id']));?>"><?php if($visitor['id'] == $user['id']): echo L('me'); else: ?>Ta<?php endif; echo L('space_follow_title');?></a></li><li <?php if($tab_current == 'fans'): ?>class="current"<?php endif; ?>><a href="<?php echo U('space/fans', array('uid'=>$user['id']));?>"><?php if($visitor['id'] == $user['id']): echo L('me'); else: ?>Ta<?php endif; echo L('space_fans_title');?></a></li></ul><ul class="people_list clearfix"><?php if(is_array($user_list)): $i = 0; $__LIST__ = $user_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><li class="J_pl clearfix" data-uid="<?php echo ($val["id"]); ?>"><a href="<?php echo U('space/index', array('uid'=>$val['id']));?>"><img class="J_card fl r5" src="<?php echo avatar($val['id'], 48);?>" data-uid="<?php echo ($val["id"]); ?>" /></a><div class="people_info"><a class="n gc" href=""><?php echo ($val["username"]); ?></a><br><span><?php echo L('fans'); echo ($val["fans"]); ?>人</span></div><?php if($visitor['id'] == $user['id']): ?><div class="people_toolbar"><div class="J_follow_bar" data-uid="<?php echo ($val["id"]); ?>"><?php if($val['mutually'] == 1): ?><span class="fo_u_all"><?php echo L('follow_mutually');?> | </span><a href="javascript:;" class="J_unfo_u green"><?php echo L('cancel');?></a><?php else: ?><a href="javascript:;" class="J_fo_u fo_u_btn fr mb3"><?php echo L('follow');?></a><?php endif; ?></div><div class="J_fans_op fans_op hide"><a href="javascript:;" class="J_delfans fr" data-uid="<?php echo ($val["id"]); ?>"><?php echo L('delete_fans');?></a><br></div></div><?php endif; ?></li><?php endforeach; endif; else: echo "" ;endif; ?></ul></div></div><div class="w960"><?php echo R('advert/index', array(11), 'Widget');?></div><div class="footer_wrap rt10"><a href="__APP__" class="foot_logo"></a><div class="foot_links clearfix"><dl class="foot_nav fl"><dt><?php echo L('site_nav');?></dt><?php $tag_nav_class = new navTag;$data = $tag_nav_class->lists(array('type'=>'lists','style'=>'bottom','cache'=>'0','return'=>'data',)); if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><dd><a href="<?php echo ($val["link"]); ?>" <?php if($val["target"] == 1): ?>target="_blank"<?php endif; ?>><?php echo ($val["name"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; ?></dl><dl class="aboutus fl"><dt><?php echo L('aboutus');?></dt><?php $tag_article_class = new articleTag;$data = $tag_article_class->cate(array('type'=>'cate','cateid'=>'1','cache'=>'0','return'=>'data',)); if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><dd><a href="<?php echo U('aboutus/index', array('id'=>$val['id']));?>" target="_blank"><?php echo ($val["name"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; ?></dl><dl class="flinks fr"><dt><?php echo L('flink');?></dt><?php $data = S('36cd2015820ec8da2a165ad5dfc0c797');if (false === $data) { $tag_flink_class = new flinkTag;$data = $tag_flink_class->lists(array('cache'=>'3600','num'=>'5','return'=>'data','type'=>'lists',));S('36cd2015820ec8da2a165ad5dfc0c797', $data, 3600); } if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><dd><a href="<?php echo ($val["url"]); ?>" target="_blank"><?php echo ($val["name"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; ?><dd><a href="<?php echo U('aboutus/flink');?>" class="more" target="_blank"><?php echo L('more');?>...</a></dd></dl><?php echo R('advert/index', array(8), 'Widget');?></div><p class="pt20">Powered by <a href="http://www.pinphp.com/" class="tdu clr6" target="_blank">PinPHP <?php echo (PIN_VERSION); echo (PIN_RELEASE); ?></a> &copy;Copyright 2010-2012 <a href="__ROOT__/" class="tdu clr6" target="_blank"><?php echo C('pin_site_name');?></a> (<a href="http://www.miibeian.gov.cn" class="tdu clr6" target="_blank"><?php echo C('pin_site_icp');?></a>)<?php echo C('pin_statistics_code');?></p></div><div id="J_returntop" class="return_top"></div><script>var PINER = {
    root: "__ROOT__",
    uid: "<?php echo $visitor['id'];?>", 
    async_sendmail: "<?php echo $async_sendmail;?>",
    config: {
        wall_distance: "<?php echo C('pin_wall_distance');?>",
        wall_spage_max: "<?php echo C('pin_wall_spage_max');?>"
    },
    //URL
    url: {}
};
//语言项目
var lang = {};
<?php $_result=L('js_lang');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>lang.<?php echo ($key); ?> = "<?php echo ($val); ?>";<?php endforeach; endif; else: echo "" ;endif; ?></script><?php $tag_load_class = new loadTag;$data = $tag_load_class->js(array('type'=>'js','href'=>'__STATIC__/js/jquery/plugins/jquery.tools.min.js,__STATIC__/js/jquery/plugins/jquery.masonry.js,__STATIC__/js/jquery/plugins/formvalidator.js,__STATIC__/js/fileuploader.js,__STATIC__/js/pinphp.js,__STATIC__/js/front.js,__STATIC__/js/dialog.js,__STATIC__/js/wall.js,__STATIC__/js/item.js,__STATIC__/js/user.js,__STATIC__/js/album.js','cache'=>'0','return'=>'data',));?><script src="__STATIC__/js/space.js"></script></body></html>
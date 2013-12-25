<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF


EOF;
 if($kl3w_guisepost_type!='floatpost') { 
$return .= <<<EOF

<script type="text/javascript" reload="1">
function select_kl3w_guisepost(obj,uid){

EOF;
 if($kl3w_guisepost_type=='toppost') { 
$return .= <<<EOF

var data = loadUserdata('forum_'+discuz_uid);data = data==null?'':data;
if(!in_array((data = trim(data)), ['', 'null', 'false', null, false])) {
var i=data.indexOf('kl3wguisepostuserid'+String.fromCharCode(9)+'INPUT');
if(i!=-1)data = data.replace('kl3wguisepostuserid'+String.fromCharCode(9)+'INPUT','kl3wguisepostuserid_tmp'+String.fromCharCode(9)+'INPUT');
saveUserdata('forum_'+discuz_uid, data);
}

EOF;
 } 
$return .= <<<EOF

var html = obj.innerHTML;
if(uid)html += '<input name="kl3wguisepostuserid" type="hidden" value="'+uid+'" />';
$('kl3w_guisepost').innerHTML = html; 
}
</script><style type="text/css">
#kl3w_guisepost_menu{background:#EEE;width:582px;padding:8px 4px 4px;}.kl3w_guisepost{padding:0px;font:12px Arial, Helvetica, sans-serif;line-height:12px;}
.kl3w_guisepost_box{position:relative;border:1px solid #B5CFD9;background:#fff;overflow:hidden;width:570px;
EOF;
 if($guiseuidcount>15) { 
$return .= <<<EOF
height:316px;
EOF;
 } 
$return .= <<<EOF
padding:5px 5px 5px 5px;text-align: left;margin:8px 0;overflow-y:scroll;}
.kl3w_guisepost a{position:relative;background:#fafafa;float:left;margin:0;padding:0;height:34px;width:100px;overflow:hidden;font:12px Arial, Helvetica, sans-serif;line-height:12px;text-decoration:none;border:1px solid #EEE;}
.kl3w_guisepost dl{padding:2px;margin:0;cursor:pointer; text-align:left;height:34px;width:100px;}.kl3w_guisepost img{float:left;padding:0;margin:2px 4px 0 0;width:26px;height:26px;}
.kl3w_guisepost dd{width:64px;height:14px;overflow:hidden;margin:0;padding:1px 0 0;line-height:14px;}#kl3w_guisepost_menu a{margin:0 6px 4px 0;}
.kl3w_guisepost dt{z-index:9999;display:block;width:16px;height:14px;top:14px;left:18px;background:url('source/plugin/{$this->identifier}/template/new_pm.png'); position:absolute;}
.kl3w_guisepost em{padding:0;margin:0;color:#CCC;line-height:12px;font-variant:normal;font-weight:normal;}.kl3w_guisepost a:hover{text-decoration:none; background:#eaeaea;}
#kl3w_guisepost dl{background:#fafafa;height:34px;width:100px;}#kl3w_guisepost dl.me{background:#FFFFE8;}.kl3w_guise dl.me{background:#FFFFE8;}
</style>

EOF;
 } 
$return .= <<<EOF

<div class="kl3w_guisepost cl">
EOF;
 if($kl3w_guisepost_type!='threadtop') { 
$return .= <<<EOF

<a id="kl3w_guisepost" 
EOF;
 if($this->vars['showmod']=='2') { 
$return .= <<<EOF
onclick
EOF;
 } else { 
$return .= <<<EOF
onmouseover
EOF;
 } 
$return .= <<<EOF
="$('kl3w_guisepost').id = 'kl3w_guisepost_tmp';this.id = 'kl3w_guisepost';showMenu({'ctrlid':this.id})">
<dl title="UID:{$GLOBALS['_G']['uid']}" class="me"><dd style="color:#000">&nbsp;&#x9ED8;&#x8BA4;&#x81EA;&#x5DF1;</dd>
<dd><em>&nbsp;&#x8BF7;
EOF;
 if($this->vars['showmod']=='2') { 
$return .= <<<EOF
&#x70b9;&#x51fb;
EOF;
 } 
$return .= <<<EOF
&#x9009;&#x62E9;&#x9A6C;&#x7532;</em></dd></dl></a><div style="clear:both"></div>
EOF;
 } if($kl3w_guisepost_type!='floatpost') { 
$return .= <<<EOF
<div id="kl3w_guisepost_menu" class="p_pop cl" style="display: none;"><strong>&#x5171;<span style="color:red">{$guiseuidcount}</span>&#x4E2A;&#x53EF;&#x7528;&#x865A;&#x62DF;&#x9A6C;&#x7532;
(&#x8BF7;&#x70B9;&#x51FB;&#x4E0B;&#x9762;&#x4F1A;&#x5458;&#x5373;&#x53EF;&#x9009;&#x4E3A;&#x9A6C;&#x7532;&#x7528;&#x6237;&#x53D1;&#x8868;)</strong><div class="kl3w_guisepost_box">
<div class="cl"><a class="kl3w_guise" onclick="select_kl3w_guisepost(this,'')"><dl title="UID:{$GLOBALS['_G']['uid']}" class="me"><dd style="color:#000">&nbsp;&#x9ED8;&#x8BA4;&#x81EA;&#x5DF1;</dd>
<dd><em>&nbsp;&#x8BF7;
EOF;
 if($this->vars['showmod']=='2') { 
$return .= <<<EOF
&#x70b9;&#x51fb;
EOF;
 } 
$return .= <<<EOF
&#x9009;&#x62E9;&#x9A6C;&#x7532;</em></dd></dl></a>
EOF;
 if(is_array($input)) foreach($input as $vo) { 
$return .= <<<EOF
<a onclick="select_kl3w_guisepost(this,'{$vo['uid']}')"><dl title="UID:{$vo['uid']}({$vo['username']})">
EOF;
 if($vo['newpm']||$vo['newprompt']) { 
$return .= <<<EOF
<dt title="&#x6709;{$vo['newprompt']}&#x4e2a;&#x63d0;&#x9192;&#x672a;&#x8bfb;">&nbsp;</dt>
EOF;
 } 
$return .= <<<EOF

{$vo['avatar']}<dd style="color:#{$gender_arr[$vo['uid']]}">{$vo['username']}</dd><dd><em>{$lastpost_arr[$vo['uid']]}</em></dd></dl></a>
EOF;
 } 
$return .= <<<EOF
</div></div>
<span class="xg1 y">Plugin by www.kl3w.com(&#x4F60;&#x53EF;&#x514D;&#x8D39;&#x4F7F;&#x7528;&#xFF0C;&#x4F46;&#x8BF7;&#x4FDD;&#x7559;&#x7248;&#x6743;)</span></div>

EOF;
 } 
$return .= <<<EOF

</div>

EOF;
?>
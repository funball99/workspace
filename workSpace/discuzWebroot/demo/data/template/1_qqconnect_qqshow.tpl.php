<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<div class="mtw bm2 cl">
<?php if($_G['member']['conisbind']) { ?>
<div class="bm2_b bw0 hm" style="padding-top: 10px;">
<?php echo $qqshow;?>

<form action="home.php?mod=spacecp&amp;ac=plugin&amp;id=qqconnect:qqshow&amp;connectsubmit=yes" id="qqshowform" method="post" autocomplete="off" onsubmit="ajaxpost('qqshowform', 'return_qqshowform', 'return_qqshowform');return false;">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
<div class="mtm">
<?php if(!$_G['member']['conisqqshow']) { ?>
<input type="hidden" name="do" value="open" />
<button type="submit" name="connectsubmit" value="yes" class="pn pnc"><strong>使用QQ秀形象</strong></button>
<?php } else { ?>
<input type="hidden" name="do" value="close" />
<button type="submit" name="connectsubmit" value="yes" class="pn pnc"><strong>取 消</strong></button>
<p class="mtm">
<a href="http://show.qq.com/?from=discuz" target="_blank" class="xi2">免费获取精美QQ秀</a>
</p>
<?php } ?>
</div>
</form>
<span id="return_qqshowform"></span>
</div>
<?php } else { ?>
<div class="bm2_b bw0 hm" style="padding-top: 40px;">
<a href="<?php echo $_G['connect']['loginbind_url'];?>&isqqshow=yes"><img src="<?php echo IMGDIR;?>/qq_bind.gif" /></a>
<p class="mtn xg1">点击按钮，立刻绑定QQ帐号</p>
</div>
<?php } ?>
<div class="bm2_b bm2_b_y bw0">
<dl class="xld">
<h2 class="xi1 xs2">在社区使用我的QQ秀</h2>
<dt></dt>
<dd class="xg1">开启后，论坛帖子左侧和个人空间首页将展示您的QQ秀，并且社区内QQ秀形象将随您的QQ秀形象更新而同步变化</dd>
<dt><?php if(!$_G['member']['conisqqshow']) { ?>您需要绑定QQ帐号才能在社区使用QQ秀形象<?php } ?></dt>
<dd class="xg1"></dd>
</dl>
</div>
</div>

<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<style type="text/css">
.gmushnh {min-height:280px;}
.avt { margin:0 20px 10px 0}
.genee_mushroom_list li{ float:left; padding:3px; width:200px; overflow:hidden;}
.genee_mushroom_list .avt { margin:0 10px 0 0}
.genee_mushroom_list .index { font-size:15px; font-family:'Georgia'; width:25px; color:#D26900}
</style>

<div class="bm_c gmushnh genee_mushroom_list cl" >
<ul class="cl"><?php if(is_array($mgdr)) foreach($mgdr as $key => $value) { ?><li ><span class="z index"><?php echo $value['top'];?></span><div class="avt z"><a href="home.php?mod=space&amp;uid=<?php echo $value['uid'];?>" target="_blank" ><?php echo avatar($value['uid'],small);?></a></div><a href="home.php?mod=space&amp;uid=<?php echo $value['uid'];?>" target="_blank" ><?php echo $value['username'];?></a><br>次数累计：<?php echo $value['ljnum'];?><BR>积分累计：<?php echo $value['credit'];?></li>
<?php } ?>
</ul>
</div>
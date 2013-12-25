<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
global $_GET;
global $rows;

$youruid = $_G['uid'];
$whoareyou = $_G['username'];
$sayavoiceinfo = DB::fetch_first("SELECT * FROM ".DB::table('common_member')." WHERE uid=".$youruid."");


$limitone=DB::query("select * from ".DB::table('saya_mails')."");
$maxnum = DB::num_rows($limitone);
$maxnum = intval($maxnum);

if ($_GET['change'] != 'yes' and $_GET['upload'] != 'yes'){
	if($sayavoiceinfo['saya_hitvoice']==0){$hitcheck="checked=\"checked\"";}else{$hitcheck='';}
if($sayavoiceinfo['saya_pmvoice']==0){$pmcheck="checked=\"checked\"";}else{$pmcheck='';}
for($i=0;$i<=$maxnum;$i++){
 	$hitchecked='';
	$pmchecked='';
	$sayamusicinfo = DB::fetch_first("SELECT * FROM ".DB::table('saya_mails')." WHERE musicid=".$i."");
	if($i%2==0){$bg="bgcolor=\"#F7EEF7\"";}else{$bg='';}
			if($sayavoiceinfo['saya_hitvoice']==$i){$hitchecked="checked=\"checked\"";}
			if($sayavoiceinfo['saya_pmvoice']==$i){$pmchecked="checked=\"checked\"";}
			if(!$sayamusicinfo['name']){$row='';}else{
				$musicname=$sayamusicinfo['name'];
	$row="<tr align=\"center\" ".$bg.">
          <td>".$musicname."</td>
          <td><input type=\"radio\" name=\"hit_mails\" ".$hitchecked."value=\"".$i."\" />
          </td>
          <td><input type=\"radio\" name=\"pm_mails\" ".$pmchecked."value=\"".$i."\" /></td>
        </tr>";}
	$rows=$rows.$row;
}

}


if ($_GET['change'] == "yes"){
	if(!submitcheck($_POST['formhash1'])){
	$pmvoice = $_POST['pm_mails'];
	$hitvoice = $_POST['hit_mails'];
	DB::update('common_member', array(
				'saya_pmvoice'=>$pmvoice,
				'saya_hitvoice'=>$hitvoice
			), "uid=".$youruid."");
			
			$youruid = $_G['uid'];
$whoareyou = $_G['username'];
$sayavoiceinfo = DB::fetch_first("SELECT * FROM ".DB::table('common_member')." WHERE uid=".$youruid."");

if($sayavoiceinfo['saya_hitvoice']==0){$hitcheck="checked=\"checked\"";}else{$hitcheck='';}
if($sayavoiceinfo['saya_pmvoice']==0){$pmcheck="checked=\"checked\"";}else{$pmcheck='';}
$limitone=DB::query("select * from ".DB::table('saya_mails')."");
$maxnum = DB::num_rows($limitone);
$maxnum = intval($maxnum);


for($i=0;$i<=$maxnum;$i++){
 	$hitchecked='';
	$pmchecked='';
	$sayamusicinfo = DB::fetch_first("SELECT * FROM ".DB::table('saya_mails')." WHERE musicid=".$i."");
	if($i%2==0){$bg="bgcolor=\"#F7EEF7\"";}else{$bg='';}
			if($sayavoiceinfo['saya_hitvoice']==$i){$hitchecked="checked=\"checked\"";}
			if($sayavoiceinfo['saya_pmvoice']==$i){$pmchecked="checked=\"checked\"";}
			if(!$sayamusicinfo['name']){$row='';}else{
				$musicname=$sayamusicinfo['name'];
	$row="<tr align=\"center\" ".$bg.">
          <td>".$musicname."</td>
          <td><input type=\"radio\" name=\"hit_mails\" ".$hitchecked."value=\"".$i."\" />
          </td>
          <td><input type=\"radio\" name=\"pm_mails\" ".$pmchecked."value=\"".$i."\" /></td>
        </tr>";}
	$rows=$rows.$row;
}
$succed= lang('plugin/saya_mails', 'succed');

}
}

if ($_GET['upload'] == 'yes'){

$fileTypes  = array('mp3','wav');
$result     = null;
$uploadDir  = './mail';
if(!submitcheck($_POST['formhash2'])){
if($_POST['upname']==''){
	$result=lang('plugin/saya_mails', 'noname');}
	else{
    $myfile = $_FILES['myfile'];
    $myfileType = substr($myfile['name'], strrpos($myfile['name'], ".") + 1);

    if ($myfile['size'] > 5000000) {
        $result = lang('plugin/saya_mails', 'big');
    } else if (!in_array($myfileType, $fileTypes)) {
        $result = lang('plugin/saya_mails', 'type');
    } elseif (is_uploaded_file($myfile['tmp_name'])) {
        $toFile = './source/plugin/saya_mails/mail/' . $myfile['name'];
        if (@move_uploaded_file($myfile['tmp_name'], $toFile)) {
			$end=0;
            $result = lang('plugin/saya_mails', 'success');
        } else {
            $result = lang('plugin/saya_mails', 'unknow');
        }
    } else {
        $result = lang('plugin/saya_mails', 'big');
	}
	if($end == 0){
	$updir=$myfile['name'];
	$upname=$_POST['upname'];
	$who=$whoareyou;
	DB::insert('saya_mails',array(
				'dir'=>$updir,
				'name'=>$upname,
				'who'=>$who
			));
			
$youruid = $_G['uid'];
$whoareyou = $_G['username'];
$sayavoiceinfo = DB::fetch_first("SELECT * FROM ".DB::table('common_member')." WHERE uid=".$youruid."");


$limitone=DB::query("select * from ".DB::table('saya_mails')."");
$maxnum = DB::num_rows($limitone);
$maxnum = intval($maxnum);


	if($sayavoiceinfo['saya_hitvoice']==0){$hitcheck="checked=\"checked\"";}else{$hitcheck='';}
if($sayavoiceinfo['saya_pmvoice']==0){$pmcheck="checked=\"checked\"";}else{$pmcheck='';}
for($i=0;$i<=$maxnum;$i++){
 	$hitchecked='';
	$pmchecked='';
	$sayamusicinfo = DB::fetch_first("SELECT * FROM ".DB::table('saya_mails')." WHERE musicid=".$i."");
	if($i%2==0){$bg="bgcolor=\"#F7EEF7\"";}else{$bg='';}
			if($sayavoiceinfo['saya_hitvoice']==$i){$hitchecked="checked=\"checked\"";}
			if($sayavoiceinfo['saya_pmvoice']==$i){$pmchecked="checked=\"checked\"";}
			if(!$sayamusicinfo['name']){$row='';}else{
				$musicname=$sayamusicinfo['name'];
	$row="<tr align=\"center\" ".$bg.">
          <td>".$musicname."</td>
          <td><input type=\"radio\" name=\"hit_mails\" ".$hitchecked."value=\"".$i."\" />
          </td>
          <td><input type=\"radio\" name=\"pm_mails\" ".$pmchecked."value=\"".$i."\" /></td>
        </tr>";}
	$rows=$rows.$row;
}

	}
	}
	}
	}
?>
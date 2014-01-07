<?php
/*
	[������׷����ظ�]Guisepost Plugin For Discuz! X1.0 - X3.1 ALL
	Copyring (C) KL3w.com; You can modify this plugin for your website
	This is not a freeware, use is subject to license terms
	Author: PGCAO��Version: 2.3.3��Time: 2013-10-26 14:10:41
*/
 
if(!defined('IN_DISCUZ')){exit('Access Denied');}

class plugin_kl3w_guisepost{
	protected $vars;							// ��Ų�����ñ���
	protected $uses;							// �������������
	protected $sysversion;						// ���İ汾��
	protected $identifier = 'kl3w_guisepost';   // �������ʶ

	function __construct(){
		$this->vars = $this->plugin_get_cache();
		$this->uses = $this->getuseid_uid($GLOBALS['_G']['uid']);
		$this->sysversion = str_replace("x",'',strtolower($GLOBALS['_G']['setting']['version']));
	}

	function plugin_kl3w_guisepost(){
		if(version_compare('5.0.0', PHP_VERSION, '>'))$this->__construct();
	}

	function plugin_get_cache(){
		$cache_plugin_var = $GLOBALS['_G']['cache']['plugin'][$this->identifier];
		if(empty($cache_plugin_var)){$cache_plugin_var = array('useid'=>'','open'=>0);}
		return $cache_plugin_var;
	}
	
	function getuseid_uid($uid){		
		if(!$uid)return array(-1, '', 0);$use_uid = -1;$useid_uid='';
		$rn = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? "\r\n" : "\n";
		$useidarr = explode($rn, $this->vars['useid']);
		if(is_array($useidarr)){
			foreach($useidarr as $key => $val){
				$useval = explode("=", str_replace('@','=',$val));
				if($uid == $useval[0] && isset($useval[1]) && !empty($useval[1])){
					$use_uid = intval(trim($useval[0]));
					$useid_uid = str_replace('��',',',$useval[1]);
					break;
				}
			}
		}
		return array($use_uid, $useid_uid, 0);
	}
	
	function getguisepostuserid($guisepost_type='post'){
		//_P($this->vars);
		
		$uid = intval($GLOBALS['_G']['uid']); //ȡ���û�UID
		if(!$uid || !$this->vars['open'] || $uid != $this->uses[0])return;
		$myguiseuid = ''; $myguiseuidarr = explode(",", $this->uses[1]);
		$kl3w_guisepost_type = $guisepost_type;
		foreach($myguiseuidarr as $val){
				$val = trim($val);
				if(!empty($val) && is_numeric($val)){
					$myguiseuid[] = $val;
				}
		}
		if($myguiseuid){
			$lastpost_arr = array();
			$query = DB::query("SELECT uid,lastpost FROM ".DB::table('common_member_status')." WHERE uid in('".implode("','", $myguiseuid)."') ORDER BY lastpost desc,uid desc");
			while($row = DB::fetch($query)) {
				$row['lastpost'] = $row['lastpost']?dgmdate($row['lastpost'], 'u', '9999', 'Y-m-d'):'&#x4ece;&#x672a;';
				$lastpost_arr[$row['uid']] = $row['lastpost'];
			}
			
			$gender_arr = array();
			$query = DB::query("SELECT uid,gender FROM ".DB::table('common_member_profile')." WHERE uid in('".implode("','", $myguiseuid)."')");
			while($row = DB::fetch($query)) {
				$gender_arr[$row['uid']] = $row['gender']=='1' ? 'F30' : ($row['gender']=='2' ? '060' : '000');
			}
			
			$useravatar = $this->vars['useravatar'] ? 1 : 0;
			
			// ��ʾ������������б�
			$input = array();
			$query = DB::query("SELECT uid,username,newpm,newprompt FROM ".DB::table('common_member')." WHERE uid in('".implode("','", $myguiseuid)."')");
			$douid_arr = array();
			while($row = DB::fetch($query)) {
				$avatar = $useravatar ? avatar($row['uid'],'small') : '';$row['newpm']=$row['newprompt']=0;#�ݲ������������֪ͨ
				$input[] = array('uid'=>$row['uid'],'username'=>$row['username'],'avatar'=>$avatar,'newpm'=>$row['newpm'],'newprompt'=>$row['newprompt']);
				$douid_arr[] = $row['uid'];
			}
			$nosearch_arr = array_diff($myguiseuid, $douid_arr);
			if($this->sysversion > '2.0' && $nosearch_arr){
				if(DB::fetch_first("SHOW TABLES LIKE '".DB::table('common_member_archive')."'")){
					$query = DB::query("SELECT uid,username FROM ".DB::table('common_member_archive')." WHERE uid in('".implode("','", $nosearch_arr)."')");
					while($row = DB::fetch($query)) {
						$gender_arr[$row['uid']] = '999';$avatar = $useravatar ? avatar($row['uid'],'small') : '';
						$input[] = array('uid'=>$row['uid'],'username'=>$row['username'],'avatar'=>$avatar,'newpm'=>0,'newprompt'=>0);
					}
				}
			}
			
			$guiseuidcount = count($input)+1;
			if($this->vars['showmod']){
				include template('kl3w_guisepost:input');
				return $return;
			}else{
				$return = '<option value="" style="background:#E8E8E8;">&#x6211;&#x81EA;&#x5DF1;</option>';
				foreach($input as $vo) {
					$return .= '<option value="'.$vo['uid'].'">'.$vo['username'].'</option>';
				}
				return '<div style="padding:5px 0px;">&#x865A;&#x6784;&#x9A6C;&#x7532;&#xFF1A;<select name="kl3wguisepostuserid">'.$return.'</select></div>';
			}
		}else{
			return  '';
		}
	}
	
	function _isip($ip){   
		if(!$ip)return false;
		return !strcmp(@long2ip(sprintf('%u',@ip2long($ip))),$ip) ? true : false;
	}   
	
	function _update_onlinetime($uid, $total, $thismonth, $lastupdate) {
		if(($uid = intval($uid))) {
			DB::query("UPDATE ".DB::table('common_onlinetime')." SET total=total+'$total', thismonth=thismonth+'$thismonth', lastupdate='".$lastupdate."' WHERE uid='{$uid}'");
			return DB::affected_rows();
		}
		return false;
	}
	
	function _guisepostchk(){
		global $_G; $guisepost='';$olduid = 0;
		// ��ȡPOST���ID����,����X1.0��X1.5��X2.0��X3.0
		$guisepostuserid = isset($_POST['kl3wguisepostuserid']) ? intval($_POST['kl3wguisepostuserid']) : intval($_G['gp_kl3wguisepostuserid']);
		if($_G['uid'] == $this->uses[0] && $guisepostuserid){
			if($this->sysversion > '2.0'){
				// ����û��Ƿ�����浵�������潫�û��ƻ�����(����DZX2.0�Ժ�汾)
				$member = getuserbyuid($guisepostuserid, 1);
				if(isset($member['_inarchive']) && $member['_inarchive']) {
					C::t('common_member_archive')->move_to_master($member['uid']);
				}
				unset($member);
			}
			// �����ݿ��û����л�ȡ����˺�����
			$guisepost = DB::fetch_first("SELECT * FROM ".DB::table('common_member')." where uid='$guisepostuserid'");
			if($guisepost){
				space_merge($guisepost, 'status');// ��ȡ����������ϷŽ�$guisepost����

				$timestamp = $_G['timestamp'];
				$olduid = $_G['uid'];//����ԭ�û�ID

				$_G['uid'] = $guisepost['uid'];				// ��ǰ��¼�û�ID���л�������û�ID
				$_G['username'] = $guisepost['username']; 	// ��ǰ��¼�û������л�������û���
				$_G['member'] = $guisepost;

				$_G['setting']['floodctrl'] = 0;//�ر����η���ʱ�������ƣ�����Ǹ߼�������Աʹ�����
				
				$_G['forum']['ismoderator'] = 1;//����ͨ����Ҳ��������׷���ͼƬ��
				
				// ���"��ʾ���������·����ȷ�����֤���������޷��ύ"����
				$_G['group']['seccode'] = '';$_G['gp_formhash'] = formhash(); //��ֹ�ٷ��޸���formhash����֤��������д����ֻΪ����(����)
				$_GET['formhash'] = $_POST['formhash'] = $_G['formhash'] = $_G['gp_formhash'];
				
				$lastactivity = $guisepost['lastactivity'];//�ϴλʱ��
				$lastvisit = $guisepost['lastvisit'];//������ʱ��
				
				//�������ʹ������IP��ʼ
				# http://ips.chacuo.net/view/s_GD ����Ե�¼��ȡ�������IP��ַ��
				$ip_long = array();$guise_onlineip = '';
				$rn = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? "\r\n" : "\n";
				$ipstrarr = explode($rn, $this->vars['IPadd']);
				if(is_array($ipstrarr)){
					foreach($ipstrarr as $ipstr){
						if(empty($ipstr))continue;
						$iparr = explode("@", $ipstr);
						$iparr[1] = empty($iparr[1]) ? $iparr[0] : $iparr[1];
						$ip_long[] = array(ip2long($iparr[0]), ip2long($iparr[1]));
					}
				}
				if($ip_long){
					$ip_count = count($ip_long);
					$rand_ipkey = mt_rand(0, $ip_count-1);
					$guise_onlineip = long2ip(mt_rand($ip_long[$rand_ipkey][0], $ip_long[$rand_ipkey][1]));
				}
				
				$guise_istimeout = false;  // ��ʼ�������û��Ƿ�ʱ
				$onlinehold = $_G['setting']['onlinehold'];
				$guise_timeout = $timestamp-$onlinehold;   // �������߳�ʱ�趨���������߱���ʱ��
				
				$guisepost_isregip = $this->_isip($guisepost['regip']) && $guisepost['regip']!='127.0.0.1';
				$guisepost_islastip = $this->_isip($guisepost['lastip']) && $guisepost['lastip']!='127.0.0.1';
				
				$ulastip = $ulastactivity = false;
				if($guisepost_islastip && !$this->vars['IPset']){
					$_G['clientip'] = $guisepost['lastip'];
					if($guise_timeout >= $lastactivity)$guise_istimeout = true;
					if($timestamp - $lastvisit > 86400 || $guise_istimeout){
						$ulastip=true;
						if($this->_isip($guise_onlineip) && !$guisepost['lastpost']){
							$_G['clientip'] = $guise_onlineip;
						}else{
							$ipal = explode('.', $guisepost['lastip']);
							$_G['clientip'] = $ipal[0].".".$ipal[1].".".$ipal[2].".".round(rand(600000, 2550000) / 10000);
						}
					}
				}else{
					if($this->vars['IPset'])$guisepost_islastip='';
					if($this->_isip($guise_onlineip))$_G['clientip'] = $guise_onlineip;
				}
				//�������ʹ������IP����
				
				$discuz_action = APPTYPEID;//��¼����
				$discuz_tid = intval($_G['tid']);
				$discuz_fid = intval($_G['fid']);				
				
				//����session[���common_session��ռ�������³�������]
				if(empty($_G['setting']['sessionclose'])){
					if($lastactivity + 600 < $timestamp){
						$guise_ss = DB::fetch_first("SELECT sid FROM ".DB::table('common_session')." where uid='$_G[uid]'");
						$now_sid = $guise_ss['sid'] ? $guise_ss['sid'] : random(6); $ips = explode('.', $_G['clientip']);
						$setting = DB::fetch_first("SELECT svalue FROM ".DB::table('common_setting')." where skey='maxonlines'");
						$sessioncount = DB::result_first('SELECT COUNT(sid) FROM '.DB::table('common_session'));
						if($setting){$maxonlines = $setting['svalue'];}else{$maxonlines = 5000;}
						if($maxonlines<$sessioncount){DB::query('DELETE FROM '.DB::table('common_session'));}						
						DB::query("REPLACE INTO ".DB::table('common_session')."(sid, ip1, ip2, ip3, ip4, groupid, lastactivity, action, fid, tid, uid, username)VALUES ('$now_sid', '$ips[0]','$ips[1]','$ips[2]','$ips[3]', '$guisepost[groupid]', '$timestamp','$discuz_action','$discuz_fid','$discuz_tid','$_G[uid]','$_G[username]')", 'UNBUFFERED'); 
					}
					#��¼����ʱ��
					$oltimespan = $_G['setting']['oltimespan'];
					if($oltimespan && $timestamp - $lastactivity > $oltimespan * 60) {
						$isinsert = false;
						if($guise_istimeout) {
							$oldata = DB::fetch_first("SELECT lastupdate FROM ".DB::table('common_onlinetime')." where uid='$_G[uid]'");
							if(empty($oldata)) {
								$isinsert = true;
							} else if($timestamp - $oldata['lastupdate'] > $oltimespan * 60) {
								$this->_update_onlinetime($_G['uid'], $oltimespan, $oltimespan, $timestamp);
							}
						} else {
							$isinsert = !$this->_update_onlinetime($_G['uid'], $oltimespan, $oltimespan, $timestamp);
						}
						if($isinsert) {
							$data = array('uid' => $_G['uid'],'thismonth' => $oltimespan,'total' => $oltimespan,'lastupdate' => $timestamp);
							DB::insert('common_onlinetime', $data, false, false, false);
						}
					}
					if($timestamp - $lastactivity > 21600) {#6i
						if($oltimespan && $timestamp - $lastactivity > 43200) {#12H
							$onlinetime = DB::fetch_first("SELECT total FROM ".DB::table('common_onlinetime')." where uid='$_G[uid]'");
							$oltime = !empty($onlinetime)?round(intval($onlinetime['total']) / 60):0;
							if($oltime>0)DB::query("UPDATE ".DB::table('common_member_count')." SET oltime='$oltime' WHERE uid='$_G[uid]'", 'UNBUFFERED');
						}
						$ulastactivity = true;
					}
				}else{
					if($timestamp - $lastactivity > 21600)$ulastactivity = true;
				}
				
				$oltimeadd = '';//���������û���Ծ״̬
				if($guise_istimeout) {//�����˺ų�ʱ����
					$oltimeadd .= ",lastvisit=lastactivity,lastactivity='$timestamp'";
					if(!$guisepost['lastip']){
						$oltimeadd .= ",regip='$_G[clientip]',lastip='$_G[clientip]'";
					}else{
						$oltimeadd .= $guisepost_isregip ? '' : ",regip='$_G[clientip]'";
						if($ulastip)$oltimeadd .= ",lastip='$_G[clientip]'";
					}
				} else {//�����˺Ų���ʱ����
					$oltimeadd = $guisepost_islastip ? '' : ",lastip='$_G[clientip]'";
					$oltimeadd .= $guisepost_isregip ? '' : ",regip='$_G[clientip]'";
					if($ulastactivity)$oltimeadd .= $guisepost['lastpost']?",lastactivity=lastpost":",lastactivity='$timestamp'";
				}
				DB::query("UPDATE ".DB::table('common_member_status')." SET lastpost='$timestamp'".$oltimeadd." WHERE uid='$_G[uid]'", 'UNBUFFERED');
				
				#����ԭ�û�ID
				$this->uses[2] = $olduid;
			}
		}
	}
}

//��̳ʹ��
class plugin_kl3w_guisepost_forum extends plugin_kl3w_guisepost{
	//�ύ�ɹ���������¼�
	function post_feedlog_message($var) {
		if(!$this->vars['open'])return;//δ��������˳���ִ������
		global $_G; $olduid = $this->uses[2];
		if($olduid != $this->uses[0])return;
		if(substr($var['param'][0], -8) == '_succeed' && $olduid){
			if(isset($_POST['attachnew']) || isset($_G['gp_attachnew'])){
				$tid = $var['param'][2]['tid']; $pid = $var['param'][2]['pid'];
				if($tid && $pid){//�и���ʱ������ת�Ƶ��������
					DB::query("UPDATE ".DB::table('forum_attachment')." SET uid='$_G[uid]' WHERE uid='$olduid' and tid='$tid' and pid='$pid'", 'UNBUFFERED');
					if($this->sysversion < '2.0'){//X1.0-X1.5.1����
						DB::query("UPDATE ".DB::table('forum_attachmentfield')." SET uid='$_G[uid]' WHERE uid='$olduid' and tid='$tid' and pid='$pid'", 'UNBUFFERED');
					}else{//X2.0��������						
						$tableid = intval(substr($tid, -1));
						DB::query("UPDATE ".DB::table('forum_attachment_'.$tableid)." SET uid='$_G[uid]' WHERE uid='$olduid' and tid='$tid' and pid='$pid'", 'UNBUFFERED');
					}
				}
			}
			$guid = $_G['uid'];$_G['uid'] = $olduid;checkusergroup($guid);$_G['uid'] = $guid;#�����û���
		}
		return;
	}
	
	//�������ת������
	function post_top() {
		if(!$this->vars['open'])return '';
		global $seccodecheck, $secqaacheck;
		if(submitcheck('topicsubmit',0,$seccodecheck,$secqaacheck) || submitcheck('replysubmit',0,$seccodecheck,$secqaacheck) || submitcheck('commentsubmit',0,$seccodecheck,$secqaacheck)){
			$this->_guisepostchk();
		}else{
			return $this->getguisepostuserid('toppost');
		}
	}
	
	//�б�ײ����ٷ���Ƕ���
	function forumdisplay_fastpost_content(){
		return $this->getguisepostuserid();
	}
	
	//�����ظ���������Ƕ���(DZX2���ϰ汾������)
	function post_infloat_top(){
		return $this->getguisepostuserid('floatpost');
	}
	
	//���ÿ��ٷ���ģʽʱʹ�ô�Ƕ���
	function viewthread_top_output(){
		if($GLOBALS['_G']['setting']['fastpost'])return '';
		return $this->getguisepostuserid('threadtop');
	}
	
	//���ĵײ���ݻظ�Ƕ���
	function viewthread_fastpost_content(){
		return $this->getguisepostuserid();
	}
	
	//��׻�����չǶ���(��ҵ��)
	function viewthread_postfooter_output(){
		if(!$GLOBALS['_G']['uid'] || !$this->vars['open'] || $GLOBALS['_G']['uid'] != $this->uses[0])return array();
		if($this->vars['docomment'] || $this->vars['dorate']){
			global $allowpostreply, $thread; $tid = $GLOBALS['_G']['tid'];$a = $b = '';$out = array();
			foreach($GLOBALS['postlist'] as $k => $post) {
				$pid = $post['pid'];
				if($this->vars['dorate']){
					if($post['authorid']){
						$a = '[<a style="padding:0px;line-height:26px;" onclick="showWindow(\'rate\', this.href, \'get\', 0)" href="plugin.php?id='.$this->identifier.':guisemisc&action=rate&tid='.$tid.'&pid='.$pid.'">&#x8BC4;&#x5206;</a>]';
					}
				}
				if($post['allowcomment'] && $this->vars['docomment']){
					if($allowpostreply && (!$thread['closed'] || $GLOBALS['_G']['forum']['ismoderator'])){
						$b = '[<a style="padding:0px;line-height:26px;" onclick="showWindow(\'comment\', this.href, \'get\', 0)" href="plugin.php?id='.$this->identifier.':guisemisc&action=comment&tid='.$tid.'&pid='.$pid.'">&#x70B9;&#x8BC4;</a>]';
					}
				}
				if(!$a && !$b)$b = '<span title="&#x8BF7;&#x5230;&#x540E;&#x53F0;&#x5F00;&#x542F;&#x76F8;&#x5173;&#x8BBE;&#x7F6E;">&#x4E3B;&#x53F7;&#x65E0;&#x6743;</span>';
				$out[] = '<span class="cmmnt" style="padding:2px 0px 2px 24px;">&#x9A6C;&#x7532;'.$b.$a.'</span>';
			}
			return $out;
		}else{
			return array();
		}
	}
}
?>
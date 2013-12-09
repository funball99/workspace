<?php
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../public/yz.php';
require_once '../tool/Thumbnail.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once libfile ( 'function/forumlist' );
require_once '../tool/constants.php';
require_once '../Config/public.php';
require_once '../public/mobcentDatabase.php';
require_once ('./abstractInformationList.php');
require_once '../model/table/x25/topic.php';
require_once '../model/table/x25/table_common_member.php';
require_once libfile('function/portal');
require_once libfile('function/home');
class InformationImpl_x25 extends abstractInformationList {
	function getInformationList() {
		$aid = empty($_GET['aid'])?0:intval($_GET['aid']);
		$article = C::t('portal_article_title')->fetch($aid);
		$article_count = C::t('portal_article_count')->fetch($aid);
		require_once libfile('function/portalcp');
		if($article_count) $article = array_merge($article_count, $article);
		if($article_count) {
			C::t('portal_article_count')->increase($aid, array('viewnum'=>1));
			unset($article_count);
		} else {
			C::t('portal_article_count')->insert(array(
			'aid'=>$aid,
			'catid'=>$article['catid'],
			'viewnum'=>1));
		}
		$cat = category_remake($article['catid']);
		
		$article['pic'] = pic_get($article['pic'], '', $article['thumb'], $article['remote'], 1, 1);
		
		$page = intval($_GET['page']);
		if($page<1) $page = 1;
		
		$content = $contents = array();
		$multi = '';
		
		$content = C::t('portal_article_content')->fetch_by_aid_page($aid, $page);
		if($article['contents'] && $article['showinnernav']) {
			foreach(C::t('portal_article_content')->fetch_all($aid) as $value) {
				$contents[] = $value;
			}
			if(empty($contents)) {
				C::t('portal_article_content')->update($aid, array('showinnernav' => '0'));
			}
		}
		$multi = multi($article['contents'], 1, $page, "portal.php?mod=view&aid=$aid");
		$org = array();
		if($article['idtype'] == 'tid' || $content['idtype']=='pid') {
			$thread = $firstpost = array();
			require_once libfile('function/discuzcode');
			require_once libfile('function/forum');
			$thread = get_thread_by_tid($article[id]);
			if(!empty($thread)) {
				if($content['idtype']=='pid') {
					$firstpost = C::t('forum_post')->fetch($thread['posttableid'], $content['id']);
				} else {
					$firstpost = C::t('forum_post')->fetch_threadpost_by_tid_invisible($article['id']);
				}
				if($firstpost && $firstpost['tid'] == $article['id']) {
					$firstpost['uid'] = $firstpost['authorid'];
					$firstpost['username'] = $firstpost['author'];
				}
			}
			if(!empty($firstpost) && !empty($thread) && $thread['displayorder'] != -1) {
				$_G['tid'] = $article['id'];
				$aids = array();
				$firstpost['message'] = $content['content'];
				if($thread['attachment']) {
					$_G['group']['allowgetimage'] = 1;
					if(preg_match_all("/\[attach\](\d+)\[\/attach\]/i", $firstpost['message'], $matchaids)) {
						$aids = $matchaids[1];
					}
				}
		
				if($aids) {
					$this->parseforumattach($firstpost, $aids);
				}
				$content['content'] = $firstpost['message'];
				$content['pid'] = $firstpost['pid'];
		
				$org = $firstpost;
				$org_url = "forum.php?mod=viewthread&tid=$article[id]";
			} else {
				C::t('portal_article_title')->update($aid, array('id' => 0, 'idtype' => ''));
				C::t('portal_article_content')->update_by_aid($aid, array('id' => 0, 'idtype' => ''));
			}
		} elseif($article['idtype']=='blogid') {
			$org = C::t('home_blog')->fetch($article['id']);
			if(empty($org)) {
				C::t('portal_article_title')->update($aid, array('id' => 0, 'idtype' => ''));
				dheader('location: portal.php?mod=view&aid='.$aid);
				exit();
			}
		}
		$article['related'] = array();
		if(($relateds = C::t('portal_article_related')->fetch_all_by_aid($aid))) {
			foreach(C::t('portal_article_title')->fetch_all(array_keys($relateds)) as $raid => $value) {
				$article['related'][$raid] = $value['title'];
			}
		}
		$article['allowcomment'] = !empty($cat['allowcomment']) && !empty($article['allowcomment']) ? 1 : 0;
		$_G['catid'] = $_GET['catid'] = $article['catid'];
		$common_url = '';
		$commentlist = array();
		if($article['allowcomment']) {
		
			if($org && empty($article['owncomment'])) {
		
				if($article['idtype'] == 'blogid') {
		
					$common_url = "home.php?mod=space&uid=$org[uid]&do=blog&id=$article[id]";
					$form_url = "home.php?mod=spacecp&ac=comment";
		
					$article['commentnum'] = C::t('home_comment')->count_by_id_idtype($article['id'], 'blogid');
					if($article['commentnum']) {
						$query = C::t('home_comment')->fetch_all_by_id_idtype($article['id'], 'blogid', 0, 20, '', 'DESC');
						foreach($query as $value) {
							if($value['status'] == 0 || $_G['adminid'] == 1 || $value['uid'] == $_G['uid']) {
								$commentlist[] = $value;
							}
						}
					}
		
				} elseif($article['idtype'] == 'tid') {
		
					$common_url = "forum.php?mod=viewthread&tid=$article[id]";
					$form_url = "forum.php?mod=post&action=reply&tid=$article[id]&replysubmit=yes&infloat=yes&handlekey=fastpost";
		
					require_once libfile('function/discuzcode');
					$posttable = empty($thread['posttable']) ? getposttablebytid($article['id']) : $thread['posttable'];
					$_G['tid'] = $article['id'];
					$article['commentnum'] = getcount($posttable, array('tid'=>$article['id'], 'first'=>'0'));
		
					if($article['allowcomment'] && $article['commentnum']) {
						$attachpids = $attachtags = array();
						$_G['group']['allowgetattach'] = $_G['group']['allowgetimage'] = 1;
						foreach(C::t('forum_post')->fetch_all_by_tid('tid:'.$article['id'], $article['id'], true, 'ASC', 0, 20, null, 0) as $value) {
							$value['uid'] = $value['authorid'];
							$value['username'] = $value['author'];
							if($value['status'] != 1 && !$value['first']) {
								$value['message'] = discuzcode($value['message'], $value['smileyoff'], $value['bbcodeoff'], $value['htmlon']);
								$value['cid'] = $value['pid'];
								$commentlist[$value['pid']] = $value;
								if($value['attachment']) {
									$attachpids[] = $value['pid'];
									if(preg_match_all("/\[attach\](\d+)\[\/attach\]/i", $value['message'], $matchaids)) {
										$attachtags[$value['pid']] = $matchaids[1];
									}
								}
							}
						}
		
						if($attachpids) {
							require_once libfile('function/attachment');
							parseattach($attachpids, $attachtags, $commentlist);
						}
					}
				}
		
			} else {
		
				$common_url = "portal.php?mod=comment&id=$aid&idtype=aid";
				$form_url = "portal.php?mod=portalcp&ac=comment";
		
				$query = C::t('portal_comment')->fetch_all_by_id_idtype($aid, 'aid', 'dateline', 'DESC', 0, 20);
				$pricount = 0;
				foreach($query as $value) {
					if($value['status'] == 0 || $value['uid'] == $_G['uid'] || $_G['adminid'] == 1) {
						$value['allowop'] = 1;
						$commentlist[] = $value;
					} else {
						$pricount += 1;
					}
				}
			}
		}
		
		$hash = md5($article['uid']."\t".$article['dateline']);
		$id = $article['aid'];
		$idtype = 'aid';
		
		loadcache('click');
		$clicks = empty($_G['cache']['click']['aid'])?array():$_G['cache']['click']['aid'];
		$maxclicknum = 0;
		foreach ($clicks as $key => $value) {
			$value['clicknum'] = $article["click{$key}"];
			$value['classid'] = mt_rand(1, 4);
			if($value['clicknum'] > $maxclicknum) $maxclicknum = $value['clicknum'];
			$clicks[$key] = $value;
		}
		
		$clickuserlist = array();
		foreach(C::t('home_clickuser')->fetch_all_by_id_idtype($id, $idtype, 0, 24) as $value) {
			$value['clickname'] = $clicks[$value['clickid']]['name'];
			$clickuserlist[] = $value;
		}
		
		$article['dateline'] = dgmdate($article['dateline']);
		
		foreach($cat['ups'] as $val) {
			$cats[] = $val['catname'];
		}
	
		$catid = $article['catid'];
		if(!$_G['setting']['relatedlinkstatus']) {
			$_G['relatedlinks'] = get_related_link('article');
		} else {
			$content['content'] = parse_related_link($content['content'], 'article');
		}
		
		$tpldirectory = '';
		$articleprimaltplname = $cat['articleprimaltplname'];
		if(strpos($articleprimaltplname, ':') !== false) {
			list($tpldirectory, $articleprimaltplname) = explode(':', $articleprimaltplname);
		}
		
		if($content['idtype']=="tid"){
			$str=str_replace("\r", "",$content['content']);
			$str=str_replace("\n", "",$str);
			$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/"; 
			preg_match_all($pattern,$str,$match); 
			foreach($match[1] as $mk => $mv){
				$message1[$mk][infor] = $match[1][$mk];
				$message1[$mk][originalInfo] = $match[1][$mk];
				$message1[$mk][aid] = 0;
				$message1[$mk][type] = 1;
			}
		}else{
			$message1 = doContent ( $content['content'] );
		}
		
		//print_r($message1);exit;
		$message2 =  getContentFont( $content['content']);
		foreach($message1 as $k=>$v){
			if($v['type']==0){
				unset($message1[$k]);
			}else{
		
			}
		}
		
		foreach($message1 as $key=>$valInfo){
			if(substr($valInfo['infor'], 0, 7)!="http://"){
				$message1[$key]['infor'] ='/'.$valInfo['infor'];
				$message1[$key]['originalInfo'] ='/'.$valInfo['originalInfo'];
			}
		}
		
		$message_array2 = explode('|~|', $message2);
		foreach($message_array2 as $k=>$v){
			if(!empty($v))
			{
				$message[]=array("infor" =>str_replace('<hr class="l" />','',preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2", $v)),"type"	=>0,);
			}
			
			if(preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$message1[$k]["infor"])){
				$message[]=$message1[$k];
			}
		}
		$data['rs']=1;
		$data['content']=empty($message)?array():$message;
		$data['create_time']=$article['dateline'];
		$data['board_name']=Common::get_unicode_charset('\u6587\u7AE0\u8BE6\u60C5');
		$data['author']=$article['author'];
		$data['title']=$article['title'];
		$data['summary']=$article['summary'];
		return $data;
		}
		public function parseforumattach(&$post, $aids) {
			global $_G;
			if(($aids = array_unique($aids))) {
				require_once libfile('function/attachment');
				$finds = $replaces = array();
				foreach(C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$post['tid'], 'aid', $aids) as $attach) {
		
					$attach['url'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/';
					$attach['dateline'] = dgmdate($attach['dateline'], 'u');
					$extension = strtolower(fileext($attach['filename']));
					$attach['ext'] = $extension;
					$attach['imgalt'] = $attach['isimage'] ? strip_tags(str_replace('"', '\"', $attach['description'] ? $attach['description'] : $attach['filename'])) : '';
					$attach['attachicon'] = attachtype($extension."\t".$attach['filetype']);
					$attach['attachsize'] = sizecount($attach['filesize']);
		
					$attach['refcheck'] = (!$attach['remote'] && $_G['setting']['attachrefcheck']) || ($attach['remote'] && ($_G['setting']['ftp']['hideurl'] || ($attach['isimage'] && $_G['setting']['attachimgpost'] && strtolower(substr($_G['setting']['ftp']['attachurl'], 0, 3)) == 'ftp')));
					$aidencode = packaids($attach);
					$widthcode = attachwidth($attach['width']);
					$is_archive = $_G['forum_thread']['is_archived'] ? "&fid=".$_G['fid']."&archiveid=".$_G['forum_thread']['archiveid'] : '';
					if($attach['isimage']) {
						$attachthumb = getimgthumbname($attach['attachment']);
						if($_G['setting']['thumbstatus'] && $attach['thumb']) {
							$replaces[$attach['aid']] = "<a href=\"javascript:;\"><img id=\"_aimg_$attach[aid]\" aid=\"$attach[aid]\" onclick=\"zoom(this, this.getAttribute('zoomfile'), 0, 0, '{$_G[forum][showexif]}')\"
							zoomfile=\"".($attach['refcheck']? "forum.php?mod=attachment{$is_archive}&aid=$aidencode&noupdate=yes&nothumb=yes" : $attach['url'].$attach['attachment'])."\"
						src=\"".($attach['refcheck'] ? "forum.php?mod=attachment{$is_archive}&aid=$aidencode" : $attach['url'].$attachthumb)."\" alt=\"$attach[imgalt]\" title=\"$attach[imgalt]\" w=\"$attach[width]\" /></a>";
						} else {
						$replaces[$attach['aid']] = "<img id=\"_aimg_$attach[aid]\" aid=\"$attach[aid]\"
						zoomfile=\"".($attach['refcheck'] ? "forum.php?mod=attachment{$is_archive}&aid=$aidencode&noupdate=yes&nothumb=yes" : $attach['url'].$attach['attachment'])."\"
						src=\"".($attach['refcheck'] ? "forum.php?mod=attachment{$is_archive}&aid=$aidencode&noupdate=yes " : $attach['url'].$attach['attachment'])."\" $widthcode alt=\"$attach[imgalt]\" title=\"$attach[imgalt]\" w=\"$attach[width]\" />";
						}
						} else {
						$replaces[$attach['aid']] = "$attach[attachicon]<a href=\"forum.php?mod=attachment{$is_archive}&aid=$aidencode\" onmouseover=\"showMenu({'ctrlid':this.id,'pos':'12'})\" id=\"aid$attach[aid]\" target=\"_blank\">$attach[filename]</a>";
						}
						$finds[$attach['aid']] = '[attach]'.$attach['aid'].'[/attach]';
			}
						if($finds && $replaces) {
						$post['message'] = str_ireplace($finds, $replaces, $post['message']);
						}
						}
		}

}

?>
<?php

class forum {function parseattach($tid,$attachpids, $attachtags, &$postlist, $skipaids = array()) {
			global $_G;
			$attachpids = empty($attachpids) ? '-1' : $attachpids;
			$query = DB::query("SELECT * FROM ".DB::table(getattachtablebytid($tid))." a WHERE a.pid IN ($attachpids)");
			$attachexists = FALSE;
			$skipattachcode = $aids = $payaids = $findattach = array();
			while($attach = DB::fetch($query)) {
				$attachexists = TRUE;
				if($skipaids && in_array($attach['aid'], $skipaids)) {
					$skipattachcode[$attach[pid]][] = "/\[attach\]$attach[aid]\[\/attach\]/i";
					continue;
				}
				$attached = 0;
				$extension = strtolower(fileext($attach['filename']));
				$attach['ext'] = $extension;
				$attach['imgalt'] = $attach['isimage'] ? strip_tags(str_replace('"', '\"', $attach['description'] ? $attach['description'] : $attach['filename'])) : '';
				$attach['attachicon'] = attachtype($extension."\t".$attach['filetype']);
				$attach['attachsize'] = sizecount($attach['filesize']);
				if($attach['isimage'] && !$_G['setting']['attachimgpost']) {
					$attach['isimage'] = 0;
				}
				$attach['attachimg'] = $attach['isimage'] && (!$attach['readperm'] || $_G['group']['readaccess'] >= $attach['readperm']) ? 1 : 0;
				if($attach['attachimg']) {
					$GLOBALS['aimgs'][$attach['pid']][] = $attach['aid'];
				}
				if($attach['price']) {
					if($_G['setting']['maxchargespan'] && TIMESTAMP - $attach['dateline'] >= $_G['setting']['maxchargespan'] * 3600) {
						DB::query("UPDATE ".DB::table(getattachtablebytid($_G['tid']))." SET price='0' WHERE aid='$attach[aid]'");
						$attach['price'] = 0;
					} elseif(!$_G['forum_attachmentdown'] && $_G['uid'] != $attach['uid']) {
						$payaids[$attach['aid']] = $attach['pid'];
					}
				}
				$attach['payed'] = $_G['forum_attachmentdown'] || $_G['uid'] == $attach['uid'] ? 1 : 0;
				$attach['url'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/';
				$attach['dateline'] = dgmdate($attach['dateline'], 'u');
				$postlist[$attach['pid']]['attachments'][$attach['aid']] = $attach;
				if(!empty($attachtags[$attach['pid']]) && is_array($attachtags[$attach['pid']]) && in_array($attach['aid'], $attachtags[$attach['pid']])) {
					$findattach[$attach['pid']][$attach['aid']] = "/\[attach\]$attach[aid]\[\/attach\]/i";
					$attached = 1;
				}
		
				if(!$attached) {
					if($attach['isimage']) {
						$postlist[$attach['pid']]['imagelist'][] = $attach['aid'];
						$postlist[$attach['pid']]['imagelistcount']++;
						if($postlist[$attach['pid']]['first']) {
							$GLOBALS['firstimgs'][] = $attach['aid'];
						}
					} else {
						if(!$_G['forum_skipaidlist'] || !in_array($attach['aid'], $_G['forum_skipaidlist'])) {
							$postlist[$attach['pid']]['attachlist'][] = $attach['aid'];
						}
					}
				}
				$aids[] = $attach['aid'];
			}
			if($aids) {
				$query = DB::query("SELECT aid, pid, downloads FROM ".DB::table('forum_attachment')." WHERE aid IN (".dimplode($aids).")");
				while($attach = DB::fetch($query)) {
					$postlist[$attach['pid']]['attachments'][$attach['aid']]['downloads'] = $attach['downloads'];
				}
			}
			if($payaids) {
				$query = DB::query("SELECT relatedid FROM ".DB::table('common_credit_log')." WHERE relatedid IN (".dimplode(array_keys($payaids)).") AND uid='$_G[uid]' AND operation='BAC'");
				while($creditlog = DB::fetch($query)) {
					$postlist[$payaids[$creditlog['relatedid']]]['attachments'][$creditlog['relatedid']]['payed'] = 1;
				}
			}
			if(!empty($skipattachcode)) {
				foreach($skipattachcode as $pid => $findskipattach) {
					foreach($findskipattach as $findskip) {
						$postlist[$pid]['message'] = preg_replace($findskip, '', $postlist[$pid]['message']);
					}
				}
			}
		
			if($attachexists) {
				foreach($attachtags as $pid => $aids) {
					if($findattach[$pid]) {
						foreach($findattach[$pid] as $aid => $find) {
							$postlist[$pid]['message'] = preg_replace($find, attachinpost($postlist[$pid]['attachments'][$aid], $postlist[$pid]['first']), $postlist[$pid]['message'], 1);
							$postlist[$pid]['message'] = preg_replace($find, '', $postlist[$pid]['message']);
						}
					}
				}
			} else {
			}
		}
	
	public function loadmobcentforum($tid, $fid) {
		global $_G;
		if (! empty ( $_GET ['archiver'] )) {  
			if ($fid) {
				dheader ( 'location: archiver/?fid-' . $fid . '.html' );
			} elseif ($tid) {
				dheader ( 'location: archiver/?tid-' . $tid . '.html' );
			} else {
				dheader ( 'location: archiver/' );
			}
		}
		if (defined ( 'IN_ARCHIVER' ) && $_G ['setting'] ['archiverredirect'] && ! IS_ROBOT) {
			dheader ( 'location: ../forum.php' . ($_G ['mod'] ? '?mod=' . $_G ['mod'] . (! empty ( $_GET ['fid'] ) ? '&fid=' . $_GET ['fid'] : (! empty ( $_GET ['tid'] ) ? '&tid=' . $_GET ['tid'] : '')) : '') );
		}
		if ($_G ['setting'] ['forumpicstyle']) {
			$_G ['setting'] ['forumpicstyle'] = dunserialize ( $_G ['setting'] ['forumpicstyle'] );
			empty ( $_G ['setting'] ['forumpicstyle'] ['thumbwidth'] ) && $_G ['setting'] ['forumpicstyle'] ['thumbwidth'] = 214;
			empty ( $_G ['setting'] ['forumpicstyle'] ['thumbheight'] ) && $_G ['setting'] ['forumpicstyle'] ['thumbheight'] = 160;
		} else {
			$_G ['setting'] ['forumpicstyle'] = array (
					'thumbwidth' => 214,
					'thumbheight' => 160
			);
		}
		if ($fid) {
			$fid = is_numeric ( $fid ) ? intval ( $fid ) : (! empty ( $_G ['setting'] ['forumfids'] [$fid] ) ? $_G ['setting'] ['forumfids'] [$fid] : 0);
		}
	
		$modthreadkey = isset ( $_GET ['modthreadkey'] ) && $_GET ['modthreadkey'] == modauthkey ( $tid ) ? $_GET ['modthreadkey'] : '';
		$_G ['forum_auditstatuson'] = $modthreadkey ? true : false;
	
		$metadescription = $hookscriptmessage = '';
		$adminid = $_G ['adminid'];
	
		if (! empty ( $tid ) || ! empty ( $fid )) {
				
			if (! empty ( $tid )) {
				$archiveid = ! empty ( $_GET ['archiveid'] ) ? intval ( $_GET ['archiveid'] ) : null;
				$_G ['thread'] = get_thread_by_tid ( $tid, $archiveid );
				if (! $_G ['forum_auditstatuson'] && ! empty ( $_G ['thread'] ) && ! ($_G ['thread'] ['displayorder'] >= 0 || (in_array ( $_G ['thread'] ['displayorder'], array (
						- 4,
						- 3,
						- 2
				) ) && $_G ['uid'] && $_G ['thread'] ['authorid'] == $_G ['uid']))) {
					$_G ['thread'] = null;
				}
	
				$_G ['forum_thread'] = & $_G ['thread'];
	
				if (empty ( $_G ['thread'] )) {
					$fid = $tid = 0;
				} else {
					$fid = $_G ['thread'] ['fid'];
					$tid = $_G ['thread'] ['tid'];
				}
			}
				
			if ($fid) {
				$forum = C::t ( 'forum_forum' )->fetch_info_by_fid ( $fid );
			}
				
			if ($forum) {
				if ($_G ['uid']) {
					if ($_G ['member'] ['accessmasks']) {
						$query = C::t ( 'forum_access' )->fetch_all_by_fid_uid ( $fid, $_G ['uid'] );
						$forum ['allowview'] = $query [0] ['allowview'];
						$forum ['allowpost'] = $query [0] ['allowpost'];
						$forum ['allowreply'] = $query [0] ['allowreply'];
						$forum ['allowgetattach'] = $query [0] ['allowgetattach'];
						$forum ['allowgetimage'] = $query [0] ['allowgetimage'];
						$forum ['allowpostattach'] = $query [0] ['allowpostattach'];
						$forum ['allowpostimage'] = $query [0] ['allowpostimage'];
					}
					if ($adminid == 3) {
						$forum ['ismoderator'] = C::t ( 'forum_moderator' )->fetch_uid_by_fid_uid ( $fid, $_G ['uid'] );
					}
				}
				$forum ['ismoderator'] = ! empty ( $forum ['ismoderator'] ) || $adminid == 1 || $adminid == 2 ? 1 : 0;
				$fid = $forum ['fid'];
				$gorup_admingroupids = $_G ['setting'] ['group_admingroupids'] ? dunserialize ( $_G ['setting'] ['group_admingroupids'] ) : array (
						'1' => '1'
				);
	
				if ($forum ['status'] == 3) {
					if (! empty ( $forum ['moderators'] )) {
						$forum ['moderators'] = dunserialize ( $forum ['moderators'] );
					} else {
						require_once libfile ( 'function/group' );
						$forum ['moderators'] = update_groupmoderators ( $fid );
					}
					if ($_G ['uid'] && $_G ['adminid'] != 1) {
						$forum ['ismoderator'] = ! empty ( $forum ['moderators'] [$_G ['uid']] ) ? 1 : 0;
						$_G ['adminid'] = 0;
						if ($forum ['ismoderator'] || $gorup_admingroupids [$_G ['groupid']]) {
							$_G ['adminid'] = $_G ['adminid'] ? $_G ['adminid'] : 3;
							if (! empty ( $gorup_admingroupids [$_G ['groupid']] )) {
								$forum ['ismoderator'] = 1;
								$_G ['adminid'] = 2;
							}
								
							$group_userperm = dunserialize ( $_G ['setting'] ['group_userperm'] );
							if (is_array ( $group_userperm )) {
								$_G ['group'] = array_merge ( $_G ['group'], $group_userperm );
								$_G ['group'] ['allowmovethread'] = $_G ['group'] ['allowcopythread'] = $_G ['group'] ['allowedittypethread'] = 0;
							}
						}
					}
				}
				foreach ( array (
						'threadtypes',
						'threadsorts',
						'creditspolicy',
						'modrecommend'
				) as $key ) {
					$forum [$key] = ! empty ( $forum [$key] ) ? dunserialize ( $forum [$key] ) : array ();
					if (! is_array ( $forum [$key] )) {
						$forum [$key] = array ();
					}
				}
	
				if ($forum ['status'] == 3) {
					$_G ['isgroupuser'] = 0;
					$_G ['basescript'] = 'group';
					if ($forum ['level'] == 0) {
						$levelinfo = C::t ( 'forum_grouplevel' )->fetch_by_credits ( $forum ['commoncredits'] );
						$levelid = $levelinfo ['levelid'];
						$forum ['level'] = $levelid;
						C::t ( 'forum_forum' )->update_group_level ( $levelid, $fid );
					}
					if ($forum ['level'] != - 1) {
						loadcache ( 'grouplevels' );
						$grouplevel = $_G ['grouplevels'] [$forum ['level']];
						if (! empty ( $grouplevel ['icon'] )) {
							$valueparse = parse_url ( $grouplevel ['icon'] );
							if (! isset ( $valueparse ['host'] )) {
								$grouplevel ['icon'] = $_G ['setting'] ['attachurl'] . 'common/' . $grouplevel ['icon'];
							}
						}
					}
						
					$group_postpolicy = $grouplevel ['postpolicy'];
					if (is_array ( $group_postpolicy )) {
						$forum = array_merge ( $forum, $group_postpolicy );
					}
					$forum ['allowfeed'] = $_G ['setting'] ['group_allowfeed'];
					if ($_G ['uid']) {
						if (! empty ( $forum ['moderators'] [$_G ['uid']] )) {
							$_G ['isgroupuser'] = 1;
						} else {
							$groupuserinfo = C::t ( 'forum_groupuser' )->fetch_userinfo ( $_G ['uid'], $fid );
							$_G ['isgroupuser'] = $groupuserinfo ['level'];
							if ($_G ['isgroupuser'] <= 0 && empty ( $forum ['ismoderator'] )) {
								$_G ['group'] ['allowrecommend'] = $_G ['cache'] ['usergroup_' . $_G ['groupid']] ['allowrecommend'] = 0;
								$_G ['group'] ['allowcommentpost'] = $_G ['cache'] ['usergroup_' . $_G ['groupid']] ['allowcommentpost'] = 0;
								$_G ['group'] ['allowcommentitem'] = $_G ['cache'] ['usergroup_' . $_G ['groupid']] ['allowcommentitem'] = 0;
								$_G ['group'] ['raterange'] = $_G ['cache'] ['usergroup_' . $_G ['groupid']] ['raterange'] = array ();
								$_G ['group'] ['allowvote'] = $_G ['cache'] ['usergroup_' . $_G ['groupid']] ['allowvote'] = 0;
							} else {
								$_G ['isgroupuser'] = 1;
							}
						}
					}
				}
			} else {
				$fid = 0;
			}
		}
	
		$_G ['fid'] = $fid;
		$_G ['tid'] = $tid;
		$_G ['forum'] = &$forum;
		$_G ['current_grouplevel'] = &$grouplevel;
	
		if (! empty ( $_G ['forum'] ['widthauto'] )) {
			$_G ['widthauto'] = $_G ['forum'] ['widthauto'];
		}
	}
	function viewthread_updateviews($threadtable,$tid) {
		global $_G;
		if($_G['setting']['delayviewcount'] == 1 || $_G['setting']['delayviewcount'] == 3) {
			$_G['forum_logfile'] = './data/cache/forum_threadviews_'.intval(getglobal('config/server/id')).'.log';
			if(substr(TIMESTAMP, -2) == '00') {
				require_once libfile('function/misc');
				updateviews($threadtable, 'tid', 'views', $_G['forum_logfile']);
			}
			if(@$fp = fopen(DISCUZ_ROOT.$_G['forum_logfile'], 'a')) {
				fwrite($fp, "$_G[tid]\n");
				fclose($fp);
			} elseif($_G['adminid'] == 1) {
				showmessage('view_log_invalid', '', array('logfile' => $_G['forum_logfile']));
			}
			return true;
		} else {
	
			return DB::query("UPDATE LOW_PRIORITY ".DB::table($threadtable)." SET views=views+1 WHERE tid='$tid'", 'UNBUFFERED');
	
		}
	}
	
function viewthread_procpost($post, $lastvisit, $ordertype, $special = 0) {
	global $_G, $rushreply;
	if(!$_G['forum_newpostanchor'] && $post['dateline'] > $lastvisit) {
		$post['newpostanchor'] = '<a name="newpost"></a>';
		$_G['forum_newpostanchor'] = 1;
	} else {
		$post['newpostanchor'] = '';
	}

	$post['lastpostanchor'] = ($ordertype != 1 && $_G['forum_numpost'] == $_G['forum_thread']['replies']) || ($ordertype == 1 && $_G['forum_numpost'] == $_G['forum_thread']['replies'] + 2) ? '<a name="lastpost"></a>' : '';

	if($_G['forum_pagebydesc']) {
		if($ordertype != 1) {
			$post['number'] = $_G['forum_numpost'] + $_G['forum_ppp2']--;
		} else {
			$post['number'] = $post['first'] == 1 ? 1 : $_G['forum_numpost'] - $_G['forum_ppp2']--;
		}
	} else {
		if($ordertype != 1) {
			$post['number'] = ++$_G['forum_numpost'];
		} else {
			$post['number'] = $post['first'] == 1 ? 1 : --$_G['forum_numpost'];
		}
	}

	$_G['forum_postcount']++;

	$post['dbdateline'] = $post['dateline'];
	if($_G['setting']['dateconvert']) {
		$post['dateline'] = dgmdate($post['dateline'], 'u');
	} else {
		$dformat = getglobal('setting/dateformat');
		$tformat = getglobal('setting/timeformat');
		$post['dateline'] = dgmdate($post['dateline'], $dformat.' '.str_replace(":i", ":i:s", $tformat));
	}
	$post['groupid'] = $_G['cache']['usergroups'][$post['groupid']] ? $post['groupid'] : 7;

	if($post['username']) {

		$_G['forum_onlineauthors'][] = $post['authorid'];
		$post['usernameenc'] = rawurlencode($post['username']);
		$post['readaccess'] = $_G['cache']['usergroups'][$post['groupid']]['readaccess'];
		if($_G['cache']['usergroups'][$post['groupid']]['userstatusby'] == 1) {
			$post['authortitle'] = $_G['cache']['usergroups'][$post['groupid']]['grouptitle'];
			$post['stars'] = $_G['cache']['usergroups'][$post['groupid']]['stars'];
		}
		$post['upgradecredit'] = false;
		if($_G['cache']['usergroups'][$post['groupid']]['type'] == 'member' && $_G['cache']['usergroups'][$post['groupid']]['creditslower'] != 999999999) {
			$post['upgradecredit'] = $_G['cache']['usergroups'][$post['groupid']]['creditslower'] - $post['credits'];
		}

		$post['taobaoas'] = addslashes($post['taobao']);
		$post['regdate'] = dgmdate($post['regdate'], 'd');
		$post['lastdate'] = dgmdate($post['lastactivity'], 'd');

		$post['authoras'] = !$post['anonymous'] ? ' '.addslashes($post['author']) : '';

		if($post['medals']) {
			loadcache('medals');
			foreach($post['medals'] = explode("\t", $post['medals']) as $key => $medalid) {
				list($medalid, $medalexpiration) = explode("|", $medalid);
				if(isset($_G['cache']['medals'][$medalid]) && (!$medalexpiration || $medalexpiration > TIMESTAMP)) {
					$post['medals'][$key] = $_G['cache']['medals'][$medalid];
					$post['medals'][$key]['medalid'] = $medalid;
					$_G['medal_list'][$medalid] = $_G['cache']['medals'][$medalid];
				} else {
					unset($post['medals'][$key]);
				}
			}
		}

		$post['avatar'] = avatar($post['authorid']);
		$post['groupicon'] = $post['avatar'] ? g_icon($post['groupid'], 1) : '';
		$post['banned'] = $post['status'] & 1;
		$post['warned'] = ($post['status'] & 2) >> 1;

	} else {
		if(!$post['authorid']) {
			$post['useip'] = substr($post['useip'], 0, strrpos($post['useip'], '.')).'.x';
		}
	}
	$post['attachments'] = array();
	$post['imagelist'] = $post['attachlist'] = '';

	if($post['attachment']) {
		if($_G['group']['allowgetattach'] || $_G['group']['allowgetimage']) {
			$_G['forum_attachpids'] .= ",$post[pid]";
			$post['attachment'] = 0;
			if(preg_match_all("/\[attach\](\d+)\[\/attach\]/i", $post['message'], $matchaids)) {
				$_G['forum_attachtags'][$post['pid']] = $matchaids[1];
			}
		} else {
			$post['message'] = preg_replace("/\[attach\](\d+)\[\/attach\]/i", '', $post['message']);
		}
	}

	$_G['forum_ratelogpid'] .= ($_G['setting']['ratelogrecord'] && $post['ratetimes']) ? ','.$post['pid'] : '';
	if($_G['setting']['commentnumber'] && ($post['first'] && $_G['setting']['commentfirstpost'] || !$post['first'])) {
		$_G['forum_commonpid'] .= $post['comment'] ? ','.$post['pid'] : '';
	}
	$post['allowcomment'] = $_G['setting']['commentnumber'] && in_array(1, $_G['setting']['allowpostcomment']) && ($_G['setting']['commentpostself'] || $post['authorid'] != $_G['uid']) &&
	($post['first'] && $_G['setting']['commentfirstpost'] && in_array($_G['group']['allowcommentpost'], array(1, 3)) ||
			(!$post['first'] && in_array($_G['group']['allowcommentpost'], array(2, 3))));
	$_G['forum']['allowbbcode'] = $_G['forum']['allowbbcode'] ? -$post['groupid'] : 0;
	$post['signature'] = $post['usesig'] ? ($_G['setting']['sigviewcond'] ? (strlen($post['message']) > $_G['setting']['sigviewcond'] ? $post['signature'] : '') : $post['signature']) : '';
	if(!defined('IN_ARCHIVER')) {
		$post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], $post['htmlon'] & 1, $_G['forum']['allowsmilies'], $_G['forum']['allowbbcode'], ($_G['forum']['allowimgcode'] && $_G['setting']['showimages'] ? 1 : 0), $_G['forum']['allowhtml'], ($_G['forum']['jammer'] && $post['authorid'] != $_G['uid'] ? 1 : 0), 0, $post['authorid'], $_G['cache']['usergroups'][$post['groupid']]['allowmediacode'] && $_G['forum']['allowmediacode'], $post['pid'], $_G['setting']['lazyload']);
		if($post['first']) {
			if(!$_G['forum_thread']['isgroup']) {
				$_G['relatedlinks'] = getrelatedlink('forum');
			} else {
				$_G['relatedlinks'] = getrelatedlink('group');
			}
		}
	}
	$_G['forum_firstpid'] = intval($_G['forum_firstpid']);
	$post['custominfo'] = $this->viewthread_custominfo($post);
	return $post;
}

function viewthread_custominfo($post) {
	global $_G;

	$types = array('left', 'menu');
	foreach($types as $type) {
		if(!is_array($_G['cache']['custominfo']['setting'][$type])) {
			continue;
		}
		$data = '';
		foreach($_G['cache']['custominfo']['setting'][$type] as $key => $order) {
			$v = '';
			if(substr($key, 0, 10) == 'extcredits') {
				$i = substr($key, 10);
				$extcredit = $_G['setting']['extcredits'][$i];
				$v = '<dt>'.($extcredit['img'] ? $extcredit['img'].' ' : '').$extcredit['title'].'</dt><dd>'.$post['extcredits'.$i].' '.$extcredit['unit'].'</dd>';
			} elseif(substr($key, 0, 6) == 'field_') {
				$field = substr($key, 6);
				if(!empty($post['privacy']['profile'][$field])) {
					continue;
				}
				require_once libfile('function/profile');
				$v = profile_show($field, $post);
				if($v) {
					$v = '<dt>'.$_G['cache']['custominfo']['profile'][$key][0].'</dt><dd title="'.htmlspecialchars(strip_tags($v)).'">'.$v.'</dd>';
				}
			} else {
				switch($key) {
					case 'uid': $v = $post['uid'];break;
					case 'posts': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=thread&type=reply&view=me&from=space" target="_blank" class="xi2">'.$post['posts'].'</a>';break;
					case 'threads': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=thread&type=thread&view=me&from=space" target="_blank" class="xi2">'.$post['threads'].'</a>';break;
					case 'doings': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=doing&view=me&from=space" target="_blank" class="xi2">'.$post['doings'].'</a>';break;
					case 'blogs': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=blog&view=me&from=space" target="_blank" class="xi2">'.$post['blogs'].'</a>';break;
					case 'albums': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=album&view=me&from=space" target="_blank" class="xi2">'.$post['albums'].'</a>';break;
					case 'sharings': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=share&view=me&from=space" target="_blank" class="xi2">'.$post['sharings'].'</a>';break;
					case 'friends': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=friend&view=me&from=space" target="_blank" class="xi2">'.$post['friends'].'</a>';break;
					case 'digest': $v = $post['digestposts'];break;
					case 'credits': $v = $post['credits'];break;
					case 'readperm': $v = $post['readaccess'];break;
					case 'regtime': $v = $post['regdate'];break;
					case 'lastdate': $v = $post['lastdate'];break;
					case 'oltime': $v = $post['oltime'].' '.lang('space', 'viewthread_userinfo_hour');break;
				}
				if($v !== '') {
					$v = '<dt>'.lang('space', 'viewthread_userinfo_'.$key).'</dt><dd>'.$v.'</dd>';
				}
			}
			$data .= $v;
		}
		$return[$type] = $data;
	}
	return $return;
}

function getrelateitem($tagarray, $tid = 0, $type = 'tid') {
	global $_G;
	$tagidarray = $relatearray = $relateitem = array();
	$limit = $_G['setting']['relatenum'];
	$limitsum = 2 * $limit;
	if(!$limit) {
		return '';
	}
	foreach($tagarray as $var) {
		$tagidarray[] = $var['0'];
	}
	if(!$tagidarray) {
		return '';
	}
	$query = DB::query("SELECT itemid FROM ".DB::table('common_tagitem')." WHERE tagid IN (".dimplode($tagidarray).") AND idtype='$type' LIMIT $limitsum");
	$i = 1;
	while($result = DB::fetch($query)) {
		if($result['itemid'] != $tid) {
			if($i > $limit) {
				break;
			}
			if($relatearray[$result[itemid]] == '') {
				$i++;
			}
			if($result['itemid']) {
				$relatearray[$result[itemid]] = $result['itemid'];
			}

		}
	}
	if(!empty($relatearray)) {
		$query = DB::query("SELECT tid,subject,displayorder FROM ".DB::table('forum_thread')." WHERE tid IN (".dimplode($relatearray).")");
		while($result = DB::fetch($query)) {
			if($result['displayorder'] >= 0) {
				$relateitem[] = $result;
			}
		}
	}
	return $relateitem;
}
function loadforum($tid,$fid) {
	global $_G;
	$tid = intval($tid);
	$fid = $fid;
	if(!$fid && getgpc('gid')) {
		$fid = intval(getgpc('gid'));
	}
	if(!empty($_G['gp_archiver'])) { 
		if($fid) {
			dheader('location: archiver/?fid-'.$fid.'.html');
		} elseif($tid) {
			dheader('location: archiver/?tid-'.$tid.'.html');
		} else {
			dheader('location: archiver/');
		}
	}
	if(defined('IN_ARCHIVER') && $_G['setting']['archiverredirect'] && !IS_ROBOT) {
		dheader('location: ../forum.php'.($_G['mod'] ? '?mod='.$_G['mod'].(!empty($_GET['fid']) ? '&fid='.$_GET['fid'] : (!empty($_GET['tid']) ? '&tid='.$_GET['tid'] : '')) : ''));
	}
	if($_G['setting']['forumpicstyle']) {
		$_G['setting']['forumpicstyle'] = unserialize($_G['setting']['forumpicstyle']);
		empty($_G['setting']['forumpicstyle']['thumbwidth']) && $_G['setting']['forumpicstyle']['thumbwidth'] = 214;
		empty($_G['setting']['forumpicstyle']['thumbheight']) && $_G['setting']['forumpicstyle']['thumbheight'] = 160;
	} else {
		$_G['setting']['forumpicstyle'] = array('thumbwidth' => 214, 'thumbheight' => 160);
	}
	if($fid) {
		$fid = is_numeric($fid) ? intval($fid) : (!empty($_G['setting']['forumfids'][$fid]) ? $_G['setting']['forumfids'][$fid] : 0);
	}

	$modthreadkey = isset($_G['gp_modthreadkey']) && $_G['gp_modthreadkey'] == modauthkey($tid) ? $_G['gp_modthreadkey'] : '';
	$_G['forum_auditstatuson'] = $modthreadkey ? true : false;

	$accessadd1 = $accessadd2 = $modadd1 = $modadd2 = $metadescription = $hookscriptmessage = '';
	$adminid = $_G['adminid'];
	if($_G['uid']) {
		if($_G['member']['accessmasks']) {
			$accessadd1 = ', a.allowview, a.allowpost, a.allowreply, a.allowgetattach, a.allowgetimage, a.allowpostattach, a.allowpostimage';
			$accessadd2 = "LEFT JOIN ".DB::table('forum_access')." a ON a.uid='$_G[uid]' AND a.fid=f.fid";
		}

		if($adminid == 3) {
			$modadd1 = ', m.uid AS ismoderator';
			$modadd2 = "LEFT JOIN ".DB::table('forum_moderator')." m ON m.uid='$_G[uid]' AND m.fid=f.fid";
		}
	}

	if(!empty($tid) || !empty($fid)) {

		if(!empty ($tid)) {
			$archiveid = !empty($_G['gp_archiveid']) ? intval($_G['gp_archiveid']) : null;
			$_G['thread'] = get_thread_by_tid($tid, '*', '', $archiveid);
			if(!$_G['forum_auditstatuson'] && !empty($_G['thread'])
					&& !($_G['thread']['displayorder'] >= 0 || (in_array($_G['thread']['displayorder'], array(-4,-3,-2)) && $_G['uid'] && $_G['thread']['authorid'] == $_G['uid']))) {
				$_G['thread'] = null;
			}

			$_G['forum_thread'] = & $_G['thread'];

			if(empty($_G['thread'])) {
				$fid = $tid = 0;
			} else {
				$fid = $_G['thread']['fid'];
				$tid = $_G['thread']['tid'];
			}
		}

		if($fid) {
			$forum = DB::fetch_first("SELECT f.fid, f.*, ff.* $accessadd1 $modadd1, f.fid AS fid
					FROM ".DB::table('forum_forum')." f
			LEFT JOIN ".DB::table("forum_forumfield")." ff ON ff.fid=f.fid $accessadd2 $modadd2
					WHERE f.fid='$fid'");
	}

	if($forum) {
		$forum['ismoderator'] = !empty($forum['ismoderator']) || $adminid == 1 || $adminid == 2 ? 1 : 0;
		$fid = $forum['fid'];
		$gorup_admingroupids = $_G['setting']['group_admingroupids'] ? unserialize($_G['setting']['group_admingroupids']) : array('1' => '1');

		if($forum['status'] == 3) {
			if(!$_G['setting']['groupstatus']) {
				showmessage('group_status_off');
			}
			if(!empty($forum['moderators'])) {
				$forum['moderators'] = unserialize($forum['moderators']);
			} else {
				require_once libfile('function/group');
				$forum['moderators'] = update_groupmoderators($fid);
			}
			if($_G['uid'] && $_G['adminid'] != 1) {
				$forum['ismoderator'] = !empty($forum['moderators'][$_G['uid']]) ? 1 : 0;
				$_G['adminid'] = 0;
				if($forum['ismoderator'] || $gorup_admingroupids[$_G['groupid']]) {
					$_G['adminid'] = $_G['adminid'] ? $_G['adminid'] : 3;
					if(!empty($gorup_admingroupids[$_G['groupid']])) {
						$forum['ismoderator'] = 1;
						$_G['adminid'] = 2;
					}

					$group_userperm = unserialize($_G['setting']['group_userperm']);
					if(is_array($group_userperm)) {
						$_G['group'] = array_merge($_G['group'], $group_userperm);
						$_G['group']['allowmovethread'] = $_G['group']['allowcopythread'] = $_G['group']['allowedittypethread']= 0;
					}
				}
			}
		}
		foreach(array('threadtypes', 'threadsorts', 'creditspolicy', 'modrecommend') as $key) {
			$forum[$key] = !empty($forum[$key]) ? unserialize($forum[$key]) : array();
			if(!is_array($forum[$key])) {
				$forum[$key] = array();
			}
		}

		if($forum['status'] == 3) {
			$_G['isgroupuser'] = 0;
			$_G['basescript'] = 'group';
			if(empty($forum['level'])) {
				$levelid = DB::result_first("SELECT levelid FROM ".DB::table('forum_grouplevel')." WHERE creditshigher<='$forum[commoncredits]' AND '$forum[commoncredits]'<creditslower LIMIT 1");
				$forum['level'] = $levelid;
				DB::query("UPDATE ".DB::table('forum_forum')." SET level='$levelid' WHERE fid='$fid'");
			}
			loadcache('grouplevels');
			$grouplevel = $_G['grouplevels'][$forum['level']];
			if(!empty($grouplevel['icon'])) {
				$valueparse = parse_url($grouplevel['icon']);
				if(!isset($valueparse['host'])) {
					$grouplevel['icon'] = $_G['setting']['attachurl'].'common/'.$grouplevel['icon'];
				}
			}

			$group_postpolicy = $grouplevel['postpolicy'];
			if(is_array($group_postpolicy)) {
				$forum = array_merge($forum, $group_postpolicy);
			}
			$forum['allowfeed'] = $_G['setting']['group_allowfeed'];
			if($_G['uid']) {
				if(!empty($forum['moderators'][$_G['uid']])) {
					$_G['isgroupuser'] = 1;
				} else {
					$_G['isgroupuser'] = DB::result_first("SELECT level FROM ".DB::table('forum_groupuser')." WHERE fid='$fid' AND uid='$_G[uid]' LIMIT 1");
					if($_G['isgroupuser'] <= 0 && empty($forum['ismoderator'])) {
						$_G['group']['allowrecommend'] = $_G['cache']['usergroup_'.$_G['groupid']]['allowrecommend'] = 0;
						$_G['group']['allowcommentpost'] = $_G['cache']['usergroup_'.$_G['groupid']]['allowcommentpost'] = 0;
						$_G['group']['allowcommentitem'] = $_G['cache']['usergroup_'.$_G['groupid']]['allowcommentitem'] = 0;
						$_G['group']['raterange'] = $_G['cache']['usergroup_'.$_G['groupid']]['raterange'] = array();
						$_G['group']['allowvote'] = $_G['cache']['usergroup_'.$_G['groupid']]['allowvote'] = 0;
					} else {
						$_G['isgroupuser'] = 1;
					}
				}
			}
		}
	} else {
		$fid = 0;
	}
	}

	$_G['fid'] = $fid;
	$_G['tid'] = $tid;
	$_G['forum'] = &$forum;
	$_G['current_grouplevel'] = &$grouplevel;

	if(isset($_G['cookie']['widthauto']) && $_G['setting']['switchwidthauto'] && empty($_G['forum']['widthauto'])) {
		$_G['forum_widthauto'] = $_G['cookie']['widthauto'] > 0;
	} else {
		$_G['forum_widthauto'] = empty($_G['forum']['widthauto']) ? !$_G['setting']['allowwidthauto'] : $_G['forum']['widthauto'] > 0;
		if(!empty($_G['forum']['widthauto'])) {
			$_G['setting']['switchwidthauto'] = 0;
		}
	}
	return $_G['forum'];
}

	
}

?>
<?php
require_once '../tool/tool.php';
class check {
	 
	public function viewperm() {
		global $_G;
		if ($_G ['forum'] ['viewperm'] && ! forumperm ( $_G ['forum'] ['viewperm'] ) && ! $_G ['forum'] ['allowview']) {
			$res = $this->topic_check ( 'viewperm', $_G ['fid'], $_G ['forum'] ['formulaperm'] );
			return array (
					'error' => 1,
					'message' => $res 
			);
			exit ();
		} elseif ($_G ['forum'] ['formulaperm']) {
			formulaperm ( $_G ['forum'] ['formulaperm'] );
		}
		return true;
	}
	
	 
	public function postperm($fid, $formula = '') {
		global $_G;
		if ($_G ['forum'] ['postperm'] && ! forumperm ( $_G ['forum'] ['postperm'] )) {
			$res = $this->topic_check ( 'postperm', $_G ['fid'], $_G ['forum'] ['formulaperm'] );
			return array (
					'error' => 1,
					'message' => $res 
			);
			exit ();
		} elseif ($_G ['forum'] ['formulaperm']) {
			formulaperm ( $_G ['forum'] ['formulaperm'] );
		}
	}
	
	 
	public function replyperm($fid, $formula = '') {
		global $_G;
		if ($_G ['forum'] ['replyperm'] && ! forumperm ( $_G ['forum'] ['replyperm'] )) {
			$res = $this->topic_check ( 'replyperm', $_G ['fid'], $_G ['forum'] ['formulaperm'] );
			return array (
					'error' => 1,
					'message' => $res 
			);
			exit ();
		} elseif ($_G ['forum'] ['formulaperm']) {
			formulaperm ( $_G ['forum'] ['formulaperm'] );
		}
	}
	
	 
	public function getattachperm($fid, $formula = '') {
		global $_G;
		if ($_G ['forum'] ['getattachperm'] && ! forumperm ( $_G ['forum'] ['getattachperm'] )) {
			$res = $this->topic_check ( 'getattachperm', $_G ['fid'], $_G ['forum'] ['formulaperm'] );
			return array (
					'error' => 1,
					'message' => $res 
			);
			exit ();
		} elseif ($_G ['forum'] ['formulaperm']) {
			formulaperm ( $_G ['forum'] ['formulaperm'] );
		}
	}
	
	 
	public function postattachperm($fid, $formula = '') {
		global $_G;
		if ($_G ['forum'] ['postattachperm'] && ! forumperm ( $_G ['forum'] ['postattachperm'] )) {
			$res = $this->topic_check ( 'postattachperm', $_G ['fid'], $_G ['forum'] ['formulaperm'] );
			return array (
					'error' => 1,
					'message' => $res 
			);
			exit ();
		} elseif ($_G ['forum'] ['formulaperm']) {
			formulaperm ( $_G ['forum'] ['formulaperm'] );
		}
	}
	 
	public function postimageperm($fid, $formula = '') {
		global $_G;
		if ($_G ['forum'] ['postimageperm'] && ! forumperm ( $_G ['forum'] ['postimageperm'] )) {
			$res = $this->topic_check ( 'postimageperm', $_G ['fid'], $_G ['forum'] ['formulaperm'] );
			return array (
					'error' => 1,
					'message' => $res 
			);
			exit ();
		} elseif ($_G ['forum'] ['formulaperm']) {
			formulaperm ( $_G ['forum'] ['formulaperm'] );
		}
	}
	 
	public function topic_check($type, $fid, $formula = '') {
		global $_G;
		loadcache ( 'usergroups' );
		
		if ($formula) {
			$formula = dunserialize ( $formula );
			$permmessage = stripslashes ( $formula ['message'] );
		}
		
		$usergroups = $nopermgroup = $forumnoperms = array ();
		$nopermdefault = array (
				'viewperm' => array (),
				'getattachperm' => array (),
				'postperm' => array (
						7 
				),
				'replyperm' => array (
						7 
				),
				'postattachperm' => array (
						7 
				) 
		);
		$perms = array (
				'viewperm',
				'postperm',
				'replyperm',
				'getattachperm',
				'postattachperm' 
		);
		
		foreach ( $_G ['cache'] ['usergroups'] as $gid => $usergroup ) {
			$usergroups [$gid] = $usergroup ['type'];
			$grouptype = $usergroup ['type'] == 'member' ? 0 : 1;
			$nopermgroup [$grouptype] [] = $gid;
		}
		if ($fid == $_G ['forum'] ['fid']) {
			$forum = $_G ['forum'];
		} else {
			$forum = C::t ( 'forum_forumfield' )->fetch ( $fid );
		}
		foreach ( $perms as $perm ) {
			$permgroups = explode ( "\t", $forum [$perm] );
			$membertype = $forum [$perm] ? array_intersect ( $nopermgroup [0], $permgroups ) : TRUE;
			$forumnoperm = $forum [$perm] ? array_diff ( array_keys ( $usergroups ), $permgroups ) : $nopermdefault [$perm];
			foreach ( $forumnoperm as $groupid ) {
				$nopermtype = $membertype && $groupid == 7 ? 'login' : ($usergroups [$groupid] == 'system' || $usergroups [$groupid] == 'special' ? 'none' : ($membertype ? 'upgrade' : 'none'));
				$forumnoperms [$fid] [$perm] [$groupid] = array (
						$nopermtype,
						$permgroups 
				);
			}
		}
		
		$v = $forumnoperms [$fid] [$type] [$_G ['groupid']] [0];
		$gids = $forumnoperms [$fid] [$type] [$_G ['groupid']] [1];
		$comma = $permgroups = '';
		if (is_array ( $gids )) {
			foreach ( $gids as $gid ) {
				if ($gid && $_G ['cache'] ['usergroups'] [$gid]) {
					$permgroups .= $comma . $_G ['cache'] ['usergroups'] [$gid] ['grouptitle'];
					$comma = ', ';
				} elseif ($_G ['setting'] ['verify'] ['enabled'] && substr ( $gid, 0, 1 ) == 'v') {
					$vid = substr ( $gid, 1 );
					$permgroups .= $comma . $_G ['setting'] ['verify'] [$vid] ['title'];
					$comma = ', ';
				}
			}
		}
		
		$custom = 0;
		if ($permmessage) {
			$message = $permmessage;
			$custom = 1;
		} else {
			if ($v) {
				$message = $type . '_' . $v . '_nopermission';
			} else {
				$message = 'group_nopermission';
			}
		}
		switch ($type) {
			case 'viewperm' :
				$str = Common::get_web_unicode_charset('\u8bbf\u95ee\u8be5\u7248\u5757 ');
				break;
			case 'postperm' :
				$str = Common::get_web_unicode_charset('\u53d1\u5e16 ');
				break;
			case 'replyperm' :
				$str = Common::get_web_unicode_charset('\u56de\u5e16 ');
				break;
			case 'getattachperm' :
				$str = Common::get_web_unicode_charset('\u4e0b\u8f7d\u672c\u9644\u4ef6 ');
				break;
			case 'postattachperm' :
				$str = Common::get_web_unicode_charset('\u4e0a\u4f20\u9644\u4ef6 ');
				break;
			case 'postimageperm' :
				$str = Common::get_web_unicode_charset('\u8fdb\u884c\u6b64\u64cd\u4f5c ');
				break;
		}
		$error_message = Common::get_web_unicode_charset('\u62b1\u6b49\uff0c\u60a8\u6ca1\u6709\u6743\u9650') . $str;
		if ($custom) {
			return '{"rs":0,"error":"99999999","message":"' . $message . '"}';
		} else {
			return '{"rs":0,"error":"99999999","message":"' . $error_message . '"}';
		}
	}
}
<?php

class topic {
	public function parseQuoteMessage($message){
		global $_G;
		foreach($_G['cache']['smilies']['searcharray'] as $key=>$val)
		{
			$message = preg_replace($val, "[$key]", $message);
		}
		$position = strpos($message, '[color');
		if($position > 0){
			$position2 = strpos($message, ']' , $position + 1);
			$position3 = strpos($message, '[/color]' , $position2 + 1);
			$str1 = substr($message, $position2 + 1, ($position3 - $position2 - 1) );
				
			$position4 = strpos($message, '[/size]') + 6;
			$position5 = strpos($message, '[/quote]' , $position4 + 1);
			$str2 = substr($message, $position4 + 1, ($position5 - $position4 - 1) );
			return $str1 .''. $str2;
		}else {
			return $message;
		}
	}
	public function getQuoteImg($_G,$postarr){
		foreach ( $postarr as $uid => $post ) {
			$postusers[$post['authorid']] = array();
			$quotemessage = '';
			if (strstr ( $post ['message'], '[quote]' ) != '') {
				$res = preg_match ( '\[color=#\d+\](.*)\[/color\]', $post ['message'], $quote );
				$postarr [$uid] ['quote_pid'] = $quote ['1'] [0];
				$postarr [$uid] ['is_quote'] = ( bool ) true;
				$postarr [$uid] ['message'] = preg_replace ( '#\[quote\][.\n\S\s]+\[/quote\]#', '', $post ['message'] );
				$quotemessage = $this->parseQuoteMessage($post ['message']);
			} else {
				$postarr [$uid] ['is_quote'] = ( bool ) false;
			}
			global $_G;
			$post = array_merge ( $postarr [$uid], ( array ) $postusers [$post ['authorid']] );
			foreach($_G['cache']['smilies']['searcharray'] as $key=>$val)
			{
				$post ['message'] = preg_replace($val, "[$key]", $post ['message']);
			}
			$post ['message'] = discuzcode ( $post ['message'], $post ['smileyoff'], $post ['bbcodeoff'] );
			$post ['message'] = str_replace('<img src="static/', '<img src="/static/', $post ['message']);
			$post ['authortitle'] = $_G ['cache'] ['usergroups'] [$post ['groupid']] ['grouptitle'];
			$post ['quotemessage'] = $quotemessage;
			if($post ['attachment'] == 2)
				$pids [] = $post ['pid'];
	
			$postlist [$post ['pid']] = $post;
		}
		return $postlist;
	}
	public function parseTopic($_G,$postlist,$forumclass,$temp,$uid){
		foreach ( $postlist as $pid => $post ) {
			$uids[]=$post ['authorid'];
			preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i", $postlist [$pid] ['message'], $matches );
			unset ( $imgs );
			unset ( $pattern );
			$aids = $matches['1'];
			
			$query = DB::query("SELECT * FROM ".DB::table(getattachtablebytid($post['tid']))." a WHERE a.pid IN (".$post['pid'].") AND isimage IN ('1', '-1')");
			while($arr=DB::fetch($query))
			{
				$Allaids[] = $arr['aid'];
				$attachmentAid[$arr['aid']][] = $arr;
			}
			if(count($aids) !== count($Allaids))
			{
				$query = DB::query("SELECT * FROM ".DB::table(getattachtablebytid(25))." a WHERE a.pid IN (".$post['pid'].") and isimage IN ('1', '-1')");
				while($rows=DB::fetch($query))
				{
					if(in_array($rows['aid'], $aids))
					{
						continue;
					}
					$attachmentAid[$rows['pid']][] = $rows;
				}
				foreach($attachmentAid[$pid] as $key=>$val)
				{
					$path = $val['attachment'];
					$filename = $this->parseTargeImage($path);
					$imgs [] = '<img src="' .$filename. '" />';
					$attachmentId = $val['aid'];
				
					$postlist [$pid] ['message'] = $postlist [$pid] ['message'].'[attach]'.$attachmentId.'[/attach]';
					$pattern [] = "#\[attach\]" . $key . "\[\/attach\]#i";
				}
				preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i", $postlist [$pid] ['message'], $matches );
				unset($attachmentAid);
				unset($Allaids);
			}
			
			if(!empty($matches) && is_array($matches))
			{
				foreach ( $matches [1] as $k => $v ) {
					$filename = $this->parseTargeImage($attachmentAid [$v][0] );
					
					$imgs [] = '<img src="' .$filename. '" />';
		
					$attachmentId = $v;
				}
				
			}
			else
			{
				if(is_array($attachmentAid) && !empty($attachmentAid))
				{
					foreach($attachmentAid as $key =>$val)
					{
						$filename = $this->parseTargeImage($attachmentAid ['attachment']);
							
						$imgs [] = '<img src="' .$filename. '" />';
						
						$attachmentId = $v;
					}
				}
			}
			foreach ( $matches [1] as $k => $v ) {
				$pattern [] = "#\[attach\]" . $v . "\[\/attach\]#i";
			}
			$message_string = preg_replace ( $pattern, $imgs, $postlist [$pid] ['message'] );
			if ($message_string) {
				$postlist [$pid] ['message'] = $message_string;
			}
			$tempPostlist = $forumclass->viewthread_procpost($postlist [$pid], $_G['member']['lastvisit'], 2, 2);
			$topicContent = text_replace($tempPostlist ['message']);
		
			$topicContent = discuzcode ( $topicContent, $post ['smileyoff'], $post ['bbcodeoff'] );
			$topicContent = str_replace('<img src="static/', '<img src="/static/', $topicContent);
			$postlist [$pid] ['message'] = text_replace($topicContent);
			$postlist [$pid] ['message'] = preg_replace( "/\<font class=\"jammer\">.+\<\/font>/i",'' ,$postlist [$pid] ['message']);
			$postlist [$pid] ['message'] = preg_replace( "/\<span style=\"display:none\">.+\<\/span>/i",'' ,$postlist [$pid] ['message']);
			$img_url = $post ['attachments'] [$attachmentId] ['url'];
		
			$font [$pid] ['quote_content'] = getContent ( $post ['message'] );
			$font [$pid] ['quote_content'] = preg_replace( "/\[attach\]\d+\[\/attach\]/i",'' ,$font [$pid] ['quote_content']);
			$tempPostlist_quote_content = $forumclass->viewthread_procpost($post, $_G['member']['lastvisit'], 2, 2);
			$topicContent_quote_content = text_replace($tempPostlist_quote_content ['quotemessage']);
		
			$topicContent_quote_content = discuzcode ( $topicContent_quote_content, $post ['smileyoff'], $post ['bbcodeoff'] );
			$topicContent_quote_content = str_replace('<img src="static/', '<img src="/static/', $topicContent_quote_content);
			$font [$pid] ['quote_content'] = text_replace($topicContent_quote_content);
		
		}
		$uids = array_unique($uids);
		
		$uids =empty($uids)?array(-1):$uids;
	
		$fav = C::t ( 'home_favorite' )->fetch_by_id_idtype ( $_G ['tid'], 'tid', $uid );
		if (!empty($fav)) {
			$is_favor = 1;
		} else {
			$is_favor = 0;
		}
		$data_profile = C::t ( 'common_member_profile' )->get_profile_by_uid ( $uids, 'gender' );
	
		$query = C::t('common_member') -> getUserStatus($uids);
		while ($arr =DB::fetch($query))
		{
			$member[$arr['uid']] ['status'] = $arr['status'];
		}
		
		$special = C::t('forum_thread') ->get_special_by_tid($_G ['tid']);
		if($special == 2)
		{
			$i =0;
			foreach ( $postlist as $key => $val ) {
				if($i ==1)
				{
					$key1 =$key;
				}
				$i ++;
			}
		}
		unset($postlist[$key1]);
		$_user=Anonymous_User($_G ['tid']);
		foreach ( $postlist as $key => $val ) {
		
			$post = $tags = array ();
			
			$post['gender'] = (int)$data_profile [$val['authorid']] ['gender'];
			$post['level'] = (int)$data_profile [$val['authorid']] ['stars'];
			$tags = explode ( ',', $val ['tags'] );
			global $_G;
			preg_match_all( "/\[(\d+)\]+/i",$val ['message'],$smailyArr);
			$thisUrl=dirname('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'/../../';
			foreach($smailyArr[1] as $key =>$Sval)
			{
				/*rx 20131016 tie zi xiang qing biao qing chu li*/
				$smArr = DB::fetch(DB::query("SELECT * FROM ".DB::table('common_smiley')." where id=".$Sval));
				$smTypeArr = DB::fetch(DB::query("SELECT directory FROM ".DB::table('forum_imagetype')." where typeid=".$smArr['typeid']));
				$smUrl = $thisUrl.'static/image/'.$smArr['type'].'/'.$smTypeArr['directory'].'/'.$smArr['url'];
				$smile .= "[mobcent_phiz=".$smUrl."]";
				/*end rx 20131016*/
				$_G['cache']['smilies']['searcharray'][$Sval] = str_replace('/', "",$_G['cache']['smilies']['searcharray'][$Sval]);
				$val ['message'] = str_replace($smailyArr[0][$key], $smile,$val ['message']);
				unset($smile);
			}
			if ($tags [0] > 0 || $temp == 0) {
				$thread = C::t ( 'forum_thread' )->fetch_all_by_tid ( $val ['tid'] );
				$message1 = doContent ( $val ['message'],$val[imagelist]);
				$message2 = getContentFont ( $val ['message'] );
				
				foreach($message1 as $k=>$v){
					if($v['type']==0){
						unset($message1[$k]);
					}else{
				
					}
				}
				
				$message_array2 = explode('|~|', $message2);
				
				$message2 = str_replace('[', '1', $message2);
				$message2 = str_replace(']', '1', $message2);
				if(is_array($message_array2) && count($message_array2)>0){
				
					foreach($message_array2 as $k=>$v){
						$message[]=array("infor" =>$v,"originalInfo" =>'',"type"=>0);
						if($message1[$k]["infor"] && !empty($message1)){
							$message[]=$message1[$k];
						}
					}
				}else{
					$message =getContentFont($val ['message']);
				}
				$post ['hits'] = ( int ) $thread [$val ['tid']]['views'];
				$post ['replies'] = ( int ) $thread [$val ['tid']]['replies'];
				$post ['essence'] = ( int ) $thread [$val ['tid']]['digest'] >0 || ( int ) $thread[$val ['tid']]['icon'] ==9 || (int ) $thread[$val ['tid']]['stamp'] ==0? 1 : 0;
				$post ['content'] = $message;
				$post ['create_date'] = $val ['dateline'] . "000"; 
				$post ['icon']		=userIconImg($val ['authorid']);
				$post ['is_favor'] = ( int ) $is_favor;
			if($val ['invisible'] == -5 || $val ['invisible'] == -1)
				{
					$post ['status'] = ( int ) 0;
					$arr['rs'] =0;
					$arr['errcode'] ='01040007';
					return $arr;exit();
				}
				else if($thread['closed'] == 1)
				{
					$post ['status'] =2;
				}
				else
				{
					$post ['status'] =1;
				}
				if(empty($val['authorid'])){
					$post ['status'] =0;
				}
				$post ['title'] = $val ['subject'];
				$post['topic_id'] = ( int )$val['tid'];
				$post ['user_id'] = ( int ) $val ['authorid']; 
				if(empty($val ['author']) && isset($val['authorid']) && !empty($val['authorid']))
				{
					$post ['reply_status'] = (int)'-1 ';
					$post ['user_nick_name'] = Common::get_unicode_charset('\u8be5\u7528\u6237\u5df2\u88ab\u5220\u9664');
				}
				else if(empty($val['author']) && empty($val['authorid']))
				{
					$post ['reply_status'] = (int)'0 ';
					$post ['user_nick_name'] = Common::get_unicode_charset('\u533f\u540d\u7528\u6237');
				}
				else
				{
					$post ['reply_status'] = (int)'1 ';
					$post ['user_nick_name'] = $val['author'];
				}
				$post ['reply_posts_id'] = ( int ) $val ['pid']; 
				$info = C::t('home_surrounding_user')->fetch_all_by_pid($val ['pid']);
				if(empty($info))
				{
					$post ['location'] ='';
		
				}
				else
				{
					$post ['location'] = $info ['location'];
				}
		
				$data_post ["topic"] = $post;
			} else {
				
				$message1 = doContent ( $val ['message'] );
				$message2 = getContentFont ( $val ['message'] );
		
				foreach($message1 as $k=>$v){
					if($v['type']==0){
						unset($message1[$k]);
					}else{
		
					}
				}
		
				$message_array2 = explode('|~|', $message2);
				$message2 = str_replace('[', '1', $message2);
				$message2 = str_replace(']', '1', $message2);
				if(is_array($message_array2) && count($message_array2)>0){
		
					foreach($message_array2 as $k=>$v){
						$message[]=array("infor" =>$v,"type"=>0);
						if($message1[$k]["infor"] && !empty($message1)){
							$message[]=$message1[$k];
						}
					}
				}else{
					$message =getContentFont($val ['message']);
				}
		
				$post ['location'] = "";
				$post ['icon']		=userIconImg($val ['authorid']);
				$post ['posts_date'] = $val ['dateline'] . "000"; 
				$post ['reply_content'] = $message;
				$post ['reply_id'] = ( int ) $val ['authorid']; 
				if(empty($val ['author']) && isset($val['authorid']) && !empty($val['authorid']))
				{
					$post ['reply_status'] = (int)'-1 ';
					$post ['reply_name'] = Common::get_unicode_charset('\u8be5\u7528\u6237\u5df2\u88ab\u5220\u9664');
				}
				else if(empty($val ['author']) && empty($val ['authorid']))
				{
					$post ['reply_status'] = (int)'0 ';
					$post ['reply_name'] = Common::get_unicode_charset('\u533f\u540d\u7528\u6237');
				}
				else
				{
					$post ['reply_status'] = (int)'1 ';
					$post ['reply_name'] = $val ['author'];
				}
				$post ['reply_posts_id'] = ( int ) $val ['pid']; 
				if($val ['invisible'] == -5)
				{
					$post ['status'] = ( int ) 0;
					$arr['rs'] =0;
					$arr['errcode'] ='01040007';
					return $arr;exit();
				}
				else if($thread['closed'] == 1)
				{
					$post ['status'] =2;
				}
				else
				{
					$post ['status'] =1;
				}
		
				$post ['title'] = $val ['subject']; 		
				$post ['role_num'] = $val ['groupid']; 
				$post ['is_quote'] = ( bool ) $val ['is_quote'];
				$post ['quote_pid'] = $val ['quote_pid'];
				if((bool) $val ['is_quote'] != false){
					$post ["quote_content"] =preg_replace( "/\[attach\]\d+\[\/attach\]/i",'',preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2", $font [$post ['reply_posts_id']]['quote_content']));
				}else
					$post ["quote_content"] = '';
				$post ["quote_user_name"] = $font [$val ['quote_pid']] ['quote_user_name'];
				$post ['position'] = $temp;
				$info = C::t('home_surrounding_user') ->fetch_all_by_pid($val ['pid']);
				if(empty($info))
				{
					$post ['location'] ='';
		
				}
				else
				{
					$post ['location'] = $info ['location'];
				}
				$data_post ['list'] [] = $post;
				unset ( $quote );
				unset ( $pid );
			}
			unset($attachmentAid);
			unset ( $post );
			unset($message);
			$temp ++;
		}
		return $data_post;
	}
	
	public function parseTradeTopic($_G,$post)
	{
		$rows = C::t('forum_thread')->fetch_all_by_tid($_G['tid']);
		$tpids = array();
		if($rows[$_G['tid']]['special'] == 2) {
			$trade_query = DB::query("SELECT * FROM ".DB::table('forum_trade')." WHERE tid='".$_G['tid']."'  ORDER BY displayorder");
			while($trade = DB::fetch($trade_query)) {
				$tradesaids[] = $trade['aid'];
				$tradespids[] = $trade['pid'];
				$query[]=$trade;
			}
			$specialadd2 = 1;
			if($tradespids) {
				$tradespids = implode(',', $tradespids);
				$thread = DB::query("SELECT * FROM ".DB::table(getattachtablebytid($_G['tid']))." WHERE pid IN ($tradespids)");
				while($attach = DB::fetch($thread)) {
					if($attach['isimage'] && is_array($tradesaids) && in_array($attach['aid'], $tradesaids)) {
						$trades[$attach['pid']]['attachurl'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
						$trades['thumb'] = $attach['thumb'] ? getimgthumbname($trades[$attach['pid']]['attachurl']) : $trades[$attach['pid']]['attachurl'];
						$trades[$attach['pid']]['width'] = $attach['thumb'] && $_G['setting']['thumbwidth'] < $attach['width'] ? $_G['setting']['thumbwidth'] : $attach['width'];
						$trades[$attach['pid']]['thumb'] = str_replace('forum/', '', $trades[$attach['pid']]['thumb']);
						$filename = $this->parseTradeTopicImg($trades[$attach['pid']]);
						$info[]= array('infor' => $filename,'type' => 1);
					}
				}
			}
			$tradepostlist = C::t('forum_post')->fetch_all($_G['tid'], $tradespids);
			foreach($query as $trade) {
				$quality = $trade['quality']==1 ? Common::get_unicode_charset('\u5168\u65b0\u5546\u54c1'):Common::get_unicode_charset('\u4e8c\u624b\u5546\u54c1');
				$transport =  $trade['transport'];
				switch ($trade['transport'])
				{
					case 3:
						$transport = Common::get_unicode_charset('\u865a\u62df\u5546\u54c1');
						break;
					case 2:
						$transport = '';
						break;
					case 0:
						$transport = Common::get_unicode_charset('\u7ebf\u4e0b\u4ea4\u6613');
						break;
				}
				$time = $trade['expiration'] - time();
				$time = explode('.',($time/3600/24));
				$time = intval($time[0]).Common::get_unicode_charset('\u5929').(intval(('0.'.$time[1])*24)).Common::get_unicode_charset('\u5c0f\u65f6'); 
				$message[]= array('infor' => Common::get_unicode_charset('\u5546\u54c1\u7c7b\u578b\u003a').$quality,'type' => 0);
				$message[]= array('infor' => Common::get_unicode_charset('\u8fd0\u8d39\u003a').$transport,'type' => 0);
				$message[]= array('infor' => Common::get_unicode_charset('\u5269\u4f59\u65f6\u95f4\u003a').$time,'type' => 0);
				$message[]= array('infor' => Common::get_unicode_charset('\u5546\u54c1\u6570\u91cf\u003a').$trade['amount'],'type' => 0);
				$message[]= array('infor' => Common::get_unicode_charset('\u5730\u70b9\u003a').$trade['locus'],'type' => 0);
				$message[]= array('infor' => Common::get_unicode_charset('\u7d2f\u8ba1\u552e\u51fa\u003a').$trade['totalitems'],'type' => 0);
			}
			foreach($tradepostlist as $val)
			{
				
				$topicContent = text_replace($val ['message']);
				
				$topicContent = discuzcode ( $topicContent, $val ['smileyoff'], $val ['bbcodeoff'] );
				$topicContent = str_replace('<img src="static/', '<img src="/static/', $topicContent);
				$val['message'] = text_replace($topicContent);
				
				$message1 = doContent ( $val ['message'] );
				$message2 = getContentFont ( $val ['message'] );
				
				foreach($message1 as $k=>$v){
					if($v['type']==0){
						unset($message1[$k]);
					}else{
				
					}
				}
				
				$message_array2 = explode('|~|', $message2);
				
				$message2 = str_replace('[', '1', $message2);
				$message2 = str_replace(']', '1', $message2);
				if(is_array($message_array2) && count($message_array2)>0){
				
					foreach($message_array2 as $k=>$v){
						$message[]=array("infor" =>$v,"type"=>0);
						if($message1[$k]["infor"] && !empty($message1)){
							$message[]=$message1[$k];
						}
					}
				}else{
					$message =getContentFont($val ['message']);
				}
			}
		}
		return $message;
	}
	
	public function parseTradeTopicImg($group)
	{
		$ret_graph_small_picture_path = '/data/attachment/forum/mobcentSmallPreview/';
		$ret_graph_big_picture_path = '/data/attachment/forum/mobcentBigPreview/';
		$origan_path = $this->getRootFolder() . "/../../data/attachment/forum/" .$group ['attachment'];
		$origan = explode('/',$group ['attachment']);
		$origan_path_date =$this->getRootFolder() . "/../../data/attachment/forum/".date('Ym/d',$group ['dateline']).'/' .$origan[count($origan)-1];
		$path=pathinfo( $group ['attachment']);
		$fileName = $path['filename'].'.'.$path['extension'];
		$Url = explode('/',$this->getRootFolder());
		unset($Url[count($Url)-1]);
		$Url = implode('/',$Url);
			
			
		$ret_suffix = $ret_graph_small_picture_path . $group ['attachment'];
		$ret_path_attachmentImg = $this->getRootFolder() .'/..' . $ret_suffix;
		$ret_path_small = $this->getRootFolder() . '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
		$ret_path_big = $this->getRootFolder() . '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
		$size = 160;
		$sizeBig = 480;
		if(file_exists($origan_path)){
			if(file_exists($ret_path_attachmentImg))
			{
				$filename = '/mobcent' . $ret_suffix; 
			}
			else
			{
					
				$pic = new Thumbnail($origan_path);
					
				if($pic->zoomcutPic($origan_path,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path,$ret_path_big,$fileName ,$sizeBig))
				{
					$filename = '/mobcent' . $ret_graph_small_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}else if(file_exists($origan_path_date))
		{
			$path=pathinfo( date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1]);
			$fileName = $path['filename'].'.'.$path['extension'];
			$Url = explode('/',$this->getRootFolder());
			unset($Url[count($Url)-1]);
			$Url = implode('/',$Url);
			
			
			$ret_suffix = $ret_graph_small_picture_path . '/'.date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
			$ret_path_attachmentImg = $this->getRootFolder() .'/..' . $ret_suffix;
			$ret_path_small = $this->getRootFolder() . '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
			$ret_path_big = $this->getRootFolder() . '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
				
			if(file_exists($ret_path_attachmentImg))
			{
				$filename = '/mobcent' . $ret_suffix;  
			}
			else
			{
					
				$pic = new Thumbnail($origan_path_date);
					
				if($pic->zoomcutPic($origan_path_date,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path_date,$ret_path_big,$fileName ,$sizeBig))
				{
					$filename = '/mobcent' . $ret_graph_small_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}
		
		return $filename;
	}
	function parseTargeBigImage($trades){

		$ret_graph_small_picture_path = '/data/attachment/forum/mobcentSmallPreview/';
		$ret_graph_big_picture_path = '/data/attachment/forum/mobcentBigPreview/';
		$origan_path = $this->getRootFolder() . "/../../data/attachment/forum/" .$trades;
		$path=pathinfo( $trades);
		$fileName = $path['filename'].'.'.$path['extension'];
		$Url = explode('/',$this->getRootFolder());
		unset($Url[count($Url)-1]);
		$Url = implode('/',$Url);
			
			
		$ret_suffix = $ret_graph_big_picture_path . $trades;
		$ret_path_attachmentImg = $this->getRootFolder() .'/..' . $ret_suffix;
		$ret_path_small = $this->getRootFolder() . '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
		$ret_path_big = $this->getRootFolder() . '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
		$size = 160;
		$sizeBig = 480;
		
		if(file_exists($origan_path)){
			if(file_exists($ret_path_attachmentImg))
			{
				$filename = '/mobcent' . $ret_suffix; 
			}
			else
			{
					
				$pic = new Thumbnail($origan_path);
					
				if($pic->zoomcutPic($origan_path,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path,$ret_path_big,$fileName ,$sizeBig))
				{
					$filename = '/mobcent' . $ret_graph_big_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}
		
		return $filename;
	}
	function parseTargeImage($pic)
	{
		$ret_graph_small_picture_path = '/data/attachment/forum/mobcentSmallPreview/';
		$ret_graph_big_picture_path = '/data/attachment/forum/mobcentBigPreview/';
		$origan_path = $this->getRootFolder() . "/../../data/attachment/forum/" .$pic ['attachment'];
		$origan = explode('/',$pic ['attachment']);
		$origan_path_date =$this->getRootFolder() . "/../../data/attachment/forum/".date('Ym/d',$pic ['dateline']) .$origan[count($origan)-1];
		$path=pathinfo( $pic['attachment']);
		$fileName = $path['filename'].'.'.$path['extension'];
		$Url = explode('/',$this->getRootFolder());
		unset($Url[count($Url)-1]);
		$Url = implode('/',$Url);
	
	
		$ret_suffix = $ret_graph_small_picture_path . $pic['attachment'];
		$ret_path_attachmentImg = $this->getRootFolder() .'/..' . $ret_suffix;
		$ret_path_small = $this->getRootFolder() . '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
		$ret_path_big = $this->getRootFolder() . '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
		$size = 160;
		$sizeBig = 480;
		if(file_exists($origan_path)){
			if(file_exists($ret_path_attachmentImg))
			{
				$pic_path = '/mobcent'. $ret_suffix; 
			}
			else
			{
	
				$pic = new Thumbnail($origan_path);
	
				if($pic->zoomcutPic($origan_path,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path,$ret_path_big,$fileName ,$sizeBig))
				{
					$pic_path = '/mobcent'.$ret_graph_small_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
	
			}
			
		}else if(file_exists($origan_path_date))
		{
			$path=pathinfo( date('Ym/d',$pic['dateline']) .'/'.$origan[count($origan)-1]);
			$fileName = $path['filename'].'.'.$path['extension'];
			$Url = explode('/',$this->getRootFolder());
			unset($Url[count($Url)-1]);
			$Url = implode('/',$Url);
				
				
			$ret_suffix = $ret_graph_small_picture_path . '/'.date('Ym/d',$pic['dateline']) .'/'.$origan[count($origan)-1];
			$ret_path_attachmentImg = $this->getRootFolder() .'/..' . $ret_suffix;
			$ret_path_small = $this->getRootFolder() . '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
			$ret_path_big = $this->getRootFolder() . '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
			
			if(file_exists($ret_path_attachmentImg))
			{
				$pic_path = '/mobcent' . $ret_suffix;  
			}
			else
			{
					
				$pic = new Thumbnail($origan_path_date);
					
				if($pic->zoomcutPic($origan_path_date,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path_date,$ret_path_big,$fileName ,$sizeBig))
				{
					$pic_path = '/mobcent' . $ret_graph_small_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}
		return $pic_path;
	
	}
	function parseTargeThumbImage($picPath)
	{
		$ret_graph_small_picture_path = '/data/attachment/forum/mobcentSmallPreview/';
		$ret_graph_big_picture_path = '/data/attachment/forum/mobcentBigPreview/';
		$origan = explode('/',$picPath ['attachment']);
		$origan_path_date =$this->getRootFolder() . "/../../data/attachment/forum/".date('Ym/d',$picPath['dateline']) .'/'.$origan[count($origan)-1];
		$origan_path = $this->getRootFolder() . "/../../data/attachment/forum/" .$picPath ['attachment'];
		$path=pathinfo( $picPath ['attachment']);
		$path['filename']=$path['filename'].'_240';
		$fileName = $path['filename'].'.'.$path['extension'];
		$ret_suffix = '/data/attachment/forum/thumbnail/'.$path['dirname'].'/'. $fileName;
		$ret_path_attachmentImg = $this->getRootFolder() .'/..' . $ret_suffix;
		$ret_path = $this->getRootFolder() . '/../data/attachment/forum/thumbnail/'.$path['dirname'].'/';
		$size = 240;
		if(file_exists($origan_path)){
			if(file_exists($ret_path_attachmentImg))
			{
				$pic_path = '/mobcent'. $ret_suffix;
			}
			else
			{
		
				$pic = new Thumbnail($origan_path);
		
				if($pic->zoomcutPic($origan_path,$ret_path ,$fileName ,$size))
					{
						$pic_path = '/mobcent/data/attachment/forum/thumbnail/'.$path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
					}
		
			}
			
		}else if(file_exists($origan_path_date))
		{
			$path=pathinfo( date('Ym/d',$picPath['dateline']) .'/'.$origan[count($origan)-1]);
			$fileName = $path['filename'].'.'.$path['extension'];
			$Url = explode('/',$this->getRootFolder());
			unset($Url[count($Url)-1]);
			$Url = implode('/',$Url);
			
			
			$ret_suffix = $ret_graph_small_picture_path . '/'.date('Ym/d',$picPath['dateline']) .'/'.$origan[count($origan)-1];
			$ret_path_attachmentImg = $this->getRootFolder() .'/..' . $ret_suffix;
			$ret_path_small = $this->getRootFolder() . '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
			$ret_path_big = $this->getRootFolder() . '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
			$size = 160;
			if(file_exists($ret_path_attachmentImg))
			{
				$pic_path = '/mobcent' . $ret_suffix;  
			}
			else
			{
					
				if($pic->zoomcutPic($origan_path_date,$ret_path ,$fileName ,$size))
				{
					$pic_path = '/mobcent/data/attachment/forum/thumbnail/'.$path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}
		return $pic_path;
	}
	function getRootFolder(){
		/*$path = str_replace("\\", "/", $path);
		if(strpos($path, 'model/table/x25') > 0){
			$path = substr($path, 0 , strlen($path) -4 );
		}*/
		return dirname(__FILE__).'/../..';
	}
	/*xml*/
	public function struct_to_array($item){
		if(!is_string($item)){
			$item =(array)$item;
			foreach($item as $key=>$val){
				$item[$key]  = $this->struct_to_array($val);
			}
		return $item;
		}else{
			$arr[0][]=$item;
			return $arr;
		}
	}
	
	public function xml_to_array($xml)
	{
		$array =(array)(simplexml_load_string($xml));
		foreach ($array as $key=>$item){
			$array[$key]  = $this->struct_to_array((array)$item);
		}
		return $array;
	}
	
	public function replaceHtmlAndJs($document)
	{
		$document = trim($document);
		if (strlen($document) <= 0) {
			return $document;
		}
		$search = array ("'<script[^>]*?>.*?</script>'si",
				"'<[\/\!]*?[^<>]*?>'si",
					
				"'&(quot|#34);'i",
				"'&(amp|#38);'i",
				"'&(lt|#60);'i",
				"'&(gt|#62);'i",
				"'&(nbsp|#160);'i"
		);
		$replace = array ("",
				"",
	
				"\"",
				"&",
				"<",
				">",
				" "
		);
		return @preg_replace ($search, $replace, $document);
	}
}

?>
<?php
/*
	dsu_czw_mergeReply (C)2007-2010 jhdxr
	This is NOT a freeware, use is subject to license terms

	$Id: hook.class.php  jhdxr 2010-11-24 12:54$
*/
!defined('IN_DISCUZ') && exit('Access Denied');
class plugin_dsu_czw_mergeReply {

	var $identifier = 'dsu_czw_mergeReply';
	var $cvars=array();

	function  __construct() {
		global $_G;
		$this->cvars = $_G['cache']['plugin']['dsu_czw_mergeReply'];
		$this->cvars['fids'] = (array)unserialize($this->cvars['fids']);
		$this->cvars['gids'] = (array)unserialize($this->cvars['gids']);
		$this->cvars['limit_rule'] = (array)unserialize($this->cvars['limit_rule']);
		$this->cvars['except_rule'] = (array)unserialize($this->cvars['except_rule']);
	}

	function strlen($str){
		global $_G;
		return mb_strlen($str, $_G['config']['output']['charset']);
	}
}

class plugin_dsu_czw_mergeReply_forum extends plugin_dsu_czw_mergeReply {
	
	function __construct(){
		parent::__construct();
	}
	
	function post_merge(){
		global $_G;
		
		if($_POST && $_G['gp_action'] == 'reply' && in_array($_G['fid'], $this->cvars['fids']) && in_array($_G['groupid'], $this->cvars['gids'])){
			$message = $message2 = $_G['gp_message'];
			$needmerge = true;

			if($needmerge && !empty($_G['gp_repquote']) && in_array('repquote', $this->cvars['except_rule'])) $needmerge = false;
			if($needmerge && !empty($_G['gp_reppost']) && in_array('reppost', $this->cvars['except_rule'])) $needmerge = false;
			if($needmerge && !empty($_G['gp_comment']) && in_array('comment', $this->cvars['except_rule'])) $needmerge = false;

			if($needmerge && $this->cvars['limit_count'] > 0){
				empty($_G['cache']['smileycodes']) && loadcache(array('bbcodes_display', 'bbcodes', 'smileycodes', 'smilies', 'smileytypes', 'icons', 'domainwhitelist'));
				if(in_array('smile', $this->cvars['limit_rule'])) $message2 = str_replace($_G['cache']['smileycodes'], '', $message2);
				if(in_array('quote', $this->cvars['limit_rule'])) $message2 = preg_replace("/\s*\[quote\][\n\r]*(.+?)[\n\r]*\[\/quote\]\s*/is", '', $message2);
				if(in_array('space', $this->cvars['limit_rule'])) $message2 = preg_replace("/\s/is", '', $message2);
				$length = $this->strlen(trim($message2));
				$needmerge = $length < $this->cvars['limit_count'];
			}
			
			if($needmerge){
				$p = DB::fetch_first("SELECT authorid, pid, dateline, message FROM ".DB::table('forum_post')." WHERE tid='$_G[tid]' ORDER BY pid DESC LIMIT 1");
				if($p && $p['authorid'] == $_G['uid'] && ($this->cvars['limit_time'] == 0 || TIMESTAMP - $p['dateline'] <= $this->cvars['limit_time'])){
					$pid = $p['pid'];
					$mergeMsg = "\n\n\n".$this->cvars['showmsg'];
					$mergeMsg = str_replace('{{user}}', $_G['username'], $mergeMsg);
					$mergeMsg = str_replace('{{time}}', dgmdate(TIMESTAMP), $mergeMsg);
					$mergeMsg = str_replace('{{message}}', $message, $mergeMsg);
					if($this->cvars['maxlen'] == 0 || $this->strlen($p['message'].$mergeMsg) < $this->cvars['maxlen']){
						DB::query("UPDATE ".DB::table('forum_post')." SET message = CONCAT(message, '$mergeMsg') WHERE pid='$pid'", 'UNBUFFERED');
						$url = "forum.php?mod=viewthread&tid={$_G[tid]}&pid=$pid&page={$_G[gp_page]}&extra={$_G[gp_extra]}#pid$pid";
						$param = array('fid' => $_G['fid'], 'tid' => $_G['tid'], 'pid' => $pid, 'from' => $_G['gp_from'], 'sechash' => !empty($_G['gp_sechash']) ? $_G['gp_sechash'] : '');
						showmessage('post_reply_succeed', $url, $param);
					}
				}
			}
		}
	}
}
?>
<?php
/*
	[DSU] Thief
	Author: Marco129[http://www.my3talk.com]
	Please respect the author, do not delete the copyright!
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_dsu_marcothief {

	var $version = '1.0';
	var $name = "[DSU] &#25105;&#26159;&#31070;&#20599; - &#36148;&#36523;&#20445;&#38230;";
	var $description = "&#22312;&#19968;&#27573;&#26102;&#38388;&#20869;&#20813;&#21463;&#20182;&#20154;&#25171;&#21163;&#24744;&#30340;&#31215;&#20998;";
	var $price = '500';
	var $weight = '20';
	var $useevent = 1;
	var $targetgroupperm = false;
	var $copyright = '<a href="http://www.dsu.cc" target="_blank">Marco129 @ DSU Team</a>';
	var $magic = array();
	var $parameters = array();

	function getsetting(&$magic) {
		$settings = array(
			'hrs' => array(
				'title' => lang('plugin/dsu_marcothief', 'magic_1'),
				'type' => 'text',
				'value' => '',
				'default' => 24,
			),
		);
		return $settings;
	}

	function setsetting(&$magicnew, &$parameters) {
		$magicnew['hrs'] = intval(abs($parameters['hrs']));
	}

	function usesubmit() {
		global $_G;
		$user = !empty($_G['gp_id']) ? htmlspecialchars($_G['gp_id']) : '';
		if($user) {
			$member = getuserinfo($user, array('groupid'));
			$this->_check($member['groupid']);
		}
		$user_db = DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcothief')." WHERE uid='$_G[uid]'");
		if(!$user_db){
			DB::query("INSERT INTO ".DB::table('dsu_marcothief')." (uid) VALUES ('$_G[uid]')");
		}
		$time = $this->parameters['hrs']*60*60 + $_G['timestamp'];
		DB::query("UPDATE ".DB::table('dsu_marcothief')." SET protect='$time' WHERE uid='$_G[uid]'");
		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', '0', 'uid', $_G['uid']);
		showmessage('dsu_marcothief:magic_2', '', array(), array('showdialog' => 1));
	}

	function show() {
		magicshowtips(lang('plugin/dsu_marcothief', 'magic_3', array('hrs' => $this->parameters['hrs'])));
	}
	
	function _check($groupid) {
		if(!checkmagicperm($this->parameters['targetgroups'], $groupid)) {
			showmessage('dsu_marcothief:magic_4');
		}
	}
	
}

?>
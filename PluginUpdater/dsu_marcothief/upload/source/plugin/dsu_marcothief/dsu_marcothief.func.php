<?php
/*
	[DSU] Thief
	Author: Marco129[http://www.my3talk.com]
	Please respect the author, do not delete the copyright!
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function log_add($username, $type, $extra = array()){
	global $_G, $config;
	if($type == 'thief_sucess'){
		$action = lang('plugin/dsu_marcothief', 'log_thief_sucess', array('username' => $username, 'earn' => $_G['setting']['extcredits'][$config['credit']]['title'].$extra['thief_get'].$_G['setting']['extcredits'][$config['credit']]['unit']));
	}elseif($type == 'thief_fail'){
		$action = lang('plugin/dsu_marcothief', 'log_thief_fail', array('username' => $username));
	}elseif($type == 'thief_fail_jail'){
		$action = lang('plugin/dsu_marcothief', 'log_fail_jail', array('username' => $username));
	}elseif($type == 'jail_raids'){
		$action = lang('plugin/dsu_marcothief', 'log_jail_raids', array('username' => $username, 'raids_user' => $extra['raids_user']));
	}else{
		$action = '';
	}
	if($action){
		DB::query("INSERT INTO ".DB::table('dsu_marcothief_log')." (log,time) VALUES ('$action','$_G[timestamp]')");
		return TRUE;
	}else{
		return FALSE;
	}
}

?>
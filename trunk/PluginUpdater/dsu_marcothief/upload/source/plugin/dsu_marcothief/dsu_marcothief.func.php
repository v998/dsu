<?php
/*
	[DSU] Thief
	Author: Marco129[http://www.my3talk.com]
	Please respect the author, do not delete the copyright!
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function is_admin($showmsg = TRUE){
	global $_G, $config;
	$admin_array = explode(',', $config['admin_list']);
	if(!in_array($_G['uid'], $admin_array)){
		if($showmsg == TRUE){
			showmessage('group_nopermission', 'plugin.php?id=dsu_marcothief', array('grouptitle' => $_G['group']['grouptitle']));
		}else{
			return FALSE;
		}
	}else{
		return TRUE;
	}
}

function log_add($username, $type, $extra = array()){
	global $_G, $config;
	if($type == 'thief_sucess'){
		$action = lang('plugin/dsu_marcothief', 'log_thief_sucess', array('username' => $username, 'earn' => $_G['setting']['extcredits'][$config['credit']]['title'].$extra['thief_get'].$_G['setting']['extcredits'][$config['credit']]['unit']));
	}elseif($type == 'thief_fail'){
		$action = lang('plugin/dsu_marcothief', 'log_thief_fail', array('username' => $username));
	}elseif($type == 'log_fail_guard'){
		$action = lang('plugin/dsu_marcothief', 'log_fail_guard', array('username' => $username));
	}elseif($type == 'thief_fail_police'){
		$action = lang('plugin/dsu_marcothief', 'log_fail_police', array('username' => $username));
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

function use_shop($uid, $type){
	global $_G, $config;
	if(in_array($type, array('1', '2', '3'))){
		$type_detail = array(1 => 'weapon', 2 => 'raids_tool', 3 => 'run_tool');
		$shop_db = DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcothief')." t,".DB::table('dsu_marcothief_shop')." s WHERE t.{$type_detail[$type]}=s.id AND t.uid='$_G[uid]'");
		if(!$shop_db){
			return FALSE;
		}else{
			return array($shop_db['function']/100, $shop_db['function']);
		}
	}else{
		return FALSE;
	}
}

?>
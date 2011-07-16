<?php
/*
	[DSU] Thief
	Author: Marco129[http://www.my3talk.com]
	Please respect the author, do not delete the copyright!
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

@require_once './source/plugin/dsu_marcothief/dsu_marcothief.func.php';
if(!$_G['uid']) {
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}else{
	$config = $_G['cache']['plugin']['dsu_marcothief'];
	$user_db = DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcothief')." t,".DB::table('common_member_count')." c WHERE t.uid=c.uid AND t.uid='$_G[uid]'");
	if(!$user_db){
		DB::query("INSERT INTO ".DB::table('dsu_marcothief')." (uid) VALUES ('$_G[uid]')");
	}
	$log_total = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_marcothief_log')."");
	$start_limit = ($log_total<=30) ? 0 : ($log_total-30);
	$query = DB::query("SELECT * FROM ".DB::table('dsu_marcothief_log')." ORDER BY time DESC LIMIT $start_limit, $log_total");
	$log = array();
	while($data = DB::fetch($query)){
		$data['time'] = dgmdate($data['time'], 't', $_G['setting']['timeoffset']);
		$log[] = $data;
	}
}

if(empty($mod)){
	$jail_left = intval(($user_db['jail']-$_G['timestamp'])/60);
	if(submitcheck('robbery')){
		$check = DB::fetch_first("SELECT * FROM ".DB::table('common_member')." m,".DB::table('common_member_count')." c WHERE m.uid=c.uid AND ".($_G['gp_method'] ? "m.username='$_G[gp_user]'" : "m.uid='$_G[gp_user]'")."");
		$check_db = DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcothief')." t,".DB::table('common_member_count')." c WHERE t.uid=c.uid AND t.uid='$check[uid]'");
		if($user_db['jail'] > $_G['timestamp']){
			showmessage('dsu_marcothief:msg_9', dreferer());
		}elseif($user_db['action'] >= $config['max_thief']){
			showmessage('dsu_marcothief:msg_7', dreferer());
		}elseif($user_db['extcredits'.$config['fee']] < $config['fee_once']){
			showmessage('dsu_marcothief:msg_1', dreferer(), array('title' => $_G['setting']['extcredits'][$config['fee']]['title']));
		}elseif(!$check){
			showmessage('dsu_marcothief:msg_2', dreferer());
		}elseif(in_array($check['groupid'], unserialize($config['protect_groups']))){
			showmessage('dsu_marcothief:msg_16', dreferer());
		}elseif($check_db['actions'] >= $config['max_steal']){
			showmessage('dsu_marcothief:msg_10', dreferer());
		}elseif($check['extcredits'.$config['credit']] < $config['credit_protect']){
			showmessage('dsu_marcothief:msg_4', dreferer(), array('title' => $_G['setting']['extcredits'][$config['credit']]['title']));
		}elseif($check['uid'] == $_G['uid']){
			showmessage('dsu_marcothief:msg_3', dreferer());
		}else{
			$rand = mt_rand(0, intval($config['max_thief_limit']));
			$jail = (round((mt_rand(0, 100))/100, 1) <= $config['jail_percentage']) ? TRUE : FALSE;
			$success = (round((mt_rand(0, 100))/100, 1) <= $config['sucess_percentage']) ? TRUE : FALSE;
			updatemembercount($_G['uid'], array('extcredits'.$config['fee'] => 'extcredits'.$config['fee']-$config['fee_once']));
			if($rand == 0 || $jail == TRUE || $success == FALSE){
				DB::query("UPDATE ".DB::table('dsu_marcothief')." SET thief=thief+'1',fail=fail+'1',action=action+'1' WHERE uid='$_G[uid]'");
				DB::query("UPDATE ".DB::table('dsu_marcothief')." SET actions=actions+'1' WHERE uid='$check[uid]'");
				notification_add($check['uid'], 'system', lang('plugin/dsu_marcothief', 'notice_thief_fail'), array('username' => $_G['username']), 1);
				if($jail == TRUE){
					DB::query("UPDATE ".DB::table('dsu_marcothief')." SET jail='".($_G['timestamp']+60*$config['jail_mins'])."' WHERE uid='$_G[uid]'");
					log_add($_G['username'], 'thief_fail_jail');
					showmessage('dsu_marcothief:msg_8', dreferer(), array('mins' => $config['jail_mins']));
				}else{
					log_add($_G['username'], 'thief_fail');
					showmessage('dsu_marcothief:msg_5', dreferer(), array('username' => $check['username']));
				}
			}else{
				if(!$check_db){
					DB::query("INSERT INTO ".DB::table('dsu_marcothief')." (uid) VALUES ('$check[uid]')");
				}
				notification_add($check['uid'], 'system', lang('plugin/dsu_marcothief', 'notice_thief'), array('username' => $_G['username'], 'lose' => $_G['setting']['extcredits'][$config['credit']]['title'].$rand.$_G['setting']['extcredits'][$config['credit']]['unit']), 1);
				updatemembercount($_G['uid'], array('extcredits'.$config['credit'] => 'extcredits'.$config['credit']+$rand));
				updatemembercount($check['uid'], array('extcredits'.$config['credit'] => 'extcredits'.$config['credit']-$rand));
				DB::query("UPDATE ".DB::table('dsu_marcothief')." SET thief=thief+'1',total=total+'$rand',sucess=sucess+'1',action=action+'1' WHERE uid='$_G[uid]'");
				DB::query("UPDATE ".DB::table('dsu_marcothief')." SET steal=steal+'1',lose=lose+'$rand',actions=actions+'1' WHERE uid='$check[uid]'");
				log_add($_G['username'], 'thief_sucess', array('thief_get' => $rand));
				showmessage('dsu_marcothief:msg_6', dreferer(), array('username' => $check['username'], 'earn' => $_G['setting']['extcredits'][$config['credit']]['title'].$rand.$_G['setting']['extcredits'][$config['credit']]['unit']));
			}
		}
	}
	
}elseif($mod == 'jail'){
	if(submitcheck('uid')){
		if(($_G['gp_uid'] == $_G['uid']) || ($user_db['jail'] > $_G['timestamp'])){
			showmessage('dsu_marcothief:msg_11', dreferer());
		}elseif($user_db['extcredits'.$config['raids_credit']] < $config['raids_once']){
			showmessage('dsu_marcothief:msg_21', dreferer(), array('title' => $_G['setting']['extcredits'][$config['raids_credit']]['title']));
		}else{
			$jail = (round((mt_rand(0, 100))/100, 1) <= $config['raids_percentage']) ? TRUE : FALSE;
			$success = (round((mt_rand(0, 100))/100, 1) <= $config['raids_sucess_percentage']) ? TRUE : FALSE;
			$raid_user_info = getuserbyuid($_G['gp_uid']);
			updatemembercount($_G['uid'], array('extcredits'.$config['raids_credit'] => 'extcredits'.$config['raids_credit']-$config['raids_once']));
		}
		if($jail == TRUE){
			notification_add($_G['gp_uid'], 'system', lang('plugin/dsu_marcothief', 'notice_raids_fail'), array('username' => $_G['username']), 1);
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET jail='".($_G['timestamp']+60*$config['raids_mins'])."' WHERE uid='$_G[uid]'");
			showmessage('dsu_marcothief:msg_12', dreferer(), array('mins' => $config['raids_mins']));
		}elseif($success == TRUE){
			notification_add($_G['gp_uid'], 'system', lang('plugin/dsu_marcothief', 'notice_raids'), array('username' => $_G['username']), 1);
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET jail='0',run='0',goodluck='0' WHERE uid='$_G[gp_uid]'");
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET raids=raids+'1' WHERE uid='$_G[uid]'");
			log_add($_G['username'], 'jail_raids', array('raids_user' => $raid_user_info['username']));
			showmessage('dsu_marcothief:msg_13', dreferer());
		}else{
			notification_add($_G['gp_uid'], 'system', lang('plugin/dsu_marcothief', 'notice_raids_fail'), array('username' => $_G['username']), 1);
			showmessage('dsu_marcothief:msg_14', dreferer());
		}
	}elseif(submitcheck('run')){
		if($user_db['run'] > $_G['timestamp']){
			showmessage('dsu_marcothief:msg_15', dreferer(), array('mins' => round(($user_db['run']-$_G['timestamp'])/60, 0)));
		}
		$success = (round((mt_rand(0, 100))/100, 1) <= $config['run_percentage']) ? TRUE : FALSE;
		if($success == TRUE){
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET jail='0',run='0',goodluck='0' WHERE uid='$_G[uid]'");
			showmessage('dsu_marcothief:msg_17', dreferer());
		}else{
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET run='".($_G['timestamp']+60*$config['run_mins'])."' WHERE uid='$_G[uid]'");
			showmessage('dsu_marcothief:msg_18', dreferer());
		}
	}elseif(submitcheck('money')){
		if($user_db['extcredits'.$config['raids_credit']] < $config['bribe_money']){
			showmessage('dsu_marcothief:msg_20', dreferer(), array('credit' => $_G['setting']['extcredits'][$config['raids_credit']]['title']));
		}else{
			updatemembercount($_G['uid'], array('extcredits'.$config['raids_credit'] => 'extcredits'.$config['raids_credit']-$config['bribe_money']));
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET jail='0',run='0',goodluck='0' WHERE uid='$_G[uid]'");
			showmessage('dsu_marcothief:msg_19', dreferer());
		}
	}elseif(submitcheck('goodluck')){
		$getluck_user = getuserbyuid(intval($_G['gp_goodluck_uid']));
		if($user_db['goodluck'] > $_G['timestamp']){
			showmessage('dsu_marcothief:msg_22', dreferer(), array('mins' => ($user_db['goodluck']-$_G['timestamp'])/60));
		}elseif(!$getluck_user){
			showmessage('username_nonexistence', dreferer());
		}else{
			notification_add(intval($_G['gp_goodluck_uid']), 'system', lang('plugin/dsu_marcothief', 'notice_goodluck'), array('username' => $_G['username']), 1);
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET goodluck='".($_G['timestamp']+$config['goodluck_mins'])."' WHERE uid='$_G[uid]'");
			showmessage('dsu_marcothief:msg_23', dreferer());
		}
	}else{
		$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_marcothief')." WHERE jail>'$_G[timestamp]'");
		$page = intval($_G['page']);
		$page = 10 && $page > 10 ? 1 : $page;
		$start_limit = ($page - 1) * 10;
		$multipage = multi($num, 10, $page, "plugin.php?id=dsu_marcothief&mod={$mod}", 10);
		$sql = "SELECT * FROM ".DB::table('dsu_marcothief')." t,".DB::table('common_member')." m WHERE t.uid=m.uid AND t.jail>'$_G[timestamp]' ORDER BY t.jail LIMIT $start_limit, 10";
		$query = DB::query($sql);
		$list = array();
		while($data = DB::fetch($query)){
			$data['jail'] = dgmdate($data['jail'], 'dt', $_G['setting']['timeoffset']);
			$list[] = $data;
		}
	}
	
}elseif($mod == 'list'){
	$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_marcothief')."");
	$page = intval($_G['page']);
	$page = 10 && $page > 10 ? 1 : $page;
	$start_limit = ($page - 1) * 10;
	$multipage = multi($num, 10, $page, ($_G['gp_by'] == 'raids') ? "plugin.php?id=dsu_marcothief&mod={$mod}&by={$_G[gp_by]}" : "plugin.php?id=dsu_marcothief&mod={$mod}", 10);
	$sql = "SELECT * FROM ".DB::table('dsu_marcothief')." t,".DB::table('common_member')." m WHERE t.uid=m.uid ORDER BY ".(($_G['gp_by'] == 'raids') ? 't.raids' : 't.total')." DESC LIMIT $start_limit, 10";
	$query = DB::query($sql);
	$list = array();
	while($data = DB::fetch($query)){
		$list[] = $data;
	}

}else{
	showmessage('undefined_action', NULL, 'HALTED');
}

include template('dsu_marcothief:dsu_marcothief');

?>
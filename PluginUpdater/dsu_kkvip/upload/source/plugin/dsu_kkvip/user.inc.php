<?php
if(!defined('IN_ADMINCP')) exit('Access Denied');
echo '<script src="static/js/calendar.js" type="text/javascript"></script>';
if (file_exists(DISCUZ_ROOT.'./data/plugindata/dsu_kkvip.lang.php')){
	include DISCUZ_ROOT.'./data/plugindata/dsu_kkvip.lang.php';
	$_T=$scriptlang['dsu_kkvip'];
}else{
	loadcache('pluginlanguage_script');
	$_T=$_G['cache']['pluginlanguage_script']['dsu_kkvip'];
}
if(!$_G['gp_username']){
	showtableheader($_T['edit_user']);
	showformheader('plugins&operation=config&identifier=dsu_kkvip&pmod=user');
	showsetting($_T['username'], 'username', '', 'text');
	showsubmit('search', 'search');
	showformfooter();
	showtablefooter();
}elseif(!submitcheck('submit')){
	$uid = DB::result_first('SELECT uid FROM '.DB::table('common_member')." WHERE username='{$_G[gp_username]}'");
	if(!$uid) cpmsg($_T['user_not_exist'], '', 'error', array('username' => $_G['gp_username']));
	require_once libfile('class/vip');
	$vip = $vip ? $vip : new vip();
	if($vip->is_vip($uid)){
		$vip_info = $vip->getvipinfo($uid);
		showtableheader($_T['modify_vip_info']);
		showformheader("plugins&operation=config&identifier=dsu_kkvip&pmod=user&username={$_G[gp_username]}");
		showsetting($_T['jointime'], 'jointime', dgmdate($vip_info['jointime'], 'dt'), 'text', true);
		showsetting($_T['exptime'], 'exptime', dgmdate($vip_info['exptime'], 'd'), 'calendar');
		showsetting($_T['year_vip'], 'year_pay', $vip_info['year_pay'], 'radio');
		showsetting($_T['czz'], 'czz', $vip_info['czz'], 'number');
		showsubmit('submit');
		showformfooter();
		showtablefooter();
	}else{
		showtableheader($_T['help_pay']);
		showformheader("plugins&operation=config&identifier=dsu_kkvip&pmod=user&username={$_G[gp_username]}");
		showsetting($_T['pay_time'], 'time', '30', 'number');
		showsubmit('submit');
		showformfooter();
		showtablefooter();
	}
}else{
	$user = DB::fetch_first('SELECT uid, groupid FROM '.DB::table('common_member')." WHERE username='{$_G[gp_username]}'");
	if(!$user['uid']) cpmsg($_T['user_not_exist'], '', 'error', array('username'=>$_G['gp_username']));
	require_once libfile('class/vip');
	$vip = $vip ? $vip : new vip();
	if($vip->is_vip($user['uid'])){
		$czz = intval($_G['gp_czz']);
		DB::update('dsu_vip', array(
			'exptime' => strtotime($_G['gp_exptime']),
			'year_pay' => $_G['gp_year_pay'] ? 1 : 0,
			'czz' => $czz
		), array('uid' => $user['uid']));
		if($czz < 600){
			$level = 1;
		}elseif($czz >= 600 && $czz < 1800){
			$level = 2;
		}elseif($czz >= 1800 && $czz < 3600){
			$level = 3;
		}elseif($czz >= 3600 && $czz < 6000){
			$level = 4;
		}elseif($czz >= 6000 && $czz < 10800){
			$level = 5;
		}elseif($czz >= 10800){
			$level = 6;
		}
		$vip->query("UPDATE pre_dsu_vip SET level='{$level}' WHERE uid='{$user[uid]}'");
		$vip->query("UPDATE pre_common_member SET groupid={$vip->group[1]} WHERE uid='{$user[uid]}' AND adminid=0");
		require_once libfile('function/cache');
		updatecache('dsu_kkvip');
	}else{
		$vip->pay_vip($user['uid'], intval($_G['gp_time']), $user['groupid']);
	}
	cpmsg($_T['user_edit_succeed'], 'action=plugins&operation=config&identifier=dsu_kkvip&pmod=user', 'succeed');
}
?>
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
error_reporting(E_ALL ^ E_NOTICE);
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
		showsetting($_T['update_cache'], 'update_cache', true, 'radio');
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
		DB::update('dsu_vip', array(
			'exptime' => strtotime($_G['gp_exptime']),
			'year_pay' => $_G['gp_year_pay'] ? 1 : 0,
			'czz' => intval($_G['gp_czz'])
		), array('uid' => $user['uid']));
		if($_G['gp_update_cache']){
			require_once libfile('function/cache');
			updatecache('dsu_kkvip');
		}
	}else{
		pay_vip($user['uid'], intval($_G['gp_time']), $user['groupid']);
	}
	cpmsg($_T['user_edit_succeed'], 'action=plugins&operation=config&identifier=dsu_kkvip&pmod=user', 'succeed');
}
?>
<?php
/*
	[DSU] Time to credit
	Author: Marco129[http://my3talk.com]
	Please respect the author, do not delete the copyright!
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['uid']){
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}
$navigation = lang('plugin/dsu_marcot2c', 'name');
$navtitle = $navigation;
$config = $_G['cache']['plugin']['dsu_marcot2c'];
$config['min_mins'] = abs($config['min_mins']);
$config['mins'] = abs($config['mins']);
$config['money'] = abs($config['money']);
$config['min_ol'] = abs($config['min_ol']);
$config['money_2'] = abs($config['money_2']);
$config['mins_2'] = abs($config['mins_2']);
$config['msg_2'] = str_replace("{mins}",$config['min_mins'],$config['msg_2']);
$config['msg_6'] = str_replace("{money}",$config['min_ol'],$config['msg_6']);

if($config['mins'] == 0 || $config['money_2'] == 0){
	if($_G['adminid'] == 1){
		showmessage('dsu_marcot2c:error_1');
	}else{
		showmessage('dsu_marcot2c:error_2', 'index.php');
	}
}

$fonder_array=explode(',',$_G['config']['admincp']['founder']);
if(in_array($_G['uid'],$fonder_array)){
	$update_notice = '<script src="http://www.dsu.cc/plugin.php?id=dsu_api:api_reg&opt=get_ver&iden=dsu_marcot2c&dv=X1.5&ver=[X1.5]V0.32"></script>';
}

if(empty($mod)){
	$ol_db = DB::fetch_first("SELECT * FROM ".DB::table('common_onlinetime')." WHERE uid='$_G[uid]'");
	$ol_db['lastupdate']=dgmdate($ol_db['lastupdate'], 'dt', $_G['setting']['timeoffset']);
	if(submitcheck('submit')){
		$time = intval($_G['gp_time']);
		if(!$time) {
			showmessage("$config[msg_3]", dreferer());
		}
		if($time > $ol_db['total']){
			showmessage("$config[msg_1]", dreferer());
		}
		if($time < $config['min_mins']){
			showmessage("$config[msg_2]", dreferer());
		}
		$change = floor(($time/$config['mins'])*($config['money']));
		updatemembercount($_G['uid'], array('extcredits'.$config['credit'].'' => $change), true, '', 0, '');
		DB::query("UPDATE ".DB::table('common_onlinetime')." SET total=total-$time WHERE uid='$_G[uid]'");
		showmessage('dsu_marcot2c:msg_1', dreferer());
	}
	
}elseif($mod == 'money'){
	if($config['money_open'] == 0){
		showmessage('undefined_action', 'index.php');
	}
	$credit_got = $_G['member']['extcredits'.$config['credit'].''];
	if(submitcheck('submit')){
		$credit = intval($_G['gp_credit']);
		if(!$credit) {
			showmessage("$config[msg_7]", dreferer());
		}
		if($credit > $_G['member']['extcredits'.$config['credit'].'']){
			showmessage("$config[msg_5]", dreferer());
		}
		if($credit < $config['min_ol']){
			showmessage("$config[msg_6]", dreferer());
		}
		$change = floor(($credit/$config['money_2'])*($config['mins_2']));
		updatemembercount($_G['uid'], array('extcredits'.$config['credit'].'' => "-$credit"), true, '', 0, '');
		DB::query("UPDATE ".DB::table('common_onlinetime')." SET total=total+$change WHERE uid='$_G[uid]'");
		showmessage('dsu_marcot2c:msg_2', dreferer());
	}
	
}else{
	showmessage('undefined_action', 'index.php');
}

include template('dsu_marcot2c:dsu_marcot2c');
function update_notice(){
	global $_G;
	$fonder_array=explode(',',$_G['config']['admincp']['founder']);
	if(!in_array($_G['uid'],$fonder_array)) return;
	include_once DISCUZ_ROOT.'./source/discuz_version.php';
	$info = '<script src="http://www.dsu.cc/plugin.php?id=dsu_api:api_reg&opt=get_ver&iden=dsu_marcot2c&dv=X1.5&ver=[X1.5]V0.31"></script>';
	return $info;
}
?>
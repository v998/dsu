<?php

define('IN_KKVIP', true);
define('CURSCRIPT', 'vip');

require './source/class/class_core.php';
$discuz = & discuz_core::instance();
$cachelist = array('plugin','dsu_kkvip');
$discuz->cachelist = $cachelist;
$discuz->init();
include_once libfile('class/vip');
$vip = $vip ? $vip : new vip();
if(!$vip->on) showmessage('undefined_action');
$is_vip=$vip->is_vip();
$_G['vip']=$vip->getvipinfo($_G['uid']);
$_G['vip']['exptime_text']=dgmdate($_G['vip']['exptime'],'d');

$_G['basescript']='vip';
$do=$_G['gp_do']?$_G['gp_do']:'vipcenter';
if (preg_match('/[^a-zA-Z0-9_]/', $do)) showmessage('undefined_action');
if (!$_G['uid']) showmessage('not_loggedin','member.php?mod=logging&action=login');

$vip_czz=$_G['vip']['year_vip']?$vip->vars['vip_czzday']:$vip->vars['vip_czzday']+$vip->vars['vip_czz_year'];

switch ($_G['vip']['level']) {
	case 5:
		$update_days=round((10800-$_G['vip']['czz'])/$vip_czz);
		$update_time=dgmdate($_G['timestamp']+$update_days*86400,'d');
		$next_level=6;
		break;
	case 4:
		$update_days=round((6000-$_G['vip']['czz'])/$vip_czz);
		$update_time=dgmdate($_G['timestamp']+$update_days*86400,'d');
		$next_level=5;
		break;
	case 3:
		$update_days=round((3600-$_G['vip']['czz'])/$vip_czz);
		$update_time=dgmdate($_G['timestamp']+$update_days*86400,'d');
		$next_level=4;
		break;
	case 2:
		$update_days=round((1800-$_G['vip']['czz'])/$vip_czz);
		$update_time=dgmdate($_G['timestamp']+$update_days*86400,'d');
		$next_level=3;
		break;
	case 1:
		$update_days=round((600-$_G['vip']['czz'])/$vip_czz);
		$update_time=dgmdate($_G['timestamp']+$update_days*86400,'d');
		$next_level=2;
		break;
}
$next_level = ($_G['vip']['level']>0 && $_G['vip']['level']<6) ? $_G['vip']['level'] + 1 : 0;

$vip_menu = array(
	'vipcenter' => lang('plugin/dsu_kkvip', 'vip_index'),
	'paycenter' => lang('plugin/dsu_kkvip', $vip->is_vip() ? 'vip_pay2' : 'vip_pay'),
	'give_vip' => lang('plugin/dsu_kkvip', 'vip_pay_give'),
	'mylevel' => lang('plugin/dsu_kkvip', 'vip_mylevel'),
	'new_vip' => lang('plugin/dsu_kkvip', 'new_vip'),
	'vip_top' => lang('plugin/dsu_kkvip', 'vip_top'),
	'year' => lang('plugin/dsu_kkvip', 'year'),
);
define('CURMODULE', $do);
runhooks();
/*
 * Here is the hook for attaching  menu item to the menu of plugin VIP
 * e.g. $vip_menu['action_id'] = 'link name';
 * and please remember to create a shortcut at VIP_DIR/module
 */
if (!file_exists(DISCUZ_ROOT."./source/plugin/dsu_kkvip/module/{$do}.inc.php")) showmessage('undefined_action');
include DISCUZ_ROOT."./source/plugin/dsu_kkvip/module/{$do}.inc.php";

?>
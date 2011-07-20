<?php
!defined('IN_KKVIP') && exit('Access Denied');

$vip_intro_array=explode("\n",$vip->vars['vip_intro']);
$vip_credit_name=$_G['setting']['extcredits'][$vip->vars['creditid']]['title'];
$vip_credit='extcredits'.$vip->vars['creditid'];
$query=DB::fetch($vip->query("SELECT {$vip_credit} FROM pre_common_member_count WHERE uid='{$_G[uid]}'"));
$my_credit=$query[$vip_credit];
$max_month=intval($my_credit/$vip->vars['vip_cost']);
foreach ($vip_intro_array as $text){
	$vip_intro.=$text?"<li>".$text."</li>\r\n":"";
}
if($_G['gp_getuid']){
	$give_user=DB::fetch_first('SELECT uid FROM '.DB::table('common_member')." WHERE username='{$_G[gp_username]}'");
	if (!$give_user['uid']){
		include template('common/header_ajax');
		echo lang('plugin/dsu_kkvip','uid_not_exists');
		include template('common/footer_ajax');
		exit();
	}
	include template('common/header_ajax');
	echo $give_user['uid'];
	include template('common/footer_ajax');
}elseif(submitcheck('month')){
	if (intval($_G['gp_uid'])!=$_G['gp_uid'] || $_G['gp_uid']<=0 || intval($_G['gp_month'])!=$_G['gp_month'] || $_G['gp_month']<=0) showmessage('undefined_action');
	$give_user=DB::fetch_first('SELECT uid, groupid FROM '.DB::table('common_member')." WHERE username='{$_G[gp_username]}'");
	if(!$give_user['uid']) showmessage('dsu_kkvip:uid_not_exists_return');
	include_once DISCUZ_ROOT.'./source/plugin/dsu_kkvip/vip.func.php';
	$vip->pay_vip($_G['uid'], $_G['gp_month']*30, $give_user['groupid']);
	notification_add($give_user['uid'], 'dsu_kkvip', lang('plugin/dsu_kkvip','notification_give'), array('month'=>$_G['gp_month'], 'username'=>$_G['username']), 1);
	updatemembercount($_G['uid'], array($vip->vars['creditid'] => -$vip->vars['vip_cost']*$_G['gp_month']), false);
	$trade_succeed	= true;
	$trade_user		= $give_user['uid'];
	showmessage('dsu_kkvip:give_succeed', 'vip.php?do=paycenter&action=give', array('month'=>$_G['gp_month'], 'giveto'=>$give_user['username']));
}else{
	include template('dsu_kkvip:paycenter_give');
}
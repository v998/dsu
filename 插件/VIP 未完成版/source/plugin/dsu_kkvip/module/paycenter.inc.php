<?php
!defined('IN_KKVIP') && exit('Access Denied');

$vip_intro_array=explode("\n",$vip->vars['vip_intro']);
$vip_credit_name=$_G['setting']['extcredits'][$vip->vars['creditid']]['title'];
$vip_credit='extcredits'.$vip->vars['creditid'];
if (in_array($_G['groupid'],unserialize($vip->vars['vip_discount_group']))){
	$vip->vars['vip_cost']=round($vip->vars['vip_cost']*('0.'.$vip->vars['vip_discount']));
}
if ($_G['groupid']==$vip->vars['vip_group']){
	$vip->vars['vip_cost']=round($vip->vars['vip_cost']*('0.'.$vip->vars['vip_discount2']));
}
$query=DB::fetch($vip->query("SELECT {$vip_credit} FROM pre_common_member_count WHERE uid='{$_G[uid]}'"));
$my_credit=$query[$vip_credit];
$max_month=intval($my_credit/$vip->vars['vip_cost']);
foreach ($vip_intro_array as $text){
	$vip_intro.=$text?"<li>".$text."</li>\r\n":"";
}
if($_G['gp_action']=='give'){
	if($_G['gp_getname']){
		if (intval($_G['gp_uid'])!=$_G['gp_uid'] || $_G['gp_uid']<=0) {
			include template('common/header_ajax');
			include template('common/footer_ajax');
			exit();
		}
		$give_user=DB::fetch_first('SELECT * FROM '.DB::table('common_member').' WHERE uid='.$_G['gp_uid']);
		if (!$give_user['username']){
			include template('common/header_ajax');
			echo lang('plugin/dsu_kkvip','uid_not_exists');
			include template('common/footer_ajax');
			exit();
		}
		include template('common/header_ajax');
		echo $give_user['username'];
		include template('common/footer_ajax');
	}elseif(submitcheck('month')){
		if (intval($_G['gp_uid'])!=$_G['gp_uid'] || $_G['gp_uid']<=0 || intval($_G['gp_month'])!=$_G['gp_month'] || $_G['gp_month']<=0) showmessage('undefined_action');
		$give_user=DB::fetch_first('SELECT * FROM '.DB::table('common_member').' WHERE uid='.$_G['gp_uid']);
		if(!$give_user['uid']) showmessage('dsu_kkvip:uid_not_exists_return');
		include_once DISCUZ_ROOT.'./source/plugin/dsu_kkvip/vip.func.php';
		pay_vip($give_user['uid'],$_G['gp_month']*30,$give_user['groupid']);
		notification_add($give_user['uid'],'dsu_kkvip',lang('plugin/dsu_kkvip','notification_give'),array('month'=>$_G['gp_month'],'username'=>$_G['username']),1);
		updatemembercount($_G['uid'], array($vip->vars['creditid']=>-$vip->vars['vip_cost']*$_G['gp_month']), false);
		showmessage('dsu_kkvip:give_succeed','vip.php?do=paycenter&action=give',array('month'=>$_G['gp_month'],'giveto'=>$give_user['username']));
	}else{
		include template('dsu_kkvip:paycenter_give');
	}
	exit();
}elseif (submitcheck('month')){
	if (intval($_G['gp_month'])!=$_G['gp_month'] || $_G['gp_month']<=0) showmessage('undefined_action');
	if ($_G['gp_discount_code']){
		$discount=DB::fetch($vip->query("SELECT * FROM pre_dsu_vip_codes WHERE code='{$_G[gp_discount_code]}'"));
		if ($discount['money']) DB::insert('dsu_vip_codelog', array('uid' => $_G['uid'],'code' => $_G['gp_discount_code'],'time' => TIMESTAMP,'money' => $discount['money']));
		if($discount['money']>$vip->vars['vip_cost']*$_G['gp_month']) $discount['money']=$vip->vars['vip_cost']*$_G['gp_month'];
		$discount_code=$_G['gp_discount_code'];
	}
	if ($my_credit < ($vip->vars['vip_cost']*$_G['gp_month']-$discount['money'])) showmessage('dsu_kkvip:buy_nomoney','vip.php?do=paycenter');
	$vip->pay_vip($_G['uid'],$_G['gp_month']*30);
	DB::delete('dsu_vip_codes', "code='{$discount_code}'");
	updatemembercount($_G['uid'], array($vip->vars['creditid']=>-($vip->vars['vip_cost']*$_G['gp_month']-$discount['money'])), false);
	showmessage('dsu_kkvip:buy_succeed','vip.php?do=paycenter',array('month'=>$_G['gp_month']));
}else{
	if($_G['gp_getmoney']){
		$discount_code=DB::fetch($vip->query("SELECT * FROM pre_dsu_vip_codes WHERE code='{$_G[gp_discount_code]}'"));
		if (!$discount_code['money']){
			include template('common/header_ajax');
			echo '0';
			include template('common/footer_ajax');
			exit();
		}
		include template('common/header_ajax');
		echo $discount_code['money'];
		include template('common/footer_ajax');
		exit();
	}
	include template('dsu_kkvip:paycenter');
}
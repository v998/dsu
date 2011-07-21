<?php
!defined('IN_KKVIP') && exit('Access Denied');

$vip_intro_array=explode("\n",$vip->vars['vip_intro']);
foreach ($vip_intro_array as $text){
	$vip_intro.=$text?"<li>".$text."</li>\r\n":"";
}
$vip_credit_name=$_G['setting']['extcredits'][$vip->vars['creditid']]['title'];
$vip_credit='extcredits'.$vip->vars['creditid'];
if (in_array($_G['groupid'], unserialize($vip->vars['vip_discount_group']))){
	$vip->vars['vip_cost']=round($vip->vars['vip_cost']*$vip->vars['vip_discount']);
}
if ($vip->is_vip()){
	$vip->vars['vip_cost']=round($vip->vars['vip_cost']*$vip->vars['vip_discount2']);
}
$query=DB::fetch($vip->query("SELECT {$vip_credit} FROM pre_common_member_count WHERE uid='{$_G[uid]}'"));
$my_credit=$query[$vip_credit];
$max_month=intval($my_credit/$vip->vars['vip_cost']);
if (submitcheck('month')){
	if (intval($_G['gp_month'])!=$_G['gp_month'] || $_G['gp_month']<=0) showmessage('undefined_action');
	if ($_G['gp_discount_code']){
		$discount=DB::fetch($vip->query("SELECT * FROM pre_dsu_vip_codes WHERE code='{$_G[gp_discount_code]}'"));
		if($discount['money'] > $vip->vars['vip_cost']*$_G['gp_month']) $discount['money']=$vip->vars['vip_cost']*$_G['gp_month'];
		if($discount['exptime'] < TIMESTAMP) $discount['money'] = 0;
	}
	if ($my_credit < ($vip->vars['vip_cost']*$_G['gp_month']-$discount['money'])) showmessage('dsu_kkvip:buy_nomoney','vip.php?do=paycenter');
	if($discount['only_once']) DB::delete('dsu_vip_codes', array('code'=>$discount['code']));
	DB::delete('dsu_vip_codes', "code='{$discount_code}'");
	updatemembercount($_G['uid'], array($vip->vars['creditid']=>-($vip->vars['vip_cost']*$_G['gp_month']-$discount['money'])), false);
	$vip->pay_vip($_G['uid'], $_G['gp_month']*30);
	$trade_succeed	= true;
	$trade_user		= $_G['uid'];
	showmessage('dsu_kkvip:buy_succeed','vip.php?do=paycenter',array('month'=>$_G['gp_month']));
}else{
	if($_G['gp_getmoney']){
		$discount_code=DB::fetch($vip->query("SELECT * FROM pre_dsu_vip_codes WHERE code='{$_G[gp_discount_code]}'"));
		if (!$discount_code['money'] || $discount_code['exptime'] < TIMESTAMP){
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
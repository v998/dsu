<?php
!defined('IN_KKVIP') && exit('Access Denied');
$vip_intro_array=explode("\n",$vip->vars['vip_intro']);
foreach ($vip_intro_array as $text){
	$vip_intro.=$text?"<li>".$text."</li>\r\n":"";
}
$page = $_G['gp_page'] ? intval($_G['gp_page']) : 1;
$start = ($page - 1) * 20;
$query = $vip->query("SELECT m.username,m.uid,v.level,v.czz,v.jointime FROM pre_dsu_vip v, pre_common_member m WHERE m.uid=v.uid ORDER BY v.jointime DESC LIMIT {$start},20");
while($value=DB::fetch($query)){
	if ($value['jointime']){
		$value['jointime']=dgmdate($value['jointime'],'u');
	}else{
		$value['jointime']=lang('plugin/dsu_kkvip','no_time');
	}
	$viparray[]=$value;
}
$amount = count($vip->vip_cache);
$multipage = multi($amount, 20, $page, 'vip.php?do=new_vip');
include template('dsu_kkvip:new_vip');
<?php
!defined('IN_KKVIP') && exit('Access Denied');
$_G['gp_formhash'] != $_G['formhash'] && exit('Access Denied');
if($_G['gp_year']){
	$vip->pay_vip($_G['uid'],360);
	showmessage('已经为您免费开通 年费 VIP 体验服务.','vip.php');
}elseif(!$vip->is_vip()){
	$vip->pay_vip($_G['uid'],7);
	showmessage('已经为您免费开通 7 天的 VIP 体验服务.','vip.php');
}
showmessage('您已经是 VIP 了，无法开通体验服务.','vip.php');
?>
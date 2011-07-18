<?php
!defined('IN_KKVIP') && exit('Access Denied');
$_G['gp_formhash'] != $_G['formhash'] && exit('Access Denied');
if(!$vip->is_vip()){
	showmessage('您不是 VIP 用户，无法注销.','vip.php');
}else{
	$vip->query("DELETE FROM pre_dsu_vip WHERE uid='{$_G[uid]}'");
	$vip->_update_vip_cache();
	showmessage('注销成功，您可以重新领取.','vip.php');
}
?>
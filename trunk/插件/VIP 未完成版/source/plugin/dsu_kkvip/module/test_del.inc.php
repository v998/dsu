<?php
!defined('IN_KKVIP') && exit('Access Denied');
$_G['gp_formhash'] != $_G['formhash'] && exit('Access Denied');
if(!$vip->is_vip()){
	showmessage('������ VIP �û����޷�ע��.','vip.php');
}else{
	$vip->query("DELETE FROM pre_dsu_vip WHERE uid='{$_G[uid]}'");
	$vip->_update_vip_cache();
	showmessage('ע���ɹ���������������ȡ.','vip.php');
}
?>
<?php
!defined('IN_KKVIP') && exit('Access Denied');
$_G['gp_formhash'] != $_G['formhash'] && exit('Access Denied');
if($_G['gp_year']){
	$vip->pay_vip($_G['uid'],360);
	showmessage('�Ѿ�Ϊ����ѿ�ͨ ��� VIP �������.','vip.php');
}elseif(!$vip->is_vip()){
	$vip->pay_vip($_G['uid'],7);
	showmessage('�Ѿ�Ϊ����ѿ�ͨ 7 ��� VIP �������.','vip.php');
}
showmessage('���Ѿ��� VIP �ˣ��޷���ͨ�������.','vip.php');
?>
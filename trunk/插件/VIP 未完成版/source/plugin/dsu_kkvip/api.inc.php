<?php
if(!defined('IN_ADMINCP')) exit('Access Denied');
$extends = array();
$extends_dir = @dir(DISCUZ_ROOT.'./source/plugin/dsu_kkvip/extend/');
while(false !== ($entry = $extends_dir->read())) {
	$file = pathinfo($entry);
	if($file['extension'] == 'php' && $file['basename']) {
		include DISCUZ_ROOT."./source/plugin/dsu_kkvip/extend/{$file[basename]}";
		$extends[$ext_name] = $file['basename'];
	}
}
if($_G['gp_api'] && in_array($_G['gp_api'], $extends)){
	include DISCUZ_ROOT."./source/plugin/dsu_kkvip/extend/{$_G[gp_api]}";
	dexit();
}
if(!$extends) cpmsg('��û�а�װ�κ� VIP �ӿ�', '', 'error');
showtableheader('VIP �ӿ��б�');
foreach($extends as $name => $file){
	showtablerow('', array('', 'width="20%"'), array($name, '<a href="?action=plugins&operation=config&identifier=dsu_kkvip&pmod=api&api='.$file.'">����</a>'));
}
showtablefooter();
?>
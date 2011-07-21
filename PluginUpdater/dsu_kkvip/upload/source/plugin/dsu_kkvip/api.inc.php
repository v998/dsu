<?php
if(!defined('IN_ADMINCP')) exit('Access Denied');
require_once DISCUZ_ROOT.'./source/plugin/dsu_kkvip/kk_lang.func.php';
$extends = array();

if($_G['gp_api']){
	include DISCUZ_ROOT."./source/plugin/dsu_kkvip/extend/{$_G[gp_api]}";
	dexit();
}
$extends_dir = @dir(DISCUZ_ROOT.'./source/plugin/dsu_kkvip/extend/');
while(false !== ($entry = $extends_dir->read())) {
	$file = pathinfo($entry);
	if($file['extension'] == 'php' && $file['basename']) {
		if(!$_G['gp_api']) include DISCUZ_ROOT."./source/plugin/dsu_kkvip/extend/{$file[basename]}";
		$extends[$ext_name] = $file['basename'];
	}
}
if(!$extends) cpmsg(klang('no_extends'), '', 'error');
showtableheader(klang('extend_list'));
foreach($extends as $name => $file){
	showtablerow('', array('', 'width="20%"'), array($name, '<a href="?action=plugins&operation=config&identifier=dsu_kkvip&pmod=api&api='.$file.'">'.klang('extend_config').'</a>'));
}
showtablefooter();
?>
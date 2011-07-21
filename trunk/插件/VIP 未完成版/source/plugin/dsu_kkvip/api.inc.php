<?php
if(!defined('IN_ADMINCP')) exit('Access Denied');
$extends = array();
if (file_exists(DISCUZ_ROOT.'./data/plugindata/dsu_kkvip.lang.php')){
	include DISCUZ_ROOT.'./data/plugindata/dsu_kkvip.lang.php';
	$_T=$scriptlang['dsu_kkvip'];
}else{
	loadcache('pluginlanguage_script');
	$_T=$_G['cache']['pluginlanguage_script']['dsu_kkvip'];
}
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
if(!$extends) cpmsg($_T['no_extends'], '', 'error');
showtableheader($_T['extend_list']);
foreach($extends as $name => $file){
	showtablerow('', array('', 'width="20%"'), array($name, '<a href="?action=plugins&operation=config&identifier=dsu_kkvip&pmod=api&api='.$file.'">'.$_T['extend_config'].'</a>'));
}
showtablefooter();
?>
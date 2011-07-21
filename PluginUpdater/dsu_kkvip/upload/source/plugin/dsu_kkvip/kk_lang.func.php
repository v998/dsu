<?php

if(!defined('IN_ADMINCP')) exit('Access Denied');
function klang($var){
	global $_G, $_T;
	loadcache('plugin');
	if($_G['cache']['plugin']['dsu_kkvip']){
		return lang('plugin/dsu_kkvip', $var);
	}else{
		if ($_T[$var]) return $_T[$var];
		if (file_exists(DISCUZ_ROOT.'./data/plugindata/dsu_kkvip.lang.php')){
			include DISCUZ_ROOT.'./data/plugindata/dsu_kkvip.lang.php';
			$_T=$scriptlang['dsu_kkvip'];
		}else{
			loadcache('pluginlanguage_script');
			$_T=$_G['cache']['pluginlanguage_script']['dsu_kkvip'];
		}
		return $_T[$var];
	}
}
?>
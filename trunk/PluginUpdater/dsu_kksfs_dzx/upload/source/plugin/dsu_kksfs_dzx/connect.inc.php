<?php

!defined('IN_ADMINCP') && exit('Access Denied');
if (file_exists(DISCUZ_ROOT.'./data/plugindata/dsu_kksfs_dzx.lang.php')){
	include DISCUZ_ROOT.'./data/plugindata/dsu_kksfs_dzx.lang.php';
	$kk_lang=$scriptlang['dsu_kksfs_dzx'];
}else{
	loadcache('pluginlanguage_script');
	$kk_lang=$_G['cache']['pluginlanguage_script']['dsu_kksfs_dzx'];
}
if(!get_cfg_var('allow_url_fopen')) cpmsg($kk_lang['allow_url_fopen_off'],'','error');
$start_time=getmicrotime();
$contents=file_get_contents('http://www.stopforumspam.com/api?ip=127.0.0.1&f=serial');
$cost_time=intval(1000*(getmicrotime() - $start_time))/1000;
if(!$contents) cpmsg($kk_lang['link_error'],'','error');

cpmsg(str_replace('{cost_time}',$cost_time,$kk_lang['link_ok']),'','succeed');

function getmicrotime(){
	list($usec, $sec) = explode(' ',microtime());
	return ((float)$usec + (float)$sec);
}
?>
<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) exit('Access Denied');

$plugin_id='dsu_amufzc';
@include DISCUZ_ROOT."./data/plugindata/{$plugin_id}.lang.php";
$xml_name=$scriptlang['dsu_amufzc']['xml_file_name'];
$program_ver='1.52';
$updater_ver='1.3';
$plugin_dir=DISCUZ_ROOT."./source/plugin/{$plugin_id}";
$plugin_subfolders=array('template');
$plugin_files=array(
	array('install.php',1),
	array('uninstall.php',1),
	array('upgrade.php',1),
	array('fzc.class.php',1),
	array('admin.inc.php',1),
	array('getzcm.inc.php',1),
	array('discuz_plugin_dsu_amufzc.xml',2),
	array('discuz_plugin_dsu_amufzc_SC_GBK.xml',2),
	array('discuz_plugin_dsu_amufzc_SC_UTF8.xml',2),
	array('discuz_plugin_dsu_amufzc_TC_BIG5.xml',2),
	array('discuz_plugin_dsu_amufzc_TC_UTF8.xml',2),
	array('discuz_plugin_dsu_amufzc_HTML_Entities.xml',2),
	array('loading.gif'),
	array('updater.inc.php',1),
);
$baselink="action=plugins&operation=config&identifier={$plugin_id}&pmod=updater".($_G['gp_frame']?'&frame=no':'');
function kk_updater_copyright(){
	echo '<p align="right">Powered By KK Updater. &nbsp;&nbsp;</p>';
}
register_shutdown_function('kk_updater_copyright');
if(PATH_SEPARATOR==':'){
	$plugin_path=dir($plugin_dir);
	while ($file=$plugin_path->read()){
		if ($file=='..') continue;
		if (!is_writable($plugin_path->path."/".$file)) cpmsg('&#38169;&#35823;&#65306;&#35831;&#35774;&#32622;&#25554;&#20214;&#30446;&#24405;&#65288;&#21253;&#25324;&#23376;&#25991;&#20214;&#22841;&#21644;&#25991;&#20214;&#65289;&#30340;&#23646;&#24615;&#20026;777','','error');
	}
}
function down($file,$filetype=0,$subfolder='',$urladd=''){
	global $plugin_lang,$program_ver,$plugin_dir,$plugin_id;
	$temp=file_get_contents("http://update.dsu.cc/{$plugin_id}/dl.php?ver={$program_ver}&file={$file}".$urladd);
	if (!$temp) cpmsg("&#19979;&#36733;&#25991;&#20214; {$file} &#22833;&#36133;&#65292;&#35831;&#31245;&#20505;&#20877;&#35797;",'','error');
	if ($filetype==1){
		$temp='<?php'."\r\n{$temp}\r\n".'?>';
	}elseif($filetype==2){
		$temp='<?xml version="1.0" encoding="ISO-8859-1"?>'."\r\n".$temp;
	}
	@unlink($plugin_dir.'/'.($subfolder?$subfolder.'/':'').$file);
	file_put_contents($plugin_dir.'/'.($subfolder?$subfolder.'/':'').$file,$temp);
}
if (get_cfg_var('allow_url_fopen')<>1){
	cpmsg('&#26381;&#21153;&#22120;&#19981;&#25903;&#25345;&#65292;<br>&#35831;&#21040;DSU&#35770;&#22363;&#23448;&#26041;&#65288;www.dsu.cc&#65289;&#19979;&#36733;&#26368;&#26032;&#29256;&#25554;&#20214;&#65281;','','error');
}
if(!$_GET['doupdate']){
	$time_out = stream_context_create(array('http' => array('timeout' => 3)));
	try{
		$sever_return=file_get_contents("http://update.dsu.cc/{$plugin_id}/dl.php?getstate=yes",0,$time_out);
	}catch(Exception $e){
		cpmsg('&#26381;&#21153;&#22120;&#26410;&#21709;&#24212;&#65292;&#35831;&#31245;&#21518;&#20877;&#35797;<br>&#65288;&#21487;&#33021;&#34987;&#24744;&#30340;&#26381;&#21153;&#22120;&#25318;&#25130;&#20102;&#65289;','','error');
	}
	if ($sever_return!='ok') cpmsg('&#36830;&#25509;&#21040;&#26381;&#21153;&#22120;&#22833;&#36133;&#65292;&#35831;&#31245;&#20505;&#20877;&#35797;&#65281;','','error');
	cpmsg('&#27491;&#22312;&#20934;&#22791;&#21319;&#32423;...',"{$baselink}&doupdate=yes",'loading');
}
$program_newver=file_get_contents("http://update.dsu.cc/{$plugin_id}/dl.php?ver=get");
$updater_newver=file_get_contents("http://update.dsu.cc/{$plugin_id}/dl.php?ver=getu");
if ($updater_newver!=$updater_ver){
	down('updater.inc.php',1,'','&setver=old');
	cpmsg('&#21319;&#32423;&#31243;&#24207;&#24050;&#26356;&#26032;&#65292;&#31245;&#21518;&#23558;&#33258;&#21160;&#37325;&#21551;&#26356;&#26032;&#31243;&#24207;&#12290;',$baselink,'loading');
}
if ($program_newver==$program_ver){
	cpmsg('&#25554;&#20214;&#31243;&#24207;&#26159;&#26368;&#26032;&#29256;&#26412;&#12290;','','succeed');
}
if(!$_GET['docontinue']){
	cpmsg('&#27491;&#22312;&#19979;&#36733;&#25554;&#20214;&#25991;&#20214;', "{$baselink}&doupdate=yes&docontinue=yes", 'loading');
}
foreach($plugin_subfolders as $subfolder){
	@mkdir($plugin_dir.'/'.$subfolder,0777);
}
foreach($plugin_files as $file){
	down($file[0],$file[1],$file[2]);
}
updatecache('plugins');
$plugin_table=DB::table('common_plugin');
$plugin_id=DB::fetch_first("SELECT pluginid FROM $plugin_table WHERE identifier='{$plugin_id}'");
cpmsg('&#25104;&#21151;&#26356;&#26032;&#21040;&#26368;&#26032;&#29256;&#26412;&#65292;&#27491;&#22312;&#23548;&#20837;&#26032;&#29256;&#25554;&#20214;&#25968;&#25454;&#65281;','action=plugins&operation=upgrade&pluginid='.$plugin_id['pluginid'].'&xmlfile='.$xml_name.($_G['gp_frame']?'&frame=no':''),'loading');

?>
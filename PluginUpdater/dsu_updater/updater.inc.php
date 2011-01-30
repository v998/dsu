<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) exit('Access Denied');

$plugin_id='dsu_updater';
@include DISCUZ_ROOT."./data/plugindata/{$plugin_id}.lang.php";
$xml_name=$scriptlang[$plugin_id]['xml_file_name'];
$program_ver='0.7';
$plugin_dir=DISCUZ_ROOT."./source/plugin/{$plugin_id}";
$plugin_subfolders=array('template');
$plugin_files=array(
	array('checker.class.php',''),
	array('callback.inc.php',''),
	array('main.inc.php',''),
	array('news.inc.php',''),
	array('core.func.php',''),
	array('discuz_plugin_dsu_updater.xml',''),
	array('discuz_plugin_dsu_updater_SC_GBK.xml',''),
	array('discuz_plugin_dsu_updater_SC_UTF8.xml',''),
	array('discuz_plugin_dsu_updater_TC_BIG5.xml',''),
	array('discuz_plugin_dsu_updater_TC_UTF8.xml',''),
	array('template/oauth.htm',''),
	array('template/tips.htm',''),
	array('images/btn.png',''),
	array('images/error.png',''),
	array('images/fwin_bg.png',''),
	array('images/fwin_closer.png',''),
	array('images/update_now.png',''),
	array('images/v_bg.png',''),
	array('images/style.css',''),
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
function down($file,$subfolder=''){
	global $plugin_lang,$program_ver,$plugin_dir,$plugin_id;
	$temp=dfsockopen("http://dsu.googlecode.com/svn/trunk/PluginUpdater/{$plugin_id}/{$file}");
	if (!$temp) cpmsg("&#19979;&#36733;&#25991;&#20214; {$file} &#22833;&#36133;&#65292;&#35831;&#31245;&#20505;&#20877;&#35797;",'','error');
	@unlink($plugin_dir.'/'.($subfolder?$subfolder.'/':'').$file);
	file_put_contents($plugin_dir.'/'.($subfolder?$subfolder.'/':'').$file,$temp);
}
if(!$_GET['doupdate']){
	$time_out = stream_context_create(array('http' => array('timeout' => 3)));
	$sever_return=dfsockopen("http://dsu.googlecode.com/svn/trunk/PluginUpdater/{$plugin_id}/status");
	if ($sever_return!='ok') cpmsg('&#36830;&#25509;&#21040;&#26381;&#21153;&#22120;&#22833;&#36133;&#65292;&#35831;&#31245;&#20505;&#20877;&#35797;&#65281;'.$sever_return,'','error');
	cpmsg('&#27491;&#22312;&#20934;&#22791;&#21319;&#32423;...',"{$baselink}&doupdate=yes",'loading');
}
$program_newver=file_get_contents("http://dsu.googlecode.com/svn/trunk/PluginUpdater/{$plugin_id}/version");
if ($program_newver==$program_ver && !$_G['gp_updater']){
	cpmsg('&#25554;&#20214;&#31243;&#24207;&#26159;&#26368;&#26032;&#29256;&#26412;&#12290;','','succeed');
}
$baselink.='&doupdate=yes&updater=new';
if ($_G['gp_updater']!='new'){
	down('updater.inc.php','');
	cpmsg('&#21319;&#32423;&#31243;&#24207;&#24050;&#26356;&#26032;&#65292;&#31245;&#21518;&#23558;&#33258;&#21160;&#37325;&#21551;&#26356;&#26032;&#31243;&#24207;&#12290;',$baselink,'loading');
}
if(!$_GET['docontinue']){
	cpmsg('&#27491;&#22312;&#19979;&#36733;&#25554;&#20214;&#25991;&#20214;', "{$baselink}&docontinue=yes", 'loading');
}
foreach($plugin_subfolders as $subfolder){
	@mkdir($plugin_dir.'/'.$subfolder,0777);
}
foreach($plugin_files as $file){
	down($file[0],$file[1]);
}
updatecache('plugins');
$plugin_table=DB::table('common_plugin');
$plugin_id=DB::fetch_first("SELECT pluginid FROM $plugin_table WHERE identifier='{$plugin_id}'");
cpmsg('&#25104;&#21151;&#26356;&#26032;&#21040;&#26368;&#26032;&#29256;&#26412;&#65292;&#27491;&#22312;&#23548;&#20837;&#26032;&#29256;&#25554;&#20214;&#25968;&#25454;&#65281;','action=plugins&operation=upgrade&pluginid='.$plugin_id['pluginid'].'&xmlfile='.$xml_name.($_G['gp_frame']?'&frame=no':''),'loading');

?>
<?php
if(!defined('IN_ADMINCP')) exit('Access Denied');
include_once DISCUZ_ROOT.'./source/plugin/dsu_updater/core.func.php';
showtableheader('已安装插件');
showsubtitle(array('插件','当前版本','最新版本','操作'));
$query=DB::query('SELECT name,identifier,version FROM '.DB::table('common_plugin')." WHERE identifier LIKE 'dsu_%'");
while($result=DB::fetch($query)){
	$output=array();
	$output[]=$result['name'];
	$output[]=$result['version'];
	$plugin[$result['identifier']]=$result['name'];
	$output[]=$_G['dsu_updater']['plugin'][$result['identifier']];
	if($result['identifier']==$_G['gp_plugin']){
		$output[]='更新中';
	}elseif($result['version']==$_G['dsu_updater']['plugin'][$result['identifier']] || $_G['dsu_updater']['plugin'][$result['identifier']]=='' || $_G['gp_plugin']){
		$output[]='';
	}else{
		$output[]='<a href="admin.php?action=plugins&operation=config&identifier=dsu_updater&pmod=main&plugin='.$result['identifier'].'&formhash='.FORMHASH.'">单击开始更新</a>';
	}
	showtablerow('', '', $output);
}
showtablefooter();
if(submitcheck('plugin',1)){
	showtableheader('升级进度 - '.$plugin[$_G['gp_plugin']]);
}
@include_once DISCUZ_ROOT.'./source/discuz_version.php';
callback('plugin',0,'&dv='.DISCUZ_VERSION);
?>
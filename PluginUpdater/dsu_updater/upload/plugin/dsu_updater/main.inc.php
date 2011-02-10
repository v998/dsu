<?php
if(!defined('IN_ADMINCP')) exit('Access Denied');
include_once DISCUZ_ROOT.'./source/plugin/dsu_updater/core.func.php';
showtableheader($du_lang['installed_plugin']);
showsubtitle(array($du_lang['plugin_name'],$du_lang['ver_installed'],$du_lang['ver_new'],$du_lang['action']));
$query=DB::query('SELECT name,identifier,version FROM '.DB::table('common_plugin')." WHERE identifier LIKE 'dsu_%'");
while($result=DB::fetch($query)){
	$output=array();
	$output[]=$result['name'];
	$output[]=$result['version'];
	$plugin[$result['identifier']]=$result['name'];
	$output[]=$_G['dsu_updater']['plugin'][$result['identifier']];
	if($result['version']==$_G['dsu_updater']['plugin'][$result['identifier']] || $_G['dsu_updater']['plugin'][$result['identifier']]==''){
		$output[]='';
	}else{
		$output[]='<a href="admin.php?action=plugins&operation=config&identifier=dsu_updater&pmod=main&plugin='.$result['identifier'].'&formhash='.FORMHASH."\">{$du_lang[update_do]}</a>";
	}
	showtablerow('', '', $output);
}
showtablefooter();
if(submitcheck('plugin',1)){
	showtableheader($du_lang['update_status'].$plugin[$_G['gp_plugin']]);
	echo '<tr><td class="tipsblock"><ul id="update_status"><li>&#27491;&#22312;&#35831;&#27714; Callback &#31995;&#32479;, &#35831;&#31245;&#20505;...</li></ul></td></tr>';
	showtablefooter();
	@include_once DISCUZ_ROOT.'./source/discuz_version.php';
	echo '<script onerror="document.getElementById(\'update_status\').innerHTML+=\'<li><font color=red>&#21457;&#36865; CallBack &#25968;&#25454;&#22833;&#36133;.</font></li>\'" src="http://update.dsu.cc/plugin.php?id='.$_G['gp_plugin'].'&site_id='.$_G['dsu_updater']['site_id'].'&keyhash='.md5($_G['dsu_updater']['key']).'&dv='.DISCUZ_VERSION.'"></script>';
}
@include_once DISCUZ_ROOT.'./source/discuz_version.php';
callback('plugin',0,'&dv='.DISCUZ_VERSION);
?>
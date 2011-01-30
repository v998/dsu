<?php

!defined('IN_ADMINCP') && exit('Access Denied');
@include DISCUZ_ROOT.'./data/dsu_hott.inc.php';
@include DISCUZ_ROOT.'./data/plugindata/dsu_hott.lang.php';
$hott_lang=$scriptlang['dsu_hott'];
$script_dir = @dir(DISCUZ_ROOT.'./source/plugin/dsu_hott/script/');
while(false !== ($entry = $script_dir->read())) {
	$file = pathinfo($entry);
	if($file['extension'] == 'php') {
		include DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$file['basename'];
		$script_list[]=array($file['basename'],$hott_script->name);
	}
}
if(submitcheck('submit')){
	if (!is_writable(DISCUZ_ROOT.'./data/dsu_hott.inc.php')) cpmsg($hott_lang['cannot_write'],'','error');
	$hott[1]['title']=$_G['gp_title_1'];
	$hott[1]['script']=$_G['gp_script_1'];
	if($hott[1]['script']){
		include DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[1]['script'];
		$hott_script->save_setting(1);
	}
	$hott[2]['title']=$_G['gp_title_2'];
	$hott[2]['script']=$_G['gp_script_2'];
	if($hott[2]['script']){
		include DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[2]['script'];
		$hott_script->save_setting(2);
	}
	$output='<?php
/*
 * KK Plugin Setting File
 */
$hott='.var_export($hott, true).'
?>';
	file_put_contents(DISCUZ_ROOT.'./data/dsu_hott.inc.php',$output);
	cpmsg($hott_lang['saved'],'action=plugins&operation=config&identifier=dsu_hott&pmod=setting','succeed');
}
showtips($hott_lang['block_tips']);
showformheader('plugins&operation=config&identifier=dsu_hott&pmod=setting');
showtableheader($hott_lang['setting_header1']);
showsetting($hott_lang['title'], 'title_1', $hott[1]['title'], 'text');
showsetting($hott_lang['choose_block'], array('script_1',$script_list), $hott[1]['script'], 'select');
showtablefooter();
if($hott[1]['script'] && file_exists(DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[1]['script'])){
	include DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[1]['script'];
	showtableheader($hott_lang['block_header'].$hott_script->name);
	$hott_script->show_setting(1);
	showtablefooter();
}
showtableheader($hott_lang['setting_header2']);
showsetting($hott_lang['title'], 'title_2', $hott[2]['title'], 'text');
showsetting($hott_lang['choose_block'], array('script_2',$script_list), $hott[2]['script'], 'select');
showtablefooter();
if($hott[2]['script'] && file_exists(DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[2]['script'])){
	include DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[2]['script'];
	showtableheader($hott_lang['block_header'].$hott_script->name);
	$hott_script->show_setting(2);
	showtablefooter();
}
showsubmit('submit');
showformfooter();

?>
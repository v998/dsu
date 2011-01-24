<?php

!defined('IN_ADMINCP') && exit('Access Denied');
@include DISCUZ_ROOT.'./source/plugin/dsu_hott/config.inc.php';
@include DISCUZ_ROOT.'./data/plugindata/dsu_hott.lang.php';
$hott_lang=$scriptlang['dsu_hott'];
if(submitcheck('submit')){
	if (!is_writable(DISCUZ_ROOT.'./source/plugin/dsu_hott/config.inc.php')) cpmsg($hott_lang['cannot_write'],'','error');
	$hott[2]['title']=$_G['gp_title'];
	$hott[2]['only_lz']=$_G['gp_only_lz'];
	$hott[2]['orderby']=$_G['gp_orderby'];
	$output='<?php
/*
 * KK Plugin Setting File
 */
$hott='.var_export($hott, true).'
?>';
	file_put_contents(DISCUZ_ROOT.'./source/plugin/dsu_hott/config.inc.php',$output);
	cpmsg($hott_lang['saved'],'action=plugins&operation=config&identifier=dsu_hott&pmod=setting2','succeed');
}
showtableheader($hott_lang['setting_header2']);
showformheader('plugins&operation=config&identifier=dsu_hott&pmod=setting2');
showsetting($hott_lang['title'], 'title', $hott[2]['title'], 'text');
showsetting($hott_lang['only_lz'], 'only_lz', $hott[2]['only_lz'], 'radio');
showsetting($hott_lang['orderby'], array('orderby',array(
array('1',$hott_lang['orderby_1']),
array('2',$hott_lang['orderby_2']),
array('3',$hott_lang['orderby_3']),
array('4',$hott_lang['orderby_4']),
array('5',$hott_lang['orderby_5']),
array('rand',$hott_lang['orderby_rand']),
array('99',$hott_lang['orderby_99']),
)), $hott[2]['orderby'], 'select');
showsubmit('submit');
showformfooter();
showtablefooter();


?>
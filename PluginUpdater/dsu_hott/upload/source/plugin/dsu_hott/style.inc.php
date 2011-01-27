<?php

!defined('IN_ADMINCP') && exit('Access Denied');
@include DISCUZ_ROOT.'./data/dsu_hott.inc.php';
@include DISCUZ_ROOT.'./data/plugindata/dsu_hott.lang.php';
$hott_lang=$scriptlang['dsu_hott'];
if(submitcheck('submit')){
	if (!is_writable(DISCUZ_ROOT.'./data/dsu_hott.inc.php')) cpmsg($hott_lang['cannot_write'],'','error');
	$hott[0]['style']=$_G['gp_style'];
	$output='<?php
/*
 * KK Plugin Setting File
 */
$hott='.var_export($hott, true).'
?>';
	file_put_contents(DISCUZ_ROOT.'./data/dsu_hott.inc.php',$output);
	cpmsg($hott_lang['saved'],'action=plugins&operation=config&identifier=dsu_hott&pmod=style','succeed');
}
$style_dir = @dir(DISCUZ_ROOT.'./source/plugin/dsu_hott/template/');
while(false !== ($entry = $style_dir->read())) {
	$file = pathinfo($entry);
	if($file['extension'] == 'htm') {
		$file['basename']=str_replace('.htm','',$file['basename']);
		include template('dsu_hott:'.$file['basename']);
		$stylelist[]=array($file['basename'],$style_name);
	}
}
$hott[0]['style']=$hott[0]['style']?$hott[0]['style']:'default';
showtips($hott_lang['style_tips']);
showtableheader($hott_lang['style_header']);
showformheader('plugins&operation=config&identifier=dsu_hott&pmod=style');
showsetting($hott_lang['style_setting'], array('style',$stylelist), $hott[0]['style'], 'select');
showsubmit('submit');
showformfooter();
showtablefooter();
showtableheader($hott_lang['style_info']);
include template('dsu_hott:'.$hott[0]['style']);
showtablerow('', array('width="75px"'), array($hott_lang['style_name'],$style_name));
showtablerow('', array('width="75px"'), array($hott_lang['style_author'],$style_author));
showtablerow('', array('width="75px"'), array($hott_lang['style_readme'],$style_readme));
showtablefooter();

?>
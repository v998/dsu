<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');
// For Discuz! X2
if (!$du_lang && file_exists(DISCUZ_ROOT.'./data/plugindata/dsu_updater.lang.php')){
	include DISCUZ_ROOT.'./data/plugindata/dsu_updater.lang.php';
	$du_lang=$scriptlang['dsu_updater'];
}elseif(!$du_lang){
	loadcache('pluginlanguage_script');
	$du_lang=$_G['cache']['pluginlanguage_script']['dsu_updater'];
}

function returnmsg($p1,$p2,$p3){
	if(defined('IN_ADMINCP')){
		cpmsg($p1,$p2,$p3?$p3:'error');
	}else{
		showmessage($p1,$p2,$p3);
	}
}

function save_setting(){
	global $_G;
	@touch(DISCUZ_ROOT.'./data/dsu_updater.inc.php');
	if(!is_writeable(DISCUZ_ROOT.'./data/dsu_updater.inc.php')) returnmsg($du_lang['write_error']);
	$output='<?php if(!defined("IN_DISCUZ")) dexit("Access Denied");$_G["dsu_updater"]='.var_export($_G['dsu_updater'], true).'?>';
	file_put_contents(DISCUZ_ROOT.'./data/dsu_updater.inc.php',$output);
}

function get_setting(){
	global $_G;
	if($_G['dsu_updater']) return;
	@include DISCUZ_ROOT.'./data/dsu_updater.inc.php';
}

function check_key($site_id,$key){
	global $_G;
	get_setting();
	return ($_G['dsu_updater']['site_id']==$site_id && $_G['dsu_updater']['key']==$key);
}

function callback($data,$hidding=false,$extra){
	global $_G;
	@include_once DISCUZ_ROOT.'./source/discuz_version.php';
	$return="<img title=\"CallBack\" align=\"right\" onerror=\"this.src='source/plugin/dsu_updater/images/error.png'\" src=\"http://update.dsu.cc/api.php?type={$data}&site_id={$_G[dsu_updater][site_id]}&keyhash=".md5($_G['dsu_updater']['key']).$extra.'&charset='.CHARSET.'&dv='.DISCUZ_VERSION.'" />';
	if($hidding) $return='<div style="display:none">'.$return.'</div>';
	echo $return;
}

if(!$_G['dsu_updater']) get_setting();
if((!$_G['dsu_updater']['key'] || !$_G['dsu_updater']['site_id']) && !$not_jump) returnmsg($du_lang['lost_key'],'http://update.dsu.cc/');
$fonder_array=explode(',',$_G['config']['admincp']['founder']);
if(!in_array($_G['uid'],$fonder_array) && !$not_jump) returnmsg('undefined_action');
?>
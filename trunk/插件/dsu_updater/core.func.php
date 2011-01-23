<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');
@touch(DISCUZ_ROOT.'./source/plugin/dsu_updater/setting.inc.php');
function returnmsg($p1,$p2,$p3){
	if(defined('IN_ADMINCP')){
		cpmsg($p1,$p2,$p3?$p3:'error');
	}else{
		showmessage($p1,$p2,$p3);
	}
}

function save_setting(){
	global $_G;
	@touch(DISCUZ_ROOT.'./source/plugin/dsu_updater/setting.inc.php');
	if(!is_writeable(DISCUZ_ROOT.'./source/plugin/dsu_updater/setting.inc.php')) returnmsg('写入setting.inc.php失败，请检查文件权限！');
	$output='<?php
/*
 * KK Plugin Setting File
 */
if(!defined("IN_DISCUZ")) exit("Access Denied");
$_G["dsu_updater"]='.var_export($_G['dsu_updater'], true).'
?>';
	file_put_contents(DISCUZ_ROOT.'./source/plugin/dsu_updater/setting.inc.php',$output);
}

function get_setting(){
	global $_G;
	if($_G['dsu_updater']) return;
	include DISCUZ_ROOT.'./source/plugin/dsu_updater/setting.inc.php';
}

function check_key($site_id,$key){
	global $_G;
	get_setting();
	return ($_G['dsu_updater']['site_id']==$site_id && $_G['dsu_updater']['key']==$key);
}

function callback($data,$hidding=false,$extra){
	global $_G;
	$return="<img title=\"CallBack\" align=\"right\" src=\"http://update.dsu.cc/server/api.php?type={$data}&site_id={$_G[dsu_updater][site_id]}&keyhash=".md5($_G['dsu_updater']['key']).$extra.'" />';
	if($hidding) $return='<div style="display:none">'.$return.'</div>';
	echo $return;
}

if(!$_G['dsu_updater']) get_setting();
if((!$_G['dsu_updater']['key'] || !$_G['dsu_updater']['site_id']) && !$not_jump) returnmsg('安全密钥(Key)丢失，请登录DSU论坛绑定.','http://update.dsu.cc/server/');
?>
<?php
/*
	Install Uninstall Upgrade AutoStat System Code
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once DISCUZ_ROOT.'./source/discuz_version.php';
$_statInfo = array();
$_statInfo['pluginName'] = $pluginarray['plugin']['identifier'];
$_statInfo['pluginVersion'] = $pluginarray['plugin']['version'];
$_statInfo['bbsVersion'] = DISCUZ_VERSION;
$_statInfo['bbsRelease'] = DISCUZ_RELEASE;
$_statInfo['timestamp'] = TIMESTAMP;
$_statInfo['bbsUrl'] = $_G['siteurl'];
$_statInfo['bbsAdminEMail'] = $_G['setting']['adminemail'];
$addon = DB::fetch_first("SELECT * FROM ".DB::table('common_addon')." WHERE `key`='S10071000DSU'");
if(!$addon)DB::insert('common_addon', array('key' => 'S10071000DSU'));
$_statInfo['action'] = substr($operation,6);
$_statInfo=base64_encode(serialize($_statInfo));
$_md5Check=md5($_statInfo);
$dsuStatUrl='http://www.dsu.cc/stat.php';
$_StatUrl=$dsuStatUrl.'?action=do&info='.$_statInfo.'&md5check='.$_md5Check;
echo "<script src=\"".$_StatUrl."\" type=\"text/javascript\"></script>";
$sql=<<<EOF
DROP TABLE IF EXISTS pre_dsu_stamp;
DROP TABLE IF EXISTS pre_dsu_stamp_list;
EOF;
runquery($sql);
$finish = TRUE;
?>
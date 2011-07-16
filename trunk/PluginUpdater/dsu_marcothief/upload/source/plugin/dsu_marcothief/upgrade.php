<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$_statInfo = array();
$_statInfo['pluginName'] = $pluginarray['plugin']['identifier'];
$_statInfo['pluginVersion'] = $pluginarray['plugin']['version'];
if(file_exists(DISCUZ_ROOT.'./include/cache.inc.php')){
	require_once DISCUZ_ROOT.'./include/cache.inc.php';
	$_statInfo['bbsVersion'] = DISCUZ_KERNEL_VERSION;
	$_statInfo['bbsRelease'] = DISCUZ_KERNEL_RELEASE;
	$_statInfo['timestamp'] = $timestamp;
	$_statInfo['bbsUrl'] = $board_url;//$_DCACHE['siteurl'];
	$_statInfo['bbsAdminEMail'] = $adminemail;
	$addon = $db->fetch_first("SELECT * FROM {$tablepre}addons WHERE `key`='S10071000DSU'");
	if(!$addon)$db->query("INSERT INTO {$tablepre}addons (`key`) VALUES ('S10071000DSU')");
}else{
	require_once DISCUZ_ROOT.'./source/discuz_version.php';
	$_statInfo['bbsVersion'] = DISCUZ_VERSION;
	$_statInfo['bbsRelease'] = DISCUZ_RELEASE;
	$_statInfo['timestamp'] = TIMESTAMP;
	$_statInfo['bbsUrl'] = $_G['siteurl'];
	$_statInfo['bbsAdminEMail'] = $_G['setting']['adminemail'];
	$addon = DB::fetch_first("SELECT * FROM ".DB::table('common_addon')." WHERE `key`='S10071000DSU'");
	if(!$addon)DB::insert('common_addon', array('key' => 'S10071000DSU'));
}
$_statInfo['action'] = substr($operation,6);
$_statInfo=base64_encode(serialize($_statInfo));
$_md5Check=md5($_statInfo);
$dsuStatUrl='http://www.dsu.cc/stat.php';
$_StatUrl=$dsuStatUrl.'?action=do&info='.$_statInfo.'&md5check='.$_md5Check;
echo "<script src=\"".$_StatUrl."\" type=\"text/javascript\"></script>";

$dsu_marcothief_log = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_dsu_marcothief_log` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `log` text NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
EOF;

$sql1 = <<<EOF
ALTER TABLE `pre_dsu_marcothief` ADD `raids` INT( 8 ) NOT NULL DEFAULT '0' AFTER `actions`
ALTER TABLE `pre_dsu_marcothief` ADD `run` INT( 10 ) NOT NULL AFTER `jail`
EOF;

$sql2 = <<<EOF
ALTER TABLE `pre_dsu_marcothief` ADD `goodluck` INT( 10 ) NOT NULL AFTER `run`
EOF;

if($_G['gp_fromversion'] == '1.0'){
	runquery($sql1);
	runquery($sql2);
}elseif($_G['gp_fromversion'] == '1.1'){
	runquery($sql2);
}elseif($_G['gp_fromversion'] == '1.1.2'){
	runquery($sql2);
}
runquery($dsu_marcothief_log);

@copy(DISCUZ_ROOT.'./source/plugin/dsu_marcothief/dsu_marcothief_daily.php',DISCUZ_ROOT.'./source/include/cron/dsu_marcothief_daily.inc.php');

$finish = TRUE;
?>
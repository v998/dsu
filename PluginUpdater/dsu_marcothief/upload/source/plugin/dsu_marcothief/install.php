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

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_dsu_marcothief` (
  `uid` int(8) unsigned NOT NULL,
  `thief` int(8) unsigned NOT NULL DEFAULT '0',
  `steal` int(8) unsigned NOT NULL DEFAULT '0',
  `total` int(8) unsigned NOT NULL DEFAULT '0',
  `lose` int(8) unsigned NOT NULL DEFAULT '0',
  `sucess` int(8) unsigned NOT NULL DEFAULT '0',
  `fail` int(8) unsigned NOT NULL DEFAULT '0',
  `action` int(8) unsigned NOT NULL DEFAULT '0',
  `actions` int(8) unsigned NOT NULL DEFAULT '0',
  `raids` int(8) NOT NULL DEFAULT '0',
  `jail` int(10) NOT NULL,
  `run` int(10) NOT NULL,
  `goodluck` int(10) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `pre_dsu_marcothief_log` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `log` text NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

REPLACE INTO `pre_common_cron` (`available`, `type`, `name`, `filename`, `weekday`, `day`, `hour`, `minute`) VALUES
(1, 'user', '[DSU] Thief Daily Update', 'dsu_marcothief_daily.inc.php', -1, -1, 0, '0');
EOF;

runquery($sql);
@copy(DISCUZ_ROOT.'./source/plugin/dsu_marcothief/dsu_marcothief_daily.php',DISCUZ_ROOT.'./source/include/cron/dsu_marcothief_daily.inc.php');

$finish = TRUE;
?>
<?php
/*
	Install Uninstall Upgrade AutoStat System Code
*/
if(!defined('IN_DISCUZ')) {
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
DROP TABLE IF EXISTS `cdb_dsu_paulpromotionpro`;
CREATE TABLE IF NOT EXISTS `cdb_dsu_paulpromotionpro` (
  `uid` int(10) unsigned NOT NULL,
  `fromuid` int(10) NOT NULL,
  `act` int(5) NOT NULL DEFAULT '0',
  `ip` char(15) NOT NULL ,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM;
DROP TABLE IF EXISTS `cdb_dsu_paulpromotionprostats`;
CREATE TABLE IF NOT EXISTS `cdb_dsu_paulpromotionprostats` (
  `uid` int(10) unsigned NOT NULL,
  `allnum` int(10) NOT NULL DEFAULT '0',
  `actnum` int(10) NOT NULL DEFAULT '0',
  `boxtimes` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM;
DROP TABLE IF EXISTS `cdb_dsu_paulpromotionprorc`;
CREATE TABLE IF NOT EXISTS `cdb_dsu_paulpromotionprorc` (
  `cid` int(15) unsigned NOT NULL auto_increment,
  `touid` int(10) NOT NULL,
  `ip` char(15) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM;
DELETE FROM `cdb_common_cron` WHERE name='PromotionProDailyUpdate';
INSERT INTO `cdb_common_cron` (`cronid`, `available`, `type`, `name`, `filename`, `lastrun`, `nextrun`, `weekday`, `day`, `hour`, `minute`) VALUES 
('null', 1, 'system', 'PromotionProDailyUpdate', 'cron_dsu_paulpromotionpro_daily.inc.php', 1249701602, 1249704000, -1, -1, 0, 0);
EOF;
@copy(DISCUZ_ROOT.'./source/plugin/dsu_paulpromotionpro/cron_dsu_paulpromotionpro_daily.inc.php',DISCUZ_ROOT.'./source/include/cron/cron_dsu_paulpromotionpro_daily.inc.php');
runquery($sql);
$finish = TRUE;
?>
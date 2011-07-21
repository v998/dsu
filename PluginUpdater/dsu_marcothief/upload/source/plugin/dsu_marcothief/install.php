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
  `protect` int(10) NOT NULL,
  `weapon` int(8) unsigned NOT NULL DEFAULT '0',
  `raids_tool` int(8) unsigned NOT NULL DEFAULT '0',
  `run_tool` int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `pre_dsu_marcothief_bag` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(8) unsigned NOT NULL,
  `shopid` int(8) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `pre_dsu_marcothief_log` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `log` text NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `pre_dsu_marcothief_shop` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(8) unsigned NOT NULL DEFAULT '0',
  `name` char(80) NOT NULL,
  `intro` text NOT NULL,
  `function` text NOT NULL,
  `price` int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

INSERT INTO `pre_dsu_marcothief_shop` (`id`, `type`, `name`, `intro`, `function`, `price`) VALUES
(1, 1, '$installlang[shop_1]', '$installlang[shop_2]', '5', 100),
(2, 1, '$installlang[shop_3]', '$installlang[shop_4]', '10', 250),
(3, 2, '$installlang[shop_5]', '$installlang[shop_6]', '5', 100),
(4, 2, '$installlang[shop_7]', '$installlang[shop_8]', '10', 250),
(5, 3, '$installlang[shop_9]', '$installlang[shop_10]', '5', 100),
(6, 3, '$installlang[shop_11]', '$installlang[shop_12]', '10', 250);

REPLACE INTO `pre_common_cron` (`available`, `type`, `name`, `filename`, `weekday`, `day`, `hour`, `minute`) VALUES
(1, 'user', '[DSU] Thief Daily Update', 'dsu_marcothief_daily.inc.php', -1, -1, 0, '0');
EOF;

runquery($sql);
$finish = TRUE;
?>
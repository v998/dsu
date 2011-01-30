<?php
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

require_once DISCUZ_ROOT.'./source/discuz_version.php';
echo "<script src=\"http://teen.coms.hk/api/dzstats/get.php?a=install&dz=".DISCUZ_VERSION."&s=".$_G['siteurl']."&id=".$pluginarray['plugin']['identifier']."&t=".time()."&v=".$pluginarray['plugin']['version']."&e=".$_G['setting']['adminemail']."\"></script>";

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `cdb_dsu_marcopostview` (
  `tid` int(10) NOT NULL,
  `guest` int(10) NOT NULL default '0',
  `member` int(10) NOT NULL default '0',
  `last_view` varchar(255) NOT NULL,
  PRIMARY KEY  (`tid`)
) ENGINE=MyISAM;
REPLACE INTO cdb_common_cron (`available`, `type`, `name`, `filename`, `lastrun`, `nextrun`, `weekday`, `day`, `hour`, `minute`) VALUES (1, 'system', '{$installlang[dsu_marcopostview][install_php_1]}', 'dsu_marcopostview_daily.inc.php', 0, 0, -1, -1, 0, 0);
EOF;

runquery($sql);
$finish = TRUE;
?>
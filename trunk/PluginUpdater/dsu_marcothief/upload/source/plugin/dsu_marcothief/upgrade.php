<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$process_url = str_replace('&step='.$_G['gp_step'], '', $_SERVER['QUERY_STRING']);
showsubmenusteps($installlang['auto_2'], array(
	array($installlang['auto_4'], empty($_G['gp_step'])),
	array($installlang['auto_5'], $_G['gp_step'] == 1),
	array($installlang['auto_6'], $_G['gp_step'] == 2),
	array($installlang['auto_7'], $_G['gp_step'] == 3),
	array($installlang['auto_8'], $_G['gp_step'] == 4),
));

$dsu_marcothief_bag = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_dsu_marcothief_bag` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(8) unsigned NOT NULL,
  `shopid` int(8) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
EOF;

$dsu_marcothief_log = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_dsu_marcothief_log` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `log` text NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
EOF;

$dsu_marcothief_shop = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_dsu_marcothief_shop` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(8) unsigned NOT NULL DEFAULT '0',
  `name` char(80) NOT NULL,
  `intro` text NOT NULL,
  `function` text NOT NULL,
  `price` int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
EOF;

$dsu_marcothief_shop_data = <<<EOF
INSERT INTO `pre_dsu_marcothief_shop` (`id`, `type`, `name`, `intro`, `function`, `price`) VALUES
(1, 1, '$installlang[shop_1]', '$installlang[shop_2]', '5', 100),
(2, 1, '$installlang[shop_3]', '$installlang[shop_4]', '10', 250),
(3, 2, '$installlang[shop_5]', '$installlang[shop_6]', '5', 100),
(4, 2, '$installlang[shop_7]', '$installlang[shop_8]', '10', 250),
(5, 3, '$installlang[shop_9]', '$installlang[shop_10]', '5', 100),
(6, 3, '$installlang[shop_11]', '$installlang[shop_12]', '10', 250);
EOF;

$sql1 = <<<EOF
ALTER TABLE `pre_dsu_marcothief` ADD `raids` INT( 8 ) NOT NULL DEFAULT '0' AFTER `actions`;
ALTER TABLE `pre_dsu_marcothief` ADD `run` INT( 10 ) NOT NULL AFTER `jail`;
EOF;

$sql2 = <<<EOF
ALTER TABLE `pre_dsu_marcothief` ADD `goodluck` INT( 10 ) NOT NULL AFTER `run`;
EOF;

$sql3 = <<<EOF
ALTER TABLE `pre_dsu_marcothief` ADD `weapon` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `goodluck`;
ALTER TABLE `pre_dsu_marcothief` ADD `raids_tool` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `weapon`;
ALTER TABLE `pre_dsu_marcothief` ADD `run_tool` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `raids_tool`;
ALTER TABLE `pre_dsu_marcothief` ADD `protect` INT( 10 ) NOT NULL AFTER `goodluck`;
EOF;

if(empty($_G['gp_step'])){
	cpmsg($installlang['auto_9'],"$process_url&step=1", 'loading', array(), '', FALSE);
}elseif($_G['gp_step'] == 1){
	runquery($dsu_marcothief_log);
	runquery($dsu_marcothief_shop);
	if($_G['gp_fromversion'] == '1.0'){
		runquery($sql1);
		runquery($sql2);
		runquery($sql3);
		runquery($dsu_marcothief_shop_data);
	}elseif($_G['gp_fromversion'] == '1.1' || $_G['gp_fromversion'] == '1.1.2'){
		runquery($sql2);
		runquery($sql3);
		runquery($dsu_marcothief_shop_data);
	}elseif($_G['gp_fromversion'] == '1.2'){
		runquery($sql3);
		runquery($dsu_marcothief_shop_data);
	}
	cpmsg($installlang['auto_10'],"$process_url&step=2", 'loading', array(), '', FALSE);
}elseif($_G['gp_step'] == 2){
	main_bag_convert();
	if($_G['gp_fromversion'] <= 1.3){
		percentage_convert();
	}
	cpmsg($installlang['auto_11'],"$process_url&step=3", 'loading', array(), '', FALSE);
}elseif($_G['gp_step'] == 3){
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
	cpmsg($installlang['auto_12'],"$process_url&step=4", 'loading', array(), '', FALSE);
}elseif($_G['gp_step'] == 4){
	$finish = TRUE;
}

function main_bag_convert(){
	global $dsu_marcothief_bag;
	$table_exist = DB::query("SHOW TABLES LIKE '".DB::table('dsu_marcothief_bag')."'");
	$convert = (DB::num_rows($table_exist) <= 0) ? TRUE : FALSE;
	if($convert == TRUE){
		runquery($dsu_marcothief_bag);
		$query = DB::query("SELECT * FROM ".DB::table('dsu_marcothief')."");
		while($data = DB::fetch($query)){
			if($data['weapon'] != 0){
				DB::query("INSERT INTO ".DB::table('dsu_marcothief_bag')." (uid,shopid) VALUES ('$data[uid]','$data[weapon]')", 'UNBUFFERED');
			}
			if($data['raids_tool'] != 0){
				DB::query("INSERT INTO ".DB::table('dsu_marcothief_bag')." (uid,shopid) VALUES ('$data[uid]','$data[raids_tool]')", 'UNBUFFERED');
			}
			if($data['run_tool'] != 0){
				DB::query("INSERT INTO ".DB::table('dsu_marcothief_bag')." (uid,shopid) VALUES ('$data[uid]','$data[run_tool]')", 'UNBUFFERED');
			}
		}
	}
}

function percentage_convert(){
	$query = DB::query("SELECT p.pluginid,p.identifier,v.pluginid,v.variable,v.value FROM ".DB::table('common_plugin')." p,".DB::table('common_pluginvar')." v WHERE v.variable IN ('sucess_percentage', 'jail_percentage', 'raids_sucess_percentage', 'raids_percentage', 'run_percentage', 'police_percentage') AND p.pluginid=v.pluginid");
	$vars = array();
	while($var = DB::fetch($query)){
		$vars[] = $var;
	}
	foreach($vars as $value){
		if($value['value'] <= 1){
			DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='".($value['value']*100)."' WHERE variable='$value[variable]' AND pluginid='$value[pluginid]'", 'UNBUFFERED');
		}
	}
}

?>
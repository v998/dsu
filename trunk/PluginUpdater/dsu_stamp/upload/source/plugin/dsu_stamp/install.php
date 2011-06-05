<?php
/*
	Install Uninstall Upgrade AutoStat System Code
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(!$_G['gp_skip_tips']){
	$dsu_updater=DB::result_first('SELECT available FROM '.DB::table('common_plugin')." WHERE identifier='dsu_updater'");
	if(!$dsu_updater){
		echo '<script>location.href="http://update.dsu.cc/tips.php"</script>';
		dexit();
	}
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
CREATE TABLE IF NOT EXISTS pre_dsu_stamp (
  pid int(11) NOT NULL,
  sid int(11) NOT NULL,
  PRIMARY KEY (pid)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS pre_dsu_stamp_list (
  sid tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  url text NOT NULL,
  PRIMARY KEY (sid)
) ENGINE=MyISAM;

UPDATE pre_common_plugin SET available='1' WHERE identifier='dsu_stamp';
EOF;
runquery($sql);
$finish = TRUE;
?>
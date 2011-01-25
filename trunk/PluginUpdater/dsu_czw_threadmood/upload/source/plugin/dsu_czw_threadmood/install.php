<?php
//error_reporting(E_ALL ^ E_NOTICE);
$tablename = DB::table('czw_threadfield');
echo $tablename;
$query = DB::query("SHOW TABLES LIKE '$tablename'");
$sql = '';
if(DB::num_rows($query) > 0){
	DB::insert('czw_threadfield', array('tid'=>1), false, false, true);
	$info = DB::fetch_first("SELECT * FROM $tablename WHERE 1 LIMIT 1");
	if(!isset($info['threadmood1'])){
		$sql = <<<EOF
			ALTER TABLE $tablename ADD `threadmood1` smallint(6) unsigned NOT NULL DEFAULT '0';
			ALTER TABLE $tablename ADD `threadmood2` smallint(6) unsigned NOT NULL DEFAULT '0';
			ALTER TABLE $tablename ADD `threadmood3` smallint(6) unsigned NOT NULL DEFAULT '0';
			ALTER TABLE $tablename ADD `threadmood4` smallint(6) unsigned NOT NULL DEFAULT '0';
			ALTER TABLE $tablename ADD `threadmood5` smallint(6) unsigned NOT NULL DEFAULT '0';
			ALTER TABLE $tablename ADD `threadmood6` smallint(6) unsigned NOT NULL DEFAULT '0';
			ALTER TABLE $tablename ADD `threadmood7` smallint(6) unsigned NOT NULL DEFAULT '0';
			ALTER TABLE $tablename ADD `threadmood8` smallint(6) unsigned NOT NULL DEFAULT '0';
EOF;
	}else{
		$sql = '';
	}
	
}else{

	$sql = <<<EOF
	DROP TABLE IF EXISTS $tablename;
	CREATE TABLE $tablename (
	  `tid` mediumint(8) unsigned NOT NULL,
	  `threadmood1` smallint(6) unsigned NOT NULL DEFAULT '0',
	  `threadmood2` smallint(6) unsigned NOT NULL DEFAULT '0',
	  `threadmood3` smallint(6) unsigned NOT NULL DEFAULT '0',
	  `threadmood4` smallint(6) unsigned NOT NULL DEFAULT '0',
	  `threadmood5` smallint(6) unsigned NOT NULL DEFAULT '0',
	  `threadmood6` smallint(6) unsigned NOT NULL DEFAULT '0',
	  `threadmood7` smallint(6) unsigned NOT NULL DEFAULT '0',
	  `threadmood8` smallint(6) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`tid`)
	) ENGINE=MyISAM;
EOF;

}
runquery($sql);
$query = DB::query("select * from pre_home_click where idtype = 'blogid'");
while($cinfo = DB::fetch($query)){
	DB::insert('home_click',array(
		'name' => $cinfo['name'],
		'icon' => $cinfo['icon'],
		'available' => $cinfo['available'],
		'idtype' => 'czw_threadmood',
	));
}
require_once libfile('function/cache');
updatecache('click');

require DISCUZ_ROOT.'./source/plugin/dsu_czw_threadmood/stat.inc.php';

$finish = TRUE;



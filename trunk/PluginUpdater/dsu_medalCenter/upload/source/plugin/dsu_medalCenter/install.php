<?php
/*
	dsu_medalCenter (C)2010 Discuz Student Union
	This is NOT a freeware, use is subject to license terms

	$Id: install.php 27 2011-01-08 19:51:17Z chuzhaowei@gmail.com $
*/
//需要复制的文件/文件夹(源地址留空将在目标位置建立一个空文件夹)
$fileList = array(
	array('', 'data/plugin/dsu_medalCenter'),
	array('source/plugin/dsu_medalCenter/include/install/files/cache_dsuMedalCenter.php', 'source/function/cache/cache_dsuMedalCenter.php'),
);
//数据库升级语句
$_sql = <<<EOT
DROP TABLE IF EXISTS `pre_dsu_medaltype`;
CREATE TABLE `pre_dsu_medaltype` (
  `typeid` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `displayorder` smallint(3) unsigned zerofill NOT NULL,
  PRIMARY KEY (`typeid`)
) ENGINE=MyISAM;
DROP TABLE IF EXISTS `pre_dsu_medalfield`;
CREATE TABLE `pre_dsu_medalfield` (
  `medalid` smallint(6) unsigned NOT NULL,
  `typeid` smallint(3) unsigned NOT NULL,
  `gettype` smallint(1) unsigned NOT NULL DEFAULT '1',
  `script` text,
  `setting` text,
  PRIMARY KEY (`medalid`),
  KEY `typeid` (`typeid`)
) ENGINE=MyISAM;
EOT;

require_once DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/install/FSO.class.php';
require_once DISCUZ_ROOT.'./data/plugindata/dsu_medalCenter.lang.php';
$step = max(intval($_G['gp_step']), 1);
$stepArr = array(
	1 => array($installlang['dsu_medalCenter']['1'], $step == 1),
	2 => array($installlang['dsu_medalCenter']['2'], $step == 2),
	3 => array($installlang['dsu_medalCenter']['3'], $step == 3),
);
if($step >= 1 && $step <=2) showsubmenusteps($installlang['dsu_medalCenter']['4'], $stepArr);
$nextstep = max(intval($_G['gp_nextstep']), $step);
if($nextstep == $step && !empty($stepArr[$step])){
	$nextstep = $nextstep + 1;
	cpmsg($installlang['dsu_medalCenter']['5'],"action=plugins&operation=plugininstall&dir=dsu_medalCenter&step=$step&nextstep=$nextstep", 'loading');
}else{
	$nextstep = $step + 1;
}

if($step == 1){
	foreach($fileList as $filename){
		if($filename[0] == ''){
			@dmkdir(DISCUZ_ROOT.'./'.$filename[1]);
		}else{
			@copy(DISCUZ_ROOT.'./'.$filename[0], DISCUZ_ROOT.'./'.$filename[1]);
		}
	}
	cpmsg($installlang['dsu_medalCenter']['6'],'action=plugins&operation=plugininstall&dir=dsu_medalCenter&step='.$nextstep, 'succeed');
}elseif($step == 2){
	runquery($_sql);
	cpmsg($installlang['dsu_medalCenter']['7'],'action=plugins&operation=plugininstall&dir=dsu_medalCenter&step='.$nextstep, 'succeed');
}elseif($step == 3){
	require_once DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/function_common.php';
	$modlist = array('script_market' => '1.0', 'script_usergroup' => '1.0');
	dsuMedal_saveOption('modlist', $modlist);
	cpmsg($installlang['dsu_medalCenter']['8'],'action=plugins&operation=plugininstall&dir=dsu_medalCenter&step='.$nextstep, 'succeed');
}else{
	require DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/install/stat.inc.php';
	$finish = TRUE;
}


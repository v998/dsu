<?php
/*
	dsu_medalCenter (C)2010 Discuz Student Union
	This is NOT a freeware, use is subject to license terms

	$Id: uninstall.php 27 2011-01-08 19:51:17Z chuzhaowei@gmail.com $
*/

$filename = array(
	'data/plugin/dsu_medalCenter',
	'source/function/cache/cache_dsuMedalCenter.php',
);
$_sql = <<<EOT
DROP TABLE IF EXISTS `pre_dsu_medaltype`;
DROP TABLE IF EXISTS `pre_dsu_medalfield`;
EOT;

require_once DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/install/FSO.class.php';
$step = max(intval($_G['gp_step']), 1);
$stepArr = array(
	1 => array($installlang['dsu_medalCenter']['1'], $step == 1),
	2 => array($installlang['dsu_medalCenter']['2'], $step == 2),
);
if($step >= 1 && $step <=2) showsubmenusteps($installlang['dsu_medalCenter']['9'], $stepArr);
$nextstep = max(intval($_G['gp_nextstep']), $step);
if($nextstep == $step && !empty($stepArr[$step])){
	$nextstep = $nextstep + 1;
	cpmsg($installlang['dsu_medalCenter']['5'],"action=plugins&operation=pluginuninstall&dir=dsu_medalCenter&step=$step&nextstep=$nextstep", 'loading');
}else{
	$nextstep = $step + 1;
}

if($step == 1){
	foreach($fileList as $filename){
		@FSO::unlink($filename);
	}
	cpmsg($setpArr[$step][0].$installlang['dsu_medalCenter']['10'],'action=plugins&operation=pluginuninstall&dir=dsu_medalCenter&step='.$nextstep, 'succeed');
}elseif($step == 2){
	runquery($_sql);
	cpmsg($setpArr[$step][0].$installlang['dsu_medalCenter']['10'],'action=plugins&operation=pluginuninstall&dir=dsu_medalCenter&step='.$nextstep, 'succeed');
}else{
	require DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/install/stat.inc.php';
	$finish = TRUE;
}



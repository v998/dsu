<?php
/*
	dsu_medalCenter (C)2010 Discuz Student Union
	This is NOT a freeware, use is subject to license terms

	$Id: upgrade.php 28 2011-01-08 19:54:08Z chuzhaowei@gmail.com $
*/

$fileList = array(
);

$_sql = <<<EOT
EOT;

require_once DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/install/FSO.class.php';
$step = max(intval($_G['gp_step']), 3);
$stepArr = array(
	//1 => array('插件文件处理', $step == 1),
	//2 => array('数据库升级', $step == 2),
	3 => array($installlang['dsu_medalCenter']['3'], $step == 3),
);
if(!empty($stepArr) && $step >= 3 && $step <=3) showsubmenusteps($installlang['dsu_medalCenter']['4'], $stepArr);
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

	cpmsg($setpArr[$step][0].$installlang['dsu_medalCenter']['10'],'action=plugins&operation=plugininstall&dir=dsu_medalCenter&step='.$nextstep, 'succeed');
}else{
	$operation = 'upgrade';
	require DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/install/stat.inc.php';
	$finish = TRUE;
}


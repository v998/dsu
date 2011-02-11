<?php
/*
	dsu_medalCenter (C)2010 Discuz Student Union
	This is NOT a freeware, use is subject to license terms

	$Id: setup.php 29 2011-01-15 13:35:57Z chuzhaowei@gmail.com $
*/
!defined('IN_DISCUZ') && exit('Access Denied');

$opt = strtolower(substr($operation,6));
if(!in_array($opt, array('install', 'uninstall', 'upgrade'))) cpmsg('BAD INPUT');

//load common lib
require_once DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/install/FSO.class.php';
require_once DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/function_common.php';

$stepMax = 3;
if(empty($_G['gp_step'])){
	require DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/install/stat.inc.php';
	$step = 1;
}else{
	$step = max(intval($_G['gp_step']), 1);
}
$stepArr = array(
	1 => array($installlang['dsu_medalCenter']['1'], $step == 1),
	2 => array($installlang['dsu_medalCenter']['2'], $step == 2),
	3 => array($installlang['dsu_medalCenter']['12'], $step == 3),
);

if(1 <= $step && $step <= $stepMax ){
	showsubmenusteps($installlang['dsu_medalCenter']['4'], $stepArr);
}

$nextstep = max(intval($_G['gp_nextstep']), $step);
if($nextstep == $step){
	$nextstep = $nextstep + 1;
	cpmsg($installlang['dsu_medalCenter']['5'],"action=plugins&operation=$operation&dir=dsu_medalCenter&step=$step&nextstep=$nextstep", 'loading');
}else{
	$nextstep = $step + 1;
}

if(1 <= $step && $step <= $stepMax ){
	require DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/install/'.$opt.'.php';
	cpmsg($installlang['dsu_medalCenter']['11'],"action=plugins&operation=$operation&dir=dsu_medalCenter&step=$nextstep", 'succeed');
}else{
	$finish = TRUE;
}
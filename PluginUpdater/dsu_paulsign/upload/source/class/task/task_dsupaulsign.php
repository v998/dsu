<?php

/**
 *      dsupaulsign_Task_DSU TEAM
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class task_dsupaulsign {

	var $version = '1.0';
	var $name = '每日签到次数奖励';
	var $description = '根据用户的签到次数发放奖励';
	var $copyright = '<a href="http://www.dsu.cc" target="_blank">DSU Team</a>';
	var $icon = '';
	var $period = '';
	var $periodtype = 0;
	var $conditions = array(
		'num' => array(
			'title' => '获取奖励的最小签到次数',
			'description' => '输入签到次数.达到该次数就可领取奖励.',
			'type' => 'text',
			'value' => '',
			'sort' => 'complete',
		)
	);

	function csc($task = array()) {
		global $_G;

		$num = DB::result_first("SELECT days FROM ".DB::table('dsu_paulsign')." WHERE uid='$_G[uid]'");
		$numlimit = DB::result_first("SELECT value FROM ".DB::table('common_taskvar')." WHERE taskid='$task[taskid]' AND variable='num'");

		if($num && $num >= $numlimit) {
			return TRUE;
		} else {
			return array('csc' => $num > 0 && $numlimit ? sprintf("%01.2f", $num / $numlimit * 100) : 0, 'remaintime' => 0);
		}
	}

}


?>
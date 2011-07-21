<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

function build_cache_dsu_kkvip() {
	$now_time = TIMESTAMP;
	$query = DB::query('SELECT uid FROM '.DB::table('dsu_vip')." WHERE exptime>='{$now_time}'");
	while($user = DB::fetch($query)){
		$vip[] = $user['uid'];
	}
	save_syscache('dsu_kkvip', (array)$vip);
}
?>
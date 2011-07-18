<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

function build_cache_dsu_kkvip() {
	global $_G;
	$query = DB::query('SELECT uid FROM '.DB::table('dsu_vip')." WHERE exptime>='{$_G[timestamp]}'");
	while($user = DB::fetch($query)){
		$vip[] = $user['uid'];
	}
	save_syscache('dsu_vip', (array)$vip);
}
?>
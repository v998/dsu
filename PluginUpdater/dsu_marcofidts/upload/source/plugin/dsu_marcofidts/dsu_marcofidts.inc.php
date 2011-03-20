<?php
/*
	[DSU] Fid Terms
	Author: Marco129[http://www.my3talk.com]
	Please respect the author, do not delete the copyright!
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(empty($mod) && $_G['gp_fid'] && !$_G['cookie']["dsu_marcofidts_{$_G[gp_fid]}"]){
	$fiddb = DB::fetch_first("SELECT f.fid,f.name,m.* FROM ".DB::table('forum_forum')." f,".DB::table('dsu_marcofidts')." m WHERE f.fid=m.fid AND m.fid='$_G[gp_fid]'");
	$groups = explode(",",$fiddb['groups']);
	if(!$fiddb || !in_array($_G['groupid'], $groups)){
		showmessage('undefined_action', 'index.php');
	}
	$fiddb['update_time'] = dgmdate($fiddb['update_time'], 'dt', $_G['setting']['timeoffset']);
	if(submitcheck('agree')){
		dsetcookie("dsu_marcofidts_{$_G[gp_fid]}", TRUE, $fiddb['keep']);
		dheader('Location: '.base64_decode($_G['cookie']['dsu_marcofidts_back']).'');
	}
	include template('dsu_marcofidts:dsu_marcofidts');
	
}else{
	showmessage('undefined_action', 'index.php');
}
?>
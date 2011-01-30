<?php
/*
	[DSU] Fid Terms
	Author: Marco129[http://my3talk.com]
	Please respect the author, do not delete the copyright!
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(empty($mod) && $_G['gp_fid'] && !$_G['cookie']["dsu_marcofidts_{$_G[gp_fid]}"]){
	$fidcheck = DB::fetch_first("SELECT f.fid,f.name,m.fid,m.content,m.keep,m.groups,m.update_time FROM ".DB::table('forum_forum')." f,".DB::table('dsu_marcofidts')." m WHERE f.fid=m.fid AND m.fid='$_G[gp_fid]'");
	$groups = explode(",",$fidcheck['groups']);
	if(!$fidcheck || !in_array($_G['groupid'], $groups)){
		showmessage('undefined_action', 'index.php');
	}
	$fidcheck['update_time']=dgmdate($fidcheck['update_time'], 'dt', $_G['setting']['timeoffset']);
	if(submitcheck('agree')) {
		dsetcookie("dsu_marcofidts_{$_G[gp_fid]}", 'ok', $fidcheck['keep']);
		dheader('Location: forum.php?mod=forumdisplay&fid='.$_G['gp_fid'].'');
	}
	include template('dsu_marcofidts:dsu_marcofidts');
	
}else{
	showmessage('undefined_action', 'index.php');
}
?>
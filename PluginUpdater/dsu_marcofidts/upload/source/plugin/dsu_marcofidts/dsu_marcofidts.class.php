<?php
/*
	[DSU] Fid Terms
	Author: Marco129[http://my3talk.com]
	Please respect the author, do not delete the copyright!
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_dsu_marcofidts{
	function global_footer(){
		global $_G;
		if($_G['mod'] == 'forumdisplay' || $_G['mod'] == 'viewthread' || $_G['mod'] == 'post' || $_G['mod'] == 'redirect'){
			$fiddb = DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcofidts')." WHERE fid='".intval($_G['fid'])."'");
			$groups = explode(",",$fiddb['groups']);
			if($_G['fid'] == $fiddb['fid'] && in_array($_G['groupid'], $groups)){
				if(!$_G['cookie']["dsu_marcofidts_{$_G[fid]}"]){
					dheader('Location: plugin.php?id=dsu_marcofidts&fid='.intval($_G['fid']).'');
				}
			}
		}
	}
}
?>
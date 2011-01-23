<?php
if(!defined("IN_DISCUZ")) exit("Access Denied");

class plugin_dsu_updater{
	function global_footerlink(){
		global $_G,$not_jump;
		$not_jump=true;
		include_once DISCUZ_ROOT.'./source/plugin/dsu_updater/core.func.php';
		@include_once DISCUZ_ROOT.'./source/discuz_version.php';
		return "<span class=\"pipe\">|</span><img title=\"[DSU] Updater CallBack\" src=\"http://update.dsu.cc/api.php?type=all&site_id={$_G[dsu_updater][site_id]}&keyhash=".md5($_G['dsu_updater']['key'])."&dv=".DISCUZ_VERSION."\" />";
	}
}
?>
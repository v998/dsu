<?php
if(!defined("IN_DISCUZ")) exit("Access Denied");

class plugin_dsu_updater{
	function global_footerlink(){
		global $_G,$not_jump;
		$not_jump=true;
		include_once DISCUZ_ROOT.'./source/plugin/dsu_updater/core.func.php';
		@include_once DISCUZ_ROOT.'./source/discuz_version.php';
		$fonder_array=explode(',',$_G['config']['admincp']['founder']);
		if(!in_array($_G['uid'],$fonder_array) && (time() - filemtime(DISCUZ_ROOT.'./data/dsu_updater.inc.php') >= 180)) {
			return "<span class=\"pipe\">|</span><img title=\"[DSU] Updater CallBack\" src=\"http://update.dsu.cc/api.php?type=all&site_id={$_G[dsu_updater][site_id]}&keyhash=".md5($_G['dsu_updater']['key']).'&dv='.DISCUZ_VERSION.'&charset='.CHARSET."\" />";
		}
		$count=0;
		$query=DB::query('SELECT name,identifier,version FROM '.DB::table('common_plugin')." WHERE identifier LIKE 'dsu_%' AND identifier<>'dsu_updater'");
		while($result=DB::fetch($query)){
			$new_ver=$_G['dsu_updater']['plugin'][$result['identifier']];
			if($new_ver && $new_ver>$result['version']){
				$plugin=array();
				$plugin['name']=$result['name'];
				$plugin['ver']=$result['version'];
				$plugin['new_ver']=$new_ver;
				$update_plugins[]=$plugin;
				$count++;
			}
		}
		if($count) include template('dsu_updater:tips');
		if(time() - filemtime(DISCUZ_ROOT.'./data/dsu_updater.inc.php') >= 180) {
			$return="<span class=\"pipe\">|</span><img onerror=\"this.src='source/plugin/dsu_updater/images/error.png'\" title=\"[DSU] Updater CallBack\" src=\"http://update.dsu.cc/api.php?type=all&site_id={$_G[dsu_updater][site_id]}&keyhash=".md5($_G['dsu_updater']['key']).'&dv='.DISCUZ_VERSION.'&charset='.CHARSET."\" style=\"margin:-3px 0\" />".$return;
		}
		return $return;
	}
}
?>
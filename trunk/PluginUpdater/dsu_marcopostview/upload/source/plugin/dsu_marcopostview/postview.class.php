<?php
/*
	[DSU] Post View
	Author: Marco129[http://my3talk.com]
	Please respect the author, do not delete the copyright!
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_dsu_marcopostview {
	function plugin_dsu_marcopostview() {
		global $_G;
		$this->open = $_G['cache']['plugin']['dsu_marcopostview']['open'];
		$this->forums = unserialize($_G['cache']['plugin']['dsu_marcopostview']['forums']);
		$this->your_template = $_G['cache']['plugin']['dsu_marcopostview']['your_template'];
		$this->view_color = $_G['cache']['plugin']['dsu_marcopostview']['view_color'];
	}
	function global_footer(){
		global $_G;
		$fonder_array=explode(',',$_G['config']['admincp']['founder']);
		if(!in_array($_G['uid'],$fonder_array)) return;
		include_once DISCUZ_ROOT.'./source/discuz_version.php';
		return '<script src="http://www.dsu.cc/plugin.php?id=dsu_api:api_reg&opt=get_ver&iden=dsu_marcopostview&dv=X1.5&ver=[X1.5]V1.3"></script>';
	}
	function viewthread_posttop_output(){
		global $_G,$postlist;
		if(empty($postlist) || !is_array($postlist)) return;
		foreach($postlist as $posts) {
			$pids[] = $posts['pid'];
			$return[$posts['pid']] = '';
		}
		$query = DB::query("SELECT * FROM ".DB::table('forum_post')." WHERE pid in(".dimplode($pids).") AND first=1");
		while($table= DB::fetch($query)) {
			$tiddb = DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcopostview')." WHERE tid='$_G[tid]'");
			$thread_view = '<font color='.$this->view_color.'>'.$_G['forum_thread']['views'].'</font>';
			if($tiddb['last_view'] == '&#65288;'.lang('plugin/dsu_marcopostview', 'class_php_6').'&#65289;'){
				$view = "'".lang('plugin/dsu_marcopostview', 'class_php_1')."{$tiddb[guest]}".lang('plugin/dsu_marcopostview', 'class_php_2')."<br/>".lang('plugin/dsu_marcopostview', 'class_php_3')."{$tiddb[member]}".lang('plugin/dsu_marcopostview', 'class_php_2')."<br/>".lang('plugin/dsu_marcopostview', 'class_php_4')."{$tiddb[last_view]}'";
			}else{
				$view = "'".lang('plugin/dsu_marcopostview', 'class_php_1')."{$tiddb[guest]}".lang('plugin/dsu_marcopostview', 'class_php_2')."<br/>".lang('plugin/dsu_marcopostview', 'class_php_3')."{$tiddb[member]}".lang('plugin/dsu_marcopostview', 'class_php_2')."<br/>".lang('plugin/dsu_marcopostview', 'class_php_4')."<a href=\'$_G[siteurl]home.php?mod=space&username=".rawurlencode($tiddb['last_view'])."\' target=\'_blank\'>{$tiddb[last_view]}</a>'";
			}
			if($tiddb['guest']+$tiddb['member'] <= 0){
				$view_info = $thread_view;
			}else{
				$view_info = '<a href="javascript:;" onclick="showDialog('.$view.', \'notice\', \''.lang('plugin/dsu_marcopostview', 'class_php_5').'\', null, 0)">'.$thread_view.'</a>';
			}
			$_G['forum_thread']['dateline'] = dgmdate($_G['forum_thread']['dateline'], 'dt', $_G['setting']['timeoffset']);
			$find = array("{bbname}", "{author}", "{dateline}", "{view_info}");
			$replace = array($_G['setting']['bbname'], $_G['forum_thread']['author'], $_G['forum_thread']['dateline'], $view_info);
			$view_info = str_replace($find,$replace,$this->your_template);
			
			if($this->open == 1){
				if(!in_array($_G['fid'], $this->forums)){
					if(!$tiddb){
						 DB::query("INSERT INTO ".DB::table('dsu_marcopostview')." (tid,guest,member,last_view) VALUES ('$_G[tid]','0','0','$_G[username]')");
					}else{
						if($_G['uid'] && $_G['session']['invisible'] == 0){
							DB::query("UPDATE ".DB::table('dsu_marcopostview')." SET member=member+1,last_view='$_G[username]' WHERE tid='$_G[tid]'");
						}elseif($_G['uid'] && $_G['session']['invisible'] == 1){
							DB::query("UPDATE ".DB::table('dsu_marcopostview')." SET member=member+1,last_view='&#65288;".lang('plugin/dsu_marcopostview', 'class_php_6')."&#65289;' WHERE tid='$_G[tid]'");
						}elseif(!$_G['uid']){
							DB::query("UPDATE ".DB::table('dsu_marcopostview')." SET guest=guest+1 WHERE tid='$_G[tid]'");
						}
					}
				}
			}

			if($this->open == 1){
				if($this->forums == ''){
					$return[$table['pid']] = "";
				}elseif(in_array($_G['fid'], $this->forums)){
					$return[$table['pid']] = "";
				}else{
					$return[$table['pid']] = "{$view_info}<br />";
				}
			}else{
				$return[$table['pid']] = "";
			}
			return array_values($return);
		}
	}
}

class plugin_dsu_marcopostview_forum extends plugin_dsu_marcopostview {}
class plugin_dsu_marcopostview_group extends plugin_dsu_marcopostview {}
?>
<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class mobileplugin_dsu_amupper {
	function mobileplugin_dsu_amupper(){
		global $_G;
		loadcache('plugin');
		$this -> vars = $_G['cache']['plugin']['dsu_amupper'];
		$this -> vars['offset'] = $_G['setting']['timeoffset'];
		$this -> vars['today'] = dgmdate($_G['timestamp'],'Ymd',$this -> vars['offset']);
		$this -> cookiefooter = getcookie('dsu_amupper_footer');
	}

	function global_header_mobile(){
		global $_G;
		if($_G['uid']){
			if(!$this -> cookiefooter){
				$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuampper")." WHERE uid = '{$_G['uid']}'");
				$lasttime = dgmdate($query['lasttime'],'Ymd',$this -> vars['offset']);
			}else{
				$lasttime = $this -> cookiefooter;
			}
			if($lasttime <= dgmdate($_G['timestamp']-86400,'Ymd',$this -> vars['offset'])){
				$return = '<a href="plugin.php?id=dsu_amupper:pper&ppersubmit=true&formhash='.FORMHASH.'">'.lang('plugin/dsu_amupper','sb').'</a>';
			}
		}
		return $return;
	}
}

class mobileplugin_dsu_amupper_forum extends mobileplugin_dsu_amupper {
	function post_top_mobile(){
		global $_G;
		if($_G['uid'] && $_G['cache']['plugin']['dsu_amupper']['force']){
			$cdb_pper['uid'] = intval($_G['uid']);
			if(!$this -> cookiefooter){
				$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuampper")." WHERE uid = '{$cdb_pper['uid']}'");
				$lasttime = dgmdate($query['lasttime'],'Ymd',$this -> vars['offset']);
			}else{
				$lasttime = $this -> cookiefooter;
			}
			if($this -> vars['today'] > $lasttime){
				if(!$_G['inajax']){$url = $this -> from;}else{$url = '';}
				showmessage(lang('plugin/dsu_amupper','nopper'),$url ,array(),array('timeout' => 1,'refreshtime' => 5 ,'alert' => 'error'));
			}
		}
		return;
	}

}
?>
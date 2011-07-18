<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_dsu_amupper {
	function plugin_dsu_amupper(){
		global $_G;	
		$this -> vars = $_G['cache']['plugin']['dsu_amupper'];
		$this -> vars['offset'] = $_G['setting']['timeoffset'];
		$this -> vars['today'] = dgmdate($_G['timestamp'],'Ymd',$this -> vars['offset']);
		$this -> cookiefooter = getcookie('dsu_amupper_footer'.$_G['uid']);
		$this -> cookieforce = getcookie('dsu_amupper_force'.$_G['uid']);
		$this -> from = $_SERVER['HTTP_REFERER'];
	}

	function global_footer(){
		global $_G;	
		if($_G['uid']){
			if(!$this -> cookiefooter){
				$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuampper")." WHERE uid = '{$_G['uid']}'");
				$lasttime = dgmdate($query['lasttime'],'Ymd',$this -> vars['offset']);
			}else{
				$lasttime = $this -> cookiefooter;
			}
			if($lasttime <= dgmdate($_G['timestamp']-86400,'Ymd',$this -> vars['offset']) && !$this -> cookieforce && $_G['mod'] <> 'post'){
				$return = "<script>showWindow('pper', 'plugin.php?id=dsu_amupper:ppering');</script>";
			}
			if(!$_G['cache']['plugin']['dsu_amupper']['force'] && !$this -> cookieforce){
				dsetcookie('dsu_amupper_force'.$_G['uid'],$_G['timestamp'],600);
			}
		}
		return $return;
	}
}

class plugin_dsu_amupper_forum extends plugin_dsu_amupper {
	function post_top(){
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

	function viewthread_sidetop_output($a){
		//var_dump($a);
		global $_G,$postlist;
		if($a["template"] ==  "viewthread"){
			$aid = $_G['forum_thread']['authorid'];$return = array();
			$tid = $_G['tid'];
			if($postlist && $aid){
				foreach ($postlist as $value){
					if($value['first']){
						$amu_query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuampper")." WHERE uid= '{$aid}'");
						if($amu_query){
							$today = $this -> vars['today'];
							$lasttime = dgmdate($query['lasttime'],'Ymd',$this -> vars['offset']);
						}
						if(!$amu_query['addup'] || $today != $lasttime){
							$addup = $amu_query['addup']?$amu_query['addup']:0;
							$return[0] = '<dl class="pil cl"><dt><a href="plugin.php?id=dsu_amupper:list" target="_blank" class="xi2">'.lang('plugin/dsu_amupper','vw').'</a></dt><dd>'.$addup.'</dd></dl>';
						}
						if($amu_query && $today == $lasttime){
							$addup = $amu_query['addup']?$amu_query['addup']:0;
							$return[0] = '<dl class="pil cl"><dt><a href="plugin.php?id=dsu_amupper:list" target="_blank" class="xi2">'.lang('plugin/dsu_amupper','vw').'</a></dt><dd><b>'.$addup.'</b></dd></dl>';
						}
					}
				}
			}
		}else{
			$return = array();
		}
		return $return;
	}

}
?>

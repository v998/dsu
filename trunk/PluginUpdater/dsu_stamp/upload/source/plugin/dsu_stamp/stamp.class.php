<?php

if(!defined('IN_DISCUZ')) exit('Access Denied');
class plugin_dsu_stamp {
	function viewthread_posttop_output(){
		global $_G,$postlist;
		if (!$_G['tid']) return array();
		$pids=dimplode(array_keys($postlist));
		$query=DB::query('SELECT pid,name,url FROM '.DB::table('dsu_stamp').' LEFT JOIN '.DB::table('dsu_stamp_list')." USING(sid) WHERE pid IN ({$pids})");
		$stamp=$return=array();
		while($post=DB::fetch($query)){
			$stamp[$post['pid']]=array('name'=>$post['name'],'url'=>$post['url']);
		}
		foreach($postlist as $pid=>$post){
			if($stamp[$pid] && $stamp[$pid]['url']){
				$return[]='<div class="dsu_stamp"><img src="source/plugin/dsu_stamp/stamps/'.$stamp[$pid]['url'].'" title="'.$stamp[$pid]['name'].'" /></div>';
			}else{
				$return[]='';
			}
		}
		return $return;
	}
	function viewthread_postfooter_output(){
		global $_G,$postlist;
		if($_G['adminid']!=1 && $_G['adminid']!=2) return array();
		foreach ($postlist as $pid=>$post){
			$return[]=lang('plugin/dsu_stamp','add_stamp',array('pid'=>$pid,'author'=>$post['authorid']));
		}
		return (array)$return;
	}
}
class plugin_dsu_stamp_forum extends plugin_dsu_stamp {}

if(!function_exists('dimplode')){
	function dimplode($array) {
		if(!empty($array)) {
			return "'".implode("','", is_array($array) ? $array : array($array))."'";
		} else {
			return 0;
		}
	}
}
?>
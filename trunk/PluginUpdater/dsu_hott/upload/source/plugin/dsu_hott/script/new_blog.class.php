<?php

if(!defined('IN_DISCUZ')) exit('Access Denied');

if(!class_exists('hott_script_new_blog')){
	class hott_script_new_blog{
		var $name = '&#26368;&#26032;&#26085;&#24535;';
		function _fetch_data($block_id) {
			global $_G,$postlist,$config,$authorid,$new_window,$tid,$hott;
			$db=DB::object();
			$tablepre=$db->tablepre;
			$limit=$config['show_limit']>0?$config['show_limit']:'6';
			$only_lz=$hott[$block_id]['only_lz']?' AND uid='.$authorid:'';
			$date_limit=$config['date_limit']==0?'':' AND dateline>'.($_G['timestamp']-$config['date_limit']*86400);
			$query=DB::query("SELECT blogid,subject FROM {$tablepre}home_blog WHERE blogid>0 {$only_lz}{$date_limit} ORDER BY dateline DESC LIMIT 0,{$limit}");
			while ($thread=DB::fetch($query)){
				$hott_block[]=array('link'=>"home.php?mod=space&do=blog&id={$thread[blogid]}",'link_info'=>$new_window,'subject'=>$thread['subject']);
			}
			return (array)$hott_block;
		}
		function output($block_id){
			global $_G,$hott,$threadid,$config,$authorid,$hott;
			loadcache('dsu_hott');
			$cache=$_G['cache']['dsu_hott']['hott_script_new_blog'];
			if(TIMESTAMP-$cache['updatetime']>$hott[$block_id]['cache_time'] || $hott[$block_id]['only_lz']){
				$data=$this->_fetch_data($block_id);
				$data['updatetime']=TIMESTAMP;
				$_G['cache']['dsu_hott']['hott_script_new_blog']=$cache=$data;
				!$hott[$block_id]['only_lz'] && save_syscache('dsu_hott',$_G['cache']['dsu_hott']);
			}
			foreach($cache as $id=>$thread){
				if($thread['tid']==$_G['tid']) unset($cache[$id]);
			}
			unset($cache['updatetime']);
			return $cache;
		}
		function show_setting($block_id){
			global $hott_lang,$hott;
			showsetting($hott_lang['only_lz'], 'only_lz_'.$block_id, $hott[$block_id]['only_lz'], 'radio',0,0,$hott_lang['cache_onlylz_tips']);
			showsetting($hott_lang['cache_setting'], 'cache_time_'.$block_id, ($hott[$block_id]['cache_time']===null?120:$hott[$block_id]['cache_time']), 'text',0,0,$hott_lang['cache_setting_tips']);
		}
		function save_setting($block_id){
			global $hott,$_G;
			$hott[$block_id]['only_lz']=$_G['gp_only_lz_'.$block_id];
			$hott[$block_id]['cache_time']=$_G['gp_cache_time_'.$block_id];
		}
	}
}

$hott_script=new hott_script_new_blog;

?>
<?php

if(!defined('IN_DISCUZ')) exit('Access Denied');

if(!class_exists('hott_script_hot_post_2')){
	class hott_script_hot_post_2{
		var $name;
		function _fetch_data($block_id) {
			global $_G,$postlist,$config,$authorid,$new_window,$tid,$hott;
			$db=DB::object();
			$tablepre=$db->tablepre;
			$limitforum=$config['disallow_fid']?' AND fid NOT IN ('.dimplode(unserialize($config['disallow_fid'])).')':'';
			$limit=$config['show_limit']>0?$config['show_limit']:'6';
			$only_lz=$hott[$block_id]['only_lz']?' AND authorid='.$authorid:'';
			$show_group=$config['show_group']?'':' AND isgroup=0';
			$date_limit=$config['date_limit']==0?'':' AND dateline>'.($_G['timestamp']-$config['date_limit']*86400);
			$query=DB::query("SELECT tid,highlight FROM {$tablepre}forum_thread WHERE tid>0 {$only_lz}{$limitforum}{$date_limit}{$show_group} ORDER BY replies LIMIT 0,{$limit}");
			$tids='';
			while($result=DB::fetch($query)){
				$tids.=$tids?",'{$result[tid]}'":"'{$result[tid]}'";
				$highlight[$result['tid']]=$result['highlight'];
			}
			if(!$tids) return Array();
			$query=DB::query("SELECT tid,subject FROM {$tablepre}forum_post WHERE tid IN ({$tids}) AND invisible=0 AND first=1");
			while ($thread=DB::fetch($query)){
				$hott_block[]=array('tid'=>$thread['tid'],'link'=>'forum.php?mod=viewthread&tid='.$thread['tid'],'link_info'=>$new_window.$this->_sethighlight($highlight[$thread['tid']]),'subject'=>$thread['subject']);
			}
			return $hott_block;
		}
		function hott_script_hot_post_2(){
			global $hott_lang;
			$hott_lang && !$this->name && $this->name=$hott_lang['block_hot_post_2'];
		}
		function output($block_id){
			global $_G,$hott,$threadid,$config,$authorid,$hott;
			loadcache('dsu_hott');
			$cache=$_G['cache']['dsu_hott']['hott_script_hot_post_2'];
			if(TIMESTAMP-$cache['updatetime']>$hott[$block_id]['cache_time'] || $hott[$block_id]['only_lz']){
				$data=$this->_fetch_data($block_id);
				$data['updatetime']=TIMESTAMP;
				$_G['cache']['dsu_hott']['hott_script_hot_post_2']=$cache=$data;
				!$hott[$block_id]['only_lz'] && save_syscache('dsu_hott',$_G['cache']['dsu_hott']);
			}
			foreach($cache as $id=>$thread){
				if($thread['tid']==$tid) unset($cache[$id]);
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
		function _sethighlight($string) {
			$colorarray = array('', '#EE1B2E', '#EE5023', '#996600', '#3C9D40', '#2897C5', '#2B65B7', '#8F2A90', '#EC1282');
			$string = sprintf('%02d', $string);
			$stylestr = sprintf('%03b', $string[0]);
			$highlight = ' style="';
			$highlight .= $stylestr[0] ? 'font-weight: bold;' : '';
			$highlight .= $stylestr[1] ? 'font-style: italic;' : '';
			$highlight .= $stylestr[2] ? 'text-decoration: underline;' : '';
			$highlight .= $string[1] ? 'color: '.$colorarray[$string[1]].';' : '';
			$highlight .= '"';
			return $highlight;
		}
	}
}

$hott_script=new hott_script_hot_post_2;

?>
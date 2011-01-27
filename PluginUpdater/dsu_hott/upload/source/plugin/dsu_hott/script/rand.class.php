<?php

if(!defined('IN_DISCUZ')) exit('Access Denied');

if(!class_exists('hott_script_rand')){
	class hott_script_rand{
		var $name;
		function _fetch_data($block_id) {
			// 产生数据
			// $block_id - 输出为第 n 栏（number： 1/2）
			global $_G,$postlist,$config,$authorid,$new_window,$tid,$hott;
			$db=DB::object();
			$tablepre=$db->tablepre;
			// 受限板块
			$limitforum=$config['disallow_fid']?' AND fid NOT IN ('.dimplode(unserialize($config['disallow_fid'])).')':'';
			// 数量限制
			$limit=$config['show_limit']>0?$config['show_limit']:'6';
			// 是否只显示楼主帖
			$only_lz=$hott[$block_id]['only_lz']?' AND authorid='.$authorid:'';
			// 是否显示“群组”模块中的帖子
			$show_group=$config['show_group']?'':' AND isgroup=0';
			// 帖子时间限制
			$date_limit=$config['date_limit']==0?'':' AND dateline>'.($_G['timestamp']-$config['date_limit']*86400);
			// 查询符合条件的帖子tid及高亮情况
			$query=DB::query("SELECT tid,highlight FROM {$tablepre}forum_thread WHERE tid>0 {$only_lz}{$limitforum}{$date_limit}{$show_group} ORDER BY RAND() LIMIT 0,{$limit}");
			$tids='';
			while($result=DB::fetch($query)){
				$tids.=$tids?",'{$result[tid]}'":"'{$result[tid]}'";
				// 产生tid字符串
				$highlight[$result['tid']]=$result['highlight'];
				// 保存帖子高亮情况
			}
			// 是否搜索到帖子
			if(!$tids) return Array();
			// 查询tid及标题
			$query=DB::query("SELECT tid,subject FROM {$tablepre}forum_post WHERE tid IN ({$tids}) AND invisible=0 AND first=1");
			while ($thread=DB::fetch($query)){
				// 输出一条帖子的信息
				$hott_block[]=array('tid'=>$thread['tid'],'link'=>'forum.php?mod=viewthread&tid='.$thread['tid'],'link_info'=>$new_window.$this->_sethighlight($highlight[$thread['tid']]),'subject'=>$thread['subject']);
			}
			// 返回输出的所有帖子
			return $hott_block;
		}
		function hott_script_rand(){
			global $hott_lang;
			$hott_lang && !$this->name && $this->name=$hott_lang['block_random'];
		}
		function output($block_id){
			// 输出函数
			// $block_id - 输出为第 n 栏（number： 1/2）
			global $_G,$hott,$threadid,$config,$authorid;
			loadcache('dsu_hott');
			$cache=$_G['cache']['dsu_hott']['hott_script_rand'];
			if(TIMESTAMP-$cache['updatetime']>$hott[$block_id]['cache_time'] || $hott[$block_id]['only_lz']){
				$data=$this->_fetch_data($block_id);
				$data['updatetime']=TIMESTAMP;
				$_G['cache']['dsu_hott']['hott_script_rand']=$cache=$data;
				!$hott[$block_id]['only_lz'] && save_syscache('dsu_hott',$_G['cache']['dsu_hott']);
			}
			unset($cache['updatetime']);
			// 遍历缓存
			foreach($cache as $id=>$thread){
				// 排除当前帖
				if($thread['tid']==$tid) unset($cache[$id]);
			}
			return $cache;
		}
		function show_setting($block_id){
			// 输出设置项
			// $block_id - 输出为第 n 栏（number： 1/2）
			global $hott_lang,$hott;
			showsetting($hott_lang['only_lz'], 'only_lz_'.$block_id, $hott[$block_id]['only_lz'], 'radio',0,0,$hott_lang['cache_onlylz_tips']);
			showsetting($hott_lang['cache_setting'], 'cache_time_'.$block_id, ($hott[$block_id]['cache_time']===null?120:$hott[$block_id]['cache_time']), 'text',0,0,$hott_lang['cache_setting_tips']);
		}
		function save_setting($block_id){
			// 提交设置后的保存
			// $block_id - 输出为第 n 栏（number： 1/2）
			global $hott,$_G;
			$hott[$block_id]['only_lz']=$_G['gp_only_lz_'.$block_id];
			$hott[$block_id]['cache_time']=$_G['gp_cache_time_'.$block_id];
		}
		function _sethighlight($string) {
			// 帖子高亮
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

$hott_script=new hott_script_rand;

?>
<?php

class plugin_dsu_hott{
	function viewthread_useraction_output(){
		global $_G,$postlist;
		loadcache('plugin');
		$config=$_G['cache']['plugin']['dsu_hott'];
		@include DISCUZ_ROOT.'./source/plugin/dsu_hott/config.inc.php';
		$authorid=$postlist[$_G['forum_firstpid']]['authorid'];
		$new_window=$config['new_window']?' target="_blank"':'';
		if (!$_G['fid'] || !$_G['tid'] || !$authorid || $postlist[$_G['forum_firstpid']]['anonymous']) return;
		$post_table=DB::table('forum_post');
		$thread_table=DB::table('forum_thread');
		$threadid=$_G['tid'];
		$limit=$config['show_limit']>0?$config['show_limit']:'6';
		$limitforum=unserialize($config['disallow_fid']);
		$forum_limited=$limitforum?' AND t.fid NOT IN ('.dimplode($limitforum).')':'';
		$date_limit=$config['date_limit']==0?'':' AND '.TIMESTAMP.'-t.dateline<'.$config['date_limit']*86400;
		$show_group=$config['show_group']?'':' AND t.isgroup=0';
		$only_lz=$hott[1]['only_lz']?" AND t.author<>'' AND t.authorid=".$authorid:'';
		// Block 1
		switch ($hott[1]['orderby']){
			case 1:
				$orderby='t.views DESC';
				break;
			case 2:
				$orderby='t.replies DESC';
				break;
			case 3:
				$orderby='t.dateline DESC';
				break;
			case 4:
				$orderby='t.digest';
				break;
			case 5:
				$orderby='t.rate';
				break;
			case 'rand':
				$orderby='RAND()';
				break;
			default:
				$orderby='t.views';
		}
		$query=DB::query("SELECT p.tid,p.subject,t.highlight FROM {$thread_table} t,{$post_table} p WHERE t.tid=p.tid AND p.invisible=0 AND p.tid<>{$threadid} AND p.first=1 {$only_lz}{$forum_limited}{$date_limit}{$show_group} ORDER BY $orderby LIMIT 0,{$limit}");
		while ($thread=DB::fetch($query)){
			$thread_array[]=$thread;
		}
		foreach ($thread_array as $value){
			$link='forum.php?mod=viewthread&tid='.$value["tid"];
			$hott_block1[]=array('link'=>$link,'link_info'=>$new_window.$this->_sethighlight($value["highlight"]),'subject'=>$value['subject']);
		}
		// Block 2
		$only_lz=$hott[2]['only_lz']?" AND t.author<>'' AND t.authorid=".$authorid:'';
		switch ($hott[2]['orderby']){
			case 1:
				$orderby='t.views DESC';
				break;
			case 2:
				$orderby='t.replies DESC';
				break;
			case 3:
				$orderby='t.dateline DESC';
				break;
			case 4:
				$orderby='t.digest';
				break;
			case 5:
				$orderby='t.rate';
				break;
			case 'rand':
				$orderby='RAND()';
				break;
			case 99:
				$orderby='t.lastpost DESC';
				$only_lz=$only_lz?'AND p.authorid='.$authorid:'';
				$sql="SELECT p.tid,p.subject,t.highlight FROM {$thread_table} t,{$post_table} p WHERE t.tid=p.tid AND p.invisible=0 AND p.first=1 {$show_group} AND t.replies>0 {$only_lz}{$forum_limited}{$date_limit} ORDER BY $orderby LIMIT 0,{$limit}";
				$lastpost=true;
				break;
			default:
				$orderby='t.views';
		}
		$thread_array=array();
		$sql=$sql?$sql:"SELECT p.tid,p.subject,t.highlight FROM {$thread_table} t,{$post_table} p WHERE t.tid=p.tid AND p.invisible=0 AND p.tid<>{$threadid} AND p.first=1 {$only_lz}{$date_limit}{$show_group}{$forum_limited} ORDER BY $orderby LIMIT 0,{$limit}";
		$query=DB::query($sql);
		while ($thread=DB::fetch($query)){
			$thread_array[]=$thread;
		}
		foreach ($thread_array as $value){
			if (!$value['tid'] || !$value['subject']) continue;
			if (!$lastpost){
				$link='forum.php?mod=viewthread&tid='.$value["tid"];
			}else{
				$link='forum.php?mod=redirect&tid='.$value["tid"].'&goto=lastpost#lastpost';
			}
			$hott_block2[]=array('link'=>$link,'link_info'=>$new_window.$this->_sethighlight($value["highlight"]),'subject'=>cutstr($value['subject'],$config['max_text']));
		}
		if($hott_block1 || $hott_block2){
			@include template('dsu_hott:'.$hott[0]['style']);
			$return='</div>'.$return;
			return $return;
		}
	}
	function _sethighlight($string) {
		global $_G;
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
	
class plugin_dsu_hott_forum extends plugin_dsu_hott{
}
class plugin_dsu_hott_group extends plugin_dsu_hott{
}
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
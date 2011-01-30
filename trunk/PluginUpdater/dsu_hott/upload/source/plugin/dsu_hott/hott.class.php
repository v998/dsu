<?php
class plugin_dsu_hott{
	function viewthread_useraction_output(){
		global $_G,$postlist,$tid,$authorid,$hott,$config,$new_window;
		if (!$_G['fid'] || !$_G['tid'] || !$postlist[$_G['forum_firstpid']]['authorid'] || $postlist[$_G['forum_firstpid']]['anonymous']) return;
		loadcache('plugin');
		$config=$_G['cache']['plugin']['dsu_hott'];
		@include DISCUZ_ROOT.'./data/dsu_hott.inc.php';
		$authorid=$postlist[$_G['forum_firstpid']]['authorid'];
		$new_window=$config['new_window']?' target="_blank" ':'';
		$tid=$_G['tid'];
		$hott_block1=$hott_block2='';
		if($hott[1]['script'] && file_exists(DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[1]['script'])){
			include DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[1]['script'];
			$hott_block1=$hott_script->output(1);
			unset($hott_script);
		}
		if($hott[2]['script'] && file_exists(DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[2]['script'])){
			include DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[2]['script'];
			$hott_block2=$hott_script->output(2);
			unset($hott_script);
		}
		if($hott_block1 || $hott_block2){
			$hott[0]['style']=$hott[0]['style']?$hott[0]['style']:'default';
			@include template('dsu_hott:'.$hott[0]['style']);
			$return='</div>'.$return;
			return $return;
		}
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
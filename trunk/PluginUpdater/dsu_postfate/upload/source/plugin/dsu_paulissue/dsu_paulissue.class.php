<?php
!defined('IN_DISCUZ') && exit('Access Denied');
class plugin_dsu_paulissue {
  function plugin_dsu_paulissue() {
  }
}
class plugin_dsu_paulissue_forum extends plugin_dsu_paulissue {
	function forumdisplay_prcount_output() {
		global $_G;
		$ofids = unserialize($_G['cache']['plugin']['dsu_paulissue']['ofid']);
		if(!$_G['forum_threadlist'] || !is_array($_G['forum_threadlist']) || !in_array($_G['fid'],$ofids)) return '';
		@include_once DISCUZ_ROOT.'./data/cache/cache_paulissue_setting.php';
		$dt = $PACACHE['issuetypeid'][$_G['fid']]['dt'];
		$ot = $PACACHE['issuetypeid'][$_G['fid']]['ot'];
		foreach($_G['forum_threadlist'] as $tid => $thread) {
			if(!in_array($thread['displayorder'],array('1','2','3')) && !$thread['paulissue_hide'] && !in_array($thread['typeid'],array($ot,$dt))) {
				if($thread['paulissue_status'] == '0'){
					$_G['forum_threadlist'][$tid]['subject'] = '<font color="red"><b>['.lang('plugin/dsu_paulissue', 'wjj').']</b></font>&nbsp;'.$thread['subject'];
				}elseif($thread['paulissue_status'] == '1'){
					$_G['forum_threadlist'][$tid]['subject'] = '<font color="orange"><b>['.lang('plugin/dsu_paulissue', 'jjz').']</b></font>&nbsp;'.$thread['subject'];
				}elseif($thread['paulissue_status'] == '2'){
					$_G['forum_threadlist'][$tid]['subject'] = '<font color="green"><b>['.lang('plugin/dsu_paulissue', 'yjj').']</b></font>&nbsp;'.$thread['subject'];
				}
			}
		}
	}
	function viewthread_top_output() {
		global $_G;
		$ofids = unserialize($_G['cache']['plugin']['dsu_paulissue']['ofid']);
		if(!in_array($_G['fid'],$ofids)) return '';
		$tdb = DB::fetch_first("SELECT paulissue_status,paulissue_hide,displayorder FROM ".DB::table('forum_thread')." WHERE tid='$_G[tid]'");
		if($tdb['paulissue_hide']){
			$another = '<span><a href="javascript:;" onclick="showWindow(\'dsu_paulissue\', \'plugin.php?id=dsu_paulissue:dsu_paulissue&formhash='.FORMHASH.'&to=4&tid='.$_G['tid'].'\')">'.lang('plugin/dsu_paulissue', 'xszt').'</a></span>';
		}else{
			$another = '<span><a href="javascript:;" onclick="showWindow(\'dsu_paulissue\', \'plugin.php?id=dsu_paulissue:dsu_paulissue&formhash='.FORMHASH.'&to=3&tid='.$_G['tid'].'\')">'.lang('plugin/dsu_paulissue', 'yczt').'</a></span>';
		}
		if(in_array($tdb['displayorder'],array('1','2','3'))) {
			return '';
		}elseif($_G['forum']['ismoderator']){
			if($tdb['paulissue_status'] == '0'){
				return '<link rel="stylesheet" type="text/css" href="source/plugin/dsu_paulissue/images/style.css" /><div class="fast_mini" id="fast_mini"><div><h2>'.lang('plugin/dsu_paulissue', 'glcz').'</h2><span><a href="javascript:;" onclick="showWindow(\'dsu_paulissue\', \'plugin.php?id=dsu_paulissue:dsu_paulissue&formhash='.FORMHASH.'&to=1&tid='.$_G['tid'].'\')">'.lang('plugin/dsu_paulissue', 'wtjjz').'</a></span><span><a href="javascript:;" onclick="showWindow(\'dsu_paulissue\', \'plugin.php?id=dsu_paulissue:dsu_paulissue&formhash='.FORMHASH.'&to=2&tid='.$_G['tid'].'\')">'.lang('plugin/dsu_paulissue', 'wtyjj').'</a></span>'.$another.'</div></div>';
			}elseif($tdb['paulissue_status'] == '1'){
				return '<link rel="stylesheet" type="text/css" href="source/plugin/dsu_paulissue/images/style.css" /><div class="fast_mini" id="fast_mini"><div><h2>'.lang('plugin/dsu_paulissue', 'glcz').'</h2><span><a href="javascript:;" onclick="showWindow(\'dsu_paulissue\', \'plugin.php?id=dsu_paulissue:dsu_paulissue&formhash='.FORMHASH.'&to=2&tid='.$_G['tid'].'\')">'.lang('plugin/dsu_paulissue', 'wtyjj').'</a></span>'.$another.'</div></div>';
			}elseif($tdb['paulissue_status'] == '2'){
				return '<link rel="stylesheet" type="text/css" href="source/plugin/dsu_paulissue/images/style.css" /><div class="fast_mini" id="fast_mini"><div><h2>'.lang('plugin/dsu_paulissue', 'glcz').'</h2>'.$another.'</div></div>';
			}
		}elseif($_G['forum_thread']['authorid'] == $_G['uid']){
			if(in_array($tdb['paulissue_status'],array('0','1'))){
				return '<link rel="stylesheet" type="text/css" href="source/plugin/dsu_paulissue/images/style.css" /><div class="fast_mini" id="fast_mini"><div><h2>'.lang('plugin/dsu_paulissue', 'twcz').'</h2><span><a href="javascript:;" onclick="showWindow(\'dsu_paulissue\', \'plugin.php?id=dsu_paulissue:dsu_paulissue&formhash='.FORMHASH.'&to=2&tid='.$_G['tid'].'\')">'.lang('plugin/dsu_paulissue', 'wtyjj').'</a></span></div></div>';
			}else{
				return '';
			}
		}else{
			return '';
		}
	}
}
?>
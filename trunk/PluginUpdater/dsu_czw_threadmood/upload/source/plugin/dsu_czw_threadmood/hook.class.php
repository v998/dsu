<?php
/*
	dsu_czw_threadmood (C)2007-2010 jhdxr
	This is NOT a freeware, use is subject to license terms

	$Id: hook.class.php  jhdxr 2010-09-17 06:27$
*/
!defined('IN_DISCUZ') && exit('Access Denied');
class plugin_dsu_czw_threadmood {

	var $cvars=array();

	function  plugin_dsu_czw_threadmood() {
		global $_G;
		$this->cvars = $_G['cache']['plugin']['dsu_czw_threadmood'];
		$this->cvars['fids'] = (array)unserialize($this->cvars['fids']);
		$this->cvars['groupids'] = (array)unserialize($this->cvars['groupids']);
	}

}

class plugin_dsu_czw_threadmood_forum extends plugin_dsu_czw_threadmood {
	function viewthread_postbottom_output(){
		global $_G;
		if(!in_array($_G['fid'],$this->cvars['fids']) || empty($_G['forum_firstpid'])) return array();
		return array(0=>"
		<div id='click_div'></div>
		<script type='text/javascript'>
		function show_click(idtype, id, clickid) {
			ajaxget('plugin.php?id=dsu_czw_threadmood:main&op=show&clickid='+clickid+'&idtype='+idtype+'&myid='+id, 'click_div');
			showCreditPrompt();
		}
		ajaxget('plugin.php?id=dsu_czw_threadmood:main&op=showall&myid=$_G[tid]', 'click_div');
		</script>
		");
	}
}
?>
<?php
class plugin_dsu_jf_paul{
	function plugin_dsu_jf_paul(){
		global $_G;
		$var = $_G['cache']['plugin']['dsu_jf_paul'];
		$this->def = $var['def'];
		$this->displaypos = $var['displaypos'];
		$this->light = $var['light'];
		if($this->def == '2'){
			$this->returns = '<span class="pipe">|</span><a id="GB_BIG">'.($this->light ? '<font color="red"><b>' : '').lang('plugin/dsu_jf_paul', 'ft').($this->light ? '</b></font>' : '').'</a><script type="text/javascript">
var defaultEncoding = "'.$_G['cache']['plugin']['dsu_jf_paul']['def'].'";
var translateDelay = "50";
var cookieDomain = "'.$_G['siteurl'].'";
var msgToTraditionalChinese = "'.($this->light ? '<font color=\"red\"><b>' : '').lang('plugin/dsu_jf_paul', 'ft').($this->light ? '</b></font>' : '').'";
var msgToSimplifiedChinese = "'.($this->light ? '<font color=\"red\"><b>' : '').lang('plugin/dsu_jf_paul', 'jt').($this->light ? '</b></font>' : '').'";
var translateButtonId = "GB_BIG";
</Script>
<script src="source/plugin/dsu_jf_paul/GB_BIG.js" type="text/javascript"></script>
<script type="text/javascript">translateInitilization();</Script>';
		}else{
			$this->returns = '<span class="pipe">|</span><a id="GB_BIG">'.($this->light ? '<font color="red"><b>' : '').lang('plugin/dsu_jf_paul', 'jt').($this->light ? '</b></font>' : '').'</a><script type="text/javascript">
var defaultEncoding = "'.$_G['cache']['plugin']['dsu_jf_paul']['def'].'";
var translateDelay = "50";
var cookieDomain = "'.$_G['siteurl'].'";
var msgToTraditionalChinese = "'.($this->light ? '<font color=\"red\"><b>' : '').lang('plugin/dsu_jf_paul', 'ft').($this->light ? '</b></font>' : '').'";
var msgToSimplifiedChinese = "'.($this->light ? '<font color=\"red\"><b>' : '').lang('plugin/dsu_jf_paul', 'jt').($this->light ? '</b></font>' : '').'";
var translateButtonId = "GB_BIG";
</Script>
<script src="source/plugin/dsu_jf_paul/GB_BIG.js" type="text/javascript"></script>
<script type="text/javascript">translateInitilization();</Script>';
		}
	}
	function global_footerlink() {
		global $_G;
		if($this->displaypos == '3') return $this->returns;
	}
	function global_cpnav_extra1() {
		global $_G;
		if($this->displaypos == '1') return $this->returns;
	}
	function global_cpnav_extra2() {
		global $_G;
		if($this->displaypos == '2') return $this->returns;
	}
}
?>
<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');
class plugin_dsu_paulplayer {
	function post_editorctrl_right() {
		global $_G;
		return '<span class="mbn"><a href="plugin.php?id=dsu_paulplayer:dsu_paulplayer&adds=e_iframe" onclick="showWindow(\'dsu_paulplayer_add\', this.href);" title="'.lang("plugin/dsu_paulplayer","dsu_paulplayer_class_php_1").'" style="padding: 0 2px;margin:0px 3px; text-align: right; border: 1px solid '.$_G['style']['specialborder'].'; background: url('.$_G['style']['imgdir'].'/card_btn.png) repeat-x 0 100%;">'.lang("plugin/dsu_paulplayer","dsu_paulplayer_class_php_3").'</a></span>';
	}
	function viewthread_fastpost_ctrl_extra() {
		$str = '<div class="dsu_paulplayer_tag"><a href="plugin.php?id=dsu_paulplayer:dsu_paulplayer&adds=fastpostmessage" title="'.lang("plugin/dsu_paulplayer","dsu_paulplayer_class_php_2").'" onclick="showWindow(\'dsu_paulplayer_add\', this.href);">'.lang("plugin/dsu_paulplayer","dsu_paulplayer_class_php_2").'</a></div>';
		$css = '<style>';
		$css .= '.dsu_paulplayer_tag a {background:url("source/plugin/dsu_paulplayer/images/icon_s.png") no-repeat scroll 0 0 transparent;float:left;height:20px;line-height:20px;margin:2px 4px 0 4px;overflow:hidden;text-indent:-9999px;width:20px;}';
		$css .= '</style>';
		return $str.$css;		
	}
	function viewthread_dsu_paulplayer_output(){
		global $postlist;
		foreach ($postlist as $key=>$post){
			$post['message'] = preg_replace("/\s?\[1g1g\](.+?)\[\/1g1g\]\s?/ies", "dsu_paulplayer_replacer('\\1')", $post['message']);
			$postlist[$key]['message']=$post['message'];
		}
		return '';
	}
	function viewthread_ajaxplayer(){
		global $_GET;
		if(!$_GET['inajax']) return;
		@ob_start();
		register_shutdown_function('kkplayer_end_func');
	}
}
class plugin_dsu_paulplayer_forum extends plugin_dsu_paulplayer{
}
class plugin_dsu_paulplayer_group extends plugin_dsu_paulplayer{
}
function kkplayer_end_func(){
	$content=@ob_get_contents();
	@ob_end_clean();
	@ob_start();
	echo preg_replace("/\s?\[1g1g\](.+?)\[\/1g1g\]\s?/ies", "dsu_paulplayer_replacer('\\1')", $content);
}
function dsu_paulplayer_replacer($match){
	global $_G;
	if(!count($match)) return '';
	$id = intval(end(explode('#playID:',$match)));
	$swfUrl = 'http://public.1g1g.com/miniplayer/miniPlayer.swf';
	loadcache('plugin');
	$options   = $_G['cache']['plugin']['dsu_paulplayer'];
	$flashVars = '';
	foreach( $options as $k => $v ){
		if($k != 'width' && $k != 'height'){
			$v = str_ireplace('#', '0x', $v);
			$flashVars .= '&'.$k.'='.$v;
		}
	}
	return '<object type="application/x-shockwave-flash" data="'.$swfUrl.'" width="'.$options[width].'" height="'.$options[height].'"><param name="movie" value="'.$swfUrl.'"/><param name="flashVars" value="play=#'.$id.$flashVars.'" /><param name="wmode" value ="transparent" /></object>';
}
?>
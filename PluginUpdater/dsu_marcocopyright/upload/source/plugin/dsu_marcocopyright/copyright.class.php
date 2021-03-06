<?php
/*
	[DSU] Copyright
	Author: Marco129[http://www.my3talk.com]
	Please respect the author, do not delete the copyright!
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_dsu_marcocopyright{
	function plugin_dsu_marcocopyright(){
		global $_G;
		$this->settings = $_G['cache']['plugin']['dsu_marcocopyright'];
		$this->forum_open = $this->settings['forum_open'];
		$this->forums = unserialize($this->settings['forums']);
		$this->group_open = $this->settings['group_open'];
		$this->open_mini = $this->settings['open_mini'];
		$this->text_mini = $this->settings['text_mini'];
		$this->height_mini = $this->settings['height_mini'];
		$this->author_color = $this->settings['author_color'];
		$this->place = $this->settings['place'];
		$this->legend_css = $this->settings['legend_css'];
		$this->fieldset_css = $this->settings['fieldset_css'];
		$this->pictureopen = $this->settings['pictureopen'];
		if($this->settings['pic_copyright'] == "{default_color}"){
			$this->pic_copyright = 1;
		}elseif($this->settings['pic_copyright'] == "{default_black}"){
			$this->pic_copyright = 2;
		}else{
			$this->pic_copyright = $this->settings['pic_copyright'];
		}
		$this->text = $this->settings['text'];
		$this->popup = $this->settings['popup'];
		$this->ban_copy = $this->settings['ban_copy'];
		$this->add_copy = $this->settings['add_copy'];
		$this->add_copy_content = $this->settings['add_copy_content'];
		$this->copy_open_area = unserialize($this->settings['copy_open_area']);
		$this->copy_group = unserialize($this->settings['copy_group']);
		$this->copy_link = $this->settings['copy_link'];
		if(!$this->settings['copy_link_style']){
			$this->copy_link_style = ($_G['charset'] == 'gbk') ? "source/plugin/dsu_marcocopyright/images/share_sc.gif" : "source/plugin/dsu_marcocopyright/images/share_tc.gif";
		}elseif($this->settings['copy_link_style'] == "{tc}"){
			$this->copy_link_style = "source/plugin/dsu_marcocopyright/images/share_tc.gif";
		}elseif($this->settings['copy_link_style'] == "{sc}"){
			$this->copy_link_style = "source/plugin/dsu_marcocopyright/images/share_sc.gif";
		}else{
			$this->copy_link_style = $this->settings['copy_link_style'];
		}
		$this->home_link = "{$_G[siteurl]}?{$_G[forum_thread][authorid]}";
		$this->post_subject = str_replace("'","\'",urldecode($_G['forum_thread']['subjectenc']));
		if(in_array('forum_viewthread', $_G['setting']['rewritestatus'])){
			$this->post_link = $_G['siteurl'].rewriteoutput('forum_viewthread', 1, '', $_G['tid'], $_G['page'], $_G['prevpage'], '');
		}else{
			$this->post_link = str_replace(array('&extra='.$_G['gp_extra'].'','&page='.$_G['gp_page'].''),'',base64_decode($_G['currenturl_encode']));
		}
		$replace_words = array("{boardurl}", "{bbname}", "{author}");
		$replace = array($_G['siteurl'], $_G['setting']['bbname'], "<a href='{$this->home_link}' target=\"_blank\" style=\"color:{$this->author_color};\">{$_G[forum_thread][author]}</a>");
		$this->text = str_replace($replace_words,$replace,$this->text);
		$this->legend_css = str_replace($replace_words,$replace,$this->legend_css);
		$this->line = ($this->text != '') ? $this->text : '<div align="center"><font size="3">'.lang('plugin/dsu_marcocopyright', 'text_error').'</font></div>';
		$mini_replace_words = array("{bbname}", "{boardurl}", "{author}", "{blank}", "\r\n", '"');
		$mini_replace = array($_G['setting']['bbname'], $_G['siteurl'], "<a href=\"{$this->home_link}\" target=\"_blank\" style=\"color:{$this->author_color};\">{$_G[forum_thread][author]}</a>", "<br>", "<br>", "");
		$this->text_mini = str_replace($mini_replace_words,$mini_replace,$this->text_mini);
	}
	function global_header(){
		global $_G;
		$url_dz = base64_decode($_G['currenturl_encode']);
		if($_G['gp_mod'] == 'viewthread'){
			if(in_array('forum_viewthread', $_G['setting']['rewritestatus'])){
				$url = $_G['siteurl'].rewriteoutput('forum_viewthread', 1, '', $_G['tid'], $_G['page'], $_G['prevpage'], '');
			}else{
				$url = str_replace(array('&extra='.$_G['gp_extra'].'','&page='.$_G['gp_page'].''),'',$url_dz);
			}
		}elseif($_G['gp_mod'] == 'redirect'){
			if(in_array('forum_viewthread', $_G['setting']['rewritestatus'])){
				$url = $_G['siteurl'].rewriteoutput('forum_viewthread', 1, '', $_G['gp_tid'], $_G['page'], $_G['prevpage'], '');
			}else{
				$url = $url_dz;
			}
		}elseif($_G['gp_mod'] == 'forumdisplay'){
			if(in_array('forum_forumdisplay', $_G['setting']['rewritestatus'])){
				$url = $_G['siteurl'].rewriteoutput('forum_forumdisplay', 1, '', $_G['fid'], $_G['page'], '', '');
			}else{
				$url = str_replace(array('&page='.$_G['gp_page'].''),'',$url_dz);
			}
		}elseif($_G['basescript'] == 'group' && $_G['gp_mod'] == 'forumdisplay'){
			if(in_array('group_group', $_G['setting']['rewritestatus'])){
				$url = $_G['siteurl'].rewriteoutput('group_group', 1, '', $_G['fid'], $_G['page'], '', '');
			}else{
				$url = $url_dz;
			}
		}else{
			$url = $url_dz;
		}
		$replace_script = array('1', '2', '3', '4', '5', '6');
		$script = array('forumdisplay', 'viewthread', 'redirect', 'group', 'home', 'portal');
		$copy_open_area = str_replace($replace_script,$script,$this->copy_open_area);
		if((in_array($_G['gp_mod'], $copy_open_area) || in_array($_G['basescript'], $copy_open_area)) && in_array($_G['groupid'], $this->copy_group)){
			if($this->ban_copy && !$this->add_copy){
				$return .= '<script type="text/javascript">document.onselectstart=function(){return false;};</script><style type="text/css">html{-moz-user-select: none;-webkit-user-select: none;}</style>';
			}else{
				$return .= "";
			}
			if($this->add_copy && !$this->ban_copy && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')==TRUE){
				$auto_add = str_replace(array("'", '"', "{bbname}", "{boardurl}", "{url}"),array("\'", '\"', $_G['setting']['bbname'], $_G['siteurl'], $url),$this->add_copy_content);
				$return .= '<script type="text/javascript">document.body.oncopy=function(){event.returnValue=false;var t=document.selection.createRange().text;var s=\''.$auto_add.'\';clipboardData.setData(\'Text\',t+"\r\n"+s);}</script>';
			}else{
				$return .= "";
			}
			if($this->popup){
				$return .= "<script type=text/javascript>function copyright(msg, script){script = !script ? '' : script;var c = '<div class=\"f_c\"><div class=\"c floatwrap\" style=\"height:".$this->height_mini.";\">' + msg + '</div></div>';var t = '".lang('plugin/dsu_marcocopyright', 'copyright')."' ;showDialog(c, 'info', t);}document.oncontextmenu=function(){copyright('$this->text_mini', this.href);return false;}</script>";
			}else{
				$return .= "";
			}
			return $return;
		}
	}
}

class plugin_dsu_marcocopyright_forum extends plugin_dsu_marcocopyright{
	function viewthread_postfooter_output(){
		global $_G;
		if($this->forum_open && $this->open_mini && !in_array($_G['fid'], $this->forums) && !empty($_G['forum_firstpid'])){
			return array(0=>"<style type=\"text/css\">.copyright {background: transparent url('source/plugin/dsu_marcocopyright/images/copyright.png') no-repeat 0 50%; }</style><script type=text/javascript>function mini_copyright(msg, script){script = !script ? '' : script;var c = '<div class=\"f_c\"><div class=\"c floatwrap\" style=\"height:".$this->height_mini.";\">' + msg + '</div></div>';var t = '".lang('plugin/dsu_marcocopyright', 'copyright')."' ;showDialog(c, 'info', t);}</script><a class=\"copyright\" style=\"CURSOR:pointer\" onclick=\"mini_copyright('$this->text_mini', this.href);return false;\">".lang('plugin/dsu_marcocopyright', 'copyright')."</a>");
		}else{
			return array();
		}
	}
	function viewthread_postbottom_output(){
		global $_G;
		if($this->forum_open && $this->place == 1 && !$this->open_mini && !in_array($_G['fid'], $this->forums) && $this->forums!='' && !empty($_G['forum_firstpid'])){
			if($this->pictureopen && $this->pic_copyright == 1){
				return array(0=>'<br /><div align="center">'.$this->fieldset_css.''.$this->legend_css.'<img src="source/plugin/dsu_marcocopyright/images/defaultpic.png"></fieldset></div><br />');
			}elseif($this->pictureopen && $this->pic_copyright == 2){
				return array(0=>'<br /><div align="center">'.$this->fieldset_css.''.$this->legend_css.'<img src="source/plugin/dsu_marcocopyright/images/defaultpic_black.png"></fieldset></div><br />');
			}elseif($this->pictureopen && (strpos($this->pic_copyright, 'http://') !== FALSE)){
				return array(0=>"<br /><div align=\"center\">{$this->fieldset_css}{$this->legend_css}</legend><img src={$this->pic_copyright} onerror=\"this.src=('source/plugin/dsu_marcocopyright/images/defaultpic.png')\"></fieldset></div><br />");
			}else{
				return array(0=>'<br />'.$this->fieldset_css.''.$this->legend_css.''.$this->line.'</fieldset><br />');
			}
		}else{
			return array();
		}
	}
	function viewthread_useraction_output(){
		global $_G;
		if($this->copy_link == 1){
			if($this->copy_link_style == "{default}"){
				$return .="<div style=\"padding:5px; text-align:center; margin-top:10px;\"><b><font color=#00A2D2>".lang('plugin/dsu_marcocopyright', 'copy_link')."</font></b><input type=\"text\" value=\"".$this->post_link."\" size=\"40\" class=\"px\" readonly=\"readonly\" style=\"vertical-align:middle;\">&nbsp;<button type=\"submit\" class=\"pn\" onclick=\"setCopy('".$this->post_subject."\\n".$this->post_link."', '".lang('plugin/dsu_marcocopyright', 'copy_link_done')."')\"><em>".lang('plugin/dsu_marcocopyright', 'copy_link_words')."</em></button></div>";
			}else{
				$return .= "<div align=\"center\"><img src=\"".$this->copy_link_style."\" style=\"CURSOR:pointer\" onclick=\"setCopy('".$this->post_subject."\\n".$this->post_link."', '".lang('plugin/dsu_marcocopyright', 'copy_link_done')."');\"></div>";
			}
		}else{
			$return .= "";
		}
		
		if($this->forum_open && $this->place == 2 && !$this->open_mini && !in_array($_G['fid'], $this->forums) && $this->forums!=''){
			if($this->pictureopen && $this->pic_copyright == 1){
				$return .= '<br /><div align="center">'.$this->fieldset_css.''.$this->legend_css.'<img src="source/plugin/dsu_marcocopyright/images/defaultpic.png"></fieldset></div><br />';
			}elseif($this->pictureopen && $this->pic_copyright == 2){
				$return .= '<br /><div align="center">'.$this->fieldset_css.''.$this->legend_css.'<img src="source/plugin/dsu_marcocopyright/images/defaultpic_black.png"></fieldset></div><br />';
			}elseif($this->pictureopen && (strpos($this->pic_copyright, 'http://') !== FALSE)){
				$return .= "<br /><div align=center>{$this->fieldset_css}{$this->legend_css}</legend><img src={$this->pic_copyright} onerror=\"this.src=('source/plugin/dsu_marcocopyright/images/defaultpic.png')\"></fieldset></div><br />";
			}else{
				$return .= '<br />'.$this->fieldset_css.''.$this->legend_css.''.$this->line.'</fieldset><br />';
			}
		}else{
			$return .= "";
		}
		return '</div><div>'.$return.'';
	}
}
class plugin_dsu_marcocopyright_group extends plugin_dsu_marcocopyright{
	function viewthread_postfooter_output(){
		global $_G;
		if($this->group_open && $this->open_mini && !empty($_G['forum_firstpid'])){
			return array(0=>"<style type=\"text/css\">.copyright {background: transparent url('source/plugin/dsu_marcocopyright/images/copyright.png') no-repeat 0 50%; }</style><script type=text/javascript>function mini_copyright(msg, script){script = !script ? '' : script;var c = '<div class=\"f_c\"><div class=\"c floatwrap\" style=\"height:".$this->height_mini.";\">' + msg + '</div></div>';var t = '".lang('plugin/dsu_marcocopyright', 'copyright')."' ;showDialog(c, 'info', t);}</script><a class=\"copyright\" style=\"CURSOR:pointer\" onclick=\"mini_copyright('$this->text_mini', this.href);return false;\">".lang('plugin/dsu_marcocopyright', 'copyright')."</a>");
		}else{
			return array();
		}
	}
	function viewthread_postbottom_output(){
		global $_G;
		if($this->group_open && $this->place == 1 && !$this->open_mini && !empty($_G['forum_firstpid'])){
			if($this->pictureopen && $this->pic_copyright == 1){
				return array(0=>'<br /><div align="center">'.$this->fieldset_css.''.$this->legend_css.'<img src="source/plugin/dsu_marcocopyright/images/defaultpic.png"></fieldset></div><br />');
			}elseif($this->pictureopen && $this->pic_copyright == 2){
				return array(0=>'<br /><div align="center">'.$this->fieldset_css.''.$this->legend_css.'<img src="source/plugin/dsu_marcocopyright/images/defaultpic_black.png"></fieldset></div><br />');
			}elseif($this->pictureopen && (strpos($this->pic_copyright, 'http://') !== FALSE)){
				return array(0=>"<br /><div align=\"center\">{$this->fieldset_css}{$this->legend_css}</legend><img src={$this->pic_copyright} onerror=\"this.src=('source/plugin/dsu_marcocopyright/images/defaultpic.png')\"></fieldset></div><br />");
			}else{
				return array(0=>'<br />'.$this->fieldset_css.''.$this->legend_css.''.$this->line.'</fieldset><br />');
			}
		}else{
			return array();
		}
	}
	function viewthread_useraction_output(){
		global $_G;
		if($this->copy_link == 1){
			if($this->copy_link_style == "{default}"){
				$return .="<div style=\"padding:5px; text-align:center; margin-top:10px;\"><b><font color=#00A2D2>".lang('plugin/dsu_marcocopyright', 'copy_link')."</font></b><input type=\"text\" value=\"".$this->post_link."\" size=\"40\" class=\"px\" readonly=\"readonly\" style=\"vertical-align:middle;\">&nbsp;<button type=\"submit\" class=\"pn\" onclick=\"setCopy('".$this->post_subject."\\n".$this->post_link."', '".lang('plugin/dsu_marcocopyright', 'copy_link_done')."')\"><em>".lang('plugin/dsu_marcocopyright', 'copy_link_words')."</em></button></div>";
			}else{
				$return .= "<div align=\"center\"><img src=\"".$this->copy_link_style."\" style=\"CURSOR:pointer\" onclick=\"setCopy('".$this->post_subject."\\n".$this->post_link."', '".lang('plugin/dsu_marcocopyright', 'copy_link_done')."');\"></div>";
			}
		}else{
			$return .= "";
		}
		
		if($this->group_open && $this->place == 2 && !$this->open_mini){
			if($this->pictureopen && $this->pic_copyright == 1){
				$return .= '<br /><div align="center">'.$this->fieldset_css.''.$this->legend_css.'<img src="source/plugin/dsu_marcocopyright/images/defaultpic.png"></fieldset></div><br />';
			}elseif($this->pictureopen && $this->pic_copyright == 2){
				$return .= '<br /><div align="center">'.$this->fieldset_css.''.$this->legend_css.'<img src="source/plugin/dsu_marcocopyright/images/defaultpic_black.png"></fieldset></div><br />';
			}elseif($this->pictureopen && (strpos($this->pic_copyright, 'http://') !== FALSE)){
				$return .= "<br /><div align=center>{$this->fieldset_css}{$this->legend_css}</legend><img src={$this->pic_copyright} onerror=\"this.src=('source/plugin/dsu_marcocopyright/images/defaultpic.png')\"></fieldset></div><br />";
			}else{
				$return .= '<br />'.$this->fieldset_css.''.$this->legend_css.''.$this->line.'</fieldset><br />';
			}
		}else{
			$return .= "";
		}
		return '</div><div>'.$return.'';
	}
}
?>
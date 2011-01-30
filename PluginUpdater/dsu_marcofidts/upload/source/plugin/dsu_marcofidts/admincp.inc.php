<?php
/*
	[DSU] Fid Terms
	Author: Marco129[http://my3talk.com]
	Please respect the author, do not delete the copyright!
*/
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($_G['mod'])){
	showtableheader("".lang("plugin/dsu_marcofidts","ts_list")."&nbsp;<button type='button' onclick='location.href=\"?action=plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp&mod=add\"';' style='float:right'>".lang("plugin/dsu_marcofidts","add_ts")."</button>");
	showsubtitle(array('',''.lang("plugin/dsu_marcofidts","fid").'',''.lang("plugin/dsu_marcofidts","ts_content").'',''.lang("plugin/dsu_marcofidts","keep").'','lastmodified','edit','delete'));
	$query = DB::query("SELECT * FROM ".DB::table('dsu_marcofidts'));
	while($info = DB::fetch($query)) {
		$info['content'] = dhtmlspecialchars(cutstr($info['content'],50));
		$info['update_time'] = dgmdate($info['update_time'], 'dt', $_G['setting']['timeoffset']);
		showtablerow('','',array('',"<a href='forum.php?mod=forumdisplay&fid=$info[fid]' target='_blank'>$info[fid]</a>",$info['content'],$info['keep'],$info['update_time'],"<a href='?action=plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp&mod=edit&fid=$info[fid]'>".cplang('edit')."</a>","<a href='?action=plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp&mod=del&fid=$info[fid]'>".cplang('delete')."</a>"));
	}
	showtablefooter();

}elseif($_G['mod'] == 'groups' && $_G['gp_frame'] == 'no'){
	echo '<div class="container"><pre class="colorbox"><h1>'.lang("plugin/dsu_marcofidts","groups_id").'</h1><br />';
	$query = DB::query("SELECT * FROM ".DB::table('common_usergroup'));
	while($info = DB::fetch($query)) {
		echo "$info[groupid] - $info[grouptitle]<br />";
	}
	echo '</pre></div>';

}elseif($_G['mod'] == 'add'){
	if(!submitcheck('submit_add')){
		showformheader('plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp&mod=add');
		showtableheader("".lang("plugin/dsu_marcofidts","add_ts")."");
		showsetting(''.lang("plugin/dsu_marcofidts","fid").'', 'fid', '', 'number', '', '', ''.lang("plugin/dsu_marcofidts","setting_intro_fid").'');
		showsetting(''.lang("plugin/dsu_marcofidts","ts_content").'', 'content', '', 'textarea', '', '', ''.lang("plugin/dsu_marcofidts","setting_intro_content").'');
		showsetting(''.lang("plugin/dsu_marcofidts","keep").'', 'keep', '3600', 'number', '', '', ''.lang("plugin/dsu_marcofidts","setting_intro_keep").'');
		showsetting(''.lang("plugin/dsu_marcofidts","groups").'', 'groups', '', 'text', '', '', ''.lang("plugin/dsu_marcofidts","setting_intro_groups").'');
		showsubmit('submit_add', 'submit', '', "<input type='button' class='btn' value='".cplang('return')."' onclick='location.href=\"?action=plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp\"';'>", '');
		showtablefooter();
		showformfooter();
	}
	if(submitcheck('submit_add')){
		if(!$_G['gp_fid'] || !$_G['gp_content'] || !$_G['gp_keep'] || !$_G['gp_groups']){
			cpmsg_error('dsu_marcofidts:error_1');
		}
		$check=DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcofidts')." WHERE fid='$_G[gp_fid]'");
		if($check){
			cpmsg_error('dsu_marcofidts:error_2');
		}
		DB::insert("dsu_marcofidts", array("fid"=>intval($_G['gp_fid']),"content"=>$_G['gp_content'],"keep"=>intval($_G['gp_keep']),"groups"=>$_G['gp_groups'],"update_time"=>$_G['timestamp']));
		cpmsg('dsu_marcofidts:succeed_1','action=plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp','succeed');
	}
  
}elseif($_G['mod'] == 'edit' && $_G['gp_fid']){
	if(!submitcheck('submit_edit')){
		$check=DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcofidts')." WHERE fid='$_G[gp_fid]'");
		if(!$check){
			cpmsg_error('dsu_marcofidts:error_3','action=plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp');
		}
		$query = DB::query("SELECT * FROM ".DB::table('dsu_marcofidts')." WHERE fid='$_G[gp_fid]'");
		while($info = DB::fetch($query)) {
			showformheader('plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp&mod=edit&fid='.$_G['gp_fid'].'');
			showtableheader("".lang("plugin/dsu_marcofidts","edit_ts")."");
			showsetting(''.lang("plugin/dsu_marcofidts","fid").'', 'fid', $info['fid'], 'number', 1, '', '');
			showsetting(''.lang("plugin/dsu_marcofidts","ts_content").'', 'content', $info['content'], 'textarea', '', '', ''.lang("plugin/dsu_marcofidts","setting_intro_content").'');
			showsetting(''.lang("plugin/dsu_marcofidts","keep").'', 'keep', $info['keep'], 'number', '', '', ''.lang("plugin/dsu_marcofidts","setting_intro_keep").'');
			showsetting(''.lang("plugin/dsu_marcofidts","groups").'', 'groups', $info['groups'], 'text', '', '', ''.lang("plugin/dsu_marcofidts","setting_intro_groups").'');
			showsubmit('submit_edit', 'submit', '', "<input type='button' class='btn' value='".cplang('return')."' onclick='location.href=\"?action=plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp\"';'>", '');
			showtablefooter();
			showformfooter();
		}
	}
	if(submitcheck('submit_edit')){
		if(!$_G['gp_fid'] || !$_G['gp_content'] || !$_G['gp_keep'] || !$_G['gp_groups']){
			cpmsg_error('dsu_marcofidts:error_1');
		}
		DB::query("UPDATE ".DB::table('dsu_marcofidts')." SET content='$_G[gp_content]',keep='".intval($_G['gp_keep'])."',groups='$_G[gp_groups]',update_time='$_G[timestamp]' WHERE fid='".intval($_G['gp_fid'])."'");
		cpmsg('dsu_marcofidts:succeed_2','action=plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp','succeed');
	}
  
}elseif($_G['mod'] == 'del' && $_G['gp_fid']){
	  $check=DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcofidts')." WHERE fid='{$_G[gp_fid]}'");
	  if(!$check){
		  cpmsg_error('dsu_marcofidts:error_3','action=plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp');
	  }else{
		  if($_G['gp_do'] == 1){
			  DB::query("DELETE FROM ".DB::table('dsu_marcofidts')." WHERE fid='".intval($_G['gp_fid'])."'");
			  cpmsg('dsu_marcofidts:succeed_3','action=plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp','succeed');
		  }else{
			  cpmsg('dsu_marcofidts:action_1','action=plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp&mod=del&fid='.intval($_G['gp_fid']).'&do=1','form');
		  }
	  }

}else{
	cpmsg_error('dsu_marcofidts:error_3','action=plugins&operation=config&identifier=dsu_marcofidts&pmod=admincp');
}
?>
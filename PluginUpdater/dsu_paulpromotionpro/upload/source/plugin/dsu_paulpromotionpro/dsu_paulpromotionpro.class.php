<?php
class plugin_dsu_paulpromotionpro_member{
	function register_dsu_paulpromotionpro_output(){
		global $_G;
			if($_G['cookie']['promotion']){
				$cp = trim($_G['cookie']['promotion']);
				$checkdb = DB::fetch_first("SELECT touid FROM ".DB::table('dsu_paulpromotionprorc')." WHERE touid='$cp' and ip='$_G[clientip]'");
				if(!$checkdb)DB::query("INSERT INTO ".DB::table('dsu_paulpromotionprorc')." (cid,touid,ip) VALUES ('NULL','$cp','$_G[clientip]')");
			}else{
				$checkreg = DB::fetch_first("SELECT touid FROM ".DB::table('dsu_paulpromotionprorc')." WHERE ip='$_G[clientip]'");
				if($_G['uid'] && $checkreg['touid']){
				$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_paulpromotionpro')." where ip='$_G[clientip]'");
				if($num == '0'){
					DB::query("INSERT INTO ".DB::table('dsu_paulpromotionpro')." (uid,fromuid,ip) VALUES ('$_G[uid]','$checkreg[touid]','$_G[clientip]')");
					$promotiondb = DB::fetch_first("SELECT uid FROM ".DB::table('dsu_paulpromotionprostats')." WHERE uid='$checkreg[touid]'");
					DB::query("INSERT INTO ".DB::table('dsu_paulpromotionprostats')." (uid,allnum) VALUES ('$_G[uid]','0')");
					if(!$promotiondb['uid']) {
						DB::query("INSERT INTO ".DB::table('dsu_paulpromotionprostats')." (uid,allnum) VALUES ('$checkreg[touid]','1')");
					}else{
						DB::query("UPDATE ".DB::table('dsu_paulpromotionprostats')." set allnum=allnum+1 where uid='$checkreg[touid]'");
					}
					DB::query("INSERT INTO ".DB::table('home_friend')." (uid,fuid,fusername,gid,dateline) VALUES ('$checkreg[touid]','$_G[uid]','$_G[username]','0','$_G[timestamp]')");
					DB::query("INSERT INTO ".DB::table('home_friendlog')." (uid,fuid,dateline,action) VALUES ('$_G[uid]','$checkreg[touid]','$_G[timestamp]','add')");
					$subject = lang('plugin/dsu_paulpromotionpro', 'class_01');
					$msg = lang('plugin/dsu_paulpromotionpro', 'class_02', array('username' => "{$_G[username]}"));
					sendpm($checkreg['touid'], $subject, $msg, 0);
				}
				DB::delete('dsu_paulpromotionprorc',"touid = '$checkreg[touid]' AND ip='$_G[clientip]'");
				}
			}
		return '';
	}
}
class plugin_dsu_paulpromotionpro_forum {
	function viewthread_postbottom_output(){
		global $_G,$navtitle,$postlist;
        $open = $_G['cache']['plugin']['dsu_paulpromotionpro']['tidopens'];
		$authorid_pd = $postlist[$_G["forum_firstpid"]]["authorid"];
		if($_G['uid']){
			if(in_array('all_script', $_G['setting']['rewritestatus'])) {
				$tglink = '<div style="border:#CAD9EA solid 0px; padding:5px; text-align:center; margin-top:10px;"><p class="mtm pns"><b><font color=gray>'.lang('plugin/dsu_paulpromotionpro', 'addnew_03').'</font></b><input type="text" onclick="this.select();setCopy(\''.urldecode($_G['forum_thread']['subjectenc']).'\n'.$_G['siteurl'].'forum-viewthread-tid-'.$_G['tid'].'-fromuid-'.$_G['uid'].'.html\', \''.lang('plugin/dsu_paulpromotionpro', 'addnew_01').'\');" value="'.$_G['siteurl'].'forum-viewthread-tid-'.$_G['tid'].'-fromuid-'.$_G['uid'].'.html" size="40" class="px" style="vertical-align:middle;" />&nbsp;<button type="submit" class="pn" onclick="setCopy(\''.urldecode($_G['forum_thread']['subjectenc']).'\n'.$_G['siteurl'].'forum-viewthread-tid-'.$_G['tid'].'-fromuid-'.$_G['uid'].'.html\', \''.lang('plugin/dsu_paulpromotionpro', 'addnew_01').'\')"><em>'.lang('plugin/dsu_paulpromotionpro', 'addnew_02').'</em></button></p></div>';
			}else{
				$tglink = '<div style="border:#CAD9EA solid 0px; padding:5px; text-align:center; margin-top:10px;"><p class="mtm pns"><b><font color=gray>'.lang('plugin/dsu_paulpromotionpro', 'addnew_03').'</font></b><input type="text" onclick="this.select();setCopy(\''.urldecode($_G['forum_thread']['subjectenc']).'\n'.$_G['siteurl'].'forum.php?mod=viewthread&tid='.$_G['tid'].'&fromuid='.$_G['uid'].'\', \''.lang('plugin/dsu_paulpromotionpro', 'addnew_01').'\');" value="'.$_G['siteurl'].'forum.php?mod=viewthread&tid='.$_G['tid'].'&fromuid='.$_G['uid'].'" size="40" class="px" style="vertical-align:middle;" />&nbsp;<button type="submit" class="pn" onclick="setCopy(\''.urldecode($_G['forum_thread']['subjectenc']).'\n'.$_G['siteurl'].'forum.php?mod=viewthread&tid='.$_G['tid'].'&fromuid='.$_G['uid'].'\', \''.lang('plugin/dsu_paulpromotionpro', 'addnew_01').'\')"><em>'.lang('plugin/dsu_paulpromotionpro', 'addnew_02').'</em></button></p></div>';
			}
		}else{
			if(in_array('forum_forumdisplay', $_G['setting']['rewritestatus'])) {
				$tglink = '<div style="border:#CAD9EA solid 0px; padding:5px; text-align:center; margin-top:10px;"><p class="mtm pns"><b><font color=gray>'.lang('plugin/dsu_paulpromotionpro', 'addnew_03').'</font></b><input type="text" onclick="this.select();setCopy(\''.urldecode($_G['forum_thread']['subjectenc']).'\n'.$_G['siteurl'].'thread-'.$_G['tid'].'-1-1.html\', \''.lang('plugin/dsu_paulpromotionpro', 'addnew_01').'\');" value="'.$_G['siteurl'].'thread-'.$_G['tid'].'-1-1.html" size="40" class="px" style="vertical-align:middle;" />&nbsp;<button type="submit" class="pn" onclick="setCopy(\''.urldecode($_G['forum_thread']['subjectenc']).'\n'.$_G['siteurl'].'thread-'.$_G['tid'].'-1-1.html\', \''.lang('plugin/dsu_paulpromotionpro', 'addnew_01').'\')"><em>'.lang('plugin/dsu_paulpromotionpro', 'addnew_02').'</em></button></p></div>';
			}else{
				$tglink = '<div style="border:#CAD9EA solid 0px; padding:5px; text-align:center; margin-top:10px;"><p class="mtm pns"><b><font color=gray>'.lang('plugin/dsu_paulpromotionpro', 'addnew_03').'</font></b><input onclick="this.select();setCopy(\''.urldecode($_G['forum_thread']['subjectenc']).'\n'.$_G['siteurl'].'forum.php?mod=viewthread&tid='.$_G['tid'].'\', \''.lang('plugin/dsu_paulpromotionpro', 'addnew_01').'\');" type="text" value="'.$_G['siteurl'].'forum.php?mod=viewthread&tid='.$_G['tid'].'" size="40" class="px" style="vertical-align:middle;" />&nbsp;<button type="submit" class="pn" onclick="setCopy(\''.urldecode($_G['forum_thread']['subjectenc']).'\n'.$_G['siteurl'].'forum.php?mod=viewthread&tid='.$_G['tid'].'\', \''.lang('plugin/dsu_paulpromotionpro', 'addnew_01').'\')"><em>'.lang('plugin/dsu_paulpromotionpro', 'addnew_02').'</em></button></p></div>';
			}
		}
        if(!$open || !$authorid_pd){
            return array();
        }else{
          return array(0=>$tglink);
        }
	}
}
?>
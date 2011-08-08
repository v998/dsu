<?php
!defined('IN_DISCUZ') && exit('Access Denied');
function dsu_paulissue_msg($msg, $treferer = '') {
	global $_G;
	$vars = explode(':', $msg);
	$msg = lang('plugin/'.$vars[0], $vars[1]);
	include template('dsu_paulissue:float');
	dexit();
}
if(!$_G['gp_to'] || !$_G['gp_tid'])dsu_paulissue_msg('dsu_paulissue:ts_1');
if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
$tid = intval($_G['gp_tid']);
$to = intval($_G['gp_to']);
@include_once DISCUZ_ROOT.'./data/cache/cache_paulissue_setting.php';
if(is_array($PACACHE)) {
	foreach($PACACHE['issuetypeid'] as $key => $item) {
		$dt[$key] = $item['dt'];
		$ot[$key] = $item['ot'];
	}
}
$tdb = DB::fetch_first("SELECT authorid,paulissue_status,fid FROM ".DB::table('forum_thread')." WHERE tid='$tid'");
if(!$tdb)dsu_paulissue_msg('dsu_paulissue:ts_2');
if($tdb['paulissue_status'] == '2')dsu_paulissue_msg('dsu_paulissue:ts_3');
$ismoderator = in_array($_G['adminid'], array(1, 2)) ? 1 : ($_G['adminid'] == 3 ? DB::result_first("SELECT uid FROM ".DB::table('forum_moderator')." m INNER JOIN ".DB::table('forum_thread')." t ON t.tid='$tid' AND t.fid=m.fid WHERE m.uid='$_G[uid]'") : 0);
if($to == '1'){
	if(!$ismoderator) dsu_paulissue_msg('dsu_paulissue:ts_4');
	if($tdb['paulissue_status'] == '1')dsu_paulissue_msg('dsu_paulissue:ts_5');
	$typeid = $dt[$tdb['fid']] ? $dt[$tdb['fid']] : '0';
	DB::update('forum_thread',array('paulissue_status' => '1','typeid' => $typeid),"tid = '$tid'");
	dsu_paulissue_msg('dsu_paulissue:ts_6',"forum.php?mod=viewthread&tid={$tid}");
}elseif($to == '2'){
	if(!$ismoderator && ($tdb['authorid'] !== $_G['uid'])) dsu_paulissue_msg('dsu_paulissue:ts_4');
	$typeid = $ot[$tdb['fid']] ? $ot[$tdb['fid']] : '0';
	if($_G['cache']['plugin']['dsu_paulissue']['autoclose']){
		DB::update('forum_thread',array('paulissue_status' => '2','typeid' => $typeid,'closed' => '1'),"tid = '$tid'");
		DB::query("INSERT INTO ".DB::table('forum_threadmod')." (tid, uid, username, dateline, action, expiration, status) VALUES ('$tid', '$_G[uid]', '$_G[username]', '$_G[timestamp]', 'CLS', '0', '1')");
	}else{
		DB::update('forum_thread',array('paulissue_status' => '2','typeid' => $typeid),"tid = '$tid'");
	}
	dsu_paulissue_msg('dsu_paulissue:ts_6',"forum.php?mod=viewthread&tid={$tid}");
}elseif($to == '3'){
	if(!$ismoderator) dsu_paulissue_msg('dsu_paulissue:ts_4');
	DB::update('forum_thread',array('paulissue_hide' => '1'),"tid = '$tid'");
	dsu_paulissue_msg('dsu_paulissue:ts_6',"forum.php?mod=viewthread&tid={$tid}");
}elseif($to == '4'){
	if(!$ismoderator) dsu_paulissue_msg('dsu_paulissue:ts_4');
	DB::update('forum_thread',array('paulissue_hide' => '0'),"tid = '$tid'");
	dsu_paulissue_msg('dsu_paulissue:ts_6',"forum.php?mod=viewthread&tid={$tid}");
}
?>
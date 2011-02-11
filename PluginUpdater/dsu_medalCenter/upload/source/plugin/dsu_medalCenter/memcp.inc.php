<?php
/*
	dsu_medalCenter (C)2010 Discuz Student Union
	This is NOT a freeware, use is subject to license terms

	$Id: memcp.inc.php 27 2011-01-08 19:51:17Z chuzhaowei@gmail.com $
*/
!defined('IN_DISCUZ') && exit('Access Denied');

loadcache('plugin');
$cvars = &$_G['cache']['plugin']['dsu_medalCenter'];
$thisurl = 'plugin.php?id=dsu_medalCenter:memcp';
require_once DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/function_common.php';
include_once DISCUZ_ROOT.'./source/language/lang_template.php';
@include_once lang('medal');
$navtitle = lang('plugin/dsu_medalCenter','1');
//lang('plugin/dsu_amupper','special_reward',array('rewards' => $return['reward'],'extcredits' => $return['extcredits']));
$page = max(1, intval($_G['gp_page']));
$tpp = 8;
$start_limit = ($page - 1) * $tpp;


if(empty($_G['gp_action']) || $_G['gp_action'] == 'list'){
	//获取分类信息
	$query = DB::query("SELECT * FROM ".DB::table('dsu_medaltype')." ORDER BY displayorder");
	$typeArr = array();
	while($typeinfo = DB::fetch($query)){
		$typeArr[] = $typeinfo;
	}

	$typeid = intval($_G['gp_typeid']);
	$thisurl .= $typeid > 0 ? "&typeid=$typeid" : '';
	
	$mymedal = getMedalByUid();

	$sqladd = '';
	$sqladd = " and available='1'";
	$sqladd .= $typeid > 0 ? " and mf.typeid = '$typeid'" : ''; //是否限制分类
	$sqladd .= $_G['cookie']['dsu_medalCenter_hidemymedal'] ? " and m.medalid NOT IN ('".implode("','", $mymedal)."')" : ''; //是否隐藏自己拥有的勋章
	
	$num = DB::result_first("SELECT count(*) FROM ".DB::table('forum_medal')." m LEFT JOIN ".DB::table('dsu_medalfield')." mf USING(medalid) WHERE 1 $sqladd");
	$multipage = multi($num, $tpp, $page, $thisurl);
	
	$query = DB::query("SELECT mf.*, m.* FROM ".DB::table('forum_medal')." m LEFT JOIN ".DB::table('dsu_medalfield')." mf USING(medalid) WHERE 1 $sqladd ORDER BY m.displayorder LIMIT ".$start_limit." ,".$tpp);
	$medallist = array();
	while($medal = DB::fetch($query)){
		$medalfieldSetting = (array)unserialize($medal['setting']);
		$medal['limit'] = '';
		$medal['owned'] = in_array($medal['medalid'], $mymedal); 
		if($medal['type'] >= 1 && !$medal['owned']){ //只有当勋章允许申请或领取并且自己没有此勋章时显示要求
			$medalid = $medal['medalid'];
			foreach(getMedalExtendClass() as $classname => $newclass){
				if(method_exists($newclass, 'memcp_show')){
					$_limit = $newclass->memcp_show($medalfieldSetting[$classname]);
					if($_limit) $medal['limit'] .= $_limit."<br />";//"<p>$_limit</p>";
				}
			}
		}
		$medallist[$medal['medalid']] = $medal;
	}
}else if($_G['gp_action'] == 'mymedal'){
	$thisurl .= '&action=mymedal';
	$usermedalArr = getMedalByUid($_G['uid'], true);
	$mymedals = array();
	if($_G['gp_op'] == 'sethide' && $_G['gp_myMedalHide']){
		$myMedalHide = (array)$_G['gp_myMedalHide'];
		foreach($myMedalHide as $medalid => $value){
			if(($value == 1 || $value == 2) && isset($usermedalArr[$medalid])){
				$medalExpiration = $usermedalArr[$medalid];
				$medalExpiration = max(abs($medalExpiration), 1);
				$medalExpiration = $value == 1 ? -$medalExpiration : $medalExpiration;
				$usermedalArr[$medalid] = $medalExpiration == 1 ? 0 : $medalExpiration;
			}
		}
		
		$common = $newmedal = '';
		foreach($usermedalArr as $medalid => $expiration){
			$newmedal .= $common.$medalid;
			$newmedal .= $expiration != 0 ? '|'.$expiration : '';
			$common = "\t";
		}
		if($newmedal)
			DB::update('common_member_field_forum',array('medals'=>$newmedal),array('uid'=>$_G['uid']));
	}
	if($usermedalArr){
		$query = DB::query("SELECT * FROM ".DB::table('forum_medal')." WHERE medalid IN('".implode("','", array_keys($usermedalArr))."') and available='1'");
		while($medal = DB::fetch($query)){
			$medal['expiration'] = $usermedalArr[$medal['medalid']];
			$medal['hide'] = $medal['expiration'] < 0 ? 1 : 2;
			$mymedals[$medal['medalid']] = $medal;
		}
	}
	$num = count($mymedals);
	
	$medallognum = DB::result_first("SELECT COUNT(*) FROM ".DB::table('forum_medallog')." WHERE uid='$_G[uid]' AND type<'2'");
	$multipage = multi($medallognum, $tpp, $page, $thisurl);

	$query = DB::query("SELECT me.*, m.image, m.name FROM ".DB::table('forum_medallog')." me
			LEFT JOIN ".DB::table('forum_medal')." m USING (medalid)
			WHERE me.uid='$_G[uid]' ORDER BY me.dateline DESC LIMIT $start_limit,$tpp");
	$medallog = array();
	while($medallog = DB::fetch($query)) {
		$medallog['dateline'] = dgmdate($medallog['dateline']);
		$medallog['expiration'] = !empty($medallog['expiration']) ? dgmdate($medallog['expiration'], 'd') : '';
		$medallogs[] = $medallog;
	}
}else if($_G['gp_action'] == 'apply'){ //领取或申请勋章
	$medalid = intval($_G['gp_medalid']);
	$medal = DB::fetch_first("SELECT m.*, mf.* FROM ".DB::table('forum_medal')." m LEFT JOIN ".DB::table('dsu_medalfield')." mf USING(medalid) WHERE m.medalid='$medalid'");

	if(empty($medal) || !$medal['available']) {
		showmessage(lang('plugin/dsu_medalCenter','2'));
	}

	if(!$medal['type']) {
		showmessage('medal_apply_invalid');
	}
	//检查是否已经领取过此勋章
	$medaldetail = DB::fetch_first("SELECT medalid FROM ".DB::table('forum_medallog')." WHERE uid='$_G[uid]' AND medalid='$medalid' AND type<'3'");
	if($medaldetail['medalid']) {
		showmessage('medal_apply_existence', $thisurl);
	}
	
	$applysucceed = TRUE;
	$medalfieldSetting = (array)unserialize($medal['setting']);
	foreach(getMedalExtendClass() as $classname => $newclass){
		if($applysucceed && method_exists($newclass, 'memcp_check')) $applysucceed = $newclass->memcp_check($medalfieldSetting[$classname]);
	}
	if($applysucceed) {
		if($medal['type'] == 1 || $medal['type'] == 5) {
			$usermedal = implode("\t", getMedalByUid($_G['uid']));
			$medalnew = $usermedal ? $usermedal."\t".$medalid : $medalid;
			DB::query("UPDATE ".DB::table('common_member_field_forum')." SET medals='$medalnew' WHERE uid='$_G[uid]'");
			foreach(getMedalExtendClass() as $classname => $newclass){
				if(method_exists($newclass, 'memcp_get_succeed')) $newclass->memcp_get_succeed($medalfieldSetting[$classname]);
			}
			$medalmessage = 'medal_get_succeed';
		} else {
			foreach(getMedalExtendClass() as $classname => $newclass){
			//	if(method_exists($newclass, 'memcp_apply_succeed')) $newclass->memcp_apply_succeed($medalfieldSetting[$classname]);
			}
			$medalmessage = 'medal_apply_succeed';
		}
		$expiration = empty($medal['expiration'])? 0 : TIMESTAMP + $medal['expiration'] * 86400;
		DB::query("INSERT INTO ".DB::table('forum_medallog')." (uid, medalid, type, dateline, expiration, status) VALUES ('$_G[uid]', '$medalid', '$medal[type]', '$_G[timestamp]', '$expiration', '0')");
		showmessage($medalmessage, $thisurl, array('medalname' => $medal['name']));
	}else{
		showmessage(lang('plugin/dsu_medalCenter','3'));
	}
}
	
	

include template('dsu_medalCenter:memcp');
?>
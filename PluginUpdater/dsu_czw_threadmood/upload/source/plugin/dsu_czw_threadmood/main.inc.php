<?php
/*
	dsu_czw_threadmood (C)2007-2010 jhdxr
	This is NOT a freeware, use is subject to license terms

	$Id: main.inc.php  jhdxr 2010-10-03 10:45$
*/
!defined('IN_DISCUZ') && exit('Access Denied');

empty($_G['cache']['plugin']) && loadcache('plugin'); //获取插件设置
$cvars = &$_G['cache']['plugin']['dsu_czw_threadmood'];
$cvars['fids'] = (array)unserialize($cvars['fids']);
$cvars['groupids'] = (array)unserialize($cvars['groupids']);

!$_G['uid'] && $_GET['op'] != 'showall' && showmessage('not_loggedin', NULL, array(), array('login' => 1));

$clickid = empty($_GET['clickid']) ? 0 : intval($_GET['clickid']); //处理参数
$idtype = 'czw_threadmood';
$id = empty($_GET['myid']) ? 0 : intval($_GET['myid']);

loadcache('click');
$clicks = empty($_G['cache']['click'][$idtype])?array():$_G['cache']['click'][$idtype];
$click = $clicks[$clickid];

if($_GET['op'] != 'showall') {
	if(empty($click) ) {
		showmessage('click_error');
	}
}
$sql = "SELECT t.*, t.authorid as uid, tf.* FROM ".DB::table('forum_thread')." t LEFT JOIN
			".DB::table('czw_threadfield')." tf ON tf.tid = t.tid
			WHERE t.tid='$id'";
$tablename = DB::table('czw_threadfield');
$query = DB::query($sql);
if(!$item = DB::fetch($query)) {
	showmessage('click_item_error');
}elseif(!in_array($item['fid'],$cvars['fids'])) {
	showmessage('undefined_action');
}

$hash = md5($item['uid']."\t".$item['dateline']);
if($_GET['op'] == 'add') {
	if($_GET['hash'] != $hash) {
		showmessage('no_privilege');
	}

	if($item['uid'] == $_G['uid']) {
		//showmessage('click_no_self');
	}

	if(isblacklist($item['uid'])) {
		showmessage('is_blacklist');
	}

	$query = DB::query("SELECT * FROM ".DB::table('home_clickuser')." WHERE uid='$_G[uid]' AND id='$id' AND idtype='$idtype'");
	if($value = DB::fetch($query)) {
		showmessage('click_have');
	}

	DB::query("UPDATE $tablename SET threadmood{$clickid}=threadmood{$clickid}+1 WHERE tid='$id'");
	if(DB::affected_rows() < 1){
		DB::insert('czw_threadfield',array('tid' => $id));
		DB::query("UPDATE $tablename SET threadmood{$clickid}=threadmood{$clickid}+1 WHERE tid='$id'");
	}

	$setarr = array(
		'uid' => $_G['uid'],
		'username' => $_G['username'],
		'id' => $id,
		'idtype' => $idtype,
		'clickid' => $clickid,
		'dateline' => $_G['timestamp']
	);
	DB::insert('home_clickuser', $setarr);
	
	//hot_update($idtype, $id, $item['hotuser']);
	showmessage('click_success', '', array('idtype' => $idtype, 'id' => $id, 'clickid' => $clickid), array('msgtype' => 3, 'showmsg' => true, 'closetime' => true));

} elseif ($_GET['op'] == 'show' || $_GET['op'] == 'showall') {
	$id = $id==0 && $_GET['op'] == 'showall' ? $tid : $id;

	$maxclicknum = 0;
	foreach ($clicks as $key => $value) {
		$value['clicknum'] = $item["threadmood{$key}"];
		$value['classid'] = mt_rand(1, 4);
		if($value['clicknum'] > $maxclicknum) $maxclicknum = $value['clicknum'];
		$clicks[$key] = $value;
	}

	$perpage = 18;
	$page = intval($_GET['page']);
	$start = ($page-1)*$perpage;
	if($start < 0) $start = 0;

	$count = getcount('home_clickuser', array('id'=>$id, 'idtype'=>$idtype));
	$clickuserlist = array();
	$click_multi = '';

	if($count) {
		$query = DB::query("SELECT * FROM ".DB::table('home_clickuser')."
			WHERE id='$id' AND idtype='$idtype'
			ORDER BY dateline DESC
			LIMIT $start,$perpage");
		while ($value = DB::fetch($query)) {
			$value['clickname'] = $clicks[$value['clickid']]['name'];
			$clickuserlist[] = $value;
			$count++;
		}

		$click_multi = multi($count, $perpage, $page, "plugin.php?id=dsu_czw_threadmood:main&ac=click&op=show&clickid=$clickid&idtype=$idtype&myid=$id");
	}
}
$clickuserlist = $click_multi = null; //disable the clickuserlist

include_once(template('dsu_czw_threadmood:show'));

function isblacklist($touid) {
	global $_G;
	return getcount('home_blacklist', array('uid'=>$touid, 'buid'=>$_G['uid']));
}
?>
<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
//入库数据的预处理

if($_G['uid']){
	$limit = 40;
	if($_G['gp_order'] == 'continuous'){
		$odmod = 'continuous';
		$order = 'continuous DESC , addup DESC ,lasttime';
		$url = "plugin.php?id=dsu_amupper:list&order=continuous";
	}else{
		$odmod = 'addup';
		$order = 'addup DESC , continuous DESC ,lasttime';
		$url = "plugin.php?id=dsu_amupper:list";
	}
	$today = dgmdate($_G['timestamp']);
	$yesterday = dgmdate($_G['timestamp']-86400,d);
	$ggprint=array();
	$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('plugin_dsuampper'));
	$page = max(1, intval($_G['gp_page']));
	$start_limit = ($page - 1) * $limit;
	$multipage = multi($num, $limit, $page, $url);

	$sql="SELECT * FROM ".DB::table('plugin_dsuampper')." ORDER BY ".$order." LIMIT ".$start_limit." ,".$limit;
	$querygg=DB::query($sql);
	$amtopid=$start_limit;

	$ammuid=array();$ammlist=array();$i = $start_limit + 1;

	while ($value=DB::fetch($querygg)){
		if($value['uid']){$pperuids[] = $value['uid'];}
	}
	$pperuid = implode(",", array_unique($pperuids));

	if($pperuid){
		$sql2="SELECT * FROM ".DB::table('common_member')." WHERE uid IN ({$pperuid})";
		$querygg2=DB::query($sql2);
		while ($value2=DB::fetch($querygg2)){
			$ppername[$value2['uid']]=cutstr($value2['username'],8,"...");
		}
	}	

	$sql="SELECT * FROM ".DB::table('plugin_dsuampper')." ORDER BY ".$order." LIMIT ".$start_limit." ,".$limit;
	$querygg=DB::query($sql);
	while ($value=DB::fetch($querygg)){
		$ammlist['uid'] = $value['uid'];
		$ammlist['lasttime'] = $value['lasttime'];
		$ammlist['continuous'] = $value['continuous'];
		$ammlist['addup'] = $value['addup'];
		$ammlist['username'] = $ppername[$value['uid']];
		$ammlist['index'] = $i;
		$ggprint[$i]=$ammlist;
		$i++;
	}
	$cdb_pper['uid'] = intval($_G['uid']);
	$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuampper")." WHERE uid = '{$cdb_pper['uid']}'");
	if($query){
		$myindex = DB::result_first("SELECT COUNT(*) from ".DB::table('plugin_dsuampper')." WHERE addup > '{$query['addup']}' OR ( addup = '{$query['addup']}' AND ( continuous > '{$query['continuous']}' OR ( continuous = '{$query['continuous']}' AND lasttime <= '{$query['lasttime']}')))");
		$mycontinuous = DB::result_first("SELECT COUNT(*) from ".DB::table('plugin_dsuampper')." WHERE continuous > '{$query['continuous']}' OR ( continuous = '{$query['continuous']}' AND ( addup > '{$query['addup']}' OR ( addup = '{$query['addup']}' AND lasttime <= '{$query['lasttime']}')))");
		if($odmod == 'continuous'){
			$mypospage = ceil($mycontinuous / $limit);
		}else{
			$mypospage = ceil($myindex / $limit);
		}
		//if($_G['uid']){echo '<BR><BR><BR>连续'.$mycontinuous;echo '|累计'.$myindex;}
	}
}else{
	showmessage('to_login', 'member.php?mod=logging&action=login', array(), array('showmsg' => true, 'login' => 1));
}
$navtitle = lang("plugin/dsu_amupper","title");
include template('dsu_amupper:list');

<?php
$veradd = 'http://www.dsu.cc/thread-27768-1-1.html';
$verBuild = 'Ver 2.0 Build T1004';
$config=$_G['cache']['plugin']['dsu_postfate'];
$setting_str=$config['setting'];
$setting_str=str_replace(array("\r\n","\r"),"\n",$setting_str);
$setting=explode("\n",$setting_str);
foreach ($setting as $value){
	$set_array[]=explode(",",$value);
}
if($_G['setting']['extcredits'][$config[credit]]['img']){
	$crn="&nbsp;".$_G['setting']['extcredits'][$config[credit]]['img']."&nbsp;".$_G['setting']['extcredits'][$config[credit]]['title'];
}else{
	$crn=$_G['setting']['extcredits'][$config[credit]]['title'];
}
$probably = $config['num'] * 100;
$probably = $probably.'%';
$credit_cn = $_G['setting']['extcredits'][$config[credit]]['title'];
if($_G['uid']){
	$stat_data = DB::fetch_first("SELECT * FROM ".DB::table('dsu_postfate_stat')." WHERE uid='$_G[uid]'");
	if(!$stat_data) DB::query("INSERT INTO ".DB::table('dsu_postfate_stat')." (uid,lucky,bad) VALUES ('$_G[uid]','0','0')");
}
if(($_G['gp_operation'] == 'my' && $_G['uid']) || $_G['gp_operation'] == ''){
	if($_G['gp_operation'] == 'my' && $_G['uid']){
		$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_postfate')." f LEFT JOIN ".DB::table('forum_post')." p on p.pid=f.pid WHERE p.authorid = '$_G[uid]'");
		$page = max(1, intval($_G['gp_page']));
		$start_limit = ($page - 1) * 10;
		$multipage = multi($num, 10, $page, "plugin.php?id=dsu_postfate:dsu_postfate&operation=my");
		$sql = "SELECT p.pid,p.dateline,p.tid,f.* FROM ".DB::table('dsu_postfate')." f LEFT JOIN ".DB::table('forum_post')." p on p.pid=f.pid WHERE p.authorid='$_G[uid]' ORDER BY p.pid desc LIMIT $start_limit, 10";
	} elseif($_G['gp_operation'] == ''){
		$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_postfate'));
		$page = max(1, intval($_G['gp_page']));
		$start_limit = ($page - 1) * 10;
		$multipage = multi($num, 10, $page, "plugin.php?id=dsu_postfate:dsu_postfate");
		$sql = "SELECT p.pid,p.dateline,p.tid,p.author,p.authorid,f.* FROM ".DB::table('dsu_postfate')." f LEFT JOIN ".DB::table('forum_post')." p on p.pid=f.pid ORDER BY p.pid desc LIMIT $start_limit, 10";
	}
	$query = DB::query($sql);
	$mrcs = array();
	while($mrc = DB::fetch($query)) {
		if(!$mrc['tid']) DB::delete('dsu_postfate',"pid = '$mrc[pid]'");
		$mrc['dateline'] = dgmdate($mrc['dateline'], 'n'.lang('plugin/dsu_postfate','dsu_postfate_inc_php_1'));
		$mrc['content']=$set_array[$mrc[types]][0];
		$mrc['content']=str_replace(array('{credit}','{username}','{creditname}','{creditunit}'),array(abs($mrc['num']),$mrc['username'],$crn,$_G['setting']['extcredits'][$config[credit]]['unit']),$mrc['content']);
		$mrcs[] = $mrc;
	}
} elseif($_G['gp_operation'] == 'list'){
	$luckymen = $badmen = array();
	$query = DB::query("SELECT s.uid,s.lucky,m.username FROM ".DB::table('dsu_postfate_stat')." s LEFT JOIN ".DB::table('common_member')." m on m.uid=s.uid ORDER BY s.lucky desc LIMIT 0, 10");
	while($lucky = DB::fetch($query)) {
		$lucky['link'] = '<a href="home.php?mod=space&uid='.$lucky['uid'].'">'.$lucky['username'].'</a>';
		$luckymen[] = $lucky;
	}
	$query = DB::query("SELECT s.uid,s.bad,m.username FROM ".DB::table('dsu_postfate_stat')." s LEFT JOIN ".DB::table('common_member')." m on m.uid=s.uid ORDER BY s.bad desc LIMIT 0, 10");
	while($bad = DB::fetch($query)) {
		$bad['link'] = '<a href="home.php?mod=space&uid='.$bad['uid'].'">'.$bad['username'].'</a>';
		$badmen[] = $bad;
	}

	for($i = 0; $i < 10; $i++) {
		$bgclass = $i % 2 ? '' : ' class="colplural"';
		$postfate_lists .= "<tr".$bgclass."><td class=\"stat_subject\">{$luckymen[$i]['link']}&nbsp;</td><td class=\"stat_num\">{$luckymen[$i]['lucky']}</td>\n".
			"<td class=\"stat_subject\">{$badmen[$i]['link']}<td class=\"stat_num\">{$badmen[$i]['bad']}</td></tr>\n";
	}
}
$navtitle = lang('plugin/dsu_postfate', 'hooks_class_php_1');
include template('dsu_postfate:dsu_postfate');
?>
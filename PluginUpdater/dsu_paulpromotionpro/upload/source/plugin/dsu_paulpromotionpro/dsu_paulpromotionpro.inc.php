<?
/*
	dsu_paulpromotionpro_VIEW By shy9000 @ DSU Team 2011-06-08
*/
$var = $_G['cache']['plugin']['dsu_paulpromotionpro'];
loadcache('pluginlanguage_script');
$lang = $_G['cache']['pluginlanguage_script']['dsu_paulpromotionpro'];
$ban = explode(",",$var['blackuid']);
if(empty($_G['uid'])) showmessage('to_login', 'member.php?mod=logging&action=login', array(), array('showmsg' => true, 'login' => 1));
if(!$var['open'] && $_G['adminid'] != 1) showmessage("{$lang['php_01']}", "index.php");
if(in_array($_G[uid],$ban)) showmessage("{$lang['php_02']}","index.php");
$dsu_paulpromotionpronum = DB::fetch_first("SELECT * FROM ".DB::table('dsu_paulpromotionprostats')." WHERE uid='$_G[uid]'");
$dsu_paulpromotionprodb = DB::fetch_first("SELECT * FROM ".DB::table('dsu_paulpromotionpro')." WHERE uid='$_G[uid]'");
$digest_thread = DB::fetch_first("SELECT tid,subject FROM ".DB::table('forum_thread')." WHERE digest IN (1,2,3) order by rand() limit 0,1");
$memberscr = DB::fetch_first("SELECT extcredits{$var[ticketfcr]} FROM ".DB::table('common_member_count')." WHERE uid='$_G[uid]'");
$memberscr = $memberscr[extcredits.$var[ticketfcr]];
if(!$dsu_paulpromotionpronum['uid']) {
	DB::query("INSERT INTO ".DB::table('dsu_paulpromotionprostats')." (uid,allnum) VALUES ('$_G[uid]','0')");
}
$alldsu_paulpromotionpronum = $dsu_paulpromotionpronum['allnum'];
$actdsu_paulpromotionpronum = $dsu_paulpromotionpronum['actnum'];
$ccactdsu_paulpromotionpronum = $dsu_paulpromotionpronum['actnum'] * $var['creditz'];
$ccactdsu_paulpromotionpronum2 = $dsu_paulpromotionpronum['actnum'] * $var['creditz2'];

if($_G['gp_operation'] == ''){
    $query = DB::query("SELECT p.uid,p.allnum,p.actnum,m.username FROM ".DB::table('dsu_paulpromotionprostats')." p, ".DB::table('common_member')." m WHERE p.allnum>0 and p.uid=m.uid ORDER BY p.allnum desc LIMIT 0, 15");
    $mrcs = array();
    while($mrc = DB::fetch($query)) {
		$mrc['cc'] = $var['creditz'] * $mrc['actnum'];
    	$mrcs[] = $mrc;
    }
} elseif ($_G['gp_operation'] == 'actnum') {
    $query = DB::query("SELECT p.uid,p.allnum,p.actnum,m.username FROM ".DB::table('dsu_paulpromotionprostats')." p, ".DB::table('common_member')." m WHERE p.actnum>0 and p.uid=m.uid ORDER BY p.actnum desc LIMIT 0, 15");
    $mrcs = array();
    while($mrc = DB::fetch($query)) {
		$mrc['cc'] = $var['creditz'] * $mrc['actnum'];
    	$mrcs[] = $mrc;
    }
} elseif ($_G['gp_operation'] == 'mynoactxx') {
	$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_paulpromotionpro')." where fromuid='$_G[uid]' and act='0'");
	$page = max(1, intval($_G['gp_page']));
	$start_limit = ($page - 1) * 10;
	$multipage = multi($num, 10, $page, "plugin.php?id=dsu_paulpromotionpro:dsu_paulpromotionpro&operation=mynoactxx");
    $query = DB::query("SELECT p.uid,p.fromuid,p.act,m.regdate,mc.posts,mc.oltime,m.username FROM ".DB::table('dsu_paulpromotionpro')." p left join ".DB::table('common_member')." m on p.uid=m.uid left join  ".DB::table('common_member_count')." mc on p.uid=mc.uid WHERE p.fromuid='$_G[uid]' and p.act='0' ORDER BY mc.posts desc LIMIT $start_limit, 10");
    $mrcs = array();
    while($mrc = DB::fetch($query)) {
		$mrc['regdate'] = intval(($_G['timestamp'] - $mrc['regdate'])/(3600*24));
		if ($mrc['regdate'] >= $var['regpass']) {
			$mrc['regdate'] = "<font color=blue>{$mrc['regdate']}</font>";
		} else {
			$mrc['regdate'] = "<font color=red>{$mrc['regdate']}</font>";
		}
		if ($mrc['posts'] >= $var['postpass']) {
			$mrc['posts'] = "<font color=blue>{$mrc['posts']}</font>";
		} else {
			$mrc['posts'] = "<font color=red>{$mrc['posts']}</font>";
		}
		if ($mrc['oltime'] >= $var['timepass']) {
			$mrc['oltime'] = "<font color=blue>{$mrc['oltime']}</font>";
		} else {
			$mrc['oltime'] = "<font color=red>{$mrc['oltime']}</font>";
		}
    	$mrcs[] = $mrc;
    }
} elseif ($_G['gp_operation'] == 'myactxx') {
	$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_paulpromotionpro')." where fromuid='$_G[uid]' and act='1'");
	$page = max(1, intval($_G['gp_page']));
	$start_limit = ($page - 1) * 10;
	$multipage = multi($num, 10, $page, "plugin.php?id=dsu_paulpromotionpro:dsu_paulpromotionpro&operation=myactxx");
    $query = DB::query("SELECT p.uid,p.fromuid,p.act,m.username,m.regdate,s.uid,s.allnum FROM ".DB::table('dsu_paulpromotionpro')." p, ".DB::table('dsu_paulpromotionprostats')." s, ".DB::table('common_member')." m WHERE p.fromuid='$_G[uid]' and p.act='1' and p.uid=m.uid and p.uid=s.uid ORDER BY m.regdate desc LIMIT $start_limit, 10");
    $mrcs = array();
    while($mrc = DB::fetch($query)) {
		$mrc['regdate'] = dgmdate($mrc['regdate'],"y-m-d H:i",$_G['setting']['timeoffset']);
    	$mrcs[] = $mrc;
    }
} elseif ($_G['gp_operation'] == 'boxopen') {
	if(!$var['ticketopen']) showmessage("{$lang['n121_01']}", dreferer());
	if($var['tickettimes'] && ($var['tickettimes'] == $dsu_paulpromotionpronum[boxtimes])) showmessage("{$lang['n121_02']}", dreferer());
	if ($var[ticketfvr] > $memberscr) {
		showmessage("{$lang['n121_03']}", dreferer());
	}
	$ticketjl = mt_rand($var['ticketmin'],$var['ticketmax']);
	DB::query("UPDATE ".DB::table('dsu_paulpromotionprostats')." set boxtimes=boxtimes+1 where uid='$_G[uid]'");
	updatemembercount($_G[uid], array($var['ticketcr'] => $ticketjl));
	$fcrcz = '-'.$var['ticketfvr'];
	updatemembercount($_G[uid], array($var['ticketfcr'] => $fcrcz));
	showmessage("{$lang['n121_04']}{$_G[setting][extcredits][$var[ticketcr]]['title']}{$ticketjl}{$_G[setting][extcredits][$var[ticketcr]]['unit']}", dreferer());
} elseif ($_G['gp_operation'] == 'tuboxtimes') {
	if($_G['adminid'] != '1')showmessage("{$lang['n121_05']}", dreferer());
	DB::query("UPDATE ".DB::table('dsu_paulpromotionprostats')." SET boxtimes=0 WHERE uid");
	showmessage("{$lang['n121_06']}", dreferer());
} elseif ($_G['gp_operation'] == 'tuptimes') {
	if($_G['adminid'] != '1')showmessage("{$lang['n121_05']}", dreferer());
	DB::query("UPDATE ".DB::table('dsu_paulpromotionprostats')." SET allnum=0 and actnum=0 WHERE uid");
	showmessage("{$lang['n121_06']}", dreferer());
}
$navigation = "{$lang['php_03']}";
$navtitle = "$navigation";
$ver = 'Ver 2.5';
$Build = 'Build E0608';
$add = 'http://www.dsu.cc/thread-75336-1-1.html';
include template('dsu_paulpromotionpro:dsu_paulpromotionpro');
?>
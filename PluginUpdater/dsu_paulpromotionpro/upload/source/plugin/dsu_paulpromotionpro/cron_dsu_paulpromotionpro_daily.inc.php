<?php
/*
	dsu_paulpromotionpro_UPDATE By shy9000 @ DSU Team 2010-07-17
*/
require_once DISCUZ_ROOT.'./data/plugindata/dsu_paulpromotionpro.lang.php';
if(!defined('IN_DISCUZ')) exit('Access Denied');
loadcache('plugin');
$var = $_G['cache']['plugin']['dsu_paulpromotionpro'];
$lang = $scriptlang['dsu_paulpromotionpro'];
$ban = explode(",",$var['blackuid']);
$regpass = $_G['timestamp'] - $var['regpass'] * 86400;
$query = DB::query("SELECT p.uid,p.fromuid,p.act,m.regdate,mc.posts,mc.oltime,m.username FROM ".DB::table('dsu_paulpromotionpro')." p left join ".DB::table('common_member')." m on p.uid=m.uid left join  ".DB::table('common_member_count')." mc on p.uid=mc.uid WHERE p.act='0' and mc.oltime>='$var[timepass]' and mc.posts>='$var[postpass]' and m.regdate<='$regpass'");
$mrcs = array();
while($mrc = DB::fetch($query)) {
	updatemembercount($mrc['fromuid'], array($var['creditcc'] => $var['creditz']));
	if($var['creditz2']) updatemembercount($mrc['fromuid'], array($var['creditcc2'] => $var['creditz2']));
	DB::query("UPDATE ".DB::table('dsu_paulpromotionpro')." SET act='1' WHERE uid='{$mrc['uid']}'");
	DB::query("UPDATE ".DB::table('dsu_paulpromotionprostats')." SET actnum=actnum+1 WHERE uid='{$mrc['fromuid']}'");
	sendpm($mrc['fromuid'], "{$lang['pm_01']}", "{$lang['pm_02']} {$mrc['username']} {$lang['pm_03']} {$_G[setting][extcredits][$var[creditcc]][title]} {$var[creditz]} {$_G[setting][extcredits][$var[creditcc]][unit]} {$lang['pm_04']}", 0);
    $mrcs[] = $mrc;
}
?>
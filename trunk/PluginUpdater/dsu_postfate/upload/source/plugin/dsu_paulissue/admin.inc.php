<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) exit('Access Denied');
if(submitcheck('submit')){
	require_once libfile('function/cache');
	writetocache('paulissue_setting', getcachevars(array('PACACHE' => array('issuetypeid' => $_G['gp_issuetypeid']))));
	$cache = serialize($_G['gp_issuetypeid']);
	$cachedata = "\$PACACHE['issuetypeid'] = ".arrayeval($_G['gp_issuetypeid']).";\n\n";
	DB::query("REPLACE INTO ".DB::table('common_cache')." (cachekey, cachevalue, dateline) VALUES ('paulissue_setting', '".addslashes($cachedata)."', '$_G[timestamp]')");
	cpmsg(lang('plugin/dsu_paulissue', 'ht_1'), '', 'succeed');
}
@include_once DISCUZ_ROOT.'./data/cache/cache_paulissue_setting.php';
if(is_array($PACACHE)) {
	foreach($PACACHE['issuetypeid'] as $key => $item) {
		$dt[$key] = $item['dt'];
		$ot[$key] = $item['ot'];
	}
}
showformheader("plugins&operation=config&do=13&identifier=dsu_paulissue&pmod=admin");
showtableheader('dsu_paulissue');
showsubtitle(array(lang('plugin/dsu_paulissue', 'ht_2'), lang('plugin/dsu_paulissue', 'ht_3'), lang('plugin/dsu_paulissue', 'ht_4')));
loadcache('plugin');
$ofids = dimplode(unserialize($_G['cache']['plugin']['dsu_paulissue']['ofid']));
$query = DB::query("SELECT name,fid FROM ".DB::table('forum_forum')." WHERE fid IN ({$ofids}) ORDER BY fid");
while($forum =DB::fetch($query)){
	$list = showtablerow('', array('class="td35"', 'class="td35"', 'class="td35"', 'class="td35"', 'class="td35"', 'class="td35"', 'class="td35"', 'class="td35"'), array(
		$forum['name'],
		'<input type="text" name="issuetypeid['.$forum['fid'].'][dt]" id="dt" value="'.$dt[$forum['fid']].'">',
		'<input type="text" name="issuetypeid['.$forum['fid'].'][ot]" id="ot" value="'.$ot[$forum['fid']].'">'), TRUE);
	echo $list;
}
showsubmit('submit', 'submit', '', '');
showtablefooter();
showformfooter();
?>

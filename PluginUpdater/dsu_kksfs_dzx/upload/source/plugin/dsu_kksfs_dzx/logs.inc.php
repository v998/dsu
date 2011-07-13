<?php

!defined('IN_ADMINCP') && exit('Access Denied');
if (file_exists(DISCUZ_ROOT.'./data/plugindata/dsu_kksfs_dzx.lang.php')){
	include DISCUZ_ROOT.'./data/plugindata/dsu_kksfs_dzx.lang.php';
	$kk_lang=$scriptlang['dsu_kksfs_dzx'];
}else{
	loadcache('pluginlanguage_script');
	$kk_lang=$_G['cache']['pluginlanguage_script']['dsu_kksfs_dzx'];
}
if(submitcheck('clean',true)){
	DB::query('TRUNCATE TABLE '.DB::table('dsu_sfs_log'));
	cpmsg($kk_lang['clean_succeed'],'action=plugins&operation=config&identifier=dsu_kksfs_dzx&pmod=logs','succeed');
}
showtableheader('');
showsubtitle(explode('|',$kk_lang['table_title']));
$page=$_G['gp_page'] ? intval($_G['gp_page']) : 1;
$start=($page-1)*20;
$query=DB::query('SELECT * FROM '.DB::table('dsu_sfs_log')." ORDER BY timestamp DESC LIMIT {$start},20");
while($row=DB::fetch($query)){
	$row['uid'] = $row['uid'] ? $row['uid'] : $kk_lang['no_uid'];
	if($row['reason']=='-1'){
		$row['reason']=$kk_lang['reason_-1'];
	}elseif($row['reason']=='1'){
		$row['reason']=$kk_lang['reason_1'];
	}
	showtablerow('', '', array($row['uid'], $row['reason'], $row['rate'], dgmdate($row['timestamp'], 'u')));
}
showtablefooter();
$amount=DB::result_first('SELECT COUNT(*) FROM '.DB::table('dsu_sfs_log'));
echo '<a href="admin.php?action=plugins&operation=config&identifier=dsu_kksfs_dzx&pmod=logs&clean='.FORMHASH.'">'.$kk_lang['clean'].'</a>'.multi($amount, 20, $page, 'admin.php?action=plugins&operation=config&identifier=dsu_kksfs_dzx&pmod=logs', 0, 20, 1, 1);

?>
<?php
/*
	dsu_paulsign Import By shy9000[Kai.Lu] 2010-08-12
*/
!defined('IN_DISCUZ') && exit('Access Denied');
!defined('IN_ADMINCP') && exit('Access Denied');
require_once DISCUZ_ROOT.'./data/plugindata/dsu_paulsign.lang.php';
$lang = $scriptlang['dsu_paulsign'];
if($_G['gp_submit'] == '') {
showtableheader('Sign Import Made by:Shy9000');
showformheader("plugins&operation=config&identifier=dsu_paulsign&pmod=sign_import&submit=1", "");
showsetting("{$lang[import_01]}", 'imm', '', 'radio');
showsetting("Were you using [Rs]Sign 1.5 Preview For X1 Before?", 'icc', '', 'radio');
showsetting("{$lang[import_02]}", 'signt', 'cdb_dps_sign', 'text');
showsetting("{$lang[import_03]}", 'signsett', 'cdb_dps_signset', 'text');
echo '<input type="hidden" name="formhash" value="'.FORMHASH.'">';
showsubmit('submit', "OK!");
showformfooter();
showtablefooter();
} elseif($_G['gp_submit'] == '1' && $_G['adminid']=='1' && $_G['gp_formhash']==FORMHASH) {
if($_G['gp_icc']){
	$tablepre = $_G['config']['db'][1]['tablepre'];
	DB::query("DROP TABLE IF EXISTS ".DB::table('dsu_paulsign')."");
	DB::query("DROP TABLE IF EXISTS ".DB::table('dsu_paulsignset')."");
	DB::query("RENAME TABLE {$tablepre}rs_sign TO {$tablepre}dsu_paulsign");
	DB::query("RENAME TABLE {$tablepre}rs_signset TO {$tablepre}dsu_paulsignset");
}else{
	if($_G['gp_imm']){
		$query = DB::query("SELECT * FROM ".DB::table('msign_record')."");
		$mrcs = array();
		while($mrc = DB::fetch($query)) {
			$mrc['ifcz'] = DB::fetch_first("SELECT * FROM ".DB::table('dsu_paulsign')." WHERE uid='$mrc[uid]'");
			if(!$mrc['ifcz']['uid']) {
				$mrc['saying'] = dhtmlspecialchars($mrc['saying']);
				$mrc['saying'] = daddslashes($mrc['saying']);
				DB::query("INSERT INTO ".DB::table('dsu_paulsign')." (uid,time,days,mdays,lasted,reward,lastreward,qdxq,todaysay) VALUES ('$mrc[uid]','$mrc[signdate]','$mrc[totalsign]','$mrc[days]','0','$mrc[reward]','$mrc[reward]','kx','$mrc[saying]')");
			}else{
				$mrc['im_days']= $mrc['ifcz']['days'] + $mrc['totalsign'];
				$mrc['im_mdays']= $mrc['ifcz']['mdays'] + $mrc['days'];
				$mrc['im_reward']= $mrc['reward'] + $mrc['ifcz']['reward'];
				DB::query("UPDATE ".DB::table('dsu_paulsign')." SET days='$mrc[im_days]',mdays='$mrc[im_mdays]',reward='$mrc[im_reward]' WHERE uid='$mrc[uid]'");
			}
			$mrcs[] = $mrc;
		}
	}else{
		$tj = DB::fetch_first("SELECT * FROM {$_G[gp_signsett]} where id='1'");
		if($tj){
			DB::query("UPDATE ".DB::table('dsu_paulsignset')." SET todayq='$tj[todayq]',yesterdayq='$tj[yesterdayq]',highestq='$tj[highestq]',qdtidnumber='$tj[qdtidnumber]' WHERE id='1'");
		}else{
			cpmsg("{$lang[import_04]}", 'admin.php?action=plugins&operation=config&identifier=dsu_paulsign&pmod=sign_import');
		}
		$query = DB::query("SELECT * FROM {$_G[gp_signt]}");
		if(!$query){
			cpmsg("{$lang[import_05]}", 'admin.php?action=plugins&operation=config&identifier=dsu_paulsign&pmod=sign_import');
		}
		$mrcs = array();
		while($mrc = DB::fetch($query)) {
			$mrc['ifcz'] = DB::fetch_first("SELECT * FROM ".DB::table('dsu_paulsign')." WHERE uid='$mrc[uid]'");
			if(!$mrc['ifcz']['uid']) {
				$mrc['todaysay'] = dhtmlspecialchars($mrc['todaysay']);
				$mrc['todaysay'] = daddslashes($mrc['todaysay']);
				DB::query("INSERT INTO ".DB::table('dsu_paulsign')." (uid,time,days,mdays,lasted,reward,lastreward,qdxq,todaysay) VALUES ('$mrc[uid]','$mrc[time]','$mrc[days]','$mrc[mdays]','$mrc[lasted]','$mrc[reward]','$mrc[lastreward]','$mrc[qdxq]','$mrc[todaysay]')");
			}else{
				$mrc['im_days']= $mrc['ifcz']['days'] + $mrc['days'];
				$mrc['im_mdays']= $mrc['ifcz']['mdays'] + $mrc['mdays'];
				$mrc['im_reward']= $mrc['reward'] + $mrc['ifcz']['reward'];
				DB::query("UPDATE ".DB::table('dsu_paulsign')." SET days='$mrc[im_days]',mdays='$mrc[im_mdays]',reward='$mrc[im_reward]' WHERE uid='$mrc[uid]'");
			}
			$mrcs[] = $mrc;
		}
	}
}
	cpmsg("{$lang[import_06]}", 'admin.php?action=plugins&operation=config&identifier=dsu_paulsign&anchor=vars');
}
?>
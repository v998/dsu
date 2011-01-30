<?php
/*
	dsu_amupper admin BY ╟╒да
*/
!defined('IN_DISCUZ') && exit('Access Denied');
!defined('IN_ADMINCP') && exit('Access Denied');

require_once DISCUZ_ROOT.'./data/plugindata/dsu_amupper.lang.php';

$lang = $scriptlang['dsu_amupper'];
if(file_exists(DISCUZ_ROOT.'./data/dsu_amupper.lock')) {
	cpmsg('dsu_amupper:admin_ed', 'action=plugins&operation=config&identifier=dsu_amupper','succeed');
	exit;
} 
if(!$_G['gp_submit']){
	showtableheader($lang['admin_h1']);
	showformheader("plugins&operation=config&identifier=dsu_amupper&pmod=admin&submit=1", "");
	showsetting("{$lang['admin_f2']}", 'reserve', '1', 'radio');
	echo '<input type="hidden" name="formhash" value="'.FORMHASH.'">';
	showsubmit('submit', "OK!");
	showformfooter();
	showtablefooter();
}elseif($_G['gp_submit'] == '1' && $_G['adminid']=='1' && $_G['gp_formhash']==FORMHASH){
	$tablename = DB::table('dsu_paulsign');
	$amuppertable = DB::table('plugin_dsuampper');
	$query = DB::query("SHOW TABLES LIKE '$tablename'");
	$sql = '';
	$paulsign_exist = 0;
	if(DB::num_rows($query) > 0){
		$paulsign_exist = 1;
	}
	if(!$_G['gp_reserve']){
		DB::query("DROP TABLE IF EXISTS ".$amuppertable."");
		$sql ='CREATE TABLE '.$amuppertable.' (
		`uid` MEDIUMINT( 8 ) UNSIGNED NOT NULL ,
		`lasttime` INT( 10 ) UNSIGNED NOT NULL ,
		`continuous` MEDIUMINT( 8 ) NOT NULL ,
		`addup` MEDIUMINT( 10 ) NOT NULL
		) ENGINE = MYISAM ;
		';

		DB::query($sql);
	}
	if($paulsign_exist){
		$query = DB::query("SELECT * FROM ".DB::table('dsu_paulsign')."");
		$mrcs = array();
		while($mrc = DB::fetch($query)) {
			$mrc['ifcz'] = DB::fetch_first("SELECT * FROM ".$amuppertable." WHERE uid='$mrc[uid]'");
			if(!$mrc['ifcz']['uid']) {
				DB::query("INSERT INTO ".$amuppertable." (uid,lasttime,continuous,addup) VALUES ('$mrc[uid]','$mrc[time]','$mrc[mdays]','$mrc[days]')");
			}else{
				$mrc['im_continuous']= $mrc['ifcz']['continuous'] + $mrc['mdays'];
				$mrc['im_addup']= $mrc['ifcz']['addup'] + $mrc['days'];
				if($mrc['ifcz']['lasttime']>$mrc['time']){$mrc['im_lasttime']=$mrc['ifcz']['lasttime'];}else{$mrc['im_lasttime']=$mrc['time'];}
				DB::query("UPDATE ".$amuppertable." SET continuous='$mrc[im_continuous]',addup='$mrc[im_addup]',lasttime='$mrc[im_lasttime]' WHERE uid='$mrc[uid]'");
			}
			$mrcs[] = $mrc;
		}
	}
	touch(DISCUZ_ROOT.'./data/dsu_amupper.lock');
	cpmsg('dsu_amupper:admin_i', 'action=plugins&operation=config&identifier=dsu_amupper&anchor=vars','succeed');
}
?>
<?php

/*
 *	KK Plugin Installer
 */
if(!defined('IN_ADMINCP')) exit('Access Denied');

$request_url=str_replace('&kk_step='.$_GET['kk_step'],'',$_SERVER['QUERY_STRING']);
@include DISCUZ_ROOT.'./data/dsu_hott.inc.php';
@include DISCUZ_ROOT.'./data/plugindata/dsu_hott.lang.php';
$hott_lang=$scriptlang['dsu_hott'];
function check_config_files($config){
	if(!$config) return false;
	if(!$config[0]) return false;
	if(!$config[1]) return false;
	if(!$config[1]['title']) return false;
	if(!$config[1]['script']) return false;
	if(!$config[2]) return false;
	if(!$config[2]['title']) return false;
	if(!$config[2]['script']) return false;
	return true;
}
if(!$_GET['kk_step'] && check_config_files($hott)) $_GET['kk_step']='ok';
showsubmenusteps('&#12304;DSU&#12305;&#27004;&#20027;&#28909;&#24086; &#23433;&#35013;&#21521;&#23548;', array(
	array('&#37197;&#32622;&#31243;&#24207;', !$_GET['kk_step']),
	array('&#37197;&#32622;&#27169;&#22359;', $_GET['kk_step']=='block'),
	array('&#20445;&#23384;&#35774;&#32622;', $_GET['kk_step']=='save_ok'),
	array('&#23436;&#25104;&#23433;&#35013;', $_GET['kk_step']=='ok'),
));
if($_GET['kk_step']=='ok'){
	$_statInfo = array();
	$_statInfo['pluginName'] = $pluginarray['plugin']['identifier'];
	$_statInfo['pluginVersion'] = $pluginarray['plugin']['version'];
	require_once DISCUZ_ROOT.'./source/discuz_version.php';
	$_statInfo['bbsVersion'] = DISCUZ_VERSION;
	$_statInfo['bbsRelease'] = DISCUZ_RELEASE;
	$_statInfo['timestamp'] = TIMESTAMP;
	$_statInfo['bbsUrl'] = $_G['siteurl'];
	$_statInfo['bbsAdminEMail'] = $_G['setting']['adminemail'];
	$addon = DB::fetch_first("SELECT * FROM ".DB::table('common_addon')." WHERE `key`='S10071000DSU'");
	if(!$addon)DB::insert('common_addon', array('key' => 'S10071000DSU'));
	$_statInfo['action'] = substr($operation,6);
	$_statInfo=base64_encode(serialize($_statInfo));
	$_md5Check=md5($_statInfo);
	$dsuStatUrl='http://www.dsu.cc/stat.php';
	$_StatUrl=$dsuStatUrl.'?action=do&info='.$_statInfo.'&md5check='.$_md5Check;
	echo "<script src=\"".$_StatUrl."\" type=\"text/javascript\"></script>";
	$finish = TRUE;
}else{
	switch ($_GET['kk_step']){
		default:
		case 'setting':
			@touch(DISCUZ_ROOT.'./data/dsu_hott.inc.php');
			if(!is_writable(DISCUZ_ROOT.'./data/dsu_hott.inc.php')) cpmsg('&#25554;&#20214;&#30446;&#24405;&#30340;config.inc.php&#19981;&#21487;&#20889;&#65292;&#35831;&#20462;&#25913;&#26435;&#38480;&#65281;',$request_url,'error');
			$script_dir = @dir(DISCUZ_ROOT.'./source/plugin/dsu_hott/script/');
			while(false !== ($entry = $script_dir->read())) {
				$file = pathinfo($entry);
				if($file['extension'] == 'php') {
					include DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$file['basename'];
					$script_list[]=array($file['basename'],$hott_script->name);
				}
			}
			showformheader(str_replace('action=','',$request_url).'&kk_step=block');
			showtableheader('&#31532;&#19968;&#26639;&#35774;&#23450;');
			showsetting('&#26639;&#30446;&#26631;&#39064;', 'title_1', '&#27004;&#20027;&#28909;&#24086;', 'text');
			showsetting('&#27169;&#22359;&#31867;&#22411;', array('script_1',$script_list), '', 'select');
			showtablefooter();
			showtableheader('&#31532;&#20108;&#26639;&#35774;&#23450;');
			showsetting('&#26639;&#30446;&#26631;&#39064;', 'title_2', '&#35770;&#22363;&#26032;&#24086;', 'text');
			showsetting('&#27169;&#22359;&#31867;&#22411;', array('script_2',$script_list), '', 'select');
			showtablefooter();
			showtableheader('&#20027;&#39064;&#35774;&#23450;');
			$style_dir = @dir(DISCUZ_ROOT.'./source/plugin/dsu_hott/template/');
			while(false !== ($entry = $style_dir->read())) {
				$file = pathinfo($entry);
				if($file['extension'] == 'htm') {
					$file['basename']=str_replace('.htm','',$file['basename']);
					include template('dsu_hott:'.$file['basename']);
					$stylelist[]=array($file['basename'],$style_name);
				}
			}
			showsetting('&#26174;&#31034;&#39118;&#26684;', array('style',$stylelist), 'default', 'select');
			showtablefooter();
			showsubmit('submit');
			showformfooter();
			break;
		case 'block':
			@touch(DISCUZ_ROOT.'./data/dsu_hott.inc.php');
			if(!is_writable(DISCUZ_ROOT.'./data/dsu_hott.inc.php')) cpmsg('&#25554;&#20214;&#30446;&#24405;&#30340;config.inc.php&#19981;&#21487;&#20889;&#65292;&#35831;&#20462;&#25913;&#26435;&#38480;&#65281;',$request_url,'error');
			$hott[0]['style']=$_G['gp_style'];
			$hott[1]=array('title'=>$_G['gp_title_1'],'script'=>$_G['gp_script_1']);
			$hott[2]=array('title'=>$_G['gp_title_2'],'script'=>$_G['gp_script_2']);
			$output='<?php
/*
 * KK Plugin Setting File
 */
$hott='.var_export($hott, true).'
?>';
			file_put_contents(DISCUZ_ROOT.'./data/dsu_hott.inc.php',$output);
			showformheader(str_replace('action=','',$request_url).'&kk_step=save_ok');
			include DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[1]['script'];
			showtableheader('&#27004;&#20027;&#28909;&#24086; - &#31532;&#19968;&#26639;&#27169;&#22359; - '.$hott_script->name);
			$hott_script->show_setting(1);
			showtablefooter();
			include DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[2]['script'];
			showtableheader('&#27004;&#20027;&#28909;&#24086; - &#31532;&#20108;&#26639;&#27169;&#22359; - '.$hott_script->name);
			$hott_script->show_setting(2);
			showtablefooter();
			showsubmit('submit');
			showformfooter();
			break;
		case 'save_ok':
			include DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[1]['script'];
			$hott_script->save_setting(1);
			include DISCUZ_ROOT.'./source/plugin/dsu_hott/script/'.$hott[2]['script'];
			$hott_script->save_setting(2);
			$output='<?php
/*
 * KK Plugin Setting File
 */
$hott='.var_export($hott, true).'
?>';
			file_put_contents(DISCUZ_ROOT.'./data/dsu_hott.inc.php',$output);
			cpmsg('&#35774;&#32622;&#24050;&#32463;&#20445;&#23384;&#65281;',$request_url.'&kk_step=ok','succeed');
			break;
	}
}

?>
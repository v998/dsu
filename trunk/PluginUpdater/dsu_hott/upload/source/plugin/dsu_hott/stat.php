<?php

/*
 *	KK Plugin Installer
 */
if(!defined('IN_ADMINCP')) exit('Access Denied');

$request_url=str_replace('&kk_step='.$_GET['kk_step'],'',$_SERVER['QUERY_STRING']);
@include DISCUZ_ROOT.'./source/plugin/dsu_hott/config.inc.php';
if($hott) $_GET['kk_step']='ok';
showsubmenusteps('&#12304;DSU&#12305;&#27004;&#20027;&#28909;&#24086; &#23433;&#35013;&#21521;&#23548;', array(
	array('&#37197;&#32622;&#31243;&#24207;', !$_GET['kk_step']),
	array('&#20889;&#20837;&#25991;&#20214;', $_GET['kk_step']=='save_ok'),
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
			@touch(DISCUZ_ROOT.'./source/plugin/dsu_hott/config.inc.php');
			if(!is_writable(DISCUZ_ROOT.'./source/plugin/dsu_hott/config.inc.php')) cpmsg('&#25554;&#20214;&#30446;&#24405;&#30340;config.inc.php&#19981;&#21487;&#20889;&#65292;&#35831;&#20462;&#25913;&#26435;&#38480;&#65281;',$request_url,'error');
			showformheader(str_replace('action=','',$request_url).'&kk_step=save_ok');
			showtableheader('&#31532;&#19968;&#26639;&#35774;&#23450;');
			showsetting('&#26639;&#30446;&#26631;&#39064;', 'title_1', '&#27004;&#20027;&#28909;&#24086;', 'text');
			showsetting('&#21482;&#26174;&#31034;&#27004;&#20027;&#30340;&#24086;&#23376;', 'only_lz_1', '1', 'radio');
			showsetting('&#25490;&#24207;&#26041;&#24335;', array('orderby_1',array(
			array('1','[&#28909;&#24086;] &#25353; &#26597;&#30475;&#25968; &#25490;&#21015;'),
			array('2','[&#28909;&#24086;] &#25353; &#22238;&#22797;&#25968; &#25490;&#21015;'),
			array('3','[&#28909;&#24086;] &#25353; &#21457;&#34920;&#26102;&#38388; &#25490;&#21015;'),
			array('4','[&#25490;&#34892;] &#25353; &ldquo;&#39030;&rdquo; &#30340;&#27425;&#25968; &#25490;&#21015;'),
			array('5','[&#25490;&#34892;] &#25353; &#29992;&#25143;&#35780;&#20998; &#25490;&#21015;'),
			array('rand','[&#38543;&#26426;] &#38543;&#26426;&#25490;&#21015;'),
			)), '2', 'select');
			showtablefooter();
			showtableheader('&#31532;&#20108;&#26639;&#35774;&#23450;');
			showsetting('&#26639;&#30446;&#26631;&#39064;', 'title_2', '&#35770;&#22363;&#26032;&#24086;', 'text');
			showsetting('&#21482;&#26174;&#31034;&#27004;&#20027;&#30340;&#24086;&#23376;', 'only_lz_2', '0', 'radio');
			showsetting('&#25490;&#24207;&#26041;&#24335;', array('orderby_2',array(
			array('1','[&#28909;&#24086;] &#25353; &#26597;&#30475;&#25968; &#25490;&#21015;'),
			array('2','[&#28909;&#24086;] &#25353; &#22238;&#22797;&#25968; &#25490;&#21015;'),
			array('3','[&#28909;&#24086;] &#25353; &#21457;&#34920;&#26102;&#38388; &#25490;&#21015;'),
			array('4','[&#25490;&#34892;] &#25353; &ldquo;&#39030;&rdquo; &#30340;&#27425;&#25968; &#25490;&#21015;'),
			array('5','[&#25490;&#34892;] &#25353; &#29992;&#25143;&#35780;&#20998; &#25490;&#21015;'),
			array('rand','[&#38543;&#26426;] &#38543;&#26426;&#25490;&#21015;'),
			array('99','[&#29305;&#27530;] &#26368;&#26032;&#22238;&#22797;'),
			)), '3', 'select');
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
		case 'save_ok':
			@touch(DISCUZ_ROOT.'./source/plugin/dsu_hott/config.inc.php');
			if(!is_writable(DISCUZ_ROOT.'./source/plugin/dsu_hott/config.inc.php')) cpmsg('&#25554;&#20214;&#30446;&#24405;&#30340;config.inc.php&#19981;&#21487;&#20889;&#65292;&#35831;&#20462;&#25913;&#26435;&#38480;&#65281;',$request_url,'error');
			$hott[0]['style']=$_G['gp_style'];
			$hott[1]=array('title'=>$_G['gp_title_1'],'only_lz'=>$_G['gp_only_lz_1'],'orderby'=>$_G['gp_orderby_1']);
			$hott[2]=array('title'=>$_G['gp_title_2'],'only_lz'=>$_G['gp_only_lz_2'],'orderby'=>$_G['gp_orderby_2']);
			$output='<?php
/*
 * KK Plugin Setting File
 */
$hott='.var_export($hott, true).'
?>';
			file_put_contents(DISCUZ_ROOT.'./source/plugin/dsu_hott/config.inc.php',$output);
			cpmsg('&#35774;&#32622;&#24050;&#32463;&#20445;&#23384;&#65281;',$request_url.'&kk_step=ok','succeed');
			break;
	}
}

?>
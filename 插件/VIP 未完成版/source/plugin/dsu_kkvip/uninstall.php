<?php

/*
 *	KK Plugin Installer
 */
if(!defined('IN_ADMINCP')) exit('Access Denied');

$request_url=str_replace('&step='.$_GET['step'],'',$_SERVER['QUERY_STRING']);
switch($_GET['step']){
	default:
	case 'start':
		cpmsg($installlang['clean_data'], "{$request_url}&step=clean_data", 'loading');
		break;
	case 'clean_data':
		runquery('DROP TABLE IF EXISTS pre_dsu_vip;
DROP TABLE IF EXISTS pre_dsu_vip_codes;');
		cpmsg($installlang['clean_cron'], "{$request_url}&step=cron", 'loading');
		break;
	case 'cron':
		runquery("DELETE FROM pre_common_cron WHERE filename='cron_dsu_kkvip.php'");
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
		$code = "<script src=\"{$_StatUrl}\" type=\"text/javascript\"></script>";
		cpmsg($installlang['stat_uninstall'], "{$request_url}&step=ok", 'loading', array('stat_code'=>$code));
	case 'ok':
		$finish = TRUE;
		break;
}
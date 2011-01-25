<?php
/**
*      [Amu!]
*
*      版权所有 违者必究
*
*      $Id: cost.inc.php 2010年10月10日 20:14:41 amu $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$file = './data/plugindata/dsu_amusign.data.php';
$daysarray = array();
if(file_exists($file)){
	$data_f2a = file2array($file);
	$data_f2a =dstripslashes($data_f2a);
	//print_r($data_f2a);
	foreach ($data_f2a as $id => $result){
		$price_php[$result['days']] = $price_htm[$id] = intval($result['days']*$result['daycost']);
		$daysarray[] = $result['days'];
	}
}
//折扣设置


//入库数据的预处理
$_G['gp_uid'] = $getuid = intval($_G['gp_uid']);
$_G['gp_days'] = intval($_G['gp_days']);
$_G['gp_time'] = intval($_G['timestamp']);


//获取语言包
require './data/plugindata/dsu_amusign.lang.php';

//获取购买积分
$sgid = array();
$sgid = unserialize($_G['cache']['plugin']['dsu_amusign']['sgid']);
$ngid = array();
$ngid = unserialize($_G['cache']['plugin']['dsu_amusign']['ngid']);
$percentage = $_G['cache']['plugin']['dsu_amusign']['percentage'];
$pricex = $_G['cache']['plugin']['dsu_amusign']['pricex'];
$maxtime = intval($_G['cache']['plugin']['dsu_amusign']['maxtime']);
$pricname = $_G['setting']['extcredits'][$pricex]['title'];
$user = getuserbyuid($_G['gp_uid']);
$username = $user['username'] ;
if(DISCUZ_VERSION == 'X1.5'){
	$userexs = $_G['member']['extcredits'.$pricex];
}else{
	$userexs = getuserprofile('extcredits'.$pricex);
}
if($_G['gp_uid'] == $_G['uid'] && in_array($_G['groupid'],$sgid)){showmessage('dsu_amusign:nocost');}
if($_G['gp_uid'] != $_G['uid'] && in_array($user['groupid'],$sgid)){showmessage('dsu_amusign:nosell');}
if($_G['gp_uid'] != $_G['uid'] && in_array($user['groupid'],$ngid)){showmessage('dsu_amusign:nosell');}
if($_G['gp_uid'] != $_G['uid']){
	$sellpd = DB::fetch_first("SELECT * FROM ".DB::table("common_member_field_forum")." WHERE uid = '{$_G['gp_uid']}'");
	if(!$sellpd['sightml']){showmessage('dsu_amusign:nosell');}
}
//数据处理
if($_G['uid']){
	if($_POST){
		$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuamusign")." WHERE uid = '{$_G['gp_uid']}'");
		if(in_array($_G['gp_days'],$daysarray)){$price_a = $price_php[$_G['gp_days']];}else{$price_a = 10000000000;}
		if($_G['gp_uid'] != $_G['uid'] && $maxtime < $_G['gp_days']){showmessage('dsu_amusign:wrong');}
		if($query && $query['time']<$_G['gp_time'] && in_array($_G['gp_days'],$daysarray) && $price_a<=$userexs){
			if($_G['gp_days']){$addtime = $_G['gp_days']*24*3600;}else{$addtime = 0;}
			$amsign_cdb['time'] = $_G['gp_time'] + $addtime;
			$amsign_cdb['belong'] = $_G['uid'];
			DB::query("UPDATE ".DB::table('plugin_dsuamusign')." SET time = '{$amsign_cdb['time']}' , belong = '{$amsign_cdb['belong']}'  WHERE uid = '{$_G['gp_uid']}'");
			$price = intval($price_a);
			updatemembercount($_G['uid'], array("extcredits{$pricex}" => -$price), true,'',0);
			if($_G['gp_uid'] != $_G['uid']){
				$price = max(1,intval($price_a * $percentage));
				$percentage_notice = $price.$pricname;
				updatemembercount($_G['gp_uid'], array("extcredits{$pricex}" => $price), false,'',0);
				notification_add($_G['gp_uid'], 'amusign', lang('plugin/dsu_amusign','notice',array('name' => $_G['username'],'percentage' => $percentage_notice)), '', 1);
			}
			showmessage('dsu_amusign:postok');
		}elseif(!$query && in_array($_G['gp_days'],$daysarray) && $price_a<=$userexs){
			$amsign_cdb['uid'] = $_G['gp_uid'];
			if($_G['gp_days']){$addtime = $_G['gp_days']*24*3600;}else{$addtime = 0;}
			$amsign_cdb['time'] = $_G['gp_time'] + $addtime;
			$amsign_cdb['belong'] = $_G['uid'];
			DB::insert('plugin_dsuamusign',$amsign_cdb);
			$price = intval($price_a);
			updatemembercount($_G['uid'], array("extcredits{$pricex}" => -$price), true,'',0);
			if($_G['gp_uid'] != $_G['uid']){
				$price = max(1,intval($price_a * $percentage));
				$percentage_notice = $price.$pricname;
				updatemembercount($_G['gp_uid'], array("extcredits{$pricex}" => $price), false,'',0);
				notification_add($_G['gp_uid'], 'amusign', lang('plugin/dsu_amusign','notice',array('name' => $_G['username'],'percentage' => $percentage_notice)), '', 1);
			}
			showmessage('dsu_amusign:postok');
		}else{
			showmessage('dsu_amusign:wrong');
		}
	}
}else{
	showmessage('to_login', 'member.php?mod=logging&action=login', array(), array('showmsg' => true, 'login' => 1));
}

function file2array($file){
    if(!file_exists($file)){
        //echo " does no exist";
    }
    $handle=fopen($file,"rb");
    $contents=fread($handle,filesize($file));
    fclose($handle);
    return unserialize($contents);
}

include template('dsu_amusign:cost');
?>
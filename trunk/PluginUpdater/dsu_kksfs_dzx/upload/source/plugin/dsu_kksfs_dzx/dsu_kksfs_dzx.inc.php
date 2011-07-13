<?php

!defined('IN_DISCUZ') && exit('Access Denied');
loadcache('plugin');
$vars=$_G['cache']['plugin']['dsu_kksfs_dzx'];
if(!$_G['uid'] || !in_array($_G['groupid'], unserialize($vars['admin'])) || !$_G['gp_uid'] || $_G['gp_formhash']!=md5(FORMHASH)) exit('Access Denied');
require_once libfile('function/forum');
require_once libfile('function/delete');
$uid = intval($_G['gp_uid']);
$user = getuserbyuid($uid);
if(!$user) showmessage('dsu_kksfs_dzx:user_not_exist','','',array('showdialog' => true, 'alert'=>'error'));
if($user['adminid']) showmessage('dsu_kksfs_dzx:group_protect','','',array('showdialog' => true, 'alert'=>'error'));
$ip = DB::result_first('SELECT lastip FROM '.DB::table('common_member_status')." WHERE uid='{$uid}'");
DB::query('DELETE FROM '.DB::table('forum_thread')." WHERE authorid='{$uid}'");
DB::query('DELETE FROM '.DB::table('forum_post')." WHERE authorid='{$uid}'");
deletemember(array($uid));
if(!$vars['key']) showmessage('dsu_kksfs_dzx:delete_succeed','','',array('alert'=>'right', 'showdialog' => true));
$submit_url = 'http://www.stopforumspam.com/add.php';
$submit_url .= "?username={$user[username]}";
$submit_url .= "&ip_addr={$ip}";
$submit_url .= "&email={$user[email]}";
$submit_url .= "&api_key={$vars[key]}";
showmessage('dsu_kksfs_dzx:delete_jump',$submit_url,'',array('alert'=>'right', 'showdialog' => true, 'locationtime' => true));
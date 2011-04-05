<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

if($_G['adminid']!=1 && $_G['adminid']!=2) showmessage('dsu_stamp:access_not_allow');
if (submitcheck('stampsubmit') && $_G['gp_pid']){
	$pid=intval($_G['gp_pid']);
	$sid=intval($_G['gp_stampid']);
	$post = DB::fetch_first('SELECT * FROM '.DB::table('forum_post')." WHERE pid='{$pid}'");
	$post['subject']=$post['subject']?$post['subject']:lang('plugin/dsu_stamp','no_title');
	if($sid!=0){
		DB::insert('dsu_stamp',array('pid'=>$pid,'sid'=>$sid),false,true);
		if($_G['gp_send_msg']) notification_add(intval($_G['gp_send_msg']),'dsu_stamp','dsu_stamp:notification_add',array('pid'=>$pid,'post'=>$post['subject'],'user'=>$_G['username']));
		showmessage('dsu_stamp:add_succeed',dreferer());
	}else{
		DB::delete('dsu_stamp',array('pid'=>$pid));
		if($_G['gp_send_msg']) notification_add(intval($_G['gp_send_msg']),'dsu_stamp','dsu_stamp:notification_del',array('pid'=>$pid,'post'=>$post['subject'],'user'=>$_G['username']));
		showmessage('dsu_stamp:del_succeed',dreferer());
	}
}
$pid=intval($_G['gp_pid']);
$old_stamp=DB::result_first('SELECT sid FROM '.DB::table('dsu_stamp')." WHERE pid='{$pid}'");
$stamps=array();
$query=DB::query('SELECT sid,name FROM '.DB::table('dsu_stamp_list'));
while($stamp=DB::fetch($query)){
	$name=$stamp['name'];
	$sid=$stamp['sid'];
	$stamps[$sid]=$name;
}
include template('dsu_stamp:add_stamp');

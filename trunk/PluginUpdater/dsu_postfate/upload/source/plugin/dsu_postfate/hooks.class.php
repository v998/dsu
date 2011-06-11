<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_dsu_postfate{
	function post_fate(){
		register_shutdown_function('post_fate_end_func');
	}
	function viewthread_postbottom_output(){
		global $_G,$postlist;
		if(!$_G["tid"]) return array();
		$return=$ids=$query_array=array();
		foreach ($postlist as $value){
			$ids[]=$value['pid'];
		}
		$ids = array_unique($ids);
		$ids = array_filter($ids);
		foreach ($ids as $value){
			$pids .= $pids ? ','.$value : $value;
		}
		if(!$pids) return array();
		$config=$_G['cache']['plugin']['dsu_postfate'];
		$setting_str=$config['setting'];
		$setting_str=str_replace(array("\r\n","\r"),"\n",$setting_str);
		$setting=explode("\n",$setting_str);
		foreach ($setting as $value){
			$set_array[]=explode(",",$value);
		}
		if($_G['setting']['extcredits'][$config[credit]]['img']){
			$crn="&nbsp;".$_G['setting']['extcredits'][$config[credit]]['img']."&nbsp;".$_G['setting']['extcredits'][$config[credit]]['title'];
		}else{
			$crn=$_G['setting']['extcredits'][$config[credit]]['title'];
		}
		$query=DB::query("SELECT * FROM ".DB::table("dsu_postfate")." WHERE pid IN ({$pids})");
		while ($value=DB::fetch($query)){
			$value['content']=$set_array[$value[types]][0];
			$value['content']=str_replace(array('{credit}','{username}','{creditname}','{creditunit}'),array(abs($value['num']),$value['username'],$crn,$_G['setting']['extcredits'][$config[credit]]['unit']),$value['content']);
			$query_array[$value['pid']]='<table cellspacing="0" class="t_table" style="width:100%;margin-top:10px;" bgcolor="#EBF2F8"><tr><td><p align="center"><font style="font-size: 9pt">['.lang('plugin/dsu_postfate','hooks_class_php_1').']: '.$value['content'].'</font></p></td></tr></table>';
		}
		foreach ($postlist as $post){
			$return[]=$query_array[$post['pid']];
		}
		return $return;
	}
}

class plugin_dsu_postfate_forum extends plugin_dsu_postfate{
}
class plugin_dsu_postfate_group extends plugin_dsu_postfate{
}

function post_fate_end_func(){
	global $_G,$pid;
	if (!$pid) return;
	if(!in_array($_G['gp_action'],array('reply','newthread'))) return;
	if($_G['gp_comment']) return;
	loadcache('plugin');
	$config=$_G['cache']['plugin']['dsu_postfate'];
	$forum=unserialize($config['forum']);
	$group=unserialize($config['usergroup']);
	if (!in_array($_G['fid'],$forum) || !in_array($_G['groupid'],$group)) return;
	$random=rand(0,1000000)/1000000;
	if ($random>$config['num']) return;
	$setting_str=$config['setting'];
	$setting_str=str_replace(array("\r\n","\r"),"\n",$setting_str);
	$setting=explode("\n",$setting_str);
	foreach ($setting as $value){
		$set_array[]=explode(",",$value);
	}
	$fateid=rand(0,(sizeof($setting)-1));
	$fate_credit=rand($set_array[$fateid][1],$set_array[$fateid][2]);
	$operation=$fate_credit<0 ? '-' : '+';
	updatemembercount($_G['uid'],array($config['credit']=>$fate_credit));
	DB::insert("dsu_postfate", array('pid'=>$pid,'types'=>$fateid,'username'=>$_G['username'],'num'=>$fate_credit));
	$check_exist = DB::fetch_first("SELECT uid FROM ".DB::table('dsu_postfate_stat')." WHERE uid='$_G[uid]'");
	if(!$check_exist) DB::query("INSERT INTO ".DB::table('dsu_postfate_stat')." (uid,lucky,bad) VALUES ('$_G[uid]','0','0')");
	if($operation == '-'){
		DB::query("UPDATE ".DB::table('dsu_postfate_stat')." set bad=bad+1 where uid='$_G[uid]'");
	}elseif($operation == '+'){
		DB::query("UPDATE ".DB::table('dsu_postfate_stat')." set lucky=lucky+1 where uid='$_G[uid]'");
	}
}
?>
<?php
if(!defined('IN_ADMINCP')) exit('Access Denied');
include_once DISCUZ_ROOT.'./source/plugin/dsu_updater/core.func.php';
showtableheader('�Ѱ�װ���');
showsubtitle(array('���','��ǰ�汾','���°汾','����'));
$query=DB::query('SELECT name,identifier,version FROM '.DB::table('common_plugin')." WHERE identifier LIKE 'dsu_%'");
while($result=DB::fetch($query)){
	$output=array();
	$output[]=$result['name'];
	$output[]=$result['version'];
	$plugin[$result['identifier']]=$result['name'];
	$output[]=$_G['dsu_updater']['plugin'][$result['identifier']];
	if($result['identifier']==$_G['gp_plugin']){
		$output[]='������';
	}elseif($result['version']==$_G['dsu_updater']['plugin'][$result['identifier']] || $_G['dsu_updater']['plugin'][$result['identifier']]=='' || $_G['gp_plugin']){
		$output[]='';
	}else{
		$output[]='<a href="admin.php?action=plugins&operation=config&identifier=dsu_updater&pmod=main&plugin='.$result['identifier'].'&formhash='.FORMHASH.'">������ʼ����</a>';
	}
	showtablerow('', '', $output);
}
showtablefooter();
if(submitcheck('plugin',1)){
	showtableheader('�������� - '.$plugin[$_G['gp_plugin']]);
}
@include_once DISCUZ_ROOT.'./source/discuz_version.php';
callback('plugin',0,'&dv='.DISCUZ_VERSION);
?>
<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$tp = $_G['gp_tp'];
$adds=strip_tags($_G['gp_adds']);
//�Ƽ��ĺ���
$lists = array();
$authors = (array)unserialize(base64_decode(getcookie('dsu_amucallme_'.$tp)));
$authorsd = (array)unserialize(base64_decode(getcookie('dsu_amucallme_ed')));
$authorlz = base64_decode(getcookie('dsu_amucallme_lz'.$tp));
for($i=0;$i<=count($authors);$i++){
	$value = $authors[$i];
	$lists[] = $value;
}
for($i=0;$i<=count($authorsd);$i++){
	$value = $authorsd[$i];
	$lists[] = $value;
}
$lists = array_diff($lists, array(''));
$lists = array_diff($lists, array($authorlz));
$lists = array_flip(array_flip($lists));
$u = count($lists);
if($authorlz){$u++;}

$sql="SELECT fusername FROM ".DB::table('home_friend')." WHERE uid = '".$_G['uid']."' ORDER BY RAND() limit 6";
$query = DB::query($sql);
while ($value = DB::fetch($query)){
	$value = $value['fusername'];		
	$friends[] = $value;
}

$gids = (array)unserialize($_G['cache']['plugin']['dsu_amucallme']['gids']);
if(in_array($_G['groupid'],$gids)){
	$callgids = (array)unserialize($_G['cache']['plugin']['dsu_amucallme']['callgids']);
	loadcache('usergroups');
	for($i=0;$i<=count($callgids);$i++){
		$value['groupid'] = $callgids[$i];
		$value['grouptitle'] = $_G['cache']['usergroups'][$callgids[$i]]['grouptitle'];
		$groups[] = $value;
	}
}
$costshow = '';
if(file_exists('./data/plugindata/dsu_amucallme.data.php')){
	require_once DISCUZ_ROOT.'./data/plugindata/dsu_amucallme.data.php';
	$data_f2a = dstripslashes($data_f2a);
	$cmcost = $data_f2a[$_G['groupid']];
	$cname = $cmcost['cost'].' '.$_G['setting']["extcredits"]["{$cmcost['extcredits']}"]['title'];
	if($cmcost['cost']>=1)$costshow = lang('plugin/dsu_amucallme','sf',array('cost' => $cname));
}
include template('dsu_amucallme:callme');

?>

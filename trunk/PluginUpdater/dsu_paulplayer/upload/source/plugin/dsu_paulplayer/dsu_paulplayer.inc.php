<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');
if(!empty($_G['gp_sreach'])){
$page = max(1, $_G['gp_page']);
$words = dhtmlspecialchars($_G['gp_keywords']);
if (CHARSET!='UTF-8') $words = diconv($words,CHARSET,'UTF-8');
$wordsen = urlencode($words);
$startline = ($page - 1) * 6;
$vars = 'http://search.1g1g.com/public/songs?encoding=utf-8&format=json&number=6&start='.$startline.'&query='.$wordsen;
$tries=0;
while($tries < 3 && ($get_things = file_get_contents($vars))===FALSE) $tries++;
if(!$get_things){
	include template('dsu_paulplayer:dsu_paulplayer_f');
	exit();
}
$get_things = json_decode($get_things,1);
$songs_count = count($get_things['songlist']);
if($songs_count){
$i = 0;
while($i<$songs_count){
	$songs[$i]['id'] = diconv($get_things['songlist'][$i]['song']['id'],'UTF-8');
	$songs[$i]['name'] = diconv($get_things['songlist'][$i]['song']['name'],'UTF-8');
	$songs[$i]['singer'] = diconv($get_things['songlist'][$i]['singer']['name'],'UTF-8');
	$songs[$i]['code'] = '[1g1g]'.$songs[$i]['name'].'-'.$songs[$i]['singer'].'#playID:'.$songs[$i]['id'].'[/1g1g]';
	$i++;
}
}
include template('dsu_paulplayer:dsu_paulplayer');
}else{
include template('dsu_paulplayer:dsu_paulplayer_window');
}
?>
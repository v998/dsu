<?php
!defined('IN_KKVIP') && exit('Access Denied');
$vip_intro_array=explode("\n",$vip->vars['vip_intro']);
foreach ($vip_intro_array as $text){
	$vip_intro.=$text?"<li>".$text."</li>\r\n":"";
}
include template('dsu_kkvip:mylevel');
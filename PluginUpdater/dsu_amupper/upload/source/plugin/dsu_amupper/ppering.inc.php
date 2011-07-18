<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

	$showmsg = lang('plugin/dsu_amupper','showmsg',array('name' => $_G['username']));
	include template('dsu_amupper:ppering');

?>

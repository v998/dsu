<?php
/*
	[DSU] Thief
	Author: Marco129[http://www.my3talk.com]
	Please respect the author, do not delete the copyright!
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
DB::query("UPDATE ".DB::table('dsu_marcothief')." SET action='0',actions='0'", 'UNBUFFERED');
?>
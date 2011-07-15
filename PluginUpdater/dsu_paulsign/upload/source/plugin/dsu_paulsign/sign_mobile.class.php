<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');
class mobileplugin_dsu_paulsign {
        function global_header_mobile(){
			global $_G,$show_message;
			function dsu_signtz() {
				dheader('Location: plugin.php?id=dsu_paulsign:sign&mobile=yes');
			}
			$var = $_G['cache']['plugin']['dsu_paulsign'];
			if(defined('IN_dsu_paulsign') || $show_message || defined('IN_dsu_paulsc') || !$_G['uid'] || !$var['ifopen'] || !$var['wap_sign']) return '';
			$fixtime = $_G['timestamp'] - (getglobal('member/timeoffset') - getglobal('setting/timeoffset'))*3600;
			$tdtime = gmmktime(0,0,0,dgmdate($fixtime, 'n'),dgmdate($fixtime, 'j'),dgmdate($fixtime, 'Y')) - (getglobal('setting/timeoffset') * 3600);
			$allowmem = memory('check');
			if($var['ftopen']  && in_array($_G['groupid'], unserialize($var['tzgroupid'])) && !in_array($_G['uid'],explode(",",$var['ban'])) && in_array($_G['groupid'], unserialize($var['groups']))) {
				if($allowmem && $var['mcacheopen']) $signtime = memory('get', 'dsu_pualsign_'.$_G['uid']);
				if(!$signtime){
					$qiandaodb = DB::fetch_first("SELECT time FROM ".DB::table('dsu_paulsign')." WHERE uid='$_G[uid]'");
					$signtime = $qiandaodb['time'];
					$htime = dgmdate($_G['timestamp'], 'H') - (getglobal('member/timeoffset') - getglobal('setting/timeoffset'));
					if($htime >= 24) $htime -= 24;
					if($qiandaodb){
						if($allowmem && $var['mcacheopen']) memory('set', 'dsu_pualsign_'.$_G['uid'], $qiandaodb['time'], 86400);
						if($qiandaodb['time'] < $tdtime){
							if($var['timeopen']) {
								if(!($htime < $var['stime']) && !($htime > $var['ftime'])) return dsu_signtz();
							}else{
								return dsu_signtz();
							}
						}
					}else{
						$ttps = DB::fetch_first("SELECT posts FROM ".DB::table('common_member_count')." WHERE uid='$_G[uid]'");
						if($var['mintdpost'] <= $ttps['posts']){
							if($var['timeopen']) {
								if(!($htime < $var['stime']) && !($htime > $var['ftime'])) return dsu_signtz();
							}else{
								return dsu_signtz();
							}
						}
					}
				}else{
					if($signtime < $tdtime){
						if($var['timeopen']) {
							if(!($htime < $var['stime']) && !($htime > $var['ftime']))return dsu_signtz();
						}else{
							return dsu_signtz();
						}
					}
				}
			}
			return '<a href="plugin.php?id=dsu_paulsign:sign">'.lang('plugin/dsu_paulsign', 'name').'</a>';
        }
}
?>
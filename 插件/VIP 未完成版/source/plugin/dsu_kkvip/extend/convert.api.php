<?php

$ext_name = '&#26087;&#29256;&#26412;&#25968;&#25454;&#23548;&#20837;&#24037;&#20855; By Kookxiang';		// extend's name, please convert it at he.kookxiang.com

if(defined('IN_ADMINCP') && $_G['gp_api']){
	if(submitcheck('submit') && $_G['gp_confirm']){
		function get_level($czz){
			if($czz < 600){
				return 1;
			}elseif($czz >= 600 && $czz < 1800){
				return 2;
			}elseif($czz >= 1800 && $czz < 3600){
				return 3;
			}elseif($czz >= 3600 && $czz < 6000){
				return 4;
			}elseif($czz >= 6000 && $czz < 10800){
				return 5;
			}elseif($czz >= 10800){
				return 6;
			}
		}
		$query = DB::query('SELECT * FROM '.DB::table('dsu_kkvip'));
		while($data = DB::fetch($query)){
			$data_arr = array();
			$data_arr['uid'] = $data['uid'];
			$data_arr['jointime'] = $data['regtime'];
			$data_arr['exptime'] = $data['endtime'];
			$data_arr['czz'] = $data['czz'];
			$data_arr['level'] = get_level($data['czz']);
			$data_arr['oldgroup'] = $data['oldgroup'];
			if(!empty($data_arr)) DB::insert('dsu_vip', $data_arr);
		}
		$query = DB::query('SELECT * FROM '.DB::table('dsu_kkvip_codes'));
		while($data = DB::fetch($query)){
			$data_arr = array();
			$data_arr['code'] = $data['code'];
			$data_arr['money'] = $data['money'];
			$data_arr['only_once'] = 1;
			$data_arr['exptime'] = TIMESTAMP + 864000*7;
			if(!empty($data_arr)) DB::insert('dsu_vip_codes', $data_arr);
		}
		cpmsg('&#25968;&#25454;&#23548;&#20837;&#23436;&#25104;&#65292;&#24863;&#35874;&#20351;&#29992;&#65281;', "action=plugins&operation=config&identifier=dsu_kkvip&pmod=api&api={$_G[gp_api]}", 'succeed');
		dexit();
	}
	showtableheader('&#26087;&#29256;&#26412;&#25968;&#25454;&#23548;&#20837;&#24037;&#20855; By Kookxiang');
	showformheader("plugins&operation=config&identifier=dsu_kkvip&pmod=api&api={$_G[gp_api]}");
	showsetting('&#20320;&#30830;&#35748;&#35201;&#36827;&#34892;&#25968;&#25454;&#35013;&#25442;&#65311;', 'confirm', false, 'radio', '', '', '&#29616;&#26377;&#25968;&#25454;&#23558;&#34987;&#28165;&#31354;&#65292;&#35831;&#35880;&#24910;&#36873;&#25321;');
	showsubmit('submit');
	showformfooter();
	showtablefooter();
}

?>
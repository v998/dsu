<?php
if(!defined('IN_ADMINCP')) exit('Access Denied');
require_once DISCUZ_ROOT.'./source/plugin/dsu_kkvip/kk_lang.func.php';
echo '<script src="static/js/calendar.js" type="text/javascript"></script>';
if (submitcheck('submit')){
	$del_arr=(array)$_G['gp_delete'];
	if($del_arr){
		foreach ($del_arr as $item){
			if($item) $del_ids.=$del_ids ? ",'{$item}'" : "'{$item}'";
		}
		if($del_ids) DB::delete('dsu_vip_codes', "code IN ({$del_ids})");
	}
	cpmsg(klang('delete_succeed'), 'action=plugins&operation=config&identifier=dsu_kkvip&pmod=discount', 'succeed');
}elseif(submitcheck('import')){
	$data = str_replace(array("\r\n", "\n", "\r"), '|', $_G['gp_import_data']);
	$code_array = (array)explode('|', $data);
	$count = 0;
	if(!empty($code_array)){
		$base_array = array(
			'money' => intval($_G['gp_money']),
			'only_once' => $_G['gp_allow_repeat'] ? 0 : 1,
			'exptime' => strtotime($_G['gp_code_exp']),
		);
		foreach ($code_array as $a_code){
			if($a_code){
				$base_array['code'] = $a_code;
				DB::insert('dsu_vip_codes', $base_array);
				++$count;
			}
		}
	}
	cpmsg(str_replace('{count}', $count, klang('import_succeed')), 'action=plugins&operation=config&identifier=dsu_kkvip&pmod=discount', 'succeed');
}
showformheader('plugins&operation=config&identifier=dsu_kkvip&pmod=discount');
showtableheader(klang('discount_manager'));
showsubtitle(explode('|', klang('discount_menu')));
$page=$_G['gp_page'] ? intval($_G['gp_page']) : 1;
$start=($page-1)*10;
$nowtime=TIMESTAMP;
$query=DB::query('SELECT * FROM '.DB::table('dsu_vip_codes')." WHERE exptime>='$nowtime' ORDER BY exptime DESC LIMIT {$start},10");
while($result=DB::fetch($query)){
	showtablerow('', array('class="td25"', 'class="td28"', 'class="td26"'), array(
		'<input type="checkbox" class="checkbox" name="delete[]" value="'.$result['code'].'" />',
		'<input type="text" onclick="this.select()" value="'.$result['code'].'" size="50" />',
		dgmdate($result['exptime'], 'dt'),
		$result['only_once'] ? '<img align="absmiddle" src="static/image/admincp/cloud/wrong.gif">' : '<img align="absmiddle" src="static/image/admincp/cloud/right.gif">',
		$result['money'],
	));
}
showsubmit('submit');
showtablefooter();
showformfooter();


showtableheader(klang('import_discount_code'));
showformheader('plugins&operation=config&identifier=dsu_kkvip&pmod=discount');
showsetting(klang('code_money'), 'money', 10, 'number');
showsetting(klang('code_exp'), 'code_exp', dgmdate(TIMESTAMP+86400, 'd'), 'calendar');
showsetting(klang('allow_repeat'), 'allow_repeat', false, 'radio');
showsetting(klang('import_data'), 'import_data', '', 'textarea', '', '', klang('import_tips'));
showsubmit('import', 'import');
showformfooter();
showtablefooter();

?>
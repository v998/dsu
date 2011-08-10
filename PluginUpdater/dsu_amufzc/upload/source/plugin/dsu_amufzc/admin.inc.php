<?php

/*
	dsu_amufzc admin BY °¢ÄÁ
*/
!defined('IN_DISCUZ') && exit('Access Denied');
!defined('IN_ADMINCP') && exit('Access Denied');


if(!$_G['gp_submit']){
	showtableheader(lang('plugin/dsu_amufzc','a2'));
	showformheader("plugins&operation=config&identifier=dsu_amufzc&pmod=admin&submit=1", "");
	shownav('plugin', lang('plugin/dsu_amufzc','a1'), lang('plugin/dsu_amufzc','a2'));
	showsetting(lang('plugin/dsu_amufzc','a3'), 'time', '1', 'text', '',0, lang('plugin/dsu_amufzc','a4'));
	showsetting(lang('plugin/dsu_amufzc','a9'), 'del', '1', 'radio');
	echo '<input type="hidden" name="formhash" value="'.FORMHASH.'">';
	showsubmit('submit', lang('plugin/dsu_amufzc','a5'));
	showformfooter();
	showtablefooter();
}elseif($_G['gp_submit'] && $_G['adminid']=='1' && $_G['gp_formhash']==FORMHASH){

	$times = intval($_G['gp_time']*60*60*24);
	$tablename = DB::table("plugin_dsuamfzc");
	$query = DB::query("SHOW TABLES LIKE '$tablename'");
	$sql = '';
	$sql_exist = 0;
	if(DB::num_rows($query) > 0){
		$sql_exist = 1;
	}
	if($sql_exist && $times){
		$times = TIMESTAMP - $times;
		if($_G['gp_del']){$wheres = " time <= ".$times;}else{$wheres = " yes = '0' AND time <= ".$times;}
		$num = DB::result_first("SELECT COUNT(*) FROM ".$tablename." WHERE ".$wheres);
		$sql="SELECT * FROM ".$tablename." WHERE ".$wheres." LIMIT 0 ,".$num;
		$querygg=DB::query($sql);
		$rid=array();
		
		while ($value=DB::fetch($querygg)){
			$rid[] = $value['rid'];
		}
		$rids = "'".implode("','", array_unique($rid))."'";
		DB::query("DELETE FROM ".DB::table("plugin_dsuamfzc")." WHERE rid IN ({$rids})");
		if($num){
			$sy = DB::result_first("SELECT COUNT(*) FROM ".$tablename." WHERE time > 0");
			$cp_message = lang('plugin/dsu_amufzc','a6', array('del' => $num,'sy' => $sy));
			cpmsg($cp_message, 'action=plugins&operation=config&identifier=dsu_amufzc&pmod=admin','succeed');
		}else{
			cpmsg('dsu_amufzc:a7', 'action=plugins&operation=config&identifier=dsu_amufzc&pmod=admin','succeed');
		}
	}else{
		cpmsg('dsu_amufzc:a8', 'action=plugins&operation=config&identifier=dsu_amufzc&pmod=admin','succeed');
	}

}
function array2txt($array, $offset = OFFSET_DELIMETER)  {
    $text = "";
    foreach($array as $k => $v) {
        if (is_array($v)) {
            $text .= "{$offset}'{$k}' => array(\n".array2txt($v, $offset.OFFSET_DELIMETER)."$offset)";
        } else {
            $text .= "{$offset}'{$k}' => ".(is_string($v)? "'$v'": $v);
        }
        $text .= ",\n";
    }	
    return $text;
}


?>
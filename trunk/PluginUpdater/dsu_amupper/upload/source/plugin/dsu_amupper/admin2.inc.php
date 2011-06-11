<?php
/*
	dsu_amupper admin BY 阿牧
*/
!defined('IN_DISCUZ') && exit('Access Denied');
!defined('IN_ADMINCP') && exit('Access Denied');
DEFINE('OFFSET_DELIMETER', "\t");
//公共部分
$file = './data/plugindata/dsu_amupper.data.php';
if(!$_G['gp_submit']){
	$exsel = extc2seled(0,$_G['setting']['extcredits']);
	loadcache('usergroups');
	$usergroups = $_G['cache']['usergroups'];
	$gidsel = usergroups2seled('-1',$usergroups);
	echo '<script type="text/JavaScript">
	var rowtypedata = [[
		[1,"", ""],
		[1,\'<input type="text" class="txt" name="days[]" size="7">\', ""],
		[1,\''.$gidsel.'\', ""],
		[1,\''.$exsel.'\', ""],
		[1,\'<input type="text" class="txt" name="reward[]" size="7">\', ""],
	]]
	</script>';
	showformheader('plugins&operation=config&identifier=dsu_amupper&pmod=admin2');
	showtips(lang("plugin/dsu_amupper","admin2_p1"));
	showtableheader(lang("plugin/dsu_amupper","admin2_h1"));
	showsubtitle(array(lang("plugin/dsu_amupper","admin2_t0"), lang("plugin/dsu_amupper","admin2_t1"), lang("plugin/dsu_amupper","admin2_t4"),lang("plugin/dsu_amupper","admin2_t2"), lang("plugin/dsu_amupper","admin2_t3")));
	if(file_exists($file)){
		require_once DISCUZ_ROOT.'./data/plugindata/dsu_amupper.data.php';
		//$data_f2a = file2array($file);
		$data_f2a =dstripslashes($data_f2a);
		//print_r($data_f2a);
		foreach ($data_f2a as $id => $result){
			$exsel = extc2seled($result['extcredits'],$_G['setting']['extcredits']);
			$gidsel = usergroups2seled($result['usergid'],$usergroups);
			showtablerow('', array(' ', ' ', ' ', ' '), array(
				'<input type="checkbox" class="checkbox" name="delete[]" value="'.$id.'" />',
				'<input type="text" class="txt" name="days[]" value="'.$result['days'].'" size="7" />',
				''.$gidsel.'',
				''.$exsel.'',
				'<input type="text" class="txt" name="reward[]" value="'.$result['reward'].'" size="7" />',
			));
		}
	}
	echo '<tr><td></td><td colspan="3"><div><a href="#addrow" name="addrow" onclick="addrow(this, 0)" class="addtr">'.lang("plugin/dsu_amupper","admin2_s1").'</a></div></td></tr>';
	showsubmit('submit', lang("plugin/dsu_amupper","admin2_s2"));
	showtablefooter();
	showformfooter();
}elseif($_G['adminid']=='1' && $_G['gp_formhash']==FORMHASH){
	$mrcs = array();
	//print_r($_G['gp_delete']);
	$max_i = max(count($_G['gp_days']), count($_G['gp_usergid']), count($_G['gp_extcredits']), count($_G['gp_reward']));
	for($i=0;$i<$max_i;$i++){
		if(intval($_G['gp_days'][$i]) && intval($_G['gp_extcredits'][$i]) && intval($_G['gp_usergid'][$i]) && intval($_G['gp_reward'][$i]*100) && !in_array($i,$_G['gp_delete'])){
			$mrcs[$i]['days']=intval($_G['gp_days'][$i]);
			$mrcs[$i]['usergid']=intval($_G['gp_usergid'][$i]);
			$mrcs[$i]['extcredits']=intval($_G['gp_extcredits'][$i]);
			$mrcs[$i]['reward']=intval($_G['gp_reward'][$i]*100)/100;
		}
	}
	usort($mrcs, "cmp");
	//array2file($file,$mrcs);print_r($mrcs);
	array2php($mrcs,$file,'data_f2a');
	cpmsg('dsu_amupper:admin2_i', 'action=plugins&operation=config&identifier=dsu_amupper&pmod=admin2','succeed');
}


//自定义函数
function usergroups2seled($id,$array){
	$extc_sel = '<select name="usergid[]">';
	if($id == '-1'){$extc_sel .='<option value="-1"  selected>'.lang("plugin/dsu_amupper","admin2_no").'</option>' ;}else{$extc_sel .='<option value="-1">'.lang("plugin/dsu_amupper","admin2_no").'</option>' ;}
	foreach($array as $i => $value){
		if($id == $i ){
			$extc_sel .='<option value="'.$i.'" selected>'.$value['grouptitle'].'</option>' ;
		}else{
			$extc_sel .='<option value="'.$i.'">'.$value['grouptitle'].'</option>' ;
		}
	}
	$extc_sel .= '</select>';
	return $extc_sel;
}
function extc2seled($id,$array){
	$extc_sel = '<select name="extcredits[]">';
	foreach($array as $i => $value){
		if($id == $i ){
			$extc_sel .='<option value="'.$i.'" selected>'.$value['title'].'</option>' ;
		}else{
			$extc_sel .='<option value="'.$i.'">'.$value['title'].'</option>' ;
		}
	}
	$extc_sel .= '</select>';
	return $extc_sel;
}
function array2file($file,$array){
    $fp = fopen($file, "wb");
    fwrite($fp, serialize($array));
    fclose($fp);
}

function file2array($file){
    if(!file_exists($file)){
        //echo " does no exist";
    }
    $handle=fopen($file,"rb");
    $contents=fread($handle,filesize($file));
    fclose($handle);
    return unserialize($contents);
}
function cmp($a, $b){
	if ($a["days"] == $b["days"] && $a["usergid"] <> $b["usergid"]) {
		return ($a["usergid"] > $b["usergid"]) ? 1 : -1;
	}elseif($a["days"] == $b["days"] && $a["usergid"] == $b["usergid"]){
		return 0;
	}
	 return ($a["days"] > $b["days"]) ? 1 : -1;
} 

function array2php($array,$file,$arrayname)  {
	$of = fopen($file,'w');
	if($of){
		$txt = array2txt($array);
		$text = "<?php\n\$".$arrayname." = array( \n".$txt.");\n?>";
		fwrite($of,$text);
	}
    return '';
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
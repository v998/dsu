<?php
/*
	dsu_amusign admin BY 阿牧
*/
!defined('IN_DISCUZ') && exit('Access Denied');
!defined('IN_ADMINCP') && exit('Access Denied');
//公共部分
$file = './data/plugindata/dsu_amusign.data.php';
require_once DISCUZ_ROOT.'./data/plugindata/dsu_amusign.lang.php';

$lang = $scriptlang['dsu_amusign'];
//if(file_exists(DISCUZ_ROOT.'./data/dsu_amusign.lock')) {
//	cpmsg('dsu_amusign:admin_ed', 'action=plugins&operation=config&identifier=dsu_amusign','succeed');
//	exit;
//} 
if(!$_G['gp_submit']){
echo '<script type="text/JavaScript">
var rowtypedata = [[
	[1,"", "td30"],
	[1,\'<input type="text" class="txt" name="days[]" size="7">\', "td35"],
	[1,\'<input type="text" class="txt" name="daycost[]" size="7">\', "td35"],
]]
</script>';
showformheader('plugins&operation=config&identifier=dsu_amusign&pmod=admin');
showtableheader(lang("plugin/dsu_amusign","admin_h1"));
showsubtitle(array(lang("plugin/dsu_amusign","admin_t0"), lang("plugin/dsu_amusign","admin_t1"), lang("plugin/dsu_amusign","admin_t2")));
if(file_exists($file)){
	$data_f2a = file2array($file);
	$data_f2a =dstripslashes($data_f2a);
	//print_r($data_f2a);
	foreach ($data_f2a as $id => $result){
		showtablerow('', array('class="td30"', 'class="td35"', 'class="td35"'), array(
			'<input type="checkbox" class="checkbox" name="delete[]" value="'.$id.'" />',
			'<input type="text" class="txt" name="days[]" value="'.$result['days'].'" size="7" />',
			'<input type="text" class="txt" name="daycost[]" value="'.$result['daycost'].'" size="7" />',
		));
	}
}
echo '<tr><td></td><td colspan="3"><div><a href="#addrow" name="addrow" onclick="addrow(this, 0)" class="addtr">'.lang("plugin/dsu_amusign","admin_s1").'</a></div></td></tr>';
showsubmit('submit', lang("plugin/dsu_amusign","admin_s2"));
showtablefooter();
showformfooter();
}elseif($_G['adminid']=='1' && $_G['gp_formhash']==FORMHASH){
	$mrcs = array();
	//print_r($_G['gp_delete']);
	$max_i = max(count($_G['gp_days']), count($_G['gp_daycost']));
	for($i=0;$i<$max_i;$i++){
		if(intval($_G['gp_days'][$i]) && intval($_G['gp_daycost'][$i]*100) && !in_array($i,$_G['gp_delete'])){
			$mrcs[$i]['days']=intval($_G['gp_days'][$i]);
			$mrcs[$i]['daycost']=intval($_G['gp_daycost'][$i]*100)/100;
		}
	}
	usort($mrcs, "cmp");
	array2file($file,$mrcs);
	cpmsg('dsu_amusign:admin_i', 'action=plugins&operation=config&identifier=dsu_amusign&pmod=admin','succeed');
}


//自定义函数
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
	if ($a["days"] == $b["days"]) return 0;
	 return ($a["days"] > $b["days"]) ? 1 : -1;
} 
?>
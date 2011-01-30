<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
dsetcookie('dsu_amupper_footer','',-1);
dsetcookie('dsu_amupper_header','',-1);

$dsu_amupperimage = $_G['cache']['plugin']['dsu_amupper']['image'];
if(!empty($_G['gp_ajax']) && $_G['uid']){
	$_G['gp_formhash'] =strip_tags($_G['gp_formhash']);
	$formhash = 0;
	if($_G['gp_formhash'] == $_G['formhash']){$formhash = 1;}

	if($formhash){
		dsu_amupper();
	}
	dexit;
}

function special_reward($i){
	global $_G;
	$file = './data/plugindata/dsu_amupper.data.php';$return = '';
	if(file_exists($file)){
		require_once DISCUZ_ROOT.'./data/plugindata/dsu_amupper.data.php';
		$data_f2a =dstripslashes($data_f2a);
		$minday = -1;
		if($_G['cache']['plugin']['dsu_amupper']['periodicity']){
			foreach ($data_f2a as $id => $result){
				if($i/$result['days'] == intval($i/$result['days']) && ($_G['groupid']==$result['usergid'] || $result['usergid'] == '-1')){
					$return = $result;					
				}
			}
		}else{
			foreach ($data_f2a as $id => $result){
				if($i == $result['days'] && ($_G['groupid']==$result['usergid'] || $result['usergid'] == '-1')){
					$return = $result;
				}
			}
		}
		if($return){
			updatemembercount($_G['uid'], array("extcredits{$return['extcredits']}" => $return['reward']), true,'',0);
			$return['extcredits'] = $_G['setting']['extcredits'][$return['extcredits']]['title'].'&nbsp;';
			$return = lang('plugin/dsu_amupper','special_reward',array('rewards' => $return['reward'],'extcredits' => $return['extcredits']));
		}
	}
	return $return;
}

function special_rewardts($i){
	global $_G;
	$file = './data/plugindata/dsu_amupper.data.php';$return = '';
	if(file_exists($file)){
		require_once DISCUZ_ROOT.'./data/plugindata/dsu_amupper.data.php';
		$data_f2a =dstripslashes($data_f2a);
		$minday = -1;
		if($_G['cache']['plugin']['dsu_amupper']['periodicity']){
			foreach ($data_f2a as $id => $result){
				if($_G['groupid']==$result['usergid'] || $result['usergid'] == '-1'){
					$day = $result['days'] - $i % $result['days'];
					if($minday <0 || $minday > $day){
						$minday = $day;
						$return = $result;	
					}
				}
			}
		}else{
			foreach ($data_f2a as $id => $result){
				if($i == $result['days'] && ($_G['groupid']==$result['usergid'] || $result['usergid'] == '-1')){
					$day = $result['days'] - $i;
					if($minday <0 || $minday > $day){
						$minday = $day;
						$return = $result;	
					}
				}
			}
		}
		if($return && $minday > 0){
			$return['extcredits'] = $_G['setting']['extcredits'][$return['extcredits']]['title'].'&nbsp;';
			$return = lang('plugin/dsu_amupper','specialreward_ts',array('minday' => $minday,'reward' => $return['reward'],'extcredits' => $return['extcredits']));
		}
	}
	return $return;
}


function dsu_amupper(){
	global $_G;
	$today = dgmdate($_G['timestamp'],'Ymd',8);
	$yesterday = dgmdate($_G['timestamp']-86400,'Ymd',8);
	$tomorrow = dgmdate($_G['timestamp']+86400,'Ymd',8);
	$cdb_pper = array();
	$cdb_pper['uid'] = intval($_G['uid']);
	$cdb_pper['lasttime'] = intval($_G['timestamp']);
	$cdb_pper['continuous'] = intval(1);
	$cdb_pper['addup'] = intval(1);
	$query = array();
	$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuampper")." WHERE uid = '{$cdb_pper['uid']}'");
	if($query){
		$lasttime =  dgmdate($query['lasttime'],'Ymd',8);
		if($lasttime == $yesterday){
			$show['continuous'] = $cdb_pper['continuous'] = 1 + $query['continuous'];
			$show['addup'] = $cdb_pper['addup'] = 1 + $query['addup'];
			$reward_if = 1;//是否正常签到参数
		}elseif(($lasttime < $yesterday)){
			$show['continuous'] = $cdb_pper['continuous'] = 1;
			$show['addup'] = $cdb_pper['addup'] = 1 + $query['addup'];
			$reward_if = 1;
		}else{
			$show['continuous'] = $cdb_pper['continuous'] = $query['addup'];
			$show['addup'] = $cdb_pper['addup'] = $query['addup'];
		}
	}else{
		$show['continuous'] = $cdb_pper['continuous'] = 1;
		$show['addup'] = $cdb_pper['addup'] = 1;
		$reward_if = 2;
	}
	if($reward_if){
		$extcredit = $_G['cache']['plugin']['dsu_amupper']['extcredit'];
		$base = $_G['cache']['plugin']['dsu_amupper']['base'];
		$supreme = $_G['cache']['plugin']['dsu_amupper']['supreme'];
		$continuous = $cdb_pper['continuous'];
		if($reward_if == 1){
			DB::query("UPDATE ".DB::table('plugin_dsuampper')." SET lasttime = '{$cdb_pper['lasttime']}' , continuous = '{$cdb_pper['continuous']}' , addup = '{$cdb_pper['addup']}' WHERE uid = '{$cdb_pper['uid']}'");
		}elseif($reward_if == 2){
			DB::insert('plugin_dsuampper',$cdb_pper);
		}
		

		$reward = intval($continuous * $continuous * 0.09 + $base);
		$continuous_next = $continuous + 1;
		
		if($supreme < $reward){$reward = $supreme;}//判断是否超过最大值
		
		$spre = special_reward($continuous);
		updatemembercount($_G['uid'], array("extcredits{$extcredit}" => $reward), true,'',0);
		$rewards = $_G['setting']['extcredits'][$extcredit]['title'].'&nbsp;'.$reward;
		$reward_next = intval($continuous_next * $continuous_next * 0.09 + $base);
		if($supreme < $reward_next){$reward_next = $supreme;}
		$reward_next = $_G['setting']['extcredits'][$extcredit]['title'].'&nbsp;'.$reward_next;
		if($_G['cache']['plugin']['dsu_amupper']['reward_notice']){notification_add($_G['uid'], 'amupper', lang('plugin/dsu_amupper','notice',array('rewards' => $rewards,'reward_next' => $reward_next,'continuous' => $continuous,'special_reward' => $spre)), '', 1);}
		include template('dsu_amupper:ajax');
	}else{
		include template('dsu_amupper:ajax');
	}

	$return = '<div id="ppered_menu" style="display:none;width:240px;">
				<p class="crly">
				<SPAN class="y xg1" ><a href="plugin.php?id=dsu_amupper:list" target="_blank">'.lang("plugin/dsu_amupper","menu2").'</a></SPAN>
				<strong>'.lang("plugin/dsu_amupper","menu1").'</strong>:'.$cdb_pper['addup'].'
				<br>'.lang("plugin/dsu_amupper","menu3").':'.$cdb_pper['continuous'] .'
				<br>
				<strong>'.lang("plugin/dsu_amupper","info2").'</strong>:'.$lasttime;

	$return .= '<br><font color="red">'.special_rewardts($cdb_pper['continuous']).'</font>';

	$return .= '</p></div>';
	dsetcookie('dsu_amupper_footer', base64_encode($return), 3600);
	return;
}
?>
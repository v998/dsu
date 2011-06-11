<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
dsetcookie('dsu_amupper_footer','',-1);
dsetcookie('dsu_amupper_header','',-1);
DEFINE('OFFSET_DELIMETER', "\t");
$dsu_amupperimage = $_G['cache']['plugin']['dsu_amupper']['image'];
$dsu_amupperlcolor = $_G['cache']['plugin']['dsu_amupper']['color'];
$file = './data/plugindata/dsu_amupper.data.php';$return = '';
if(file_exists($file)){
	require_once DISCUZ_ROOT.'./data/plugindata/dsu_amupper.data.php';
	$data_f2a =dstripslashes($data_f2a);
}
$tone = $_G['cache']['plugin']['dsu_amupper']['tone'];
$return_txt = '<a id="my_amupper" href="javascript:;" onclick="ajaxget(\'plugin.php?id=dsu_amupper:pper&ajax=1&formhash='.$_G['formhash'].'\', \'my_amupper\', \'my_amupper\', \''.lang("plugin/dsu_amupper","ppering").'\', \'\',function () {toneplayer('.$tone.');});">'.lang("plugin/dsu_amupper","pper0",array('hcolor'=>$_G['cache']['plugin']['dsu_amupper']['hcolor'])).'</a>';
$return_img = '<a id="my_amupper" href="javascript:;" onclick="ajaxget(\'plugin.php?id=dsu_amupper:pper&ajax=1&formhash='.$_G['formhash'].'\', \'my_amupper\', \'my_amupper\', \''.lang("plugin/dsu_amupper","ppering").'\', \'\',function () {toneplayer('.$tone.');});">'.lang("plugin/dsu_amupper","pper1").'</a>';


if(!empty($_G['gp_ajax']) && $_G['uid']){
	$_G['gp_formhash'] =strip_tags($_G['gp_formhash']);
	$formhash = 0;
	if($_G['gp_formhash'] == $_G['formhash']){$formhash = 1;}
	if($formhash){
		$dsu_amuppercked = dsu_amupper();
		if($dsu_amupperimage){
			$returnajax = $return_img;
		}else{
			$returnajax = $return_txt;
		}
		include template('dsu_amupper:ajax');
	}
}


function special_reward($i){
	global $_G,$data_f2a;
	$file = './data/plugindata/dsu_amupper.data.php';$return = '';
	if(file_exists($file)){
		$minday = -1;
		if($_G['cache']['plugin']['dsu_amupper']['periodicity']){
			foreach ($data_f2a as $id => $result){
				if($i % $result['days'] == 0 && ($_G['groupid']==$result['usergid'] || $result['usergid'] == '-1')){
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
			$return['extcredits'] = $_G['setting']['extcredits'][$return['extcredits']]['title'];
			$return = lang('plugin/dsu_amupper','special_reward',array('rewards' => $return['reward'],'extcredits' => $return['extcredits']));
		}
	}

	return $return;
}

function special_rewardts($i){
	global $_G,$data_f2a;
	$file = './data/plugindata/dsu_amupper.data.php';$return = '';
	if(file_exists($file)){
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
			$return['extcredits'] = $_G['setting']['extcredits'][$return['extcredits']]['title'];
			$return = lang('plugin/dsu_amupper','specialreward_ts',array('minday' => $minday,'reward' => $return['reward'],'extcredits' => $return['extcredits']));
		}
	}
	return $return;
}



function dsu_amupper(){
	global $_G;
	$processname = 'dsu_amu_pper';
	$ywzx = discuz_process::islocked($processname, 600);
	if($ywzx) {
		sleep(1);
		return 0;
	}
	$today = dgmdate($_G['timestamp'],'Ymd',8);
	$time = dgmdate($_G['timestamp'],'Y-m-d',8);
	$times = dgmdate($_G['timestamp'],'Y-m-d H:i:s',8);
	$yesterday = dgmdate($_G['timestamp']-86400,'Ymd',8);
	$tomorrow = dgmdate($_G['timestamp']+86400,'Ymd',8);
	$cdb_pper = array();
	$cdb_pper['uid'] = intval($_G['uid']);
	$cdb_pper['lasttime'] = intval($_G['timestamp']);
	$cdb_pper['continuous'] = intval(1);
	$cdb_pper['addup'] = intval(1);
	$query = array();
	$reward_if = 0;
	$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuampper")." WHERE uid = '{$cdb_pper['uid']}'");
	if($query){
		$lasttime =  dgmdate($query['lasttime'],'Ymd',8);
		if($lasttime == $yesterday || $_G['cache']['plugin']['dsu_amupper']['liwai']){
			$show['continuous'] = $cdb_pper['continuous'] = 1 + $query['continuous'];
			$show['addup'] = $cdb_pper['addup'] = 1 + $query['addup'];
			$reward_if = 1;//是否正常签到参数
		}elseif($lasttime < $yesterday && !$_G['cache']['plugin']['dsu_amupper']['liwai']){
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
		$reward_if = 2;//是否是首次签到
	}
	$spre_msg = special_rewardts($cdb_pper['continuous']);
	$return = '<div id="ppered_menu" style="display:none;width:240px;">
		<p class="crly" style="padding:6px 8px;border:1px solid #CDCDCD;background:#F2F2F2;line-height:1.6em;">
		<SPAN class="y xg1" ><a href="plugin.php?id=dsu_amupper:list" target="_blank">'.lang("plugin/dsu_amupper","menu2").'</a></SPAN>
		<strong>'.lang("plugin/dsu_amupper","menu1").'</strong>:'.$cdb_pper['addup'].'
		<br>'.lang("plugin/dsu_amupper","menu3").':'.$cdb_pper['continuous'] .'
		<br>
		<strong>'.lang("plugin/dsu_amupper","info2").'</strong>:'.$lasttime;

	$return .= '<br><font color="red">'.$spre_msg.'</font>';

	$return .= '</p></div>';
	
	dsetcookie('dsu_amupper_footer', base64_encode($return), 3600);
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
		$rewards = $_G['setting']['extcredits'][$extcredit]['title'].$reward;
		$reward_next = intval($continuous_next * $continuous_next * 0.09 + $base);
		if($supreme < $reward_next){$reward_next = $supreme;}
		$reward_next = $_G['setting']['extcredits'][$extcredit]['title'].$reward_next;
		if($_G['cache']['plugin']['dsu_amupper']['reward_notice']){
			notification_add($_G['uid'], 'amupper', lang('plugin/dsu_amupper','notice',array('rewards' => $rewards,'reward_next' => $reward_next,'continuous' => $continuous,'special_reward' => $spre)), '', 1);
		}
		$logid = $_G['cache']['plugin']['dsu_amupper']['logid'];
		$file2 = './data/plugindata/dsu_amupper.log.php';
		if(file_exists($file2)){
			require_once DISCUZ_ROOT.'./data/plugindata/dsu_amupper.log.php';
		}
		if($logid){
			if($log_f2a['day'] <> $today){
				$log_subject = lang('plugin/dsu_amupper','logsj',array('day' => $time));
				$log_message = lang('plugin/dsu_amupper','msg',array('username' => $_G['username'],'time' => $times,'rewards' => $rewards,'reward_next' => $reward_next,'continuous' => $continuous,'special_reward' => $spre))."\n".$spre_msg;
				$log_tid = addnewtid($logid,$log_subject,$log_message);
				$log_data['day'] = $today;$log_data['tid'] = $log_tid;
				array2php($log_data,$file2,'log_f2a');

			}else{
				$log_subject = lang('plugin/dsu_amupper','logsj',array('day' => $time));
				$log_message = lang('plugin/dsu_amupper','msg',array('username' => $_G['username'],'time' => $times,'rewards' => $rewards,'reward_next' => $reward_next,'continuous' => $continuous,'special_reward' => $spre))."\n".$spre_msg;
				addnewpid($logid,$log_f2a['tid'],$log_subject,$log_message);
				
			}
			
		}
		
	}
	discuz_process::unlock($processname);
	return true;
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

function addnewtid($fid,$subject,$message){
	global $_G;
	if($_G['uid'] && $fid && $subject && $message){
		require_once libfile('function/forum');
		$tid=insertpost(array(
			'fid'=>$fid,
			'first'=>'1',
			'author'=>$_G['username'],
			'authorid'=>$_G['uid'],
			'subject'=>$subject,
			'dateline'=>$_G['timestamp'],
			'message'=>$message,
			'useip'=>$_G['clientip']));
		if($tid){
			DB::insert('forum_thread', array(
				'tid'=>$tid,
				'fid'=>$fid,
				'author'=>$_G['username'],
				'authorid'=>$_G['uid'],
				'subject' =>$subject,
				'dateline' => $_G['timestamp'],
				'lastpost' => $_G['timestamp'],
				'lastposter'=>$_G['username'],
				'closed'=>1));
			DB::query('UPDATE '.DB::table('forum_post')." SET tid=pid,tags='' WHERE pid=".$tid);
			$lastpost = "$tid\t".addslashes($subject)."\t$_G[timestamp]\t$_G[username]";
			DB::query('UPDATE '.DB::table('forum_forum')." SET threads=threads+'1', todayposts=todayposts+1, lastpost='{$lastpost}' WHERE fid=".$fid);
			//file_put_contents("test.txt",$tid);
		}
	}
	
	return $tid;
}

function addnewpid($fid,$tid,$subject='',$message){
	global $_G;
	if($_G['uid'] && $fid && $tid && $message){
		require_once libfile('function/forum');
		$pid=insertpost(array(
			'fid'=>$fid,
			'tid'=>$tid,
			'first'=>'0',
			'author'=>$_G['username'],
			'authorid'=>$_G['uid'],
			'subject'=>$subject,
			'dateline'=>$_G['timestamp'],
			'message'=>$message,
			'useip'=>$_G['clientip']));
		if($pid){
			DB::query("UPDATE ".DB::table('forum_thread')." SET lastposter='$_G[username]', lastpost='$_G[timestamp]', replies=replies+1 WHERE tid='$tid' AND fid='$fid'", 'UNBUFFERED');
			$lastpost = "$tid\t".addslashes($subject)."\t$_G[timestamp]\t$_G[username]";
			DB::query("UPDATE ".DB::table('forum_forum')." SET lastpost='$lastpost', posts=posts+1, todayposts=todayposts+1 WHERE fid='$fid'", 'UNBUFFERED');
			file_put_contents("test.txt",$pid);
		}
	}
}
?>


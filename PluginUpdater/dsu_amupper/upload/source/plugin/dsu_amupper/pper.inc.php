<?php
DEFINE('OFFSET_DELIMETER', "\t");
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$file = './data/plugindata/dsu_amupper.data.php';$return = '';
if(file_exists($file)){
	require_once DISCUZ_ROOT.'./data/plugindata/dsu_amupper.data.php';
	$data_f2a =dstripslashes($data_f2a);
}

if(!empty($_G['uid'])){
	$processname = 'dsu_amupper';
	discuz_process::unlock($processname);
	if(discuz_process::islocked($processname, 50)) {
		showmessage('dsu_amupper:wrong', '', array(), array('showdialog' => true, 'alert' => 'error', 'closetime' => true));
	}else{
		$ppereturn = dsu_amupper();
		discuz_process::unlock($processname);
		if($ppereturn){
			$ppereturn['message'];
			if($ppereturn['tid'] && $ppereturn['pid']){
				$url = "forum.php?mod=redirect&goto=findpost&ptid={$ppereturn['tid']}&pid={$ppereturn['pid']}";
				showmessage($ppereturn['message']."<br><b><a href='".$url."'>".lang('plugin/dsu_amupper','goto')."</a></b>", $url, array(), array('showmsg' => true,'alert' => 'right', 'closetime' =>5));
			}else{
				showmessage($ppereturn['message'], $url, array(), array('showmsg' => true,'alert' => 'right', 'closetime' =>8));
			}
		}else{
			showmessage('dsu_amupper:wrong', '', array(), array('showdialog' => true, 'alert' => 'error', 'closetime' => true));
		}
	}
}




function dsu_amupper(){
	global $_G;
	$thisvars['offset'] = $_G['setting']['timeoffset'];
	$thisvars['today'] = dgmdate($_G['timestamp'],'Ymd',$thisvars['offset']);
	$thisvars['yesterday'] = dgmdate($_G['timestamp']-86400,'Ymd',$thisvars['offset']);
	$thisvars['tomorrow'] = dgmdate($_G['timestamp']+86400,'Ymd',$thisvars['offset']);
	$time = dgmdate($_G['timestamp'],'Y-m-d',$thisvars['offset']);
	$times = dgmdate($_G['timestamp'],'Y-m-d H:i:s',$thisvars['offset']);

	
	$cdb_pper = array();
	$cdb_pper['uid'] = intval($_G['uid']);
	$cdb_pper['lasttime'] = intval($_G['timestamp']);
	$cdb_pper['continuous'] = intval(1);
	$cdb_pper['addup'] = intval(1);
	$query = array();
	$reward_if = 0;

	$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuampper")." WHERE uid = '{$cdb_pper['uid']}'");
	if($query){
		$lasttime =  dgmdate($query['lasttime'],'Ymd',$thisvars['offset']);
		if($lasttime == $thisvars['yesterday'] || ($lasttime <= $thisvars['yesterday'] && $_G['cache']['plugin']['dsu_amupper']['liwai'])){
			$show['continuous'] = $cdb_pper['continuous'] = 1 + $query['continuous'];
			$show['addup'] = $cdb_pper['addup'] = 1 + $query['addup'];
			$reward_if = 1;//是否正常签到参数
		}elseif($lasttime < $thisvars['yesterday'] && !$_G['cache']['plugin']['dsu_amupper']['liwai']){
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
		$log_subject = lang('plugin/dsu_amupper','logsj',array('day' => $time));
		$log_message = lang('plugin/dsu_amupper','notice',array('rewards' => $rewards,'reward_next' => $reward_next,'continuous' => $continuous,'special_reward' => $spre))."\n".$spre_msg;
		$show_out['message'] = lang('plugin/dsu_amupper','notice',array('rewards' => $rewards,'reward_next' => $reward_next,'continuous' => $continuous,'special_reward' => $spre));
		$logid = $_G['cache']['plugin']['dsu_amupper']['logid'];
		if($logid){
			$querylog = DB::fetch_first("SELECT * FROM ".DB::table("forum_thread")." WHERE subject = '{$log_subject}'");
			if(!$querylog['tid'] || $querylog['fid'] <> $logid){
				$show_out['tid'] = addnewtid($logid,$log_subject,$log_message);
				$log_data['day'] = $thisvars['today'];$log_data['tid'] = $show_out['tid'];
			}else{
				$show_out['tid'] = $querylog['tid'];
				$show_out['pid'] = addnewpid($logid,$querylog['tid'],$log_subject,$log_message);	
			}
			
		}
		dsetcookie('dsu_amupper_footer'.$_G['uid'],$_G['timestamp'],600);
		
		return $show_out;
	}else{
		return false;
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

		DB::insert('forum_thread', array(
			'fid'=>$fid,
			'author'=>$_G['username'],
			'authorid'=>$_G['uid'],
			'subject' =>$subject,
			'dateline' => $_G['timestamp'],
			'lastpost' => $_G['timestamp'],
			'lastposter'=>$_G['username'],
			'closed'=>1));

		$tid = DB::insert_id();

		if($tid){
			$pid=insertpost(array(
				'fid'=>$fid,
				'tid' => $tid,
				'first'=>'1',
				'author'=>$_G['username'],
				'authorid'=>$_G['uid'],
				'subject'=>$subject,
				'dateline'=>$_G['timestamp'],
				'message'=>$message,
				'useip'=>$_G['clientip']));
			//DB::query('UPDATE '.DB::table('forum_post')." SET tid=$tid,tags='' WHERE pid=".$pid);
			$lastpost = "$tid\t".addslashes($subject)."\t$_G[timestamp]\t$_G[username]";
			DB::query('UPDATE '.DB::table('forum_forum')." SET threads=threads+'1', todayposts=todayposts+1, lastpost='{$lastpost}' WHERE fid=".$fid);
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
			return $pid;
		}
	}
	
}

?>



<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


$thisemail = $_G['cache']['plugin']['dsu_amufzc']['email'];
$thisonlyone = $_G['cache']['plugin']['dsu_amufzc']['onlyone'];
$thismaxtime = !empty($_G['cache']['plugin']['dsu_amufzc']['maxtime']) ? $_G['cache']['plugin']['dsu_amufzc']['maxtime'] : '30';
$thisregname = !empty($_G['setting']['regname']) ? $_G['setting']['regname'] : 'register';
//email验证
if(!function_exists('sendmail')) {
	include libfile('function/mail');
}
$inputname = $_G['setting']['reginput'];
$_G['gp_email'] = getEmail(strip_tags($_G['gp_email']));
$input[1] = $_G['gp_username'];
$input[2] = $_G['gp_password'];
$input[3] = $_G['gp_password2'];
$input[4] = $_G['gp_email'];
//var_dump($input);
$inputse = serialize($input);
$_G['gp_activationauth'] = strip_tags(str_replace(" ", "+", $_G['gp_activationauth']));
if(!$_G['gp_email']){
	IF($_G['gp_activationauth']){
		$activationinfo = authcode($_G['gp_activationauth'], $operation = 'DECODE');
		$activationinfoname = preg_replace("/\s.+/i","",$activationinfo);
		if($activationinfoname){
			loaducenter();
			if($data = uc_get_user($activationinfoname)) {
				list($uid, $username, $email) = $data;
			}
			$randid = randStr(8);
			$insert['rid'] = $randid;
			$insert['time'] = TIMESTAMP;
			$insert['email'] = getEmail(strip_tags($email));
			if($insert['email']){
				DB::insert('plugin_dsuamfzc',$insert);
				if($thisemail){
					$return = "{$_G['siteurl']}member.php?mod={$thisregname}&rid={$randid}";
					$email_message = lang('plugin/dsu_amufzc','email_message', array('bbname' => $_G['setting']['bbname'],'siteurl' => $_G['siteurl'],'url' => $return,'code' => $randid));
					$email_subject = trim(lang('plugin/dsu_amufzc','email_subject'));
					$succeed = sendmail("$insert[email] <$insert[email]>", $email_subject, $email_message);
					if($succeed) {
						dsetcookie('dsu_amufzc_'.$randid, base64_encode($inputse), 6000);
						$randid = lang('plugin/dsu_amufzc','10');
						$topmsg = '<FONT COLOR="#FF0000"><B>'.lang('plugin/dsu_amufzc','93').'</B>('.$insert['email'].')</FONT>';
					} else {
						$topmsg = '<FONT COLOR="#FF0000"><B>'.lang('plugin/dsu_amufzc','931').'</B>('.$insert['email'].')</FONT>';
					}
				}
				include template('dsu_amufzc:ajax');
			}else{
				$randid = lang('plugin/dsu_amufzc','10');
				$topmsg = '<FONT COLOR="#FF0000"><B>'.lang('plugin/dsu_amufzc','13').'</B></FONT>['.strip_tags($query['email']).']';
				include template('dsu_amufzc:ajax');
			}
		}else{
			$randid = lang('plugin/dsu_amufzc','10');
			$topmsg = '<FONT COLOR="#FF0000"><B>'.lang('plugin/dsu_amufzc','12').'</B></FONT>';
			include template('dsu_amufzc:ajax');
		}
	}ELSE{
		$topmsg = '<FONT COLOR="#FF0000"><B>'.lang('plugin/dsu_amufzc','9').'</B></FONT>';
		include template('dsu_amufzc:ajax');

	}
}else{
	loaducenter();
	$ucresult = uc_user_checkemail($_G['gp_email']);
	IF($ucresult == '-6'){
		$topmsg = '<FONT COLOR="#FF0000"><B>'.lang('plugin/dsu_amufzc','92').'</B></FONT>';
		include template('dsu_amufzc:ajax');
	}ELSE{
		if($thisonlyone){
			$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuamfzc")." WHERE email = '".$_G['gp_email']."' AND yes = '1'");
			if($query){
				$topmsg = '<FONT COLOR="#FF0000"><B>'.lang('plugin/dsu_amufzc','92').'</B></FONT>';
				include template('dsu_amufzc:ajax');
				$query = '';
			}else{
				//获取注册码
				$randid = randStr(8);
				$insert['rid'] = $randid;
				$insert['time'] = TIMESTAMP;
				$insert['email'] = $_G['gp_email'];
				DB::insert('plugin_dsuamfzc',$insert);
				if($thisemail){
					$return = "{$_G['siteurl']}member.php?mod={$thisregname}&rid={$randid}";
					$email_message = lang('plugin/dsu_amufzc','email_message', array('bbname' => $_G['setting']['bbname'],'siteurl' => $_G['siteurl'],'url' => $return,'code' => $randid));
					$email_subject = trim(lang('plugin/dsu_amufzc','email_subject'));
					$succeed = sendmail("$_G[gp_email] <$_G[gp_email]>", $email_subject, $email_message);
					if($succeed) {
						dsetcookie('dsu_amufzc_'.$randid, base64_encode($inputse), 6000);
						$randid = lang('plugin/dsu_amufzc','10');
						$topmsg = '<FONT COLOR="#FF0000"><B>'.lang('plugin/dsu_amufzc','93').'</B></FONT>';
					} else {
						$topmsg = '<FONT COLOR="#FF0000"><B>'.lang('plugin/dsu_amufzc','931').'</B>('.$insert['email'].')</FONT>';
					}
				}
				include template('dsu_amufzc:ajax');
			}
		}else{
			$maxtime = TIMESTAMP - $thismaxtime;
			$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuamfzc")." WHERE email = '".$_G['gp_email']."' AND time > '".$maxtime."'");
			if($query){
				$maxtime = $thismaxtime - TIMESTAMP + $query['time'];
				$topmsg = '<FONT COLOR="#FF0000"><B>'.lang('plugin/dsu_amufzc','91', array('maxtime' => $maxtime)).'</B></FONT>';
				include template('dsu_amufzc:ajax');
				$query = '';
			}else{
				//获取注册码
				$randid = randStr(8);
				$insert['rid'] = $randid;
				$insert['time'] = TIMESTAMP;
				$insert['email'] = $_G['gp_email'];
				DB::insert('plugin_dsuamfzc',$insert);
				if($thisemail){
					$return = "{$_G['siteurl']}member.php?mod={$thisregname}&rid={$randid}";
					$email_message = lang('plugin/dsu_amufzc','email_message', array('bbname' => $_G['setting']['bbname'],'siteurl' => $_G['siteurl'],'url' => $return,'code' => $randid));
					$email_subject = trim(lang('plugin/dsu_amufzc','email_subject'));
					$succeed = sendmail("$_G[gp_email] <$_G[gp_email]>", $subject, $email_message);
					if($succeed) {
						dsetcookie('dsu_amufzc_'.$randid, base64_encode($inputse), 6000);
						$randid = lang('plugin/dsu_amufzc','10');
						$topmsg = '<FONT COLOR="#FF0000"><B>'.lang('plugin/dsu_amufzc','4',array('email' => $_G['gp_email'])).'</B></FONT>';
					} else {
						$topmsg = '<FONT COLOR="#FF0000"><B>'.lang('plugin/dsu_amufzc','931').'</B>('.$insert['email'].')</FONT>';
					}
				}
				include template('dsu_amufzc:ajax');
			}
		}
	}
}


function randStr($i){
	$str = "123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ";
	$finalStr = "";
	for($j=0;$j<$i;$j++)
	{
		$finalStr .= substr($str,rand(0,59),1);
	}
	$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuamfzc")." WHERE rid = '".$finalStr."'");
	if($query){randStr($i);}
	return $finalStr;
}

function getEmail($str) { 
	$pattern = "/[a-z0-9]([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i";//为了适合qq的数字邮箱,正则开头作了修改 
	preg_match_all($pattern,$str,$emailArr);
	if($emailArr[0][0]){$return = $emailArr[0][0];}else{$return = '';}
	return $return; 
}
?>
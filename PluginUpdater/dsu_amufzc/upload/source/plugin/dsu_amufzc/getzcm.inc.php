<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require './data/plugindata/dsu_amufzc.lang.php';
$thislang = $scriptlang['dsu_amufzc'];

$thisemail = $_G['cache']['plugin']['dsu_amufzc']['email'];
$thisonlyone = $_G['cache']['plugin']['dsu_amufzc']['onlyone'];
$thismaxtime = !empty($_G['cache']['plugin']['dsu_amufzc']['maxtime']) ? $_G['cache']['plugin']['dsu_amufzc']['maxtime'] : '30';
$thisregname = !empty($_G['setting']['regname']) ? $_G['setting']['regname'] : 'register';
//email验证
include libfile('function/mail');
$_G['gp_email'] = getEmail(strip_tags($_G['gp_email']));
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
					$succeed = sendmail("$insert[email]<$insert[email]>", $email_subject, $email_message);
					if($succeed) {
						$randid = '';
						echo '<FONT COLOR="#FF0000"><B>'.$thislang['93'].'</B>('.$insert['email'].')</FONT><BR>';
					} else {
						echo '<FONT COLOR="#FF0000"><B>'.$thislang['931'].'</B>('.$insert['email'].')</FONT><BR>';
					}
				}
				echo '<label class="xs2"><em>'.$thislang['3'].':</em><input type="text" id="rid" name="rid" autocomplete="off" size="25" maxlength="15" value="'.$randid.'" class="txt"> *</label>';
			}else{
				$randid = '';
				echo '<FONT COLOR="#FF0000"><B>'.$thislang['13'].'</B></FONT>['.strip_tags($query['email']).']<BR><label class="xs2"><em>'.$thislang['3'].':</em><input type="text" id="rid" name="rid" autocomplete="off" size="25" maxlength="15" value="'.$randid.'" class="txt"> *</label><A HREF="javascript:;" onClick="getzcm();return false;">'.$thislang['7'].'</A>'.$thislang['8'].'<script src="source/plugin/dsu_amufzc/getzcm.js?'.VERHASH.'" type="text/javascript"></script> ';
			}
		}else{
			$randid = '';
			echo '<FONT COLOR="#FF0000"><B>'.$thislang['12'].'</B></FONT><BR><label class="xs2"><em>'.$thislang['3'].':</em><input type="text" id="rid" name="rid" autocomplete="off" size="25" maxlength="15" value="'.$randid.'" class="txt"> *</label><A HREF="javascript:;" onClick="getzcm();return false;">'.$thislang['7'].'</A>'.$thislang['8'].'<script src="source/plugin/dsu_amufzc/getzcm.js?'.VERHASH.'" type="text/javascript"></script> ';
		}
	}ELSE{
		echo '<FONT COLOR="#FF0000"><B>'.$thislang['9'].'</B></FONT><BR><label class="xs2"><em>'.$thislang['3'].':</em><input type="text" id="rid" name="rid" autocomplete="off" size="25" maxlength="15" value="'.$randid.'" class="txt"> *</label><A HREF="javascript:;" onClick="getzcm();return false;">'.$thislang['7'].'</A>'.$thislang['8'].'<script src="source/plugin/dsu_amufzc/getzcm.js?'.VERHASH.'" type="text/javascript"></script> ';
	}
}else{
	loaducenter();
	$ucresult = uc_user_checkemail($_G['gp_email']);
	IF($ucresult == '-6'){
		echo '<FONT COLOR="#FF0000"><B>'.$thislang['92'].'</B></FONT><BR><label class="xs2"><em>'.$thislang['3'].':</em><input type="text" id="rid" name="rid" autocomplete="off" size="25" maxlength="15" value="'.$randid.'" class="txt"> *</label><A HREF="javascript:;" onClick="getzcm();return false;">'.$thislang['7'].'</A>'.$thislang['8'].'<script src="source/plugin/dsu_amufzc/getzcm.js?'.VERHASH.'" type="text/javascript"></script> ';
	}ELSE{
		if($thisonlyone){
			$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuamfzc")." WHERE email = '".$_G['gp_email']."' AND yes = '1'");
			if($query){
				echo '<FONT COLOR="#FF0000"><B>'.$thislang['92'].'</B></FONT><BR><label class="xs2"><em>'.$thislang['3'].':</em><input type="text" id="rid" name="rid" autocomplete="off" size="25" maxlength="15" value="'.$randid.'" class="txt"> *</label><A HREF="javascript:;" onClick="getzcm();return false;">'.$thislang['7'].'</A>'.$thislang['8'].'<script src="source/plugin/dsu_amufzc/getzcm.js?'.VERHASH.'" type="text/javascript"></script> ';
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
					$succeed = sendmail("$_G[gp_email]<$_G[gp_email]>", $email_subject, $email_message);
					if($succeed) {
						$randid = '';
						echo '<FONT COLOR="#FF0000"><B>'.$thislang['93'].'</B></FONT><BR>';
					} else {
						echo '<FONT COLOR="#FF0000"><B>'.$thislang['931'].'</B>('.$insert['email'].')</FONT><BR>';
					}
				}
				echo '<label class="xs2"><em>'.$thislang['3'].':</em><input type="text" id="rid" name="rid" autocomplete="off" size="25" maxlength="15" value="'.$randid.'" class="txt"> *</label>';
			}
		}else{
			$maxtime = TIMESTAMP - $thismaxtime;
			$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuamfzc")." WHERE email = '".$_G['gp_email']."' AND time > '".$maxtime."'");
			if($query){
				$maxtime = $thismaxtime - TIMESTAMP + $query['time'];
				$thislang['91'] = lang('plugin/dsu_amufzc','91', array('maxtime' => $maxtime));
				echo '<FONT COLOR="#FF0000"><B>'.$thislang['91'].'</B></FONT><BR><label class="xs2"><em>'.$thislang['3'].':</em><input type="text" id="rid" name="rid" autocomplete="off" size="25" maxlength="15" value="'.$randid.'" class="txt"> *</label><A HREF="javascript:;" onClick="getzcm();return false;">'.$thislang['7'].'</A>'.$thislang['8'].'<script src="source/plugin/dsu_amufzc/getzcm.js?'.VERHASH.'" type="text/javascript"></script> ';
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
					$succeed = sendmail("$_G[gp_email]<$_G[gp_email]>", $subject, $email_message);
					if($succeed) {
						$randid = '';
						echo '<FONT COLOR="#FF0000"><B>'.$thislang['93'].'</B></FONT><BR>';
					} else {
						echo '<FONT COLOR="#FF0000"><B>'.$thislang['931'].'</B>('.$insert['email'].')</FONT><BR>';
					}
				}
				echo '<label class="xs2"><em>'.$thislang['3'].':</em><input type="text" id="rid" name="rid" autocomplete="off" size="25" maxlength="15" value="'.$randid.'" class="txt"> *</label>';
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
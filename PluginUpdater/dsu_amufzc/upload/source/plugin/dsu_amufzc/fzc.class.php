<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_dsu_amufzc {
	var $email = false;

	function plugin_dsu_amufzc() {
		global $_G;
		require './data/plugindata/dsu_amufzc.lang.php';
		$this->lang = $scriptlang['dsu_amufzc'];
		$this->email = $_G['cache']['plugin']['dsu_amufzc']['email'];
		$this->regemail = (array)unserialize($_G['setting']['reginput']);
		$this->regemail['email'] = $this->regemail['email']?$this->regemail['email']:'email';
		$this->js = '<script type="text/javascript">
var xmlHttp

function getzcm()
{
document.getElementById("msg").innerHTML=\'<img src="source/plugin/dsu_amufzc/loading.gif" style="border: 0px" />'.$this->lang['94'].'\'
var f = document.register; 
var email = f.'.$this->regemail['email'].'.value;
var activationauth = f.activationauth.value;
xmlHttp=GetXmlHttpObject()
if (xmlHttp==null)
  {
  alert ("Browser does not support HTTP Request")
  return
  } 
var url="plugin.php?id=dsu_amufzc:getzcm"
url=url+"&email="+email
url=url+"&activationauth="+activationauth
url=url+"&snd="+Math.random()
xmlHttp.onreadystatechange=stateChanged 
xmlHttp.open("GET",url,true)
xmlHttp.send(null)
} 

function stateChanged() 
{ 
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
 { 
 document.getElementById("msg").innerHTML=xmlHttp.responseText 
 } 
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 xmlHttp=new XMLHttpRequest();
 }
catch (e)
 {
 // Internet Explorer
 try
  {
  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
return xmlHttp;
}</script>';

	}

}


class plugin_dsu_amufzc_member extends plugin_dsu_amufzc {
	function register_header(){
		global $_G;
		$inputemail = 'gp_'.$this->regemail['email'];
		$_G['gp_email'] = $_G[$inputemail]; 
		if($_POST){
			$_G['gp_activationauth'] = strip_tags(str_replace(" ", "+", $_G['gp_activationauth']));
			$activationinfo = authcode($_G['gp_activationauth'], $operation = 'DECODE');
			$activationinfoname = preg_replace("/\s.+/i","",$activationinfo);
			if($activationinfoname){
				loaducenter();
				if($data = uc_get_user($activationinfoname)) {
					list($uid, $username, $email) = $data;
				}
				$_G['gp_email'] = getEmail(strip_tags($email));
			}else{
				$_G['gp_email'] = getEmail(strip_tags($_G['gp_email']));
			}
			$_G['gp_rid'] = strip_tags($_G['gp_rid']);
			if(!$_G['gp_rid']){
				showmessage('dsu_amufzc:1', '');
			}
			if(!$_G['gp_email']){
				showmessage('dsu_amufzc:5', '');
			}
			$amutb = DB::table("plugin_dsuamfzc");
			$query = DB::fetch_first("SELECT * FROM $amutb WHERE rid = '".$_G['gp_rid']."'");
			if(!$query || $query['yes'] == '1'){
				showmessage('dsu_amufzc:2', '');
			}
			if($_G['gp_email'] != $query['email']){
				showmessage('dsu_amufzc:11', '');
			}
			//showmessage('dsu_amufzc:12', '');
		}
	}

	function register_input(){
		global $_G; 
		$_G['gp_rid'] = strip_tags($_G['gp_rid']);
		if($_G['gp_action'] == 'activation'){
			$return = '<SPAN id="msg"><label class="xs2"><em>'.$this->lang['3'].':</em><input type="text" id="rid" name="rid" autocomplete="off" size="25" maxlength="15" value="'.$_G['gp_rid'].'" class="txt"> *</label><A HREF="javascript:;" onClick="getzcm();return false;">'.$this->lang['7'].'</A></SPAN>'.$this->js;
		}else{
			$return = '<SPAN id="msg"><label class="xs2"><em>'.$this->lang['3'].':</em><input type="text" id="rid" name="rid" autocomplete="off" size="25" maxlength="15" value="'.$_G['gp_rid'].'" class="txt"> *</label><A HREF="javascript:;" onClick="getzcm();return false;">'.$this->lang['7'].'</A>'.$this->lang['8'].'</SPAN>'.$this->js;
		}
		

		return $return;
	}

	function register_test_output($a){
		global $_G;
		$_G['gp_rid'] = strip_tags($_G['gp_rid']);
		if($_POST && $a['message'] =='' && $_G['gp_rid']){
			DB::query("UPDATE ".DB::table("plugin_dsuamfzc")." SET yes = '1'  WHERE rid = '{$_G['gp_rid']}'");
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
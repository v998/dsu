<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_dsu_amufzc {
	var $email = false;

	function plugin_dsu_amufzc() {
		global $_G;
		$this->email = $_G['cache']['plugin']['dsu_amufzc']['email'];
		$this->regemail = (array)$_G['setting']['reginput'];
		//var_dump($_G['setting']['reginput']);
		$this->regemail['email'] = $this->regemail['email']?$this->regemail['email']:'email';
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

		}
	}

	function register_fzc_output($a){
		global $_G;
		$_G['gp_rid'] = strip_tags($_G['gp_rid']);
		if($_POST && $a["message"]=='register_succeed' && $_G['gp_rid']){
			DB::query("UPDATE ".DB::table("plugin_dsuamfzc")." SET yes = '1'  WHERE rid = '{$_G['gp_rid']}'");
		}
	}

	function register_input(){
		global $_G ; 
		$_G['gp_rid'] = strip_tags($_G['gp_rid']);
		$return = '';
		if($_G['gp_action'] == 'activation'){
			include template('dsu_amufzc:afzc');
			return $return;
		}else{
			$input = (array)unserialize(base64_decode(getcookie('dsu_amufzc_'.$_G['gp_rid'])));
			include template('dsu_amufzc:fzc');
			return $return;
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
	$pattern = "/[a-z0-9]([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i";//Ϊ���ʺ�qq����������,����ͷ�����޸� 
	preg_match_all($pattern,$str,$emailArr);
	if($emailArr[0][0]){$return = $emailArr[0][0];}else{$return = '';}
	return $return; 
}
?>
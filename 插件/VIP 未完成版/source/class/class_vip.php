<?php

class vip{
	var $groupid;
	var $vars;
	var $group			= array();
	var $vip_cache		= array();
	var $vip_info_cache	= array();
	var $on				= false;
	function vip(){
		global $_G;
		loadcache('plugin');
		$this->vars=$_G['cache']['plugin']['dsu_kkvip'];
		if($this->vars) $this->on = true;
		$this->group[1] = $this->vars['vip_1_group'];
		$this->group[2] = $this->vars['vip_2_group'];
		$this->group[3] = $this->vars['vip_3_group'];
		$this->group[4] = $this->vars['vip_4_group'];
		$this->group[5] = $this->vars['vip_5_group'];
		$this->group[6] = $this->vars['vip_6_group'];
		$this->_load_cache();
	}
	function is_vip($uid=''){
		global $_G;
		$uid = $uid ? $uid : $_G['uid'];
		if(!$uid) return false;
		return in_array($uid, $this->vip_cache);
	}
	function getvipinfo($uid){
		global $_G;
		if (!$this->is_vip($uid)) return array();
		return DB::fetch($this->query("SELECT * FROM pre_dsu_vip WHERE uid='{$uid}'"));
	}
	function pay_vip($in_uid, $day, $in_oldgroup){
		global $_G;
		$uid		= $in_uid		? $in_uid		: $_G['uid'];
		$oldgroup	= $in_oldgroup	? $in_oldgroup	: $_G['groupid'];
		$year_pay	= $day>=360		? 1				: 0;
		$time		= $day * 86400;
		if (!$this->is_vip($uid)){
			DB::insert('dsu_vip',array(
				'uid'=>$uid,
				'exptime'=>$_G['timestamp']+$time,
				'jointime'=>$_G['timestamp'],
				'year_pay'=>$year_pay,
				'level'=>1,
				'oldgroup'=>$oldgroup,
			), false, true);
			$this->query("UPDATE pre_common_member SET groupid='{$this->vars[vip_1_group]}' WHERE uid='{$uid}' AND adminid<>'1'");
			$this->vip_cache[] = $uid;
			require_once libfile('function/cache');
			updatecache('dsu_kkvip');
		}else{
			if($year_pay){
				$this->query("UPDATE pre_dsu_vip SET exptime=exptime+'{$time}' , year_pay='1' WHERE uid='{$uid}'");
			}else{
				$this->query("UPDATE pre_dsu_vip SET exptime=exptime+'{$time}' WHERE uid='{$uid}'");
			}
		}
	}
	function _load_cache(){
		global $_G;
		loadcache('dsu_kkvip');
		$this->vip_cache=$_G['cache']['dsu_kkvip'];
	}
	function query($sql, $extra=''){
		$db = & DB::object();
		$sql = str_ireplace('pre_',$db->tablepre,$sql);
		return $db->query($sql,$extra);
	}
}

?>
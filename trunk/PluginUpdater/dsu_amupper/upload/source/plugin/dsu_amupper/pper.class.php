<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_dsu_amupper {
	function plugin_dsu_amupper(){
		global $_G;	
		$this -> tone = $_G['cache']['plugin']['dsu_amupper']['tone'];
		$this -> autogid = (array)unserialize($_G['cache']['plugin']['dsu_amupper']['autogid']);
		$this -> today = dgmdate($_G['timestamp'],'Ymd',8);
		$this -> cookietoday = getcookie('dsu_amupper_timer');
		$this -> cookieheader = getcookie('dsu_amupper_header');
		$this -> cookiefooter = getcookie('dsu_amupper_footer');
		if($this -> cookietoday == $this -> today){$this -> cookies = 1;}else{$this -> cookies = 0;}
	}

	function global_usernav_extra1(){
		global $_G;	


		if($_G['uid']){
			if($this -> cookieheader && $this -> cookies){
				return base64_decode($this -> cookieheader); 
			}

			$return = $this ->dsu_amupperaf();
		}
		return $return;
	}

	function global_footer(){
		global $_G;	
		if($_G['uid']){
			$return_cj ='<div style="position: absolute; top: -100000px;" id="soundplayerlayer"></div>';
			$return_cj .='<script type="text/javascript" reload="1">function toneplayer(file){document.getElementById(\'soundplayerlayer\').innerHTML = AC_FL_RunContent(\'id\', \'pmsoundplayer\', \'name\', \'pmsoundplayer\', \'width\', \'0\', \'height\', \'0\', \'src\', \'./source/plugin/dsu_amupper/template/sound/player.swf\', \'FlashVars\', \'sFile=./source/plugin/dsu_amupper/template/sound/pm_\' + file + \'.mp3\', \'menu\', \'false\',  \'allowScriptAccess\', \'sameDomain\', \'swLiveConnect\', \'true\');}</script>';
			if($this -> cookiefooter && $this -> cookies){
				return $return_cj.base64_decode($this -> cookiefooter);
			}
			$this -> cdbtoday = dgmdate($this -> query['lasttime'],'Ymd',8);
			if($this -> query && $this -> cdbtoday == $this -> today){
				$returnfooter = '<div id="ppered_menu" style="display:none;width:240px;">
					<p class="crly">
					<SPAN class="y xg1" ><a href="plugin.php?id=dsu_amupper:list" target="_blank">'.lang("plugin/dsu_amupper","menu2").'</a></SPAN>
					<strong>'.lang("plugin/dsu_amupper","menu1").'</strong>'.$this -> query['addup'].lang("plugin/dsu_amupper","menu4").'
					<br>'.lang("plugin/dsu_amupper","menu3").$this -> query['continuous'].lang("plugin/dsu_amupper","menu4").'
					<br>
					<strong>'.lang("plugin/dsu_amupper","info2").'</strong>:'.dgmdate($this -> query['lasttime'],"Y-m-d H:i",8);
				if($special_reward = $this->special_reward($this ->query['continuous'])){
					$returnfooter .= '<br><font color="red">'.$special_reward.'</font>';
				}
				$returnfooter .= '</p></div>';
			}
			dsetcookie('dsu_amupper_footer', base64_encode($returnfooter), 600);
			dsetcookie('dsu_amupper_header', base64_encode($this->returnheader), 600);
			dsetcookie('dsu_amupper_timer',$today, 600);
		}else{
			$returnfooter ='';
		}
		return $return_cj.$returnfooter;

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

	function dsu_amupperaf(){
		global $_G;

		$dsu_amupperimage = $_G['cache']['plugin']['dsu_amupper']['image'];
		$this -> return_txt = '<span class="pipe">|</span><span id="my_amupper"><a href="javascript:;" onclick="ajaxget(\'plugin.php?id=dsu_amupper:pper&ajax=1&formhash='.$_G['formhash'].'\', \'my_amupper\', \'my_amupper\', \''.lang("plugin/dsu_amupper","ppering").'\', \'\',function () {toneplayer('.$this -> tone.');});">'.lang("plugin/dsu_amupper","pper0").'</a>&nbsp;</span>';
		$this -> return_img = '<span class="pipe">|</span><span id="my_amupper"><a href="javascript:;" onclick="ajaxget(\'plugin.php?id=dsu_amupper:pper&ajax=1&formhash='.$_G['formhash'].'\', \'my_amupper\', \'my_amupper\', \''.lang("plugin/dsu_amupper","ppering").'\', \'\',function () {toneplayer('.$this -> tone.');});">'.lang("plugin/dsu_amupper","pper1").'</a>&nbsp;</span>';
		$this -> return_auto = '<script type="text/javascript">function autopper(){ajaxget(\'plugin.php?id=dsu_amupper:pper&ajax=1&formhash='.$_G['formhash'].'\', \'my_amupper\', \'my_amupper\', \''.lang("plugin/dsu_amupper","ppering").'\', \'\',function () {toneplayer('.$this -> tone.');});}window.onload=autopper;</script>';
		$this -> returned_txt = '<span class="pipe">|</span><span id="ppered" onmouseover="showMenu({\'ctrlid\':this.id,\'pos\':\'43\'})"><A HREF="plugin.php?id=dsu_amupper:list" target="_blank">'.lang("plugin/dsu_amupper","info0").'</A></span>&nbsp;';
		$this -> returned_img = '<span class="pipe">|</span><span id="ppered" onmouseover="showMenu({\'ctrlid\':this.id,\'pos\':\'43\'})"><A HREF="plugin.php?id=dsu_amupper:list" target="_blank">'.lang("plugin/dsu_amupper","info1").'</A></span>&nbsp;';

		$today = dgmdate($_G['timestamp'],'Ymd',8);
		$yesterday = dgmdate($_G['timestamp']-86400,'Ymd',8);
		$tomorrow = dgmdate($_G['timestamp']+86400,'Ymd',8);
		$cdb_pper = array();
		$cdb_pper['uid'] = intval($_G['uid']);
		$this -> query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuampper")." WHERE uid = '{$cdb_pper['uid']}'");

		if($this -> query){
			$this -> lasttime =  dgmdate($this -> query['lasttime'],'Ymd',8);
			if($this -> lasttime == $today){
				if($dsu_amupperimage){$return = $this -> returned_img;}else{$return = $this -> returned_txt;}
			}elseif($this -> lasttime < $today){
				if(in_array($_G['groupid'],$this -> autogid)){
					$return = $this -> return_auto;
				}else{
					if($dsu_amupperimage){
						$return = $this -> return_img;
					}else{
						$return = $this -> return_txt;
					}
				}
			}elseif($this -> lasttime > $today){
				if($dsu_amupperimage){$return = $this -> returned_img;}else{$return = $this -> returned_txt;}
			}
		}else{
			if(in_array($_G['groupid'],$this -> autogid)){
				$return = $this -> return_auto;
			}else{
				if($dsu_amupperimage){
					$return = $this -> return_img;
				}else{
					$return = $this -> return_txt;
				}
			}
		}
		$this->returnheader = $return;
		return $return;
	}



}

class plugin_dsu_amupper_forum extends plugin_dsu_amupper {
	function post_top(){
		global $_G;
		if($_G['uid'] && $_G['cache']['plugin']['dsu_amupper']['force']){
			if(isset($_G['gp_message'])){
				$cdb_pper['uid'] = intval($_G['uid']);
				$this -> query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuampper")." WHERE uid= '{$cdb_pper['uid']}'");
				if($this -> query){
					$today = dgmdate($_G['timestamp'],'Ymd');
					$this -> lasttime = dgmdate($this -> query['lasttime'],'Ymd');
				}

				if(!$this -> query || $today != $this -> lasttime){
					if(!$_G['inajax']){$url = 'setTimeout(function(){location.reload()},2000);return false;';}
					if($_G['cache']['plugin']['dsu_amupper']['image']){
						showmessage(lang('plugin/dsu_amupper','nopper1',array('formhash'=>$_G['formhash'],'tone'=>$_G['cache']['plugin']['dsu_amupper']['tone'],'js'=>$url)),'');
					}else{
						showmessage(lang('plugin/dsu_amupper','nopper0',array('formhash'=>$_G['formhash'],'tone'=>$_G['cache']['plugin']['dsu_amupper']['tone'],'js'=>$url)),'');
					}
				}
			}
		}
		return $_G['gp_message'];
	}
}
?>
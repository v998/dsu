<?php

class plugin_dsu_kksfs_dzx{
	var $vars=array();
	var $frequency = 0;
	function plugin_dsu_kksfs_dzx(){
		global $_G;
		loadcache('plugin');
		$this->vars=$_G['cache']['plugin']['dsu_kksfs_dzx'];
		$this->vars['groups']=unserialize($this->vars['usergroup']);
		$this->vars['admin_group']=unserialize($this->vars['admin']);
	}
	function _check($data){
		global $_G;
		if(!in_array($_G['groupid'],$this->vars['groups'])) return true;
		$query=$this->_build_query($data);
		if(!$query) return true;
		$return_str=file_get_contents($query);
		$return=unserialize($return_str);
		if($return['success']!=1) return true;
		$this->frequency = 0;
		if($return['ip']) $this->frequency = $this->frequency + $return['ip']['frequency'];
		if($return['email']) $this->frequency = $this->frequency + $return['email']['frequency'];
		if($return['username']) $this->frequency = $this->frequency + $return['username']['frequency'];
		if($this->frequency>=$this->vars['level']) return false;
		return true;
	}
	function _build_query($data){
		if(!$data['ip']&&!$data['email']&&!$data['username']) return;
		$query='http://www.stopforumspam.com/api?f=serial';
		if($data['ip']) $query.="&ip={$data[ip]}";
		if($data['email']) $query.="&email={$data[email]}";
		if($data['username']) $query.="&username={$data[username]}";
		return $query;
	}
	function viewthread_postfooter_output(){
		global $_G,$postlist;
		if(!$_G['uid'] || !in_array($_G['groupid'], $this->vars['admin_group'])) return array();
		$return=array();
		foreach($postlist as $post){
			if($post['adminid']){
				$return[]='';
			}else{
				$return[]='<a href="javascript:;" onclick="showDialog(\''.lang('plugin/dsu_kksfs_dzx','confirm_kill').'\',\'confirm\',\'[DSU] SFS\',function(){showWindow(\'dsu_sfs\', \'plugin.php?id=dsu_kksfs_dzx&uid='.$post['authorid'].'&formhash='.md5(FORMHASH).'\')});return false;" style="background: url(static/image/common/recyclebin.gif) no-repeat 5px 56%" target="_blank">'.lang('plugin/dsu_kksfs_dzx','delete').'</a>';
			}
		}
		return $return;
	}
	function post_check(){
		global $_G;
		if(!$_G['member']) return;
		$data=array();
		if($_G['clientip']) $data['ip']=$_G['clientip'];
		if($_G['member']['email']) $data['email']=$_G['member']['email'];
		if($_G['member']['username']) $data['username']=urlencode($_G['member']['username']);
		if(!$this->_check($data)){
			DB::insert('dsu_sfs_log',array('uid'=>$_G['uid'],'reason'=>'-1','rate'=>$this->frequency,'timestamp'=>TIMESTAMP));
			showmessage('dsu_kksfs_dzx:block_post');
		}
	}
	function register_check(){
		global $_G;
		$username=$_POST[$_G['setting']['reginput']['username']];
		$email=$_POST[$_G['setting']['reginput']['email']];
		if(!$username && !$email) return;
		$data=array();
		if($_G['clientip']) $data['ip']=$_G['clientip'];
		if($email) $data['email']=$email;
		if($username) $data['username']=urlencode($username);
		if(!$this->_check($data)){
			DB::insert('dsu_sfs_log',array('uid'=>$_G['uid'],'reason'=>'-1','rate'=>$this->frequency,'timestamp'=>TIMESTAMP));
			showmessage('dsu_kksfs_dzx:block_register');
		}
	}
	function _adblock_output(){
		return '<script type="text/javascript" defer>
function kk_adblock_ok(){
	var kk_adblock_inputok = document.createElement("input");
	kk_adblock_inputok.type="text";
	kk_adblock_inputok.name="kk_adblock_inputok";
	kk_adblock_inputok.value="kk_adblock_inputok";
	$("kk_adblock").appendChild(kk_adblock_inputok);
	kk_adblock_ok=function(){}
}
document.onmousedown=function(){kk_adblock_ok();}
document.onmousemove=function(){kk_adblock_ok();}
</script>
<div id="kk_adblock" style="display:none">
<input type="text" name="kk_adblock_hiddentext" />
</div>';
	}
	function _adblock_stat(){
		global $_G;
		DB::insert('dsu_sfs_log',array('uid'=>$_G['uid'],'reason'=>'1','rate'=>'','timestamp'=>TIMESTAMP));
	}
}

class plugin_dsu_kksfs_dzx_forum extends plugin_dsu_kksfs_dzx{
	function forumdisplay_fastpost_content(){
		return $this->_adblock_output();
	}
	function viewthread_fastpost_content_output(){
		return $this->_adblock_output();
	}
	function post_check(){
		global $_G;
		loadcache('plugin');
		$config=$_G['cache']['plugin']['dsu_kksfs_dzx'];
		$adlock_group=unserialize($vars['adlock_group']);
		if($_G['gp_message'] && in_array($_G['groupid'],$adlock_group) && in_array($_G['gp_action'],array('newthread','reply')) && !$_G['gp_comment'] && !$_G['gp_inajax']){
			if ($_G['gp_kk_adblock_inputok']!='kk_adblock_inputok') {
				$this->_adblock_stat();
				showmessage('dsu_kksfs_dzx:block_kk_adblock');
			}
			if ($_G['gp_kk_adblock_hiddentext']) {
				$this->_adblock_stat();
				showmessage('dsu_kksfs_dzx:block_kk_adblock');
			}
		}
	}
	function post_bottom(){
		global $_G;
		if(!in_array($_G['gp_action'],array('newthread','reply'))) return;
		return '
<script type="text/javascript" defer>
$("postsubmit").disabled=true;
function kk_adblock_ok(){
	var kk_adblock_inputok = document.createElement("input");
	kk_adblock_inputok.type="text";
	kk_adblock_inputok.name="kk_adblock_inputok";
	kk_adblock_inputok.id="kk_adblock_inputok";
	$("kk_adblock").appendChild(kk_adblock_inputok);
	$("postsubmit").disabled=false;
	document.getElementById("kk_adblock_inputok").value="kk_adblock_inputok";
	kk_adblock_ok=function(){}
}
document.onmousedown=function(){kk_adblock_ok();}
document.onmousemove=function(){kk_adblock_ok();}
</script>
<div id="kk_adblock" style="display:none">
<input type="text" name="kk_adblock_hiddentext" />
</div>';
	}
}
class plugin_dsu_kksfs_dzx_member extends plugin_dsu_kksfs_dzx{
}

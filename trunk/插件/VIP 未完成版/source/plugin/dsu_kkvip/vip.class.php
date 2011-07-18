<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_dsu_kkvip{
	function global_kkvip(){
		global $_G,$vip;
		if ($_GET['op']=='verify' && in_array($_GET['vid'],array('vip','no_vip'))) dheader('Location: vip.php');
		if ($_GET['op']=='verify' && in_array($_GET['vid'],array('year_vip','no_year_vip'))) dheader('Location: vip.php?do=year');
		if (!$_G['uid'] || $_G['vip']) return;
		loadcache('plugin');
		include_once libfile('class/vip');
		$vip=$vip?$vip:new vip();
		if ($vip->is_vip($_G['uid'])){
			$_G['vip']['isvip']=false;
			return;
		}
		$_G['vip']=$vip->getvipinfo($_G['uid']);
		$_G['vip']['isvip']=true;
		$_G['vip']['level_text']="VIP {$_G[vip][level]}";
		if ($vip->vars['vip_noad']){
			loadcache('advs');
			unset($_G['cache']['advs']);
		}
		return;
	}
	function viewthread_sidetop_output(){
		global $_G,$vip,$postlist;
		if(!$postlist) return array();
		loadcache('plugin');
		include_once libfile('class/vip');
		$vip=$vip?$vip:new vip();
		foreach ($postlist as $post){
			$uids.=$uids?",'{$post[authorid]}'":"'{$post[authorid]}'";
		}
		if (!$uids || !$_G['tid']) return array();
		$query=$vip->query("SELECT * FROM pre_dsu_vip WHERE uid IN ({$uids}) AND exptime>='{$_G[timestamp]}'");
		while($user=DB::fetch($query)){
			$vip_users[$user['uid']]=$user;
		}
		foreach ($postlist as $pid=>$post){
			if($vip_users[$post['authorid']]){
				$return[]='<dl class="pil cl vm"><img src="source/plugin/dsu_kkvip/images/vip'.$vip_users[$post['authorid']]['level'].'.gif">&nbsp;<font color="red">³É³¤Öµ: '.$vip_users[$post['authorid']]['czz'].'</font></dl>';
				$postlist[$pid]['verifyvip']=true;
				if($vip->vars['viewthread_redname']) $postlist[$pid]['author']="<font color=\"red\">{$post[author]}</font>";
				if($vip_users[$post['authorid']]['year_pay']){
					$postlist[$pid]['verifyyear_vip']=true;
				}else{
					$postlist[$pid]['verifyno_year_vip']=true;
				}
			}else{
				$return[]='';
				$postlist[$pid]['verifyno_vip']=true;
			}
			$postlist[$pid]['vip']=$vip_users[$post['authorid']];
		}
		// VIP Icons
		$_G['setting']['verify']['enabled']=true;
		$_G['setting']['verify']['vip']=array(
			'available'=>true,
			'title'=>'VIP &#20250;&#21592;',
			'icon'=>'source/plugin/dsu_kkvip/images/vip.jpg',
		);
		$_G['setting']['verify']['no_vip']=array(
			'available'=>true,
			'title'=>'&#38750; VIP &#20250;&#21592;',
			'icon'=>'source/plugin/dsu_kkvip/images/novip.jpg',
		);
		$_G['setting']['verify']['year_vip']=array(
			'available'=>true,
			'title'=>'&#24180;&#36153; VIP &#20250;&#21592;',
			'icon'=>'source/plugin/dsu_kkvip/images/year_vip.jpg',
		);
		$_G['setting']['verify']['no_year_vip']=array(
			'available'=>true,
			'title'=>'&#38750;&#24180;&#36153;&#20250;&#21592;',
			'icon'=>'source/plugin/dsu_kkvip/images/no_year_vip.jpg',
		);
		return (array)$return;
	}
	function forumdisplay_kkvip_output(){
		global $_G,$vip,$verify;
		loadcache('plugin');
		include_once libfile('class/vip');
		$vip=$vip?$vip:new vip();
		foreach ($_G['forum_threadlist'] as $key=>$thread){
			if ($vip->is_vip($thread['authorid'])){
				if (!$thread['highlight']){
					$subject=$_G['forum_threadlist'][$key]['subject'];
					if ($vip->vars['highlight_blod']==1 || $vip->vars['highlight_blod']==3){
						$subject="<b>{$subject}</b>";
					}
					if ($vip->vars['highlight_blod']==2 || $vip->vars['highlight_blod']==3){
						$subject="<font color=\"{$vip->vars[highlight_color]}\">{$subject}</font>";
					}
					$_G['forum_threadlist'][$key]['subject']=$subject;
				}
				$_G['forum_threadlist'][$key]['author']='<font color="red">'.$thread['author'].'</font>';
				$verify[$thread['authorid']]='&nbsp;<a href="vip.php" target="_blank"><img src="source/plugin/dsu_kkvip/images/vip.jpg"></a>';
			}
		}
	}
}
class plugin_dsu_kkvip_forum extends plugin_dsu_kkvip{
}
class plugin_dsu_kkvip_group extends plugin_dsu_kkvip_forum{
}
class plugin_dsu_kkvip_home extends plugin_dsu_kkvip{
}
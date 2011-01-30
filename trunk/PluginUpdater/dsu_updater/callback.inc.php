<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');
$not_jump=true;
include DISCUZ_ROOT.'./source/plugin/dsu_updater/core.func.php';
switch($_G['gp_do']){
	default:
	case 'ping':
		exit('ok');
	case 'oauth':
		if(!$_G['uid']) exit('<script>location.href="member.php?mod=logging&action=login"</script>');
		$fonder_array=explode(',',$_G['config']['admincp']['founder']);
		!in_array($_G['uid'],$fonder_array) && exit('Access Denied');
		$site_id=$_G['gp_site_id']?intval($_G['gp_site_id']):'';
		$key=strip_tags($_G['gp_key']);
		if(submitcheck('submit') && $site_id && $key){
			$_G['dsu_updater']['site_id']=$site_id;
			$_G['dsu_updater']['key']=$key;
			save_setting();
			@include_once DISCUZ_ROOT.'./source/discuz_version.php';
			exit("<a href=\"\" onclick=\"window.opener.location.reload();window.close();\" onload=\"window.close();\">{$du_lang[accept_succeed]}</a><br><span class=\"pipe\">|</span><img title=\"[DSU] Updater CallBack\" src=\"http://update.dsu.cc/api.php?type=all&site_id={$site_id}&keyhash=".md5($key).'&dv='.DISCUZ_VERSION.'&charset='.CHARSET."\" />");
		}else{
			include template('dsu_updater:oauth');
		}
		break;
	case 'receive_data':
		$type=$_G['gp_type'];
		get_setting();
		$site_id=$_G['gp_site_id']?intval($_G['gp_site_id']):'';
		$key=strip_tags($_G['gp_key']);
		if(!check_key($site_id,$key)) exit();
		$_G['dsu_updater'][$type]=stripslashes(stripslashes($_G['gp_data']));
		if($_G['gp_is_array']) $_G['dsu_updater'][$type]=unserialize($_G['dsu_updater'][$type]);
		save_setting();
		break;
	case 'receive_file':
		get_setting();
		$site_id=$_G['gp_site_id']?intval($_G['gp_site_id']):'';
		$key=strip_tags($_G['gp_key']);
		if(!check_key($site_id,$key)) exit('E0');
		$file_list=unserialize(quot_fix($_POST['file_list']));
		foreach($file_list as $id=>$path){
			$contents=gzuncompress(base64_decode($_POST["file_{$id}"]));
			if($contents && $path){
				$dir=dirname($path);
				!file_exists($dir) && mkdir($dir,0777);
				@touch(DISCUZ_ROOT.'./'.$path);
				if(!is_writeable(DISCUZ_ROOT.'./'.$path)) exit("E1|{$path}");
				file_put_contents(DISCUZ_ROOT.'./'.$path,$contents);
			}
		}
		$pluginid=DB::result_first('SELECT pluginid FROM '.DB::table('common_plugin')." WHERE identifier='{$_G[gp_plugin]}'");
		exit("ok|$pluginid");
		break;
	case 'check':
		get_setting();
		$site_id=$_G['gp_site_id']?intval($_G['gp_site_id']):'';
		$key=strip_tags($_G['gp_key']);
		if(check_key($site_id,$key)) exit('ok');
		exit();
		break;
}

function quot_fix($str){
	return stripslashes(stripslashes($str));
}

?>
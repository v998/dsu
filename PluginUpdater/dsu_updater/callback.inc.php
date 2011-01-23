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
			showmessage($du_lang['accept_succeed'],'admin.php?frames=yes&action=plugins&operation=config&identifier=dsu_updater&pmod=news');
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
		$_G['dsu_updater'][$type]=stripslashes(stripslashes($_POST['data']));
		if($_G['gp_is_array']) $_G['dsu_updater'][$type]=unserialize($_G['dsu_updater'][$type]);
		save_setting();
		break;
	case 'check':
		get_setting();
		$site_id=$_G['gp_site_id']?intval($_G['gp_site_id']):'';
		$key=strip_tags($_G['gp_key']);
		if(check_key($site_id,$key)) exit('ok');
		exit();
		break;
}

?>
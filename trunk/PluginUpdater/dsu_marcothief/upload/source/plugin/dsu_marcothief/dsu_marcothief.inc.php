<?php
/*
	[DSU] Thief
	Author: Marco129[http://www.my3talk.com]
	Please respect the author, do not delete the copyright!
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

@require_once './source/plugin/dsu_marcothief/dsu_marcothief.func.php';
if(!$_G['uid']) {
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}else{
	$config = $_G['cache']['plugin']['dsu_marcothief'];
	$user_db = DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcothief')." t,".DB::table('common_member_count')." c WHERE t.uid=c.uid AND t.uid='$_G[uid]'");
	if(!$user_db){
		DB::query("INSERT INTO ".DB::table('dsu_marcothief')." (uid) VALUES ('$_G[uid]')");
	}
	$system_success = $config['sucess_percentage']*100;
	$system_fail_police = $config['police_percentage']*100;
	$system_fail_jail = $config['jail_percentage']*100;
	$system_raids_sucess = $config['raids_sucess_percentage']*100;
	$system_raids_fail_jail = $config['raids_percentage']*100;
	$system_run = $config['run_percentage']*100;
	$success_shop = use_shop($_G['uid'], 1);
	$raids_sucess_shop = use_shop($_G['uid'], 2);
	$run_success_shop = use_shop($_G['uid'], 3);
	$query = DB::query("SELECT * FROM ".DB::table('dsu_marcothief_log')." ORDER BY id DESC LIMIT 30");
	$log = array();
	while($data = DB::fetch($query)){
		$data['time'] = dgmdate($data['time'], 't', $_G['setting']['timeoffset']);
		$log[] = $data;
	}
}

if(empty($mod)){
	$thief_left = ($config['max_thief']-$user_db['action'])<0 ? 0 : ($config['max_thief']-$user_db['action']);
	$jail_left = intval(($user_db['jail']-$_G['timestamp'])/60);
	if(submitcheck('robbery')){
		$check = DB::fetch_first("SELECT * FROM ".DB::table('common_member')." m,".DB::table('common_member_count')." c WHERE m.uid=c.uid AND ".($_G['gp_method'] ? "m.username='$_G[gp_user]'" : "m.uid='$_G[gp_user]'")."");
		$check_db = DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcothief')." t,".DB::table('common_member_count')." c WHERE t.uid=c.uid AND t.uid='$check[uid]'");
		if($config['thief_allow'] > $user_db['extcredits'.$config['fee']]){
			showmessage('dsu_marcothief:msg_36', dreferer(), array('credit' => $config['thief_allow']));
		}elseif($user_db['jail'] > $_G['timestamp']){
			showmessage('dsu_marcothief:msg_9', dreferer());
		}elseif($user_db['action'] >= $config['max_thief']){
			showmessage('dsu_marcothief:msg_7', dreferer());
		}elseif($user_db['extcredits'.$config['fee']] < $config['fee_once']){
			showmessage('dsu_marcothief:msg_1', dreferer(), array('title' => $_G['setting']['extcredits'][$config['fee']]['title']));
		}elseif(!$check){
			showmessage('dsu_marcothief:msg_2', dreferer());
		}elseif(in_array($check['groupid'], unserialize($config['protect_groups']))){
			showmessage('dsu_marcothief:msg_16', dreferer());
		}elseif($check_db['jail'] > $_G['timestamp']){
			showmessage('dsu_marcothief:msg_24', dreferer());
		}elseif($check_db['actions'] >= $config['max_steal']){
			showmessage('dsu_marcothief:msg_10', dreferer());
		}elseif($check['extcredits'.$config['credit']] < $config['credit_protect']){
			showmessage('dsu_marcothief:msg_4', dreferer(), array('title' => $_G['setting']['extcredits'][$config['credit']]['title']));
		}elseif($check['uid'] == $_G['uid']){
			showmessage('dsu_marcothief:msg_3', dreferer());
		}else{
			$sucess_percentage = (($success_shop[0]+$config['sucess_percentage'])>1) ? 1 : ($success_shop[0]+$config['sucess_percentage']);
			$success = (round((mt_rand(0, 100))/100, 1) <= $sucess_percentage) ? TRUE : FALSE;
			$police = (round((mt_rand(0, 100))/100, 1) <= $config['police_percentage']) ? TRUE : FALSE;
			$jail = (round((mt_rand(0, 100))/100, 1) <= $config['jail_percentage']) ? TRUE : FALSE;
			$rand = mt_rand(0, intval($config['max_thief_limit']));
			updatemembercount($_G['uid'], array('extcredits'.$config['fee'] => 'extcredits'.$config['fee']-$config['fee_once']));
			if($rand > $check['extcredits'.$config['credit']]){
				$success == FALSE;
			}
			if(!$check_db){
				DB::query("INSERT INTO ".DB::table('dsu_marcothief')." (uid) VALUES ('$check[uid]')");
			}
			if($check_db['protect'] > $_G['timestamp']){
				notification_add($check['uid'], 'system', lang('plugin/dsu_marcothief', 'notice_thief_guard'), array('username' => $_G['username']), 1);
				DB::query("UPDATE ".DB::table('dsu_marcothief')." SET thief=thief+'1',fail=fail+'1',action=action+'1' WHERE uid='$_G[uid]'");
				DB::query("UPDATE ".DB::table('dsu_marcothief')." SET steal=steal+'1',actions=actions+'1' WHERE uid='$check[uid]'");
				log_add($_G['username'], 'log_fail_guard');
				showmessage('dsu_marcothief:msg_33', dreferer(), array('username' => $check['username']));
			}elseif($success == TRUE && $rand != 0){
				notification_add($check['uid'], 'system', lang('plugin/dsu_marcothief', 'notice_thief'), array('username' => $_G['username'], 'lose' => $_G['setting']['extcredits'][$config['credit']]['title'].$rand.$_G['setting']['extcredits'][$config['credit']]['unit']), 1);
				updatemembercount($_G['uid'], array('extcredits'.$config['credit'] => 'extcredits'.$config['credit']+$rand));
				updatemembercount($check['uid'], array('extcredits'.$config['credit'] => 'extcredits'.$config['credit']-$rand));
				DB::query("UPDATE ".DB::table('dsu_marcothief')." SET thief=thief+'1',total=total+'$rand',sucess=sucess+'1',action=action+'1' WHERE uid='$_G[uid]'");
				DB::query("UPDATE ".DB::table('dsu_marcothief')." SET steal=steal+'1',lose=lose+'$rand',actions=actions+'1' WHERE uid='$check[uid]'");
				log_add($_G['username'], 'thief_sucess', array('thief_get' => $rand));
				showmessage('dsu_marcothief:msg_6', dreferer(), array('username' => $check['username'], 'earn' => $_G['setting']['extcredits'][$config['credit']]['title'].$rand.$_G['setting']['extcredits'][$config['credit']]['unit']));
			}else{
				DB::query("UPDATE ".DB::table('dsu_marcothief')." SET thief=thief+'1',fail=fail+'1',action=action+'1' WHERE uid='$_G[uid]'");
				DB::query("UPDATE ".DB::table('dsu_marcothief')." SET steal=steal+'1',actions=actions+'1' WHERE uid='$check[uid]'");
				notification_add($check['uid'], 'system', lang('plugin/dsu_marcothief', 'notice_thief_fail'), array('username' => $_G['username']), 1);
				if($police == TRUE && $user_db['weapon']){
					DB::query("DELETE FROM ".DB::table('dsu_marcothief_bag')." WHERE shopid='$user_db[weapon]' AND uid='$_G[uid]'");
					DB::query("UPDATE ".DB::table('dsu_marcothief')." SET weapon='0' WHERE uid='$_G[uid]'");
					log_add($_G['username'], 'thief_fail_police');
					showmessage('dsu_marcothief:msg_31', dreferer(), array('username' => $check['username']));
				}elseif($jail == TRUE){
					DB::query("UPDATE ".DB::table('dsu_marcothief')." SET jail='".($_G['timestamp']+60*$config['jail_mins'])."' WHERE uid='$_G[uid]'");
					log_add($_G['username'], 'thief_fail_jail');
					showmessage('dsu_marcothief:msg_8', dreferer(), array('mins' => $config['jail_mins']));
				}else{
					log_add($_G['username'], 'thief_fail');
					showmessage('dsu_marcothief:msg_5', dreferer(), array('username' => $check['username']));
				}
			}
		}
	}
	
}elseif($mod == 'jail'){
	if(submitcheck('uid')){
		if(($_G['gp_uid'] == $_G['uid']) || ($user_db['jail'] > $_G['timestamp'])){
			showmessage('dsu_marcothief:msg_11', dreferer());
		}elseif($user_db['extcredits'.$config['raids_credit']] < $config['raids_once']){
			showmessage('dsu_marcothief:msg_21', dreferer(), array('title' => $_G['setting']['extcredits'][$config['raids_credit']]['title']));
		}else{
			$raids_sucess_percentage = (($raids_sucess_shop[0]+$config['raids_sucess_percentage'])>1) ? 1 : ($raids_sucess_shop[0]+$config['raids_sucess_percentage']);
			$jail = (round((mt_rand(0, 100))/100, 1) <= $config['raids_percentage']) ? TRUE : FALSE;
			$success = (round((mt_rand(0, 100))/100, 1) <= $raids_sucess_percentage) ? TRUE : FALSE;
			$raid_user_info = getuserbyuid($_G['gp_uid']);
			updatemembercount($_G['uid'], array('extcredits'.$config['raids_credit'] => 'extcredits'.$config['raids_credit']-$config['raids_once']));
		}
		if($success == TRUE){
			notification_add($_G['gp_uid'], 'system', lang('plugin/dsu_marcothief', 'notice_raids'), array('username' => $_G['username']), 1);
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET jail='0',run='0',goodluck='0' WHERE uid='$_G[gp_uid]'");
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET raids=raids+'1' WHERE uid='$_G[uid]'");
			log_add($_G['username'], 'jail_raids', array('raids_user' => $raid_user_info['username']));
			showmessage('dsu_marcothief:msg_13', dreferer());
		}elseif($jail == TRUE){
			notification_add($_G['gp_uid'], 'system', lang('plugin/dsu_marcothief', 'notice_raids_fail'), array('username' => $_G['username']), 1);
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET jail='".($_G['timestamp']+60*$config['raids_mins'])."' WHERE uid='$_G[uid]'");
			showmessage('dsu_marcothief:msg_12', dreferer(), array('mins' => $config['raids_mins']));
		}else{
			notification_add($_G['gp_uid'], 'system', lang('plugin/dsu_marcothief', 'notice_raids_fail'), array('username' => $_G['username']), 1);
			showmessage('dsu_marcothief:msg_14', dreferer());
		}
	}elseif(submitcheck('run')){
		if($user_db['run'] > $_G['timestamp']){
			showmessage('dsu_marcothief:msg_15', dreferer(), array('mins' => round(($user_db['run']-$_G['timestamp'])/60, 0)));
		}
		$run_percentage = (($run_success_shop[0]+$config['run_percentage'])>1) ? 1 : ($run_success_shop[0]+$config['run_percentage']);
		$success = (round((mt_rand(0, 100))/100, 1) <= $run_percentage) ? TRUE : FALSE;
		if($success == TRUE){
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET jail='0',run='0',goodluck='0' WHERE uid='$_G[uid]'");
			showmessage('dsu_marcothief:msg_17', dreferer());
		}else{
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET run='".($_G['timestamp']+60*$config['run_mins'])."' WHERE uid='$_G[uid]'");
			showmessage('dsu_marcothief:msg_18', dreferer());
		}
	}elseif(submitcheck('money')){
		if($user_db['extcredits'.$config['raids_credit']] < $config['bribe_money']){
			showmessage('dsu_marcothief:msg_20', dreferer(), array('credit' => $_G['setting']['extcredits'][$config['raids_credit']]['title']));
		}else{
			updatemembercount($_G['uid'], array('extcredits'.$config['raids_credit'] => 'extcredits'.$config['raids_credit']-$config['bribe_money']));
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET jail='0',run='0',goodluck='0' WHERE uid='$_G[uid]'");
			showmessage('dsu_marcothief:msg_19', dreferer());
		}
	}elseif(submitcheck('goodluck')){
		$getluck_user = getuserbyuid(intval($_G['gp_goodluck_uid']));
		if($user_db['goodluck'] > $_G['timestamp']){
			showmessage('dsu_marcothief:msg_22', dreferer(), array('mins' => ($user_db['goodluck']-$_G['timestamp'])/60));
		}elseif(!$getluck_user){
			showmessage('username_nonexistence', dreferer());
		}else{
			notification_add(intval($_G['gp_goodluck_uid']), 'system', lang('plugin/dsu_marcothief', 'notice_goodluck'), array('username' => $_G['username']), 1);
			DB::query("UPDATE ".DB::table('dsu_marcothief')." SET goodluck='".($_G['timestamp']+$config['goodluck_mins'])."' WHERE uid='$_G[uid]'");
			showmessage('dsu_marcothief:msg_23', dreferer());
		}
	}else{
		$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_marcothief')." WHERE jail>'$_G[timestamp]'");
		$page = intval($_G['page']);
		$page = 10 && $page > 10 ? 1 : $page;
		$start_limit = ($page - 1) * 10;
		$multipage = multi($num, 10, $page, "plugin.php?id=dsu_marcothief&mod={$mod}", 10);
		$sql = "SELECT * FROM ".DB::table('dsu_marcothief')." t,".DB::table('common_member')." m WHERE t.uid=m.uid AND t.jail>'$_G[timestamp]' ORDER BY t.jail LIMIT $start_limit, 10";
		$query = DB::query($sql);
		$list = array();
		while($data = DB::fetch($query)){
			$data['jail'] = dgmdate($data['jail'], 'dt', $_G['setting']['timeoffset']);
			$list[] = $data;
		}
	}
	
}elseif($mod == 'list'){
	$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_marcothief')."");
	$page = intval($_G['page']);
	$page = 10 && $page > 10 ? 1 : $page;
	$start_limit = ($page - 1) * 10;
	$multipage = multi($num, 10, $page, ($_G['gp_by'] == 'raids') ? "plugin.php?id=dsu_marcothief&mod={$mod}&by={$_G[gp_by]}" : "plugin.php?id=dsu_marcothief&mod={$mod}", 10);
	$sql = "SELECT * FROM ".DB::table('dsu_marcothief')." t,".DB::table('common_member')." m WHERE t.uid=m.uid ORDER BY ".(($_G['gp_by'] == 'raids') ? 't.raids' : 't.total')." DESC LIMIT $start_limit, 10";
	$query = DB::query($sql);
	$list = array();
	while($data = DB::fetch($query)){
		$list[] = $data;
	}
	
}elseif($mod == 'shop'){
	if(submitcheck('buy')){
		if($check_db['jail'] > $_G['timestamp']){
			showmessage('dsu_marcothief:msg_34', dreferer());
		}
		$shop_db = DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcothief_shop')." WHERE id='$_G[gp_buy]'");
		if($user_db['extcredits'.$config['shop_fee']] < $shop_db['price']){
			showmessage('dsu_marcothief:msg_25', dreferer(), array('credit' => $_G['setting']['extcredits'][$config['shop_fee']]['title']));
		}else{
			DB::query("INSERT INTO ".DB::table('dsu_marcothief_bag')." (uid,shopid) VALUES ('$_G[uid]','$shop_db[id]')");
			updatemembercount($_G['uid'], array('extcredits'.$config['shop_fee'] => 'extcredits'.$config['shop_fee']-$shop_db['price']));
			showmessage('dsu_marcothief:msg_26', dreferer());
		}
	}else{
		$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_marcothief_shop')."");
		$page = intval($_G['page']);
		$page = 10 && $page > 10 ? 1 : $page;
		$start_limit = ($page - 1) * 10;
		$multipage = multi($num, 10, $page, "plugin.php?id=dsu_marcothief&mod={$mod}", 10);
		$sql = "SELECT b.uid,b.shopid,s.* FROM ".DB::table('dsu_marcothief_shop')." s LEFT JOIN ".DB::table('dsu_marcothief_bag')." b ON s.id=b.shopid AND b.uid='$_G[uid]' ORDER BY s.type LIMIT $start_limit, 10";
		$query = DB::query($sql);
		$list = array();
		while($data = DB::fetch($query)){
			if($data['type'] == 1){
				$data['function'] = lang('plugin/dsu_marcothief', 'shop_1').'+'.$data['function'].'%';
			}elseif($data['type'] == 2){
				$data['function'] = lang('plugin/dsu_marcothief', 'shop_2').'+'.$data['function'].'%';
			}elseif($data['type'] == 3){
				$data['function'] = lang('plugin/dsu_marcothief', 'shop_3').'+'.$data['function'].'%';
			}
			$list[] = $data;
		}
	}
	
}elseif($mod == 'bag'){
	if(submitcheck('pack')){
		$shop_db = DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcothief_shop')." WHERE id='$_G[gp_pack]'");
		$type = ($shop_db['type']==1) ? 'weapon' : (($shop_db['type']==2) ? 'raids_tool' : 'run_tool');
		DB::query("UPDATE ".DB::table('dsu_marcothief')." SET {$type}='$_G[gp_pack]' WHERE uid='$_G[uid]'");
		showmessage('dsu_marcothief:msg_32', dreferer(), array('name' => $shop_db['name']));
	}elseif(submitcheck('unpack')){
		$shop_db = DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcothief_shop')." WHERE id='$_G[gp_unpack]'");
		$type = ($shop_db['type']==1) ? 'weapon' : (($shop_db['type']==2) ? 'raids_tool' : 'run_tool');
		DB::query("UPDATE ".DB::table('dsu_marcothief')." SET {$type}='0' WHERE uid='$_G[uid]'");
		showmessage('dsu_marcothief:msg_35', dreferer(), array('name' => $shop_db['name']));
	}else{
		$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_marcothief_bag')."");
		$page = intval($_G['page']);
		$page = 10 && $page > 10 ? 1 : $page;
		$start_limit = ($page - 1) * 10;
		$multipage = multi($num, 10, $page, "plugin.php?id=dsu_marcothief&mod={$mod}", 10);
		$sql = "SELECT t.weapon,t.raids_tool,t.run_tool,b.uid,b.shopid,s.* FROM ".DB::table('dsu_marcothief')." t,".DB::table('dsu_marcothief_bag')." b,".DB::table('dsu_marcothief_shop')." s WHERE b.shopid=s.id AND t.uid='$_G[uid]' AND b.uid=t.uid ORDER BY s.type LIMIT $start_limit, 10";
		$query = DB::query($sql);
		$list = array();
		while($data = DB::fetch($query)){
			if($data['type'] == 1){
				$data['function'] = lang('plugin/dsu_marcothief', 'shop_1').'+'.$data['function'].'%';
			}elseif($data['type'] == 2){
				$data['function'] = lang('plugin/dsu_marcothief', 'shop_2').'+'.$data['function'].'%';
			}elseif($data['type'] == 3){
				$data['function'] = lang('plugin/dsu_marcothief', 'shop_3').'+'.$data['function'].'%';
			}
			$list[] = $data;
		}
		$magic_db = DB::fetch_first("SELECT * FROM ".DB::table('common_magic')." WHERE identifier='dsu_marcothief'");;
		$protect = ($user_db['protect']>$_G['timestamp']) ? lang('plugin/dsu_marcothief', 'bag_1', array('time' => dgmdate($user_db['protect'], 'dt', $_G['setting']['timeoffset']))) : (($magic_db['available']==1) ? '<a href="home.php?mod=magic&action=shop&operation=buy&mid=dsu_marcothief" onclick="showWindow(\'magics\', this.href);return false;" class="xi2 xw1">'.lang('plugin/dsu_marcothief', 'bag_2').'</a>' : lang('plugin/dsu_marcothief', 'bag_2'));
	}
	
}elseif($mod == 'admin_shop' && !$_G['gp_action']){
	is_admin();
	$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_marcothief_shop')."");
	$page = intval($_G['page']);
	$page = 10 && $page > 10 ? 1 : $page;
	$start_limit = ($page - 1) * 10;
	$multipage = multi($num, 10, $page, "plugin.php?id=dsu_marcothief&mod={$mod}", 10);
	$sql = "SELECT * FROM ".DB::table('dsu_marcothief_shop')." ORDER BY id LIMIT $start_limit, 10";
	$query = DB::query($sql);
	$list = array();
	while($data = DB::fetch($query)){
		if($data['type'] == 1){
			$data['function'] = lang('plugin/dsu_marcothief', 'shop_1').'+'.$data['function'].'%';
		}elseif($data['type'] == 2){
			$data['function'] = lang('plugin/dsu_marcothief', 'shop_2').'+'.$data['function'].'%';
		}elseif($data['type'] == 3){
			$data['function'] = lang('plugin/dsu_marcothief', 'shop_3').'+'.$data['function'].'%';
		}
		$list[] = $data;
	}
	
}elseif($mod == 'admin_shop' && $_G['gp_action'] == 'new'){
	is_admin();
	if(submitcheck('new')){
		$_G['gp_function'] = ($_G['gp_function']>100) ? 100 : intval(abs($_G['gp_function']));
		DB::query("INSERT INTO ".DB::table('dsu_marcothief_shop')." (type,name,intro,function,price) VALUES ('$_G[gp_type]','".strip_tags($_G['gp_name'])."','".strip_tags($_G['gp_intro'])."','$_G[gp_function]','".intval(abs($_G['gp_price']))."')");
		showmessage('dsu_marcothief:msg_27', 'plugin.php?id=dsu_marcothief&mod=admin_shop');
	}
	
}elseif($mod == 'admin_shop' && $_G['gp_action'] == 'edit' && $_G['gp_shopid']){
	is_admin();
	if(submitcheck('edit')){
		$_G['gp_function'] = ($_G['gp_function']>100) ? 100 : intval(abs($_G['gp_function']));
		DB::query("UPDATE ".DB::table('dsu_marcothief_shop')." SET type='$_G[gp_type]',name='".strip_tags($_G['gp_name'])."',intro='".strip_tags($_G['gp_intro'])."',function='$_G[gp_function]',price='".intval(abs($_G['gp_price']))."' WHERE id='".intval($_G['gp_shopid'])."'");
		showmessage('dsu_marcothief:msg_28', 'plugin.php?id=dsu_marcothief&mod=admin_shop');
	}else{
		$sql = "SELECT * FROM ".DB::table('dsu_marcothief_shop')." WHERE id=".intval($_G['gp_shopid'])."";
		$exist = DB::fetch_first($sql);
		if(!$exist){
			showmessage('dsu_marcothief:msg_29', dreferer());
		}else{
			$query = DB::query($sql);
			$list = array();
			while($data = DB::fetch($query)){
				$list[] = $data;
			}
		}
	}
	
}elseif($mod == 'admin_shop' && $_G['gp_action'] == 'delete' && $_G['gp_shopid']){
	is_admin();
	$id = intval($_G['gp_shopid']);
	$exist = DB::fetch_first("SELECT * FROM ".DB::table('dsu_marcothief_shop')." WHERE id='$id'");
	if(!$exist){
		showmessage('dsu_marcothief:msg_29', dreferer());
	}else{
		DB::query("UPDATE ".DB::table('dsu_marcothief')." SET weapon='0' WHERE weapon='$id'");
		DB::query("UPDATE ".DB::table('dsu_marcothief')." SET raids_tool='0' WHERE raids_tool='$id'");
		DB::query("UPDATE ".DB::table('dsu_marcothief')." SET run_tool='0' WHERE run_tool='$id'");
		DB::query("DELETE FROM ".DB::table('dsu_marcothief_bag')." WHERE shopid='$id'");
		DB::query("DELETE FROM ".DB::table('dsu_marcothief_shop')." WHERE id='$id'");
		showmessage('dsu_marcothief:msg_30', dreferer());
	}

}else{
	showmessage('undefined_action', NULL, 'HALTED');
}

include template('dsu_marcothief:dsu_marcothief');

?>
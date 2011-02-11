<?php
/*
	dsu_medalCenter (C)2010 Discuz Student Union
	This is NOT a freeware, use is subject to license terms

	$Id: admin_extend.inc.php 26 2011-01-08 19:00:35Z chuzhaowei@gmail.com $
*/
(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied');

require_once DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/function_common.php';

$modlist = dsuMedal_getOption('modlist');
$sysmod = array('script_market');

if(in_array($_G['gp_pdo'], array('install', 'upgrade', 'uninstall'))){ //脚本操作
	$classname = $_G['gp_classname'];
	if(!preg_match("/^[a-zA-Z0-9_]+$/", $classname) || !file_exists(DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/script/'.$classname.'.php')){
		cpmsg("BAD INPUT", '', 'error');
	}else if($_G['gp_pdo'] == 'uninstall' && in_array($classname, $sysmod)){
		cpmsg(lang('plugin/dsu_medalCenter','15'), '', 'error');
	}else{
		include DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/script/'.$classname.'.php';
		if(class_exists($classname)){
			$newclass = new $classname;
		}else{
			cpmsg(lang('plugin/dsu_medalCenter','16'), '', 'error');
		}
	}
	switch($_G['gp_pdo']){
		case 'install':
			$modlist[$classname] = $newclass->version;
			if(method_exists($newclass, 'install')) $newclass->install();
			$msg = lang('plugin/dsu_medalCenter','17');
			break;
		case 'uninstall':
			unset($modlist[$classname]);
			if(method_exists($newclass, 'uninstall')) $newclass->uninstall();
			$msg = lang('plugin/dsu_medalCenter','18');
			break;
		case 'upgrade':
			$modlist[$classname] = $newclass->version;
			if(method_exists($newclass, 'upgrade')) $newclass->upgrade();
			$msg = lang('plugin/dsu_medalCenter','19');
			break;
	}
	dsuMedal_saveOption('modlist', $modlist);
	cpmsg($msg, 'action=plugins&operation=config&identifier=dsu_medalCenter&pmod=admin_extend', 'succeed');
}else{
	showtips(lang('plugin/dsu_medalCenter','20'));
	showtableheader('');
	showsubtitle(array(lang('plugin/dsu_medalCenter','21'), lang('plugin/dsu_medalCenter','22'), lang('plugin/dsu_medalCenter','23'), ''));
	$dir = dir(DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/script/');
	while (false !== ($entry = $dir->read())) {
		if(substr($entry, 0, 7) != 'script_' || substr($entry, -4) != '.php') continue;
		include DISCUZ_ROOT.'./source/plugin/dsu_medalCenter/include/script/'.$entry;
		$classname = substr($entry, 0, -4);
		if(class_exists($classname)){
			$newclass = new $classname;
			if(empty($newclass->name)) continue;
			$adminaction = $namemsg = $versionmsg = '';
			$namemsg = $newclass->name;
			$versionmsg = $newclass->version;
			$introduction = empty($newclass->introduction) ? $newclass->name : $newclass->introduction;
			if(isset($modlist[$classname])){ //检查是否已经安装
				if($modlist[$classname] < $newclass->version){ //是否需要升级
					$adminaction .= "<a href=\"".ADMINSCRIPT."?action=plugins&operation=config&identifier=dsu_medalCenter&pmod=admin_extend&pdo=upgrade&classname=$classname\" class=\"act\">".lang('plugin/dsu_medalCenter','25')."</a>" ;
					$versionmsg .= '('.lang("plugin/dsu_medalCenter","24").$modlist[$classname].')';
				}
				$namemsg = "<strong>$newclass->name</strong>";
				$adminaction .= "<a href=\"".ADMINSCRIPT."?action=plugins&operation=config&identifier=dsu_medalCenter&pmod=admin_extend&pdo=uninstall&classname=$classname\" class=\"act\">".lang('plugin/dsu_medalCenter','26')."</a>";
			}else{
				$adminaction .= "<a href=\"".ADMINSCRIPT."?action=plugins&operation=config&identifier=dsu_medalCenter&pmod=admin_extend&pdo=install&classname=$classname\" class=\"act\">".lang('plugin/dsu_medalCenter','27')."</a>";
			}
			$namemsg = '<span title="'.$introduction.'">'.$namemsg.'</span>';
			showtablerow('', array('class="td25"', 'class="td25"', 'class="td25"', 'class="td25"'), array(
					$namemsg,
					$versionmsg,
					$newclass->copyright,
					$adminaction
				));
			
		}
	}
	$dir->close();
	showtablefooter();
}
?>
<?php
/**
 * [DSU] VIP Cron
 * Copyright 2010-2011, DSU Team. && Kookxiang
 */

if(!defined('IN_DISCUZ')) exit('Access Denied');

include_once libfile('class/vip');
$vip = $vip ? $vip : new vip();
$nowtime = TIMESTAMP;

// ���ڴ���
$vip->query("DELETE FROM pre_dsu_vip WHERE exptime<='{$nowtime}'");
$vip->query("UPDATE pre_dsu_vip v, pre_common_member m SET m.groupid=v.oldgroup WHERE m.uid=v.uid AND v.exptime<='{$nowtime}'");

// �ɳ�ֵ����
$vip->query("UPDATE pre_dsu_vip SET czz=czz+'{$vip->vars[vip_czzday]}' WHERE exptime>'{$nowtime}'", 'UNBUFFERED');
$vip->query("UPDATE pre_dsu_vip SET czz=czz+'{$vip->vars[vip_czz_year]}' WHERE year_pay=1 AND exptime>'{$nowtime}'", 'UNBUFFERED');

// �����ۿ��봦��
$vip->query("DELETE FROM pre_dsu_vip_codes WHERE exptime<='$nowtime'", 'UNBUFFERED');

// VIP�ȼ����û���󶨴���
$g = $vip->group;
$vip->query("UPDATE pre_dsu_vip v, pre_common_member m SET m.groupid={$g[1]}, v.level='1' WHERE m.uid=v.uid AND v.czz<600 AND m.adminid=0", 'UNBUFFERED');
$vip->query("UPDATE pre_dsu_vip v, pre_common_member m SET m.groupid={$g[2]}, v.level='2' WHERE m.uid=v.uid AND v.czz>=600 AND v.czz<1800 AND m.adminid=0", 'UNBUFFERED');
$vip->query("UPDATE pre_dsu_vip v, pre_common_member m SET m.groupid={$g[3]}, v.level='2' WHERE m.uid=v.uid AND v.czz>=1800 AND v.czz<3600 AND m.adminid=0", 'UNBUFFERED');
$vip->query("UPDATE pre_dsu_vip v, pre_common_member m SET m.groupid={$g[4]}, v.level='2' WHERE m.uid=v.uid AND v.czz>=3600 AND v.czz<6000 AND m.adminid=0", 'UNBUFFERED');
$vip->query("UPDATE pre_dsu_vip v, pre_common_member m SET m.groupid={$g[5]}, v.level='2' WHERE m.uid=v.uid AND v.czz>=6000 AND v.czz<10800 AND m.adminid=0", 'UNBUFFERED');
$vip->query("UPDATE pre_dsu_vip v, pre_common_member m SET m.groupid={$g[6]}, v.level='2' WHERE m.uid=v.uid AND v.czz>=10800 AND m.adminid=0", 'UNBUFFERED');
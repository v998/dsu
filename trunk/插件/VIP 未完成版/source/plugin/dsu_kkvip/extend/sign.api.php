<?php

// [DSU]VIP API For Paul's Sign.
$ext_name = '&#12304;DSU&#12305;&#27599;&#26085;&#31614;&#21040;(&#20316;&#32773;:shy9000) &#19987;&#29992;&#25509;&#21475;';		// extend's name, pls convert it at he.kookxiang.com

if(defined('IN_ADMINCP') && $_G['gp_api']){
	if(!$_G['gp_submit']) {
		$config = file_get_contents(DISCUZ_ROOT.'./data/vip_extend/dsu_paulsign.conf');
		showtableheader('[DSU]&#27599;&#26085;&#31614;&#21040;&#27169;&#22359; For VIP By [DSU]Shy9000');
		showformheader("plugins&operation=config&identifier=dsu_kkvip&pmod=api&api={$_G[gp_api]}");
		showsetting('&#31614;&#21040;&#21069;N&#21517;&#29305;&#21035;&#22870;&#21169;VIP&#22825;&#25968;/&#25104;&#38271;&#20540;', 'newconf', $config, 'textarea', '', '', '&#26684;&#24335;&#22914;&#19979;: &#27599;&#19968;&#34892;&#20195;&#34920;&#23545;&#24212;&#21517;&#25968;&#30340;&#21442;&#25968;,&#22914;3|2&#22312;&#31532;&#19968;&#34892;,&#21017;&#20195;&#34920;&#35813;&#29992;&#25143;&#22870;&#21169;3&#22825;VIP&#21644;2&#28857;&#25104;&#38271;&#20540;<br>&#22914;&#26524;&#19981;&#24819;&#32473;&#23545;&#24212;&#21517;&#25968;&#22870;&#21169;&#21017;&#23545;&#24212;&#34892;&#22635;&#20889;0|0.&#20999;&#21247;&#22635;&#38169;,&#21542;&#21017;&#31243;&#24207;&#23558;&#20250;&#20986;&#38169;');
		showsubmit('submit', '&#20445;&#23384;');
		showformfooter();
		showtablefooter();
	} elseif($_G['gp_submit'] && $_G['adminid']=='1' && $_G['gp_formhash']==FORMHASH) {
		@mkdir(DISCUZ_ROOT.'./data/vip_extend', 0777);
		file_put_contents(DISCUZ_ROOT.'./data/vip_extend/dsu_paulsign.conf', $_G['gp_newconf']);
		cpmsg('&#20445;&#23384;&#25104;&#21151;', "action=plugins&operation=config&identifier=dsu_kkvip&pmod=api&api={$_G[gp_api]}", 'succeed');
	}else{
		cpmsg('&#38169;&#35823;&#65306;&#38750;&#27861;&#35843;&#29992;&#25991;&#20214;', "action=plugins&operation=config&identifier=dsu_kkvip&pmod=api&api={$_G[gp_api]}", 'error');
	}
}elseif(defined('IN_DISCUZ')){
	require_once libfile('class/vip');
	$vip = $vip ? $vip : new vip();
	if($vip->on){
		$config = @file_get_contents(DISCUZ_ROOT.'./data/vip_extend/dsu_paulsign.conf');
		$nlconfig = str_replace(array("\r\n", "\n", "\r"), '/hhf/', $config);
		$rewards = explode('/hhf/', $nlconfig);
		$reward_id = $num + 1;
		if($reward[$reward_id]){
			list($rewarddays,$growupnum) = explode('|', $reward[$reward_id]);
			if($rewarddays) pay_vip($_G['uid'],$rewarddays,$_G['groupid']);
			if($growupnum) $vip->query("UPDATE pre_dsu_vip SET czz=czz+'{$growupnum}' WHERE uid='{$_G[uid]}'");
		}
	}
}

?>
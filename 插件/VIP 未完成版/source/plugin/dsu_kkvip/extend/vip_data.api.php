<?php

$ext_name = '&#12304;DSU&#12305;VIP&#25968;&#25454;&#22791;&#20221;/&#36824;&#21407; By kookxiang';		// extend's name, please convert it at he.kookxiang.com

if(defined('IN_ADMINCP') && $_G['gp_api']){
	if(submitcheck('backup')){
		if(preg_match('/[^A-Za-z0-9_]/', $_G['gp_filename'])) cpmsg('&#25991;&#20214;&#21517;&#31216;&#21547;&#26377;&#38750;&#27861;&#23383;&#31526;&#65281;');
		$file = DISCUZ_ROOT."./data/vip_backup/{$_G[gp_filename]}.vbak";
		@touch($file);
		if(!is_writeable($file)) cpmsg('&#25991;&#20214;&#19981;&#21487;&#20889;&#65292;&#35831;&#26816;&#26597;&#30446;&#24405;&#26435;&#38480;');
		$out_arr = array('codes' => array(), 'main' => array());
		$query = DB::query('SELECT * FROM '.DB::table('dsu_vip'));
		while($data = DB::fetch($query)){
			$out_arr['main'][] = $data;
		}
		$query = DB::query('SELECT * FROM '.DB::table('dsu_vip_codes'));
		while($data = DB::fetch($query)){
			$out_arr['codes'][] = $data;
		}
		$output = serialize($out_arr);
		file_put_contents($file, $output);
		cpmsg('&#22791;&#20221;&#25104;&#21151;&#65281;', "action=plugins&operation=config&identifier=dsu_kkvip&pmod=api&api={$_G[gp_api]}", 'succeed');
		dexit();
	}elseif(submitcheck('restore', 1)){
		$file = DISCUZ_ROOT."./data/vip_backup/{$_G[gp_filename]}";
		if(!file_exists($file)) cpmsg('&#22791;&#20221;&#25991;&#20214;&#19981;&#23384;&#22312;&#65281;');
		$data_str = file_get_contents($file);
		$data = unserialize($data_str);
		$main = $data['main'];
		$codes = $data['codes'];
		DB::query('TRUNCATE TABLE '.DB::table('dsu_vip'));
		DB::query('TRUNCATE TABLE '.DB::table('dsu_vip_codes'));
		foreach ($main as $line){
			DB::insert('dsu_vip', $line);
		}
		foreach ($codes as $line){
			DB::insert('dsu_vip_codes', $line);
		}
		cpmsg('&#36824;&#21407;&#25104;&#21151;&#65281;', "action=plugins&operation=config&identifier=dsu_kkvip&pmod=api&api={$_G[gp_api]}", 'succeed');
		dexit();
	}
	showtableheader('VIP&#25968;&#25454;&#22791;&#20221;');
	showformheader("plugins&operation=config&identifier=dsu_kkvip&pmod=api&api={$_G[gp_api]}");
	showsetting('&#22791;&#20221;&#25991;&#20214;&#21517;&#31216;', 'filename', random(10), 'text', '', '', '&#20648;&#23384;&#22312; /data/vip_backup/ &#19979;&#30340;&#25991;&#20214;&#21517;');
	showsubmit('backup', '&#24320;&#22987;&#22791;&#20221;');
	showformfooter();
	showtablefooter();
	showtableheader('VIP&#25968;&#25454;&#36824;&#21407;');
	!is_dir(DISCUZ_ROOT.'./data/vip_backup/') && @mkdir(DISCUZ_ROOT.'./data/vip_backup/', 0777);
	$backup_dir = @dir(DISCUZ_ROOT.'./data/vip_backup/');
	$flag = false;
	while(false !== ($entry = $backup_dir->read())) {
		$file = pathinfo($entry);
		if($file['extension'] == 'vbak' && $file['basename']) {
			showtablerow('', '', array(
				'&#22791;&#20221;: '.$file['basename'],
				dgmdate(filemtime(DISCUZ_ROOT."./data/vip_backup/{$file[basename]}"), 'u'),
				'<a href="?action=plugins&operation=config&identifier=dsu_kkvip&pmod=api&api='.$_G['gp_api'].'&filename='.$file['basename'].'&restore=yes&formhash='.FORMHASH.'">&#24320;&#22987;&#36824;&#21407;</a>',
			));
			$flag = true;
		}
	}
	if(!$flag) showtablerow('', '', array('<font color="red">&#26408;&#26377;&#22791;&#20221;&#20063;~</font>'));
	showtablefooter();
}

?>
<?php
if(!defined('IN_ADMINCP')) exit('Access Denied');

showtips(lang('plugin/dsu_stamp','tips'));
if (submitcheck('submit')){
	if($item_id=intval($_G['gp_edit'])){
		DB::query('UPDATE '.DB::table('dsu_stamp_list')." SET name='{$_G[gp_name]}', url='{$_G[gp_url]}' WHERE sid='{$item_id}'");
		cpmsg('dsu_stamp:edit_succeed','action=plugins&operation=config&identifier=dsu_stamp&pmod=stamp','succeed');
	}
	$del_arr=(array)$_G['gp_delete'];
	$new_arr=(array)$_G['gp_newname'];
	$newurl_arr=(array)$_G['gp_newurl'];
	if($del_arr){
		foreach ($_G['gp_delete'] as $item){
			if($item){
				$del_ids.=$del_ids ? ",'{$item}'" : "'{$item}'";
			}
		}
		if($del_ids){
			DB::delete('dsu_stamp_list', "sid IN ({$del_ids})");
			DB::delete('dsu_stamp', "sid IN ({$del_ids})");
		}
	}
	if($new_arr && $newurl_arr){
		foreach ($new_arr as $key=>$item){
			if($item){
				$temp_arr['name']=$item;
				$temp_arr['url']=$newurl_arr[$key];
				DB::insert('dsu_stamp_list', $temp_arr);
			}
		}
	}
	cpmsg('dsu_stamp:edit_succeed','action=plugins&operation=config&identifier=dsu_stamp&pmod=stamp','succeed');
}elseif($item_id=intval($_G['gp_edit'])){
	$stamp=DB::fetch_first('SELECT * FROM '.DB::table('dsu_stamp_list')." WHERE sid='{$item_id}'");
	showformheader('plugins&operation=config&identifier=dsu_stamp&pmod=stamp&edit='.$item_id);
	showtableheader(lang('plugin/dsu_stamp','stamp_edit'));
	showsetting(lang('plugin/dsu_stamp','stamp_name'), 'name', $stamp['name'], 'text');
	showsetting(lang('plugin/dsu_stamp','stamp_url'), 'url', $stamp['url'], 'text');
	echo "<tr><td><div style=\"padding-left:15px\"><img src=\"source/plugin/dsu_stamp/stamps/{$stamp[url]}\"></div></td></tr>";
	showsubmit('submit');
	showtablefooter();
	showformfooter();
	dexit();
}

echo '<script type="text/JavaScript">
var rowtypedata = [[
	[1,"", "td25"],
	[1,\'<input type="text" class="txt" name="newname[]" size="3">\', "td26"],
	[1,\'<input type="text" class="txt" name="newurl[]" size="3">\', "td26"],
	[1,"", "td28"],
]]
</script>';
showformheader('plugins&operation=config&identifier=dsu_stamp&pmod=stamp');
showtableheader(lang('plugin/dsu_stamp','stamp_manage'));
showsubtitle(array('', lang('plugin/dsu_stamp','stamp_name'), lang('plugin/dsu_stamp','stamp_url'), lang('plugin/dsu_stamp','stamp_action')));
$page=$_G['gp_page'] ? intval($_G['gp_page']) : 1;
$start=($page-1)*10;
$query=DB::query('SELECT * FROM '.DB::table('dsu_stamp_list')." ORDER BY sid LIMIT {$start},10");
while($result=DB::fetch($query)){
	showtablerow('', array('class="td25"', 'class="td26"', 'class="td26"', 'class="td28"'), array(
		'<input type="checkbox" class="checkbox" name="delete[]" value="'.$result['sid'].'" />',
		$result['name'],
		"<img width=\"32px\" src=\"source/plugin/dsu_stamp/stamps/{$result[url]}\"> &nbsp; {$result[url]}",
		lang('plugin/dsu_stamp','stamp_modify',array('sid'=>$result['sid'])),
	));
}
echo lang('plugin/dsu_stamp','stamp_add');
$amount=DB::result_first('SELECT COUNT(*) FROM '.DB::table('dsu_stamp_list'));
$multi=multi($amount, 10, $page, 'admin.php?action=plugins&operation=config&identifier=dsu_stamp&pmod=stamp', 0, 10, 1, 1);
showsubmit('submit', 'submit', 'del' , '', $multi);
showtablefooter();
showformfooter();
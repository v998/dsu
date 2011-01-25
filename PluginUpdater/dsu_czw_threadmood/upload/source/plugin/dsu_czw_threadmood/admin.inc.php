<?php
/*
	dsu_czw_threadmood (C)2007-2010 jhdxr
	This is NOT a freeware, use is subject to license terms

	$Id: admin.inc.php  jhdxr 2010-10-03 10:45$
*/
(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied');

$idtype = 'czw_threadmood';
if(!submitcheck('clicksubmit')) {
	shownav('【DSU】看帖心情', '心情管理');
	showtips('<ul id="tipslis"><li>本功能用于设置看帖心情，心情图片中请填写图片文件名，并将相应图片文件上传到 static/image/click/ 目录中。</li><li>最多启用8个心情，删除旧的心情时会清空相应的数据。</li></ul>');
	showformheader('plugins&operation=config&identifier=dsu_czw_threadmood&pmod=admin');
	showtableheader();
	showtablerow('', array('class="td25"', 'class="td28"', 'class="td25"', 'class="td25"', '', '', '', 'class="td23"', 'class="td25"'), array(
		'',
		cplang('display_order'),
		'',
		cplang('available'),
		cplang('name'),
		cplang('心情图片'),
		'',
	));
	print <<<EOF
<script type="text/JavaScript">
var rowtypedata = [
	[
		[1,'', 'td25'],
		[1,'<input type="text" class="txt" name="newdisplayorder[]" size="3">', 'td28'],
		[1,'', 'td25'],
		[1,'<input type="checkbox" name="newavailable[]" value="1">', 'td25'],
		[1,'<input type="text" class="txt" name="newname[]" size="10">'],
		[1,'<input type="text" class="txt" name="newicon[]" size="20">'],
		[1,'', 'td23']
	]
];
</script>
EOF;
	$query = DB::query("SELECT * FROM ".DB::table('home_click')." WHERE idtype='$idtype' ORDER BY displayorder DESC");
	while($click = DB::fetch($query)) {
		$checkavailable = $click['available'] ? 'checked' : '';
		$click['idtype'] = cplang('click_edit_'.$click['idtype']);
		showtablerow('', array('class="td25"', 'class="td28"', 'class="td25"', 'class="td25"', '', '', '', 'class="td23"', 'class="td25"'), array(
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"$click[clickid]\">",
			"<input type=\"text\" class=\"txt\" size=\"3\" name=\"displayorder[$click[clickid]]\" value=\"$click[displayorder]\">",
			"<img src=\"static/image/click/$click[icon]\">",
			"<input class=\"checkbox\" type=\"checkbox\" name=\"available[$click[clickid]]\" value=\"1\" $checkavailable>",
			"<input type=\"text\" class=\"txt\" size=\"10\" name=\"name[$click[clickid]]\" value=\"$click[name]\">",
			"<input type=\"text\" class=\"txt\" size=\"20\" name=\"icon[$click[clickid]]\" value=\"$click[icon]\">",
			''
		));
	}
	echo '<tr><td></td><td colspan="8"><div><a href="###" onclick="addrow(this, 0)" class="addtr">添加新心情</a></div></td></tr>';
	showsubmit('clicksubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();

} else {
	$ids = $updatearr = array();
	if(is_array($_G['gp_delete'])) {
		foreach($_G['gp_delete'] as $id) {
			$ids[] = $id;
			$updatearr['threadmood'.$id] = 0;
		}
		if($ids) {
			DB::query("DELETE FROM ".DB::table('home_click')." WHERE clickid IN (".dimplode($ids).")");
			DB::update('home_click', $updatearr);
		}
	}

	if(is_array($_G['gp_name'])) {
		foreach($_G['gp_name'] as $id => $val) {
			$id = intval($id);
			$updatearr = array(
				'name' => dhtmlspecialchars($_G['gp_name'][$id]),
				'icon' => $_G['gp_icon'][$id],
				'idtype' => $idtype,
				'available' => intval($_G['gp_available'][$id]),
				'displayorder' => intval($_G['gp_displayorder'][$id]),
			);
			DB::update('home_click', $updatearr, array('clickid' => $id));
		}
	}

	if(is_array($_G['gp_newname'])) {
		foreach($_G['gp_newname'] as $key => $value) {
			if($value != '' && $_G['gp_newicon'][$key] != '') {
				$data = array(
					'name' => dhtmlspecialchars($value),
					'icon' => $_G['gp_newicon'][$key],
					'idtype' => $idtype,
					'available' => intval($_G['gp_newavailable'][$key]),
					'displayorder' => intval($_G['gp_newdisplayorder'][$key])
				);
				DB::insert('home_click', $data);
			}
		}
	}

	$keys = $ids = $_G['cache']['click'] = array();
	$query = DB::query("SELECT * FROM ".DB::table('home_click')." WHERE available='1' ORDER BY displayorder DESC");
	while($value = DB::fetch($query)) {
		if(count($_G['cache']['click'][$value['idtype']]) < 8) {
			$keys[$value['idtype']] = $keys[$value['idtype']] ? ++$keys[$value['idtype']] : 1;
			$_G['cache']['click'][$value['idtype']][$keys[$value['idtype']]] = $value;
		} else {
			$ids[] = $value['clickid'];
		}
	}
	if($ids) {
		DB::query("UPDATE ".DB::table('home_click')." SET available='0' WHERE clickid IN (".dimplode($ids).")");
	}
	updatecache('click');
	cpmsg('看帖心情更新完成', 'action=plugins&operation=config&identifier=dsu_czw_threadmood&pmod=admin', 'succeed');
}

?>
<?php
if(submitcheck('submit')){
	DB::query('UPDATE '.DB::table('common_plugin')." SET available=1 WHERE identifier='dsu_updater'");
	$site_id=$_G['gp_site_id']?intval($_G['gp_site_id']):'';
	$key=strip_tags($_G['gp_key']);
	$_G['dsu_updater']['site_id']=$site_id;
	$_G['dsu_updater']['key']=$key;
	@touch(DISCUZ_ROOT.'./data/dsu_updater.inc.php');
	$output='<?php if(!defined("IN_DISCUZ")) dexit("Access Denied");$_G["dsu_updater"]='.var_export($_G['dsu_updater'], true).'?>';
	@file_put_contents(DISCUZ_ROOT.'./data/dsu_updater.inc.php',$output);
	@include_once DISCUZ_ROOT.'./source/discuz_version.php';
	echo "<div style=\"display:none\"><img title=\"CallBack\" align=\"right\" onerror=\"this.src='source/plugin/dsu_updater/images/error.png'\" src=\"http://update.dsu.cc/api.php?type=all&site_id={$site_id}&keyhash=".md5($key).'&charset='.CHARSET.'&dv='.DISCUZ_VERSION.'" /></div>';
	include DISCUZ_ROOT.'./source/plugin/dsu_updater/stat.php';
}elseif(!file_exists(DISCUZ_ROOT.'./data/dsu_updater.inc.php')){
	$call_back_url=$_G['siteurl'].'/plugin.php?id=dsu_updater:callback';
	echo '<style type="text/css">body {color: #535353;font: normal normal normal 12px/1.5em Tahoma,"hiragino sans gb",Helvetica,Arial;margin: 0;padding: 0;word-break: break-all;}.main {width: 370px;margin: -115px 0 0 -185px;border-width: 1px 2px 2px 1px;border-style: solid;border-color: #DDD;position: absolute;top: 50%;left: 50%;}.user_action {margin: 0 10px;overflow: hidden;}.user_action table{font-size: 13px;}.alert {color: #666;background-color: #FFFAE2;padding: 5px 10px;border-bottom: 1px solid #F5E190;}.content {margin: 0 10px;overflow: hidden;border-bottom: 1px dashed #DDD;_zoom: 1;}.content p{display: table;line-height: 20px;margin: 10px 0;font-size: 14px;zoom: 1;}.btn {border: 0;background: url(http://update.dsu.cc/images/btn.png) no-repeat;color: #2473A2;width: 90px;height: 28px;cursor: pointer;font-weight: bold;font-size: 14px;}</style><div class="main"><div class="alert">&#12304;DSU&#12305;&#25554;&#20214;&#26356;&#26032;&#21161;&#25163; - &#21021;&#22987;&#21270;</div><div class="content"><p>&#37197;&#32622;&#25554;&#20214;&#26356;&#26032;&#21161;&#25163;</p></div><div class="user_action"><p>&#31532;&#19968;&#27425;&#23433;&#35013;&#26412;&#31243;&#24207;&#65292;&#23558;&#33258;&#21160;&#20026;&#24744;&#33258;&#21160;&#30003;&#35831;&#31449;&#28857;Key&#65292;<font color="red">&#35831;&#21247;&#27844;&#28431;&#20197;&#19979;&#20449;&#24687;&#65281;</font></p><script src="http://update.dsu.cc/reg.php?url='.$call_back_url.'"></script><p style="text-align: center"><table><tr><td width="80px">&#31449;&#28857; ID</td><td><font color="red"><script>document.write(site_id);</script></font></td></tr><tr><td width="80px">&#23433;&#20840;&#23494;&#38053;</td><td><font color="red"><script>document.write(skey);</script></font></td></tr></table></p><form method="post" action="?'.$_SERVER['QUERY_STRING'].'"><script>document.write(\'<input type="hidden" name="site_id" value="\'+site_id+\'" />\');</script><script>document.write(\'<input type="hidden" name="key" value="\'+skey+\'" />\');</script><input type="hidden" name="formhash" value="'.FORMHASH.'" /><p align="right"><input type="submit" name="submit" class="btn" value="&#32487;&#32493;" /></p></form></div></div>';
}
?>
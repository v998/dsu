<?php

if(!defined('IN_ADMINCP')) exit('Access Denied');

include DISCUZ_ROOT.'./source/plugin/dsu_updater/core.func.php';

showtableheader('最新公告');
echo '<tr><td class="tipsblock"><ul id="tipslis">'.$_G['dsu_updater']['news'].'</ul></td></tr>';
showtablefooter();
showtableheader('最近更新插件');
echo '<tr><td class="tipsblock"><ul id="tipslis">'.$_G['dsu_updater']['new_plugin'].'</ul></td></tr>';
showtablefooter();
callback('news');
callback('new_plugin',1);
?>
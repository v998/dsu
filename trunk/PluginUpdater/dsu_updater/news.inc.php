<?php

if(!defined('IN_ADMINCP')) exit('Access Denied');

include DISCUZ_ROOT.'./source/plugin/dsu_updater/core.func.php';

showtableheader('���¹���');
echo '<tr><td class="tipsblock"><ul id="tipslis">'.$_G['dsu_updater']['news'].'</ul></td></tr>';
showtablefooter();
showtableheader('������²��');
echo '<tr><td class="tipsblock"><ul id="tipslis">'.$_G['dsu_updater']['new_plugin'].'</ul></td></tr>';
showtablefooter();
callback('news');
callback('new_plugin',1);
?>
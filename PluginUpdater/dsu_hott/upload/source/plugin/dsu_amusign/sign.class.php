<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_dsu_amusign {

}

class plugin_dsu_amusign_forum extends plugin_dsu_amusign {
	function viewthread_sign_output(){
		global $_G,$postlist;
		//获取语言包
		require './data/plugindata/dsu_amusign.lang.php';
		$sgid=$qids=array();
		$sgid = unserialize($_G['cache']['plugin']['dsu_amusign']['sgid']);
		$ngid = unserialize($_G['cache']['plugin']['dsu_amusign']['ngid']);
		$dayprice=$_G['cache']['plugin']['dsu_amusign']['dayprice'];
		$pricex=$_G['cache']['plugin']['dsu_amusign']['pricex'];
		$sign_vip=$_G['cache']['plugin']['dsu_amusign']['vip'];
		//---KK-VIP-----
		$kkvip_exists = file_exists("./source/plugin/dsu_kkvip/vip.func.php");
		if($kkvip_exists){require DISCUZ_ROOT.'./source/plugin/dsu_kkvip/vip.func.php';}
		//---KK-VIP-----		echo '<pre>';		print_R($postlist);
		if($postlist){
			$postuids = array();$belonguids = array();
			foreach ($postlist as $id => $postvalue){
				if($postvalue['authorid']){$postuids[] = $postvalue['authorid'];}
			}
			$postuid = implode(",", array_unique($postuids));
			//echo $postuid;
			if($postuid){
				$sql="SELECT * FROM ".DB::table("plugin_dsuamusign")." WHERE uid IN ({$postuid})";
				$querygg=DB::query($sql);
				while ($value=DB::fetch($querygg)){
					$ammlist['uid'] = $value['uid'];
					$ammlist['time'] = $value['time'];
					$ammlist['belong'] = $value['belong'];
					if($value['belong']){$belonguids[] = $value['belong'];}
					$ggprint[$value['uid']]=$ammlist;
				}
			}

			$belonguid = implode(",", array_unique($belonguids));
			//echo '|'.$belonguid;
			if($belonguid){
				$sql="SELECT * FROM ".DB::table("common_member_field_forum")." WHERE uid IN ({$belonguid})";
				$querygg=DB::query($sql);
				while ($value=DB::fetch($querygg)){
					$ammlist['uid'] = $value['uid'];
					$ammlist['sightml'] = $value['sightml'];
					$belong_array[$value['uid']]=$ammlist;
				}
			}

			foreach ($postlist as $id => $postvalue){
				$query = array();
				$postsign = $postvalue['signature'];
				$postuids []= $postvalue['authorid'];
				//---KK-VIP-----
				if(array_key_exists('vip',$postvalue) && $sign_vip){
					$uid_vip = $postvalue['vip'];
				}else{
					$uid_vip = 0;
					if($kkvip_exists && $_G['vip'] && $sign_vip){
						$uid_vip = check_vip($postvalue['authorid']);
					}else{
						$uid_vip = 0;
					}
				}
				//---KK-VIP-----
				if(!in_array($postvalue['groupid'],$sgid) && !$uid_vip){
					$query = $ggprint[$postvalue['authorid']];
					if(!$query){$query['time'] = 0;}
					if($query['time']<$_G['timestamp'] && $postsign){
						if($postvalue['authorid'] == $_G['uid']){
							$postsign = '<a href="javascript:;" onclick="showWindow(\'win\', \'plugin.php?id=dsu_amusign:cost&infloat=1&uid='.$_G['uid'].'\');return false;">';
							if($postvalue['gender'] == 1){
								$postsign .= $scriptlang['dsu_amusign']['no-sign11'];
							}elseif($postvalue['gender'] == 2){
								$postsign .= $scriptlang['dsu_amusign']['no-sign12'];
							}elseif(!$postvalue['gender']){
								$postsign .= $scriptlang['dsu_amusign']['no-sign13'];
							}
							$postsign .= '</a>';
						}else{
							$postsign = '<a href="javascript:;" onclick="showWindow(\'win\', \'plugin.php?id=dsu_amusign:cost&infloat=1&uid='.$postvalue['authorid'].'\');return false;">';
							if($postvalue['gender'] == 1){
								$postsign .= $scriptlang['dsu_amusign']['no-sign2'];
							}elseif($postvalue['gender'] == 2){
								$postsign .= $scriptlang['dsu_amusign']['no-sign2'];
							}elseif(!$postvalue['gender']){
								$postsign .= $scriptlang['dsu_amusign']['no-sign2'];
							}
							$postsign .= '</a>';
						}
					}elseif($query['time'] >= $_G['timestamp'] && $query['belong'] && $query['belong'] != $postvalue['authorid']){
						$postsign = $belong_array[$query['belong']]['sightml'];
					}elseif(!$postsign && $query['time'] < $_G['timestamp']){
						if($postvalue['authorid'] == $_G['uid']){$postsign = $scriptlang['dsu_amusign']['no-sign'];}else{$postsign = $scriptlang['dsu_amusign']['nosign'];}
					}
				}
				$postlist[$id]['signature']=$postsign;
				if(!$postlist[$id]['signature'] && $postvalue['authorid'] == $_G['uid']){$postlist[$id]['signature'] = $scriptlang['dsu_amusign']['no-sign'];}
			}
		}
	}
}


class plugin_dsu_amusign_home extends plugin_dsu_amusign {
	function spacecp_profile_top_output(){
		global $_G;
		if($_G['gp_op'] == 'bbs' && $_G['uid']){
			//获取语言包
			require './data/plugindata/dsu_amusign.lang.php';
			$sgid=array();
			$sgid = unserialize($_G['cache']['plugin']['dsu_amusign']['sgid']);
			$dayprice=$_G['cache']['plugin']['dsu_amusign']['dayprice'];
			$pricex=$_G['cache']['plugin']['dsu_amusign']['pricex'];
			$sign_vip=$_G['cache']['plugin']['dsu_amusign']['vip'];
			//---KK-VIP-----
			$kkvip_exists = file_exists("./source/plugin/dsu_kkvip/vip.func.php");
			if($kkvip_exists){require DISCUZ_ROOT.'./source/plugin/dsu_kkvip/vip.func.php';}
			$uid_vip = 0;
			if($kkvip_exists && $_G['vip'] && $sign_vip){
				$uid_vip = check_vip($_G['uid']);
			}else{
				$uid_vip = 0;
			}
			//---KK-VIP-----;
			if(!in_array($_G['groupid'],$sgid) && !$uid_vip){
				$query = DB::fetch_first("SELECT * FROM ".DB::table("plugin_dsuamusign")." WHERE uid = '{$_G['uid']}'");
				if(!$query){$query['time'] = 0;}
				if($query['time']<$_G['timestamp']){
					$return = '<div style="margin:10px 0;padding:8px 8px 8px 24px;border:1px dashed #FF9A9A;background:#FFC url(./static/image/common/locked.gif) no-repeat 6px 50%;font-size:12px;">'.$scriptlang['dsu_amusign']['home'].'[<a href="javascript:;" onclick="showWindow(\'win\', \'plugin.php?id=dsu_amusign:cost&infloat=1&uid='.$_G['uid'].'\');return false;"><FONT COLOR="#3300FF"><B>'.$scriptlang['dsu_amusign']['cost'].'</B></FONT></a>]'.$scriptlang['dsu_amusign']['juhao'].'</div>';
				}elseif($query['time'] > $_G['timestamp'] || $query['time'] == $_G['timestamp']){
					$showtime = date('Y-m-d H:i',$query['time']);
					$showdays = howlong($_G['timestamp'],$query['time']);
					$showday = '';
					if($showdays['0']){$showday = $showdays['0'].lang("plugin/dsu_amusign","day");}
					if($showdays['1']){$showday .= $showdays['1'].lang("plugin/dsu_amusign","hr");}
					if(!$showday){$showday = lang("plugin/dsu_amusign","min");}
					if(!$query['belong'] || $query['belong'] == $_G['uid']){
						$showlang = lang('plugin/dsu_amusign','qixian',array('time' => $showtime,'days' => $showday));
					}else{
						$user = getuserbyuid($query['belong']);
						$nameurl = '<a href="home.php?mod=space&uid='.$query['belong'].'" target="_blank">'.$user["username"].'</a>';
						$showlang = lang('plugin/dsu_amusign','belong',array('name' => $nameurl , 'time' => $showtime , 'days' => $showday));
					}
					$return = '<div style="margin:10px 0;padding:8px 8px 8px 24px;border:1px dashed #FF9A9A;background:#FFC url(./static/image/common/locked.gif) no-repeat 6px 50%;font-size:12px;">'.$showlang.'</div>';
				}
			}elseif(in_array($_G['groupid'],$sgid) || $uid_vip){
				$return = '<div style="margin:10px 0;padding:8px 8px 8px 24px;border:1px dashed #FF9A9A;background:#FFC url(./static/image/common/locked.gif) no-repeat 6px 50%;font-size:12px;">'.$scriptlang['dsu_amusign']['nocost'].'</div>';
			}
			return $return;
		}else{
			return '';
		}
	}
}

function howlong($now,$unix_timestamp){
       $date = $unix_timestamp - $now;
	   $day = $date/86400;
	   $days = floor($day);
	   $hour = ($day-$days)*24;
       $hours = floor($hour);
       $result = array($days,$hours);
       return $result;
}

function implodeids($array) {
	if(!empty($array)) {
		return "'".implode("','", is_array($array) ? $array : array($array))."'";
	} else {
		return ”;
	}
}
?>
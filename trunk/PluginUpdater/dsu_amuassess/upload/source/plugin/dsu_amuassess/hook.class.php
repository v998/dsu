<?php
!defined('IN_DISCUZ') && exit('Access Denied');
class plugin_dsu_amuassess {

	var $vars=array();

	function  plugin_dsu_amuassess() {
		global $_G;
		$this->vars = $_G['cache']['plugin']['dsu_amuassess'];
		$amu_ranks = array();
		$amu_rank = strip_tags($this->vars['rank']);
		$amupma= '/((.+)=(\d+),(\d+);)/';
		preg_match_all($amupma,$amu_rank,$amu_ranks,PREG_SET_ORDER);
		$this->amu_ranks = $amu_ranks;
	}
}

class plugin_dsu_amuassess_forum extends plugin_dsu_amuassess{
	function viewthread_sidetop_output(){
		global $_G,$postlist;
		$aid = $_G['forum_thread']['authorid'];$return = array();
		$tid = $_G['tid'];
		if($postlist && $aid){
			foreach ($postlist as $value){
				if($value['first']){
					$amu_query = DB::fetch_first("SELECT SUM(recommends),SUM(recommend_add),SUM(recommend_sub) FROM ".DB::table('forum_thread')." WHERE authorid='$aid'");
					$amu_recommends = $amu_query['SUM(recommends)'];
					$amu_recommend_add = $amu_query['SUM(recommend_add)'];
					$amu_recommend_sub = $amu_query['SUM(recommend_sub)'];
					if($amu_recommend_add || $amu_recommend_sub){$amu_hpl = round($amu_recommend_add*100/($amu_recommend_add + $amu_recommend_sub),1);}
					for($k=0;$k<count($this->amu_ranks,0);$k++){
						if($amu_recommends >= $this->amu_ranks[$k][3] && ($amu_recommends <= $this->amu_ranks[$k][4] || $this->amu_ranks[$k][4] == 0 )){
							$return[0] = '<dl class="pil">';
							if($this->amu_ranks[$k][4] && $this->vars['showmod']==2){
								$bilv = '|'.round($amu_recommends*100/($this->amu_ranks[$k][4]+1),1).'%';
								$return[0] .= '<dt>'.$this->vars['txt'].'</dt><dd id="amurecommend_add" onmouseover="showMenu({\'ctrlid\':this.id, \'pos\':\'12\'});">'.$this->amu_ranks[$k][2].$bilv.'</dd>';
							}							
							if($amu_hpl){
								$return[0] .= '<dt>'.$this->vars['txt2'].'</dt><dd>'.$amu_hpl.'%&nbsp;<span id="amuassess" onmouseover="showMenu({\'ctrlid\':this.id,\'pos\':\'43\'})"><IMG SRC="source/plugin/dsu_amuassess/images/more_'.$this->vars['icon'].'.png"></span></dd>'; 
							}
							$return[0] .= '</dl>';
						}
					}
				}
			}
		}
		$return[0] .= '<div id="amurecommend_add_menu" class="g_up" style="display:none"><div class="crly">&#24110;&#25105;&#21319;&#32423;&#23601;&#28857;&nbsp;:&nbsp;<a id="recommend_add2" href="forum.php?mod=misc&action=recommend&do=add&tid='.$tid.'" onclick="ajaxmenu(this, 3000, 1, 0, \'43\', \'recommendupdate(1)\');return false;">'.$_G['setting']['recommendthread']['addtext'].'</a> </div><div class="mncr"></div></div>';
		$ggprint=array();
		$fromfid = (array)unserialize($this->vars['fromfid']);
		if(count($fromfid,0)==1 && $fromfid[0]==''){
			$where = '';
		}else{
			$fromfid = array_diff($fromfid, array(null));
			$where = ' AND fid IN ('.implode(",", array_unique($fromfid)).')';
		}
		if($aid){
			$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('forum_thread')." WHERE authorid = '".$aid."' AND recommends > 0".$where);
			$page = max(1, intval($_G['gp_page']));
			$start_limit = ($page - 1) * 10;
			$multipage = multi($num, 10, $page, "plugin.php?id=dsu_amuassess:hook&authorid=".$aid);

			$sql="SELECT * FROM ".DB::table('forum_thread')." WHERE authorid = '".$aid."' AND recommends > 0".$where." ORDER BY recommends DESC LIMIT ".$start_limit." , 10";
			$querygg=DB::query($sql);
			$return[0] .= '<div id="amuassess_menu" style="display:none;width:340px;"><div class="crly bm_c"><ol class="xl xl1">';
			while ($value=DB::fetch($querygg)){
				$return[0] .= '<li><a href="forum.php?mod=viewthread&tid='.$value['tid'].'" target="_blank" style="color:#3083C7; white-space:nowrap;">'.cnsubstr($value['subject'],30).'</a><em style="position:absolute; width:90px; color:#B7B7B7; text-align:right; height:12px;left:240px;">'.$value['replies'].'/'.$value['views'].'</em></li>';
			}
			$return[0] .= '</ol></div></div>';
		}
		
		return $return;
	}
}

	/* 截取一定长度的完整的中文字符 */ 

	function cnsubstr($str,$strlen=20) { 
		if(empty($str)||!is_numeric($strlen)){ 
		return false; 
		} 
		if(strlen($str)<=$strlen){ 
		return $str; 
		} 

		//得到第$length个字符 并判断是否为非中文 若为非中文 
		//直接返回$length长的字符串 
		$str = diconv($str ,CHARSET, "GBK");
		$last_word_needed=substr($str,$strlen-1,1); 
		if(!ord($last_word_needed)>128){ 
		$needed_sub_sentence=substr($str,0,$strlen).'...'; 
		$needed_sub_sentence = diconv($needed_sub_sentence,"GBK",CHARSET);
		return $needed_sub_sentence; 
		}else{ 
		for($i=0;$i<$strlen;$i++){ 
		if(ord($str[$i])>128){ 
		$i++; 
		} 
		}//end of for 
		$needed_sub_sentence=substr($str,0,$i).'...'; 
		$needed_sub_sentence = diconv($needed_sub_sentence,"GBK",CHARSET);
		return $needed_sub_sentence; 
		} 
	} 
?>
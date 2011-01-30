<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


class plugin_dsu_amuqreply{

	function plugin_dsu_amuqreply(){
		global $_G;
		$this->radio = $_G['cache']['plugin']['dsu_amuqreply']['qrradio'];
		$this->txt = $_G['cache']['plugin']['dsu_amuqreply']['qrtxt'];
		$this->adgtxt = $_G['cache']['plugin']['dsu_amuqreply']['adgtxt'];
		$this->wtxt = $_G['cache']['plugin']['dsu_amuqreply']['qrwtxt'];
		$this->t = $_G['cache']['plugin']['dsu_amuqreply']['qrt'];
		$this->wt = $_G['cache']['plugin']['dsu_amuqreply']['qrwt'];
		$this->zq = $_G['cache']['plugin']['dsu_amuqreply']['qrzq'];
		$this->gid = (array)unserialize($_G['cache']['plugin']['dsu_amuqreply']['qrgid']);
		$this->admingid = (array)unserialize($_G['cache']['plugin']['dsu_amuqreply']['admingid']);
		$this->autogid = (array)unserialize($_G['cache']['plugin']['dsu_amuqreply']['autogid']);
		$this->bfid = (array)unserialize($_G['cache']['plugin']['dsu_amuqreply']['qrbfid']);
		$this->zshzs=$_G['cache']['plugin']['dsu_amuqreply']['zshzs'];
		$this->hzcfs=$_G['cache']['plugin']['dsu_amuqreply']['hzcfs']-1;
		$this->zfcfs=$_G['cache']['plugin']['dsu_amuqreply']['zfcfs']-1;
		$this->spgid=(array)unserialize($_G['cache']['plugin']['dsu_amuqreply']['spgid']);
	}

	function amuqreply_showrq(){
		global $_G;
		$return = '';
		$data = explode("\n",$this->txt);
		$dataw = explode("\n",$this->wtxt);
		for($k=0;$k<count($data,0);$k++){
			if(strstr($data[$k], '(=)')){
				$dk[$k] = explode("(=)",$data[$k]);
				if(strstr($dk[$k][0], ',')){
					$df[$k]['fid'] = explode(",",$dk[$k][0]);
				}else{
					$df[$k]['fid']=$dk[$k][0];
				}
				$df[$k]['v']=$dk[$k][1];
				if(in_array('0',$df[$k]['fid'])||in_array($_G['fid'],$df[$k]['fid'])||$df[$k]['fid']=='0'||$_G['fid']==$df[$k]['fid']){
					$out[].=$df[$k]['v'];$pd = $df[$k]['v'];
				}
			}
		}
		$i=0;
		while($i<count($dataw,0)){
			$dataw[$i]=str_replace('"', "&#34;", $dataw[$i]);
			$i++;
		}

		if(in_array($_G['groupid'],$this->admingid)){
			$ad_data = explode("\n",$this->adgtxt);
			for($k=0;$k<count($ad_data,0);$k++){
				if(strstr($ad_data[$k], '(=)')){
					$dk[$k] = explode("(=)",$ad_data[$k]);
					if(strstr($dk[$k][0], ',')){
						$df[$k]['fid'] = explode(",",$dk[$k][0]);
					}else{
						$df[$k]['fid']=$dk[$k][0];
					}
					$df[$k]['v']=$dk[$k][1];
					if(in_array('0',$df[$k]['fid'])||in_array($_G['fid'],$df[$k]['fid'])||$df[$k]['fid']=='0'||$_G['fid']==$df[$k]['fid']){
						$ad_out[].=$df[$k]['v'];$pd = $df[$k]['v'];
					}
				}
			}
		}
		if($this->radio==1 && $pd && in_array($_G['groupid'],$this->gid)&&in_array($_G['fid'],$this->bfid)){
			$return='<div id="rqcss">'.$this->t.'<select id="s1" onchange="qreplyfun()" style="width: 150px; height: 20px" ><option value=""></option>';
			$i=0;
			while($i<count($out,0)){
				$data[$i]=str_replace('"', "&#34;", $out[$i]);
				$w=round(rand(0,count($dataw,0)-1));
				$return.='<option value="'.$data[$i].$dataw[$w].'">'.$data[$i].'</option>';
				$i++;
			}
			$i=0;
			while($i<count($ad_out,0)){
				$ad_data[$i]=str_replace('"', "&#34;", $ad_out[$i]);
				$return.='<option value="'.$ad_data[$i].'">'.$ad_data[$i].'</option>';
				$i++;
			}
			$return.='</select>';
			$return.=$this->wt;
			if(in_array($_G['groupid'],$this->autogid)){$autosj = 'document.getElementById("fastpostsubmit").click();';}
			$return.='</div><script type="text/javascript">function qreplyfun(){var v = document.getElementById("s1").value; seditor_insertunit("fastpost", v,"");'.$autosj.'}</script><style type="text/css">#rqcss {width:100%;padding:4px 0;border:#ddedf7 1px dashed;background:#f1f9fd;color:#006699;} #rqcss img {margin-bottom:-3px;}</style>';
		}
	
		if($this->radio==1 && $pd && in_array($_G['groupid'],$this->gid) && $this->zq){
			$return='<div id="rqcss">'.$this->t.'<select id="s1" onchange="qreplyfun()" style="width: 150px; height: 20px" ><option value=""></option>';
			$i=0;
			while($i<count($out,0)){
				$data[$i]=str_replace('"', "&#34;", $out[$i]);
				$w=round(rand(0,count($dataw,0)-1));
				$return.='<option value="'.$data[$i].$dataw[$w].'">'.$data[$i].'</option>';
				$i++;
			}
			$i=0;
			while($i<count($ad_out,0)){
				$ad_data[$i]=str_replace('"', "&#34;", $ad_out[$i]);
				$return.='<option value="'.$ad_data[$i].'">'.$ad_data[$i].'</option>';
				$i++;
			}
			$return.='</select>';
			$return.=$this->wt;
			if(in_array($_G['groupid'],$this->autogid)){$autosj = 'document.getElementById("fastpostsubmit").click();';}
			$return.='</div><script type="text/javascript">function qreplyfun(){var v = document.getElementById("s1").value; seditor_insertunit("fastpost", v,"");'.$autosj.'}</script><style type="text/css">#rqcss {width:100%;padding:4px 0;border:#ddedf7 1px dashed;background:#f1f9fd;color:#006699;} #rqcss img {margin-bottom:-3px;}</style>';
		}
		return $return;
	}

	function amuqreply_postrq(){
		global $_G;
		if($_G['cache']['plugin']['dsu_amuqreply']['nrzz']&&isset($_G['gp_handlekey'])&&!in_array($_G['groupid'],$this->spgid)){
			if(isset($_G['gp_message'])){
				$message=diconv($_G['gp_message'] ,CHARSET, "GBK");
				$amuq0='/([\x80-\xFE][\x40-\x7E\x80-\xFE]){1}/';
				preg_match_all($amuq0,$message,$amuq1,PREG_SET_ORDER);
				if(count($amuq1,0)<$this->zshzs){showmessage("dsu_amuqreply:1", '',array('zshzs'=>$this->zshzs));}
				$amuqa='/([\x80-\xFE][\x40-\x7E\x80-\xFE])\1{'.$this->hzcfs.',}/';
				$amuqb='/(\w)\1{'.$this->zfcfs.',}/';
				preg_match_all($amuqa,$message,$amuqc,PREG_SET_ORDER);
				if(isset($_G['gp_message'])&&isset($amuqc[0])){$amuqc[0][1]=diconv($amuqc[0][1], "GBK" ,CHARSET);showmessage('dsu_amuqreply:2', '',array('cfwenzi'=>$amuqc[0][1].$amuqc[0][1].$amuqc[0][1].'......','hzcfs'=>$this->hzcfs+1));}
				preg_match_all($amuqb,$message,$amuqd,PREG_SET_ORDER);
				if(isset($_G['gp_message'])&&isset($amuqd[0])){$amuqd[0][1]=diconv($amuqd[0][1], "GBK" ,CHARSET);showmessage('dsu_amuqreply:3', '',array('cfwenzi'=>$amuqd[0][1].$amuqd[0][1].$amuqd[0][1].'......','zfcfs'=>$this->zfcfs+1));}
			}
		}
		if($_G['cache']['plugin']['dsu_amuqreply']['tidzz']&&$_G['cache']['plugin']['dsu_amuqreply']['nrzz']&&!isset($_G['gp_handlekey'])&&!in_array($_G['groupid'],$this->spgid)){	
			if(isset($_G['gp_message'])){
				$message=diconv($_G['gp_message'] ,CHARSET, "GBK");
				$amuq0='/([\x80-\xFE][\x40-\x7E\x80-\xFE]){1}/';
				preg_match_all($amuq0,$message,$amuq1,PREG_SET_ORDER);
				if(count($amuq1,0)<$this->zshzs){showmessage('dsu_amuqreply:10', 'javascript:history.back()',array('zshzs'=>$this->zshzs),array('refreshtime' => 7));}
				$amuqa='/([\x80-\xFE][\x40-\x7E\x80-\xFE])\1{'.$this->hzcfs.',}/';
				$amuqb='/(\w)\1{'.$this->zfcfs.',}/';
				preg_match_all($amuqa,$message,$amuqc,PREG_SET_ORDER);
				if(isset($message)&&isset($amuqc[0])){$amuqc[0][1]=diconv($amuqc[0][1], "GBK" ,CHARSET);showmessage('dsu_amuqreply:20', 'javascript:history.back()',array('cfwenzi'=>$amuqc[0][1].$amuqc[0][1].$amuqc[0][1].'......','hzcfs'=>$this->hzcfs+1),array('refreshtime' => 7));}
				preg_match_all($amuqb,$message,$amuqd,PREG_SET_ORDER);
				if(isset($message)&&isset($amuqd[0])){$amuqd[0][1]=diconv($amuqd[0][1], "GBK" ,CHARSET);showmessage('dsu_amuqreply:30', 'javascript:history.back()',array('cfwenzi'=>$amuqd[0][1].$amuqd[0][1].$amuqd[0][1].'......','zfcfs'=>$this->zfcfs+1),array('refreshtime' => 7));}
			}
		}
	}
}

class plugin_dsu_amuqreply_forum extends plugin_dsu_amuqreply {
	function viewthread_fastpost_content(){
		global $_G;
		$this->s= $this->amuqreply_showrq();
		return $this->s;
	}

	function post_dsu_amuqreply(){
		global $_G;
		$this->amuqreply_postrq();
	}
}
class plugin_dsu_amuqreply_group extends plugin_dsu_amuqreply {
	function viewthread_fastpost_content(){
		global $_G;
		if($this->zq){$this->s= $this->amuqreply_showrq();}
		return $this->s;
	}
	function post_dsu_amuqreply(){
		global $_G;
		if($this->zq){$this->amuqreply_postrq();}
	}
}
?>

<?php
!defined('P_W') && exit('Forbidden');
/**
 * �㳡��ط�����
 * 
 */
class PW_SquareService {
	function getLastPostUser($limit=10){
		$userIds=$this->_getElementList('lastpostuser',100,true);
		if(!S::isArray($userIds)) return array();
		shuffle($userIds);
		$userIds=array_slice($userIds,0,$limit);
		return $this->_getUserInfo($userIds);
	}
	function getFansDescUser($limit=10){
		$userIds=$this->_getElementList('totalfans',100,true);
		if(!S::isArray($userIds)) return array();
		shuffle($userIds);
		$userIds=array_slice($userIds,0,$limit);
		return $this->_getUserInfo($userIds);
	}
    function getLastUpgradeUser($limit=10){
		$userIds=$this->_getElementList('gradeuser',$limit,true);
		return $this->_getUserInfo($userIds);
	}
	function getFansBrand($limit=10){
		$brand=array();
		$yesterdayBrand=$this->_getYesterdayFansBrand();
		$todayBrand=$this->_getTodayFans($limit);
		if (!S::isArray($todayBrand))return array();
		foreach ($todayBrand as $k=>$v){
			$yesterday=array_search($v['uid'],$yesterdayBrand);
			if ($yesterdayBrand[$v['uid']]!==false &&$k>$yesterday){
				$todayBrand[$k]['brand']='down';
			}else{
				$todayBrand[$k]['brand']='up';
			}
		}
		return $todayBrand;
	}
	function _getYesterdayFansBrand(){
		extract(pwCache::getData(D_P.'data/bbscache/yesterday_fans_brand.php', false));
		return $yesterdayfansbrand;
	}
	function _getTodayFans($limit=10){
		$userIds= $this->_getElementList('todayfans',$limit,true);
		return $this->_getUserInfo($userIds);
	}
	
	function getLastThread($limit=10){
		$openforum=array();
		require_once(R_P.'require/bbscode.php');
		$openforum=$this->_getOpenforums();
		$service = L::loadClass('threads','forum');
		$data = $service->getLatestThreads($openforum, '', '', 0,$limit);
		if (!S::isArray($data))return array();
		foreach($data as $v){
			if ($v['ifhide']){
				$v['content'] ="[����������]";
			}
			if ($v['anonymous']){
				$v['author'] = '�����û�';
				$v['authorid'] = 0;
			}
			if ($v['locked']==2){
				$v['content'] ="�����ѱ��ر�";
				$v['subject']="�����ѱ��ر�";
			}
			$v['content'] = $this->_replace($v['content']);
			//$v['content'] = convert($v['content'],'','post');
			
			$v['content'] =	substrs($v['content'],160,'Y');
			list($v['postdate'], $v['postdate_s']) =getLastDate($v['postdate']);
			$arr[]=array(
				'tid' => $v['tid'],
				'fid' => $v['fid'],
				'author' => $v['author'],
				'authorid' => $v['authorid'],
				'subject'  => $v['subject'],
				'postdate' => $v['postdate'],
				'content' => $v['content'],
				'anonymous' => $v['anonymous'],

				'url' =>"read.php?tid=".$v['tid']
				);
			$uids[] = $v['authorid'];
		}
		$usersInfo= $this->_getUserInfo($uids,true);
		if (empty($arr))return array();		
		foreach ($arr as $k => $value) {
			!is_array($usersInfo[$value['authorid']]) && $usersInfo[$value['authorid']] = array();
			$arr[$k]['icon']=$usersInfo[$value['authorid']]['icon'];
		}
		return $arr;
	}
	
	function _replace($content){
		$content = preg_replace(
				array(
					"/\[s:(\d+)\]/is",
					"/\[mp3=[01]{1}\]([^\<\r\n\"']+?)\[\/mp3\]/is",
					"/\[wmv=[01]{1}\]([^\<\r\n\"']+?)\[\/wmv\]/is",
					"/\[wmv(?:=[0-9]{1,3}\,[0-9]{1,3}\,[01]{1})?\]([^\<\r\n\"']+?)\[\/wmv\]/is",
					"/\[rm(?:=[0-9]{1,3}\,[0-9]{1,3}\,[01]{1})\]([^\<\r\n\"']+?)\[\/rm\]/is",
					"/\[attachment=.+?\]/is",
					"/\[fly\]([^\[]*)\[\/fly\]/is",
					"/\[move\]([^\[]*)\[\/move\]/is",
					"/\[music=(\d+)\](.+?)\[\/music\]/is",
					"/\[img\]([^\<\r\n\"']+?)\[\/img\]/is",
					"/\[post\](.+?)\[\/post\]/is",
					"/\[hide=.+?\].+?\[\/hide\]/is",
					"/\[sell=.+?\].+?\[\/sell\]/is",
					"/\[quote\](.*?)\[\/quote\]/is",
					"/\[flash.+?\[\/flash\]/is"
				),
				array(
					"[����]",
					"[����]",
					"[����]",
					"[����]",
					"[����]",
					"[ͼƬ]",
					"[��Ƶ]",
					"[��Ƶ]",
					"[����]",
					"[ͼƬ]",
					"[��������]",
					"[��������]",
					"[���ݳ���]",
					"[����]",
					"[��Ƶ]"
				),
				$content,1
			);
		/*����Ҫ��ֻ��ʾһ��,���������*/
		$content = stripwindcode($content);
		$content = strip_tags($content);
		$content = preg_replace(array('/\s(?=\s)/','/[\n\r\t]/'), array('',''), $content);
		return $content;
	}
	function _getOpenforums(){
		
		extract(pwCache::getData(D_P.'data/bbscache/forum_cache_openforum.php', false));
		return $openforum;
	}


	/**
	 * ȡ���������б�
	 *
	 * @return array
	 */
	function getWeiboLives($num=10){
		$service = L::loadClass('weibo','sns');
		return $service->getWeiboLives($num);
		
	}


	function _getElementList($type,$limit=10,$isid=false){
		$element = L::loadClass('element');
	
		return $element->getElementList($type,$limit,$isid);
	}
	function _getUserInfo($userarr,$isid=false) {
		global $winduid;
		if (empty($userarr) || !is_array($userarr)) {
			return array();
		}
		$userInfos=$arr = array();
		$service =L::loadClass('userservice','user');
		$attentionService = L::loadClass('Attention', 'friend'); 
		$list= $service->getUsersWithMemberDataByUserIds($userarr);
		if (!S::isArray($list))return array();
		foreach ($list as  $value) {
			list($icon) = showfacedesign($value['icon'], 1, 's');
			$value['icon']=$icon;
			$value['isAttention'] = $attentionService->isFollow($winduid, $value['uid']);
			$arr[$value['uid']] = $value;
		}
		foreach ($userarr AS $val){
			$userInfos[]=$arr[$val];
		}
		if ($isid)return $arr;	
		return $userInfos;
	}
}
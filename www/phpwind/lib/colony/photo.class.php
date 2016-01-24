<?php
!defined('P_W') && exit('Forbidden');
/**
 * ���˿ռ����ҵ����
 * @package PW_Photo
 * @author suqian
 * @access public
 */
class PW_Photo {
	var $_winduid = 0;
	var $_windid = 0;
	var $_uid = 0;
	var $_groupid = 0;
	var $_manager = array();
	var $_dbshield = 0;
	var $_dbifftp = 0;
	var $_timestamp = 0;
	var $_page = 0;
	var $_perpage = 0;
	var $_pwModeImg = '';
	var $_ifriend = 0;
	function PW_Photo($uid,$ifriend,$page,$perpage){
		$this->__construct($uid,$ifriend,$page,$perpage);
	}
	function __construct($uid,$ifriend,$page,$perpage){
		global $winduid,$windid,$groupid,$manager,$db_shield,$db_ifftp,$timestamp,$pwModeImg;
		$this->_winduid = $winduid;
		$this->_windid = $windid;
		$this->_uid = $uid;
		$this->_groupid = $groupid;
		$this->_manager = $manager;
		$this->_dbshield = $db_shield;
		$this->_dbifftp = $db_ifftp;
		$this->_timestamp = $timestamp;
		$this->_page = $page;
		$this->_perpage = $perpage;
		$this->_pwModeImg = $pwModeImg;
		$this->_ifriend = $ifriend;
	}
	/**
	 * ���ö�ȡ����
	 * @param unknown_type $num
	 */
	function setPerpage($num) {
		$num = (int) $num ? (int) $num : 20;
		$this->_perpage = $num;
	}
	/**
	 *�Ƿ��ǳ�������Ա
	 *@return boolean
	 */
	function isSuper (){
		return in_array($this->_windid,$this->_manager);
	}
	/**
	 *�Ƿ������ڹ�����
	 *@return boolean
	 */
	function isManger (){
		return $this->_groupid == 3;
	}
	/**
	 *�Ƿ���й���Ȩ��
	 *@return boolean
	 */
	function isPermission(){
		if($this->isSuper() || $this->isManger()){
			return true;
		}
		return false;
	}
	
	/**
	 *�Ƿ����ɾ��Ȩ��
	 *@return boolean
	 */
	function isDelRight() {
		global $SYSTEM;
		return ($this->isSuper() || $SYSTEM['delalbum']);
	}
	/**
	 *�Ƿ����Լ�
	 *@return boolean
	 */
	function isSelf(){
		return $this->_uid == $this->_winduid;
	}
	/**
	 *�Ƿ����Լ������
	 *@param $albumOwnerId int ���ӵ����ID
	 *@return boolean
	 */
	function isMyAlbum($albumOwnerId){
		return $albumOwnerId == $this->_winduid;
	}
	/**
	 *�Ƿ���ָ���û����Լ������
	 *@param $albumOwnerId int ���ӵ����ID
	 *@param $ifriend int �Ƿ��Ǻ������
	 *@return boolean
	 */
	function isUserAlbum($albumOwnerId){
		if($this->_ifriend){
			return true;
		}
		return $albumOwnerId == $this->_uid;
	}
	/**
	 * ȡ��DAO�๤��,�������DB��
	 * @param string $daoName model���db��
	 * @param string $dir ���db��·��
	 * @return resource ����DAO
	 */
	function _getDaoFactory($daoName, $dir = 'colony'){
		static $dao = array();
		if(!isset($dao[$daoName])){
			$dao[$daoName] = L::loadDB($daoName, $dir);
		}
		return $dao[$daoName];
	}
	/**
	 * ȡ����ط�����ʵ������
	 *@param string $servicename �����������
	 *@return resource ������ط�����ʵ��
	 */
	function _getServiceFactory($servicename, $dir = ''){
		return L::loadClass($servicename, $dir);
	}
	
	/****************************�����ز���**************************/
	/**
	 *�������
	 *@param $data Array Ҫ�������������Ϣ
	 *@return int ��������ID
	 */
	function createAlbum($data){
		if((!$this->isPermission() && !$this->isSelf()) || empty($data) || !is_array($data)){
			return array();
		}
		$albumDao = $this->_getDaoFactory('CnAlbum');
		return	 $albumDao->insert($data);
	}
	/**
	 *ȡ���������
	 *@return int �����������
	 */
	function getAlbumNumByUid(){
		$albumDao = $this->_getDaoFactory('CnAlbum');
		$priacy = $this->_getAlbumBrowseListPriacy();
		$result = $albumDao->getAlbumNumByUid($this->_uid,0,$priacy);
		return $result['sum'];
	}
	/**
	 *ȡ������б�(�������˹�����������б�,���˿ռ�����б�)
	 *@return Array ��������б�
	 */
	function getAlbumBrowseList(){
		$albumDao = $this->_getDaoFactory('CnAlbum');
		$priacy = $this->_getAlbumBrowseListPriacy();
		$userInfo = $this->getUserInfoByUid();
		$result = $albumDao->getAlbumNumByUid($this->_uid,0,$priacy);
		$total = $result['sum'];
		$albumdb =  array();
		if($total){
			$pageCount = ceil($total / $this->_perpage);
			$this->_page = validatePage($this->_page,$pageCount);
			$albumList = $albumDao->getPageAlbumsByUid($this->_uid,$this->_page,$this->_perpage,0,$priacy);
			foreach($albumList as $key => $value){
				$value['sub_aname']  = substrs($value['aname'],18);
				$value['lasttime']	 = get_date($value['lasttime']);
				$value['lastphoto']	 = getphotourl($value['lastphoto']);
				if ($this->_dbshield && $userInfo['groupid'] == 6 && !$this->isPermission()) {
					$value['lastphoto'] = $this->_pwModeImg.'/banuser.gif';
				}
				$albumdb[] = $value;
			}
		}
		return array($total,$albumdb);	
		
	}
	
	/**
	 * �б��ȡ���������� $priacy
	 *@return Array ��������б���������
	 */
	function _getAlbumBrowseListPriacy() {
		$priacy = array();
		$friendService = $this->_getServiceFactory('Friend', 'friend');
		if(!$this->isSelf() && !$this->isPermission()){	
			$priacy = array_merge($priacy,array(0,3));
			$isFriend = $friendService->isFriend($this->_winduid,$this->_uid);
			if ($isFriend !== "null") $priacy = array_merge($priacy,array(1));
		}
		return $priacy;
	}
	/**
	 *ȡ����������б�
	 *@return Array ������������б�
	 */
	function getFriendAlbumsList(){
		$friendService = $this->_getServiceFactory('Friend', 'friend');
		$friendInfo = $friendService->getFriendInfoByUid($this->_uid);
		if(empty($friendInfo)){
			return array();
		}
		$priacy = array();
		if(!$this->isPermission()){
			$priacy = array_merge($priacy,array(0,1,3));		
		}
		$friend = $ouserData = array();
		foreach($friendInfo as $key => $value){
			$friend[$value['friendid']] = $value;
		}
		$ouserDao = $this->_getDaoFactory('Ouserdata', 'sns');
		$ouserData = $ouserDao->findUserPhotoPrivacy(array_keys($friend));
		foreach($ouserData as $key=>$value){
			if($value['photos_privacy'] == 2 && $friend[$value['uid']]){
				unset($friend[$value['uid']]);
			}
		}
		if(empty($friend)){
			return array();
		}
		$albumDao = $this->_getDaoFactory('CnAlbum');
		$result =  $albumDao->getAlbumsNumByUids(array_keys($friend),0,$priacy);
		$total = $result['total'];
		$albumdb =  array();
		if($total){
			$pageCount = ceil($total / $this->_perpage);
			$this->_page = validatePage($this->_page,$pageCount);
			$albumList = $albumDao->getAlbumsByUids(array_keys($friend),$this->_page,$this->_perpage,0,$priacy);
			foreach($albumList as $key => $value){
				$value['sub_aname']  = substrs($value['aname'],18);
				$value['lasttime']	 = get_date($value['lasttime']);
				$value['lastphoto']	 = getphotourl($value['lastphoto']);
				$ownerid = $value['ownerid'];
				if ($this->_dbshield && $friend[$ownerid]['groupid'] == 6 && !$this->isPermission()) {
					$value['lastphoto'] = $this->_pwModeImg.'/banuser.gif';
				}
				$albumdb[] = $value;
			}
		}
		
		return array($total,$albumdb);	
	}
	/**
	 *ȡ������б�
	 *@param $atype int   �������(0��ʾ�������,1��ʾȺ�����)
	 *@param $priacy Array ������Ȩ��
	 *@return Array ��������б�
	 */
	function getAlbumList($atype = 0,$priacy = array()){
		$albumDao = $this->_getDaoFactory('CnAlbum');
		$albumList = $albumDao->getAlbumsByUid($this->_uid,$atype,$priacy);
		return $albumList;
	}
	function updateAlbumInfo($aid,$data){
		if((!$this->isPermission && !$this->isSelf()) || empty($data) || !is_array($data) || intval($aid) <= 0){
			return false;
		}
		$albumDao = $this->_getDaoFactory('CnAlbum');
		$albumDao->update($data,$aid);
		return true;
	}
	/**
	 *ɾ�����
	 *@param $aid int ���ID
	 */
	function delAlbum($aid){
		if((!$this->isDelRight() && !$this->isSelf()) || intval($aid) <= 0){
			return array();
		}
		$photoDao = $this->_getDaoFactory('CnPhoto');
		$albumDao = $this->_getDaoFactory('CnAlbum');
		$photoList = $photoDao->getPhotosInfoByAid($aid);
		if(!empty($photoList)){
			$affected_rows = 0;
			foreach($photoList as $key => $value){
				pwDelatt($value['path'], $this->_dbifftp);
				if ($value['ifthumb']) {
					$lastpos = strrpos($value['path'],'/') + 1;
					pwDelatt(substr($value['path'], 0, $lastpos) . 's_' . substr($value['path'], $lastpos), $this->_dbifftp);
				}
				$affected_rows += delAppAction('photo',$value['pid'])+1;//TODO Ч�ʣ�
			}
			pwFtpClose($ftp);
			countPosts("-$affected_rows");
		}
		$photoDao->delPhotosByAid($aid);
		$albumDao->delete($aid);		
	}
	/**
	 *ȡ����������Ϣ
	 *@param $aid int ���ID
	 *@param $atype int ������� 0��ʾ������� 1��ʾȺ�����
	 *@return Array ������������Ϣ
	 */
	function getAlbumInfo($aid,$atype=0){
		$albumDao = $this->_getDaoFactory('CnAlbum');
		return $albumDao->getAlbumInfo($aid,$atype);
	}
	/**
	 *������Ȩ��
	 *@param $aid int ���ID
	 *@return Array ������������Ϣ
	 */
	function albumViewRight($aid){
		$albumDao = $this->_getDaoFactory('CnAlbum');
		$album = $albumDao->getAlbumInfo($aid,0);
		if(empty($album)){
			return 'data_error';
		}
		$ownerid = $album['ownerid'];
		/*if(!$this->isUserAlbum($ownerid)){
			return 'mode_o_photos_private_0';
		}*/
		$friendService = $this->_getServiceFactory('Friend', 'friend');
		if (!$this->isMyAlbum($ownerid) && $album['private'] == 1 && $friendService->isFriend($this->_winduid,$ownerid) !== true && !$this->isPermission()) {
			return 'mode_o_photos_private_1';
		}
		if (!$this->isMyAlbum($ownerid) && $album['private'] == 2 && !$this->isPermission()) {
			return 'mode_o_photos_private_2';
		}
		$cookiename = 'albumview_'.$aid;
		if (($album['albumpwd'] && PwdCode($album['albumpwd']) != GetCookie($cookiename)) && !$this->isMyAlbum($ownerid) && $album['private'] == 3 && !$this->isPermission()) {
			//GetCookie($cookiename) && Cookie($cookiename,'',time()-3600);
			return 'mode_o_photos_private_3';
		}
		return $album;
	}
	
	
	function getSort($sort,$field){
		if(!is_array($sort)){
			return array();
		}
		if(!isset($sort[0][$field])){
			return array();
		}
		$count = count($sort);
		for($i=$count;$i>0;$i--){
			for($j=0;$j<$i;$j++){
				if($sort[$j+1][$field] < $sort[$j][$field]){
					$tmp = $sort[$j];
					$sort[$j] = $sort[$j+1];
					$sort[$j+1] = $tmp;
					
				}
			}
		}
		return $sort;
		
	}
	
	/**************************��Ƭ��ز���****************************/
	
	/**
	 * �ϴ���Ƭ
	 * @param $aid int ����ID
	 * @return Array �ϴ���Ƭ�����Ϣ
	 **/
	function uploadPhoto($aid){
		if((!$this->isPermission && !$this->isSelf())){
			return 'colony_phototype';
		}
		$albumDao = $this->_getDaoFactory('CnAlbum');
		$photoDao = $this->_getDaoFactory('CnPhoto');
		if (!$aid) {
			$albumcheck = $albumDao->getAlbumNumByUid($this->_uid,0);
			if (empty($albumcheck)) {
				return 'colony_albumclass';
			} else {
				$userInfo = $this->getUserInfoByUid();
				$data = array(
						'aname'		=> getLangInfo('app','defaultalbum'),	
						'atype'		=> 0,
						'ownerid'	=> $this->_uid,		
						'owner'		=> $this->_windid,
						'lasttime'	=> $this->_timestamp,	
						'crtime'	=> $this->_timestamp,
					);			
				$aid = $albumDao->insert($data);
			}
		}
		if(!$aid){
			return 'colony_albumclass';
		}
		$albumInfo = $albumDao->getAlbumInfo($aid,0);
		if(empty($albumInfo)){
			return 'undefined_action';
		}
		$uploadNum = 0;
		foreach($_FILES as $k=>$v){
			(isset($v['name']) && $v['name'] != "") && $uploadNum++;
		}
		//* include_once pwCache::getPath(D_P.'data/bbscache/o_config.php');
		extract(pwCache::getData(D_P.'data/bbscache/o_config.php', false));
		if($o_maxphotonum && ($albumInfo['photonum']+$uploadNum) > $o_maxphotonum ){
			return 'colony_photofull';
		}
		L::loadClass('photoupload', 'upload', false);
		$img = new PhotoUpload($aid);
		PwUpload::upload($img);
		pwFtpClose($ftp);

		if (!$photos = $img->getAttachs()) {
			return 'colony_uploadnull';
		}
		$photoNum = count($photos);
		$pid = $img->getNewID();
	
		if (empty($albumInfo['lastphoto'])) {
			$albumDao->update(array('lastphoto' => $img->getLastPhotoThumb()), $aid);
		}
		return array($albumInfo,$pid,$photoNum,$photos);		
	}
	
	/**
	 *ȡ����һ����Ƭ
	 *@param $pid int ��Ƭ��ID
	 *@param $aid int ����ID
	 *@return Array ������һ����Ƭ�����Ϣ
	 */
	function getNextPhoto($pid){
		$photoDao = $this->_getDaoFactory('CnPhoto');
		$next_photo = $photoDao->getNextPhoto($pid,$aid);
		return "ok\t".$next_photo['pid'];	
	}
	/**
	 *ȡ����һ����Ƭ
	 *@param $pid int ��Ƭ��ID
	 *@param $aid int ����ID
	 *@return Array ������һ����Ƭ�����Ϣ
	 */
	function getPrevPhoto($pid){
		$photoDao = $this->_getDaoFactory('CnPhoto');
		$prev_photo = $photoDao->getPrevPhoto($pid);
		return "ok\t".$prev_photo['pid'];
	}
	/**
	 * ������Ƭ�����Ϣ
	 * @param $pid int ��Ƭ��ID
	 * @param $aid int ����ID
	 * @param $srcPhoto Array ��Ƭԭʼ��Ϣ
	 * @param $dstPhoto Array ��ƬĿ����Ϣ
	 * @return boolean �����Ƿ�ɹ�
	 */
	function updatePhoto($pid,$aid,$srcPhoto,$dstPhoto){
		if((!$this->isPermission && !$this->isSelf()) || intval($pid) <= 0 || intval($aid) <= 0){
			return false;
		}
		if(!is_array($srcPhoto) || !is_array($dstPhoto) || empty($dstPhoto) || empty($srcPhoto)){
			return false;
		}
		$albumDao = $this->_getDaoFactory('CnAlbum');
		$photoDao = $this->_getDaoFactory('CnPhoto');
		$ischage = false;
		if ($aid != $srcPhoto['aid'] && ($this->isPermission() || $this->isSelf())) {
			$dstPhoto['aid'] = $aid;
			$ischage = true;
		}
		$photoDao->update($dstPhoto,$pid);
		if($ischage){
			$phnum = array();
			$list = $photoDao->getPhotoNumsGroupByAid(array($aid,$srcPhoto['aid']));
			foreach($list as $key => $value){
				$phnum[$value['aid']] = $value['sum'];
			}
			$srcPhoto['aid'] or	$srcPhoto = $this->getPhotoInfo($pid);
			$srcAlbum = $this->getAlbumInfo($srcPhoto['aid']);
			if (empty($srcAlbum['lastphoto']) || ($srcPhoto['lastphoto']?$srcPhoto['lastphoto']:$srcPhoto['path']) == $srcAlbum['lastphoto']) {
				$result = $photoDao->getPhotosInfoByAid($srcPhoto['aid'],1,1);
				$lastphoto = $this->getPhotoThumb($result[0]['path'],$result[0]['ifthumb']);
			}
			$srcAlbumPhotoNum = $phnum[$srcPhoto['aid']] ? $phnum[$srcPhoto['aid']] : 0;
			$dstAlbumPhotoNum = $phnum[$aid] ? $phnum[$aid] : 0;
			$albumDao->update(array('photonum'=>$dstAlbumPhotoNum),$aid);
			$srcAlbumData = array('photonum'=>$srcAlbumPhotoNum);
			$lastphoto && $srcAlbumData['lastphoto'] = $lastphoto;
			$srcAlbumPhotoNum or $srcAlbumData['lastphoto'] = '';
			$albumDao->update($srcAlbumData,$srcPhoto['aid']);		
			$dstAlbumPhotoNum == 1 && $this->setCover($pid);
		}
		return true;
	}
	/**
	 * ɾ����Ƭ
	 *@param $pid int ��ƬID
	 *@return Array ������Ƭ�����Ϣ
	 */
	function delPhoto($pid) {
		if (intval($pid) <= 0) {
			return array();
		}
		$albumDao = $this->_getDaoFactory('CnAlbum');
		$photoDao = $this->_getDaoFactory('CnPhoto');
		$photo = $photoDao->getPhotoUnionInfoByPid($pid);

		if (empty($photo) || ($photo['ownerid'] != $GLOBALS['winduid'] && !$this->isDelRight())) {
			return array();
		}
		$photoDao->delete($pid);
		
		$thumbPath = $this->getPhotoThumb($photo['path'],$photo['ifthumb']);
		$photoPath = $this->getPhotoThumb($photo['path'],0);
		
		if (empty($photo['lastphoto']) || $thumbPath == $photo['lastphoto'] || $photoPath == $photo['lastphoto']) {	
			$result = $photoDao->getPhotosInfoByAid($photo['aid'],1,1);
			$data['lastphoto'] = $this->getPhotoThumb($result[0]['path'],$result[0]['ifthumb']);
		}
		$data['photonum'] = intval($photo['photonum'])-1;
		$albumDao->update($data,$photo['aid']);
		pwDelatt($photo['path'], $this->_dbifftp);
	//	if($photo['ifthumb']){
		pwDelatt($thumbPath, $this->_dbifftp);
	//		pwDelatt($path, $this->_dbifftp);
	//	}
		pwFtpClose($ftp);
		$photo['uid'] = $this->_uid;
		return $photo;
	}
	/**
	 *����������
	 *@param $pid int ��Ƭ��ID
	 *@return void
	 */
	function setCover($pid){
		if((!$this->isPermission && !$this->isSelf()) || intval($pid) <= 0){
			return array();
		}
		$albumDao = $this->_getDaoFactory('CnAlbum');
		$photoDao = $this->_getDaoFactory('CnPhoto');
		
		$photo = $photoDao->getPhotoUnionInfoByPid($pid);
		if(empty($photo)){
			return array();
		}
		$data['lastphoto'] = $this->getPhotoThumb($photo['path'],$photo['ifthumb']);
		$albumDao->update($data,$photo['aid']);
		$photo['uid'] = $this->_uid;
		return $photo;
	}
	/**
	 *ȡ����Ƭ�͸���Ƭ�������������Ϣ
	 *@param $pid int ��������ID
	 *@return Array ������Ƭ�����Ϣ 
	 */
	function getPhotoUnionInfo($pid){
		$photoDao = $this->_getDaoFactory('CnPhoto');
		$photo = $photoDao->getPhotoUnionInfoByPid($pid);
		return $photo;
	}
	
	function getPhotoInfo($pid) {
		$photoDao = $this->_getDaoFactory('CnPhoto');
		return $photoDao->get($pid);
	}
	
	/**
	 * �������IDȡ����Ƭ�б�
	 *@param $aid int ���ID
	 *@return Array ������Ƭ�б�
	 */
	function getPhotoListByAid($aid,$paging=true,$ifthumb = true){
		$album = $this->albumViewRight($aid);
		if(!is_array($album)){
			return $album;
		}
		$ownerid = 	$album['ownerid'];	
		$userInfo = $this->getUserInfoByUid($ownerid);
		$album['lastphoto']	= getphotourl($album['lastphoto']);
		if ($this->_dbshield && $userInfo['groupid'] == 6  && !$this->isPermission()) {
			$album['lastphoto'] = $this->_pwModeImg.'/banuser.gif';
			$album['aintro'] = appShield('ban_album_aintro');
		}
		$cnpho = array();
		if ($album['photonum']) {
			$pageCount = ceil($album['photonum'] / $this->_perpage);
			$this->_page = validatePage($this->_page,$pageCount);
			$photoDao = $this->_getDaoFactory('CnPhoto');
			!$paging && $this->_perpage = $album['photonum'];
			$photoList = $photoDao->getPagePhotosInfoByAid($aid,$this->_page,$this->_perpage,1);
			foreach ($photoList as $key => $value) {
				$value['defaultPath'] = $value['path'];
				$value['path'] = getphotourl($value['path'], $value['ifthumb'] && $ifthumb);
				if ($this->_dbshield && $userInfo['groupid'] == 6 && !$this->isPermission()) {
					$value['path'] = $this->_pwModeImg.'/banuser.gif';
				}
				$value['sub_pintro'] = substrs($value['pintro'],25);
				$value['uptime']	= get_date($value['uptime']);
				$cnpho[] = $value;
			}
		}
		return array($album,$cnpho);
	}
	/**
	 *�鿴��Ƭ
	 *@param $pid int ��ƬID
	 *@param $aid int ���ID
	 *@return Array ��Ƭ�����Ϣ
	 */
	function viewPhoto($pid){
		global $attachpath;
		$nearphoto = array();
        $register = array('db_shield'=>$this->_dbshield,"groupid"=>$this->_groupid,"pwModeImg"=>$this->_pwModeImg);
        L::loadClass('showpicture', 'colony', false);
        $sp = new PW_ShowPicture($register);
        list($photo,$nearphoto,$prePid,$nextPid) = $sp->getPictures($pid);
        if(empty($photo)){
        	return 'data_error';
        }
		$album = $this->albumViewRight($photo['aid']);
		if(!is_array($album)){
			return $album;
		}
		$photo['privacy'] = $album['privacy'];
		$photo['uptime'] = get_date($photo['uptime']);
		$photo['path'] = getphotourl($photo['basepath']);
		$tmpPath = substr($photo['path'], 0, strlen($attachpath) + 1) == "$attachpath/" ? R_P . $photo['path'] : $photo['path'];
		list($photo['w'],$photo['h']) = getimagesize($tmpPath);
		if ($this->_dbshield && $photo['groupid'] == 6  && !$this->isPermission()) {
			$photo['path'] = $this->_pwModeImg.'/banuser.gif';
			$photo['pintro'] = appShield('ban_photo_pintro');
		}
		$photoDao = $this->_getDaoFactory('CnPhoto');
		$data['hits'] = intval($photo['hits'])+1;
		$photoDao->update($data,$pid);
		return array($photo,$nearphoto,$prePid,$nextPid);		
	}
	/**
	 * ȡ���û������Ϣ
	 * @param $uid int �û�ID
	 * @return Array �����û���Ϣ
	 */
	function getUserInfoByUid($uid = 0){
		$userInfo = array();
		$uid = $uid ? $uid : $this->_uid;
		if($this->isSelf() && !$uid){
			$userInfo['groupid'] = $this->_groupid;
			$userInfo['uid'] = $this->_winduid;
			$userInfo['username'] = $this->_windid;
		}else{
			$userService = $this->_getServiceFactory('UserService', 'user'); /* @var $userService PW_UserService */
			$userinfo = $userService->get($uid);
		}
		return $userInfo;	
	}
	
	function getPhotoThumb($path,$ifthumb){
		$thumbpath = '';
		$lastpos = strrpos($path,'/') + 1;
		if($ifthumb){
			$thumbpath = substr($path, 0, $lastpos) . 's_' . substr($path, $lastpos);
		}else{
			$thumbpath = $path;
		}
		return $thumbpath;
	}
	
	function getAlbumAidsByUids($uids) {
		if(!$uids || !is_array($uids)) return false;
		$albumList = $result = array();
		$albumDao = $this->_getDaoFactory('Cnalbum'); /* @var $albumDao PW_CnalbumDB */
		$albumList = $albumDao->getAlbumByUids($uids);
		foreach ($albumList as $album) {
			$result[] = $album['aid'];
		}
		return $result;	
	}
	
	function delAlbumByUids($uids) {
		if(!$uids || !is_array($uids)) return false;
		$aids = array();
		$aids = $this->getAlbumAidsByUids($uids);
		foreach($aids as $aid) {
			$this->delAlbum($aid);
		}
		return true;
	}
}

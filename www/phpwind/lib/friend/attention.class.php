<?php
!defined('P_W') && exit('Forbidden');

/**
 * ��ע�����
 * 
 * @package  PW_Attention
 * @author   lmq
 * @abstract  
 */
class PW_Attention {
	
	var $_timestamp = 0;
	
	function PW_Attention() {
		global $timestamp;
		$this->_timestamp = $timestamp;
	}
	
	/**
	 * �û���ӹ�ע��ͬʱ������������
	 * 
	 * @param $uid	�û�
	 * @param $friendid ��ע�Ķ���
	 * @param $limit	����������
	 */
	function addFollow($uid, $friendid, $limit = 20, $from = '') {//fixed
		global $timestamp;
		if (!$uid || !$friendid) return false;
		if ($this->isFollow($uid, $friendid)) return 'user_attention_exists';
	
		$attentionDB = $this->_getAttentionDB();
		$attentionDB->insert(array(
			'uid' => $uid,
			'friendid' => $friendid,
			'joindate' => $this->_timestamp
		));
	
		$userServer = L::loadClass('UserService', 'user');
		$userServer->updateByIncrement($uid, array(), array('follows' => 1));
		$userServer->updateByIncrement($friendid, array(), array('fans' => 1, 'newfans' => 1));

		$medalservice = L::loadClass('medalservice','medal');
		$medalservice->runAutoMedal($friendid,'fans','fans',1);

		$friendService = L::loadClass('Friend', 'friend');
		if (!$friendService->isFriend($uid,$friendid) || $from == 'addFriend') {
			$this->addUserWeiboRelationsByFriendid($friendid, $uid, $limit);
		}
		/*���·�˿���м�¼*/
		L::loadClass('elementupdate', '', false);
		$elementupdate = new ElementUpdate('fans');
		$elementupdate->setCacheNum(100);
		$elementupdate->totalFansUpdate($friendid);
		$elementupdate->updateSQL();
		$elementupdate->setCacheNum(20);
		$elementupdate->todayFansUpdate($friendid);
		$elementupdate->updateSQL();	
		return true;
	}
	
	/**
	 * ��ӹ�עʱ,��������˵����������£�20����
	 * 
	 * @param $friendid
	 * @param $uid
	 * @param $limit
	 */
	function addUserWeiboRelationsByFriendid($friendid, $uid, $limit = 20) {
		if (!$uid || !$friendid) return false;
		$weiboService = $this->_serviceFactory('Weibo', 'sns'); /* @var $weiboService PW_Weibo */
		return $weiboService->pushData($uid, $friendid, $limit);
	}

	/**
	 * �����ע
	 * 
	 * ���1:������Ǻ���,�Ϳ���ɾ��¼
	 * ���2:����Ǻ���,����attentionΪ0 ������ע
	 * @param int $uid
	 * @param int $friendid
	 */
	function delFollow($uid, $friendid) {//fixed
		if (!$uid || !$friendid) return false;
		if (!$this->isFollow($uid, $friendid)) return 'user_not_exists';

		$attentionDB = $this->_getAttentionDB();
		$attentionDB->delByUidAndFriendid($uid, $friendid);

		$userServer = L::loadClass('UserService', 'user');
		$userServer->updateByIncrement($uid, array(), array('follows' => -1));
		$userServer->updateByIncrement($friendid, array(), array('fans' => -1));

		$medalservice = L::loadClass('medalservice','medal');
		$medalservice->runAutoMedal($friendid,'fans','fans',-1);
		
		$friendService = L::loadClass('Friend', 'friend');
		if ($friendService->isFriend($uid,$friendid)) {
			$privacyService = L::loadClass('privacy','sns');
			$myattention = $privacyService->getIsFollow($uid, 'friend');
			!$myattention && $this->delUserWeiboRelationsByFriendid($uid, $friendid);
		}
		return true;
	}
	
	/**
	 * �����Ī���˹�ע��ͬʱɾ����ע�ߵ�������
	 * 
	 * @param int $uid
	 * @param int $friendid
	 */
	function delUserWeiboRelationsByFriendid($uid, $friendid) {
		if (!$uid || !$friendid) return false;
		$weiboService = $this->_serviceFactory('Weibo', 'sns'); /* @var $weiboService PW_Weibo */
		return $weiboService->removeRelation($uid,$friendid);
	}
	
	/**
	 * �ж��Ƿ��ע
	 * 
	 * @param int $uid  
	 * @param int $friendid
	 * @return bool
	 */
	function isFollow($uid, $friendid) {//fixed
		if (!$uid || !$friendid) return false;
		$user = $this->getUserByUidAndFriendid($uid, $friendid);
		return !empty($user);
	}

	/**
	 * ��ȡ�ҹ�ע����/list
	 * 
	 * @param int $uid
	 */
	function getFollowList($uid, $page = 1, $perpage = 20) {//fixed
		if (!$uid) return false;
		$perpage = (int)$perpage;
		$offset = ($page -1 ) * $perpage;
		$attentionDB = $this->_getAttentionDB();
		return $attentionDB->getFollowList($uid, $offset, $perpage);
	}
	
	/**
	 * ��ȡ��ע�ҵ���/list
	 * 
	 * @param int $uid
	 */
	function getFansList($uid) {//fixed
		if (!$uid) return false;
		$attentionDB = $this->_getAttentionDB();
		return $attentionDB->getFansList($uid);
	}
	
	/**
	 * ��ȡ�ҹ�ע����UIDS/array(0=>uid1,1=>uid2,...n=>uidn);
	 * 
	 * @param int $uid
	 */
	function getUidsInFollowList($uid, $page = 1, $perpage = 500) {//fixed
		if (!$uid) return false;
		$users = $attention = array();
		$users = $this->getFollowList($uid, $page, $perpage);
		if (!$users) return array();
		foreach ($users as $user) {
			$attention[] = $user['friendid'];
		}
		return $attention;
	}
	
	/**
	 * �����û���ָ����ϵ���󣬻�ù�ע��Ϣ��
	 * 
	 * @param int $uid
	 * @param int $friendids
	 */
	function getFollowListByFriendids($uid,$friendids = array()) {//fixed
		if (!$uid) return false;
		$attentionDB = $this->_getAttentionDB();
		return $attentionDB->getFollowListByFriendids($uid, $friendids);
	}
	
	/**
	 * �����û���ָ����ϵ���󣬻�ù�עuids��
	 * 
	 * @param int $uid
	 * @param int $friendids
	 */
	function getUidsInFollowListByFriendids($uid,$friendids = array()) {//fixed
		if (!$uid) return false;
		$attentionInfo = $attentionUids = array();
		$attentionInfo = $this->getFollowListByFriendids($uid, $friendids);
		foreach ($attentionInfo as $attention) {
			$attentionUids[] = $attention['friendid'];
		}
		return $attentionUids;
	}
	
	function getUidsInFansListByFriendids($uid,$friendids = array()) {//fixed
		if (!$uid) return false;
		$attentionInfo = $attentionUids = array();
		$attentionDB = $this->_getAttentionDB();
		$attentionInfo = $attentionDB->getUidsInFansListByFriendids($uid, $friendids);
		foreach ($attentionInfo as $attention) {
			$attentionUids[] = $attention['uid'];
		}
		return $attentionUids;
	}
	
	/**
	 * �����û�id�ͺ���id,�ҳ������Ϣ
	 * 
	 * @param int $uid
	 * @param int $friendid
	 */
	function getUserByUidAndFriendid($uid, $friendid) {//fixed
		if (!$uid || !$friendid) return false;
		$attentionDB = $this->_getAttentionDB();
		return $attentionDB->getUserByUidAndFriendid($uid, $friendid);
	}
		
	function getFollowListInPage($uid, $page = 1, $perpage = 20) {//fixed
		if (!$uid) return false;
		$perpage = (int)$perpage;
		$offset = ($page -1 ) * $perpage;
		$attentionDB = $this->_getAttentionDB();
		$attention = $temp = array();
		$temp = $attentionDB->findAttentions($uid, $offset, $perpage);
		return $this->_formatAttentionsData($temp);
	}
	
	function getFansListInPage($uid, $page = 1, $perpage = 20) {//fixed
		if (!$uid) return false;
		$perpage = (int)$perpage;
		$offset = ($page -1 ) * $perpage;
		$attentionDB = $this->_getAttentionDB();
		$attention = $temp = array();
		$temp =  $attentionDB->findFans($uid, $offset, $perpage);
		return $this->_formatAttentionsData($temp);
	}
	
	function _formatAttentionsData($temp) {//fixed
		if(!$temp || !is_array($temp)) return false;
		require_once(R_P.'require/showimg.php');
		$result = array();
		foreach ($temp as $value) {
			list($value['face']) = showfacedesign($value['face'], '1', 's');
			$value['honor'] = substrs($value['honor'],90);
			$value['lastvisit']	= get_date($value['lastvisit']);
			$result[$value['uid']] = $value;
		}
		return $result;
	}
	
	/**
	 * ����ҹ�ע����/count
	 * 
	 * @param int $uid
	 */
	function countFollows($uid) {//fixed
		if (!$uid) return false;
		$attentionDB = $this->_getAttentionDB();
		return $attentionDB->countFollows($uid);
	}
	
	/**
	 * ��ñ���ע����/fans ����
	 * 
	 * @param int $uid
	 */
	function countFans($uid) {//fixed
		if (!$uid) return false;
		$attentionDB = $this->_getAttentionDB();
		return $attentionDB->countFans($uid);
	}
	
	/**
	 * �û� touid �Ƿ��û� uid ����
	 * @param int $uid �û�id
	 * @param array $uIds �����ε��û�id
	 * return bool
	 */
	function isInBlackList($uid, $touid) {
		if (!$uid || !$touid) return false;
		$attentionBlackListDB = $this->_getAttentionBlackListDB();
		return $attentionBlackListDB->isInBlackList($uid, $touid);
	}

	/**
	 * �������ĳ�û����˵��б�
	 * @param int $uid �����ε��û�id
	 * @param array $uIds �û��б�
	 * return array
	 */
	function getBlackListToMe($uid, $uIds = array()) {
		if (!$uid) return false;
		$attentionBlackListDB = $this->_getAttentionBlackListDB();
		$blackList = $attentionBlackListDB->getBlackListToMe($uid, $uIds);
		$array = array();
		if ($blackList) {
			foreach ($blackList as $key => $value) {
				$array[] = $value['uid'];
			}
		}
		return $array;
	}
	
	/**
	 * ���ĳ�û��������б�
	 * @param int $uid
	 * return array
	 */
	function getBlackList($uid) {
		if (!$uid) return false;
		$attentionBlackListDB = $this->_getAttentionBlackListDB();
		$blackList = $attentionBlackListDB->getBlackList($uid);
		$array = array();
		if ($blackList) {
			foreach ($blackList as $key => $value) {
				$array[] = $value['touid'];
			}
		}
		return $array;
	}

	function getNamesOfBlackList($uid) {
		if (!$uid) return false;
		if (!$blackList = $this->getBlackList($uid)) {
			return array();
		}
		$userService = L::loadClass('UserService', 'user');
		return $userService->getUserNamesByUserIds($blackList);
	}

	function setBlackList($uid, $newBlackList = array()) {
		if (!$uid) return false;
		$blackList = $this->getBlackList($uid);
		$attentionBlackListDB = $this->_getAttentionBlackListDB();
		if ($add = array_diff($newBlackList, $blackList)) {
			$attentionBlackListDB->add($uid, $add);
			foreach ($add as $val) {
				$this->delFollow($val, $uid);
			}
		}
		if ($del = array_diff($blackList, $newBlackList)) {
			$attentionBlackListDB->del($uid, $del);
		}
		return true;
	}
	
	/**
	 * ���������˿�û� top10
	 * return array
	 */
	function getTopFansUsers($num){
		$num = intval($num);
		if($num < 0) return array();
		global $timestamp,$db_uidblacklist;
		extract (pwCache::getData(D_P.'data/bbscache/o_config.php',false));
		$time = $this->_timestamp - ($o_weibo_hotfansdays ? intval($o_weibo_hotfansdays) * 86400 : 86400);
		$attentionDB = $this->_getAttentionDB();
		$topUserIds = $attentionDB->getTopFansUser($time,$num);
		$tagsService = L::loadClass('memberTagsService', 'user');
		$tagsData = $tagsService->getTagsByUidsForSource($topUserIds);
		$tags = array();
		foreach($tagsData as $v){
			$tags[$v['userid']][] = $v['tagname'];
		}
		$userService = L::loadClass('UserService','user');
		require_once(R_P . 'require/showimg.php');
		$userData = $userService->getByUserIds($topUserIds);
		$newUsersInfo = array();
		$data = array();
		if($db_uidblacklist) $topUserIds = array_diff($topUserIds,explode(',',$db_uidblacklist));
		foreach ($topUserIds as $uid){
			if(!$userData[$uid]) continue;
			$data[] = $userData[$uid];
		}
		
		foreach ($data as $key => $value) {
			list($value['icon']) = showfacedesign($value['icon'], 1, 's');
			$value['tags'] = S::isArray($tags[$value['uid']]) ? implode(' ', $tags[$value['uid']]) : $tags[$value['uid']];
			$newUsersInfo[$key] = $value;
		}
		return $newUsersInfo;
	}
	
	/**
	 * Get PW_FriendDB
	 * 
	 * @access protected
	 * @return PW_FriendDB
	 */
	function _getAttentionDB() {
		return L::loadDB('Attention', 'friend');
	}

	function _getAttentionBlackListDB() {
		return L::loadDB('attention_blacklist', 'friend');
	}
	
	/**
	 * ˽�м��ؼ�¼�������
	 * @param PW_$name
	 * @return PW_$name
	 */
	function _serviceFactory($name, $dir='') {
		$name = strtolower($name);
		return L::loadClass($name, $dir);
	}
}
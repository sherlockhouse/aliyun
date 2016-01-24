<?php
! defined ( 'P_W' ) && exit ( 'Forbidden' );
/**
 * �û���Ϣ�����࣬���������±�Ļ��� pw_members, pw_memberdata, pw_memberInfo, pw_memberCredit, pw_singleRight
 *
 */
class GatherCache_PW_Members_Cache extends GatherCache_Base_Cache {
	var $_defaultCache = PW_CACHE_MEMCACHE;
	var $_prefix = 'member_';

	var $_membersField = array ('uid' => null, 'username' => null, 'password' => null, 'safecv' => null, 'email' => null, 'groupid' => null, 'memberid' => null, 'groups' => null, 'icon' => null, 'gender' => null, 'regdate' => null, 'signature' => null, 'introduce' => null, 'oicq' => null, 'aliww' => null, 'icq' => null, 'msn' => null, 'yahoo' => null, 'site' => null, 'location' => null, 'honor' => null, 'bday' => null, 'lastaddrst' => null, 'yz' => null, 'timedf' => null, 'style' => null, 'datefm' => null, 't_num' => null, 'p_num' => null, 'attach' => null, 'hack' => null, 'newpm' => null, 'banpm' => null, 'msggroups' => null, 'medals' => null, 'userstatus' => null, 'shortcut' => null );
	var $_memberDataField = array ('uid' => null, 'postnum' => null, 'digests' => null, 'rvrc' => null, 'money' => null, 'credit' => null, 'currency' => null, 'lastvisit' => null, 'thisvisit' => null, 'lastpost' => null, 'onlinetime' => null, 'monoltime' => null, 'todaypost' => null, 'monthpost' => null, 'uploadtime' => null, 'uploadnum' => null, 'follows' => null, 'fans' => null, 'newfans' => null, 'newreferto' => null, 'newcomment' => null, 'onlineip' => null, 'starttime' => null, 'postcheck' => null, 'pwdctime' => null, 'f_num' => null, 'creditpop' => null, 'jobnum' => null, 'lastmsg' => null, 'lastgrab' => null, 'punch' => null,'newnotice' => null, 'newrequest' => null );
	var $_memberInfoField = array ('uid' => null, 'adsips' => null, 'credit' => null, 'deposit' => null, 'startdate' => null, 'ddeposit' => null, 'dstartdate' => null, 'regreason' => null, 'readmsg' => null, 'delmsg' => null, 'tooltime' => null, 'replyinfo' => null, 'lasttime' => null, 'digtid' => null, 'customdata' => null, 'tradeinfo' => null );
	var $_singleRightField = array ('uid' => null, 'visit' => null, 'post' => null, 'reply' => null );

	/**
	 * ��ȡһ��members����Ϣ
	 *
	 * @param int $userId
	 * @return array
	 */
	function getMembersByUserId($userId) {
		$userId = S::int ( $userId );
		if ($userId < 1) {
			return false;
		}
		$key = $this->_getMembersKey ( $userId );
		$result = $this->_cacheService->get ( $key );
		if ($result === false) {
			$result = $this->_getMembersByUserIdNoCache ( $userId );
			$result = $result ? $result : array();
			$this->_cacheService->set ( $key,  $result);
		}
		return $result;
	}

	/**
	 * ��ȡһ��MemberData��Ϣ
	 *
	 * @param int $userId
	 * @return array
	 */
	function getMemberDataByUserId($userId) {
		$userId = S::int ( $userId );
		if ($userId < 1) {
			return false;
		}
		$key = $this->_getMemberDataKey ( $userId );
		$result = $this->_cacheService->get ( $key );
		if ($result === false) {
			$result = $this->_getMemberDataByUserIdNoCache ( $userId );
			$result = $result ? $result : array();
			$this->_cacheService->set ( $key, $result );
		}
		return $result;
	}

	/**
	 * ��ȡһ��MemberInfo��Ϣ
	 *
	 * @param int $userId
	 * @return array
	 */
	function getMemberInfoByUserId($userId) {
		$userId = S::int ( $userId );
		if ($userId < 1) {
			return false;
		}
		$key = $this->_getMemberInfoKey ( $userId );
		$result = $this->_cacheService->get ( $key );
		if ($result === false ) {
			$result = $this->_getMemberInfoByUserIdNoCache ( $userId );
			$result = $result ? $result : array();
			$this->_cacheService->set ( $key, $result );
		}
		return $result;
	}

	/**
	 * ��ȡһ��SingleRight��Ϣ
	 *
	 * @param int $userId
	 * @return array
	 */
	function getSingleRightByUserId($userId) {
		$userId = S::int ( $userId );
		if ($userId < 1 ) {
			return false;
		}
		$key = $this->_getSingleRightKey ( $userId );
		$result = $this->_cacheService->get ( $key );
		if ($result === false){
			$result = $this->_getSingleRightByUserIdNoCache ( $userId );
			$result = $result ? $result : array();
			$this->_cacheService->set ( $key, $result );
		}
		return $result;
	}

	/**
	 * ������ȡһ��Members��Ϣ
	 *
	 * @param array $userIds
	 * @return array
	 */
	function getMembersByUserIds($userIds) {
		if (! S::isArray ( $userIds )) {
			return false;
		}
		$userIds = array_unique ( $userIds );
		$result = $_tmpResult = $keys = $_tmpUserIds = array ();	
		foreach ( $userIds as $userId ) {
			$keys [$this->_getMembersKey ( $userId )] = $userId;
		}
		if (($members = $this->_cacheService->get ( array_keys($keys) ))) {
			$_unique = $this->getUnique();
			foreach ($keys as $key=>$userId){
				$_key = $_unique . $key;
				if (isset($members[$_key]) && is_array($members[$_key])){
					$_tmpUserIds [] = $userId;
					$result[$userId] = $members[$_key];
				}
			}
		}		
		$userIds = array_diff ( $userIds, $_tmpUserIds );
		if ($userIds) {
			$_tmpResult = $this->_getMembersByUserIdsNoCache ( $userIds );
			foreach ($userIds as $userId){
				$this->_cacheService->set ( $this->_getMembersKey ( $userId ), isset($_tmpResult[$userId]) ? $_tmpResult[$userId] : array() );
			}
		}			
		return  (array)$result + (array)$_tmpResult;
	}

	/**
	 * ������ȡһ��MemberData��Ϣ
	 *
	 * @param array $userIds
	 * @return array
	 */
	function getMemberDataByUserIds($userIds) {
		if (! S::isArray ( $userIds )) {
			return false;
		}
		$userIds = array_unique ( $userIds );
		$result = $_tmpResult = $keys = $_tmpUserIds = array ();
		foreach ( $userIds as $userId ) {
			$keys [$this->_getMemberDataKey ( $userId )] = $userId;
		}
		if (($members = $this->_cacheService->get ( array_keys($keys) ))) {
			$_unique = $this->getUnique();
			foreach ($keys as $key=>$userId){
				$_key = $_unique . $key;
				if (isset($members[$_key]) && is_array($members[$_key])){
					$_tmpUserIds [] = $userId;
					$result[$userId] = $members[$_key];
				}
			}
		}
		$userIds = array_diff ( $userIds, $_tmpUserIds );
		if ($userIds) {
			$_tmpResult = $this->_getMemberDataByUserIdsNoCache ( $userIds );
			foreach ($userIds as $userId){
				$this->_cacheService->set ( $this->_getMemberDataKey ( $userId ), isset($_tmpResult[$userId]) ? $_tmpResult[$userId] : array() );
			}
		}		
		
		return (array)$result + (array)$_tmpResult;
	}

	/**
	 * ������ȡһ��MemberInfo��Ϣ
	 *
	 * @param array $userIds
	 * @return array
	 */
	function getMemberInfoByUserIds($userIds) {
		if (! S::isArray ( $userIds )) {
			return false;
		}
		$userIds = array_unique ( $userIds );
		$result = $_tmpResult = $keys = $_tmpUserIds = array ();
		foreach ( $userIds as $userId ) {
			$keys [$this->_getMemberInfoKey ( $userId )] = $userId;
		}
		if (($members = $this->_cacheService->get ( array_keys($keys) ))) {
			$_unique = $this->getUnique();
			foreach ($keys as $key=>$userId){
				$_key = $_unique . $key;
				if (isset($members[$_key]) && is_array($members[$_key])){
					$_tmpUserIds [] = $userId;
					$result[$userId] = $members[$_key];
				}
			}
		}		
		$userIds = array_diff ( $userIds, $_tmpUserIds );
		if ($userIds) {
			$_tmpResult = $this->_getMemberInfoByUserIdsNoCache ( $userIds );
			foreach ($userIds as $userId){
				$this->_cacheService->set ( $this->_getMemberInfoKey ( $userId ), isset($_tmpResult[$userId]) ? $_tmpResult[$userId] : array() );
			}
		}			
		return (array)$result + (array)$_tmpResult;
	}

	/**
	 * ������ȡһ��MemberCredit��Ϣ
	 *
	 * @param array $userIds
	 * @return array
	 */
	function getMemberCreditByUserIds($userIds) {
		if (! S::isArray ( $userIds )) {
			return false;
		}
		$userIds = array_unique ( $userIds );
		$result = $_tmpResult = $keys = $_tmpUserIds = array ();
		foreach ( $userIds as $userId ) {
			$keys [$this->_getMemberCreditKey ( $userId )] = $userId;
		}
		if (($members = $this->_cacheService->get ( array_keys($keys) ))) {
			$_unique = $this->getUnique();
			foreach ($keys as $key=>$userId){
				$_key = $_unique . $key;
				if (isset($members[$_key]) && is_array($members[$_key])){
					$_tmpUserIds [] = $userId;
					$result[$userId] = $members[$_key];
				}
			}
		}
		$userIds = array_diff ( $userIds, $_tmpUserIds );
		if ($userIds) {
			$_tmpResult = $this->_getMemberCreditByUserIdsNoCache ( $userIds );
			foreach ($userIds as $userId){
				$this->_cacheService->set ( $this->_getMemberCreditKey ( $userId ), isset($_tmpResult[$userId]) ? $_tmpResult[$userId] : array() );
			}
		}

		return (array)$result + (array)$_tmpResult;
	}

	/**
	 * ������ȡ�û�Ⱥ����Ϣ�� ����read.php�ڻ�ȡ�û���Ϣʱ����
	 *
	 * @param array $userIds
	 * @return array
	 */
	function getCmemberAndColonyByUserIds($userIds) {
		if (! S::isArray ( $userIds )) {
			return false;
		}
		$userIds = array_unique ( $userIds );
		$result = $_tmpResult = $keys = $_tmpUserIds = array ();
		foreach ( $userIds as $userId ) {
			$keys [$this->_getCmemberAndColonyKey ( $userId )] = $userId;
		}
		if (($members = $this->_cacheService->get ( array_keys($keys) ))) {
			$_unique = $this->getUnique();
			foreach ($keys as $key=>$userId){
				$_key = $_unique . $key;
				if (isset($members[$_key]) && is_array($members[$_key])){
					$_tmpUserIds [] = $userId;
					$result[$userId] = $members[$_key];
				}
			}
		}
		$userIds = array_diff ( $userIds, $_tmpUserIds );
		if ($userIds) {
			$_tmpResult = $this->_getCmemberAndColonyByUserIdsNoCache ( $userIds );
			foreach ($userIds as $userId){
				$this->_cacheService->set ( $this->_getCmemberAndColonyKey ( $userId ), isset($_tmpResult[$userId]) ? $_tmpResult[$userId] : array() );
			}
		}		
		return (array)$result + (array)$_tmpResult;
	}

	/**
	 * ��ȡһ���û�������Ϣ��Data��Ϣ��SingleRight��Ϣ������global.php���getUserByUid��������
	 * ʵ��������sql��� "SELECT m.*, md.*, sr.* FROM pw_members m LEFT JOIN pw_memberdata md ON m.uid=md.uid LEFT JOIN pw_singleright sr ON m.uid=sr.uid WHERE m.uid=" . S::sqlEscape($uid) . " AND m.groupid<>'0' AND md.uid IS NOT NULL"
	 * 
	 * @param int $userId
	 * @return array
	 */
	/**
	function getMembersAndMemberDataAndSingleRightByUserId($userId) {
		$userId = S::int ( $userId );
		if ($userId < 1) return false;
		$membersAndMemberData = $this->getAllByUserId($userId, true, true);
		$singleRight = $this->getSingleRightByUserId($userId);
		return  (array)$membersAndMemberData + ($singleRight ? (array)$singleRight : $this->_singleRightField);
	}
	**/

	/**
	 * ����һ���û�id��ȡ�û���
	 * 
	 * @param int $userId �û�id
	 * @return string
	 */
	function getUserNameByUserId($userId) {
		$userId = S::int ( $userId );
		if ($userId < 1) return false;
		$result = $this->getMembersByUserId ( $userId );
		return $result ? $result['username'] : false;
	}

	/**
	 * �����û�id������ȡ�û���
	 * 
	 * @param array $userIds �û�id����
	 * @return array ��uidΪkey���û���Ϊֵ������
	 */
	function getUserNameByUserIds($userIds) {
		if (! S::isArray ( $userIds )) {
			return false;
		}
		if (!($members = $this->getMembersByUserIds($userIds))) return false;
		$_userNames = array ();
		foreach ( $members as $member ) {
			$_userNames [$member ['uid']] = $member ['username'];
		}
		return $_userNames;
	}

	/**
	 * ��ȡ�û���Ϣ
	 *
	 * @param int $userId �û�ID
	 * @param bool $isMembers �Ƿ�ȡ�û���Ҫ��Ϣ
	 * @param bool $isMemberData �Ƿ�ȡ�û�������Ϣ
	 * @param bool $isMemberInfo �Ƿ�ȡ�û������Ϣ
	 * @return array|boolean
	 */
	
	function getAllFieldByUserId($userId, $isMembers = true, $isMemberData = false, $isMemberInfo = false) {
		$userId = S::int($userId);
		if ($userId < 1) return false;
		$members = $isMembers ? $this->getMembersByUserId($userId) : false;
		$memberData = $isMemberData ? $this->getMemberDataByUserId($userId) : false;
		$memberInfo = $isMemberInfo ? $this->getMemberInfoByUserId($userId) : false;
		return $this->_joinTables(array($isMembers, $isMemberData, $isMemberInfo), array($members, $memberData, $memberInfo));
	}
	
	
	/**
	 * �ӻ�����������ȡ�û�������Ϣ��Data��Ϣ��Info��Ϣ
	 *
	 * @param array $userIds
	 * @return array
	 */
	
	function getAllFieldByUserIds($userIds, $isMembers = true, $isMemberData = false, $isMemberInfo = false) {
		if (! S::isArray ( $userIds )) return false;
		$arrMembers = $isMembers ? $this->getMembersByUserIds($userIds) : array();
		$arrMemberData = $isMemberData ? $this->getMemberDataByUserIds($userIds) : array();
		$arrMemberInfo = $isMemberInfo ? $this->getMemberInfoByUserIds($userIds) : array();
		$result = array();
		foreach ($userIds as $userId){
			$isMembers && $members = isset ( $arrMembers [$userId]) ?  $arrMembers [$userId] : false;
			$isMemberData && $memberData = isset ( $arrMemberData [$userId]) ?  $arrMemberData [$userId] : false;
			$isMemberInfo && $memberInfo = isset ( $arrMemberInfo [$userId]) ?  $arrMemberInfo [$userId] : false;
			$tmp = $this->_joinTables(array($isMembers, $isMemberData, $isMemberInfo), array($members, $memberData, $memberInfo));
			$tmp && $result [$userId] = $tmp;
		}
		return $result;
	}
	
	
	/**
	 * ģ�����ݿ�left join��Ч��
	 *
	 * @param array $tables  ��Ҫ��ѯ�ı� array(true, false, true)
	 * @param array $values  ��Ӧ������Ҫ��ѯ�ı� array($result1, false, $result3)
	 * @return array
	 */
	function _joinTables($tables, $values){
		$tableField = array($this->_membersField, $this->_memberDataField, $this->_memberInfoField);
		$tableAlias = array('m.', 'md.', 'mi.');
		$first = false;
		$result = array();
		foreach ($tables as $k => $table){
			if (!$first && $table){
				if (!$values[$k]) return false;
				$first = true;
			}
			if ($first){
				!$values[$k] && $values[$k] = $tableField[$k];
				$values[$k][$tableAlias[$k]. 'uid'] = $values[$k]['uid'];
				if (isset($result['credit'])) {
					$values[$k]['creditinfo'] = $values[$k]['credit'];
					$values[$k][$tableAlias[$k]. 'credit'] = $values[$k]['credit'];
				}
				(!isset($result['credit']) && $table && $values[$k]['credit']) && $result['credit'] = $values[$k]['credit'];
				$result += $values[$k];
			}
		}
		return $first ? $result : false;
	}

	/**
	 * ����û�������Ϣ����
	 *
	 * @param array $userIds
	 */
	function clearCacheForMembersByUserIds($userIds) {
		$userIds = ( array ) $userIds;
		foreach ( $userIds as $uid ) {
			$this->_cacheService->delete ( $this->_getAllMembersKey ( $uid ) );
			$this->_cacheService->delete ( $this->_getMembersKey ( $uid ) );
		}
		return true;
	}

	/**
	 * ����û�Data��Ϣ����
	 *
	 * @param array $userIds
	 */
	function clearCacheForMemberDataByUserIds($userIds) {
		$userIds = ( array ) $userIds;
		foreach ( $userIds as $uid ) {
			$this->_cacheService->delete ( $this->_getAllMembersKey ( $uid ) );
			$this->_cacheService->delete ( $this->_getMemberDataKey ( $uid ) );
		}
		return true;
	}

	/**
	 * ����û�Info��Ϣ����
	 *
	 * @param array $userIds
	 */
	function clearCacheForMemberInfoByUserIds($userIds) {
		$userIds = ( array ) $userIds;
		foreach ( $userIds as $uid ) {
			$this->_cacheService->delete ( $this->_getAllMembersKey ( $uid ) );
			$this->_cacheService->delete ( $this->_getMemberInfoKey ( $uid ) );
		}
		return true;
	}

	/**
	 * ����û���SingleRight��Ϣ
	 *
	 * @param array $userIds
	 */
	function clearCacheForSingleRightByUserIds($userIds) {
		$userIds = ( array ) $userIds;
		foreach ( $userIds as $uid ) {
			$this->_cacheService->delete ( $this->_getSingleRightKey ( $uid ) );
		}
		return true;
	}

	function clearCacheForMemberCreditByUserIds($userIds) {
		$userIds = ( array ) $userIds;
		foreach ( $userIds as $uid ) {
			$this->_cacheService->delete ( $this->_getMemberCreditKey ( $uid ) );
		}
		return true;
	}

	function clearCacheForCmemberAndColonyByUserIds($userIds) {
		$userIds = ( array ) $userIds;
		foreach ( $userIds as $uid ) {
			$this->_cacheService->delete ( $this->_getCmemberAndColonyKey ( $uid ) );
		}
		return true;
	}

	function clearCacheForMemberTagsByUserIds($userIds) {
		$userIds = ( array ) $userIds;
		foreach ( $userIds as $uid ) {
			$this->_cacheService->delete ( $this->_getMemberTagsKey ( $uid ) );
		}
		return true;
	}
	
	/**
	 * ��ͨ������ֱ�Ӵ����ݿ��ȡһ���û�������Ϣ
	 *
	 * @param array $userIds �û�id����
	 * @return array
	 */
	function _getMembersByUserIdsNoCache($userIds) {
		if (! S::isArray ( $userIds )) return false;
		$membersDb = L::loadDB ( 'Members', 'user' );
		return $membersDb->getUsersByUserIds ( $userIds );
	}

	/**
	 * ��ͨ������ֱ�Ӵ����ݿ��ȡһ���û���Data��Ϣ
	 *
	 * @param array $userIds �û�id����
	 * @return array
	 */
	function _getMemberDataByUserIdsNoCache($userIds) {
		if (! S::isArray ( $userIds )) return false;
		$memberDataDb = L::loadDB ( 'MemberData', 'user' );
		return $memberDataDb->getUsersByUserIds ( $userIds );
	}

	/**
	 * ��ͨ������ֱ�Ӵ����ݿ��ȡһ���û���Info��Ϣ
	 *
	 * @param array $userIds �û�id����
	 * @return array
	 */
	function _getMemberInfoByUserIdsNoCache($userIds) {
		if (! S::isArray ( $userIds )) return false;
		$memberInfoDb = L::loadDB ( 'MemberInfo', 'user' );
		return $memberInfoDb->getUsersByUserIds ( $userIds );
	}

	/**
	 * ��ͬ������ֱ�Ӵ����ݿ��ȡһ���û�������Ϣ
	 *
	 * @param int $userId �û�id
	 * @return array
	 */
	function _getMembersByUserIdNoCache($userId) {
		$userId = S::int ( $userId );
		if ($userId < 1) return false;
		$membersDb = L::loadDB ( 'Members', 'user' );
		return $membersDb->get ( $userId );
	}

	/**
	 * ��ͬ������ֱ�Ӵ����ݿ��ȡһ���û�Data��Ϣ
	 *
	 * @param int $userId �û�id
	 * @return array
	 */
	function _getMemberDataByUserIdNoCache($userId) {
		$userId = S::int ( $userId );
		if ($userId < 1) return false;
		$memberDataDb = L::loadDB ( 'MemberData', 'user' );
		return $memberDataDb->get ( $userId );
	}

	/**
	 * ��ͬ������ֱ�Ӵ����ݿ��ȡһ���û�Info��Ϣ
	 *
	 * @param int $userId �û�id
	 * @return array
	 */
	function _getMemberInfoByUserIdNoCache($userId) {
		$userId = S::int ( $userId );
		if ($userId < 1) return false;
		$memberInfoDb = L::loadDB ( 'MemberInfo', 'user' );
		return $memberInfoDb->get ( $userId );
	}

	/**
	 * ��ͨ����������ݿ��ȡһ���û�Ȩ����Ϣ, ����ѯpw_singleRight��
	 *
	 * @param int $userId
	 * @return array
	 */
	function _getSingleRightByUserIdNoCache($userId) {
		$userId = S::int ( $userId );
		if ($userId < 1) return false;
		$singleRightDb = L::loadDB ( 'SingleRight', 'user' );
		return $singleRightDb->get ( $userId );
	}

	/**
	 * �����ݿ��ȡһ��MemberCredit����
	 *
	 * @param array $userIds
	 * @return array
	 */
	function _getMemberCreditByUserIdsNoCache($userIds) {
		if (!S::isArray($userIds)) return false;
		$memberCreditDb = L::loadDB ( 'MemberCredit', 'user' );
		$memberCredits = $memberCreditDb->gets ( $userIds );
		if (! S::isArray ( $memberCredits )) return false;
		$result = array ();
		foreach ( $memberCredits as $mc ) {
			$result [$mc ['uid']] [$mc ['cid']] = $mc ['value'];
			$result[$mc ['uid']]['uid'] = $mc ['uid'];
		}
		return $result;
	}

	/**
	 * ��ͨ������ֱ�Ӵ����ݿ��ȡ�û�Ⱥ����Ϣ�� ��Ҫ�����ѯpw_cmembers��pw_colonys
	 *
	 * @param int $threadId ����id
	 * @return array
	 */
	function _getCmemberAndColonyByUserIdsNoCache($userIds) {
		$cmembersDb = L::loadDB ( 'cmembers', 'colony' );
		return $cmembersDb->getsCmemberAndColonyByUserIds ( $userIds );
	}

	/**
	 * ��ȡ�û�������Ϣ�Ļ���key
	 *
	 * @param int $userId �û�id
	 * @return string
	 */
	function _getMembersKey($userId) {
		return $this->_prefix . 'main_uid_' . $userId;
	}

	/**
	 * ��ȡ�û�Data��Ϣ�Ļ���key
	 *
	 * @param int $userId �û�id
	 * @return string
	 */
	function _getMemberDataKey($userId) {
		return $this->_prefix . 'data_uid_' . $userId;
	}

	/**
	 * ��ȡ�û�Info��Ϣ�Ļ���key
	 *
	 * @param int $userId �û�id
	 * @return string
	 */
	function _getMemberInfoKey($userId) {
		return $this->_prefix . 'info_uid_' . $userId;
	}

	/**
	 * ��ȡSingleRight����key
	 *
	 * @param int $userId
	 * @return string
	 */
	function _getSingleRightKey($userId) {
		return $this->_prefix . 'singleright_uid_' . $userId;
	}

	/**
	 * ��ȡMemberCredit����key
	 *
	 * @param int $userId
	 * @return string
	 */
	function _getMemberCreditKey($userId) {
		return $this->_prefix . 'credit_uid_' . $userId;
	}

	/**
	 * ���ԱȺ����Ϣ��memcache�����key
	 *
	 * @param int $userId �û�id
	 * @return string
	 */
	function _getCmemberAndColonyKey($userId) {
		return $this->_prefix . 'colony_uid_' . $userId;
	}

	/**
	 * ��ȡ�û���ǩ��memcache�����key
	 *
	 * @param int $userId �û�id
	 * @return string
	 */
	function _getMemberTagsKey($userId) {
		return $this->_prefix . 'membertag_uid_' . $userId;
	}
	
	
	/************************ �ָ���**********************************/

	function _getMembersAndMemberDataAndMemberInfoByUserIdsNoCache($userIds){
		global $customfield;
		$fieldinfo = '';
		if (is_array($customfield)) {
			foreach ($customfield as $value) {
				!$value['ifsys'] && $fieldinfo .= ',mi.field_'.(int)$value['id'];
			}
		}
		$membersDb = L::loadDB ( 'Members', 'user' );
		return $membersDb->getMembersAndMemberDataAndMemberInfoByUserIds ( $userIds,$fieldinfo );
	}
	function _getAllMembersKey($userId){
		return $this->_prefix . 'all_uid_' . $userId;
	}
	
	/**
	 * ��ȡһ���û���Ϣ
	 * ��ѯmembers,memberData, memberInfo���ű�Ĳ����ֶΣ� ����global.php, read.phpҳ�����ض��ط�����
	 *
	 * @param array $userIds
	 * @param unknown_type $a ����
	 * @param unknown_type $b ����
	 * @param unknown_type $c ����
	 * @return array
	 */
	function getAllByUserIds($userIds, $a=false, $b=false, $c=false){
		$userIds = array_unique ( (array)$userIds );
		$result = $_tmpResult = $keys = $_tmpUserIds = array ();
		foreach ( $userIds as $userId ) {
			$keys [$this->_getAllMembersKey ( $userId )] = $userId;
		}
		if (($members = $this->_cacheService->get ( array_keys($keys) ))) {
			$_unique = $this->getUnique();
			foreach ($keys as $key=>$userId){
				$_key = $_unique . $key;
				if (isset($members[$_key]) && is_array($members[$_key])){
					$_tmpUserIds [] = $userId;
					$result[$userId] = $members[$_key];
				}
			}
		}
		$userIds = array_diff ( $userIds, $_tmpUserIds );
		if ($userIds) {
			$_tmpResult = $this->_getMembersAndMemberDataAndMemberInfoByUserIdsNoCache ( $userIds );
			foreach ($userIds as $userId){
				$this->_cacheService->set ( $this->_getAllMembersKey ( $userId ), isset($_tmpResult[$userId]) ? $_tmpResult[$userId] : array() );
			}
		}		
		return (array)$result + (array)$_tmpResult;		
	}
	
	/**
	 * ��ȡһ���û���Ϣ
	 * ��ѯmembers,memberData, memberInfo���ű�Ĳ����ֶΣ� ����global.php, read.phpҳ�����ض��ط�����
	 *
	 * @param array $userIds
	 * @param unknown_type $a ����
	 * @param unknown_type $b ����
	 * @param unknown_type $c ����
	 * @return array
	 */	
	function getAllByUserId($userId, $a=false, $b=false, $c=false){
		$userId = S::int($userId);
		if ($userId < 1) return false;
		$members = $this->getAllByUserIds($userId);
		return $members ? current($members) : array();
	}
	
	/**
	 * ��ȡһ���û���Ϣ
	 * ��ѯmembers,memberData, singleRight���ű�Ĳ����ֶΣ� ����global.phpҳ�����ض��ط�����
	 *
	 * @param array $userIds
	 * @return array
	 */		
	function getMembersAndMemberDataAndSingleRightByUserId($userId) {
		$userId = S::int ( $userId );
		if ($userId < 1) return false;
		$membersAndMemberData = $this->getAllByUserId($userId);
		if (!$membersAndMemberData) return array();
		$singleRight = $this->getSingleRightByUserId($userId);
		return  (array)$membersAndMemberData + ($singleRight ? (array)$singleRight : $this->_singleRightField);
	}	
		
	/**
	 * ����һ���û�id��ȡ�û���ǩ
	 * 
	 * @param int $uid �û�id
	 * @return array
	 */
	function getMemberTagsByUserid($userId) {
		$userId = S::int ( $userId );
		if ($userId < 1) {
			return false;
		}
		$key = $this->_getMemberTagsKey ( $userId );
		$result = $this->_cacheService->get ( $key );
		if ($result === false) {
			$memberTagsService = L::loadClass('memberTagsService', 'user');
			$result = $memberTagsService->getMemberTagsByUidFromDB($userId);
			$result = $result ? $result : array();
			$this->_cacheService->set ( $key,  $result);
		}
		return $result;
	}
}
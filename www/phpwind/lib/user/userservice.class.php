<?php
/**
 * �û��������ļ�
 * 
 * @package User
 */

!defined('P_W') && exit('Forbidden');

/**
 * �û��������
 * 
 * @package User
 */
class PW_UserService {
	
	/**
	 * ��ȡ�û���Ϣ
	 *
	 * @param int $userId �û�ID
	 * @param bool $withMainFields �Ƿ�ȡ�û���Ҫ��Ϣ
	 * @param bool $withMemberDataFields �Ƿ�ȡ�û�������Ϣ
	 * @param bool $withMemberInfoFields �Ƿ�ȡ�û������Ϣ
	 * @return array|null �û��������飬�Ҳ�������null
	 */
	function get($userId, $withMainFields = true, $withMemberDataFields = false, $withMemberInfoFields = false) {
		$userId = (int) $userId;
		if ($userId <= 0) return null;
		if (perf::checkMemcache()){
			$_cacheService = Perf::gatherCache('pw_members');
			return $_cacheService->getAllFieldByUserId($userId, $withMainFields, $withMemberDataFields, $withMemberInfoFields);			
		}
		$membersDb = $this->_getMembersDB();
		return $membersDb->getWithJoin($userId, $withMainFields, $withMemberDataFields, $withMemberInfoFields);
		/*
		$member = array();
		if ($withMainFields) {
			$membersDb = $this->_getMembersDB();
			$data = $membersDb->get($userId);
			if ($data) $member = array_merge($member, $data);
		}
		if ($withMemberDataFields) {
			$memberDataDb = $this->_getMemberDataDB();
			$data = $memberDataDb->get($userId);
			if ($data) $member = array_merge($member, $data);
		}
		if ($withMemberInfoFields) {
			$memberInfoDb = $this->_getMemberInfoDB();
			$data = $memberInfoDb->get($userId);
			if ($data) $member = array_merge($member, $data);
		}
		return $member ? $member : null;
		*/
	}
	
	/**
	 * �����û�id������ȡ�û���Ϣ
	 * @param array $userIds
	 * @return array
	 */
	function getByUserIds($userIds) {
		if (!is_array($userIds) || !count($userIds)) return array();
		if (perf::checkMemcache()){
			$_cacheService = Perf::gatherCache('pw_members');
			return $_cacheService->getMembersByUserIds($userIds);
		}
		$membersDb = $this->_getMembersDB();
		return $membersDb->getUsersByUserIds($userIds);
	}
	
	/**
	 * �����û�id������ȡ�û���Ϣ������memberdata����Ϣ
	 * @param array $userIds
	 * @return array
	 */
	function getUsersWithMemberDataByUserIds($userIds) {
		if (!is_array($userIds) || !count($userIds)) return array();
		if (perf::checkMemcache()){
			$_cacheService = Perf::gatherCache('pw_members');
			return $_cacheService->getAllFieldByUserIds($userIds, true, true);
		}	
		$membersDb = $this->_getMembersDB();
		return $membersDb->getUserInfosByUserIds($userIds);
	}
	
	/**
	 * �����û�����ȡ�û���Ϣ
	 *
	 * @param string $userName
	 * @param bool $withMainFields �Ƿ�ȡ�û���Ҫ��Ϣ
	 * @param bool $withMemberDataFields �Ƿ�ȡ�û�������Ϣ
	 * @param bool $withMemberInfoFields �Ƿ�ȡ�û������Ϣ
	 * @return array|null �û��������飬�Ҳ�������null
	 */
	function getByUserName($userName, $withMainFields = true, $withMemberDataFields = false, $withMemberInfoFields = false) {
		$userName = trim($userName);
		if (!$userName) return null;
		
		$member = array();
		$membersDb = $this->_getMembersDB();
		$data = $membersDb->getUserByUserName($userName);
		if (!$data || !$data['uid']) return null;
		
		$userId = (int) $data['uid'];
		$withMainFields && $member = array_merge($member, $data);
		if ($withMemberDataFields) {
			$memberDataDb = $this->_getMemberDataDB();
			$data = $memberDataDb->get($userId);
			if ($data) $member = array_merge($member, $data);
		}
		if ($withMemberInfoFields) {
			$memberInfoDb = $this->_getMemberInfoDB();
			$data = $memberInfoDb->get($userId);
			if ($data) $member = array_merge($member, $data);
		}
		return $member ? $member : null;
	}
	
	/**
	 * �����û���������ȡ�û���Ϣ
	 * 
	 * @param array $userNames
	 * @return array
	 */
	function getByUserNames($userNames) {
		if (!is_array($userNames) || !count($userNames)) return array();
		
		$membersDb = $this->_getMembersDB();
		return $membersDb->getUsersByUserNames($userNames);
	}
	
	/**
	 * �����û�����ȡ�û�id
	 * 
	 * @param string $userName �û���
	 * @return int
	 */
	function getUserIdByUserName($userName) {
		if (!$data = $this->getByUserName($userName)) return 0;
		return (int) $data['uid'];
	}
	
	/**
	 * �����û�email��ȡ�û�id
	 * 
	 * @param string $email ����
	 * @return int
	 */
	function getUserIdByEmail($email) {
		if (!$data = $this->getByEmail($email)) return 0;
		return (int) $data['uid'];
	}
	
	/**
	 * �����û�id��ȡ�û���
	 * 
	 * @param int $userId �û�id
	 * @return string|null
	 */
	function getUserNameByUserId($userId) {
		$userId = S::int($userId);
		if ($userId < 1) return false;
		if (perf::checkMemcache()){
			$_cacheService = Perf::gatherCache('pw_members');
			return $_cacheService->getUserNameByUserId($userId);
		}				
		if (!$data = $this->get($userId)) return null;
		return $data['username'];
	}
	
	/**
	 * �����û�id������ȡ�û���
	 * 
	 * @param array $userIds �û�id����
	 * @return array ��uidΪkey���û���Ϊֵ������
	 */
	function getUserNamesByUserIds($userIds) {
		if (!is_array($userIds) || !count($userIds)) return array();
		if (perf::checkMemcache()){
			$_cacheService = Perf::gatherCache('pw_members');
			return $_cacheService->getUserNameByUserIds($userIds);
		}
		$userNames = array();
		$members = $this->getByUserIds($userIds);
		foreach ($members as $member) {
			$member['uid'] && $userNames[$member['uid']] = $member['username'];
		}
		return $userNames;
	}
	
	/**
	 * ����email��ȡ�û���Ϣ
	 * 
	 * @param string $email
	 * @return array|null �û��������飬�Ҳ�������null
	 */
	function getByEmail($email) {
		$email = trim($email);
		if ('' == $email) return null;
		
		$membersDb = $this->_getMembersDB();
		$users = $membersDb->getUserByUserEmails(array($email));
		return !empty($users) ? current($users) : null;
	}
	
	/**
	 * ����email������ȡ�û���Ϣ
	 * @param array $emails
	 * @return array
	 */
	function getByEmails($emails) {
		if (!is_array($emails) || !count($emails)) return array();
		
		$membersDb = $this->_getMembersDB();
		return $membersDb->getUserByUserEmails($emails);
	}

	
	/**
	 * ����groupid��ȡ����û���Ϣ
	 * @param array $groupIds
	 * @return array
	 */
	function getByGroupId($groupId) {
		$membersDb = $this->_getMembersDB();
		return $membersDb->getUsersByGroupId($groupId);
	}
	
	
	/**
	 * ����groupid������ȡ����û���Ϣ
	 * @param array $groupIds
	 * @return array
	 */
	function getByGroupIds($groupIds) {
		if (!is_array($groupIds) || !count($groupIds)) return array();
		
		$membersDb = $this->_getMembersDB();
		return $membersDb->getUsersByGroupIds($groupIds);
	}
	
	/**
	 * ���������û�
	 * 
	 * @return array|null �����û���Ϣ���Ҳ�������null
	 */
	function getLatestNewUser() {
		$membersDb = $this->_getMembersDB();
		$users = $membersDb->findUsersOrderByUserId();
		return count($users) ? current($users) : null;
	}
	
	/**
	 * �������µļ����û�
	 * 
	 * @return array
	 */
	function findLatestNewUsers($number = 10) {
		$number = intval($number);
		if ($number <= 0) return array();
		
		$membersDb = $this->_getMembersDB();
		return $membersDb->findUsersOrderByUserId($number);
	}
	
	/**
	 * �������µļ���δ�����Ե��û�
	 * 
	 * @return array
	 */
	function findNotBannedNewUsers($number = 10) {
		$number = intval($number);
		if ($number <= 0) return array();
		
		$membersDb = $this->_getMembersDB();
		return $membersDb->findNotBannedUsersOrderByUserId($number);
	}
	
	
	/**
	 * ���Membersȫ�����ݵĸ���
	 */
	function count() {
		$membersDb = $this->_getMembersDB();
		return $membersDb->_count();
	}
	
	/**
	 * ���һ���û�
	 * 
	 * @param array $mainFields �û���Ҫ��Ϣ����
	 * @param array $memberDataFields �û�������Ϣ����
	 * @param array $memberInfoFields �û������Ϣ����
	 * @return int �����û�id��ʧ�ܷ���0
	 */
	function add($mainFields, $memberDataFields = array(), $memberInfoFields = array()) {
		if (!is_array($mainFields) || !count($mainFields)) return 0;
		if (!isset($mainFields['username']) || !isset($mainFields['password'])) return 0;
		if ('' == $mainFields['username'] || '' == $mainFields['password']) return 0;
		
		$membersDb = $this->_getMembersDB();
		$userId = $membersDb->insert($mainFields);
		if (!$userId) return 0;
		
		$memberDataFields['uid'] = $userId;
		$memberDataDb = $this->_getMemberDataDB();
		$memberDataDb->insert($memberDataFields);
		
		$this->_replaceMemberInfo($userId, $memberInfoFields, false);
		
		return $userId;
	}
	
	/**
	 * �����û���Ϣ
	 * 
	 * @param int $userId
	 * @param array $mainFields �û���Ҫ��Ϣ����
	 * @param array $memberDataFields �û�������Ϣ����
	 * @param array $memberInfoFields �û������Ϣ����
	 * @return bool �Ƿ����
	 */
	function update($userId, $mainFields = array(), $memberDataFields = array(), $memberInfoFields = array()) {
		$userId = intval($userId);
		if ($userId <= 0) return false;
		
		$updates = 0;
		if (is_array($mainFields) && count($mainFields)) {
			$membersDb = $this->_getMembersDB();
			$updates += $membersDb->update($mainFields, $userId); //TODO refactor update
		}
		if (is_array($memberDataFields) && count($memberDataFields)) {
			$memberDataDb = $this->_getMemberDataDB();
			$updates += $memberDataDb->update($memberDataFields, $userId);
		}
		$updates += $this->_replaceMemberInfo($userId, $memberInfoFields);
		
		return (bool) $updates;
	}
	
	/**
	 * ���������û���Ϣ
	 * 
	 * @param array $userIds
	 * @param array $mainFields �û���Ҫ��Ϣ����
	 * @param array $memberDataFields �û�������Ϣ����
	 * @param array $memberInfoFields �û������Ϣ����
	 * @return int ���¸���
	 */
	function updates($userIds, $mainFields = array(), $memberDataFields = array(), $memberInfoFields = array()) {
		if (!is_array($userIds) || !count($userIds)) return 0;
		
		$updates = 0;
		if (is_array($mainFields) && count($mainFields)) {
			$membersDb = $this->_getMembersDB();
			$updates += $membersDb->updates($mainFields, $userIds); //TODO refactor update
		}
		if (is_array($memberDataFields) && count($memberDataFields)) {
			$memberDataDb = $this->_getMemberDataDB();
			$updates += $memberDataDb->updates($memberDataFields, $userIds);
		}
		if (is_array($memberInfoFields) && count($memberInfoFields)) {
			foreach ($userIds as $userId) {
				$updates += $this->_replaceMemberInfo($userId, $memberInfoFields);
			}
		}
		
		return $updates;
	}
	
	function clearUserMessage($uid){
		$uid = intval($uid);
		if ($uid < 1) return false;
		$this->update($uid, array('newpm'=>0), array('newfans'=>0,'newreferto'=>0,'newnotice'=>0,'newrequest'=>0));
	}
	
	/**
	 * ���������û���Ϣ
	 * 
	 * @param int $userId
	 * @param array $mainFields �û���Ҫ��Ϣ����
	 * @param array $memberDataFields �û�������Ϣ����
	 * @param array $memberInfoFields �û������Ϣ����
	 * @return bool
	 */
	function updateByIncrement($userId, $mainFields = array(), $memberDataFields = array(), $memberInfoFields = array()) {
		$userId = intval($userId);
		if ($userId <= 0) return false;
		
		$updates = 0;
		if (is_array($mainFields) && count($mainFields)) {
			$membersDb = $this->_getMembersDB();
			$updates += $membersDb->increase($userId, $mainFields);
		}
		if (is_array($memberDataFields) && count($memberDataFields)) {
			$memberDataDb = $this->_getMemberDataDB();
			$updates += $memberDataDb->increase($userId, $memberDataFields);
		}
		if (is_array($memberInfoFields) && count($memberInfoFields)) {
			$memberInfoDb = $this->_getMemberInfoDB();
			$updates += $memberInfoDb->increase($userId, $memberInfoFields);
		}
		return (bool) $updates;
	}
	
	/**
	 * ���������������û���Ϣ
	 * 
	 * @param array $userIds
	 * @param array $mainFields �û���Ҫ��Ϣ����
	 * @param array $memberDataFields �û�������Ϣ����
	 * @param array $memberInfoFields �û������Ϣ����
	 * @return int ���¸���
	 */
	function updatesByIncrement($userIds, $mainFields = array(), $memberDataFields = array(), $memberInfoFields = array()) {
		if (!is_array($userIds) || !count($userIds)) return 0;
		
		$updates = 0;
		foreach ($userIds as $userId) {
			$updates += (int) $this->updateByIncrement($userId, $mainFields, $memberDataFields, $memberInfoFields);
		}
		return $updates;
	}
	/**
	 * �����������
	 * @param $type	����ֶ�
	 */
	function updateOverflow($type) {
		$memberDataDb = $this->_getMemberDataDB();
		return $memberDataDb->updateOverflow($type);
	}

	/**
	 * �����û�ĳ�����͵�״̬
	 * 
	 * @param int $userId �û�id
	 * @param int $type �û�״̬���� ������PW_USERSTATUS_*
	 * @param bool|int $status ״ֵ̬��0-false, 1-true, other
	 * @return bool
	 */
	function setUserStatus($userId, $type, $status = true) {
		list($userId, $type) = array(intval($userId), intval($type));
		if ($userId <= 0 || $type <= 0) return false;

		$num = $this->_getUserStatusNumberWithUserStatusType($type);
		$membersDb = $this->_getMembersDB();
		return (bool)$membersDb->setUserStatus($userId, $type, $status, $num);
	}

	
	/**
	 * ��ȡ�û�ĳ�����͵�״̬
	 * 
	 * @param int $userId �û�id
	 * @param int $type �û�״̬���� ������PW_USERSTATUS_*
	 * @return int
	 */
	function getUserStatus($userId, $type) {
		list($userId, $type) = array(intval($userId), intval($type));
		if ($userId <= 0 || $type <= 0) return false;
		if (!$user = $this->get($userId)) return false;
		$num = $this->_getUserStatusNumberWithUserStatusType($type);
		$user['userstatus'] >>= --$type;
		return bindec(substr(sprintf('%0'.$num.'b', $user['userstatus']), -$num));
	}
	
	/**
	 * ɾ���û�
	 * 
	 * @param int $userId
	 * @return bool
	 */
	function delete($userId) {
		$membersDb = $this->_getMembersDB();
		$memberDataDb = $this->_getMemberDataDB();
		$memberInfoDb = $this->_getMemberInfoDB();
		$banUserDb = $this->_getBanUserDB();
		
		$memberDataDb->delete($userId);
		$memberInfoDb->delete($userId);
		$banUserDb->deleteByUserId($userId);
		return (bool) $membersDb->delete($userId);
	}
	
	/**
	 * ɾ������û�
	 * 
	 * @param array $userIds
	 * @return int ɾ������
	 */
	function deletes($userIds) {
		if (!is_array($userIds) || !count($userIds)) return 0;
		
		$deletes = 0;
		foreach ($userIds as $userId) {
			$deletes += $this->delete($userId);
		}
		return $deletes;
	}
	
	/**
	 * �����û�id�ж��û��Ƿ����
	 * 
	 * @param int $userId
	 * @return boolean
	 */
	function isExist($userId) {
		if (!$data = $this->get($userId)) return false;
		return (bool)$data['uid'];
	}
	
	/**
	 * �����û����ж��û��Ƿ����
	 * 
	 * @param string $userName
	 * @return boolean
	 */
	function isExistByUserName($userName) {
		if (!$data = $this->getByUserName($userName)) return false;
		return (bool)$data['uid'];
	}
	
	function findOnlineUsers($onlineTimestamp) { //TODO move to OnlineUserService
		$onlineTimestamp = intval($onlineTimestamp);
		
		$memberDataDb = $this->_getMemberDataDB();
		return $memberDataDb->getOnlineUsers($onlineTimestamp);
	}
	
	/**
	 * �����뼤���û�
	 * 
	 * @param int $userId
	 * @param string $activateCode ������
	 * @param string $siteHash վ��hash
	 * @param string $toemail ���������ַ
	 * @return bool �Ƿ񼤻�ɹ�
	 */
	function activateUser($userId, $activateCode, $siteHash,$toemail) {
		$userId = (int) $userId;
		$activateCode = trim($activateCode);
		if ($userId <= 0 || '' == $activateCode) return false;
		
		$membersDb = $this->_getMembersDB();
		$user = $membersDb->get($userId);
		if($user['email'] != $toemail) return false;
		if (!$user) return false;
		
		$comparedActivateCode = $this->_generateUserActivateCode($user, $siteHash);
		if ($comparedActivateCode == $activateCode) {
			$this->update($userId, array('yz' => 1));
			return true;
		}
		return false;
	}
	
	/**
	 * ��ȡδ�����û���Ϣ
	 * 
	 * @param int $userId �û�id
	 * @param string $email �û�email����������������һ������
	 * @param string $siteHash վ��hash
	 * @return array|null �û��������飨��activateCode�ֶΣ�Ϊ���û��ļ����룩���Ҳ�������null
	 */
	function getUnactivatedUser($userId, $email, $siteHash) {
		$user = null;
		if ($userId) $user = $this->get($userId);
		if (!$user) $user = $this->getByEmail($email);
		
		if (!$user) return null;
		if ($user['yz'] <= 1) return null;
		
		$user['activateCode'] = $this->_generateUserActivateCode($user, $siteHash);
		return $user;
	}

	/**
	 * ����ĳ��״̬������ռbitλ����
	 * 
	 * @param int $type �û�״̬���� ������PW_USERSTATUS_*
	 * @return int
	 */
	function _getUserStatusNumberWithUserStatusType($type) {
		switch ($type) {
			case PW_USERSTATUS_CFGFRIEND : $num = 2; break;
			default: $num = 1;
		}
		return $num;
	}
	
	function _generateUserActivateCode($userData, $siteHash) {
		return md5($userData['yz'] . substr(md5($siteHash), 0, 5) . substr(md5($userData['username']), 0, 5));
	}
	
	function _replaceMemberInfo($userId, $fieldsData, $checkExist = true) {
		if (!is_array($fieldsData) || !count($fieldsData)) return 0;
		
		$memberInfoDb = $this->_getMemberInfoDB();
		
		if ($checkExist && $memberInfoDb->get($userId)) {
			return $memberInfoDb->update($fieldsData, $userId);
		} else {
			$fieldsData['uid'] = $userId;
			return $memberInfoDb->insert($fieldsData);
		}
	}
	
	/**
	 * ��װ�����û��־ӵء����硢������������������Ϣ
	 * 
	 * @param int $userId �û�id
	 * @return array
	 */
	function getOnLineUsers() {
		global $winduid;
		$onlineUsers = GetOnlineUser();
		if (!s::isArray($onlineUsers)) return array();
		$userIds = array();
		foreach ($onlineUsers as $key => $v) {
			if ($key == $winduid) continue;
			$userIds[] = $key;
		}
		return $userIds;
	}
	
	/**
	 * ��װ�û�uids
	 * 
	 * @param array $fieldsData �û���Ϣ
	 * @return array
	 */
	function buildUids($fieldsData) {
		$uids = array();
		foreach ((array)$fieldsData as $v) {
			$uids[] = $v['uid'];
		}
		return array_diff($uids,$winduid);
	}
	
	/**
	 * ��װ�û���Ϣuid��username��face������ͼ��
	 * 
	 * @param array $uids
	 * @return array
	 */
	function buildUserInfo($uids) {
		if (!s::isArray($uids)) return array();
		require_once(R_P.'require/showimg.php');
		$userInfo = array();
		foreach ((array)$this->getUsersWithMemberDataByUserIds($uids) as $data) {
			$user['uid'] = $data['uid'];
			$user['username'] = $data['username'];
			$user['thisvisit'] = $data['thisvisit'];
			list($user['face']) = showfacedesign($data['icon'], '1', 's');
			$userInfo[] = $user;
		}
		return $userInfo;
	}
	
	/**
	 * ������ʶ����
	 * 
	 * @param int $userId �û�id
	 * @return array
	 */
	function getMayKnownUserIds($fieldsData,$num = 12) {
		$onlineUserIds = $this->getOnLineUsers();
		if (!s::isArray($onlineUserIds)) return array();
		if (count($onlineUserIds) <= $num) return $onlineUserIds;
		
		$tmpApartmentUsers = $this->getUsersByApartmentAndUserIds($fieldsData['apartment'],$onlineUserIds,$num);
		$countApartmentUser = count($tmpApartmentUsers);
		$apartmentUsers = $this->buildUids($tmpApartmentUsers);
		if ($countApartmentUser >= $num) return $apartmentUsers;
		$homeUids = array_diff($onlineUserIds,$apartmentUsers);
		$homeNum = $num - $countApartmentUser;

		$tmpHomeUsers = $this->getUsersByHomeAndUserIds($fieldsData['home'],$homeUids,$homeNum);
		$countHomeUser = count($tmpHomeUsers);
		$homeUsers = $this->buildUids($tmpHomeUsers);
		if ($countHomeUser >= $homeNum) return array_merge($apartmentUsers,$homeUsers);
		$companyUids = array_diff($homeUids,$homeUsers);
		$companyNum = $homeNum - $countPlaceUser;
			
		$tmpCompanyUsers = $this->getUsersByCompanyidAndUserIds($fieldsData['companyid'],$companyUids,$companyNum);
		$countCompanyUser = count($tmpCompanyUsers);
		$companyUsers = $this->buildUids($tmpCompanyUsers);
		if ($countCompanyUser >= $companyNum) return array_merge($apartmentUsers,$homeUsers,$companyUsers);
		$educationUids = array_diff($companyUids,$companyUsers);
		$educationNum = $companyNum - $countCompanyUser;

		$tmpEducationUsers = $this->getUsersBySchoolidsAndUserIds($fieldsData['schoolid'],$educationUids,$educationNum);
		$countEducationUser = count($tmpEducationUsers);
		$educationUsers = $this->buildUids($tmpEducationUsers);
		if ($countEducationUser >= $educationNum) return array_merge($apartmentUsers,$homeUsers,$companyUsers,$educationUsers);
		$endUids = array_diff($educationUids,$educationUsers);
		$endNum = $educationNum - $countEducationUser;
		
		return array_merge($apartmentUsers,$homeUsers,$companyUsers,$educationUsers,array_slice($endUids,0,$endNum));
	}
	
	/**
	 * �������ڵ�apartment��userIdsͳ���û�
	 * 
	 * @param int $apartment ���ڵ�
	 * @param array $userIds �û�ids
	 * @return int
	 */
	function countUsersByApartmentAndUserIds($apartment,$userIds) {
		$apartment = intval($apartment);
		if ($apartment < 1 || !s::isArray($userIds)) return 0;
		$membersDb = $this->_getMembersDB();
		return $membersDb->countUsersByApartmentAndUserIds($apartment,$userIds);
	}
	
	/**
	 * �������ڵ�apartment��userIds��ȡ�û�
	 * 
	 * @param int $apartment ���ڵ�
	 * @param array $userIds �û�ids
	 * @return array
	 */
	function getUsersByApartmentAndUserIds($apartment,$userIds,$num) {
		$apartment = intval($apartment);
		if ($apartment < 1 || !s::isArray($userIds)) return array();
		$membersDb = $this->_getMembersDB();
		if ($this->countUsersByApartmentAndUserIds($apartment,$userIds) < 1) return array();
		return $membersDb->getUsersByApartmentAndUserIds($apartment,$userIds,$num);
	}
	
	/**
	 * ���ݼ���home��userIdsͳ���û�
	 * 
	 * @param int $home ���ڵ�
	 * @param array $userIds �û�ids
	 * @return int
	 */
	function countUsersByHomeAndUserIds($home,$userIds) {
		$home = intval($home);
		if ($home < 1 || !s::isArray($userIds)) return 0;
		$membersDb = $this->_getMembersDB();
		return $membersDb->countUsersByHomeAndUserIds($home,$userIds);
	}
	
	/**
	 * ���ݼ���home��userIds��ȡ�û�
	 * 
	 * @param int $home ����
	 * @param array $userIds �û�ids
	 * @return array
	 */
	function getUsersByHomeAndUserIds($home,$userIds,$num) {
		$home = intval($home);
		if ($home < 1 || !s::isArray($userIds)) return array();
		$membersDb = $this->_getMembersDB();
		if ($this->countUsersByHomeAndUserIds($home,$userIds) < 1) return array();
		return $membersDb->getUsersByHomeAndUserIds($home,$userIds,$num);
	}
	
	/**
	 * ���ݹ�������companyids��userIdsͳ���û�
	 * 
	 * @param array $companyids
	 * @param array $userIds �û�ids
	 * @return array
	 */
	function countUsersByCompanyidAndUserIds($companyids,$userIds) {
		if (!s::isArray($companyids) || !s::isArray($userIds)) return 0;
		$membersDb = $this->_getMembersDB();
		return $membersDb->countUsersByCompanyidAndUserIds($companyids,$userIds);
	}
	
	/**
	 * ���ݹ�������companyids��userIds��ȡ�û�
	 * 
	 * @param array $companyids
	 * @param array $userIds �û�ids
	 * @return array
	 */
	function getUsersByCompanyidAndUserIds($companyids,$userIds,$num) {
		if (!s::isArray($companyids) || !s::isArray($userIds)) return array();
		$membersDb = $this->_getMembersDB();
		if ($this->countUsersByCompanyidAndUserIds($companyids,$userIds) < 1) return array();
		return $membersDb->getUsersByCompanyidAndUserIds($companyids,$userIds,$num);
	}
	
	/**
	 * ���ݽ�������schoolids��userIdsͳ���û�
	 * 
	 * @param array $schoolids
	 * @param array $userIds �û�ids
	 * @return array
	 */
	function countUsersBySchoolidsAndUserIds($schoolids,$userIds) {
		if (!s::isArray($schoolids) || !s::isArray($userIds)) return 0;
		$membersDb = $this->_getMembersDB();
		return $membersDb->countUsersBySchoolidsAndUserIds($schoolids,$userIds);
	}
	
	/**
	 * ���ݽ�������schoolids��userIds��ȡ�û�
	 * 
	 * @param array $companyids
	 * @param array $userIds �û�ids
	 * @return array
	 */
	function getUsersBySchoolidsAndUserIds($schoolids,$userIds,$num) {
		if (!s::isArray($schoolids) || !s::isArray($userIds)) return array();
		$membersDb = $this->_getMembersDB();
		if ($this->countUsersBySchoolidsAndUserIds($schoolids,$userIds) < 1) return array();
		return $membersDb->getUsersByCompanyidAndUserIds($schoolids,$userIds,$num);
	}
	
	/**
	 * ��ȡ�����û������������ڵء����硢������������Ϣ
	 * 
	 * @param int $userId �û�id
	 * @return array
	 */
	function getUserInfoByUserId($userId) {
		$userId = intval($userId);
		if ($userId < 1) return array();
		$membersDb = $this->_getMembersDB();
		return $membersDb->getUserInfoByUserId($userId);
	}
	
	/**
	 * �û���Ȩ��
	 * 
	 * @param int $groupId �û���
	 * @return array
	 */
	function getRightByGroupId($groupId){
		static $groupRight;
		if (file_exists(D_P . "data/groupdb/group_$groupId.php")) {
			extract(pwCache::getData(S::escapePath(D_P . "data/groupdb/group_$groupId.php"),false));
			$groupRight = $_G;
		}
		return $groupRight;
	}

	function getUserInfoWithFace($uids) {
		if(!S::isArray($uids)) return array();
		require_once (R_P . 'require/showimg.php');
		$usersInfo = array();
		$users = $this->getByUserIds($uids); //'m.uid','m.username','m.icon','m.groupid'

		foreach ($users as $key => $value) {
			list($value['icon']) = showfacedesign($value['icon'], 1, 's');
			$usersInfo[$value['uid']] = $value;
		}
		return $usersInfo;
	}
	
	/**
	 * get PW_MembersDB
	 * 
	 * @access protected
	 * @return PW_MembersDB
	 */
	function _getMembersDB() {
		return L::loadDB('Members', 'user');
	}
	
	/**
	 * get PW_MemberdataDB
	 * 
	 * @return PW_MemberdataDB
	 */
	function _getMemberDataDB() {
		return L::loadDB('MemberData', 'user');
	}
	
	/**
	 * get PW_MemberinfoDB
	 * 
	 * @return PW_MemberinfoDB
	 */
	function _getMemberInfoDB() {
		return L::loadDB('MemberInfo', 'user');
	}
	
	/**
	 * @return PW_BanUserDB
	 */
	function _getBanUserDB() {
		return L::loadDB('BanUser', 'user');
	}
}


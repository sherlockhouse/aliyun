<?php
!function_exists('readover') && exit('Forbidden');
/**
 * ��Ϣ���Ļ�������
 * ����ȫ�ַ��� ��������/���ݷ��ʽӿ�/ͨ�ù�������
 * @copyright phpwind v8.0
 * @author liuhui 2010-4-6
 */
class MS_Base {
	var $_sms = 'sms'; //վ����
	var $_sms_message = 'sms_message'; //����Ϣ
	var $_sms_rate = 'sms_rate'; //����
	var $_sms_comment = 'sms_comment'; //����
	var $_sms_guestbook = 'sms_guestbook'; //����
	var $_sms_share = 'sms_share'; //����
	var $_sms_reply = 'sms_reply'; //���ӻظ�
	var $_notice = 'notice'; //֪ͨ
	var $_notice_system = 'notice_system'; //ϵͳ֪ͨ
	var $_notice_postcate = 'notice_postcate'; //�Ź�֪ͨ
	var $_notice_active = 'notice_active'; //�֪ͨ
	var $_notice_apps = 'notice_apps'; //Ӧ��֪ͨ
	var $_notice_comment = 'notice_comment'; //����֪ͨ
	var $_notice_guestbook = 'notice_guestbook'; //����֪ͨ 
	var $_request = 'request'; //����
	var $_request_friend = 'request_friend'; //��������
	var $_request_group = 'request_group'; //Ⱥ������
	var $_request_active = 'request_active'; //�����
	var $_request_apps = 'request_apps'; //Ӧ������
	var $_groupsms = 'groupsms';
	var $_groupsms_colony = 'groupsms_colony'; //Ⱥ��
	var $_groupsms_normal = 'groupsms_normal'; //�����Ķ��˶Ի�
	var $_groupsms_shield = 'groupsms_shield'; //���εĶ��˶Ի�
	var $_chat = 'chat';
	var $_history = 'history';
	
	var $_s_have_read = 0; //�Ѷ�
	var $_s_not_read = 1; //ĩ��
	var $_s_new_reply = 2; //�»ظ�
	var $_s_self = 1; //�ҷ���
	var $_s_other = 0; //�ҽ���
	

	var $_s_overlook = 4; //��������
	var $_s_agree = 5; //ͬ������
	

	var $_timestamp = null;
	var $_receiver = 20;
	var $_attachmentPath = null;
	var $_userId = null;
	var $_userName = null;
	var $_groupId = null;
	var $_userGroup = null;
	var $_nodeTime = 0; //ÿ�����ʱ�� 12:00
	var $_super = 0; //����Ȩ�޿��� �Ƿ���ȫ������
	
	var $_c_relation_reply = 2; //��ϵ����,�ظ�����

	var $_c_sms_num = 'sms_num'; //վ������Ϣ��
	var $_c_notice_num = 'notice_num'; //֪ͨ��Ϣ��
	var $_c_request_num = 'request_num'; //������Ϣ��
	var $_c_groupsms_num = 'groupsms_num'; //Ⱥ����Ϣ��
	

	var $_s_notice_system = 'notice_website';
	
	function MS_Base() {
		global $timestamp, $db_attachname, $winduid, $windid, $_G, $tdtime, $db_windpost, $groupid, $winddb;
		$this->_userId = &$winduid;
		$this->_userName = &$windid;
		$this->_groupId = ($groupid > 0) ? $groupid : $winddb['memberid'];
		$this->_userGroup = &$_G;
		$this->_nodeTime = &$tdtime;
		$this->_windpost = &$db_windpost;
		$this->_timestamp = ($timestamp) ? $timestamp : time();
		$this->_attachmentPath = ($db_attachname) ? $db_attachname : 'attachment';
	
	}
	/**
	 * ȫ�ּ���û��Ƿ�����Ϣ����Ȩ��
	 * @return unknown_type
	 */
	function _checkUserLevle($category, $number = 1, $typeId = null) {
		//������Ϣ�Ƿ���
		if ($this->_super) {
			return true;
		}
		if (!in_array($category, array($this->_sms,$this->_groupsms))) {
			return true;
		}
		$typeIds = $this->_getSpecialMap(array($this->_sms_message,$this->_groupsms_normal));
		if ($typeId && !in_array($typeId, $typeIds)) {
			return true;
		}
		if (!isset($this->_userGroup['allowmessege']) || !$this->_userGroup['allowmessege']) {
			return false;
		}
		if ($number > 1 && (!isset($this->_userGroup['multiopen']) || !$this->_userGroup['multiopen'])) {
			return false;
		}
		// ÿ���������Ϣ��Ŀ
		$relationsDao = $this->getRelationsDao();
		//$this->_userGroup['maxsendmsg'] = ($this->_userGroup['maxsendmsg']) ? $this->_userGroup['maxsendmsg'] : 20;
		if (isset($this->_userGroup['maxsendmsg']) && $this->_userGroup['maxsendmsg'] > 0) {
			if ($this->_userGroup['maxsendmsg'] - 1 < ($total = $relationsDao->countSelfByUserId($this->_userId, $this->_nodeTime))) {
				return false;
			}
		}
		// �û������Ϣ��
		if (isset($this->_userGroup['maxmsg']) && $this->_userGroup['maxmsg'] > 0) {
			$userInfo = $this->_countUserNumbers(array($this->_userId));
			if ($userInfo && $this->_userGroup['maxmsg'] - 1 < $userInfo[$this->_userId]) {
				return false;
			}
		}
		return true;
	}
	/**
	 * ȫ�ּ����Ϣ����������Ϣ������Ϣ
	 * @return unknown_type
	 */
	function _checkReceiver($usernames, $category, $typeId) {
		if ("" == $usernames || "" == $category) {
			return array(false,false,false);
		}
		$usernames = is_array($usernames) ? $usernames : array($usernames);
		$usernames = array_unique($usernames);
		$categoryId = intval($this->getMap($category));
		$typeId = intval($typeId);
		if (0 > $categoryId || 1 > $typeId) {
			return array(false,false,false);
		}
		return array($usernames,$categoryId,$typeId);
	}
	/**
	 * ����������Ϣ�ӿڷ���
	 * @param int $userId
	 * @param array $userIds
	 * @param int $categoryId
	 * @param int $typeId
	 * @param array $messageInfo
	 * @param bool $both �Ƿ�˫�������Ϣ
	 * @return messageId ���͵���Ϣ��ID
	 */
	function _doSend($userId, $userIds, $categoryId, $typeId, $messageInfo, $both = true) {
		$messageInfo['expand'] = serialize(array('categoryid' => $categoryId,'typeid' => $typeId));
		if (!($messageId = $this->_addMessage($messageInfo))) {
			return false;
		}
		($both && $userId > 0 && !in_array($userId, $userIds)) && array_push($userIds, $userId);
		$relations = array();
		$userIds = array_unique($userIds);
		foreach ($userIds as $otherId) {
			$relation = array();
			$relation['uid'] = $otherId;
			$relation['mid'] = $messageId;
			$relation['categoryid'] = $categoryId;
			$relation['typeid'] = $typeId;
			$relation['status'] = ($otherId == $userId) ? $this->_s_have_read : $this->_s_not_read;
			$relation['isown'] = ($otherId == $userId) ? $this->_s_self : $this->_s_other;
			$relation['created_time'] = $relation['modified_time'] = $this->_timestamp;
			$relations[] = $relation;
		}
		$relationsDao = $this->getRelationsDao();
		if (!$relationsDao->addRelations($relations)) {
			return false;
		}
		return $messageId;
	}
	/**
	 * ˽��������Ϣ��ӿڷ���
	 * @param $messageInfo
	 * @return unknown_type
	 */
	function _addMessage($messageInfo) {
		if (false == ($messageInfo = $this->_checkInfo($messageInfo))) {
			return false;
		}
		$messagesDao = $this->getMessagesDao();
		if (!($messageId = $messagesDao->insert($messageInfo))) {
			return false;
		}
		return $messageId;
	}
	/**
	 * ����ȫ�ֻظ���Ϣ����
	 * @param int $userId         �ظ��û�UID
	 * @param int $parentId       ����ϢMID
	 * @param array $messageInfo  ��Ϣ����������
	 * @return array ���سɹ�����Ϣ
	 */
	function _reply($userId, $relationId, $parentId, $messageInfo) {
		$messagesDao = $this->getMessagesDao();
		if (!($message = $messagesDao->get($parentId))) {
			return false;
		}
		$relationsDao = $this->getRelationsDao();
		#if (!($relation = $relationsDao->getRelation($userId, $relationId)) || $relation['mid'] != $parentId) {
		#	return false;
		#}
		if (!($relation = $relationsDao->getRelation($userId, $relationId))) {
			return false;
		}
		if (!($result = $this->_doReply($userId, $parentId, $messageInfo))) {
			return false;
		}
		if($this->getMapByTypeId($relation['typeid']) == $this->_sms ){
			$messageInfo['title'] = 'RE:'.$message['title'];
			$actor = (isset($message['expand']) && ($expand = unserialize($message['expand'])) && (isset($expand['actor']))) ? $expand['actor'] : array();
			$this->_addReplyRelations($userId,$actor,$parentId,$relation['categoryid'],$relation['typeid'], $messageInfo);
			$fieldData = array();
		}else{
			$messagesDao->update(array('modified_time' => $this->_timestamp,'content' => $messageInfo['content']), $parentId);
			$fieldData = array('status' => $this->_s_new_reply,'modified_time' => $this->_timestamp);
		}
		$expand = ($message['expand']) ? unserialize($message['expand']) : array();
		if ($relation['categoryid'] == $this->getMap($this->_history)) {
			$expand = ($message['expand']) ? unserialize($message['expand']) : array();
			$expand && $fieldData['categoryid'] = $expand['categoryid'];
		}
		$categoryId = ($fieldData['categoryid']) ? $fieldData['categoryid'] : $relation['categoryid'];
		$fieldData && $relationsDao->updateRelationsByMessageId($fieldData, $parentId);
		$this->_updateStatisticsByCategoryId($categoryId, $message, $userId);
		return $result;
	}
	/**
	 * �����ظ���ϵ
	 */
	function _addReplyRelations($userId, $actor, $parentId, $categoryId, $typeId, $messageInfo){
		if(!$actor && is_array($actor) && !$messageInfo && !is_array($messageInfo)){
			return false;
		}
		$userIds = array();
		foreach($actor as $tmpUserId){
			($userId != $tmpUserId) && $userIds[] = $tmpUserId;
		}
		$userService = $this->_getUserService();
		if(!($toUser = $userService->get($userIds[0]))){
			return false;
		}
		$messageInfo['expand'] = serialize(array('categoryid' => $categoryId,'typeid' => $typeId, 'parentid' => $parentId));
		$messageInfo['extra'] = serialize(array($toUser['username']));
		if (!($messageId = $this->_addMessage($messageInfo))) {
			return false;
		}
		$relations = array();
		$userIds = array($toUser['uid'],$userId);
		foreach ($userIds as $otherId) {
			$relation = array();
			$relation['uid']          = $otherId;
			$relation['mid']          = $messageId;
			$relation['categoryid']   = $categoryId;
			$relation['typeid']       = $typeId;
			$relation['status']       = ($otherId == $userId) ? $this->_s_have_read : $this->_s_not_read;
			$relation['isown']        = ($otherId == $userId) ? $this->_s_self : $this->_s_other;
			$relation['relation']     = $this->_c_relation_reply;
			$relation['created_time'] = $relation['modified_time'] = $this->_timestamp;
			$relations[] = $relation;
		}
		$relationsDao = $this->getRelationsDao();
		if (!($relationId = $relationsDao->addReplyRelations($relations))) {
			return false;
		}
		$this->_addSearch($userId, $toUser['uid'], $relationId, $messageId, $typeId);
		return $messageId;
	}
	
	function _doReply($userId, $parentId, $messageInfo) {
		$userId = intval($userId);
		$parentId = intval($parentId);
		if (1 > $userId || 1 > $parentId) {
			return false;
		}
		if (false == ($messageInfo = $this->_checkInfo($messageInfo))) {
			return false;
		}
		$repliesDao = $this->getRepliesDao();
		$fieldData = array();
		$fieldData['parentid'] = $parentId;
		$fieldData['create_uid'] = $messageInfo['create_uid'];
		$fieldData['create_username'] = $messageInfo['create_username'];
		$fieldData['title'] = $messageInfo['title'];
		$fieldData['content'] = $messageInfo['content'];
		$fieldData['status'] = $this->_s_not_read;
		$fieldData['created_time'] = $fieldData['modified_time'] = $this->_timestamp;
		if (!($result = $repliesDao->insert($fieldData))) {
			return false;
		}
		return $result;
	}
	/**
	 * ��ȡĳ����Ϣ��ȫ���Ի�
	 * @param int $userId
	 * @param int $messageId
	 * @return unknown_type
	 */
	function _getReplies($userId, $messageId, $relationId) {
		$userId = intval($userId);
		$messageId = intval($messageId);
		if (1 > $userId || 1 > $messageId) {
			return false;
		}
		$repliesDao = $this->getRepliesDao();
		if (!($replies = $repliesDao->getRepliesByMessageId($messageId))) {
			return false;
		}
		//update not self status
		$ids = array();
		foreach ($replies as $r) {
			($r['create_uid'] != $userId) ? $ids[] = $r['id'] : 0;
		}
		$ids && $this->_updateRepliesByIds(array('status' => $this->_s_have_read), $ids);
		//$this->_updateByMessageIds(array('status'=>$this->_s_have_read),$userId,array($messageId));
		$this->_update(array('actived_time' => $this->_timestamp,'status' => $this->_s_have_read), $userId, $relationId);
		if (!($result = $this->_buildUsersLists($replies))) {
			return false;
		}
		return $this->_buildOnLineUser($result);
	}
	/**
	 * ��ȡĳ����Ϣ�Ķ��˶Ի�
	 * @param $userId
	 * @param $messageId
	 * @param $relationId
	 * @return array
	 */
	function _getGroupReplies($userId, $messageId, $relationId) {
		$userId = intval($userId);
		$messageId = intval($messageId);
		$relationId = intval($relationId);
		if (1 > $userId || 1 > $messageId || 1 > $relationId) {
			return false;
		}
		$repliesDao = $this->getRepliesDao();
		if (!($replies = $repliesDao->getRepliesByMessageId($messageId))) {
			return false;
		}
		$this->_update(array('actived_time' => $this->_timestamp,'status' => $this->_s_have_read), $userId, $relationId);
		if (!($result = $this->_buildUsersLists($replies))) {
			return false;
		}
		return $this->_buildOnLineUser($result);
	}
	
	function _buildOnLineUser($replies) {
		if (!$replies) return false;
		$userIds = array();
		foreach ($replies as $r) {
			$userIds[] = $r['uid'];
		}
		
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
		$onlineUser = array();
		foreach ($userService->getByUserIds($userIds) as $u) {
			$onlineUser[$u['uid']] = $u['thisvisit'];
		}
		
		$tmp = array();
		foreach ($replies as $r) {
			$r['thisvisit'] = (isset($onlineUser[$r['uid']])) ? $onlineUser[$r['uid']] : 0;
			$tmp[] = $r;
		}
		return $tmp;
	}
	/**
	 * �����������ID��ȡ��Ϣ
	 * @param int $userId   �û�UID
	 * @param string $category ��������
	 * @param int $typeId      ����ID
	 * @param int $page        ҳ��
	 * @param int $perpage     ��ҳ��
	 * @return array  ������Ϣ��+��ϵ������ 
	 */
	function _getsByTypeId($userId, $category, $typeId, $page, $perpage) {
		$userId = intval($userId);
		$page = intval($page);
		$perpage = intval($perpage);
		$typeId = intval($typeId);
		if (1 > $userId || 1 > $page || 1 > $perpage || 1 > $typeId) {
			return false;
		}
		$categoryId = intval($this->getMap($category));
		$relationsDao = $this->getRelationsDao();
		$start = ($page - 1) * $perpage;
		if (!($relations = $relationsDao->getRelations($userId, $categoryId, $typeId, $start, $perpage))) {
			return false;
		}
		return $this->_build($relations, $category);
	}
	/**
	 * �����������ͳ����Ϣ
	 * @param $userId
	 * @param $category
	 * @param $typeId
	 * @return unknown_type
	 */
	function _countByTypeId($userId, $category, $typeId) {
		$userId = intval($userId);
		$typeId = intval($typeId);
		if (1 > $userId || 1 > $typeId) {
			return false;
		}
		$categoryId = intval($this->getMap($category));
		$relationsDao = $this->getRelationsDao();
		return intval($relationsDao->countRelations($userId, $categoryId, $typeId));
	}
	/**
	 * ������ȡ����ĳ����Ϣ
	 * @param $userId
	 * @param $category
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function _getAll($userId, $category, $page, $perpage) {
		$userId = intval($userId);
		$page = intval($page);
		$perpage = intval($perpage);
		if (1 > $userId || 1 > $page || 1 > $perpage) {
			return false;
		}
		$categoryId = intval($this->getMap($category));
		$relationsDao = $this->getRelationsDao();
		$start = ($page - 1) * $perpage;
		if (!($relations = $relationsDao->getAllRelations($userId, $categoryId, $start, $perpage))) {
			return false;
		}
		return $this->_build($relations, $category);
	}
	/**
	 * ����ͳ������ĳ����Ϣ
	 * @param $userId
	 * @param $category
	 * @return unknown_type
	 */
	function _countAll($userId, $category) {
		$userId = intval($userId);
		if (1 > $userId) {
			return false;
		}
		$categoryId = intval($this->getMap($category));
		$relationsDao = $this->getRelationsDao();
		return intval($relationsDao->countAllRelations($userId, $categoryId));
	}
	/**
	 * ������ȡĳ����ĩ����Ϣ
	 * @param $userId
	 * @param $category
	 * @param $status �Ƿ���/ĩ����Ϣ
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function _getsByStatus($userId, $category, $status, $page, $perpage) {
		$userId = intval($userId);
		$page = intval($page);
		$perpage = intval($perpage);
		$status = intval($status);
		if (1 > $userId || 1 > $page || 1 > $perpage) {
			return false;
		}
		$categoryId = intval($this->getMap($category));
		$relationsDao = $this->getRelationsDao();
		$start = ($page - 1) * $perpage;
		if (!($relations = $relationsDao->getRelationsByStatus($userId, $categoryId, $status, $start, $perpage))) {
			return false;
		}
		return $this->_build($relations, $category);
	}
	/**
	 * ����ͳ��ĳ����ĩ����Ϣ
	 * @param $userId
	 * @param $category
	 * @param $status
	 * @return unknown_type
	 */
	function _countByStatus($userId, $category, $status) {
		$userId = intval($userId);
		$status = intval($status);
		if (1 > $userId) {
			return false;
		}
		$categoryId = intval($this->getMap($category));
		$relationsDao = $this->getRelationsDao();
		return intval($relationsDao->countRelationsByStatus($userId, $categoryId, $status));
	}
	
	/**
	 * ���������Ϣ������ȡ��Ϣ
	 * @param $userId
	 * @param $category
	 * @param $isown   �Ƿ��ҽ���/����
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function _getsByIsown($userId, $category, $typeId, $isown, $page, $perpage) {
		$userId = intval($userId);
		$typeId = intval($typeId);
		$page = intval($page);
		$perpage = intval($perpage);
		$isown = intval($isown);
		if (1 > $userId || 1 > $page || 1 > $perpage) {
			return false;
		}
		$categoryId = intval($this->getMap($category));
		$relationsDao = $this->getRelationsDao();
		$start = ($page - 1) * $perpage;
		if (!($relations = $relationsDao->getRelationsByIsown($userId, $categoryId, $typeId, $isown, $start, $perpage))) {
			return false;
		}
		return $this->_build($relations);
	}
	/**
	 * ���������Ϣ������ȡ��Ϣ ��ȡȫ����Ϣ
	 * @param $userId
	 * @param $category
	 * @param $isown   �Ƿ��ҽ���/����
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function _getsAllByIsown($userId, $category, $typeId, $isown, $page, $perpage) {
		$userId = intval($userId);
		$typeId = intval($typeId);
		$page = intval($page);
		$perpage = intval($perpage);
		$isown = intval($isown);
		if (1 > $userId || 1 > $page || 1 > $perpage) {
			return false;
		}
		$categoryId = intval($this->getMap($category));
		$relationsDao = $this->getRelationsDao();
		$start = ($page - 1) * $perpage;
		if (!($relations = $relationsDao->getAllRelationsByIsown($userId, $categoryId, $typeId, $isown, $start, $perpage))) {
			return false;
		}
		return $this->_build($relations);
	}
	/**
	 * ����ͳ����Ϣ������
	 * @param $userId
	 * @param $category
	 * @param $isown
	 * @return unknown_type
	 */
	function _countByIsown($userId, $category, $typeId, $isown) {
		$userId = intval($userId);
		$isown = intval($isown);
		$typeId = intval($typeId);
		if (1 > $userId) {
			return false;
		}
		$categoryId = intval($this->getMap($category));
		$relationsDao = $this->getRelationsDao();
		return intval($relationsDao->countRelationsByIsown($userId, $categoryId, $typeId, $isown));
	}
	/**
	 * �������ȡ�ҷ���/���յ���Ϣ
	 * @param $userId
	 * @param $category
	 * @param $typeId
	 * @param $isown
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function _getsSpecialByIsown($userId, $category, $typeId, $isown, $page, $perpage) {
		$userId = intval($userId);
		$typeId = intval($typeId);
		$page = intval($page);
		$perpage = intval($perpage);
		$isown = intval($isown);
		if (1 > $userId || 1 > $page || 1 > $perpage) {
			return false;
		}
		$categoryId = intval($this->getMap($category));
		$relationsDao = $this->getRelationsDao();
		$start = ($page - 1) * $perpage;
		if (!($relations = $relationsDao->getSpecialRelationsByIsown($userId, $categoryId, $typeId, $isown, $start, $perpage))) {
			return false;
		}
		return $this->_build($relations);
	}
	/**
	 * ������ͳ���ҷ���/���յ���Ϣ
	 * @param $userId
	 * @param $category
	 * @param $typeId
	 * @param $isown
	 * @return unknown_type
	 */
	function _countSpecialByIsown($userId, $category, $typeId, $isown) {
		$userId = intval($userId);
		$isown = intval($isown);
		$typeId = intval($typeId);
		if (1 > $userId) {
			return false;
		}
		$categoryId = intval($this->getMap($category));
		$relationsDao = $this->getRelationsDao();
		return intval($relationsDao->countSpecialRelationsByIsown($userId, $categoryId, $typeId, $isown));
	}
	/**
	 * ��������ɾ����ϵ��
	 * @param $relationIds
	 * @return unknown_type
	 */
	function _deleteRelations($userId, $relationIds) {
		$userId = intval($userId);
		if (1 > $userId || !$relationIds) {
			return false;
		}
		$relationIds = (is_array($relationIds)) ? $relationIds : array($relationIds);
		$relationsDao = $this->getRelationsDao();
		if (!$relationsDao->deleteRelations($userId, $relationIds)) {
			return false;
		}
		$this->_deleteSearch($userId, $relationIds);
		return true;
	}
	/**
	 * ����û�UID���ϵID���¹�ϵ��
	 * @param array $fieldData
	 * @param $userId
	 * @param $relationId
	 * @return unknown_type
	 */
	function _update($fieldData, $userId, $relationId) {
		$userId = intval($userId);
		$relationId = intval($relationId);
		if (1 > $userId || 1 > $relationId) {
			return false;
		}
		$relationsDao = $this->getRelationsDao();
		return $relationsDao->updateRelationsByUserId($fieldData, $userId, array($relationId));
	}
	/**
	 * ��Ƕ�����Ϣ�Ѷ�
	 * @param $userId
	 * @param array $relationIds
	 * @return unknown_type
	 */
	function _mark($userId, $relationIds) {
		$userId = intval($userId);
		if (1 > $userId || !$relationIds || !is_array($relationIds)) {
			return false;
		}
		$relationsDao = $this->getRelationsDao();
		$relationsDao->updateRelationsByUserId(array('status' => $this->_s_have_read), $userId, $relationIds);
		return true;
	}
	/**
	 * �����Ϣ������ȡһ����Ϣ����
	 * @param $messageId
	 * @return array
	 */
	function _get($messageId) {
		$messageId = intval($messageId);
		if (1 > $messageId) {
			return false;
		}
		$messagesDao = $this->getMessagesDao();
		return $messagesDao->get($messageId);
	}
	
	/**
	 * ���ݹ�ϵID�������¹�ϵ״̬
	 * @param array $fieldData
	 * @param int $userId
	 * @param array $relationIds
	 * @return unknown_type
	 */
	function updateRelations($fieldData, $userId, $relationIds) {
		if (!$fieldData || !$relationIds || !is_array($relationIds)) {
			return false;
		}
		$relationsDao = $this->getRelationsDao();
		return $relationsDao->updateRelations($fieldData, $userId, $relationIds);
	}
	/**
	 * ����û�UID���ϵID���¹�ϵ��
	 * @param array $fieldData
	 * @param $userId
	 * @param array $messageIds
	 * @return unknown_type
	 */
	function _updateByMessageIds($fieldData, $userId, $messageIds) {
		$userId = intval($userId);
		if (1 > $userId || !$messageIds) {
			return false;
		}
		$relationsDao = $this->getRelationsDao();
		return $relationsDao->updateRelationsByUserIdAndMessageId($fieldData, $userId, $messageIds);
	}
	/**
	 * ���������û���Ϣͳ����
	 * @param $userIds
	 * @param $category
	 * @param $number
	 * @return unknown_type
	 */
	function _updateNumsByUserIds($userIds, $category, $number) {
		if (!$userIds || !$category) return false;
		list($bool, $eUserIds, $nUserIds) = $this->_checkUsersByUserIds($userIds);
		if (!$bool) return false;
		$configsDao = $this->getConfigsDao();
		switch ($category) {
			case $this->_sms:
				$configsDao->updateSmsNumsByUserIds($eUserIds, $nUserIds, $number);
				break;
			case $this->_notice:
				$configsDao->updateNoticeNumsByUserIds($eUserIds, $nUserIds, $number);
				break;
			case $this->_request:
				$configsDao->updateRequestNumsByUserIds($eUserIds, $nUserIds, $number);
				break;
			case $this->_groupsms:
				$configsDao->updateGroupsmsNumsByUserIds($eUserIds, $nUserIds, $number);
				break;
			default:
				break;
		}
	}
	/**
	 * ����û�����ȡ�û���Ϣ
	 * @param $usernames
	 * @param $isFilter �Ƿ�����û�
	 * @return unknown_type
	 */
	function _getUserByUserNames($usernames, $isFilter = true) {
		if (!$usernames) return array(false,false);
		
		$userService = $this->_getUserService();
		$users = $userService->getByUserNames($usernames);
		if (!$users) {
			return array(false,false);
		}
		$userIds = $userNames = $groupIds = $tmp = array();
		foreach ($users as $user) {
			if ($user['uid'] > 0) {
				$userIds[] = $user['uid'];
				$userNames[] = $user['username'];
				//$groupIds[$user['uid']] = $user['groupid'];
				$groupIds[$user['uid']] = ($user['groupid'] > 0) ? $user['groupid'] : $user['memberid'];
				$tmp[$user['uid']] = $user['username'];
			}
		}
		//black filter
		return ($isFilter) ? $this->_filterUsers($tmp, $groupIds) : array($userIds,$userNames);
	}
	/**
	 * �����û�����
	 * @param $userId
	 * @param $mkey
	 * @param $mValue
	 * @return unknown_type
	 */
	function _setMsConfig($fieldData, $userId) {
		if (1 > $userId || !$fieldData) return false;
		$configsDao = $this->getConfigsDao();
		if (!($config = $configsDao->get($userId))) {
			return $configsDao->insertConfigs($fieldData, array($userId));
		}
		return $configsDao->update($fieldData, $userId);
	}
	/**
	 * ��ȡһ���û�����
	 * @param $userId
	 * @param $mKey
	 * @return unknown_type
	 */
	function _getMsConfig($userId, $mKey) {
		if (1 > $userId || "" == $mKey) return false;
		$configsDao = $this->getConfigsDao();
		$config = $configsDao->get($userId);
		return (isset($config[$mKey])) ? $config[$mKey] : '';
	}
	/**
	 * ��駻ظ�IDS���»ظ�״̬
	 * @param $fieldData
	 * @param $ids
	 * @return unknown_type
	 */
	function _updateRepliesByIds($fieldData, $ids) {
		if (!$fieldData || !$ids) return false;
		$repliesDao = $this->getRepliesDao();
		return $repliesDao->updateRepliesByIds($fieldData, $ids);
	}
	function _upMessage($userId, $category, $relationId, $typeId = null) {
		$userId = intval($userId);
		$categoryId = intval($this->getMap($category));
		$typeId = intval($typeId);
		$relationId = intval($relationId);
		if (1 > $userId || 1 > $categoryId || 1 > $relationId) {
			return false;
		}
		$relationsDao = $this->getRelationsDao();
		if (!$tmpRelation = $relationsDao->get($relationId)) {
			return false;
		}
		if (!$relation = $relationsDao->getUpRelation($userId, $categoryId, $relationId, $tmpRelation['modified_time'], $typeId)) {
			return false;
		}
		if (!$message = $this->_get($relation['mid'])) {
			return false;
		}
		($relation['status'] == $this->_s_not_read) && $this->_mark($userId, array($relationId));
		return $relation + $message;
	}
	/**
	 * 
	 * ��ȡ��һ����Ϣ ������
	 * @param int $userId
	 * @param int $category
	 * @param int $relationId
	 * @param int $typeId
	 */
	function _getUpMsInfoByType($userId, $category, $relationId, $isown, $typeId = null) {
		$userId = intval($userId);
		$categoryId = intval($this->getMap($category));
		$typeId = intval($typeId);
		$relationId = intval($relationId);
		$isown = intval($isown);
		if (1 > $userId || 1 > $categoryId || 1 > $relationId) {
			return false;
		}
		$relationsDao = $this->getRelationsDao();
		if (!$tmpRelation = $relationsDao->get($relationId)) {
			return false;
		}
		if (!$relation = $relationsDao->getUpInfoByType($userId, $categoryId, $relationId, $tmpRelation['modified_time'], $isown, $typeId)) {
			return false;
		}
		if (!$message = $this->_get($relation['mid'])) {
			return false;
		}
		($relation['status'] == $this->_s_not_read) && $this->_mark($userId, array($relationId));
		return $relation + $message;
	}
	/**
	 * 
	 * ��ȡ��һ����Ϣ ������
	 * @param int $userId
	 * @param int $category
	 * @param int $relationId
	 * @param int $typeId
	 */
	function _getDownMsInfoByType($userId, $category, $relationId, $isown, $typeId = null) {
		$userId = intval($userId);
		$categoryId = intval($this->getMap($category));
		$typeId = intval($typeId);
		$relationId = intval($relationId);
		$isown = intval($isown);
		if (1 > $userId || 1 > $categoryId || 1 > $relationId) {
			return false;
		}
		$relationsDao = $this->getRelationsDao();
		if (!$tmpRelation = $relationsDao->get($relationId)) {
			return false;
		}
		if (!$relation = $relationsDao->getDownInfoByType($userId, $categoryId, $relationId, $tmpRelation['modified_time'], $isown, $typeId)) {
			return false;
		}
		if (!$message = $this->_get($relation['mid'])) {
			return false;
		}
		($relation['status'] == $this->_s_not_read) && $this->_mark($userId, array($relationId));
		return $relation + $message;
	}
	function _downMessage($userId, $category, $relationId, $typeId = null) {
		$userId = intval($userId);
		$categoryId = intval($this->getMap($category));
		$typeId = intval($typeId);
		$relationId = intval($relationId);
		if (1 > $userId || 1 > $categoryId || 1 > $relationId) {
			return false;
		}
		$relationsDao = $this->getRelationsDao();
		if (!$tmpRelation = $relationsDao->get($relationId)) {
			return false;
		}
		if (!$relation = $relationsDao->getDownRelation($userId, $categoryId, $relationId, $tmpRelation['modified_time'], $typeId)) {
			return false;
		}
		if (!$message = $this->_get($relation['mid'])) {
			return false;
		}
		($relation['status'] == $this->_s_not_read) && $this->_mark($userId, array($relationId));
		return $relation + $message;
	}
	/**
	 * ˽�м���û�ĳKEY�Ƿ����
	 * @param $userIds
	 * @param $mKey
	 * @return array(bool,�Ѿ����ڵ��û����飬�����ڵ��û�����)
	 */
	function _checkUsersByUserIds($userIds) {
		$configsDao = $this->getConfigsDao();
		$configs = $configsDao->gets($userIds);
		$eUserIds = $nUserIds = array();
		if ($configs) {
			foreach ($configs as $c) {
				($c['uid'] > 0) ? $eUserIds[] = $c['uid'] : 0;
			}
			$nUserIds = array_diff($userIds, $eUserIds);
			return array(true,$eUserIds,$nUserIds);
		}
		return array(true,array(),$userIds);
	}
	/**
	 * ˽�м����Ϣ�����ݺ���
	 * @param $messageInfo
	 * @return unknown_type
	 */
	function _checkInfo($messageInfo) {
		$data = array();
		$data['create_uid'] = intval($messageInfo['create_uid']);
		$data['create_username'] = trim($messageInfo['create_username']);
		if (0 == $data['create_uid'] || "" == $data['create_username']) {
			return false;
		}
		$data['title'] = trim($messageInfo['title']);
		$data['content'] = trim($messageInfo['content']);
		if ("" == $data['title'] || "" == $data['content']) {
			return false;
		}
		isset($messageInfo['extra']) && $data['extra'] = $messageInfo['extra'];
		isset($messageInfo['expand']) && $data['expand'] = $messageInfo['expand'];
		$data['created_time'] = $data['modified_time'] = time();
		return $data;
	}
	/**
	 * ˽����װ��Ϣ�����ϵ����Ϣ����
	 * @param $relations
	 * @return unknown_type
	 */
	function _build($relations, $category = false) {
		if (!$relations) return false;
		$messageIds = $tmpRelations = array();
		foreach ($relations as $r) {
			($r['mid']) ? $messageIds[] = $r['mid'] : 0;
			$tmpRelations[$r['rid']] = $r;
		}
		if (!$messageIds) return false;
		$messagesDao = $this->getMessagesDao();
		if (!($messages = $messagesDao->getMessagesByMessageIds($messageIds))) {
			return false;
		}
		$tmpMessages = $result = array();
		foreach ($messages as $m) {
			$tmpMessages[$m['mid']] = $m;
		}
		foreach ($tmpRelations as $rid => $r) {
			(isset($tmpMessages[$r['mid']])) ? $result[$rid] = $r + $tmpMessages[$r['mid']] : 0;
		}
		return ($category == $this->_notice) ? $result : $this->_buildUsersLists($result);
	}
	/**
	 * ˽����װǰ̨չʾ��Ϣ����
	 * @param $arrays ��Ϣ��+��ϵ����Ϣ
	 * ע��:$tpc_author����������װ�����û���ǰ׺
	 * @return array
	 */
	function _buildUsersLists($arrays) {
		global $tpc_author;
		if (!$arrays) return false;
		$userIds = array();
		foreach ($arrays as $v) {
			(0 < $v['create_uid']) ? $userIds[] = $v['create_uid'] : 0;
		}
		$tmp = $this->_retrieveUsers($userIds);
		require_once (R_P . 'require/bbscode.php');
		$groupInfos = $tmpArrays = array();
		foreach ($arrays as $rid => $a) {
			$created_timefromat = getLastDate($a['created_time']);
			$modified_timefromat = getLastDate($a['modified_time']);
			$a['title'] = $this->_reverseString($a['title']);
			$tpc_author = $a['create_username'];
			$a['created_time_format'] = $created_timefromat[0];
			$a['modified_time_format'] = $modified_timefromat[0];
			$a['created_time_detail'] = get_date($a['created_time'], 'Y-m-d H:i');
			$a['modified_time_detail'] = get_date($a['modified_time'], 'Y-m-d H:i');
			$a['content'] = $this->_reverseString($this->_stringReplace(convert($a['content'], $this->_windpost)));
			$a['extra'] = ($a['extra']) ? unserialize($a['extra']) : '';
			$tmpArrays[$rid] = isset($tmp[$a['create_uid']]) ? $tmp[$a['create_uid']] + $a : $a;
			($a['typeid'] == $this->getMap($this->_groupsms_colony)) ? $groupInfos[$a['mid']] = $a['extra'] : 0;
		}
		// build group
		if ($groupInfos && ($groups = $this->_buildColonyList($groupInfos))) {
			$t = array();
			foreach ($tmpArrays as $rid => $v) {
				$t[$rid] = (isset($groups[$v['mid']])) ? $groups[$v['mid']] + $v : $v;
			}
			return $t;
		}
		return $tmpArrays;
	}
	function _retrieveUsers($userIds) {
		if (!$userIds) return array();
		array_unique($userIds);
		$userService = $this->_getUserService();
		$members = $userService->getByUserIds($userIds);
		$tmp = array();
		require_once (R_P . 'require/showimg.php');
		foreach ($members as $member) {
			list($member['face']) = showfacedesign($member['icon'], 1, 'm');
			$tmp[$member['uid']] = $member;
		}
		return $tmp;
	}
	function _reverseString($content) {
		return str_replace(array('"' . $this->_userName . '"','[' . $this->_userName . ']',
								'&quot;' . $this->_userName . '&quot;'), '��', $content);
	}
	function _stringReplace($value) {
		return nl2br($value);
	}
	/**
	 * ˽����װȺ����Ϣ
	 * @param $groupInfos
	 * @return unknown_type
	 */
	function _buildColonyList($groupInfos) {
		if (!$groupInfos) return false;
		$groupIds = $ids = array();
		foreach ($groupInfos as $mid => $group) {
			$groupIds[$mid] = $group['groupid'];
			$ids[] = $group['groupid'];
		}
		$colonysDao = $this->getColonysDao();
		if (!$colonys = $colonysDao->getsIds($ids)) {
			return false;
		}
		$tmpColonys = array();
		foreach ($colonys as $c) {
			$c['cnimg'] = ($c['cnimg']) ? $this->_attachmentPath . '/cn_img/' . $c['cnimg'] : 'images/g/groupnopic.gif';
			$tmpColonys[$c['colonyid']] = $c;
		}
		$result = array();
		foreach ($groupIds as $mid => $v) {
			$result[$mid] = $tmpColonys[$v];
		}
		return $result;
	}
	
	function _clearMessages($userId, $categoryIds) {
		if (1 > $userId || !$categoryIds) return false;
		$relationsDao = $this->getRelationsDao();
		if (!$relationsDao->deleteRelationsByUserIdAndCategoryId($userId, $categoryIds)) {
			return false;
		}
		$categoryId = $this->getMap($this->_sms);
		if (in_array($categoryId, $categoryIds)) {
			$searchsDao = $this->getSearchsDao();
			$searchsDao->deleteAll($userId);
		}
		return false;
	}
	/**
	 * �����û������û�ID�����û���Ϣ��
	 * @param array $userIds
	 * @param array $userNames
	 * @param int $number
	 * @return array($userIds,$userNames)
	 */
	function _updateStatisticsByUserNames($userIds, $userNames = null, $category, $number) {
		if (!$userIds && $userNames) {
			list($userIds) = $this->_getUserByUserNames($userNames);
		}
		if (!$userIds) return false;
		$category = ($category) ? $category : $this->_sms;
		$category = ((count($userIds) >= 2) && ($category == $this->_sms)) ? $this->_groupsms : $category;
		$this->_updateNumsByUserIds($userIds, $category, $number);
		return true;
	}
	/**
	 * �����Ϣ���͸����û���Ϣ��
	 * @param $categoryId
	 * @param $message
	 */
	function _updateStatisticsByCategoryId($categoryId, $message, $userId = null) {
		switch ($categoryId) {
			case $this->getMap($this->_groupsms):
				if ($message['extra']) {
					$userNames = unserialize($message['extra']);
					$userNames = $userNames + array($message['create_username']);
					$receiveUserIds = $this->_getParticipantByMessageId($message['mid']);
					if ($receiveUserIds) {
						list($userIds) = $this->_getUserByUserNames($userNames);
						$userIds = array_intersect($userIds, $receiveUserIds);
						$this->_updateStatisticsByUserNames($userIds, null, $this->_groupsms, 1);
						$this->_updateUserMessageNumbers($userIds);
					}
				}
				break;
			default:
				$userIds = $this->_getParticipantByMessageId($message['mid']);
				if ($userIds) {
					$userIds = array_diff($userIds, array($userId));
					$this->_updateStatisticsByUserNames($userIds, null, null, 1);
					$this->_updateUserMessageNumbers($userIds);
				}
				break;
		}
		return true;
	}
	/**
	 * �����û���Ϣ��
	 * @param $userIds
	 */
	function _updateUserMessageNumbers($userIds,$category = null) {
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
		$messageServer = L::loadClass('message', 'message');
		!S::isArray($userIds) && $userIds = array($userIds);
		foreach ($userIds as $userid) {
			list(,$messageNumber,$noticeNumber,$requestNumber,$groupsmsNumber) = $messageServer->countAllByUserId($userid);
			switch ($category) {
				case $this->_notice:
					$userService->update($userid, array(), array('newnotice' => $noticeNumber));
					break;
				case $this->_request:
					$userService->update($userid, array(), array('newrequest' => $requestNumber));
					break;
				default:
					$userService->update($userid, array('newpm' => $messageNumber + $groupsmsNumber));
			}
		}
		return true;
	}
	/**
	 * ͨ����ϢID��ȡ������
	 * @param $messageId
	 * @return unknown_type
	 */
	function _getParticipantByMessageId($messageId) {
		$relationsDao = $this->getRelationsDao();
		if (!($result = $relationsDao->getRelationsByMessageId($messageId))) {
			return array();
		}
		$userIds = array();
		foreach ($result as $r) {
			$userIds[] = $r['uid'];
		}
		return $userIds;
	}
	/**
	 * ������������
	 * @param $fieldData
	 * @return unknown_type
	 */
	function _addSearch($userId, $toUserId, $relationId, $messageId, $typeId) {
		$fieldData = array('rid' => $relationId,'uid' => $toUserId,'mid' => $messageId,'typeid' => $typeId,
						'create_uid' => $userId,'created_time' => $this->_timestamp);
		$searchsDao = $this->getSearchsDao();
		return $searchsDao->insert($fieldData);
	}
	/**
	 * ����ɾ����������
	 * @param $userId
	 * @param $relationIds
	 * @return unknown_type
	 */
	function _deleteSearch($userId, $relationIds) {
		$userId = intval($userId);
		if (1 > $userId || !$relationIds) {
			return false;
		}
		$searchsDao = $this->getSearchsDao();
		return $searchsDao->deletesByUserId($userId, $relationIds);
	}
	/**
	 * �����������͵�ͼ
	 * @return unknown_type
	 */
	function maps() {
		return array($this->_sms => 1,
					$this->_sms_message => 100,$this->_sms_rate => 101,$this->_sms_comment => 102,
					$this->_sms_guestbook => 103,$this->_sms_share => 104,$this->_sms_reply => 105,
					$this->_notice => 2,
					$this->_notice_system => 200,$this->_notice_postcate => 201,$this->_notice_active => 202,
					$this->_notice_apps => 203,$this->_notice_comment => 204,$this->_notice_guestbook => 205,
					$this->_request => 3,
					$this->_request_friend => 300,$this->_request_group => 301,$this->_request_active => 302,$this->_request_apps => 303,
					$this->_groupsms => 4,
					$this->_groupsms_colony => 400,$this->_groupsms_normal => 401,
					$this->_groupsms_shield => 402,$this->_chat => 5,$this->_history => 0);
	}
	/**
	 * �����������ϵͼ
	 * @return unknown_type
	 */
	function maps2() {
		return array(
					$this->_sms => array($this->_sms_message,$this->_sms_rate,$this->_sms_comment,$this->_sms_guestbook,
										$this->_sms_share,$this->_sms_reply),
					$this->_notice => array($this->_notice_system,$this->_notice_postcate,$this->_notice_active,
											$this->_notice_apps,$this->_notice_comment,$this->_notice_guestbook),
					$this->_request => array($this->_request_friend,$this->_request_group,$this->_request_active,
											$this->_request_apps),
					$this->_groupsms => array($this->_groupsms_colony,$this->_groupsms_normal,$this->_groupsms_shield));
	}
	/**
	 * �����������ȡ����ID
	 * @param $types
	 * @return unknown_type
	 */
	function _getSpecialMap($types) {
		if (!$types) return array();
		$maps = $this->maps();
		$typeIds = array();
		foreach ($types as $type) {
			(isset($maps[$type])) ? $typeIds[] = $maps[$type] : 0;
		}
		return $typeIds;
	}
	/**
	 * ͳ������
	 * @param $category
	 * @return unknown_type
	 */
	function getStatisticsByCategory($category) {
		$maps = array($this->_sms => $this->_c_sms_num,$this->_notice => $this->_c_notice_num,
					$this->_request => $this->_c_request_num,$this->_groupsms => $this->_c_groupsms_num);
		return (isset($maps[$category])) ? $maps[$category] : '';
	}
	/**
	 * ����������ƻ�ȡ����ΨһID
	 * @param $k
	 * @return unknown_type
	 */
	function getMap($k) {
		$maps = $this->maps();
		return (isset($maps[$k])) ? $maps[$k] : 0;
	}
	/**
	 * ��������ͻ�ȡ����������
	 * @param $typeId
	 * @param $isType �Ƿ��ȡ������
	 * @return unknown_type
	 */
	function getMapByTypeId($typeId, $isType = false) {
		$maps = array_flip($this->maps());
		if (!isset($maps[$typeId])) return false;
		$type = $maps[$typeId];
		if ($isType) return $type;
		$maps2 = $this->maps2();
		foreach ($maps2 as $category => $map) {
			if (is_array($map) && in_array($type, $map)) return $category;
		}
		return false;
	}
	/**
	 * �����û� ������
	 * @param $users
	 * @return array($userIds,$userNames)
	 */
	function _filterUsers($users, $groupInfos) {
		if (!$users) return array(false,false);
		$configs = $this->_getMsConfigsByUserIds(array_keys($users));
		if ($configs) {
			foreach ($configs as $uid => $config) {
				if (isset($config[$this->_c_blackgroup]) && $this->_groupId && is_array($config[$this->_c_blackgroup]) && in_array($this->_groupId, $config[$this->_c_blackgroup])) {
					unset($users[$uid]);
					continue;
				}
				if (isset($config[$this->_c_blacklist]) && $this->_userName && is_array($config[$this->_c_blacklist]) && in_array($this->_userName, $config[$this->_c_blacklist])) {
					unset($users[$uid]);
				}
			}
		}
		if (!$users) return array(false,false);
		
		//�û��������Ϣ��Ŀ
		$permissions = $this->_getPermissions(); // array('gid'=>'total')
		//ͳ���û��Ķ���Ϣ��
		$userInfos = $this->_countUserNumbers(array_keys($users)); // array('uid'=>'total')
		foreach ($groupInfos as $uid => $groupId) {
			if (isset($permissions[$groupId]) && $permissions[$groupId] > 0 && isset($userInfos[$uid]) && $userInfos[$uid] >= $permissions[$groupId]) {
				unset($users[$uid]);
			}
		}
		return ($users) ? array(array_keys($users),array_values($users)) : array(false,false);
	}
	/**
	 * ͳ���û������Ϣ��
	 * @param $userIds
	 * @return unknown_type
	 */
	function _countUserNumbers($userIds) {
		$relationsDao = $this->getRelationsDao();
		$typeIds = array($this->getMap($this->_sms_message),$this->getMap($this->_groupsms_normal));
		$users = $relationsDao->countAllByUserIds($userIds, $typeIds);
		if (!$users) return false;
		$tmp = array();
		foreach ($users as $user) {
			$tmp[$user['uid']] = $user['total'];
		}
		return $tmp;
	}
	/**
	 * ��ȡ���л�Ա�������Ϣ��Ŀ
	 * @return unknown_type
	 */
	function _getPermissions() {
		$permissonsDao = $this->getPermissionDao();
		$permissons = $permissonsDao->getsByRkey('maxmsg');
		if (!$permissons) return false;
		$tmp = array();
		foreach ($permissons as $p) {
			$tmp[$p['gid']] = $p['rvalue'];
		}
		return $tmp;
	}
	/**
	 * ����û�ID��ȡ�û�������Ϣ
	 * @param $userIds
	 * @return unknown_type
	 */
	function _getMsConfigsByUserIds($userIds) {
		if (!$userIds) return false;
		$configsDao = $this->getConfigsDao();
		$configs = $configsDao->gets($userIds);
		if (!$configs) return false;
		$tmp = array();
		foreach ($configs as $c) {
			$shield = array();
			$shield[$this->_c_blacklist] = (isset($c[$this->_c_blacklist])) ? unserialize($c[$this->_c_blacklist]) : '';
			$shield[$this->_c_shieldinfo] = (isset($c[$this->_c_shieldinfo])) ? unserialize($c[$this->_c_shieldinfo]) : '';
			$shield[$this->_c_blackcolony] = (isset($c[$this->_c_blackcolony])) ? unserialize($c[$this->_c_blackcolony]) : '';
			$shield[$this->_c_blackgroup] = (isset($c[$this->_c_blackgroup])) ? unserialize($c[$this->_c_blackgroup]) : '';
			$tmp[$c['uid']] = $shield;
		}
		return $tmp;
	}
	/**
	 * ����������Ϣ������Ϣ
	 * @param array $messageIds
	 * @return unknown_type
	 */
	function _deleteAttachsByMessageIds($messageIds) {
		if (!$messageIds) return false;
		$msAttachsDao = $this->getMsAttachsDao();
		if (!($msAttachs = $msAttachsDao->getAttachsByMessageIds($messageIds))) {
			return false;
		}
		$attachIds = array();
		foreach ($msAttachs as $attach) {
			$attachIds[] = $attach['aid'];
		}
		$msAttachsDao->deleteAttachsByMessageIds($messageIds);
		$attachsDao = $this->getAttachsDao();
		if (!($attachs = $attachsDao->getsByAids($attachIds))) {
			return false;
		}
		$files = array();
		foreach ($attachs as $attach) {
			$file = $this->_attachmentPath . '/' . $attach['attachurl'];
			if (is_file($file)) {
				P_unlink($file);
			}
		}
		$attachsDao->deleteByAids($attachIds);
		return true;
	}
	/**
	 * ��Ϣ�������ñ���keys
	 * @return unknown_type
	 */
	var $_c_blackcolony = 'blackcolony'; //��Ⱥ�鵥
	var $_c_blacklist = 'blacklist'; //���û���
	var $_c_categories = 'categories'; //����
	var $_c_statistics = 'statistics'; //ͳ��
	var $_c_shieldinfo = 'shieldinfo'; //��������
	var $_c_blackgroup = 'blackgroup'; //���û���
	function _msConfigs() {
		return array($this->_c_blacklist,$this->_c_categories,$this->_c_statistics,$this->_c_blackcolony,
					$this->_c_shieldinfo,$this->_c_blackgroup);
	}
	/**
	 * ��ȡ��Ϣ���ı���keys
	 * @param $mkey
	 * @return unknown_type
	 */
	function _getMsConfigByKey($mkey) {
		$msConfigs = $this->_msConfigs();
		return (isset($msConfigs[$mkey])) ? $msConfigs[$mkey] : '';
	}
	
	/**
	 * ˽��ϵͳ�����û�
	 * @return unknown_type
	 */
	function virtualUser() {
		return array('uid' => -1,'username' => 'system');
	}
	/**
	 * ��Ϣ��DAO
	 * @return unknown_type
	 */
	function getMessagesDao() {
		static $sMessagesDao;
		if (!$sMessagesDao) {
			$sMessagesDao = L::loadDB('ms_messages', 'message');
		}
		return $sMessagesDao;
	}
	/**
	 * ��ϵ��DAO
	 * @return unknown_type
	 */
	function getRelationsDao() {
		static $sRelationsDao;
		if (!$sRelationsDao) {
			$sRelationsDao = L::loadDB('ms_relations', 'message');
		}
		return $sRelationsDao;
	}
	/**
	 * �ظ���DAO
	 * @return unknown_type
	 */
	function getRepliesDao() {
		static $sRepliesDao;
		if (!$sRepliesDao) {
			$sRepliesDao = L::loadDB('ms_replies', 'message');
		}
		return $sRepliesDao;
	}
	
	/**
	 * ��Ϣ����DAO
	 * @return unknown_type
	 */
	function getConfigsDao() {
		static $sConfigsDao;
		if (!$sConfigsDao) {
			$sConfigsDao = L::loadDB('ms_configs', 'message');
		}
		return $sConfigsDao;
	}
	/**
	 * ��Ϣ������ϵDAO
	 * @return unknown_type
	 */
	function getMsAttachsDao() {
		static $sMsAttachsDao;
		if (!$sMsAttachsDao) {
			$sMsAttachsDao = L::loadDB('ms_attachs', 'message');
		}
		return $sMsAttachsDao;
	}
	/**
	 * ������ϵDAO
	 * @return unknown_type
	 */
	function getAttachsDao() {
		static $sAttachsDao;
		if (!$sAttachsDao) {
			$sAttachsDao = L::loadDB('attachs', 'forum');
		}
		return $sAttachsDao;
	}
	/**
	 * Ⱥ��DAO
	 * @return unknown_type
	 */
	function getColonysDao() {
		static $sColonysDao;
		if (!$sColonysDao) {
			$sColonysDao = L::loadDB('colonys', 'colony');
		}
		return $sColonysDao;
	}
	/**
	 * Ⱥ���ԱDAO
	 * @return unknown_type
	 */
	function getCmembersDao() {
		static $sCmembersDao;
		if (!$sCmembersDao) {
			$sCmembersDao = L::loadDB('cmembers', 'colony');
		}
		return $sCmembersDao;
	}
	/**
	 * ��Ϣ��������DAO
	 * @return unknown_type
	 */
	function getSearchsDao() {
		static $sSearchDao;
		if (!$sSearchDao) {
			$sSearchDao = L::loadDB('ms_searchs', 'message');
		}
		return $sSearchDao;
	}
	/**
	 * ��Ϣ��������DAO
	 * @return unknown_type
	 */
	function getTaskDao() {
		static $sTaskDao;
		if (!$sTaskDao) {
			$sTaskDao = L::loadDB('ms_tasks', 'message');
		}
		return $sTaskDao;
	}
	/**
	 * �û���Ȩ��DAO
	 * @return unknown_type
	 */
	function getPermissionDao() {
		static $sPermissionDao;
		if (!$sPermissionDao) {
			$sPermissionDao = L::loadDB('permission', 'user');
		}
		return $sPermissionDao;
	}
	
	/**
	 * @return PW_UserService
	 */
	function _getUserService() {
		return L::loadClass('UserService', 'user');
	}
}
?>
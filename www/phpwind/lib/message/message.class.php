<?php
!function_exists('readover') && exit('Forbidden');
/**
 * ��Ϣ���ķ����
 * @2010-4-6 liuhui
 */
class PW_Message {
	/**
	 * ��������վ���ź���  ���˶Ի�/���˶Ի�
	 * @param int $userId  �����û�UID
	 * @param array $usernames �����û�������
	 * @param array $messageinfo array('create_uid','create_username','title','content','expand') ��Ϣ������
	 * @param string $typeId վ�������ͣ�Ĭ��Ϊ����Ϣ����ѡ����/����/����
	 * @return int ���ͳɹ���messageId
	 */
	function sendMessage($userId, $usernames, $messageInfo, $typeId = null ,$isSuper = false) {
		$service = $this->_serviceFactory("message");
		return $service->sendMessage($userId, $usernames, $messageInfo, $typeId ,$isSuper);
	}
	/**
	 * ������Ϣ����ȫ�ֻظ�����
	 * @param int $parentId ����ϢID
	 * @param int $relationId ��ϵID
	 * @param int $userId �����û�UID
	 * @param array $messageinfo �ظ�������
	 * @return int ���ͳɹ��Ļظ�id
	 */
	function sendReply($userId, $relationId, $parentId, $messageInfo) {
		$service = $this->_serviceFactory("message");
		return $service->sendReply($userId, $relationId, $parentId, $messageInfo);
	}
	/**
	 * ��ȡĳ��������վ����
	 * @param int $page
	 * @param int $userId �û�UID
	 * @param int $perpage
	 * @return array array(array(mid,rid,uid,title,content,typeid,categoryid,status,isown,created_time,modified_time,expand,create_uid,create_username)) ��ά����ṹ ��ϵ�����Ϣ����ֶ�����
	 */
	function getAllMessages($userId, $page, $perpage) {
		$service = $this->_serviceFactory("message");
		return $service->getAllMessages($userId, $page, $perpage);
	}
	/**
	 * ��ȡĳ����ĩ��վ����
	 * @param int $userId �û�UID
	 * @param int $page
	 * @param int $perpage
	 * @return array array(array(mid,rid,uid,title,content,typeid,categoryid,status,isown,created_time,modified_time,expand,create_uid,create_username)) ��ά����ṹ ��ϵ�����Ϣ����ֶ�����
	 */
	function getMessagesNotRead($userId, $page, $perpage) {
		$service = $this->_serviceFactory("message");
		return $service->getMessagesNotRead($userId, $page, $perpage);
	}
	/**
	 * ��ȡĳ���͵�վ����
	 * @param int $userId �û�UID
	 * @param int $typeId
	 * @param int $page
	 * @param int $perpage
	 * @return array
	 */
	function getMessages($userId, $typeId, $page, $perpage) {
		$service = $this->_serviceFactory("message");
		return $service->getMessages($userId, $typeId, $page, $perpage);
	}
	/**
	 * ��ȡȫ���Ի�����
	 * @param int $userId �û�UID
	 * @param int $messageId
	 * @param int $relationId
	 * @return array array(array(mid,rid,uid,title,content,typeid,categoryid,status,isown,created_time,modified_time,expand,create_uid,create_username))
	 */
	function getReplies($userId, $messageId, $relationId) {
		$service = $this->_serviceFactory("message");
		return $service->getReplies($userId, $messageId, $relationId);
	}
	/**
	 * ��ȡ��Ϣ����һ��
	 * @param int $relationId
	 * @return array array(mid,rid,uid,title,content,typeid,categoryid,status,isown,created_time,modified_time,expand,create_uid,create_username)
	 */
	function getUpMessage($userId, $relationId, $typeId = null) {
		$service = $this->_serviceFactory("message");
		return $service->getUpMessage($userId, $relationId, $typeId);
	}
	
	function getUpInfoByType($userId, $relationId, $isown, $typeId = null) {
		$service = $this->_serviceFactory("message");
		return $service->getUpInfoByType($userId, $relationId, $isown, $typeId);
	}
	
	function getDownInfoByType($userId, $relationId, $isown, $typeId = null) {
		$service = $this->_serviceFactory("message");
		return $service->getDownInfoByType($userId, $relationId, $isown, $typeId);
	}
	/**
	 * ��ȡ��Ϣ����һ��
	 * @param int $relationId
	 * @return array array(mid,rid,uid,title,content,typeid,categoryid,status,isown,created_time,modified_time,expand,create_uid,create_username)
	 */
	function getDownMessage($userId, $relationId, $typeId = null) {
		$service = $this->_serviceFactory("message");
		return $service->getDownMessage($userId, $relationId, $typeId);
	}
	/**
	 * ��ȡ�ҷ��͵�վ����
	 * @param int $page
	 * @param int $perpage
	 * @return array array(array(mid,rid,uid,title,content,typeid,categoryid,status,isown,created_time,modified_time,expand,create_uid,create_username))
	 */
	function getMessagesBySelf($userId, $typeId, $page, $perpage) {
		$service = $this->_serviceFactory("message");
		return $service->getMessagesBySelf($userId, $typeId, $page, $perpage);
	}
	/**
	 * ��ȡ�ҽ��յ�վ����
	 * @param int $page
	 * @param int $perpage
	 * @return array array(array(mid,rid,uid,title,content,typeid,categoryid,status,isown,created_time,modified_time,expand,create_uid,create_username))
	 */
	function getMessagesByOther($userId, $typeId, $page, $perpage) {
		$service = $this->_serviceFactory("message");
		return $service->getMessagesByOther($userId, $typeId, $page, $perpage);
	}
	/**
	 * ��ȡ�ҷ��͵�վȫ������
	 * @param int $page
	 * @param int $perpage
	 * @return array array(array(mid,rid,uid,title,content,typeid,categoryid,status,isown,created_time,modified_time,expand,create_uid,create_username))
	 */
	function getAllMessagesBySelf($userId, $typeId, $page, $perpage) {
		$service = $this->_serviceFactory("message");
		return $service->getAllMessagesBySelf($userId, $typeId, $page, $perpage);
	}
	/**
	 * ��ȡ�ҽ��յ�ȫ��վ����
	 * @param int $page
	 * @param int $perpage
	 * @return array array(array(mid,rid,uid,title,content,typeid,categoryid,status,isown,created_time,modified_time,expand,create_uid,create_username))
	 */
	function getAllMessagesByOther($userId, $typeId, $page, $perpage) {
		$service = $this->_serviceFactory("message");
		return $service->getAllMessagesByOther($userId, $typeId, $page, $perpage);
	}
	/**
	 * ͳ������վ����
	 * @return int
	 */
	function countAllMessage($userId) {
		$service = $this->_serviceFactory("message");
		return $service->countAllMessage($userId);
	}
	/**
	 * ͳ������ĩ��վ����
	 * @return int
	 */
	function countMessagesNotRead($userId) {
		$service = $this->_serviceFactory("message");
		return $service->countMessagesNotRead($userId);
	}
	/**
	 * ͳ�������ҷ���վ����
	 * @return int
	 */
	function countMessagesBySelf($userId, $typeId) {
		$service = $this->_serviceFactory("message");
		return $service->countMessagesBySelf($userId, $typeId);
	}
	/**
	 * ͳ���������յ�վ����
	 * @return int
	 */
	function countMessagesByOther($userId, $typeId) {
		$service = $this->_serviceFactory("message");
		return $service->countMessagesByOther($userId, $typeId);
	}
	/**
	 * ͳ��ĳ���͵�վ����
	 * @param $typeid
	 * @return int
	 */
	function countMessage($userId, $typeId) {
		$service = $this->_serviceFactory("message");
		return $service->countMessage($userId, $typeId);
	}
	/**
	 * ɾ��һ����ϵ��
	 * @param $relationId
	 * @return int
	 */
	function deleteMessage($userId, $relationId) {
		$service = $this->_serviceFactory("message");
		return $service->deleteMessage($userId, $relationId);
	}
	/**
	 * ɾ��������ϵ��
	 * @param array $relationIds
	 * @return int
	 */
	function deleteMessages($userId, $relationIds) {
		$service = $this->_serviceFactory("message");
		return $service->deleteMessages($userId, $relationIds);
	}
	/**
	 * ����һ����ϵ��
	 * @param array $fieldData
	 * @param $relationId
	 * @return int
	 */
	function updateMessage($fieldData, $userId, $relationId) {
		$service = $this->_serviceFactory("message");
		return $service->updateMessage($fieldData, $userId, $relationId);
	}
	/**
	 * ���һ����ϵ��
	 * @param $relationId
	 * @return bool
	 */
	function markMessage($userId, $relationId) {
		$service = $this->_serviceFactory("message");
		return $service->markMessage($userId, $relationId);
	}
	/**
	 * ��Ƕ�����ϵ��
	 * @param array $relationIds
	 * @return bool
	 */
	function markMessages($userId, $relationIds) {
		$service = $this->_serviceFactory("message");
		return $service->markMessages($userId, $relationIds);
	}
	/**
	 * �����ϢID��ȡ��Ϣ����
	 * @param $messageId
	 * @return unknown_type
	 */
	function getMessage($messageId) {
		$service = $this->_serviceFactory("message");
		return $service->getMessage($messageId);
	}
	/**
	 * �����ϢID���¹�ϵ״̬Ϊ�Ѷ�
	 * @param $userId
	 * @param $messageId
	 * @return unknown_type
	 */
	function readMessages($userId, $messageId) {
		$service = $this->_serviceFactory("message");
		return $service->readMessages($userId, $messageId);
	}
	/**
	 * ���ݴ��������Ϣ
	 * @param $categorys ����/����keys���� �� array('groupsms','sms','notice');
	 * @return unknown_type
	 */
	function clearMessages($userId, $categorys) {
		$service = $this->_serviceFactory("message");
		return $service->clearMessages($userId, $categorys);
	}
	/**
	 * ͳ���û�������Ϣ��
	 * @param $userId
	 * @return unknown_type
	 */
	function statisticsMessage($userId) {
		$service = $this->_serviceFactory("message");
		return $service->statisticsMessage($userId);
	}
	/**
	 * ��ȡ������Ϣ
	 * @param $userId
	 * @param $page
	 * @param $perpage
	 * @return array('����','��Ϣ����')
	 */
	function interactiveMessages($userId, $page, $perpage) {
		$service = $this->_serviceFactory("message");
		return $service->interactiveMessages($userId, $page, $perpage);
	}
	/**
	 * ����û�������ϢȨ��
	 * @param $category
	 * @param $number
	 * @return unknown_type
	 */
	function checkUserMessageLevle($category, $number) {
		$service = $this->_serviceFactory("message");
		return $service->checkUserLevle($category, $number);
	}
	/**
	 * �������ߵ���Ϣ
	 * @param $usernames
	 * @return unknown_type
	 */
	function checkReceiver($usernames) {
		$service = $this->_serviceFactory("message");
		return $service->checkReceiver($usernames);
	}
	/**
	 * ��駹�ϵ��ɾ����ϵ
	 * @param $relationIds
	 * @return unknown_type
	 */
	function deleteRelationsByRelationIds($relationIds) {
		$service = $this->_serviceFactory("message");
		return $service->deleteRelationsByRelationIds($relationIds);
	}
	/**
	 * ��ȡ����ĩ����Ϣ�б�
	 * @param $userId
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function getAllNotRead($userId, $page, $perpage) {
		$service = $this->_serviceFactory("message");
		return $service->getAllNotRead($userId, $page, $perpage);
	}
	/**
	 * ����ͳ���û���Ϣ��
	 * @param array $userIds
	 * @return unknown_type
	 */
	function statisticUsersNumbers($userIds) {
		$service = $this->_serviceFactory("message");
		return $service->countUserNumbers($userIds);
	}
	/**
	 * ����û����͹�ϵID��ȡ��ϵ
	 * @param $userId
	 * @param $relationId
	 * @return unknown_type
	 */
	function getRelation($userId, $relationId) {
		$service = $this->_serviceFactory("message");
		return $service->getRelation($userId, $relationId);
	}
	
	function getInBox($userId, $page, $perpage){
		$service = $this->_serviceFactory("message");
		return $service->getInBox($userId, $page, $perpage);
	}
	
	function countInBox($userId){
		$service = $this->_serviceFactory("message");
		return $service->countInBox($userId);
	}
	
	function getOutBox($userId, $page, $perpage){
		$service = $this->_serviceFactory("message");
		return $service->getOutBox($userId, $page, $perpage);
	}
	
	function countOutBox($userId){
		$service = $this->_serviceFactory("message");
		return $service->countOutBox($userId);
	}
	
	function getMessageByTypeIdWithBoxName($userId, $typeId, $page, $perpage, $boxName = 'outbox'){
		$service = $this->_serviceFactory("message");
		return $service->getMessageByTypeIdWithBoxName($userId, $typeId, $page, $perpage, $boxName);
	}
	
	function countMessageByTypeIdWithBoxName($userId, $typeId, $boxName = 'outbox'){
		$service = $this->_serviceFactory("message");
		return $service->countMessageByTypeIdWithBoxName($userId, $typeId, $boxName);
	}
	
	/**************************************************************/
	/**
	 * ����һ��֪ͨ
	 * @param $userId
	 * @param array $usernames
	 * @param array $messageinfo array('create_uid','create_username','title','content','expand') ��Ϣ������
	 * @param int $typeId
	 * @return $messageId �������ӳɹ�����ϢID
	 */
	function sendNotice($userId, $usernames, $messageInfo, $typeId = null) {
		$service = $this->_serviceFactory("notice");
		return $service->sendNotice($userId, $usernames, $messageInfo, $typeId);
	}
	/**
	 * ��ȡĳ���û�������֪ͨ
	 * @param $userId
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function getAllNotices($userId, $page, $perpage) {
		$service = $this->_serviceFactory("notice");
		return $service->getAllNotices($userId, $page, $perpage);
	}
	/**
	 * ��ȡĳ���û�������ĩ��֪ͨ
	 * @param $userId
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function getNoticesNotRead($userId, $page, $perpage) {
		$service = $this->_serviceFactory("notice");
		return $service->getNoticesNotRead($userId, $page, $perpage);
	}
	/**
	 * ��ȡĳ���û���ĳ����֪ͨ
	 * @param $userId
	 * @param $typeId
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function getNotices($userId, $typeId, $page, $perpage) {
		$service = $this->_serviceFactory("notice");
		return $service->getNotices($userId, $typeId, $page, $perpage);
	}
	/**
	 * ��ȡ��һ��֪ͨ
	 * @param $userId
	 * @param $relationId
	 * @return unknown_type
	 */
	function getUpNotice($userId, $relationId, $typeId) {
		$service = $this->_serviceFactory("notice");
		return $service->getUpNotice($userId, $relationId, $typeId);
	}
	/**
	 * ��ȡ��һ��֪ͨ
	 * @param $userId
	 * @param $relationId
	 * @return unknown_type
	 */
	function getDownNotice($userId, $relationId, $typeId) {
		$service = $this->_serviceFactory("notice");
		return $service->getDownNotice($userId, $relationId, $typeId);
	}
	/**
	 * ͳ������֪ͨ
	 * @param $userId
	 * @return unknown_type
	 */
	function countAllNotice($userId) {
		$service = $this->_serviceFactory("notice");
		return $service->countAllNotice($userId);
	}
	/**
	 * ͳ������ĩ��֪ͨ
	 * @param $userId
	 * @return unknown_type
	 */
	function countNoticesNotRead($userId) {
		$service = $this->_serviceFactory("notice");
		return $service->countNoticesNotRead($userId);
	}
	/**
	 * ͳ��ĳ����֪ͨ
	 * @param $userId
	 * @param $typeId
	 * @return unknown_type
	 */
	function countNotice($userId, $typeId) {
		$service = $this->_serviceFactory("notice");
		return $service->countNotice($userId, $typeId);
	}
	/**
	 * ɾ��һ��֪ͨ
	 * @param $userId
	 * @param $relationId
	 * @return unknown_type
	 */
	function deleteNotice($userId, $relationId) {
		$service = $this->_serviceFactory("notice");
		return $service->deleteNotice($userId, $relationId);
	}
	/**
	 * ɾ������֪ͨ
	 * @param $userId
	 * @param $relationIds
	 * @return unknown_type
	 */
	function deleteNotices($userId, $relationIds) {
		$service = $this->_serviceFactory("notice");
		return $service->deleteNotices($userId, $relationIds);
	}
	/**
	 * ����һ��֪ͨ
	 * @param $fieldData
	 * @param $userId
	 * @param $relationId
	 * @return unknown_type
	 */
	function updateNotice($fieldData, $userId, $relationId) {
		$service = $this->_serviceFactory("notice");
		return $service->updateNotice($fieldData, $userId, $relationId);
	}
	/**
	 * ���һ��֪ͨ�Ѷ�
	 * @param $userId
	 * @param $relationId
	 * @return unknown_type
	 */
	function markNotice($userId, $relationId) {
		$service = $this->_serviceFactory("notice");
		return $service->markNotice($userId, $relationId);
	}
	/**
	 * ��Ƕ���֪ͨ�Ѷ�
	 * @param $userId
	 * @param $relationIds
	 * @return unknown_type
	 */
	function markNotices($userId, $relationIds) {
		$service = $this->_serviceFactory("notice");
		return $service->markNotices($userId, $relationIds);
	}
	/**
	 * ��ȡһ��֪ͨ
	 * @param $messageId
	 * @return unknown_type
	 */
	function getNotice($messageId) {
		$service = $this->_serviceFactory("notice");
		return $service->getNotice($messageId);
	}
	/**
	 * ��ȡһ��֪ͨ
	 * @param $userId
	 * @param $messageId
	 * @return unknown_type
	 */
	function readNotices($userId, $messageId) {
		$service = $this->_serviceFactory("notice");
		return $service->readNotices($userId, $messageId);
	}
	/**************************************************************/
	/**
	 * ������Ϣ����
	 * @param array $fieldDatas ��ά���� array(array('uid','aid','mid','status')......);
	 * @return last_insert_id
	 */
	function sendAttachs($fieldDatas) {
		$service = $this->_serviceFactory("attach");
		return $service->addAttachs($fieldDatas);
	}
	/**
	 * չʾ��Ϣ����
	 * @param $userId
	 * @param $messageId
	 * @return array
	 */
	function showAttachs($userId, $messageId) {
		$service = $this->_serviceFactory("attach");
		return $service->getAttachs($userId, $messageId);
	}
	/**
	 * �Ƴ���Ϣ����
	 * @param $userId
	 * @param $id
	 * @return bool
	 */
	function removeAttach($userId, $id) {
		$service = $this->_serviceFactory("attach");
		return $service->removeAttach($userId, $id);
	}
	/**
	 * ��ȡ������Ϣ����
	 * @return unknown_type
	 */
	function getAllAttachs($page, $perpage) {
		$service = $this->_serviceFactory("attach");
		return $service->getAllAttachs($page, $perpage);
	}
	/**
	 * ͳ��������Ϣ����
	 * @return unknown_type
	 */
	function countAllAttachs() {
		$service = $this->_serviceFactory("attach");
		return $service->countAllAttachs();
	}
	/**
	 * �����Ϣ����ɾ������
	 * @param $messageIds
	 * @return unknown_type
	 */
	function deleteAttachsByMessageIds($messageIds) {
		$service = $this->_serviceFactory("search");
		return $service->deleteAttachsByMessageIds($messageIds);
	}
	/*************************************************/
	/**
	 * ��������
	 * @param int $userId
	 * @param array $usernames
	 * @param array array('create_uid','create_username','title','content','expand') ��Ϣ������
	 * @param int $typeId
	 * @return $messageId �������ӳɹ�����ϢID
	 */
	function sendRequest($userId, $usernames, $messageInfo, $typeId) {
		$service = $this->_serviceFactory("request");
		return $service->sendRequest($userId, $usernames, $messageInfo, $typeId);
	}
	/**
	 * ��ȡ��������
	 * @param int $userId
	 * @param int $page
	 * @param int $perpage
	 * @return array
	 */
	function getAllRequests($userId, $page, $perpage) {
		$service = $this->_serviceFactory("request");
		return $service->getAllRequests($userId, $page, $perpage);
	}
	/**
	 * ��ȡ����ĩ������
	 * @param int $userId
	 * @param int $page
	 * @param int $perpage
	 * @return array 
	 */
	function getRequestsNotRead($userId, $page, $perpage) {
		$service = $this->_serviceFactory("request");
		return $service->getRequestsNotRead($userId, $page, $perpage);
	}
	/**
	 * ������ͻ�ȡ����
	 * @param int $userId
	 * @param int $typeId
	 * @param int $page
	 * @param int $perpage
	 * @return array 
	 */
	function getRequests($userId, $typeId, $page, $perpage) {
		$service = $this->_serviceFactory("request");
		return $service->getRequests($userId, $typeId, $page, $perpage);
	}
	/**
	 * ��ȡ��һ������
	 * @param int $userId
	 * @param int $relationId
	 * @param int $typeId
	 * @return array 
	 */
	function getUpRequest($userId, $relationId, $typeId) {
		$service = $this->_serviceFactory("request");
		return $service->getUpRequest($userId, $relationId, $typeId);
	}
	/**
	 * ��ȡ��һ������
	 * @param int $userId
	 * @param int $relationId
	 * @param int $typeId
	 * @return array 
	 */
	function getDownRequest($userId, $relationId, $typeId) {
		$service = $this->_serviceFactory("request");
		return $service->getDownRequest($userId, $relationId, $typeId);
	}
	/**
	 * ͳ����������
	 * @param int $userId
	 * @return int
	 */
	function countAllRequest($userId) {
		$service = $this->_serviceFactory("request");
		return $service->countAllRequest($userId);
	}
	/**
	 * ͳ��ĩ������
	 * @param int $userId
	 * @return int
	 */
	function countRequestsNotRead($userId) {
		$service = $this->_serviceFactory("request");
		return $service->countRequestsNotRead($userId);
	}
	/**
	 * ͳ��ĳ������
	 * @param int $userId
	 * @param int $typeId
	 * @return int
	 */
	function countRequest($userId, $typeId) {
		$service = $this->_serviceFactory("request");
		return $service->countRequest($userId, $typeId);
	}
	/**
	 * ɾ��һ������
	 * @param int $userId
	 * @param int $relationId
	 * @return int
	 */
	function deleteRequest($userId, $relationId) {
		$service = $this->_serviceFactory("request");
		return $service->deleteRequest($userId, $relationId);
	}
	/**
	 * ɾ����������
	 * @param int $userId
	 * @param int $relationIds
	 * @return int
	 */
	function deleteRequests($userId, $relationIds) {
		$service = $this->_serviceFactory("request");
		return $service->deleteRequests($userId, $relationIds);
	}
	/**
	 * ����һ������
	 * @param array $fieldData
	 * @param int $userId
	 * @param int $relationId
	 * @return int
	 */
	function updateRequest($fieldData, $userId, $relationId) {
		$service = $this->_serviceFactory("request");
		return $service->updateRequest($fieldData, $userId, $relationId);
	}
	/**
	 * ���һ������
	 * @param int $userId
	 * @param int $relationId
	 * @return bool
	 */
	function markRequest($userId, $relationId) {
		$service = $this->_serviceFactory("request");
		return $service->markRequest($userId, $relationId);
	}
	/**
	 * ��Ƕ�������
	 * @param int $userId
	 * @param int $relationIds
	 * @return bool
	 */
	function markRequests($userId, $relationIds) {
		$service = $this->_serviceFactory("request");
		return $service->markRequests($userId, $relationIds);
	}
	/**
	 * ��ȡһ����Ϣ��
	 * @param $messageId
	 * @return bool
	 */
	function getRequest($messageId) {
		$service = $this->_serviceFactory("request");
		return $service->getRequest($messageId);
	}
	/**
	 * ��ȡһ����Ϣ
	 * @param $userId
	 * @param $messageId
	 * @return array 
	 */
	function readRequests($userId, $messageId) {
		$service = $this->_serviceFactory("request");
		return $service->readRequests($userId, $messageId);
	}
	/**
	 * ��������
	 * @param int $userId
	 * @param array $relationIds
	 * @return bool
	 */
	function overlookRequests($userId, $relationIds) {
		$service = $this->_serviceFactory("request");
		return $service->overlookRequest($userId, $relationIds);
	}
	/**
	 * ͬ������
	 * @param $userId
	 * @param $relationIds
	 * @return unknown_type
	 */
	function agreeRequests($userId, $relationIds) {
		$service = $this->_serviceFactory("request");
		return $service->agreeRequests($userId, $relationIds);
	}
	/*************************************************/
	/**
	 * ����Ⱥ��/�û�����Ϣ
	 * @param int $userId
	 * @param int $groupId Ⱥ��/�û���ID
	 * @param array array('create_uid','create_username','title','content','expand') ��Ϣ������
	 * @param int $type  colony/usergroup
	 * @param array $userNames ָ�������û�`
	 * @return $messageId �������ӳɹ�����ϢID
	 */
	function sendGroupMessage($userId, $groupId, $messageInfo, $type = null, $userNames = array()) {
		$service = $this->_serviceFactory("groupsms");
		return $service->sendGroupMessage($userId, $groupId, $messageInfo, $type, $userNames);
	}
	/**
	 * ��ȡ����Ⱥ��/���˶Ի���Ϣ
	 * @param int $userId
	 * @param int $page
	 * @param int $perpage
	 * @return array
	 */
	function getAllGroupMessages($userId, $page, $perpage) {
		$service = $this->_serviceFactory("groupsms");
		return $service->getAllGroupMessages($userId, $page, $perpage);
	}
	/**
	 * ����ĩ��Ⱥ��/���˶Ի���Ϣ
	 * @param $userId
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function getGroupMessagesNotRead($userId, $page, $perpage) {
		$service = $this->_serviceFactory("groupsms");
		return $service->getGroupMessagesNotRead($userId, $page, $perpage);
	}
	/**
	 * ��ȡȺ����Ϣ
	 * @param $userId
	 * @param $typeId
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function getGroupMessages($userId, $typeId, $page, $perpage) {
		$service = $this->_serviceFactory("groupsms");
		return $service->getGroupMessages($userId, $typeId, $page, $perpage);
	}
	/**
	 * ��ȡȺ��/���˶Ի�
	 * @param $userId
	 * @param $messageId
	 * @param $relationId
	 * @return unknown_type
	 */
	function getGroupReplies($userId, $messageId, $relationId) {
		$service = $this->_serviceFactory("groupsms");
		return $service->getGroupReplies($userId, $messageId, $relationId);
	}
	/**
	 * ��ȡȺ��/������һ��
	 * @param $userId
	 * @param $relationId
	 * @param $typeId
	 * @return unknown_type
	 */
	function getGroupUpMessage($userId, $relationId, $typeId = null) {
		$service = $this->_serviceFactory("groupsms");
		return $service->getGroupUpMessage($userId, $relationId, $typeId);
	}
	/**
	 * ��ȡȺ��/������һ��
	 * @param $userId
	 * @param $relationId
	 * @param $typeId
	 * @return unknown_type
	 */
	function getGroupDownMessage($userId, $relationId, $typeId = null) {
		$service = $this->_serviceFactory("groupsms");
		return $service->getGroupDownMessage($userId, $relationId, $typeId);
	}
	/**
	 * ��ȡ�ҷ��͵�Ⱥ��/������Ϣ
	 * @param $userId
	 * @param $typeId
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function getGroupMessagesBySelf($userId, $page, $perpage, $typeId = null) {
		$service = $this->_serviceFactory("groupsms");
		return $service->getGroupMessagesBySelf($userId, $typeId, $page, $perpage);
	}
	/**
	 * ��ȡ���յ���Ⱥ��/������Ϣ
	 * @param $userId
	 * @param $typeId
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function getGroupMessagesByOther($userId, $page, $perpage, $typeId = null) {
		$service = $this->_serviceFactory("groupsms");
		return $service->getGroupMessagesByOther($userId, $typeId, $page, $perpage);
	}
	/**
	 * ͳ������Ⱥ��/������Ϣ
	 * @param $userId
	 * @return unknown_type
	 */
	function countAllGroupMessage($userId) {
		$service = $this->_serviceFactory("groupsms");
		return $service->countAllGroupMessage($userId);
	}
	/**
	 * ͳ��Ⱥ��/����ĩ����Ϣ
	 * @param $userId
	 * @return unknown_type
	 */
	function countGroupMessagesNotRead($userId) {
		$service = $this->_serviceFactory("groupsms");
		return $service->countGroupMessagesNotRead($userId);
	}
	/**
	 * ͳ��Ⱥ��/�����ҷ��͵���Ϣ
	 * @param $userId
	 * @param $typeId
	 * @return unknown_type
	 */
	function countGroupMessagesBySelf($userId, $typeId = null) {
		$service = $this->_serviceFactory("groupsms");
		return $service->countGroupMessagesBySelf($userId, $typeId);
	}
	/**
	 * ͳ��Ⱥ��/�����ҽ��յ���Ϣ
	 * @param $userId
	 * @param $typeId
	 * @return unknown_type
	 */
	function countGroupMessagesByOther($userId, $typeId = null) {
		$service = $this->_serviceFactory("groupsms");
		return $service->countGroupMessagesByOther($userId, $typeId);
	}
	/**
	 * ͳ��Ⱥ��/������Ϣ
	 * @param $userId
	 * @param $typeId
	 * @return unknown_type
	 */
	function countGroupMessage($userId, $typeId) {
		$service = $this->_serviceFactory("groupsms");
		return $service->countGroupMessage($userId, $typeId);
	}
	/**
	 * ɾ��һ��Ⱥ��/������Ϣ
	 * @param $userId
	 * @param $relationId
	 * @return unknown_type
	 */
	function deleteGroupMessage($userId, $relationId) {
		$service = $this->_serviceFactory("groupsms");
		return $service->deleteGroupMessage($userId, $relationId);
	}
	/**
	 * ɾ������Ⱥ��/������Ϣ
	 * @param $userId
	 * @param $relationIds
	 * @return unknown_type
	 */
	function deleteGroupMessages($userId, $relationIds) {
		$service = $this->_serviceFactory("groupsms");
		return $service->deleteGroupMessages($userId, $relationIds);
	}
	/**
	 * ����һ��Ⱥ��/������Ϣ
	 * @param $fieldData
	 * @param $userId
	 * @param $relationId
	 * @return unknown_type
	 */
	function updateGroupMessage($fieldData, $userId, $relationId) {
		$service = $this->_serviceFactory("groupsms");
		return $service->updateGroupMessage($fieldData, $userId, $relationId);
	}
	/**
	 * ���Ⱥ��/������Ϣ
	 * @param $userId
	 * @param $relationId
	 * @return unknown_type
	 */
	function markGroupMessage($userId, $relationId) {
		$service = $this->_serviceFactory("groupsms");
		return $service->markGroupMessage($userId, $relationId);
	}
	/**
	 * ��Ƕ���Ⱥ��/������Ϣ
	 * @param $userId
	 * @param $relationIds
	 * @return unknown_type
	 */
	function markGroupMessages($userId, $relationIds) {
		$service = $this->_serviceFactory("groupsms");
		return $service->markGroupMessages($userId, $relationIds);
	}
	/**
	 * ��ȡһ��Ⱥ��/������Ϣ
	 * @param $messageId
	 * @return unknown_type
	 */
	function getGroupMessage($messageId) {
		$service = $this->_serviceFactory("groupsms");
		return $service->getGroupMessage($messageId);
	}
	/**
	 * ��ȡһ��Ⱥ��/������Ϣ
	 * @param $userId
	 * @param $messageId
	 * @return unknown_type
	 */
	function readGroupMessages($userId, $messageId) {
		$service = $this->_serviceFactory("groupsms");
		return $service->readGroupMessages($userId, $messageId);
	}
	/**
	 * ���ζ�����Ϣ
	 * @param $userId
	 * @param $relationId
	 * @param $messageId
	 * @return unknown_type
	 */
	function shieldGroupMessage($userId, $relationId, $messageId) {
		$service = $this->_serviceFactory("groupsms");
		return $service->shieldGroupMessage($userId, $relationId, $messageId);
	}
	/**
	 * �ָ�������Ϣ
	 * @param $userId
	 * @param $relationId
	 * @param $messageId
	 * @return unknown_type
	 */
	function recoverGroupMessage($userId, $relationId, $messageId) {
		$service = $this->_serviceFactory("groupsms");
		return $service->recoverGroupMessage($userId, $relationId, $messageId);
	}
	/**
	 * ����Ⱥ����Ϣ
	 * @param $userId
	 * @param $groupId
	 * @param $messageId
	 * @return unknown_type
	 */
	function openGroupMessage($userId, $groupId, $messageId) {
		$service = $this->_serviceFactory("groupsms");
		return $service->openGroupMessage($userId, $groupId, $messageId);
	}
	/**
	 * �ر�Ⱥ����Ϣ
	 * @param $userId
	 * @param $groupId
	 * @param $messageId
	 * @return unknown_type
	 */
	function closeGroupMessage($userId, $groupId, $messageId) {
		$service = $this->_serviceFactory("groupsms");
		return $service->closeGroupMessage($userId, $groupId, $messageId);
	}
	/*************************************************/
	/**
	 * ����������ƻ�ȡ����ֵ
	 * @param $typeName
	 * @return unknown_type
	 */
	function getConst($typeName) {
		$service = $this->_serviceFactory("default");
		return $service->getConst($typeName);
	}
	/**
	 * �������ID��ȡ��������
	 * @return unknown_type
	 */
	function getReverseConst($id) {
		$service = $this->_serviceFactory("default");
		return $service->getReverseConst($id);
	}
	/**
	 * ��ȡ�û�����Ⱥ������
	 * @param $userId
	 * @return array ���ε�Ⱥ��ID����
	 */
	function getBlackColony($userId) {
		$service = $this->_serviceFactory("default");
		return $service->getBlackColony($userId);
	}
	/**
	 * ������Ϣ��������
	 * @param $userId
	 * @param array $fieldData
	 * @return unknown_type
	 */
	function setMsConfig($fieldData, $userId) {
		$service = $this->_serviceFactory("default");
		return $service->setMsConfig($fieldData, $userId);
	}
	/**
	 * ��ȡ��Ϣ��������
	 * @param $userId
	 * @param $mKey
	 * @return unknown_type
	 */
	function getMsConfig($userId, $mKey) {
		$service = $this->_serviceFactory("default");
		return $service->getMsConfig($userId, $mKey);
	}
	/**
	 * ��駼�����ȡ��ֵ
	 * @return unknown_type
	 */
	function getMsKey($key) {
		$service = $this->_serviceFactory("default");
		return $service->getMsKey($key);
	}
	/**
	 * ����û�����ȡ�����û�����
	 * @param $userId
	 * @return unknown_type
	 */
	function getMsConfigs($userId) {
		$service = $this->_serviceFactory("default");
		return $service->getMsConfigs($userId);
	}
	/**
	 * ��ȡĬ��������Ϣ
	 * @param array $app_array
	 * @return unknown_type
	 */
	function getDefaultShields($app_array = array()) {
		$service = $this->_serviceFactory("default");
		return $service->setDefaultShield($app_array);
	}
	/**
	 * ��ȡĳ���û���������Ϣ
	 * @param $userId
	 * @param $key
	 * @param array $app_array
	 * @return unknown_type
	 */
	function getMessageShield($userId, $key, $app_array = array()) {
		$service = $this->_serviceFactory("default");
		return $service->getMessageShield($userId, $key, $app_array);
	}
	/**
	 * ����û�����ȡĳ���û���������Ϣ 
	 * @param $userName
	 * @param $key
	 * @param $app_array
	 * @return unknown_type
	 */
	function getMessageShieldByUserName($userName, $key, $app_array = array()) {
		$service = $this->_serviceFactory("default");
		return $service->getMessageShieldByUserName($userName, $key, $app_array);
	}
	/****************************************************/
	/**
	 * ��駺�������������������Ϣ
	 * @param int $userId  ˭���� 
	 * @param string $userName  ����˭
	 * @param int $type
	 * @param int $page
	 * @param int $perpage
	 * @return array
	 */
	function searchMessages($userId, $userName, $type = null, $page, $perpage) {
		$service = $this->_serviceFactory("search");
		return $service->searchMessages($userId, $userName, $type, $page, $perpage);
	}
	/**
	 * TODO ��̨��Ϣ����ӿ� ɾ��
	 * @param $keyWords  �ؼ���
	 * @param $startTime ��ʼʱ��
	 * @param $endTime   ����ʱ��
	 * @param $sender    ������
	 * @param $isDelete  �Ƿ�ֱ��ɾ��
	 * @param $page      ҳ��
	 * @param $perpage   ÿҳ��
	 * @return array(��ҳ��,��Ϣ����)
	 */
	function manageMessage($keyWords = null, $startTime = null, $endTime = null, $sender = null, $isDelete = 0, $page = 1, $perpage = 30) {
		$service = $this->_serviceFactory("search");
		return $service->manageMessage($keyWords, $startTime, $endTime, $sender, $isDelete, $page, $perpage);
	}
	/**
	 * TODO ��̨��Ϣ����ӿ� ɾ��
	 * @param $category ��������
	 * @param $unRead   �Ƿ���ĩ��
	 * @param $isDelete �Ƿ�ֱ��ɾ��
	 * @return array(��ҳ��,��Ϣ����)
	 */
	function manageMessageWithCategory($category, $unRead = 0, $isDelete = 0, $page = 1, $perpage = 30) {
		$service = $this->_serviceFactory("search");
		return $service->manageMessageWithCategory($category, $unRead, $isDelete, $page, $perpage);
	}
	/**
	 * TODO ��̨��Ϣ����ӿ� ɾ��
	 * @param $messageIds
	 * @return unknown_type
	 */
	function manageMessageWithMessageIds($messageIds) {
		$service = $this->_serviceFactory("search");
		return $service->manageMessageWithMessageIds($messageIds);
	}
	/*******************************************/
	/**
	 * ����û�ID��ȡ��ʷ��Ϣ
	 * @param $userId
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function getHistoryMessages($userId, $page, $perpage) {
		$service = $this->_serviceFactory("history");
		return $service->getHistoryMessages($userId, $page, $perpage);
	}
	/**
	 * ����û�IDͳ����ʷ��Ϣ
	 * @param $userId
	 * @return unknown_type
	 */
	function countHistoryMessage($userId) {
		$service = $this->_serviceFactory("history");
		return $service->countHistoryMessage($userId);
	}
	/**
	 * ����ɾ����ʷ����Ϣ
	 * @param int $userId
	 * @param array $relationIds
	 * @return unknown_type
	 */
	function deleteHistoryMessages($userId, $relationIds) {
		$service = $this->_serviceFactory("history");
		return $service->deleteHistoryMessages($userId, $relationIds);
	}
	/**
	 * ��ȡһ����ʷ��Ϣ��ϸ��Ϣ
	 * @param $messageId
	 * @return unknown_type
	 */
	function getHistoryMessage($messageId) {
		$service = $this->_serviceFactory("history");
		return $service->getHistoryMessage($messageId);
	}
	/**
	 * ��ȡ�ظ���ʷ��Ϣ
	 * @param $userId
	 * @param $messageId
	 * @param $relationId
	 * @return unknown_type
	 */
	function getHistoryReplies($userId, $messageId, $relationId) {
		$service = $this->_serviceFactory("history");
		return $service->getHistoryReplies($userId, $messageId, $relationId);
	}
	/**
	 * ����Ϣת��Ϊ��ʷʱ���
	 * @param $timeSegment ʱ���  unix ʱ���
	 * @return unknown_type
	 */
	function setHistorys($timeSegment) {
		$service = $this->_serviceFactory("history");
		return $service->setHistorys($timeSegment);
	}
	/**
	 * �����û�ͳ����
	 * @param array $userIds
	 * @param string $mKey
	 * @param $mValue
	 * @return unknown_type
	 */
	function resetStatistics($userIds, $mKey) {
		$service = $this->_serviceFactory("default");
		return $service->resetStatistics($userIds, $mKey);
	}
	/**
	 * ����û�ID��ȡ�û�ͳ����Ϣ
	 * @param $userId
	 * @return array('վ������','֪ͨ��','������','Ⱥ��Ϣ��')
	 */
	function getUserStatistics($userId) {
		$service = $this->_serviceFactory("default");
		return $service->getUserStatistics($userId);
	}
	/**
	 * ����û�ID��ȡ�û�������Ϣ
	 * @param $userId
	 * @return unknown_type
	 */
	function getUserSpecialStatistics($userId) {
		$service = $this->_serviceFactory("default");
		return $service->getUserSpecialStatistics($userId);
	}
	/*****************************************************/
	/**
	 * ץȡ�û�����Ϣ ϵͳ֪ͨ
	 * @param int $userId
	 * @param array $groupIds
	 * @param int $lastgrab
	 * @return unknown_type
	 */
	function grabMessage($userId, $groupIds, $lastgrab) {
		$service = $this->_serviceFactory("notice");
		return $service->grabMessage($userId, $groupIds, $lastgrab);
	}
	/**
	 * ������Ϣ���� �û�����Ϣ
	 * @param array $groupIds   �û�������
	 * @param array $messageinfo array('create_uid','create_username','title','content','expand') ��Ϣ������
	 * @return bool
	 */
	function createMessageTasks($groupIds, $messageInfo) {
		$service = $this->_serviceFactory("notice");
		return $service->createMessageTasks($groupIds, $messageInfo);
	}
	/**
	 * �������û�������Ϣ����
	 * @param array $onlineUserIds �����û�
	 * @param array $messageinfo array('create_uid','create_username','title','content','expand') ��Ϣ������
	 * @return unknown_type
	 */
	function sendOnlineMessages($onlineUserIds, $messageInfo) {
		$service = $this->_serviceFactory("notice");
		return $service->sendOnlineMessages($onlineUserIds, $messageInfo);
	}
	
	function sendTaskMessages($userIds, $messageInfo, $messageId = null) {
		$service = $this->_serviceFactory("task");
		return $service->sendTaskMessages($userIds, $messageInfo, $messageId);
	}
	/**
	 * ˽�м�����Ϣ���ķ������
	 * @param $name
	 * @return unknown_type
	 */
	function _serviceFactory($name) {
		static $classes = array();
		$name = strtolower($name);
		$filename = R_P . "lib/message/message/" . $name . ".ms.php";
		if (!is_file($filename)) {
			return null;
		}
		$class = 'MS_' . ucfirst($name);
		if (isset($classes[$class])) {
			return $classes[$class];
		}
		if (!class_exists('MS_BASE')) require (R_P . 'lib/message/message/base.ms.php');
		if (!class_exists($class)) include S::escapePath($filename);
		$classes[$class] = new $class();
		return $classes[$class];
	}
	
	function matchTidByConetnt($content) {
		if (strrpos($content,'read.php?tid=') === false) return false;
		preg_match_all('/read.php\?tid=(.*)\]/U', $content, $matches);
		$tid = intval($matches[1][0]);
		return $tid ? $tid : false;
	}
	
	function countAllByUserId($userId) {
		$userId = intval($userId);
		if ($userId < 1) return array();
		$relationsDb = L::loadDB('ms_relations', 'message');
		foreach ($relationsDb->countAllByUserId($userId) as $k=>$v) {
			$result[$k] = $v['total'];
		}
		return $result;
	}
}


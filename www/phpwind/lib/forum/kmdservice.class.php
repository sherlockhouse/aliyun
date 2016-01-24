<?php
!defined('P_W') && exit('Forbidden');

define('KMD_THREAD_STATUS_EMPTY',1);//δʹ��
define('KMD_THREAD_STATUS_CHECK',2);//�����
define('KMD_THREAD_STATUS_REJECT',3);//�Ѿܾ�
define('KMD_THREAD_STATUS_OK',4);//�ƹ���

define('KMD_PAY_STATUS_NOTPAY',1);//δ֧��
define('KMD_PAY_STATUS_PAYED',2);//��֧��
define('KMD_PAY_STATUS_INVALID',3);//��Ч

define('KMD_PAY_TYPE_ALIPAY', 1);
define('KMD_PAY_TYPE_BANK', 2);
define('KMD_PAY_TYPE_CASH', 3);

class PW_KmdService {

	var $threadStatus;
	var $payTypes;
	var $payStatus;
	
	function PW_KmdService(){
		$this->_initThreadStatus();
		$this->_initPayTypes();
		$this->_initPayStatus();
	}
	
	function _initThreadStatus(){
		$this->threadStatus = array(
			KMD_THREAD_STATUS_EMPTY => 'δʹ��',
			KMD_THREAD_STATUS_CHECK => '�����',
			KMD_THREAD_STATUS_REJECT => '�Ѿܾ�',
			KMD_THREAD_STATUS_OK => '�ƹ���'
		);
	}
	
	function _initPayTypes(){
		$this->payTypes = array(
			KMD_PAY_TYPE_ALIPAY => '֧����',
			KMD_PAY_TYPE_BANK => '���л��',
			KMD_PAY_TYPE_CASH => '�ֽ�'
		);
	}
	
	function _initPayStatus(){
		$this->payStatus = array(
			KMD_PAY_STATUS_NOTPAY => 'δ֧��',
			KMD_PAY_STATUS_PAYED => '��֧��',
			KMD_PAY_STATUS_INVALID => '��Ч'
		);
	}
	
	function updateKmdThread($kid){
		$kmdInfo = $this->getKmdInfoByKid($kid);
		if (!S::isArray($kmdInfo)) return false;
		if ($kmdInfo['tid']) {
			$endtime = $kmdInfo['status'] == KMD_THREAD_STATUS_OK ? $kmdInfo['endtime'] : 0;
			$this->updateKmdThreadByTid($kmdInfo['tid'], $endtime);
		}
		return true;
	}
	
	function updateKmdThreadByTid($tid,$endtime){
		$tid = intval($tid);
		$endtime = intval($endtime);
		$threadsDb = $this->_getThreadsDB();
		$threadInfo = $threadsDb->get($tid);
		if (!S::isArray($threadInfo)) return false;
		//toolfield
		list($t1,$t2) = explode(',', $threadInfo['toolfield']);
		$specialSort = $endtime > 0 ? PW_THREADSPECIALSORT_KMD : 0;
		$updateData = array(
			'toolfield' => implode(',', array($t1,$t2,$endtime)),
			'specialsort' => $specialSort
		);
		$threadsDb->update($updateData,$tid);
	}
	
/** info start **/
	
	/**
	 * ��ӿ�����
	 * @param array $fieldData
	 * @return mixed
	 */
	function addKmdInfo($fieldData) {
		if (!S::isArray($fieldData)) return false;
		$kmdInfoDb = $this->_getKmdInfoDB();
		$fid = intval($fieldData['fid']);
		if ($fid < 1) return false;
		$unusedKmdInfo = $this->getUnusedKmdInfoByFid($fid);
		if ($unusedKmdInfo) return $kmdInfoDb->updateKmdInfo($fieldData, $unusedKmdInfo['kid']);
		return $kmdInfoDb->addKmdInfo($fieldData);
	}
	
	/**
	 * ����idɾ��������
	 * @param int $kid
	 * @return bool
	 */
	function deleteKmdInfoByKid($kid) {
		$kid = intval($kid);
		if ($kid < 1) return false;
		$kmdInfoDb = $this->_getKmdInfoDB();
		return $kmdInfoDb->deleteKmdInfoByKid($kid);
	}
	
	/**
	 * ����id�ջؿ�����
	 * @param int $kid
	 * @return bool
	 */
	function initKmdInfoByKid($kid) {
		$kid = intval($kid);
		if ($kid < 1) return false;
		$kmdInfo = $this->getKmdInfoByKid($kid);
		if (!$kmdInfo) return false;
		//���¿����������
		$kmdInfo['tid'] && $this->updateKmdThreadByTid($kmdInfo['tid'], 0);
		$data = array('uid' => 0, 'tid' => 0, 'status' => 0, 'starttime' => 0, 'endtime' => 0);
		$kmdInfoDb = $this->_getKmdInfoDB();
		return $kmdInfoDb->updateKmdInfo($data, $kid);
	}
	
	/**
	 * ����tid�ջؿ�����
	 * @param int $tid
	 * @return bool
	 */
	function initKmdInfoByTid($tid){
		$tid = intval($tid);
		if ($tid < 1) return false;
		$kmdInfo = $this->getKmdInfoByTid($tid);
		if (S::isArray($kmdInfo)) {
			$kmdInfo['kid'] && $this->initKmdInfoByKid($kmdInfo['kid']);
		}
		return true;
	}
	
	/**
	 * ����kid��տ������ƹ���
	 * @param int $tid
	 */
	function initThreadInfoByKid($kid){
		$kid = intval($kid);
		if ($kid < 1) return false;
		$kmdInfo = $this->getKmdInfoByKid($kid);
		if (!$kmdInfo) return false;
		//���¿����������
		$kmdInfo['tid'] && $this->updateKmdThreadByTid($kmdInfo['tid'], 0);
		$data = array('tid' => 0, 'status' => KMD_THREAD_STATUS_EMPTY);
		$kmdInfoDb = $this->_getKmdInfoDB();
		return $kmdInfoDb->updateKmdInfo($data, $kid);
	}
	
	/**
	 * ����tid��տ������ƹ���
	 * @param int $tid
	 */
	function initThreadInfoByTid($tid){
		$tid = intval($tid);
		if ($tid < 1) return false;
		$kmdInfo = $this->getKmdInfoByTid($tid);
		if (S::isArray($kmdInfo)) {
			$kmdInfo['kid'] && $this->initThreadInfoByKid($kmdInfo['kid']);
		}
		return true;
	}
	
	/**
	 * ����id���¿�������Ϣ
	 * @param array $fieldData
	 * @param int $kid
	 * @return bool
	 */
	function updateKmdInfo($fieldData, $kid) {
		$kid = intval($kid);
		if ($kid < 1 || !S::isArray($fieldData)) return false;
		$kmdInfoDb = $this->_getKmdInfoDB();
		return $kmdInfoDb->updateKmdInfo($fieldData, $kid);
	}
	
	/**
	 * ����id��ȡ��������Ϣ
	 * @param int $kid
	 * @return array
	 */
	function getKmdInfoByKid($kid) {
		$kid = intval($kid);
		if ($kid < 1) return array();
		$kmdInfoDb = $this->_getKmdInfoDB();
		return $kmdInfoDb->getKmdInfoByKid($kid);
	}
	
	/**
	 * ����tid��ȡ��������Ϣ
	 * @param int $tid
	 * @return array
	 */
	function getKmdInfoByTid($tid) {
		$tid = intval($tid);
		if ($tid < 1) return array();
		$kmdInfoDb = $this->_getKmdInfoDB();
		return $kmdInfoDb->getKmdInfoByTid($tid);
		
	}
	
	/**
	 * ����id��ȡ��������Ϣ
	 * @param int $kid
	 * @return array
	 */
	function getKmdInfoDetailByKid($kid) {
		$kid = intval($kid);
		if ($kid < 1) return array();
		$kmdInfoDb = $this->_getKmdInfoDB();
		return $kmdInfoDb->getKmdInfoDetailByKid($kid);
	}
	
	/**
	 * ����uid��ȡ��������Ϣ
	 * @param int $uid
	 * @return array
	 */
	function getKmdInfoDetailByUid($uid, $start, $limit) {
		list($uid, $start, $limit) = array(intval($uid), intval($start), intval($limit));
		if ($uid < 1) return array();
		$kmdInfoDb = $this->_getKmdInfoDB();
		$tmpKmdInfo = $kmdInfoDb->getKmdInfoByUid($uid, $start, $limit);
		if (!S::isArray($tmpKmdInfo)) return array();
		$tids = $fids = $result = array();
		foreach ($tmpKmdInfo as $value) {
			$tids[$value['kid']] = $value['tid'];
			$fids[$value['kid']] = $value['fid'];
		}
		$cacheService = Perf::gatherCache('pw_threads');
		$threads = $cacheService->getThreadsByThreadIds(array_unique($tids));
		$forumService = L::loadClass('Forums', 'forum');
		$forums = $forumService->getForumsByFids(array_unique($fids));
		
		foreach ($tmpKmdInfo as $value) {
			$value['subject'] = $threads[$value['tid']]['subject'];
			$value['forumname'] = $forums[$value['fid']]['name'];
			$result[$value['kid']] = $value;
		}
		return $result;
	}
	
	/**
	 * ����fid��ȡδ�������һ��������
	 * @param int $fid
	 * @return array
	 */
	function getUnusedKmdInfoByFid($fid) {
		$fid = intval($fid);
		if ($fid < 1) return array();
		$kmdInfoDb = $this->_getKmdInfoDB();
		return $kmdInfoDb->getUnusedKmdInfoByFid($fid);
	}
	
	/**
	 * ����fidͳ���ѱ�����Ŀ���������
	 * @param int $fid
	 * @return int
	 */
	function countUsedKmdNumsByFid($fid) {
		$fid = intval($fid);
		if ($fid < 1) return false;
		$kmdInfoDb = $this->_getKmdInfoDB();
		return $kmdInfoDb->countUsedKmdNumsByFid($fid);
	}
	
	/**
	 * ����fid����ͳ���ѱ�����Ŀ���������
	 * @param array $fids
	 * @return array
	 */
	function countUsedKmdNumsByFids($fids) {
		if (!S::isArray($fids)) return false;
		$kmdInfoDb = $this->_getKmdInfoDB();
		return $kmdInfoDb->countUsedKmdNumsByFids($fids);
	}
	
	/**
	 * ��fid��ȡʣ���������
	 * @param int $fid
	 * @return int
	 */
	function getLeftKmdNumsByFid($fid) {
		$fid = intval($fid);
		if ($fid < 1) return false;
		L::loadClass('forum', 'forum', false);
		$forumInfo = new PwForum($fid);
		$kmdNum = intval($forumInfo->forumset['kmdnumber']);
		if (!$kmdNum) return 1;
		$usedKmdNums = $this->countUsedKmdNumsByFid($fid);
		$leftNum = $kmdNum - $usedKmdNums;
		if ($leftNum <= 0) return 0;
		$notPayedNums = $this->countPayLogs(array('fid' => $fid, 'status' => 1, 'kid' => 0));
		$leftNum -= $notPayedNums;
		if ($leftNum <= 0) return 0;
		$this->recycleAllExpiredKmds();
		$expiredNum = $this->countRenewedButExpiredNum($fid);
		$leftNum -= $expiredNum;
		return $leftNum <= 0 ? 0 : $leftNum;
	}
	
	/**
	 * ����״̬��ȡ���п�����
	 * @param int $status
	 * @return array
	 */
	function getKmdInfosByStatus($status){
		$status = intval($status);
		$data = array();
		if (!isset($this->threadStatus[$status])) return $data;
		$kmdInfoDb = $this->_getKmdInfoDB();
		return $kmdInfoDb->getKmdInfosByStatus($status);
	}
	
	/**
	 * ����������
	 * @param array $params
	 * @param int $offset
	 * @param int $size
	 * @return array
	 */
	function getKmdInfosWithCondition($params,$offset,$size) {
		if (!is_array($params)) return false;
		list($start, $limit, $fid, $uid, $status, $starttime, $endtime) = array(intval($offset), intval($size), intval($params['fid']), intval($params['uid']), intval($params['status']), intval($params['starttime']), intval($params['endtime']));
		$kmdInfoDb = $this->_getKmdInfoDB();
		$tmpKmdInfo = $kmdInfoDb->getKmdInfosWithCondition($start, $limit, $fid, $uid, $status, $starttime, $endtime);
		if (!S::isArray($tmpKmdInfo)) return array();
		$tids = $fids = $uids = $result = array();
		foreach ($tmpKmdInfo as $value) {
			$tids[$value['kid']] = $value['tid'];
			$fids[$value['kid']] = $value['fid'];
			$uids[$value['kid']] = $value['uid'];
		}
		$cacheService = Perf::gatherCache('pw_threads');
		$threads = $cacheService->getThreadsByThreadIds(array_unique($tids));
		$forumService = L::loadClass('Forums', 'forum');
		$forums = $forumService->getForumsByFids(array_unique($fids));
		$userService = L::loadClass('UserService', 'user');
		$users = $userService->getByUserIds(array_unique($uids));
		
		foreach ($tmpKmdInfo as $value) {
			$value['subject'] = $threads[$value['tid']]['subject'];
			$value['forumname'] = $forums[$value['fid']]['name'];
			$value['username'] = $users[$value['uid']]['username'];
			$result[$value['kid']] = $value;
		}
		return $result;
	}
	
	/**
	 * ��������ͳ�Ƹ���
	 * @param array $params
	 * @return int
	 */
	function countKmdInfosWithCondition($params) {
		if (!is_array($params)) return false;
		list($fid, $uid, $status, $starttime, $endtime) = array(intval($params['fid']), intval($params['uid']), intval($params['status']), intval($params['starttime']), intval($params['endtime']));
		$kmdInfoDb = $this->_getKmdInfoDB();
		return $kmdInfoDb->countKmdInfosWithCondition($fid, $uid, $status, $starttime, $endtime);
	}
	
	/**
	 * �������е��ڵĿ�����
	 * @return bool
	 */
	function recycleAllExpiredKmds() {
		$kmdInfoDb = $this->_getKmdInfoDB();
		$recycles = $kmdInfoDb->getAllExpiredKmds();
		if (!S::isArray($recycles)) return true;
		foreach ($recycles as $v){
			if ($v['tid'] > 0){
				$this->updateKmdThread($v['kid']);
			}
			$this->initKmdInfoByKid($v['kid']);
		}
		require_once(R_P . 'require/updateforum.php');
		updatetop();
	}
	
	/**
	 * �������п�����(�����عرյȲ�������)
	 */
	function recycleAllKmds($fid=0){
		$fid = intval($fid);
		$kmdInfoDb = $this->_getKmdInfoDB();
		$recycles = $kmdInfoDb->getAllValidKmds($fid);
		foreach ($recycles as $v){
			if ($v['tid'] > 0){
				$this->updateKmdThread($v['kid']);
			}
			$this->initKmdInfoByKid($v['kid']);
		}
		require_once(R_P . 'require/updateforum.php');
		updatetop();
	}
	
/** info end **/

/** log start **/
	
	/**
	 * ����һ��֧����¼
	 * @param array $fieldData
	 * @return bool
	 */
	function addPayLog($fieldData) {
		if (!S::isArray($fieldData)) return false;
		$kmdPayLogDb = $this->_getKmdPayLogDB();
		return $kmdPayLogDb->addPayLog($fieldData);
	}
	
	/**
	 * ����idɾ��֧����¼
	 * @param int $id
	 * @return bool
	 */
	function deletePayLogById($id) {
		$id = intval($id);
		if ($id < 1) return false;
		$kmdPayLogDb = $this->_getKmdPayLogDB();
		return $kmdPayLogDb->deletePayLogById($id);
	}
	
	/**
	 * ����uidɾ��֧����¼
	 * @param int $uid
	 * @return bool
	 */
	function deletePayLogByUid($uid) {
		$uid = intval($uid);
		if ($uid < 1) return false;
		$kmdPayLogDb = $this->_getKmdPayLogDB();
		return $kmdPayLogDb->deletePayLogByUid($uid);
	}
	
	/**
	 * ����id����֧����¼
	 * @param array $fieldData
	 * @param int $id
	 * @return bool
	 */
	function updatePayLog($fieldData, $id) {
		$id = intval($id);
		if ($id < 1 || !S::isArray($fieldData)) return false;
		$kmdPayLogDb = $this->_getKmdPayLogDB();
		return $kmdPayLogDb->updatePayLog($fieldData, $id);
	}
	
	/**
	 * ����id��������Ϊ��֧��
	 * @param array $ids
	 * @return bool
	 */
	function setLogsPayedByIds($ids) {
		if (!S::isArray($ids)) return false;
		$kmdPayLogDb = $this->_getKmdPayLogDB();
		return $kmdPayLogDb->setLogsPayedByIds($ids);
	}
	
	/**
	 * ȡ��һ������֮�ڵ�֧����¼
	 * @param int $day
	 * @return bool
	 */
	function setPayLogsInvalidUsingTimestamp($uid, $day = 7) {
		global $timestamp;
		list($day, $uid) = array(intval($day), intval($uid));
		if ($uid < 1) return false;
		$time = $timestamp - 86400 * $day;
		$kmdPayLogDb = $this->_getKmdPayLogDB();
		return $kmdPayLogDb->setPayLogsInvalidUsingTimestamp($uid, $time);
	}
	
	/**
	 * ������������
	 * @param array $params
	 * @param int $offset
	 * @param int $size
	 * @return array
	 */
	function searchPayLogs($params, $offset, $size) {
		if (!is_array($params)) return false;
		list($start, $limit, $fid, $uid, $status, $starttime, $endtime) = array(intval($offset), intval($size), intval($params['fid']), intval($params['uid']), intval($params['status']), intval($params['starttime']), intval($params['endtime']));
		$kmdPayLogDb = $this->_getKmdPayLogDB();
		$kid = isset($params['kid']) ? intval($params['kid']) : null;
		$tmpPayLogs = $kmdPayLogDb->getPayLogsWithCondition($start, $limit, $fid, $uid, $status, $starttime, $endtime, $kid);
		if (!S::isArray($tmpPayLogs)) return array();
		$fids = $uids = $result = array();
		foreach ($tmpPayLogs as $value) {
			$fids[$value['id']] = $value['fid'];
			$uids[$value['id']] = $value['uid'];
		}
		$forumService = L::loadClass('Forums', 'forum');
		$forums = $forumService->getForumsByFids(array_unique($fids));
		$userService = L::loadClass('UserService', 'user');
		$users = $userService->getByUserIds(array_unique($uids));
		
		foreach ($tmpPayLogs as $value) {
			$value['forumname'] = $forums[$value['fid']]['name'];
			$value['username'] = $users[$value['uid']]['username'];
			$result[$value['id']] = $value;
		}
		return $result;
	}
	
	/**
	 * ��������ͳ�Ƹ���
	 * @param array $params
	 * @return int
	 */
	function countPayLogs($params) {
		if (!is_array($params)) return false;
		list($fid, $uid, $status, $starttime, $endtime) = array(intval($params['fid']), intval($params['uid']), intval($params['status']), intval($params['starttime']), intval($params['endtime']));
		$kmdPayLogDb = $this->_getKmdPayLogDB();
		$kid = isset($params['kid']) ? intval($params['kid']) : null;
		return $kmdPayLogDb->countPayLogsWithCondition($fid, $uid, $status, $starttime, $endtime, $kid);
	}
	
	/**
	 * ��������ͳ������
	 * @param array $param
	 * @return int
	 */
	function getKmdIncome($params) {
		if (!is_array($params)) return false;
		list($fid, $uid, $starttime, $endtime) = array(intval($params['fid']), intval($params['uid']), intval($params['starttime']), intval($params['endtime']));
		$kmdPayLogDb = $this->_getKmdPayLogDB();
		return $kmdPayLogDb->countKmdIncomeWithCondition($fid, $uid, $starttime, $endtime);
	}
	
	/**
	 * ����id��ȡ֧����Ϣ
	 * @param int $id
	 * @return array
	 */
	function getPayLogById($id) {
		$id = intval($id);
		if ($id < 1) return false;
		$kmdPayLogDb = $this->_getKmdPayLogDB();
		return $kmdPayLogDb->getPayLogById($id);
	}
	
	/**
	 * 
	 * ��uid��ȡδ֧����¼
	 * @param int $uid
	 * @return array
	 */
	function getUnPayedLogsByUid($uid) {
		$uid = intval($uid);
		if ($uid < 1) return array();
		$kmdPayLogDb = $this->_getKmdPayLogDB();
		return $kmdPayLogDb->getUnPayedLogsByUid($uid);
	}
	
	/**
	 * ͳ�������˵����ѹ��ڵĿ���������
	 * @param int $fid
	 * @return int
	 */
	function countRenewedButExpiredNum($fid) {
		$fid = intval($fid);
		if ($fid < 1) return false;
		$kmdPayLogDb = $this->_getKmdPayLogDB();
		return $kmdPayLogDb->countRenewedButExpiredNum($fid);
	}

/** log end **/

/** spread start **/
	
	/**
	 * ���������ײ���Ϣ
	 * @param array $spreads[key] = array('field1'=>xxx,'field2'=>xxx);
	 * @return bool
	 */
	function updateSpreads($spreads) {
		if (!S::isArray($spreads)) return false;
		$kmdSpreadDb = $this->_getKmdSpreadDB();
		foreach ($spreads as $key => $value) {
			$key = intval($key);
			if ($key < 1 || !S::isArray($value)) continue;
			$kmdSpreadDb->updateSpread($value, $key);
		}
		return true;
	}
	
	/**
	 * ���������ײ���Ϣ 
	 * @param array $spreads[] = array('field1'=>xxx,'field2'=>xxx);
	 * @return bool
	 */
	function addSpreads($spreads) {
		if (!S::isArray($spreads)) return false;
		$kmdSpreadDb = $this->_getKmdSpreadDB();
		foreach ($spreads as $value) {
			if (!S::isArray($value)) continue;
			$kmdSpreadDb->addSpread($value);
		}
		return true;
	}
	
	/**
	 * ��ȡ�����ײ���Ϣ
	 * @return array
	 */
	function getSpreads() {
		$kmdSpreadDb = $this->_getKmdSpreadDB();
		return $kmdSpreadDb->getAllSpreads();
	}
	
	/**
	 * ����sidɾ���ײ���Ϣ
	 * @param int $sid
	 */
	function deleteSpreadById($sid) {
		$sid = intval($sid);
		if ($sid < 1) return false;
		$kmdSpreadDb = $this->_getKmdSpreadDB();
		return $kmdSpreadDb->deleteSpreadBySid($sid);
	}
	
	/**
	 * �����ײ�id��ȡ�ײ���Ϣ
	 * @param int $sid
	 * @return array
	 */
	function getSpreadById($sid) {
		$sid = intval($sid);
		if ($sid < 1) return false;
		$kmdSpreadDb = $this->_getKmdSpreadDB();
		return $kmdSpreadDb->getSpreadBySid($sid);
	}
	

/** spread end **/	
	
/* user start */
	
	/**
	 * �����������û�
	 * @param array $params
	 * @param int $offset
	 * @param int $size
	 * @return array
	 */
	function searchUsers($params, $offset, $size) {
		if (!is_array($params)) return false;
		list($uid, $start, $limit) = array(intval($params['uid']), intval($offset), intval($size));
		$kmdUserDb = $this->_getKmdUserDB();
		$tmpKmdUserInfo = $kmdUserDb->getKmdUsersWithCondition($uid, $start, $limit);
		if (!S::isArray($tmpKmdUserInfo)) return array();
		$uids = $result = array();
		foreach ($tmpKmdUserInfo as $value) {
			$uids[$value['uid']] = $value['uid'];
		}
		$userService = L::loadClass('UserService', 'user');
		$users = $userService->getByUserIds(array_unique($uids));
		
		foreach ($tmpKmdUserInfo as $value) {
			$value['username'] = $users[$value['uid']]['username'];
			$result[$value['uid']] = $value;
		}
		return $result;
	}
	
	/**
	 * ��������ͳ������
	 * @param array $params
	 * @return int
	 */
	function countKmdUsers($params) {
		if (!is_array($params)) return false;
		$uid = intval($params['uid']);
		$kmdUserDb = $this->_getKmdUserDB();
		return $kmdUserDb->countKmdUsersWithCondition($uid);
	}
	
	/**
	 * ���¿����ƹ�������Ϣ
	 * @param array $fieldData
	 * @param int $uid
	 * @return bool
	 */
	function setUserInfoByUid($fieldData) {
		if (!S::isArray($fieldData)) return false;
		$kmdUserDb = $this->_getKmdUserDB();
		return $kmdUserDb->addKmdUser($fieldData);
	}
	
	/**
	 * ����uid��ȡ�û���Ϣ
	 * @param int $uid
	 * @return array
	 */
	function getUserInfoByUid($uid) {
		$uid = intval($uid);
		if ($uid < 1) return array();
		$kmdUserDb = $this->_getKmdUserDB();
		return $kmdUserDb->getKmdUserByUid($uid);
	}

/* user end */
	
/* db start */
	
	/**
	 * ��ȡ��������Ϣ��dao
	 */
	function _getKmdInfoDB() {
		return L::loadDB('KmdInfo', 'forum');
	}
	
	/**
	 * ��ȡ������֧����¼��dao
	 */
	function _getKmdPayLogDB() {
		return L::loadDB('KmdPayLog', 'forum');
	}
	
	/**
	 * ��ȡ�������ײͱ�dao
	 */
	function _getKmdSpreadDB() {
		return L::loadDB('KmdSpread', 'forum');
	}
	
	/**
	 * ��ȡ�������û���Ϣ��dao
	 */
	function _getKmdUserDB() {
		return L::loadDB('KmdUser', 'forum');
	}

	/**
	 * ��ȡpw_threads��dao
	 */
	function _getThreadsDB() {
		return L::loadDB('threads', 'forum');
	}
/* db end */	
}
?>
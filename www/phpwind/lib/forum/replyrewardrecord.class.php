<?php
!function_exists('readover') && exit('Forbidden');

class PW_ReplyRewardRecord {
	
	/**
	 * ��������
	 * @param unknown_type $uid
	 * @param unknown_type $tid
	 * @param unknown_type $pid
	 */
	function rewardReplyUser($uid, $tid, $pid) {
		list($uid, $tid, $pid) = array(intval($uid), intval($tid), intval($pid));
		if ($uid < 1 || $tid < 1 || $pid < 1) return false;
		$threadsService = L::loadClass('Threads', 'forum');
		$threadData = $threadsService->getByThreadId($tid);
		if (!$threadData || $threadData['authorid'] == $uid) return false;
		
		$replyRewardService = L::loadClass('ReplyReward', 'forum');/* @var $replyRewardService PW_ReplyReward */
		$rewardInfo = $replyRewardService->getRewardByTid($tid);
		if (!$this->_checkRewardCondition($rewardInfo, $uid, $tid) || !$this->_checkIfReward($rewardInfo['chance'])) return false;
		return $this->_rewardUser($uid, $tid, $pid, $rewardInfo);
	}
	
	/**
	 * �������
	 * @param $rewardInfo
	 * @param $uid
	 * @param $tid
	 */
	function _checkRewardCondition($rewardInfo, $uid, $tid) {
		if (!$rewardInfo || !$rewardInfo['rewardtimes'] || $rewardInfo['lefttimes'] < 1) return false;
		$rewardRecords = $this->countRecordsByTidAndUid($tid, $uid);
		if ($rewardInfo['repeattimes'] && $rewardRecords >= $rewardInfo['repeattimes']) return false;
		return true;
	}
	
	/**
	 * �н�����
	 * @param $chance
	 */
	function _checkIfReward($chance) {
		return rand(1, 10) <= ($chance / 10);
	}
	
	/**
	 * ���影������
	 * @param $uid
	 * @param $tid
	 * @param $pid
	 * @param $rewardInfo
	 */
	function _rewardUser($uid, $tid, $pid, $rewardInfo) {
		global $credit;
		$record = array(
			'tid' => intval($tid),
			'pid' => intval($pid),
			'uid' => intval($uid),
			'credittype' => $rewardInfo['credittype'],
			'creditnum' => $rewardInfo['creditnum'],
			'rewardtime' => $GLOBALS['timestamp']
		);
		$this->addRewardRecord($record);
		$replyRewardService = L::loadClass('ReplyReward', 'forum');/* @var $replyRewardService PW_ReplyReward */
		$lefttimes = ($rewardInfo['lefttimes'] - 1 >= 0) ? $rewardInfo['lefttimes'] - 1 : 0;
		$replyRewardService->updateByTid($tid, array('lefttimes' => $lefttimes));
		if (!$credit) require_once R_P . 'require/credit.php';
		$credit->set($uid, $rewardInfo['credittype'], $rewardInfo['creditnum']);
		return $this->_addCreditPop($uid, $rewardInfo['credittype'], $rewardInfo['creditnum']);
	}
	
	/**
	 * ��¼�û�creditpop��Ϣ
	 * @param $uid
	 * @param $creditType
	 * @param $creditNum
	 */
	function _addCreditPop($uid, $creditType, $creditNum) {
		global $db_ifcredit;
		list($creditNum, $creditpop) = array(intval($creditNum), '');
		if (!$db_ifcredit || !$creditNum) return false;
		
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
		$userMemberData = $userService->get($uid, false, true);
		$creditpop = $userMemberData['creditpop'] ? $userMemberData['creditpop'] . ",reply_reward|$creditType:+$creditNum|" : "reply_reward|$creditType:+$creditNum|";
		$userService->update($uid, array(), array('creditpop' => $creditpop));
		return true;
	}
	
	/**
	 * ����tid��ȡ�н���Ϣ
	 */
	function getRewardRecordByTid($tid) {
		list($tid) = intval($tid);
		if ($tid < 1) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->getRewardRecordByTid($tid);
	}
	
	/**
	 * ����uid��ȡ�н���Ϣ
	 * @param $uid
	 */
	function getRewardRecordByUid($uid) {
		list($uid) = intval($uid);
		if ($uid < 1) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->getRewardRecordByUid($uid);
	}
	
	/**
	 * ����uids������ȡ�н���Ϣ
	 * @param $uids
	 */
	function getRewardRecordByUids($uids) {
		if (!S::isArray($uids)) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->getRewardRecordByUids($uids);
	}
	
	/**
	 * ����tid, uid��ȡ�н���Ϣ
	 * @param $tid
	 * @param $uid
	 */
	function getRewardRecordByTidAndUid($tid, $uid) {
		list($tid, $uid) = array(intval($tid), intval($uid));
		if ($tid < 1 || $uid < 1) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->getRewardRecordByTidAndUid($tid, $uid);
	}
	
	/**
	 * ����tid, pid��ȡ�н���Ϣ
	 * @param $tid
	 */
	function getRewardRecordByTidAndPid($tid, $pid) {
		list($tid, $pid) = array(intval($tid), intval($pid));
		if ($tid < 1 || $pid < 1) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->getRewardRecordByTidAndPid($tid, $pid);
	}
	
	/**
	 * ����tid,pids��ȡ��Ϣ
	 */
	function getRewardRecordByTidAndPids($tid, $pids) {
		$tid = intval($tid);
		if ($tid < 1 || !S::isArray($pids)) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->getRewardRecordByTidAndPids($tid, $pids);
	}
	
	/**
	 * ������ȡ�н���Ϣ
	 * @param $tids
	 */
	function getRewardRecordByTids($tids) {
		if (!S::isArray($tids)) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->getRewardRecordByTids($tids);
	}
	
	/**
	 * �����µ��н���Ϣ�����ݿ���
	 * @param $data
	 */
	function addRewardRecord($data) {
		if (!S::isArray($data)) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->addRewardRecord($data);
	}
	
	/**
	 * �������ݿ��е��н���Ϣ
	 * @param $tid
	 * @param $data
	 */
	function updateRecordByTidAndPid($tid, $pid, $data) {
		list($tid, $pid) = array(intval($tid), intval($pid));
		if ($tid < 1 || $pid < 1 || !S::isArray($data)) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->updateRecordByTidAndPid($tid, $pid, $data);
	}
	
	/**
	 * ����tidɾ����Ϣ
	 * @param $tid
	 */
	function deleteByTid($tid) {
		$tid = intval($tid);
		if ($tid < 1) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->deleteByTid($tid);
	}
	
	/**
	 * ����ɾ����Ϣ
	 * @param $tids
	 */
	function deleteByTids($tids) {
		if (!S::isArray($tids)) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->deleteByTids($tids);
	}
	
	/**
	 * ����tid,pidɾ����Ϣ
	 * @param $tid
	 * @param $pid
	 */
	function deleteByTidAndPid($tid, $pid) {
		list($tid, $pid) = array(intval($tid), intval($pid));
		if ($tid < 1 || $pid < 1) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->deleteByTidAndPid($tid, $pid);
	}
	
	/**
	 * ����tid,pidsɾ����Ϣ
	 * @param $tid
	 * @param $pids
	 */
	function deleteByTidAndPids($tid, $pids) {
		$tid = intval($tid);
		if ($tid < 1 || !S::isArray($pids)) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->deleteByTidAndPids($tid, $pids);
	}
	
	/**
	 * ͳ��ĳ���û���һ�����ӵ��е��н�����
	 * @param $tid
	 * @param $uid
	 */
	function countRecordsByTidAndUid($tid, $uid) {
		list($tid, $uid) = array(intval($tid), intval($uid));
		if ($tid < 1 || $uid < 1) return false;
		$replyRewardRecordDao = $this->_getReplyRewardRecordDao();
		return $replyRewardRecordDao->countRecordsByTidAndUid($tid, $uid);
	}
	
	/**
	 * ��ȡdao����
	 */
	function _getReplyRewardRecordDao() {
		static $replyRewardRecordDao = null;
		if (is_null($replyRewardRecordDao)) {
			$replyRewardRecordDao = L::loadDb('ReplyRewardRecord', 'forum');
		}
		return $replyRewardRecordDao;
	}
}

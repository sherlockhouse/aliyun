<?php
!function_exists('readover') && exit('Forbidden');

/**
 * ���ӹ��������
 * 
 * @package Thread
 */
class PW_Threads {

	/**
	 * ɾ��pw_threads���һ����¼
	 *
	 * @param int $threadId ����id
	 * @return int
	 */
	function deleteByThreadId($threadId) {
		$threadId = S::int($threadId);
		if($threadId < 1){
			return false;
		}
		$_dbService = L::loadDB('threads', 'forum');
		return $_dbService->deleteByThreadId($threadId);
	}
	
	/**
	 * ��ȡpw_threads���һ����¼
	 *
	 * @param int $threadId ����id
	 * @return array
	 */
	function getByThreadId($threadId) {
		$threadId = S::int($threadId);
		if($threadId < 1){
			return false;
		}
		$_dbService = L::loadDB('threads', 'forum');
		return $_dbService->get($threadId);
	}
	
	/**
	 * 
	 * ����tpc״̬
	 * @param int $threadId
	 * @param int $b λ
	 * @param int $v 0|1
	 */
	function setTpcStatusByThreadId($threadId,$b,$v = '1') {
		$b = intval($b);
		$threadId = intval($threadId);
		if (!$threadId) return false;
		$v != 1 && $v = '0';
		$threadInfo = $this->getByThreadId($threadId);
		if (!S::isArray($threadInfo)) return false;
		$tpcstatus = $threadInfo['tpcstatus'];
		setstatus($tpcstatus, $b ,$v);
		$_dbService = L::loadDB('threads', 'forum');
		$_dbService->update(array('tpcstatus' => $tpcstatus) ,$threadId);
		return true;
	}
	
	//** oxFFEF for tucool status
	function setTpcStatusByThreadIds($tids,$mask=0xFFEF){
		$_dbService = L::loadDB('threads', 'forum');
		$_dbService->setTpcStatusByThreadIds($tids,$mask=0xFFEF);
	}
	
	/**
	 * ɾ��pw_threads����һ���¼
	 *
	 * @param array $threadIds ����id �������ʽ��
	 * @return int
	 */	
	function deleteByThreadIds($threadIds) {
		$threadIds = (array) $threadIds;
		$_dbService = L::loadDB('threads', 'forum');
		return $_dbService->deleteByThreadIds($threadIds);
	}
	
	/**
	 * ��������@��Ϣ
	 * @param int $tid
	 * @param int $pid
	 * @param array $usernames
	 */
	function setAtUsers($tid,$pid,$usernames){
		$tid = intval($tid);
		$pid = intval($pid);
		if (!$tid || !S::isArray($usernames)) {
			return false;
		}
		$userService = L::loadClass('userservice','user');
		$users = $userService->getByUserNames($usernames);
		if ($users) {
			$userids = array();
			foreach ($users as $v) {
				$userids[] = $v['uid'];
			}
			$threadsAtDb = $this->_getThreadsAtDB();
			$threadsAtDb->adds($tid,$pid,$userids);
			return true;
		}
		return false;
	}
	
	function updateAtUsers($tid,$pid,$usernames){
		$tid = intval($tid);
		$pid = intval($pid);
		if (!$tid) return false;
		if (!S::isArray($usernames)){
			return $this->deleteAtUsers($tid,array($pid));
		} else {
			$userService = L::loadClass('userservice','user');
			$users = $userService->getByUserNames($usernames);
			if ($users) {
				$deleteUserIds = $userids = array();
				foreach ($users as $v) {
					$userids[] = $v['uid'];
				}
				$threadsAtDb = $this->_getThreadsAtDB();
				$threadAt = $threadsAtDb->gets($tid,array($pid));
				foreach ($threadAt as $v) {
					$k = array_search($v['uid'], $userids);
					if ($k !== false){
						unset($userids[$k]);
						continue;
					} else {
						$deleteUserIds[] = $v['uid'];
					}
				}
				$userids && $threadsAtDb->adds($tid,$pid,$userids);
				$deleteUserIds && $threadsAtDb->deleteByUids($tid,$pid,$deleteUserIds);
				return true;
			}
		}
	}
	
	function getAtUsers($tid,$pids) {
		$tid = intval($tid);
		if (!$tid || !S::isArray($pids)) {
			return false;
		}
		$data = $tmpData = $uids = array();
		$threadsAtDb = $this->_getThreadsAtDB();
		$threadAt = $threadsAtDb->gets($tid,$pids);
		if (!$threadAt) return $data;
		foreach ($threadAt as $v){
			$uids[] = $v['uid'];
			$tmpData[] = $v;
		}
		$uids = array_unique($uids);
		if ($uids) {
			$userService = L::loadClass('userservice','user');
			$userNames = $userService->getUserNamesByUserIds($uids);
		}
		if($userNames && $tmpData){
			foreach ($tmpData as $v) {
				$data[$v['pid']][] = $userNames[$v['uid']];
			}
		}
		return $data;
	}
	
	function deleteAtUsers($tid,$pids){
		$threadsAtDb = $this->_getThreadsAtDB();
		return $threadsAtDb->delete($tid,$pids);
	}
	/**
	 * ���ݰ��idɾ������
	 *
	 * @param int $forumId ���id
	 * @return int
	 */
	function deleteByForumId($forumId) {
		$forumId = S::int($forumId);
		if($forumId < 1){
			return false;
		}
		$_dbService = L::loadDB('threads', 'forum');
		return $_dbService->deleteByForumId($forumId);
	}
	
	/**
	 * ��������id ɾ������
	 *
	 * @param int $authorId ����id
	 * @return int
	 */
	function deleteByAuthorId($authorId) {
		$authorId = S::int($authorId);
		if($authorId < 1){
			return false;
		}
		$_dbService = L::loadDB('threads', 'forum');
		return $_dbService->deleteByAuthorId($authorId);
	}	
	
	function getLatestImageThreads($num){
		$num = intval($num);
		if (!$num) return array();
		$_dbService = L::loadDB('threads', 'forum');
		$threads = $_dbService->getLatestImageThreads($num);
		if ($threads) {
			$_attachService = L::loadDB('attachs', 'forum');
			$attaches = $_attachService->getByTid(array_keys($threads),null,null,'img');
			if (!$attaches) return $threads;
			krsort($attaches);
			foreach ($attaches as $k=>$v) {
				if (isset($threads[$v['tid']]['attachurl']) ) continue;
				$threads[$v['tid']]['attachurl'] = $v['attachurl'];
				$threads[$v['tid']]['ifthumb'] = $v['ifthumb'];
			}
		}
		return $threads;
	}
	
	function deleteTucoolThreadsByTids($tids){
		if(!S::isArray($tids)) return false;
		$_dbService = L::loadDB('threads', 'forum');
		$_dbService->deleteTucoolThreadsByTids($tids);
	}
	function getLatestThreads($forumIds, $starttime, $endtime, $offset, $limit){
		$limit = intval($limit);
		if ($limit<=0) return array();
		if (!is_array($forumIds) || !count($forumIds)) return array();
		$_dbService = L::loadDB('threads', 'forum');
		return $_dbService->getLatestThreads($forumIds, $starttime, $endtime, $offset, $limit);
	}
	/**
	 * ��ȡ�������ӵ�tmsgs����Ϣ  by chenyun 2011-07-13
	 * 
	 * @param int $tid
	 * @return array
	 */
	function getTmsgByTid($tid) {
		$tid = intval($tid);
		if ($tid < 1) return array(); 
		$_dbService = L::loadDB('threads', 'forum');
		return $_dbService->getTmsgByThreadId($tid);
	}
	
	/**
	 * ��ȡ����������Ϣ
	 * 
	 * @param int $tid
	 * @return array
	 */
	function getByTid($tid) {
		$tid = intval($tid);
		if ($tid < 1) return array(); 
		$threadsDb = $this->_getThreadsDB();
		return $threadsDb->get($tid);
	}

	/**
	 * @return PW_ThreasdDB
	 */
	function _getThreadsDB() {
		return L::loadDB('threads', 'forum');
	}
	
	function _getThreadsAtDB() {
		return L::loadDB('threadsat', 'forum');
	}
}

?>

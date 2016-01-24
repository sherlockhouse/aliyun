<?php
! defined ( 'P_W' ) && exit ( 'Forbidden' );
class GatherCache_PW_Threads_Cache extends GatherCache_Base_Cache {
	var $_defaultCache = PW_CACHE_MEMCACHE;
	var $_prefix = 'thread_';
	 
	/**
	 * ��ȡһ�����ӻ�����Ϣ
	 *
	 * @param int $threadId ����id
	 * @return array
	 */
	function getThreadByThreadId($threadId) {
		$threadId = S::int($threadId);
		if ($threadId < 1) return false;
		if (! $this->checkMemcache()) {
			return $this->_getThreadNoCache($threadId);
		}
		$key = $this->_getKeyForThread($threadId);
		$result = $this->_cacheService->get($key);
		if ($result === false) {
			$result = $this->_getThreadNoCache($threadId);
			$this->_cacheService->set($key, $result);
		}
		return $result;
	}
	
	/**
	 * ��ȡһ�����ӵĻ�����Ϣ
	 *
	 * @param array $threadIds ����id����
	 * @return array
	 */	
	function getThreadsByThreadIds($threadIds) {
		if (! S::isArray ( $threadIds )) {
			return array();
		}
		if (!$this->checkMemcache()) {
			return $this->_getThreadsNoCache($threadIds);
		}
		$result = $resultInCache = $resultInDb = $keys = $_cachedThreadIds = array ();
		foreach ( $threadIds as $threadId ) {
			$keys [] = $this->_getKeyForThread ( $threadId );
		}
		if (($threads = $this->_cacheService->get ( $keys ))) {
			foreach ( $threads as $value ) {
				$_cachedThreadIds [] = $value ['tid'];
				$resultInCache [$value ['tid']] = $value;
			}
		}
		$_noCachedThreadIds = array_diff ( $threadIds, $_cachedThreadIds );
		if ($_noCachedThreadIds && ($resultInDb = $this->_getThreadsNoCache ( $_noCachedThreadIds ))) {
			foreach ( $resultInDb as $value ) {
				$this->_cacheService->set ( $this->_getKeyForThread ( $value ['tid'] ), $value );
			}
		}
		$tmpResult = (array)$resultInCache + (array)$resultInDb;
		foreach ($threadIds as $threadId){
			$result[$threadId] = isset($tmpResult[$threadId]) ? $tmpResult[$threadId] : false;
		}
		return $result;
	}
	
	/**
	 * ��ȡ���ӻ�����Ϣ����ϸ��Ϣ
	 *
	 * @param int $threadId ����id
	 * @return array
	 */
	function getThreadAndTmsgByThreadId($threadId) {
		$threadId = S::int($threadId);
		if ($threadId < 1) return false;
		if (! $this->checkMemcache ()) {
			return $this->_getThreadAndTmsgByThreadIdNoCache($threadId);
		}
		$threadKey = $this->_getKeyForThread($threadId);
		$tmsgKey = $this->_getKeyForTmsg($threadId);
		//* $result = $this->_cacheService->get(array($threadKey, $tmsgKey));
		//* $thread = isset($result[$threadKey]) ? $result[$threadKey] : false;
		//* $tmsg = isset($result[$tmsgKey]) ? $result[$tmsgKey] : false;
		$thread = $this->_cacheService->get($threadKey);
		$tmsg = $this->_cacheService->get($tmsgKey);
		if ($thread === false){
			$thread = $this->_getThreadNoCache($threadId);
			$this->_cacheService->set($threadKey, $thread);
		}
		if ($tmsg === false){
			$tmsg = $this->_getTmsgNoCache($threadId);
			$this->_cacheService->set($tmsgKey, $tmsg);
		}
		return ($thread && $tmsg) ? array_merge($thread, $tmsg) : array();
	}
	
	/**
	 * ���ݰ��id��ȡ�����б�
	 *
	 * @param int $forumId
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	function getThreadListByForumId($forumId, $offset, $limit) {
		if (! $this->checkMemcache ()) {
			return $this->_getThreadListNoCache($forumId, $offset, $limit);
		}
		$key = $this->_getKeyForThreadList($forumId, $offset, $limit);
		$threadListIds = $this->_cacheService->get($key);
		if (!$threadListIds && ($threadList = $this->_getThreadListNoCache($forumId, $offset, $limit))) {
			$this->_cacheService->set($key, array_keys($threadList));
		}
		return $threadList ?  $threadList : $this->getThreadsByThreadIds($threadListIds);
	}	
	
	/**
	 * ������ӻ���
	 *
	 * @param array $threadIds ����id����
	 * @return boolean 
	 */
	function clearCacheForThreadByThreadIds($threadIds){
		$threadIds = (array) $threadIds;
		foreach ($threadIds as $tid){
			$this->_cacheService->delete($this->_getKeyForThread($tid));
		}
		return true;
	}
	
	/**
	 * ���������ϸ��Ϣ����
	 *
	 * @param array $threadIds ����id����
	 * @return boolean 
	 */
	function clearCacheForTmsgByThreadIds($threadIds){
		$threadIds = (array) $threadIds;
		foreach ($threadIds as $tid){
			$this->_cacheService->delete($this->_getKeyForTmsg($tid));
		}
		return true;
	}	
	
	/**
	 * ���ĳһ���������б�
	 *
	 * @param array $forumIds ���id
	 * @return int
	 */
	function clearCacheForThreadListByForumIds($forumIds){
		$forumIds = (array) $forumIds;
		foreach ($forumIds as $forumId){
			$this->_cacheService->increment($this->_getKeyForForumVersion($forumId));
		}
		return  true;
	}
		
	/**
	 * ��ȡ������memcache�����key
	 *
	 * @param int $threadId ����id
	 * @return string
	 */
	function _getKeyForThread($threadId) {
		return $this->_prefix . 'tid_' . $threadId;
	}
	
	/**
	 * ��ȡ�����б����key
	 *
	 * @param int $forumId ���id
	 * @param int $offset
	 * @param int $limit
	 * @return string
	 */
	function _getKeyForThreadList($forumId, $offset, $limit){
		return $this->_prefix . 'fid_' . $forumId . '_offset_' . $offset . '_limit_' . $limit . '_ver_' . $this->_getForumVersionId($forumId); 
	}
	
	/**
	 * ��ȡ������ϸ��Ϣ�Ļ���key
	 *
	 * @param int $threadId ����id
	 * @return string
	 */
	function _getKeyForTmsg($threadId){
		return $this->_prefix . 'tmsg_tid_' . $threadId;
	}
	
	/**
	 * ��ȡ���汾�Ļ���key
	 *
	 * @param int $forumId
	 * @return string
	 */
	function _getKeyForForumVersion($forumId){
		return $this->_prefix . 'forumversion_' . $forumId;
	}
	
	/**
	 * ��ȡ�������°汾��
	 *
	 * @param int $forumId ���id
	 * @return int
	 */
	function _getForumVersionId($forumId){
		$key = $this->_getKeyForForumVersion($forumId);
		$versionId = $this->_cacheService->get($key);
		if (!$versionId){
			$versionId = 1;
			$this->_cacheService->set($key, $versionId, 3600*24);
		}
		return $versionId;
	}
	
	/**
	 * ��ͨ�����棬ֱ�Ӵ����ݿ��ȡһ�����ӻ�����Ϣ
	 *
	 * @param int $threadId ����id
	 * @return array
	 */	
	function _getThreadNoCache($threadId) {
		$_dbService = L::loadDB('threads', 'forum');
		return $_dbService->getThreadByThreadId ( $threadId );
	}
	
	/**
	 * ��ͨ�����棬ֱ�Ӵ����ݿ��ȡһ��������ϸ��Ϣ
	 *
	 * @param int $threadId ����id
	 * @return array
	 */		
	function _getTmsgNoCache($threadIds){
		$_dbService = L::loadDB('threads', 'forum');
		return $_dbService->getTmsgByThreadId($threadIds);		
	}	
	
	/**
	 * ��ͨ�����棬ֱ�Ӵ����ݿ��ȡһ�����ӻ�����Ϣ
	 *
	 * @param int $threadIds ����id����
	 * @return array
	 */		
	function _getThreadsNoCache($threadIds) {
		$_dbService = L::loadDB('threads', 'forum');
		return $_dbService->getThreadsByThreadIds($threadIds);
	}	
	
	/**
	 * ��ͨ�����棬ֱ�Ӵ����ݿ��ȡĳһ�������ӻ�����Ϣ
	 *
	 * @param int $forumId ���id
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	function _getThreadListNoCache($forumId, $offset, $limit){
		$_dbService = L::loadDB('threads', 'forum');
		return $_dbService->getThreadsByFroumId($forumId, $offset, $limit);		
	}
	
	/**
	 * ��ͨ�����棬ֱ�Ӵ����ݿ��ȡһ�����ӵ���ϸ��Ϣ
	 *
	 * @param int $threadId
	 * @return array
	 */
	function _getThreadAndTmsgByThreadIdNoCache($threadId){
		$_dbService = L::loadDB('threads', 'forum');
		return $_dbService->getThreadAndTmsgByThreadId($threadId);			
	}
}
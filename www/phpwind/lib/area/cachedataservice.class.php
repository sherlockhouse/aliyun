<?php
!defined('P_W') && exit('Forbidden');
/**
 * �Ż���ҳ�滺�����ݱ�
 * @author xiejin
 *
 */
class PW_CacheDataService {

	function updateCacheDataPiece($invokepieceid) {
		$this->deleteCacheData($invokepieceid);
	}

	function deleteCacheData($invokepieceid) {
		$cacheDataDB = $this->_getCacheDataDB();
		$cacheDataDB->deleteData($invokepieceid);
	}

	function deleteCacheDatas($ids) {
		$cacheDataDB = $this->_getCacheDataDB();
		$cacheDataDB->deleteDatas($ids);
	}
	
	function updateCacheDatas($datas) {
		$cacheDataDB = $this->_getCacheDataDB();
		$cacheDataDB->updates($datas);
	}
	
	function _getCacheDataDB() {
		return L::loadDB('CacheData', 'area');
	}
}
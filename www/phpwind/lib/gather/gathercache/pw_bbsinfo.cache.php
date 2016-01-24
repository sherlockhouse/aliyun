<?php
! defined ( 'P_W' ) && exit ( 'Forbidden' );
class GatherCache_PW_Bbsinfo_Cache extends GatherCache_Base_Cache {
	var $_defaultCache = PW_CACHE_MEMCACHE; 
	var $_prefix = 'bbsinfo_'; 
	
	/**
	 * �ӻ����ȡһ��bbsinfo��¼
	 *
	 * @param int $id
	 * @return array
	 */
	function getBbsInfoById($id){
		$id = S::int ( $id );
		if ($id < 1) return false;
		$key = $this->_getBbsInfoKeyById($id);
		if (! ($bbsInfo = $this->_cacheService->get($key))){
			$bbsInfo = $this->_getBbsInfoByIdNoCache($id);
			$bbsInfo && $this->_cacheService->set($key, $bbsInfo);
		}
		return $bbsInfo;
	}
	
	/**
	 * ��ͨ�����棬ֱ�Ӵ�bbsinfo��ȡһ����¼
	 *
	 * @param int $id
	 * @return array
	 */
	function _getBbsInfoByIdNoCache($id){
		$bbsInfoDb = L::loadDB ( 'bbsInfo', 'forum' );
		return $bbsInfoDb->get( $id );		
	}
	
	/**
	 * ���һ��bbsinfo����
	 *
	 * @param int $id
	 */
	function clearBbsInfoCacheById($id){
		$this->_cacheService->delete($this->_getBbsInfoKeyById($id));
	}
	
	/**
	 * �����������
	 *
	 * @param array $ids
	 */
	function clearBbsInfoCacheByIds($ids){
		$ids = (array) $ids;
		foreach ($ids as $id){
			$this->_cacheService->delete($this->_getBbsInfoKeyById($id));
		}
	}
	
	/**
	 * ��ȡbbsinfo�ڻ����е�key
	 *
	 * @param int $id
	 * @return array
	 */
	function _getBbsInfoKeyById($id){
		return $this->_prefix . 'id_' . $id;
	}
}
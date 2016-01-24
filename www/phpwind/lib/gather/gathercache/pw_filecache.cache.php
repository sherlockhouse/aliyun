<?php
! defined ( 'P_W' ) && exit ( 'Forbidden' );
class GatherCache_PW_FileCache_Cache extends GatherCache_Base_Cache {
	var $_prefix = 'filecache_';
	/*
	 * ��ȡ�ļ�����
	 * @param string $filePath	�ļ�����
	 */
	function getFileCache($filePath) {
		if (! $GLOBALS ['db_filecache_to_memcache']) {
			return $filePath;
		}
		$key = $this->_getKeyByFilePath ( $filePath );
		if (! ($result = $this->_cacheService->get ( $key )) && ($result = $this->getVarsByFilePath ( $filePath ))) {
			$this->_cacheService->set ( $key, $result );
		}
		if($result){
			foreach($result as $k=>$v){
				$GLOBALS[$k] = $v;
			}
		}
		return (! $result) ? $filePath : R_P . 'require/returns.php';
	}
	/*
	 * ɾ���ļ�����
	 * @param string $filePath	�ļ�����
	 */
	function clearFileCache($filePath) {
		$this->_cacheService->delete ( $this->_getKeyByFilePath ( $filePath ) );
	}
	/*
	 * ����ļ�·�����ɼ�ֵ
	 * @param string $filePath	�ļ�����
	 */
	function _getKeyByFilePath($filePath) {
		return $this->_prefix . md5 ( $filePath );
	}
	/*
	 * ����ļ�·����ȡ�ļ��ڵı���
	 * @param string 		$filePath	�ļ�����
	 */
	function getVarsByFilePath($filePath) {
		include S::escapePath($filePath);
		unset ( $filePath );
		return get_defined_vars ();
	}
}
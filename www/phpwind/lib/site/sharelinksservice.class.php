<?php
!defined('P_W') && exit('Forbidden');
/**
 * �������ӷ����
 * @package  PW_SharelinksService
 * @author panjl @2010-11-5
 */
class PW_SharelinksService {

	/**
	 * ����dao
	 * 
	 * @return PW_SharelinkstypeDB
	 */
	function _getLinksDB() {
		return L::loadDB('sharelinks', 'site');
	}

	/**
	 * ���շ��ࡢ�Ƿ���logo����������Ϣ
	 * 
	 * @param int $num ����
	 * @param bool $haveLogo=false �Ƿ���logo
	 * @param int $sids ����ID����array(1,2,3)
	 * @return array ����������Ϣ
	 */
	function getData($num,$stid = '',$haveLogo = false) {
		$num = (int) $num;
		$stid && $sids = '';
		if ($stid) { 
			$stid = (int) $stid;
			$relationService = L::loadClass('SharelinksRelationService', 'site');
			$stid && $sids = $relationService->findSidByStid($stid);
		}
		if($stid && !$sids) return array();
		$linksDb = $this->_getLinksDB();
		return $linksDb->getData($num,$sids,$haveLogo);
	}

}

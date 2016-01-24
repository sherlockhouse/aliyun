<?php
!defined('P_W') && exit('Forbidden');
/**
 * �������ӹ�ϵ�����
 * @package  PW_SharelinksRelationService
 * @author panjl @2010-11-5
 */
class PW_SharelinksRelationService {

	/**
	 * ����dao
	 * 
	 * @return PW_SharelinksRelationDB
	 */
	function _getTypeDB() {
		return L::loadDB('SharelinksRelation', 'site');
	}

	/**
	 * ���
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @return int ����id
	 */
	function insert($fieldsData) {
		if (S::isArray($fieldsData)) {
			$typeDb = $this->_getTypeDB();
			return $typeDb->insert($fieldsData);
		}
	}

	/**
	 * ���ݷ���IDɾ����ϵ����
	 * 
	 * @param int $stid  ���ӷ���ID
	 * @return int ɾ������
	 */
	function deleteByStid($stid) {
		$stid = intval($stid);
		if ($stid < 1) return null;
		$typeDb = $this->_getTypeDB();
		return $typeDb->deleteBySid($stid);
	}

	/**
	 * ��������IDɾ����ϵ����
	 * 
	 * @param int $sid  ����ID
	 * @return int ɾ������
	 */
	function deleteBySid($sid) {
		$sid = intval($sid);
		if ($sid < 1) return null;
		$typeDb = $this->_getTypeDB();
		return $typeDb->deleteBySid($sid);
	}

	/**
	 * ��������ID�����ID
	 * 
	 * @param int $sid ��������ID
	 * @return int ����ID
	 */
	function findStidBySid($sid) {
		$sid = intval($sid);
		if ($sid < 1) return null;
		$typeDb = $this->_getTypeDB();
		$typeNames = $typeDb->findStidBySid($sid);
		foreach ($typeNames as $value) {
			$names[] = $value['stid'];
		}
		return $names;
	}

	/**
	 * ���ݷ���ID������ID
	 * 
	 * @param int $stid ����ID
	 * @return array ������������ID
	 */
	function findSidByStid($stid) {
		$stid = intval($stid);
		if ($stid < 1) return array();
		$typeDb = $this->_getTypeDB();
		$stidsArray = $typeDb->findSidByStid($stid);
		$stids = array();
		foreach ($stidsArray as $sids) {
			$sid[] = $sids['sid'];
		}
		return $sid;
	}
}
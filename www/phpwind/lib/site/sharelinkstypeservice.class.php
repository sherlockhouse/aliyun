<?php
!defined('P_W') && exit('Forbidden');
/**
 * �������ӷ�������
 * @package  PW_SharelinksTypeService
 * @author panjl @2010-11-5
 */
class PW_SharelinksTypeService {

	/**
	 * ����dao
	 * 
	 * @return PW_SharelinkstypeDB
	 */
	function _getTypeDB() {
		return L::loadDB('sharelinkstype', 'site');
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
	 * ɾ��
	 * 
	 * @param int $typeId  ����ID
	 * @return int ɾ������
	 */
	function delete($typeId) {
		$typeId = intval($typeId);
		if ($typeId < 1) return null;
		$typeDb = $this->_getTypeDB();
		return $typeDb->delete($typeId);
	}

	/**
	 * �༭
	 * 
	 * @param array $typeName �������飬�����ݿ��ֶ�Ϊkey
	 * @param int $typeId ����ID
	 * @return int ���±༭�ķ���ID
	 */
	function update($fieldsData,$typeId) {
		if (!intval($typeId)) return null;
		if (S::isArray($fieldsData)) {
			$typeDb = $this->_getTypeDB();
			return $typeDb->update($fieldsData,$typeId);
		}
	}

	/**
	 * ���ݷ���name��stid
	 * 
	 * @param string ��������
	 * @return array ��ѯ�������
	 */
	function getTypeIdByName($name) {
		if ( !$name ) return array();
		$typeDb = $this->_getTypeDB();
		return $typeDb->getTypeIdByName($name);
	}

	/**
	 * ���ݷ���stid��name
	 * 
	 * @param stid ����id
	 * @return array ��ѯ�������
	 */
	function getTypesByStid($stid) {
		$stid = intval($stid);
		if ( !$stid ) return array();
		$typeDb = $this->_getTypeDB();
		return $typeDb->getTypesByStid($stid);
	}

	/**
	 * ��ѯ���ӷ���
	 * 
	 * @return array ��ѯ���
	 */
	function getAllTypes() {
		$typeDb = $this->_getTypeDB();
		return $typeDb->getAllTypes();
	}

	/**
	 * ��ѯ�������ӷ���
	 * 
	 * @return array ��ѯ���
	 */
	function getAllTypesName() {
		$typeDb = $this->_getTypeDB();
		return $typeDb->getAllTypesName();
	}
}

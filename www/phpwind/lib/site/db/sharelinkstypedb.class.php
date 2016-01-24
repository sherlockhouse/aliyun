<?php
!defined('P_W') && exit('Forbidden');

/**
 * �����������ݲ�
 * 
 * @package PW_SharelinkstypeDB
 * @author	panjl @2010-11-5
 */

class PW_SharelinkstypeDB extends BaseDB {
	var $_tableName 	= 	'pw_sharelinkstype';
	var $_primaryKey 	= 	'stid';

	/**
	 * ���
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @return int ����id
	 */
	function insert($fieldsData) {
		if (S::isArray($fieldsData)) {
			return $this->_insert($fieldsData);
		}
	}

	/**
	 * ɾ��
	 * 
	 * @param int $typeId  ����ID
	 * @return int ɾ������
	 */
	function delete($typeId){
		$typeId = intval($typeId);
		if ($typeId < 1) return null;
		return $this->_delete($typeId);
	}

	/**
	 * �༭
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @param int $typeId ����ID
	 * @return int ���±༭�ķ���ID
	 */
	function update($fieldsData,$typeId){
		if (S::isArray($fieldsData)) {
			return $this->_update($fieldsData,$typeId);
		}
	}

	/**
	 * ���ݷ���name��stid
	 * 
	 * @param string ��������
	 * @return int stidֵ
	 */
	function getTypeIdByName($name) {
		if ( !$name ) return null;
		return $this->_db->get_one('SELECT stid FROM ' . $this->_tableName . ' WHERE ifable <> 0 AND name= ' . S::sqlEscape($name));
	}

	/**
	 * ���ݷ���stid��name
	 * 
	 * @param stid ����id
	 * @return array ��ѯ�������
	 */
	function getTypesByStid($stid) {
		$stid = intval($stid);
		if ( !$stid ) return null;
		return $this->_db->get_one('SELECT name FROM ' . $this->_tableName . ' WHERE ifable <> 0 AND stid= ' . S::sqlEscape($stid));
	}

	/**
	 * ��ѯ���õ����ӷ���
	 * 
	 * @return array ��ѯ���
	 */
	function getAllTypes() {
		$query = $this->_db->query('SELECT stid,name,ifable,vieworder FROM ' . $this->_tableName . ' WHERE ifable <> 0 ORDER BY vieworder ASC');
		return $this->_getAllResultFromQuery($query);
	}

	/**
	 * ��ѯ�������ӷ���
	 * 
	 * @return array ��ѯ���
	 */
	function getAllTypesName() {
		$query = $this->_db->query('SELECT * FROM ' . $this->_tableName . ' ORDER BY vieworder ASC');
		return $this->_getAllResultFromQuery($query);
	}
}
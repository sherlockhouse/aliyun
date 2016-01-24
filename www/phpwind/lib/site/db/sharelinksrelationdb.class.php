<?php
!defined('P_W') && exit('Forbidden');

/**
 * �������ӹ�ϵ���ݲ�
 * 
 * @package PW_SharelinkstypeDB
 * @author	panjl
 * @abstract
 */

class PW_SharelinksRelationDB extends BaseDB {
	var $_tableName 	= 	'pw_sharelinksrelation';

	/**
	 * ���
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @return int ����id
	 */
	function insert($fieldsData) {
		if (S::isArray($fieldsData)) {
			pwQuery::insert($this->_tableName, $fieldsData);
			return $this->_db->insert_id();
		}
	}

	/**
	 * ���ݷ���IDɾ������
	 * 
	 * @param int $typeId 
	 * @return int ɾ������
	 */
	function deleteByStid($stid){
		$stid = intval($stid);
		if ($stid < 1) return null;
		pwQuery::delete($this->_tableName, "stid=:stid", array($stid));
		return $this->_db->affected_rows();
	}

	/**
	 * ��������IDɾ������
	 * 
	 * @param int $sid  ����ID
	 * @return int ɾ������
	 */
	function deleteBySid($sid) {
		$sid = intval($sid);
		if ($sid < 1) return null;
		pwQuery::delete($this->_tableName, "sid=:sid", array($sid));
		return $this->_db->affected_rows();
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
		$query = $this->_db->query('SELECT stid FROM ' . $this->_tableName . ' WHERE sid = ' . S::sqlEscape($sid) .' ORDER BY stid ASC');
		return $this->_getAllResultFromQuery($query);
	}

	/**
	 * ������ID������sid
	 * 
	 * @param int $stid ����ID
	 * @return array ��������ID
	 */
	function findSidByStid($stid) {
		$stid = intval($stid);
		if ($stid < 1) return array();
		$query = $this->_db->query('SELECT sid FROM ' . $this->_tableName . ' WHERE stid = ' . S::sqlEscape($stid));
		return $this->_getAllResultFromQuery($query);
	}

}
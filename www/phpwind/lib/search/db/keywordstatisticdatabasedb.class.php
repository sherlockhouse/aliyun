<?php
!defined('P_W') && exit('Forbidden');

/**
 * @package  PW_KeywordStatisticDatabaseDb
 * @author panjl @2011-6-10
 */
class PW_KeywordStatisticDatabaseDb extends BaseDB {
	var $_tableName 	= 	'pw_temp_keywords';
	var $_primaryKey 	= 	'id';

	/**
	 * ���
	 * 
	 * @param array $fieldsData
	 * @return int
	 */
	function insert($fieldsData) {
		if (!S::isArray($fieldsData)) return false;
		return $this->_insert($fieldsData);
	}

	/**
	 * ����ɾ����ǩ
	 * 
	 * @return boolean
	 */
	function deleteAll() {
		return pwQuery::delete($this->_tableName, "", array());
	}
	
	/**
	 * �����ݿ��л��������ʱ��
	 * 
	 * @return array
	 */
	function getLastUpdateTime() {
		return $this->_db->get_value("SELECT MAX(created_time) FROM " . $this->_tableName );
	}

	
	/**
	 * ��ȡ���йؼ���
	 * 
	 * @return array
	 */
	function getAllKeywords() {
		$query = $this->_db->query("SELECT keyword FROM " .  $this->_tableName);
		return $this->_getAllResultFromQuery($query);
	}
}
<?php
!defined('P_W') && exit('Forbidden');

class PW_CustomerFieldDB extends BaseDB {
	
	var $_tableName = "pw_customfield";
	var $_primaryKey = 'id';

	/**
	 * 
	 * �����ֶ�ID��ȡ�ֶ���Ϣ
	 * @param int $id fieldid
	 * @return array
	 */
	function get($id) {
		return $this->_get($id);
	}
	
	function insert($fieldData) {
		return $this->_insert($fieldData);
	}
	
	function update($fieldData, $id) {
		return $this->_update($fieldData, $id);
	}
	
	function delete($fieldId) {
		$fieldId = (int) $fieldId;
		if ($fieldId < 1) return false;
		return $this->_delete($fieldId);
	}
	
	/**
	 * 
	 * �����ֶη�������ȡ�ֶ��б�
	 * @param string $categoryName
	 * @return array
	 */
	function getFieldsByCategoryName($categoryName){
		$query = $this->_db->query("SELECT * FROM " . $this->_tableName . " WHERE category = " . S::sqlEscape($categoryName) . ' AND state = 1 ORDER BY vieworder ASC');
		return $this->_getAllResultFromQuery($query,$this->_primaryKey);
	}
	
	function getFieldByFieldName($fieldName) {
		if (!$fieldName) return false;
		return $this->_db->get_one("SELECT * FROM " . $this->_tableName . " WHERE fieldname = " . S::sqlEscape($fieldName) .' limit 1');
	}
	
	/**
	 * 
	 * ���������״���д�����ȡ�ֶ��б�
	 * @param int $complement
	 * @return array
	 */
	function getFieldsByComplement($complement) {
		$complement = (int) $complement;
		if (!S::inArray($complement, array(0,1,2))) return array();
		$query = $this->_db->query('SELECT * FROM ' . $this->_tableName . ' WHERE complement = ' . S::sqlEscape($complement) . ' AND state = 1 ORDER BY vieworder ASC');
		return $this->_getAllResultFromQuery($query,$this->_primaryKey);
	}
	
	/**
	 * ��ҳȡ�������ֶ���Ϣ
	 * @param int $start ��ʼλ��
	 * @param int $num	 ����
	 * @return array
	 */
	function getAllFieldsWithPages($start, $num) {
		$fields = array();
		$start = (int) $start;
		$num = (int) $num;
		if ($start < 0 || $num < 1) return $fields;
		$query = $this->_db->query('SELECT * FROM ' . $this->_tableName . ' ORDER BY vieworder ASC' . S::sqlLimit($start, $num));
		return $this->_getAllResultFromQuery($query, $this->_primaryKey);
	}
	
	/**
	 * ͳ�������ֶ���Ŀ
	 * @return int
	 */
	function countAllFields() {
		return $this->_db->get_value('SELECT COUNT(*) as total FROM ' . $this->_tableName);
	}
}
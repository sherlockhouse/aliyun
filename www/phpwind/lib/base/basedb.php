<?php
!defined('P_W') && exit('Forbidden');

/**
 * ���ݿ��������
 * 
 * @package DB
 */
class BaseDB {
	/**
	 * ���ݿ����Ӷ���
	 * 
	 * @var DB
	 */
	var $_db = null;
	/**
	 * @var primary ID
	 */
	var $_primaryKey = '';
	var $_tableName = '';
	
	/**
	 * ������
	 */
	function BaseDB() {
		if (!$GLOBALS['db']) PwNewDB();
		$this->_db = $GLOBALS['db'];
	}
	
	function _getConnection() {
		return $GLOBALS['db']; //global
	}
	
	/**
	 * ������µ�sql
	 * 
	 * @see S::sqlSingle
	 * @access protected
	 * @param array $arr ������������
	 * @return string
	 */
	function _getUpdateSqlString($arr) {
		return S::sqlSingle($arr);
	}
	
	/**
	 * ��ȡ��ѯ���
	 * 
	 * @access protected
	 * @param resource $query ���ݿ�������Դ��
	 * @param string|null ���ݽ�����������key��Ϊnull��Ϊ����key
	 * @return array
	 */
	function _getAllResultFromQuery($query, $resultIndexKey = null) {
		$result = array();
		
		if ($resultIndexKey) {
			while ($rt = $this->_db->fetch_array($query)) {
				$result[$rt[$resultIndexKey]] = $rt;
			}
		} else {
			while ($rt = $this->_db->fetch_array($query)) {
				$result[] = $rt;
			}
		}
		return $result;
	}
	
	/**
	 * �������key�Ƿ�Ϊ�Ϸ��ֶ�
	 * 
	 * @access protected
	 * @param array $fieldData ��������
	 * @param array $allowFields ������ֶ�
	 * @return array ���˺������
	 */
	function _checkAllowField($fieldData, $allowFields) {
		foreach ($fieldData as $key => $value) {
			if (!in_array($key, $allowFields)) {
				unset($fieldData[$key]);
			}
		}
		return $fieldData;
	}
	
	/**
	 * ��б�ܹ���
	 * 
	 * @see S::sqlEscape
	 * @access protected
	 * @param mixed $var ����
	 * @return mixed ���˺������
	 */
	function _addSlashes($var) {
		return S::sqlEscape($var);
	}
	
	/**
	 * implode��װ����Ϊsql
	 * 
	 * @see S::sqlImplode
	 * @access protected
	 * @param $arr ��������
	 * @param bool $strip �Ƿ񾭹�stripslashes����
	 */
	function _getImplodeString($arr, $strip = true) {
		return S::sqlImplode($arr, $strip);
	}
	
	/**
	 * ���л�����
	 * 
	 * @access protected
	 * @param mixed $value
	 * @return string
	 */
	function _serialize($value) {
		if (is_array($value)) {
			return serialize($value);
		}
		if (is_string($value) && is_array(unserialize($value))) {
			return $value;
		}
		return '';
	}
	
	/**
	 * �����л�����
	 * 
	 * @access protected
	 * @param string $value
	 * @return mixed
	 */
	function _unserialize($value) {
		if ($value && is_array($tmpValue = unserialize($value))) {
			$value = $tmpValue;
		}
		return $value;
	}
	/**
	 * �����������ݲ�ѯ���
	 * @param $fieldData
	 * @return unknown_type
	 */
	function _insert($fieldData) {
		if (!$this->_check() || !$fieldData) return false;
		//* $this->_db->update("INSERT INTO " . $this->_tableName . " SET " . $this->_getUpdateSqlString($fieldData));
		return pwQuery::insert($this->_tableName, $fieldData);
	}
	/**
	 * �����������ݲ�ѯ���
	 * @param $fieldData
	 * @param $id
	 * @return unknown_type
	 */
	function _update($fieldData, $id) {
		if (!$this->_check() || !$fieldData || $id < 1) return false;
		//* $this->_db->update("UPDATE " . $this->_tableName . " SET " . $this->_getUpdateSqlString($fieldData) . " WHERE " . $this->_primaryKey . "=" . $this->_addSlashes($id) . " LIMIT 1");
		return pwQuery::update($this->_tableName, "{$this->_primaryKey}=:{$this->_primaryKey}", array($id), $fieldData);
	}
	/**
	 * ����ɾ��һ�����ݲ�ѯ���
	 * @param $id
	 * @return unknown_type
	 */
	function _delete($id) {
		if (!$this->_check() || $id < 1) return false;
		//* $this->_db->update("DELETE FROM " . $this->_tableName . " WHERE " . $this->_primaryKey . "=" . $this->_addSlashes($id) . " LIMIT 1");
		return pwQuery::delete($this->_tableName, "{$this->_primaryKey}=:{$this->_primaryKey}", array($id));
	}
	/**
	 * ������ȡһ�����ݲ�ѯ���
	 * @param $id
	 * @param $fields ���ص��ֶ����������','������ȫ��Ϊ'*'
	 * @return unknown_type
	 */
	function _get($id, $fields = '*') {
		if (!$this->_check() || $id < 1) return false;
		return $this->_db->get_one("SELECT $fields FROM " . $this->_tableName . " WHERE " . $this->_primaryKey . "=" . $this->_addSlashes($id) . " LIMIT 1");
	}
	/**
	 * ����ͳ��ȫ�����ݲ�ѯ���
	 * @return unknown_type
	 */
	function _count() {
		if (!$this->_check()) return false;
		$result = $this->_db->get_one("SELECT COUNT(*) as total FROM " . $this->_tableName);
		return $result['total'];
	}
	/**
	 * ˽�ü��������������Ƿ��庯��
	 * @return unknown_type
	 */
	function _check() {
		return (!$this->_tableName || !$this->_primaryKey) ? false : true;
	}
	
	/**
	 * SQL��ѯ��,����LIMIT���
	 *
	 * @param int $start ��ʼ��¼λ��
	 * @param int $num ��ȡ��¼��Ŀ
	 * @return string SQL���
	 */
	function _Limit($start, $num = false){
		return S::sqlLimit($start, $num);
	}
}


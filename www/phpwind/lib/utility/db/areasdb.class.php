<?php
!defined('P_W') && exit('Forbidden');

/**
 * �������ݲ�
 * @package  PW_AreasDB
 * @author phpwind @2010-1-18
 */
class PW_AreasDB extends BaseDB {
	var $_tableName 	= 	'pw_areas';
	var $_primaryKey 	= 	'areaid';

	/**
	 * ���
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @return boolean
	 */
	function insert($fieldsData) {
		if(!S::isArray($fieldsData)) return false;
		return $this->_insert($fieldsData);
	}

	/**
	 * �������
	 * 
	 * @param array $fieldsData
	 * @return boolean
	 */
	function addAreas($fieldsData) {
		if(!S::isArray($fieldsData)) return false;
		$this->_db->update("INSERT INTO " . $this->_tableName . " (name,joinname,parentid,vieworder) VALUES  " . S::sqlMulti($fieldsData));
		return true;
	}
	
	/**
	 * ����
	 * 
	 * @param int $areaid  ����ID
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @return boolean
	 */
	function update($fieldsData,$areaid) {
		$areaid = intval($areaid);
		if($areaid < 1 || !S::isArray($fieldsData)) return false;
		return (bool)$this->_update($fieldsData,$areaid);
	}

	/**
	 * ����ɾ��
	 * 
	 * @param int $areaid  ����ID
	 * @return boolean 
	 */
	function delete($areaid) {
		$areaid = intval($areaid);
		if ($areaid < 1) return false;
		return (bool)$this->_delete($areaid);
	}

	/**
	 * ����ɾ��
	 * 
	 * @param array $areaids  ����IDs
	 * @return boolean
	 */
	function deleteByAreaIds($areaids) {
		if(!S::isArray($areaids)) return false;
		return (bool)pwQuery::delete($this->_tableName, "$this->_primaryKey in(:$this->_primaryKey)", array($areaids));
	}
	
	/**
	 * ���ݵ���ID��ȡ��Ϣ
	 * 
	 * @param int $areaid  ����ID
	 * @return array
	 */
	function getAreaByAreaId($areaid) {
		$areaid = intval($areaid);
		if ($areaid < 1) return array();
		return $this->_db->get_one("SELECT * FROM " . $this->_tableName . " WHERE  " . $this->_primaryKey . " = " . $this->_addSlashes($areaid));
	}
	
	/**
	 * ���ݶ������id��ȡȫ��
	 * @param array $areaids
	 * @return array
	 */
	function getFullAreaByAreaIds($areaids) {
		$result = array();
		$query = $this->_db->query("SELECT areaid,joinname FROM " . $this->_tableName . " WHERE " . $this->_primaryKey . " IN (" . $this->_getImplodeString($areaids) . ")");
		while ($rt = $this->_db->fetch_array($query)) {
				$result[$rt['areaid']] = $rt['joinname'];
		}
		return $result;
	}
	
	/**
	 * ���ݶ������id��ȡ��Ϣ
	 * @param array $areaids
	 * @return array
	 */
	function getAreasByAreadIds($areaids) {
		if (!S::isArray($areaids)) return array();
		$query = $this->_db->query("SELECT * FROM " . $this->_tableName . " WHERE " . $this->_primaryKey . " IN (" . $this->_getImplodeString($areaids) . ")");
		return $this->_getAllResultFromQuery($query, $this->_primaryKey);
	}
	/**
	 * ���ݵ�������ȡ��Ϣ
	 * 
	 * @param string $areaName ������
	 * @return array
	 */
	function getAreaByAreaName($areaName) {
		$areaName = trim($areaName);
		if (!$areaName) return array();
		return $this->_db->get_one("SELECT * FROM " . $this->_tableName . " WHERE name = " . $this->_addSlashes($areaName));
	}
	
	/**
	 * ����parent��ȡ����
	 * 
	 * @param int $parent ��һ��areaid
	 * @return array
	 */
	function getAreaByAreaParent($parentid) {
		$parentid = intval($parentid);
		if ($parentid < 0) return array();
		$query = $this->_db->query("SELECT * FROM  " . $this->_tableName . " WHERE parentid = " . $this->_addSlashes($parentid) . " ORDER BY vieworder ASC");
		return $this->_getAllResultFromQuery($query);
	}

	/**
	 * ��ȡ���ݿ������е���
	 * @return array
	 */
	function getAllAreas() {
		$query = $this->_db->query('SELECT * FROM ' . $this->_tableName . ' ORDER BY vieworder ASC,name ASC');
		return $this->_getAllResultFromQuery($query);
	}
}
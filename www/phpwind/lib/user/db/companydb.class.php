<?php 
!defined('P_W') && exit('Forbidden');

/**
 * ��˾DAO
 * @package PW_SchoolDB
 */
class PW_CompanyDB extends BaseDB{
	
	var $_tableName = 'pw_company';
	var $_primaryKey = 'companyid';

	/**
	 * ���һ����˾
	 * @param string $companyName ��˾����
	 * @return int $companyId
	 */
	function addCompany($companyName){
		if (!$companyName) return false;
		$companyName = trim($companyName);
		$tmpCompanyId = $this->_db->get_value("SELECT `companyid` FROM $this->_tableName WHERE companyname =" . S::sqlEscape($companyName)."");
		if($tmpCompanyId > 0) return $tmpCompanyId;
		return $this->_insert(array('companyname' => $companyName));
	}
	
	/**
	 * ���ݹ�˾ID��ȡ��˾����
	 * @param int $companyId
	 * @return array
	 */
	function getCompanyNameById($companyId){
		$companyId = intval($companyId);
		if($companyId < 1) continue;
		$companyData = $this->_get($companyId);
		return $companyData['companyname'];
	}
	
	/**
	 * ���ݹ�˾ID������ȡ��˾����
	 * @param int $companyIds
	 * @return array
	 */
	function getCompanyNameByIds($companyIds){
		if(!S::isArray($companyIds)) return false;
		$query = $this->_db->query("SELECT companyname FROM  $this->_tableName WHERE companyid in (" . S::sqlImplode($companyIds) . ")");
		return $this->_getAllResultFromQuery($query);
	}
	
	/**
	 * ���ݹ�˾���ƻ�ȡ��˾����
	 * @param int $companyNames
	 * @return array
	 */
	function getCompanyByNames($companyNames){
		if(!S::isArray($companyNames)) return false;
		$query = $this->_db->query("SELECT * FROM  $this->_tableName WHERE companyname in (" . S::sqlImplode($companyNames) . ")");
		return $this->_getAllResultFromQuery($query,'companyid');
	}
	
	/**
	 * ���ݹ�˾���ƻ�ȡ��˾ID
	 * @param string $companyName
	 * @return array
	 */
	function getCompanyIdByName($companyName){
		if (!$companyName) return array();
		return $this->_db->get_value("SELECT companyid FROM  $this->_tableName WHERE companyname = " . S::sqlEscape($companyName) . "");
	}
	
	/**
	 * ���ݹ�˾����������ȡ��˾ID
	 * @param string $companyNames
	 * @return array
	 */
	function getCompanyIdsByName($companyNames){
		if (!S::isArray($companyNames)) return array();
		$query = $this->_db->query("SELECT companyid FROM  $this->_tableName WHERE companyname in (" . S::sqlImplode($companyNames) . ")");
		return $this->_getAllResultFromQuery($query);
	}
	
	/**
	 * �༭��˾����
	 * @param int $companyId ��˾Id
	 * @param string $companyName ��˾����
	 * @return bool
	 */
	function editCompany($companyId,$companyName){
		if (!$companyId || !$companyName) return false;
		return pwQuery::update($this->_tableName, "companyid=:companyid", array($companyId), array('companyname'=>$companyName));
	}
	
	/**
	 * ���ݹ�˾idɾ����¼
	 * @param int $companyId ������˾id
	 * @return bool
	 */
	function deleteCompany($companyId){
		if(!$companyId) return false;
		return pwQuery::delete($this->_tableName, 'companyid=:companyid', array($companyId));
	}
}
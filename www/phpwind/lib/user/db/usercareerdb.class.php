<?php 
!defined('P_W') && exit('Forbidden');

/**
 * ��������DAO
 * @package PW_User_CareerDB
 */
class PW_UserCareerDB extends BaseDB{
	
	var $_tableName = 'pw_user_career';
	var $_companyTable = 'pw_company';
	var $_primaryKey = 'careerid';
	
	/**
	 * ��ӹ�������
	 * @param array $data
	 * @return bool
	 */
	function addCareers($data){
		if (!S::isArray($data)) return false;
		return $this->_db->update("INSERT INTO $this->_tableName (uid,companyid,starttime) VALUES " . S::sqlMulti($data)."");
	}
	
	/**
	 * �����û�ID��ȡ��������
	 * @param int uid
	 * @return array
	 */
	function getCareers($uid){
		$uid = (int) $uid;
		if ($uid < 1) return array();
		$query = $this->_db->query("SELECT uc.*, c.companyname FROM  $this->_tableName uc LEFT JOIN $this->_companyTable c USING(companyid) WHERE uc.uid = " . S::sqlEscape($uid) . ' ORDER BY careerid ASC');
		return $this->_getAllResultFromQuery($query,'careerid');
	}
	
	/**
	 * ���ݹ�˾ID��ȡ�û�id
	 * @param int companyId
	 * @return array
	 */
	function getUserIdsByCompanyId($companyId){
		if (!$companyId) return array();
		$query = $this->_db->query("SELECT uid FROM  $this->_tableName WHERE companyid = " . S::sqlEscape($companyId) . "");
		return $this->_getAllResultFromQuery($query,'uid');
	}
	
	/**
	 * ���ݹ�������ID��ȡһ����������
	 * @param int careerId
	 * @return array
	 */
	function getCareer($careerId){
		if (!$careerId) return array();
		return $this->_get($careerId);
	}
	
	/**
	 * �༭һ����������
	 * @param int $uid �û�id
	 * @param int $companyId ��˾Id
	 * @param int $startTime �빫˾���
	 * @return bool
	 */
	function editCareer($careerId,$companyId,$startTime){
		if (!$careerId || !$companyId || !$startTime) return false;
		return pwQuery::update($this->_tableName, "careerid=:careerid", array($careerId), array('companyid'=>$companyId,'starttime'=>$startTime));
	}
	
	/**
	 * ���ݹ�������id����ɾ����������
	 * @param int careerIds ��������id
	 * @return bool
	 */
	function deleteCareers($careerIds){
		if(!$careerIds) return false;
		return pwQuery::delete($this->_tableName, 'IN(:careerid)', array($careerIds));
	}
	
	/**
	 * ���ݹ�������idɾ��һ����������
	 * @param int careerId ��������id
	 * @return bool
	 */
	function deleteCareer($careerId){
		if(!$careerId) return false;
		return pwQuery::delete($this->_tableName, 'careerid=:careerid', array($careerId));
	}
}
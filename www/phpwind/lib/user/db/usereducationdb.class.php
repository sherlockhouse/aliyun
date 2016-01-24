<?php 
!defined('P_W') && exit('Forbidden');

/**
 * ѧϰ����DAO
 * @package PW_UserEducationDB
 */
class PW_UserEducationDB extends BaseDB{
	
	var $_tableName = 'pw_user_education';
	var $_schoolTable = 'pw_school';
	var $_primaryKey = 'educationid';
	
	/**
	 * ��ӽ�������
	 * @param array $data
	 * @return bool
	 */
	function addEducation($data){
		if (!S::isArray($data)) return false;
		return $this->_insert($data);
	}
	
	/**
	 * ������ӽ�������
	 * @param array $data
	 * @return bool
	 */
	function addEducations($data){
		if (!S::isArray($data)) return false;
		return $this->_db->update("INSERT INTO " . $this->_tableName . "(uid,schoolid,educationlevel,starttime) VALUES " . S::sqlMulti($data));
	}
	
	/**
	 * �����û�ID��ȡ��������
	 * @param int uid
	 * @return array
	 */
	function getEducations($uid){
		if(!$uid) return array();
		$query = $this->_db->query("SELECT ue.*, s.schoolname FROM  $this->_tableName ue LEFT JOIN $this->_schoolTable s USING(schoolid) WHERE ue.uid = " . S::sqlEscape($uid) . " ORDER BY educationid ASC");
		return $this->_getAllResultFromQuery($query,'educationid');
	}
	
	/**
	 * ����ѧУ��ѧԺID��ȡ�û�ID
	 * @param int schoolId
	 * @return array
	 */
	function getUserId($schoolId){
		if(!$schoolId) return array();
		$query = $this->_db->query("SELECT uid FROM  $this->_tableName WHERE schoolid = " . S::sqlEscape($schoolId) . "");
		return $this->_getAllResultFromQuery($query,'uid');
	}
	
	/**
	 * ���ݽ�������ID��ȡһ����������
	 * @param int educationId
	 * @return array
	 */
	function getEducation($educationId){
		if(!$educationId) return array();
		return $this->_get($educationId);
	}
	
	/**
	 * �༭һ����������
	 * @param int $uid �û�id
	 * @param int $schoolId ѧУId
	 * @param int $startTime ��ѧ���
	 * @return bool
	 */
	function editEducation($educationId,$educationLevel,$schoolId,$startTime){
		if (!$educationId || !$educationLevel || !$schoolId || !$startTime) return false;
		return pwQuery::update($this->_tableName, "educationid=:educationid", array($educationId), array('schoolid'=>$schoolId,'educationlevel'=>$educationLevel,'starttime'=>$startTime));
	}
	
	/**
	 * ����ѧУIDɾ������
	 * @param int schoolId ѧУid
	 * @return bool
	 */
	function deleteEduBySchoolId($schoolId){
		if(!$schoolId) return false;
		return pwQuery::delete($this->_tableName, 'schoolid=:schoolid', array($schoolId));
	}
	
	/**
	 * ����ѧУIDɾ����������
	 * @param int schoolIds ѧУid
	 * @return bool
	 */	
	function deleteEduBySchoolIds($schoolIds){
		if(!S::isArray($schoolIds)) return false;
		return pwQuery::delete($this->_tableName, 'schoolid IN (:schoolid)', array($schoolIds));
	}
	
	/**
	 * ���ݽ�������idɾ��һ����������
	 * @param int educationId ��������id
	 * @return bool
	 */
	function deleteEducation($educationId){
		if(!$educationId) return false;
		return pwQuery::delete($this->_tableName, 'educationid=:educationid', array($educationId));
	}
	
	/**
	 * ���ݽ�������idɾ��������������
	 * @param int educationIds ��������id
	 * @return bool
	 */
	function deleteEducations($educationIds){
		if(!S::isArray($educationIds)) return false;
		return pwQuery::delete($this->_tableName, 'educationid IN (:educationid)', array($educationIds));
	}
}
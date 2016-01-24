<?php 
!defined('P_W') && exit('Forbidden');

/**
 * ѧУ����DAO
 * @package PW_SchoolDB
 */
class PW_SchoolDB extends BaseDB{
	
	var $_tableName = 'pw_school';
	var $_primaryKey = 'schoolid';
	
	/**
	 * ���һ��ѧУ
	 * @param string $schoolName ѧУ����
	 * @param int $areaId ����Id
	 * @param int $type ѧУ���ͣ�1ΪСѧ��2Ϊ��ѧ��3Ϊ��ѧ
	 * @return int $Id ѧУ
	 */
	function addSchool($areaId,$schoolName,$type){
		$areaId = intval($areaId);
		$schoolName = trim($schoolName);
		$type = intval($type);
		if (!$schoolName || $areaId < 1 || $type < 0) return false;
		$tmpSchoolId = $this->_db->get_value("SELECT `schoolid` FROM $this->_tableName WHERE areaid =". S::sqlEscape($areaId). "AND schoolname= ". S::sqlEscape($schoolName)."");
		if(count($tmpSchoolId) > 0) return $tmpSchoolId;
		$fieldData = array($areaId,$schoolName,$type);
		return $this->_insert($fieldData);
	}
	
	/**
	 * ������Ӷ���ѧУ
	 * @param string $schoolNames ѧУ����
	 * @return int $Id ѧУ
	 */
	function addSchools($fieldData){
		if (!S::isArray($fieldData)) return false;
		return $this->_db->update("INSERT INTO " . $this->_tableName . " (schoolname,areaid,type) VALUES " . S::sqlMulti($fieldData));
	}
	
	/**
	 * ��������ѧУ���Ƿ��ظ�
	 * @param int $areaIds ����id
	 * @param string $schoolNames ѧУ����
	 * @param array schoolids
	 */
	function checkSchoolNames($areaid,$type,$schoolNames){
		if (!$areaid || !$schoolNames || !$type) return false;
		return $this->_db->get_value("SELECT count(*) FROM $this->_tableName WHERE areaid = " . S::sqlEscape($areaid) . " AND type = " . S::sqlEscape($type) . " AND schoolname IN (" . S::sqlImplode($schoolNames) . ")");
	}
	
	/**
	 * ����һ��ѧУID��ȡ����
	 * @param int schoolId
	 * @return array
	 */
	function getBySchoolId($schoolId){
		$schoolId = intval($schoolId);
		if($schoolId < 1) return false;
		return $this->_get($schoolId);
	}
	
	/**
	 * ���ݶ��ѧԺID��ȡ����
	 * @param array schoolIds
	 * @return array
	 */
	function getSchoolsBySchoolIds($schoolIds){
		if(!S::isArray($schoolIds)) return array();
		$query = $this->_db->query("SELECT * FROM  $this->_tableName WHERE schoolid IN (" . S::sqlImplode($schoolIds) . ")");
		return $this->_getAllResultFromQuery($query,'schoolid');
	}
	
	/**
	 * ���ݵ���ID��ȡѧУ
	 * @param int areaId ����ID
	 * @param int type ѧУ����
	 * @return array ѧУ����
	 */
	function getSchoolByArea($areaId,$type){
		if(!$areaId || !$type) return false;
		$result = array();
		$query = $this->_db->query("SELECT * FROM  $this->_tableName WHERE areaid = " . S::sqlEscape($areaId) . " And type = " . S::sqlEscape($type)."");
		while ($rt = $this->_db->fetch_array($query)) {
			$rt['schoolname'] = str_replace('&nbsp;', ' ', $rt['schoolname']);
			$result[$rt[schoolid]] = $rt;
		}
		return $result;
		//return $this->_getAllResultFromQuery($query,'schoolid');
	}
	
	/**
	 * �༭һ��ѧУ����
	 * @param int id
	 * @param string name
	 * @return bool
	 */
	function editSchool($schoolId,$schoolName){
		if (!$schoolId || !$schoolName) return false;
		return pwQuery::update($this->_tableName, "schoolid=:schoolid", array($schoolId), array('schoolname'=>$schoolName));
	}
	
	/**
	 * ����ѧУIDɾ������
	 * @param int schoolId ѧУid
	 * @return bool
	 */
	function deleteSchool($schoolId){
		if(!$schoolId) return false;
		return pwQuery::delete($this->_tableName, 'schoolid=:schoolid', array($schoolId));
	}
	
	/**
	 * ����ѧУIDɾ����������
	 * @param int schoolIds ѧУid
	 * @return bool
	 */	
	function deleteSchools($schoolIds){
		if(!S::isArray($schoolIds)) return false;
		return pwQuery::delete($this->_tableName, 'schoolid IN (:schoolid)', array($schoolIds));
	}
}
?>
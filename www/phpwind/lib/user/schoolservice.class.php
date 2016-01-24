<?php 
!defined('P_W') && exit('Forbidden');
define('PW_UNIVERSITY', 3); //ѧУ����Ϊ��ѧ
/**
 * ѧУ����service
 * @package PW_School
 */
class PW_SchoolService{
	
	/**
	 * ���һ��ѧУ����ѧУid
	 * @param string $schoolName ѧУ����
	 * @param int $areaId ����Id
	 * @param int $type ѧУ���ͣ�1ΪСѧ��2Ϊ��ѧ��3Ϊ��ѧ
	 * @return int
	 */
	function addSchool($areaId,$type,$schoolName){
		$schoolName = trim($schoolName);
		$schoolName = trim(substrs($schoolName, 32, 'N'), ' &nbsp;');
		$areaId = intval($areaId);
		$type = $type ? intval($type) : 1;
		if (!$schoolName || !$areaId < 0 || $type < 1) return false;
		$schoolDb = $this->_getSchoolDao();
		return $schoolDb->addSchool($areaId,$type,$schoolName);
	}
	
	/**
	 *�������ѧУ����
	 * @param array $data����
	 * @return array $schoolIdsѧУid
	 */
	function addSchools($data){
		if (!S::isArray($data)) return false;
		$fieldData = array();
		$schoolNames = array();
		$schoolDb = $this->_getSchoolDao();
		foreach ($data as $value){
			$value['areaid']  = intval($value['areaid']);
			$value['schoolname']= trim($value['schoolname']);
			$value['schoolname'] = trim(substrs($value['schoolname'], 32, 'N'), ' &nbsp;');
			$schoolNames[] = trim($value['schoolname']);
			$value['type'] = $value['type'] ? intval($value['type']) : 1;
			if(!$value['schoolname'] || $value['areaid'] < 0 || $value['type'] < 0) continue;
			$fieldData[] = $value;
		}
		$schoolIds = $schoolDb->checkSchoolNames((int)$value['areaid'],$value['type'],$schoolNames); 
		if($schoolIds > 0) return $schoolIds;
		return $schoolDb->addSchools($fieldData); 
	}
	
	/**
	 * ����ѧУ���ͺ͵���ID��ȡѧУ
	 * @param int areaId ����ID
	 * @param int type ѧУ����
	 * @return array ѧУ����
	 */
	function getByAreaAndType($areaId,$type){
		$areaId = intval($areaId);
		$type = intval($type);
		if ($areaId < 0 || $type < 1) return false;
		$schoolDb = $this->_getSchoolDao();
		return $schoolDb->getSchoolByArea($areaId,$type);		
	}
	
	/**
	 * ����һ��ѧУID��ȡ����
	 * @param int schoolId
	 * @return array
	 */
	function getBySchoolId($schoolId){
		$schoolId = intval($schoolId);
		if (!$schoolId < 1) return false;
		$schoolDb = $this->_getSchoolDao();
		return $schoolDb->getBySchoolId($schoolId);
	}
	
	/**
	 * ���ݶ��ѧУID��ȡ����
	 * @param array schoolIds
	 * @return array
	 */
	function getBySchoolIds($schoolIds){
		if(!S::isArray($schoolIds)) return array();
		$schoolDb = $this->_getSchoolDao();
		return $schoolDb->getSchoolsBySchoolIds($schoolIds);
	}
	
	/**
	 * ����ѧУID�����Ʊ༭һ��ѧУ���� 
	 * @param int schoolId
	 * @param string newSchoolName
	 * @return bool
	 */
	function editSchool($schoolId,$newSchoolName){
		$schoolId = intval($schoolId);
		$newSchoolName = trim($newSchoolName);
		if (!newSchoolName || $schoolId < 1) return false;
		$schoolDb = $this->_getSchoolDao();
		return $schoolDb->editSchool($schoolId,$newSchoolName);
	}
	
	/**
	 * ����ѧУIDɾ������
	 * @param int $schoolId ѧУid
	 * @param int $type ѧУ����
	 * @return bool
	 */
	function deleteSchool($schoolId,$type){
		$schoolId = intval($schoolId);
		$type = intval($type);
		if($schoolId < 1 || $type < 0) return false;
	  /*if($type = PW_UNIVERSITY){
			$collegeDb = $this->_getCollegeService();
			$deleteCollege = $collegeDb->deleteBySchoolId($schoolId);
		}*/
		$schoolDb = $this->_getSchoolDao();
		$educationDb = $this->_getEducationService();
		$deleteEducation = $educationDb->deleteEduBySchoolId($schoolId);
		return $deleteSchool = $schoolDb->deleteSchool($schoolId);
	}

	/**
	 * ���ݶ��ѧУIDɾ������
	 * @param int schoolIds ѧУid
	 * @return bool
	 */
	function deleteSchools($schoolIds){
		if(!S::isArray($schoolIds)) return false;
		foreach($schoolIds as $value){
			$value['schoolid'] = intval($value['schoolid']);
			if($value['schoolid'] < 1) continue;
		}
		/*$collegeDb = $this->_getCollegeService();
		$deleteCollege = $collegeDb->deleteBySchoolIds($schoolIds);*/
		$schoolDb = $this->_getSchoolDao();
		$educationDb = $this->_getEducationService();
		$deleteEducation = $educationDb->deleteEduBySchoolIds($schoolIds);
		return $deleteSchool = $schoolDb->deleteSchools($schoolIds);
	}
	
	/**
	 * ��װ����������
	 * 
	 * @param int $parentid ��һ��areaid
	 * @param int $defaultValue Ĭ��ѡ��ֵ��id 
	 * @return string
	 */
	function getSchoolsSelectHtml($parentid, $type, $defaultValue = null) {
		$parentid = intval($parentid);
		$type = intval($type);
		if ($parentid < 0 || $type < 0) return null;
		$schools = $this->getByAreaAndType($parentid,$type);
		$schoolsSelect = '';
		foreach ((array)$schools as $value) {
			$selected = ($defaultValue && $value['schoolid'] == $defaultValue) ? 'selected' : '';
			$schoolsSelect .= "<option value=\"$value[schoolid]\" $selected>{$value[schoolname]}</option>\r\n";
		}
		return $schoolsSelect;
	}
	
	function _getSchoolDao(){
		return L::loadDB('School', 'user'); 
	}
	
	/*function _getCollegeService(){
		return L::loadClass('CollegeService', 'user'); 
	}*/
	
	function _getEducationService(){
		return L::loadClass('EducationService', 'user'); 
	}
}
?>
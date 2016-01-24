<?php 
!defined('P_W') && exit('Forbidden');
/**
 * ��������service
 * @package PW_Education
 */
class PW_EducationService{

	var $educationMap = array();
	
	function PW_EducationService(){
		$this->_initEducationMap();
	}
	
	function _initEducationMap(){
		$this->educationMap = array(
			1 => 'Сѧ',
			2 => '����',
			3 => '����',
			4 => '��ѧר��',
			5 => '��ѧ����',
			6 => '˶ʿ',
			7 => '��ʿ',
			8 => '��ʿ��',
		);
	}
	
	/**
	 * ���һ����������
	 * @param int $uid �û�id
	 * @param int $schoolId ѧУId
	 * @param int $educationLevel�����̶�
	 * @param int $startTime ��ѧ���
	 * @return int $educationId����id
	 */
	function addEducation($uid,$schoolId,$educationLevel,$startTime){
		$uid = intval($uid);
		$schoolId = intval($schoolId);
		$eduicationLevel = intval($educationLevel);
		$startTime = intval($startTime);
		if ($uid < 1 || $schoolId < 1 || $educationLevel < 0) return false;
		$fieldData = array('uid'=>$uid, 'schoolid'=>$schoolId, 'educationlevel'=>$eduicationLevel, 'starttime'=>$startTime);
		$eduDb = $this->_getEducationDao();
		return $eduDb->addEducation($fieldData);
	}
	
	/**
	 * ������ӽ�������
	 * @param array $data
	 * @return $educationIds��������Id
	 */
	function addEducations($data){
		if (!S::isArray($data)) return array();
		$fieldData = array();
		foreach ($data as $value){
			$value['uid'] 			= intval($value['uid']);
			$value['schoolid'] 		= intval($value['schoolid']);
			$value['educationlevel']= intval($value['educationlevel']);
			$value['starttime'] 	= intval($value['starttime']);
			if($value['uid'] < 1 || $value['schoolid'] < 1 || $value['educationlevel'] < 0) continue;
			$fieldData[] = $value;
		}
		$eduDb = $this->_getEducationDao();
		return $eduDb->addEducations($fieldData);
	}
	
	/**
	 * �����û�ID��ȡ��������
	 * @param int uid
	 * @return array
	 */
	function getEducationsByUid($uid){
		$uid = intval($uid);
		if($uid < 1) return array();
		$eduDb = $this->_getEducationDao();
		return $eduDb->getEducations($uid);
	}
	
	/**
	 * ����ѧУID��ȡ�û�����
	 * @param int schoolId
	 * @return array
	 */
	function getUserNameBySchoolId($schoolId){
		$schoolId = (int) $schoolId;
		if ($schoolId < 1) return array();
		$eduDb = $this->_getEducationDao();
		$userIds = $eduDb->getUserId($schoolId);
		$userService = L::loadClass('UserService', 'user');
		return $userService->getByUserIds($userIds);
	}
	
	/**
	 * ���ݽ�������ID��ȡһ����������
	 * @param int educationId
	 * @return array
	 */
	function getEducationById($educationId){
		$educationId = intval($educationId);
		if($educationId < 1) return array();
		$eduDb = $this->_getEducationDao();
		return $eduDb->getEducation($educationId);
	}
	
	/**
	 * �༭һ����������
	 * @param int $uid �û�id
	 * @param int $schoolId ѧУId
	 * @param int $educationLevel �����̶�
	 * @param int $startTime ��ѧ���
	 * @return bool
	 */
	function editEducation($educationId,$educationLevel,$schoolId,$startTime){
		$educationId = intval($educationId);
		$educationLevel = intval($educationLevel);
		$schoolId = intval($schoolId);
		$starTime = intval($startTime);
		if($educationId < 1 || $educationLevel < 0 || $schoolId < 1) return false;
		$eduDb = $this->_getEducationDao();
		return $eduDb->editEducation($educationId,$educationLevel,$schoolId,$startTime);
	}
	
	/**
	 * ����ѧУIDɾ������
	 * @param int schoolId ѧУid
	 * @return bool
	 */
	function deleteEduBySchoolId($schoolId){
		$schoolId = intval(schoolId);
		$eduDb = $this->_getEducationDao();
		return $eduDb->deleteEduBySchoolId($schoolId);
	}
	
	/**
	 * ����ѧУIDɾ����������
	 * @param int schoolIds ѧУid
	 * @return bool
	 */	
	function deleteEduBySchools($schoolIds){
		if(!S::isArray($schoolIds)) return false;
		$filteredSchoolIds = array();
		foreach ($schoolIds as $value) {
			$value = (int) $value;
			if ($value < 1) continue;
			$filteredSchoolIds[] = $value;
		}
		if (!S::isArray($filteredSchoolIds)) return false;
		$eduDb = $this->_getEducationDao();
		return $eduDb->deleteEduBySchoolIds($filteredSchoolIds);
	}
	
	
	/**
	 * ���ݽ�������idɾ��һ����������
	 * @param int educationId ��������id
	 * @return bool
	 */
	function deleteEducationById($educationId){
		$educationId = intval($educationId);
		if($educationId < 1) return false;
		$eduDb = $this->_getEducationDao();
		return $eduDb->deleteEducation($educationId);
	}
	
	/**
	 * ɾ��������������
	 * @param array educationIds ��������id
	 * @return bool
	 */
	function deleteEducationByIds($educationIds){
		if (!S::isArray($educationIds)) return false;
		$eduDb = $this->_getEducationDao();
		$filteredEducationIds = array();
		foreach($educationIds as $value){		
			$value = intval($value);
			if($value < 1) continue;
			$filteredEducationIds[] = $value;
		}
		if (!S::isArray($filteredEducationIds)) return false;
		return $educations = $eduDb->deleteEducations($filteredEducationIds);
	}
	
	function _getEducationDao(){
		return L::loadDB('UserEducation', 'user'); 
	}
}
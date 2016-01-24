<?php 
!defined('P_W') && exit('Forbidden');
/**
 * ���������͹�˾service
 * @package PW_Career
 */
class PW_CareerService{

//��˾start

	/**
	 * ���һ����˾
	 * @param string $companyName ��˾����
	 * @return int
	 */
	function addCompany($companyName){
		$companyName = trim($companyName);
		if (!$companyName) return false;
		$companyDb = $this->_getcompanyDao();
		return $companyDb->addCompany($companyName);
		
		
	}
	
	/**
	 * ���ݹ�˾ID��ȡ��˾����
	 * @param int $companyId
	 * @return string companyname
	 */
	function getByCompanyId($companyId){
		$companyId = intval($companyId);
		if ($companyId < 1) return array();
		$companyDb = $this->_getcompanyDao();
		return $companyDb->getCompanyNameById($companyId);
		
	}
	
	/**
	 * ���ݹ�˾���ƻ�ȡ��˾Id
	 * @param string $companyName
	 * @return int companyid
	 */
	function getByCompanyName($companyName){
		$companyName = trim($companyName);
		if (!$companyName) return false;
		$companyDb = $this->_getcompanyDao();
		return $companyDb->getCompanyIdByName($companyName);
		
	}
	
	/**
	 * �༭��˾��Ϣ
	 * @param int $companyId ��˾Id
	 * @param string $companyName ��˾����
	 * @return bool
	 */
	function editCompany($uid,$companyId,$startTime){
		$companyName = trim($companyName);
		$companyId = intval($companyId);
		if ($companyId < 1 || !$companyName) return false;
		$companyDb = $this->_getcompanyDao();
		return $companyDb->editCompany($uid,$companyId,$startTime);
	}
	
	
//��������start
	
	/**
	 * ���һ����������
	 * @param int $uid �û�id
	 * @param string $companyName ��˾����
	 * @param int $startTime �빫˾���
	 * @return int $careerId����id
	 */
	function addCareer($uid,$companyName,$startTime){
		$uid = intval($uid);
		$startTime = intval($startTime);
		$companyName = trim($companyName);
		if (!$companyName || $uid < 1) return false;
		$careerDb = $this->_getCareerDao();
		$companyId = $this->getbyCompanyName($companyName);
		if(!$companyId) return false;
		$fieldData = array();
		$fieldData['uid'] = $uid;
		$fieldData['companyid'] = $companyId;
		$fieldData['starttime'] = $startTime;
		return $careerId = $careerDb->addCareer($fieldData);
	}
	
	/**
	 * ������ӹ�������
	 * @param array $data
	 * @return $careerIds ��������id
	 */
	function addCareers($data){
		if (!S::isArray($data)) return false;
		$companyNames = array();
		foreach ($data as $key=>$value){
			$data[$key]['uid']		   = intval($value['uid']);
			$data[$key]['companyname'] = trim($value['companyname']);
			$data[$key]['starttime']   = intval($value['starttime']);
			$companyNames[] 		   = trim($value['companyname']);
			if($value['uid'] < 1 || !$value['companyname']) unset($data[$key]);
		}
		$companyDb = $this->_getCompanyDao();
		$careerDb = $this->_getCareerDao();
		$companyData = (array)$companyDb->getCompanyByNames($companyNames);
		foreach ($companyData as $k=>$v) {
			$companyData[$k] = $v['companyname'];
		}
		$companyInfo = array_flip($companyData);
		$fields = array();
		foreach ($data as $key=>$value){
			if (!in_array($value['companyname'],$companyData)){
				$companyid = $companyDb->addCompany($data[$key]['companyname']);
			}else{
				$companyid = $companyInfo[$value['companyname']];
			}
			$fields[] = array($value['uid'],$companyid,$value['starttime']);
		}
		return $careerDb->addCareers($fields);
		
	}
	
	/**
	 * �����û�ID��ȡ��������
	 * @param int uid
	 * @return array
	 */
	function getCareersByUid($uid) {
		$uid = (int) $uid;
		if ($uid < 1) return array();
		$careerDb = $this->_getCareerDao();
		return $careerDb->getCareers($uid);
	}
	
	/**
	 * ���ݹ�˾���ƻ�ȡ�û�����
	 * @param int companyId
	 * @return array
	 */
	function getUserCareerName($companyName){
		$companyName = trim($companyName);
		if (!$companyName) return array();
		$companyId = $this->getByCompanyName($companyName);
		$careerDb = $this->_getCareerDao();
		$userIds = $careerDb->getUserIdsByCompanyId($companyId);
		if(!$userIds) return array();
		$userService = L::loadClass('UserService', 'user');
		return $userService->getByUserIds($userIds);
		
	}
	
	/**
	 * ���ݹ�������ID��ȡһ����������
	 * @param int careerId
	 * @return array
	 */
	function getCareerById($careerId){
		$careerId = intval($careerId);
		if($careerId < 1) return array();
		$careerDb = $this->_getCareerDao();
		return $careerDb->getCareer($careerId);
	}
	
	/**
	 * �༭һ����������
	 * @param int $uid �û�id
	 * @param int $companyName ��˾����
	 * @param int $startTime �빫˾���
	 * @return bool
	 */
	function editCareer($careerId,$companyName,$startTime){
		$companyName = trim($companyName);
		$careerId = intval($careerId);
		$starttime = intval($startTime);
		if (!$companyName || $careerId < 1) return false;
		$companyId = $this->getByCompanyName($companyName);
		!$companyId && $companyId = $this->addCompany($companyName);
		if (!$companyId) return false;
		$careerDb = $this->_getCareerDao();
		return $careerDb->editCareer($careerId,$companyId,$startTime);
	}
	
	/**
	 * ���ݹ�������idɾ��һ����������
	 * @param int careerId ��������id
	 * @return bool
	 */
	function deleteCareerById($careerId){
		$careerId = intval($careerId);
		if($careerId < 1) return false;
		$careerDb = $this->_getCareerDao();
		return $careerDb->deleteCareer($careerId);
	}
	
	/**
	 * ɾ��������������
	 * @param array careerIds ��������id
	 * @return bool
	 */
	function deleteCareerByIds($careerIds){
		if (!S::isArray($careerIds)) return false;
		$careerDb = $this->_getCareerDao();
		foreach($careerIds as $value){
			$value['careerid'] = intval($value['careerid']);
			if($value['careerid'] < 1) continue;
		}
		return $careerDb->deleteCareers($value['careerid']);
	}

	function _getCompanyDao(){
		return L::loadDB('company', 'user'); 
	}
	
	function _getCareerDao(){
		return L::loadDB('UserCareer', 'user'); 
	}
}
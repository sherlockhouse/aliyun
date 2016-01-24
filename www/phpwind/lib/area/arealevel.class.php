<?php
!defined('P_W') && exit('Forbidden');
/**
 * �Ż�Ȩ�޷����
 * @author liuhui @2010-3-8
 */
class PW_AreaLevel {
	
	/**
	 * ���Ƶ��ID��ģ�����ƻ�ȡ�û�Ȩ��
	 * @param $userId 
	 * @param $channel
	 * @param $invoke
	 * @return bool
	 */
	function getAreaLevel($userId,$channel,$invoke=''){
		$userId = intval($userId);
		$invoke = trim($invoke);
		if($userId < 1/* || $channel < 1*/){
			return false;
		}
		$userLevel = $this->getAreaUser($userId);
		if(!$userLevel){
			return false;
		}
		if( 1 == $userLevel['super']){
			return $userLevel;
		}
		if( "" == $userLevel['level'] ){
			return false;
		}
		$levle = unserialize($userLevel['level']);
		if( !$levle || !isset($levle[$channel])){
			return false;
		}
		
		if("" != $invoke && ( !isset($levle[$channel]['invokes']) ||  !isset($levle[$channel]['invokes'][$invoke]))){
			return false;
		}
		return $userLevel;
	}
	
	function getAreaLevelByUserId($userId){
		$userId = intval($userId);
		if($userId < 1 ){
			return false;
		}
		$userLevel = $this->getAreaUser($userId);
		if(!$userLevel){
			return false;
		}
		if( 1 == $userLevel['super']){
			return true;
		}
		if( "" == $userLevel['level'] ){
			return false;
		}
		return true;
	}
	
	function language($key){
		$messages = array(
			"username_empty"      =>"��Ǹ,�������û���",
			"uid_empty"           =>"��Ǹ,�û�ID����ȷ",
			"username_not_exist"  =>"��Ǹ,�û���������",
			"add_success"         =>"�����û�Ȩ�����",
			"update_success"      =>"�����û�Ȩ�����",
			"update_fail"         =>"�����û�Ȩ��ʧ��",
			"userlevel_not_exist" =>"��Ǹ,�û�Ȩ�޲�����",
			"delete_success"      =>"ɾ���û�Ȩ�����",
			"do_success"          =>"�������",
			"area_no_level"       =>"��Ǹ,��û�й����ģ���Ȩ��",
			"area_no_pushto"      =>"��Ǹ,��û���Ż�Ƶ���Ĺ���Ȩ�޲��ܽ�������",
			"area_no_invoke"      =>"��Ǹ,ģ�鲻����",
		);
		return isset($messages[$key]) ? $messages[$key] : '';
	}
	
	/**
	 * ���Ӷ���Ż�����Ա
	 * @param $fields
	 * @return true/false
	 */
	function addAreaUsers($fields){
		$haystack = trim($fields['username']);
		if("" == $haystack){
			return array(false,$this->language("username_empty"));
		}
		$mows = explode(",",$haystack);
		$userNames = array();
		foreach($mows as $username){
			$userNames[] = strip_tags(trim($username));
		}
		
		$userService = $this->_getUserService();
		$users = $userService->getByUserNames(array_unique($userNames));
		if(!$users){
			return array(false,$this->language("username_not_exist"));
		}
		$needles = array();
		$needles['hasedit'] = $fields['hasedit'];
		$needles['hasattr'] = $fields['hasattr'];
		$needles['super'] = $fields['super'];
		$needles['level'] = $fields['level'];
		foreach($users as $user){
			$needles['uid'] = $user['uid'];
			$needles['username'] = $user['username'];
			$this->addAreaUser($needles);
		}
		$this->_updateAreaUserConfig();
		return array(true,$this->language("add_success"));
	}
	/**
	 * ����һ���Ż�����Ա
	 * @param $fields
	 * @param $areaLevelDB
	 * @return last_insert_id / false
	 */
	function addAreaUser($fields,$areaLevelDB=false){
		$fields['uid'] = intval($fields['uid']);
		$fields['username'] = trim($fields['username']);
		$fields['hasedit'] = intval($fields['hasedit']);
		$fields['hasattr'] = intval($fields['hasattr']);
		$needles['super'] = intval($fields['super']);
		$fields['level'] = (is_array($fields['level'])) ? serialize($fields['level']) : $fields['level'];
		if( 1 > $fields['uid']){
			return false;
		}
		if($this->getAreaUser($fields['uid'])){
			return false;
		}
		$areaLevelDB = ($areaLevelDB) ? $areaLevelDB : $this->_getAreaLevelDB();
		return $areaLevelDB->add($fields);
	}
	
	function updateAreaUserByUserName($fields,$userName){
		if("" == $userName){
			return array(false,$this->language("username_empty"));
		}
		$userService = $this->_getUserService();
		$userId = $userService->getUserIdByUserName($userName);
		if(!$userId){
			return array(false,$this->language("username_empty"));
		}
		$this->_updateAreaUserConfig();
		return $this->updateAreaUser($fields,$userId);
	}
	/**
	 * ����һ���Ż�����Ա
	 * @param $fields
	 * @param $userId
	 * @return unknown_type
	 */
	function updateAreaUser($fields,$userId,$areaLevelDB=false){
		$fields['hasedit'] = intval($fields['hasedit']);
		$fields['hasattr'] = intval($fields['hasattr']);
		$fields['super'] = intval($fields['super']);
		$fields['level'] = (is_array($fields['level'])) ? serialize($fields['level']) : $fields['level'];
		if( 1 > $userId ){
			return array(false,$this->language("uid_empty"));
		}
		$areaLevelDB = ($areaLevelDB) ? $areaLevelDB : $this->_getAreaLevelDB();
		$areaLevelDB->update($fields,$userId);
		$this->_updateAreaUserConfig();
		return array(true,$this->language("update_success"));
	}
	/**
	 * ɾ��һ���Ż�����Ա
	 * @param $userId
	 * @return unknown_type
	 */
	function deleteAreaUser($userId){
		if( 1 > $userId ){
			return array(false,$this->language("uid_empty"));
		}
		$areaLevelDB = $this->_getAreaLevelDB();
		$areaLevelDB->delete($userId);
		$this->_updateAreaUserConfig();
		return array(true,$this->language("delete_success"));
	}
	/**
	 * ����û�����ȡ�û�Ȩ��
	 * @param $userName
	 * @return unknown_type
	 */
	function getAreaUserByUserName($userName){
		if("" == $userName){
			return array(false,$this->language("username_empty"),'');
		}
		
		$userService = $this->_getUserService();
		$userId = $userService->getUserIdByUserName($userName);
		if(!$userId){
			return array(false,$this->language("username_empty"),'');
		}
		if(!($areaUser = $this->getAreaUser($userId))){
			return array(false,$this->language("userlevel_not_exist"),'');
		}
		return array(true,$this->language("do_success"),$areaUser);
	}
	
	function _updateAreaUserConfig() {
		require_once(R_P.'admin/cache.php');
		$users = $this->getAllAreaUser();
		$temp = array();
		foreach ($users as $value) {
			$temp[] = $value['uid'];
		}
		setConfig('db_portal_admins', $temp);
		updatecache_c();
	}
	
	/**
	 * ��ȡһ���Ż�����Ա
	 * @param $userId
	 * @return unknown_type
	 */
	function getAreaUser($userId){
		if( 1 > $userId ){
			return false;
		}
		$areaLevelDB = $this->_getAreaLevelDB();
		$result = $areaLevelDB->get($userId);
		if ($result) return $result;
		return $this->_getGMLevel();
	}
	/**
	 * ��ʼ��������Ȩ��
	 */
	function _getGMLevel() {
		global $manager,$windid;
		if (!S::inArray($windid, $manager)) return false;
		return array('uid'=>$windid,'hasedit'=>1,'hasattr'=>1,'super'=>1);
	}
	/**
	 * ��ȡ����Ż�����Ա
	 * @param $page
	 * @param $perpage
	 * @return unknown_type
	 */
	function getAreaUsers($page,$perpage){
		if( 1 > $page || 1 > $perpage ){
			return false;
		}
		$areaLevelDB = $this->_getAreaLevelDB();
		return $areaLevelDB->gets($page,$perpage);
	}
	/**
	 * ͳ���Ż�����Ա
	 * @return unknown_type
	 */
	function countAreaUser(){
		$areaLevelDB = $this->_getAreaLevelDB();
		return $areaLevelDB->count();
	}
	function getAllAreaUser() {
		$areaLevelDB = $this->_getAreaLevelDB();
		return $areaLevelDB->getAll();
	}
	/**
	 * �Ż�Ȩ�����ݲ�
	 * @return unknown_type
	 */
	function _getAreaLevelDB() {
        return L::loadDB('AreaLevel', 'area');
    }
    
    /**
     * @return PW_UserService
     */
    function _getUserService() {
    	return L::loadClass('UserService', 'user');
    }
	
}
?>
<?php 
!defined('P_W') && exit('Forbidden');
/**
 * �û�����Ϣservice
 * @package PW_UserGroups
 */
class PW_UserGroups{
	
	/**
	 * �����û������ͻ�ȡ�û���id
	 * 
	 * @param string $type �û�������
	 * @return int gid �û���id
	 */
	function getUserGroupIds($type){
		$type = trim($type);
		if(!$type) return false;
		$userGroupsDb = $this->_getUserGroupsDao(); 
		return $userGroupsDb->getUserGroups($type);
	}
	
	function _getUserGroupsDao(){
		return L::loadDB('usergroups', 'user');
	}
}

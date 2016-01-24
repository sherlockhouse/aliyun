<?php 
/**
 * �û���ط������ļ�
 * 
 * @package Ouserdata
 */
!defined('P_W') && exit('Forbidden');

/**
 * �û���ط������
 * 
 * @package Ouserdata
 */
class PW_Ouserdata {
	
	function get($userId) {
		$ouserdataDb = $this->_getOuserdataDB();
		return 	$ouserdataDb->get($userId);	
		
	}

	/**
	 * �����û���Χ��ɸѡ��¼Ȩ��
	 * 
	 * @param array		$userIds		array(1,2,3,4,......n)	
	 * @return array 	
	 */ 
	function findUserOwritePrivacy($userIds) {
		$ouserdataDb = $this->_getOuserdataDB();
		return 	$ouserdataDb->findUserOwritePrivacy($userIds);	
	}
	
	
	/**
	 * Get PW_OuserdataDB
	 * 
	 * @access protected
	 * @return PW_OuserdataDB
	 */
	function _getOuserdataDB() {
		return L::loadDB('Ouserdata', 'sns');
	}	
	
	
}


?>
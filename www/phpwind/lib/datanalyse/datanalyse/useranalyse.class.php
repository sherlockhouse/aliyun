<?php
!defined('P_W') && exit('Forbidden');
include_once (R_P . 'lib/datanalyse/datanalyse.base.php');

/**
 * �û�����
 * @author yishuo
 */
class PW_Useranalyse extends PW_Datanalyse {
	var $pk = 'tid';
	/* �û��������У��������У��������У��������� */
	var $actions = array('memberOnLine', 'memberThread', 'memberShare', 'memberCredit', 'memberFriend');

	function PW_Useranalyse() {
		$this->__construct();
	}

	function __construct() {
		parent::__construct();
	}

	/**
	 * ������־ID��������־��Ϣ
	 * @return array
	 */
	function _getDataByTags() {
		if (empty($this->tags)) return array();
		
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
		return $userService->getUsersWithMemberDataByUserIds($this->tags);
	}
}
?>
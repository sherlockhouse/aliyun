<?php
!defined('P_W') && exit('Forbidden');
include_once (R_P . 'lib/datanalyse/datanalyse.base.php');

/**
 * ��������
 * @author yishuo
 */
class PW_Threadanalyse extends PW_Datanalyse {
	var $pk = 'tid';
	/* �ظ����У��������У��ղ����У��������� */
	var $actions = array('threadPost', 'threadFav', 'threadShare', 'threadRate');

	function PW_Threadanalyse() {
		$this->__construct();
	}

	function __construct() {
		parent::__construct();
	}

	/**
	 * ��������������
	 * 	1 ��������
	 *  2 ��־����
	 *  3 ��Ƭ����
	 * @return array
	 */
	function _getExtendActions() {
		global $db_ratepower;
		$rateSets = unserialize($db_ratepower);
		if ($rateSets[1]) {
			$rate = L::loadClass('rate', 'rate');
			$_tmp = $rate->getRatePictureHotTypes();
		}
		return array_keys($_tmp);
	}

	/**
	 * ������־ID��������־��Ϣ
	 * @return array
	 */
	function _getDataByTags() {
		if (empty($this->tags)) return array();
		$threadsDB = L::loadDB('threads', 'forum');
		$result = $threadsDB->getsBythreadIds($this->tags);
		return $result;
	}

}
?>
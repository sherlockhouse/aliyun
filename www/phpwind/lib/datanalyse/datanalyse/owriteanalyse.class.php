<?php
!defined('P_W') && exit('Forbidden');
include_once (R_P . 'lib/datanalyse/datanalyse.base.php');

class PW_Owriteanalyse extends PW_Datanalyse {
	var $actions = array(
		'writeNew', 
		'writeComment'
	);

	function PW_Owriteanalyse() {
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
		$cnphotoDB = L::loadDB('owritedata', 'sns');
		$result = $cnphotoDB->getDataByIds($this->tags);
		return $result;
	}

}
?>
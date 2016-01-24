<?php
!defined('P_W') && exit('Forbidden');
include_once (R_P . 'lib/datanalyse/datanalyse.base.php');

class PW_GroupPhotoanalyse extends PW_Datanalyse {
	var $pk = 'pid';
	var $actions = array(
		'groupPicNew', 
		'groupPicComment',
	);

	function PW_Photoanalyse() {
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
		$cnphotoDB = L::loadDB('cnphoto', 'colony');
		$result = $cnphotoDB->getDataByPidsAndPrivate($this->tags);
		return $result;
	}

}
?>
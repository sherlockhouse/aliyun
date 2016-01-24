<?php
!defined('P_W') && exit('Forbidden');
require_once (R_P.'lib/base/systemdata.php');
class PW_TagSource extends SystemData {
	var $_element;
	var $_lang = array(
		'title'	=> '��ǩ����',
	);
	function getSourceData($config,$num) {
		$config = $this->_initConfig($config);
		$element = $this->_getElement();
		return $element->getTags($config['tagsort'],$num);
	}
	
	function getRelateType() {
		return false;
	}

	function getSourceConfig() {
		return array(
			'tagsort' 	=> array(
				'name' 	=> '��ǩ����',
				'type'	=> 'select',
				'value'	=> array(
					'hot'		=>	'���ű�ǩ',
					'new'		=>	'���±�ǩ',
				),
			),
		);
	}
	
	function _getElement() {
		if (!$this->_element) {
			$this->_element = L::loadClass('element');
		}
		return $this->_element;
	}
	
	function _initConfig($config) {
		$temp = array();
		$temp['tagsort'] = $config['tagsort'];

		return $temp;
	}
	
}
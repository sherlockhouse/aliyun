<?php
!defined('P_W') && exit('Forbidden');
require_once (R_P.'lib/base/systemdata.php');
class PW_ForumSource extends SystemData {
	var $_element;
	var $_lang = array(
		'title'	=> '�������',
	);
	function getSourceData($config,$num) {
		$config = $this->_initConfig($config);
		$element = $this->_getElement();
		return $element->forumSort($config['forumsort'],$num);
	}
	
	function getRelateType() {
		return false;
	}
	//article������������topic����������tpost�����շ�����
	function getSourceConfig() {
		return array(
			'forumsort' 	=> array(
				'name' 	=> '�������',
				'type'	=> 'select',
				'value'	=> array(
					'article'	=> '��������',
					'topic'		=> '��������',
					'tpost'		=> '��������',
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
		$temp['forumsort'] = $config['forumsort'];

		return $temp;
	}
	
}
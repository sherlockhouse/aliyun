<?php
!defined('P_W') && exit('Forbidden');
require_once (R_P.'lib/base/systemdata.php');
class PW_SubjectSource extends SystemData {
	var $_element;
	function getSourceData($config,$num) {
		$config = $this->_initConfig($config);
		return $this->_getDataBySortType($config['sorttype'],$config['fid'],$num);
	}
	
	function getRelateType() {
		return 'subject';
	}
	
	function getSourceConfig() {
		return array(
			'sorttype' 	=> array(
				'name' 	=> '��������',
				'type'	=> 'select',
				'value'	=> array(
					'newsubject'	=>'��������',
					'newreply'		=>'���»ظ�',
					'digestsubject'	=>'��������',
					'topsubject'	=>'�ö�����',
					'highlightsubject'	=>'��������',
					'replysortday'	=>'���ջظ�',
					'replysortweek'	=>'һ�ܻظ�',
					'replysort'		=>'�ظ�����',
					'hitsortday'	=>'���յ��',
					'hitsort'		=>'�������',
				),
			),
			'fid'	=> array(
				'name' 	=> 'ѡ����',
				'type' 	=> 'mselect',
				'value'	=> $this->_getForums(),
			),
		);
	}
	
	function _getDataBySortType($sortType,$fid,$num) {
		$element = $this->_getElement();
		switch ($sortType) {
			case 'newsubject':
				return $element->newSubject($fid,$num);
			case 'newreply':
				return $element->newReply($fid,$num);
			case 'digestsubject':
				return $element->digestSubject($fid,$num);
			case 'topsubject':
				return $element->areaTopSubject($fid,$num);
			case 'replysort':
				return $element->replySort($fid,$num);
			case 'hitsort':
				return $element->hitSort($fid,$num);
			case 'replysortday':
				return $element->replySortDay($fid,$num);
			case 'hitsortday':
				return $element->hitSortDay($fid,$num);
			case 'highlightsubject':
				return $element->highLightSubject($fid,$num);
			case 'replysortweek':
				return $element->replySortWeek($fid,$num);
			default :
				return $element->newSubject($fid,$num);
		}
	}
	
	function _getForums() {
		$forumOption = L::loadClass('forumoption');
		return $forumOption->getForums();
	}
	
	function _getElement() {
		if (!$this->_element) {
			$this->_element = L::loadClass('element');
		}
		return $this->_element;
	}
	
	function _initConfig($config) {
		$temp = array();
		$temp['fid'] = $config['fid'];
		$temp['sorttype'] = $config['sorttype'];

		return $temp;
	}
}
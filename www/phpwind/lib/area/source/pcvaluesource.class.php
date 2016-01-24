<?php
/**
 * �Ź��������ݵ��÷��� 
 */

!defined('P_W') && exit('Forbidden');
require_once (R_P . 'lib/base/systemdata.php');

class PW_PcvalueSource extends SystemData {
	
	/**
	 * ����������Ϣ����Ź���������
	 * @param Array $config
	 * @param int $num
	 */
	function getSourceData($config,$num) {
		$config = $this->_initConfig($config);
		return $this->_getDataBySortType($config['sorttype'],$config['fid'],$num);
	}
	
	function _getDataBySortType($sortType,$fid,$num) {
		$pcvalueDao = $this->getPcvalueDao();
		$data = array();
		$fid = $this->_cookFid($fid);
		switch ($sortType) {
			case 'newTop':
				$data = $pcvalueDao->getSourceByPostdate($fid,$num);
				break;
			case 'endtime':
				$data = $pcvalueDao->getSourceByEndtime($fid,$num);
				break;
			case 'replysTop':
				$data = $pcvalueDao->getSourceByReplys($fid,$num);
				break;
			case 'hitsTop':
				$data = $pcvalueDao->getSourceByHits($fid,$num);
				break;
		}
		$data = $this->_cookData($data);
		return $data;
	}
	
	function getSourceConfig() {
		return array(
			'sorttype' => array(
				'name' => '�Ź�����', 
				'type' => 'select', 
				'value' => array(
					'newTop'		=> '�����Ź�',
					'endtime'		=> '������ֹ',
					'replysTop'	=> '�ظ�����',
					'hitsTop'	=> '�������',
				)
			),
			'fid'	=> array(
				'name' 	=> 'ѡ����',
				'type' 	=> 'mselect',
				'value'	=> $this->_getForums(),
			),
		);
	}

	function _cookData($data) {
		foreach ($data as $k=>$v){
			$v['url'] 	= 'read.php?tid='.$v['tid'];
			$v['title'] 	= $v['subject'];
			$v['value'] 	= $v['postdate'];
			$temp = geturl($v['pcattach']);
			$v['image'] = $temp[0] ? $temp[0] : '';
			$v['authorurl']	= 'u.php?uid='.$v['authorid'];
			$v['author'] = $v['anonymous'] ? '����' : $v['author'];
			$v['forumname']	= getForumName($v['fid']);
			$v['forumurl']	= getForumUrl($v['fid']);
			list($v['topictypename'],$v['topictypeurl']) = getTopicType($v['type'],$v['fid']);
			$v['addition'] = $v;
			$data[$k] = $v;
		}
		return $data;
	}
	function _getForums() {
		$forumOption = L::loadClass('forumoption');
		return $forumOption->getForums();
	}
	
	function _initConfig($config) {
		$temp = array();
		$temp['fid'] = $config['fid'];
		$temp['sorttype'] = $config['sorttype'];
		return $temp;
	}
	
	function _cookFid($fid) {
		return getCookedCommonFid($fid);
	}

	function getPcvalueDao(){
		static $sPcvalueDao;
		if(!$sPcvalueDao){
			$sPcvalueDao = L::loadDB('pcvalue', 'forum');
		}
		return $sPcvalueDao;
	}
}

?>
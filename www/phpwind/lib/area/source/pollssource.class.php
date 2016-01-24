<?php
/**
 * ͶƱ�������ݵ��÷��� 
 */

!defined('P_W') && exit('Forbidden');
require_once (R_P . 'lib/base/systemdata.php');

class PW_PollsSource extends SystemData {
	
	/**
	 * ����������Ϣ���ͶƱ��������
	 * @param Array $config
	 * @param int $num
	 */
	function getSourceData($config,$num) {
		$config = $this->_initConfig($config);
		return $this->_getDataBySortType($config['sorttype'],$config['fid'],$num);
	}
	
	function _getDataBySortType($sortType,$fid,$num) {
		$pollsDao = $this->getPollsDao();
		$data = $polls = array();
		$fid = $this->_cookFid($fid);
		switch ($sortType) {
			case 'newTop':
				$data = $pollsDao->getSourceByPostdate($fid,$num);
				break;
			case 'endtime':
				$polls = $pollsDao->getSourceByEndtime($fid,$num);
				$data = $this->getSourceFilterTime($polls);
				break;
			case 'hotTop':
				$polls = $pollsDao->getSourceByVoters($fid,$num);
				$data = $this->getSourceFilterTime($polls);
				break;
			case 'replysTop':
				$polls = $pollsDao->getSourceByReplys($fid,$num);
				$data = $this->getSourceFilterTime($polls);
				break;
			case 'hitsTop':
				$polls = $pollsDao->getSourceByHits($fid,$num);
				$data = $this->getSourceFilterTime($polls);
				break;
		}
		$data = $this->_cookData($data);
		return $data;
	}
	
	function getSourceConfig() {
		return array(
			'sorttype' => array(
				'name' => 'ͶƱ����', 
				'type' => 'select', 
				'value' => array(
					'newTop'		=> '����ͶƱ',
					'endtime'		=> '������ֹ',
					'hotTop'	=> '����ͶƱ',
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
			$v['image']	= '';
			$v['authorurl']	= 'u.php?uid='.$v['authorid'];
			$v['forumname']	= getForumName($v['fid']);
			$v['forumurl']	= getForumUrl($v['fid']);
			if ($v['anonymous']) {
				$v['author'] ='����';
				$v['authorid'] = '';
			}
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

	function getSourceFilterTime($datas,$sorts = null){
		global $timestamp;
		if (!S::isArray($datas)) return array();
		$source = array();
		foreach ($datas as $key => $value) {
			$allowTime = $value['postdate']+$value['timelimit']*24*3600;
			if ($value['timelimit'] && $allowTime < $timestamp) continue;
			$source[$key] = $value;
			array_unshift($source[$key],$allowTime);
		}
		if ($sorts) asort($source);
		return $source;
	}

	function getPollsDao(){
		static $sPollsDao;
		if(!$sPollsDao){
			$sPollsDao = L::loadDB('polls', 'forum');
		}
		return $sPollsDao;
	}
}

?>
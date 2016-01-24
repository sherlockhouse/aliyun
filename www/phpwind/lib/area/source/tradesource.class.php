<?php
/**
 * ��Ʒ�������ݵ��÷��� 
 */

!defined('P_W') && exit('Forbidden');
require_once (R_P . 'lib/base/systemdata.php');

class PW_TradeSource extends SystemData {
	
	/**
	 * 
	 * ����������Ϣ�����Ʒ��������
	 * @param Array $config 
	 * @param int $num
	 */
	function getSourceData($config,$num) {
		$config = $this->_initConfig($config);
		return $this->_getDataBySortType($config['sorttype'],$config['fid'],$num);
	}

	/**
	 * 
	 * �������з����ȡ����
	 * @param string $sortType
	 * @param array $fid ���ID
	 * @param int $num
	 * @return array
	 */
	function _getDataBySortType($sortType,$fid,$num) {
		$tradeDao = $this->getTradeDao();
		$data = array();
		$fid = $this->_cookFid($fid);
		switch ($sortType) {
			case 'newTrade':
				$data = $tradeDao->getSourceByPostdate($fid,$num);
				break;
			case 'saleTop':
				$data = $tradeDao->getSourceBySalenum($fid,$num);
				break;
			case 'replysTop':
				$data = $tradeDao->getSourceByReplys($fid,$num);
				break;
			case 'hitsTop':
				$data = $tradeDao->getSourceByHits($fid,$num);
				break;
		}
		$data = $this->_cookData($data);
		return $data;
	}

	/**
	 * 
	 * ��ȡ����ѡ����Ϣ
	 * @return array
	 */	
	function getSourceConfig() {
		return array(
			'sorttype' => array(
				'name' => '��Ʒ����', 
				'type' => 'select', 
				'value' => array(
					'newTrade'		=> '������Ʒ',
					'saleTop'		=> '��������',
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

	/**
	 * 
	 * ���ݴ���
	 * @param array $data
	 * @return array
	 */
	function _cookData($data) {
		foreach ($data as $k=>$v){
			$v['url'] 	= 'read.php?tid='.$v['tid'];
			$v['title'] 	= $v['subject'];
			$v['value'] 	= $v['postdate'];
			$temp = geturl($v['icon']);
			$v['image'] = $temp[0] ? $temp[0] : '';
			$v['authorurl']	= 'u.php?uid='.$v['authorid'];
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

	function getTradeDao(){
		static $sTradeDao;
		if(!$sTradeDao){
			$sTradeDao = L::loadDB('trade', 'forum');
		}
		return $sTradeDao;
	}
}

?>
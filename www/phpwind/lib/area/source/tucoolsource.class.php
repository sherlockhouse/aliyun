<?php
/**
 *ͼ�����������ݵ��÷��� 
 */

!defined('P_W') && exit('Forbidden');
require_once (R_P . 'lib/base/systemdata.php');

class PW_TuCoolSource extends SystemData {
	
	/**
	 * 
	 * ����������Ϣ���ͼ����������
	 * @param array $config 
	 * @param int $num
	 */
	function getSourceData($config,$num) {
		$config = $this->_initConfig($config);
		return $this->_getDataBySortType($config['sorttype'],$config['fid'],$num);
	}
	
	/**
	 * 
	 * ��ȡ����
	 * @param array $config 
	 * @param array $sortType
	 * @param int $num
	 */
	function _getDataBySortType($sortType,$fid,$num) {
		$tucoolService = $this->getTuCoolService();
		$data = array();
		$fid = $this->filterForums($fid);
		switch ($sortType) {
			case 'new':
				$data = $tucoolService->newTuCoolSort($fid,$num);
				break;
			case 'total':
				$data = $tucoolService->subjectPicNumSort($fid,$num);
				break;
			case 'hitsortday':
				$data = $tucoolService->getTucoolThreadsByHitSortToday($fid,$num);
				break;
			case 'hitsortyesterday':
				$data = $tucoolService->getTucoolThreadsByHitSortYesterday($fid,$num);
				break;
		}
		return $this->_cookData($data) ;
	}
	
	/**
	 * 
	 * ��ȡ����ѡ����Ϣ
	 * @return array
	 */	
	function getSourceConfig() {
		return array(
			'sorttype' => array(
				'name' => 'ͼ������', 
				'type' => 'select', 
				'value' => array(
					'new'		=> '����ͼ����',
					'total'		=> 'ͼƬ������',
					'hitsortday'=> '���յ��',
					'hitsortyesterday'	=> '���յ��',
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
	 * @param int $fid
	 * @return 
	 */
	function _cookData($data) {
		$attachsService = L::loadClass('attachs','forum');
		foreach ($data as $k=>$v){
			$v['url'] 	= 'read.php?tid='.$v['tid'];
			$v['title'] 	= $v['subject'];
			if(!$v['title']){
				unset($data[$k]);
				continue;
			}
			$v['value'] 	= $v['postdate'];
			$v['hits'] 		= $v['hits'] ? $v['hits'] : 0;
			$v['totalnum'] 	= $v['totalnum'] ? $v['totalnum'] : 0;
			$v['collectnum'] = $v['collectnum'] ? $v['collectnum'] : 0;
			$temp = geturl($v['cover'],'show');
			$v['image'] = $temp[0] ? $temp[0] : $GLOBALS['imgpath'] . '/imgdel_h200.jpg';
			$v['forumname']	= getForumName($v['fid']);
			$v['forumurl']	= getForumUrl($v['fid']);
			$v['authorurl']	= 'u.php?uid='.$v['authorid'];
			$v['addition'] = $v;
			$data[$k] = $v;
		}
		return $data;
	}

	/**
	 * 
	 * ��ȡ���
	 * @return array
	 */
	function _getForums() {
		$forumOption = L::loadClass('forumoption');
		$forums = $forumOption->getForums();
		$fids = array();
		foreach ($forums as $key => $v) {
			$foruminfo = L::forum($key);
			if (isset($foruminfo['forumset']['iftucool']) && !$foruminfo['forumset']['iftucool']) continue;
			$fids[$key] = $v;
		}
		return $fids;
	}
	
	/**
	 * 
	 * ��������
	 * @param array 
	 * @return array
	 */
	function _initConfig($config) {
		$temp = array();
		$temp['fid'] = $config['fid'];
		$temp['sorttype'] = $config['sorttype'];

		return $temp;
	}	

	/**
	 * 
	 * ��鴦��
	 * @param string $fid
	 * @return string
	 */
	function _cookFid($fid) {
		return getCookedCommonFid($fid);
	}

	/**
	 * 
	 * ����δ����ͼ����
	 * @param string $fid
	 * @return string
	 */
	function filterForums($fid) {
		$tmpfids = $this->_cookFid($fid);
		$fids = array();
		if ($tmpfids && !S::isArray($tmpfids)) return $tmpfids;
		foreach ((array)$tmpfids as $v) {
			$foruminfo = L::forum($v);
			if (isset($foruminfo['forumset']['iftucool']) && !$foruminfo['forumset']['iftucool']) continue;
			$fids[] = $v;
		}
	 	return S::sqlImplode($fids);
	}
	
	/**
	 * 
	 * ��ȡͼ��service����
	 * @return array
	 */
	function getTuCoolService(){
		static $sTuCoolService;
		if(!$sTuCoolService){
			$sTuCoolService = L::loadClass('tucool', 'forum');
		}
		return $sTuCoolService;
	}
}

?>
<?php
/**
 * ��������ݵ��÷��� 
 */

!defined('P_W') && exit('Forbidden');
require_once (R_P . 'lib/base/systemdata.php');

class PW_ActivitySource extends SystemData {
	var $_lang = array(
		'forumurl' => '�������',
		'author' => '����',
		'authorid' => '����id',
		'postdate' => '����ʱ��',
		'topictypename' => '��������',
		'topictypeurl' => '����id'
	);
	
	/**
	 * 
	 * ����������Ϣ��û��������
	 * @param array $config 
	 * @param int $num
	 */
	function getSourceData($config,$num) {
		$data = array();
		$config = $this->_initConfig($config);
		$data = $this->_getData($config, $num);
		if(empty($data)) return $data;
		return $this->_cookData($data);
	}
	
	/**
	 * 
	 * ��ȡ����ѡ����Ϣ
	 * @return array
	 */	
	function getSourceConfig() {
		return array(
			'fid' => array(
				'name' => 'ѡ����',
				'type' => 'mselect',
				'value' => $this->_getForums()
			),
			'type' => array(
				'name' => '�����',
				'type' => 'mselect',
				'value' => $this->_getActivity()
			),
			'sorttype' => array(
				'name' => '��������',
				'type' => 'select',
				'value' => array(
					'new' => '���»',
					'ending' => '������ֹ',
					'signup' => '��������',
					'reply'  => '�ظ�����',
					'click'	 => '�������'
				)
			)
		);
	}
	
	/**
	 * 
	 * ��������
	 * @param array 
	 * @return array
	 */
	function _initConfig($config) {
		$temp = array();
		$temp['type'] = $config['type'];
		$temp['fid'] = $config['fid'];
		$temp['sorttype'] = $config['sorttype'];
		return $temp;
	}
	
	/**
	 * 
	 * ��ȡ����
	 * @param array $config 
	 * @param int $num
	 */
	function _getData($config, $num) {
		$dao = $this->_getActivityDao();
		$data = array();
		$modelid = $this->_cookModelid($config['type']);
		$fid = $this->_cookFid($config['fid']);
		switch ($config['sorttype']) {
			case 'new' :
				$data = $dao->newActivityTopic($modelid, $fid, $num);
				break;
			case 'ending' :
				$data = $dao->endingActivityTopic($modelid, $fid, $num);
				break;
			case 'signup' :
				$data = $dao->signupActivityTopic($modelid, $fid, $num);
				break;
			case 'reply' :
				$data = $dao->replyActivityTopic($modelid, $fid, $num);
				break;	
			case 'click' :
				$data = $dao->clickActivityTopic($modelid, $fid, $num);
				break;
		}
		return $data;
	}
	
	/**
	 * 
	 * ���ݴ���
	 * @param array $data
	 * @return array
	 */
	function _cookData($data) {
		foreach ($data as $key => $value) {
			$v = array();
			$v['url'] = 'read.php?tid='.$value['tid'];
			$v['title'] = $value['subject'];
			$v['forumname'] = getForumName($value['fid']);
			$v['forumurl'] = getForumUrl($value['fid']);
			$v['author'] = $value['anonymous'] ? '����' :  $value['author'];
			$v['authorid'] = $value['authorid'];
			$v['authorurl'] = 'u.php?uid='.$v['authorid']; 
			for($i = 1; $i < 6; $i++){
				if($value['picture'.$i]){
					$temp = geturl($value['picture'.$i]);
					break;
				}
			}
			$v['image'] = $temp[0] ? $temp[0] : '';
			$v['postdate'] = $value['postdate'];
			$v['topictypename'] = $value['modelname'];
			$v['topictypeurl'] = 'thread.php?fid=' . $value['fid'] . '&actmid=' . $value['actmid'];
			$v['starttime'] = get_date($value['starttime'], 'Y-m-d');
			$v['endtime'] = get_date($value['endtime'], 'Y-m-d');
			$data[$key] = $v;
		}
		return $data;
	}
	
	/**
	 * 
	 * ��ȡ�����
	 * @return array
	 */
	function _getActivity() {
		//* include_once pwCache::getPath(D_P . 'data/bbscache/activity_config.php');
		extract(pwCache::getData(D_P . 'data/bbscache/activity_config.php', false));
		$activityType = array('ȫ������');
		foreach ($activity_catedb as $key => $value) {
			if (!$value['ifable']) continue;
			$activityType['c_' . $key] = $value['name'];
			foreach ($activity_modeldb as $k => $v) {
				if (!$v['ifable'] || $v['actid'] != $key) continue;
				$activityType['m_' . $k] = '--' . $v['name'];
			}
		}
		return $activityType;
	}
	
	/**
	 * 
	 * ��ȡ���
	 * @return array
	 */
	function _getForums() {
		$forumOption = L::loadClass('forumoption');
		return $forumOption->getForums();
	}
	
	/**
	 * 
	 * ����ʹ���
	 * @param array 
	 * @return array
	 */
	function _cookModelid($type) {
		$modelids = array();
		!S::isArray($type) && $type = array($type);
		$activityCate = $this->_getActivityCate();
		foreach ($type as $value) {
			if (!$value) return array();
			list($cateType, $id) = explode('_', $value);
			if ($cateType == 'c' && !empty($activityCate[$id])) {
				foreach ($activityCate[$id] as $v) {
					$modelids[] = (int) $v;
				}
				continue;
			}
			$modelids[] = (int) $id;
		}
		return array_unique(array_filter($modelids));
	}
	
	/**
	 * 
	 * ��鴦��
	 * @param mixed
	 * @return string
	 */
	function _cookFid($fid) {
		return getCookedCommonFid($fid);
	}
	
	/**
	 * 
	 * ��ð�����������ķ���
	 * @return array
	 */
	function _getActivityCate() {
		//* include_once pwCache::getPath(D_P . 'data/bbscache/activity_config.php');
		extract(pwCache::getData(D_P . 'data/bbscache/activity_config.php', false));
		if (empty($activity_catedb) || empty($activity_modeldb)) return array();
		$activityCate = array();
		foreach ($activity_catedb as $key => $value) {
			if (!$value['ifable']) continue;
			foreach ($activity_modeldb as $v) {
				if (!$v['ifable'] || $v['actid'] != $key) continue;
				$activityCate[$key][] = $v['actmid'];
			}
		}
		return $activityCate;
	}
	
	/**
	 * 
	 * ��ȡ�dao����
	 * @return array
	 */
	function _getActivityDao() {
		static $sActivityDao;
		if(!$sActivityDao){
			$sActivityDao = L::loadDB('activity', 'forum');
		}
		return $sActivityDao;
	}
}
?>
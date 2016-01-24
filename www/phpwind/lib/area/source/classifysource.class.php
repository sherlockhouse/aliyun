<?php
/**
 * ������Ϣ�������ݵ��÷��� 
 */

!defined('P_W') && exit('Forbidden');
require_once (R_P . 'lib/base/systemdata.php');

class PW_ClassifySource extends SystemData {
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
	 * ����������Ϣ��÷�����Ϣ��������
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
				'name' => '������Ϣ����',
				'type' => 'mselect',
				'value' => $this->_getClassify()
			),
			'sorttype' => array(
				'name' => '��������',
				'type' => 'select',
				'value' => array(
					'newtopic' 		=> '��������',
					'newreply' 		=> '���»ظ�',
					'toppedtopic' 	=> '�ö�����'
				)
			)
		);
	}
	
	/**
	 * 
	 * ��ȡ����
	 * @param array $config 
	 * @param int $num
	 */
	function _getData($config, $num) {
		$dao = $this->_getClassifyDao();
		$data = array();
		$modelid = $this->_cookModelid($config['type']);
		$fid = $this->_cookFid($config['fid']);
		switch ($config['sorttype']) {
			case 'newtopic' :
				$data = $dao->newClassifyTopic($modelid, $fid, $num);
				break;
			case 'newreply' :
				$data = $dao->newClassifyReply($modelid, $fid, $num);
				break;
			case 'toppedtopic' :
				$data = $dao->toppedClassifyTopic($modelid, $fid, $num);
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
			$v['authorurl'] = 'u.php?uid='.$value['authorid'];
			$v['title'] = $value['subject'];
			$v['forumname'] = getForumName($value['fid']);
			$v['forumurl'] = getForumUrl($value['fid']);
			$v['author'] = $value['anonymous'] ? '����' : $value['author'];
			$v['authorid'] = $value['authorid'];
			$v['postdate'] = $value['postdate'];
			$v['topictypename'] = $value['modelname'];
			$v['topictypeurl'] = 'thread.php?fid=' . $value['fid'] . '&modelid=' . $value['modelid'];
			$data[$key] = $v;
		}
		return $data;
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
	 * ��ȡ������Ϣ����
	 * @return array
	 */
	function _getClassify() {
		$classifyType = array('ȫ������');
		$topiccatedb = $this->_getTopicCatedb();
		$topicmodeldb = $this->_getTopicModeldb();
		foreach ($topiccatedb as $key => $value) {
			if (!$value['ifable']) continue;
			$classifyType['c_' . $key] = $value['name'];
			foreach ($topicmodeldb as $k => $v) {
				if (!$v['ifable'] || $v['cateid'] != $key) continue;
				$classifyType['m_' . $k] = '--' . $v['name'];
			}
		}
		return $classifyType;
	}
	
	function _getTopicCatedb() {
		global $db;
		$topiccatedb = array();
		$query = $db->query("SELECT * FROM pw_topiccate ORDER BY vieworder,cateid");
		while ($rt = $db->fetch_array($query)) {
			$topiccatedb[$rt['cateid']] = $rt;
		}
		return $topiccatedb;
	}
	
	
	function _getTopicModeldb() {
		global $db;
		$topicmodeldb = array();
		$query = $db->query("SELECT * FROM pw_topicmodel ORDER BY vieworder,modelid");
		while ($rt = $db->fetch_array($query)) {
			$topicmodeldb[$rt['modelid']] = $rt;
		}
		return $topicmodeldb;
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
	 * ������Ϣ���ʹ���
	 * @param array 
	 * @return array
	 */
	function _cookModelid ($type) {
		$modelids = array();
		!S::isArray($type) && $type = array($type);
		$topicCate = $this->_getTopicCate();
		foreach ($type as $value) {
			if (!$value) return array();
			list($cateType, $id) = explode('_', $value);
			if ($cateType == 'c' && !empty($topicCate[$id])) {
				foreach ($topicCate[$id] as $v) {
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
	function _getTopicCate() {
		$topiccatedb = $this->_getTopicCatedb();
		$topicmodeldb = $this->_getTopicModeldb();
		if (empty($topiccatedb) || empty($topicmodeldb)) return array();
		$topicCate = array();
		foreach ($topiccatedb as $key => $value) {
			if (!$value['ifable']) continue;
			foreach ($topicmodeldb as $v) {
				if (!$v['ifable'] || $v['cateid'] != $key) continue;
				$topicCate[$key][] = $v['modelid'];
			}
		}
		return $topicCate;
	}
	
	/**
	 * 
	 * ��ȡ������Ϣdao����
	 * @return array
	 */
	function _getClassifyDao() {
		static $sClassifyDao;
		if(!$sClassifyDao){
			$sClassifyDao = L::loadDB('classify', 'forum');
		}
		return $sClassifyDao;
	}
}
?>
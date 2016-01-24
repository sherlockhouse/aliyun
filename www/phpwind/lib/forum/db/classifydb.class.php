<?php
!defined('P_W') && exit('Forbidden');
include_once (R_P . 'lib/base/basedb.php');
//* include_once pwCache::getPath(D_P.'data/bbscache/forum_cache.php');
pwCache::getData(D_P.'data/bbscache/forum_cache.php');

class PW_ClassifyDB extends BaseDB {
	var $_tableName = 'pw_threads';
	
	/**
	 * 
	 * ��ȡ���·���������Ϣ
	 * @param array $modelid ������Ϣid
	 * @param string $fid ���id
	 * @param int $num ���ø���
	 * @return array
	 */
	function newClassifyTopic($modelid, $fid, $num) {
		$posts = array();
		$sqlWhere = $this->_buildCondition($modelid, $fid);
		$query = $this->_db->query('SELECT tid,fid,modelid,author,authorid,subject,postdate,anonymous FROM ' . $this->_tableName . $sqlWhere . ' AND ifshield != 1 AND locked != 2  ORDER BY postdate DESC' . S::sqlLimit(0,$num));
		$posts = $this->_cookData($query);
		return $posts;
	}
	
	/**
	 * 
	 * ��ȡ���»ظ�������Ϣ
	 * @param array $modelid ������Ϣid
	 * @param string $fid ���id
	 * @param int $num ���ø���
	 * @return array
	 */
	function newClassifyReply($modelid, $fid, $num) {
		$posts = array();
		$sqlWhere = $this->_buildCondition($modelid, $fid);
		$query = $this->_db->query('SELECT tid,fid,modelid,author,authorid,subject,postdate,anonymous FROM ' . $this->_tableName . $sqlWhere . ' AND ifshield != 1 AND locked != 2  ORDER BY lastpost DESC' . S::sqlLimit(0,$num));
		$posts = $this->_cookData($query);
		return $posts;
	}
	
	/**
	 * 
	 * ��ȡ�����ö�������Ϣ
	 * @param array $modelid ������Ϣid
	 * @param string $fid ���id
	 * @param int $num ���ø���
	 * @return array
	 */
	function toppedClassifyTopic($modelid, $fid, $num) {
		$posts = array();
		$sqlWhere = $this->_buildCondition($modelid, $fid);
		$sqlWhere .= ' AND topped != 0';
		$query = $this->_db->query('SELECT tid,fid,modelid,author,authorid,subject,postdate,anonymous FROM ' . $this->_tableName . $sqlWhere . ' AND ifshield != 1 AND locked != 2  ORDER BY lastpost DESC' . S::sqlLimit(0,$num));
		$posts = $this->_cookData($query);
		return $posts;
	}
	
	/**
	 * 
	 * ��װ��������
	 * @param array $modelid ������Ϣid
	 * @param string $fid ���id
	 * @return string
	 */
	function _buildCondition($modelid, $fid) {
		$sqlWhere = ' WHERE modelid != 0';
		!empty($modelid) && $sqlWhere .= ' AND modelid IN (' . S::sqlImplode($modelid) . ')';
		$fid && $sqlWhere .= ' AND fid IN (' . $fid . ')';
		$blackListedTids = $this->_getBlackListedTids();
		$blackListedTids && $sqlWhere .= ' AND tid NOT IN (' . $blackListedTids . ')';
		return $sqlWhere;
	}
	
	/**
	 * 
	 * ��ȡ��ѯ���
	 * @param unknown $query ��ѯ���
	 * @return array
	 */
	function _cookData($query) {
		//* include pwCache::getPath(D_P . 'data/bbscache/topic_config.php');
		extract(pwCache::getData(D_P . 'data/bbscache/topic_config.php', false));
		while ($row = $this->_db->fetch_array($query)) {
			$row['modelname'] = $topicmodeldb[$row['modelid']]['name'];
			$posts[] = $row;
		}
		return $posts;
	}
	
	function _getBlackListedTids() {
		global $db_tidblacklist;
		return $db_tidblacklist;
	}
}
?>
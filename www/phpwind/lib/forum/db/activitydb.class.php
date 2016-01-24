<?php
!defined('P_W') && exit('Forbidden');
include_once (R_P . 'lib/base/basedb.php');

class PW_ActivityDB extends BaseDB {
	var $_tableName = 'pw_activitydefaultvalue';
	
	/**
	 * 
	 * ��ȡ���·����
	 * @param array $modelid �id
	 * @param string $fid ���id
	 * @param int $num ���ø���
	 * @return array
	 */
	function newActivityTopic($modelid, $fid, $num) {
		$posts = array();
		$sqlWhere = $this->_buildCondition($modelid, $fid);
		$_sql = "SELECT dv.tid,dv.fid,dv.actmid,dv.starttime,dv.endtime,dv.picture1,dv.picture2,dv.picture3,dv.picture4,dv.picture5,t.author,t.authorid,t.subject,t.postdate,t.anonymous FROM $this->_tableName dv LEFT JOIN pw_threads t USING(tid)$sqlWhere AND t.ifshield != 1 AND t.locked != 2  ORDER BY t.postdate DESC" . S::sqlLimit(0,$num);
		$query = $this->_db->query($_sql);
		$posts = $this->_cookData($query);
		return $posts;
	}
	
	/**
	 * 
	 * ��ȡ������ֹ�
	 * @param array $modelid �id
	 * @param string $fid ���id
	 * @param int $num ���ø���
	 * @return array
	 */
	function endingActivityTopic($modelid, $fid, $num) {
		$posts = array();
		$sqlWhere = $this->_buildCondition($modelid, $fid);
		$_sql = "SELECT dv.tid,dv.fid,dv.actmid,dv.starttime,dv.endtime,dv.picture1,t.author,t.authorid,t.subject,t.postdate,t.anonymous FROM $this->_tableName dv LEFT JOIN pw_threads t USING(tid)$sqlWhere AND t.ifshield != 1 AND t.locked != 2  ORDER BY dv.endtime ASC" . S::sqlLimit(0,$num);
		$query = $this->_db->query($_sql);
		$posts = $this->_cookData($query);
		return $posts;
	}
	
	/**
	 * 
	 * ��ȡ�,��������������
	 * @param array $modelid �id
	 * @param string $fid ���id
	 * @param int $num ���ø���
	 * @return array
	 */
	function signupActivityTopic($modelid, $fid, $num) {
		$posts = array();
		$sqlWhere = $this->_buildCondition($modelid, $fid);
		$_sql = "SELECT dv.tid,dv.fid,dv.actmid,dv.starttime,dv.endtime,dv.picture1,t.author,t.authorid,t.subject,t.postdate,t.anonymous,sum(am.signupnum) AS totalsum FROM $this->_tableName dv LEFT JOIN pw_threads t USING(tid) LEFT JOIN pw_activitymembers am USING(tid)$sqlWhere AND t.ifshield != 1 AND t.locked != 2  GROUP BY dv.tid ORDER BY totalsum DESC" . S::sqlLimit(0,$num);
		$query = $this->_db->query($_sql);
		$posts = $this->_cookData($query);
		return $posts;
	}
	
	/**
	 * 
	 * ��ȡ�,���ظ�����
	 * @param array $modelid �id
	 * @param string $fid ���id
	 * @param int $num ���ø���
	 * @return array
	 */
	function replyActivityTopic($modelid, $fid, $num) {
		$posts = array();
		$sqlWhere = $this->_buildCondition($modelid, $fid);
		$_sql = "SELECT dv.tid,dv.fid,dv.actmid,dv.starttime,dv.endtime,dv.picture1,t.author,t.authorid,t.subject,t.postdate,t.anonymous FROM $this->_tableName dv LEFT JOIN pw_threads t USING(tid)$sqlWhere AND t.ifshield != 1 AND t.locked != 2  ORDER BY t.replies DESC" . S::sqlLimit(0,$num);
		$query = $this->_db->query($_sql);
		$posts = $this->_cookData($query);
		return $posts;
	}
	
	/**
	 * 
	 * ��ȡ�,���������
	 * @param array $modelid �id
	 * @param string $fid ���id
	 * @param int $num ���ø���
	 * @return array
	 */
	function clickActivityTopic($modelid, $fid, $num) {
		$posts = array();
		$sqlWhere = $this->_buildCondition($modelid, $fid);
		$_sql = "SELECT dv.tid,dv.fid,dv.actmid,dv.starttime,dv.endtime,dv.picture1,t.author,t.authorid,t.subject,t.postdate,t.anonymous FROM $this->_tableName dv LEFT JOIN pw_threads t USING(tid)$sqlWhere AND t.ifshield != 1 AND t.locked != 2  ORDER BY t.hits DESC" . S::sqlLimit(0,$num);
		$query = $this->_db->query($_sql);
		$posts = $this->_cookData($query);
		return $posts;
	}
	
	/**
	 * 
	 * ��װ��������
	 * @param array $modelid �id
	 * @param string $fid ���id
	 * @return string
	 */
	function _buildCondition($modelid, $fid) {
		global $timestamp;
		$sqlWhere = ' WHERE dv.endtime >= ' . $timestamp;
		!empty($modelid) && $sqlWhere .= ' AND dv.actmid IN (' . S::sqlImplode($modelid) . ')';
		$fid && $sqlWhere .= ' AND dv.fid IN (' . $fid . ')';
		$sqlWhere .= ' AND t.ifcheck = 1  AND t.fid != 0' ;
		$blackListedTids = $this->_getBlackListedTids();
		$blackListedTids && $sqlWhere .= ' AND t.tid NOT IN (' . $blackListedTids . ')';
		return $sqlWhere;
	}
	
	/**
	 * 
	 * ��ȡ��ѯ���
	 * @param unknown $query ��ѯ���
	 * @return array
	 */
	function _cookData($query) {
		//* include pwCache::getPath(D_P . 'data/bbscache/activity_config.php');
		extract(pwCache::getData(D_P . 'data/bbscache/activity_config.php', false));
		while ($row = $this->_db->fetch_array($query)) {
			$row['modelname'] = $activity_modeldb[$row['actmid']]['name'];
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
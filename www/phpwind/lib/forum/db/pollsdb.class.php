<?php
!defined('P_W') && exit('Forbidden');
@include_once (R_P . 'lib/base/basedb.php');

class PW_PollsDB extends BaseDB {
	var $_tableName = 'pw_polls';
	
	/**
	 * 
	 * ��ȡ����ͶƱ������
	 * @param array $fid ���ID
	 * @param int $num
	 * @param string $order
	 * @return array
	 */
	function getSourceByPostdate($fid,$num,$order = 'DESC'){
		$num = intval($num);
		$sqlAdd = $this->buildConditions($fid);
		$order = strtoupper($order);
		$order !== 'DESC' && $order = 'ASC';
		$query = $this->_db->query("SELECT t.tid,t.fid,t.author,t.authorid,t.subject,t.type,t.postdate,t.hits,t.replies,t.anonymous FROM $this->_tableName p LEFT JOIN pw_threads t USING(tid) $sqlAdd AND t.ifshield != 1 AND t.locked != 2  ORDER BY t.postdate $order " . S::sqlLimit($num));
		return $this->_getAllResultFromQuery ( $query );
	}
	
	/**
	 * 
	 * ��������ֹ��ȡͶƱ������
	 * @param array $fid ���ID
	 * @param int $num
	 * @param string $order
	 * @return array
	 */
	function getSourceByEndtime($fid,$num = 100){
		$num = intval($num);
		$sqlAdd = $this->buildConditions($fid);
		//�趨��ֹʱ�� ��
		$query = $this->_db->query("SELECT (t.postdate+p.timelimit*86400) AS endtime,p.timelimit,t.tid,t.fid,t.author,t.authorid,t.subject,t.type,t.postdate,t.hits,t.replies,t.anonymous FROM $this->_tableName p LEFT JOIN pw_threads t USING(tid) $sqlAdd AND t.ifshield != 1 AND t.locked != 2 AND p.timelimit>0 ORDER BY endtime".S::sqlLimit($num));
		$data = $this->_getAllResultFromQuery ( $query );
		$count = count($data);
		if ($count < $num) {
			//δ�趨��ֹʱ���
			$limit = $num - $count;
			$query = $this->_db->query("SELECT p.timelimit,t.tid,t.fid,t.author,t.authorid,t.subject,t.type,t.postdate,t.hits,t.replies,t.anonymous FROM $this->_tableName p LEFT JOIN pw_threads t USING(tid) $sqlAdd AND t.ifshield != 1 AND t.locked != 2 AND p.timelimit=0 ORDER BY pollid ASC ".S::sqlLimit($limit));
			$data2 = $this->_getAllResultFromQuery ( $query );
			$data2 && $data = array_merge($data,$data2);
		} 
		return $data;
	}
	
	/**
	 * 
	 * ������ͶƱ��ȡͶƱ������
	 * @param array $fid ���ID
	 * @param int $num
	 * @param string $order
	 * @return array
	 */
	function getSourceByVoters($fid,$num = 10,$order = 'DESC'){
		$num = intval($num);
		$sqlAdd = $this->buildConditions($fid);
		$order = strtoupper($order);
		$order !== 'DESC' && $order = 'ASC';
		$query = $this->_db->query("SELECT p.timelimit,t.tid,t.fid,t.author,t.authorid,t.subject,t.type,t.postdate,t.hits,t.replies,t.anonymous FROM $this->_tableName p LEFT JOIN pw_threads t USING(tid) $sqlAdd AND t.ifshield != 1 AND t.locked != 2  ORDER BY p.voters $order" . S::sqlLimit($num));
		return $this->_getAllResultFromQuery ( $query );
	}

	/**
	 * 
	 * ���ظ�����ȡͶƱ������
	 * @param array $fid ���ID
	 * @param int $num
	 * @param string $order
	 * @return array
	 */
	function getSourceByReplys($fid,$num,$order = 'DESC'){
		$num = intval($num);
		$sqlAdd = $this->buildConditions($fid);
		$order = strtoupper($order);
		$order !== 'DESC' && $order = 'ASC';
		$query = $this->_db->query("SELECT p.timelimit,t.tid,t.fid,t.author,t.authorid,t.subject,t.type,t.postdate,t.hits,t.replies,t.anonymous FROM $this->_tableName p LEFT JOIN pw_threads t USING(tid) $sqlAdd AND t.ifshield != 1 AND t.locked != 2  ORDER BY t.replies $order " . S::sqlLimit($num));
		return $this->_getAllResultFromQuery ( $query );
	}

	/**
	 * 
	 * ���������ȡͶƱ������
	 * @param array $fid ���ID
	 * @param int $num
	 * @param string $order
	 * @return array
	 */
	function getSourceByHits($fid,$num,$order = 'DESC'){
		$num = intval($num);
		$sqlAdd = $this->buildConditions($fid);
		$order = strtoupper($order);
		$order !== 'DESC' && $order = 'ASC';
		$query = $this->_db->query("SELECT p.timelimit,t.tid,t.fid,t.author,t.authorid,t.subject,t.type,t.postdate,t.hits,t.replies,t.anonymous FROM $this->_tableName p LEFT JOIN pw_threads t USING(tid) $sqlAdd AND t.ifshield != 1 AND t.locked != 2  ORDER BY t.hits $order " . S::sqlLimit($num));
		return $this->_getAllResultFromQuery ( $query );
	}
	
	/**
	 * 
	 * ��װ��������
	 * @param string $fid ���id
	 * @return string
	 */
	function buildConditions($fid) {
		global $timestamp;
		$sqlAdd = ' WHERE (p.timelimit=0 or t.postdate + p.timelimit*86400 >= ' . $timestamp .')';
		if ($fid) $sqlAdd .= " AND t.fid IN ($fid)";
		$sqlAdd .= ' AND t.ifcheck = 1  AND t.fid != 0' ;
		$blackListedTids = $this->_getBlackListedTids();
		$blackListedTids && $sqlAdd .= ' AND t.tid NOT IN (' . $blackListedTids . ')';
		return $sqlAdd;
	}
	
	function _getBlackListedTids() {
		global $db_tidblacklist;
		return $db_tidblacklist;
	}
}

?>
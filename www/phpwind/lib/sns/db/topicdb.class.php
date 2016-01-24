<?php
!defined('P_W') && exit('Forbidden');
/**
 * ���⹦�����ݿ�DAO����
 * @package PW_TopicDB
 */
class PW_TopicDB extends BaseDB{
	
	var $_tableName = 'pw_weibo_topics';
	var $_primaryKey = 'topicid';
	
	var $_tableRelations = 'pw_weibo_topicrelations';	
	var $_tableAttention = 'pw_weibo_topicattention';	
	
	function insert($fieldData){
		return $this->_insert($fieldData);
	}

	function update($fieldData,$id){
		return $this->_update($fieldData,$id);
	}

	function delete($id){
		return $this->_delete($id);
	}

	function get($id){
		return $this->_get($id);
	}

	function count(){
		return $this->_count();
	}
	
	function limit($start,$num){
		return $this->_Limit($start, $num);
	}
	
	
	/**
	 * ���滰��
	 * @param string $topicName 
	 * @return int $topicId
	 */
	function addTopic($topicName){
		if (!topicName || $topicName == '# #') return false;
		$topicId = $this->_db->get_value("SELECT `topicid` FROM $this->_tableName WHERE `topicname`=" . S::sqlEscape($topicName));
		if (!$topicId) {
			$fieldData = array();
			$fieldData['topicname'] = $topicName;
			$fieldData['crtime'] = $fieldData['lasttime'] = $GLOBALS['timestamp'];
			$topicId = $this->_insert($fieldData);
		}
		return $topicId;
	}
	
	/**
	 * ����id��ȡ��������
	 * @param int $topicId ����id
	 * @return array
	 */
	function getTopic($topicId){
		if(!$topicId) return array();
		return $this->_db->get_one("SELECT * FROM  $this->_tableName WHERE topicid = " . $this->_addSlashes($topicId));
	}
	
	/**
	 * ����id��ȡ��������
	 * @param array $topicid ����id
	 * @return array
	 */
	function getTopics($topicids){
		if(!S::isArray($topicids)) return array();
		$query = $this->_db->query("SELECT * FROM  $this->_tableName WHERE topicid IN (" . S::sqlImplode($topicids) . ")");
		return $this->_getAllResultFromQuery($query,'topicid');
	}

	/**
	 * ���ݻ������ƻ�ȡ����
	 * @param string $topicname ��������
	 * @return array
	 */
	function getTopicByName($topicname){
		if(!$topicname) return array(); 
		return $this->_db->get_one("SELECT * FROM  $this->_tableName WHERE topicname = " . $this->_addSlashes($topicname));
	}
	
	/**
	 * ���ݻ������ƻ�ȡ����
	 * @param array $topicnames ��������
	 * @return array
	 */
	function getTopicByNames($topicnames){
		if(!S::isArray($topicnames)) return array(); 
		$query = $this->_db->query("SELECT * FROM  $this->_tableName WHERE topicname IN (" . S::sqlImplode($topicnames) .")" );
		return $this->_getAllResultFromQuery($query);
	}
	
	/**
	 * �������������Ż���񻺴�ʱ���ȡ��������Ϊ���ŵĻ���
	 * @param int $num ��ʾ�Ļ�����
	 * @param int $days ���Ż���񻺴�ʱ�䣨�죩
	 * @return array
	 */
	function getHotTopics($num = 10,$days = 7){
		$num = intval($num);
		$days = intval($days);
		if (!$num || !$days) return array();
		$query = $this->_db->query("SELECT t.*,COUNT(r.mid) AS counts FROM " . $this->_tableRelations . " AS r RIGHT JOIN " . $this->_tableName . ' AS t USING(topicid) WHERE r.crtime > ' . intval($days) . ' AND t.ifhot=1 GROUP BY r.topicid ORDER BY counts DESC' . $this->_limit($num));
		return $this->_getAllResultFromQuery($query, 'topicid');
	}
	
	/**
	 * ���ݻ���id���ӻ���ʹ�ô���
	 * @param int topicId ����ID
	 * @param int numʹ�ô���
	 * @return bool
	 */
	function updateTopicNum($topicId,$num){
		if(!$topicId || !$num) return false;
		return $this->_db->update("update " . $this->_tableName . " SET num = num + " . $num . " WHERE topicid = " . $this->_addSlashes($topicId));
	}
	
	/**
	 * ���ݻ���id���ٻ���ʹ�ô���
	 * @param int topicId ����ID
	 * @param int numʹ�ô���
	 * @return bool
	 */
	function decreaseTopicNum($topicId){
		!is_array($topicId) && $topicId = array();
		if(!$topicId) return false;
		return $this->_db->update("UPDATE $this->_tableName SET num=num-1 WHERE topicid IN (" . S::sqlImplode($topicId) .") AND num>0" );
	}
	
	/**
	 * ���ݻ���idɾ������
	 * @param int|array $topicids ����ID
	 * @return bool
	 */
	function deleteTopicById($topicIds){
		!is_array($topicIds) && $topicIds = array();
		if(!$topicIds) return false;
		return pwQuery::delete($this->_tableName, 'topicid IN (:topicid)', array($topicIds));
	}
	
	/**
	 * ͳ�ƻ�����
	 * 
	 * @param int $topicid
	 * @return int
	 */
	function countTopics($topicid) {
		return $this->_count();
	}
	
	/**
	 * �������Ż���
	 * 
	 * @param array $topicids
	 * @return boolean
	 */
	function setHotByTopicids($topicids,$ifhot) {
		if(!S::isArray($topicids)) return array();
		pwQuery::update($this->_tableName, "topicid in(:topicid)", array($topicids), array('ifhot'=>$ifhot));
		return $this->_db->affected_rows();
	}
	
	/**
	 * ��̨��������
	 * @param array $sqlAdd
	 * @param array $querySql
	 * @return array
	 */
	function adminSearchTopic($sqlAdd,$querySql){
		if(!$querySql) return array();
		$sql = 'SELECT count(*) FROM '.$this->_tableName.' WHERE 1 '.$sqlAdd;
		$total =  $this->_db->get_value($sql);
		$sql = 'SELECT * FROM '.$this->_tableName.' WHERE 1 '.$querySql;
		$query = $this->_db->query($sql);
		$result =  $this->_getAllResultFromQuery($query);
		return array($total,$result);
	}
	
	/**
	 * 
	 * ��ȡ�û���ע�Ļ���
	 * @param int $uid
	 * @param int $num
	 */
	function getUserAttentionTopics($uid, $offset=0 ,$num = 10) {
		$uid = intval($uid);
		$offset = intval($offset);
		$num = intval($num);
		if (!$uid || !$num) return array();
		$sql = 'SELECT b.topicname,b.topicid FROM ' . $this->_tableAttention . ' a LEFT JOIN ' . $this->_tableName . ' b USING(topicid) WHERE a.userid=' . $uid . ' ORDER BY a.crtime DESC ' . $this->_Limit($offset,$num);
		$query = $this->_db->query($sql);
		return $this->_getAllResultFromQuery($query);
	}
}
?>

<?php
!defined('P_W') && exit('Forbidden');

class PW_AttentionDB extends BaseDB {

	var $_tableName = "pw_attention";
	var $_primaryKey = 'uid';
	
	function insert($fieldData) {
		return $this->_insert($fieldData);
	}
		
	/**
	 * �����û��͹�ע����,�ҳ������Ϣ
	 * 
	 * @param int	$uid	��ע����
	 * @param int	$friendid	����ע��uid
	 * @return	
	 */
	function getUserByUidAndFriendid($uid, $friendid) {//fixed
		$sql = "SELECT * FROM " . $this->_tableName . " WHERE uid=" . $this->_addSlashes($uid) . " AND friendid=" . $this->_addSlashes($friendid);
		return $this->_db->get_one($sql);
	}
	
	/**
	 * �����û��͹�ע����ɾ����ע��¼
	 * 
	 * 
	 * @param int $uid	�û�
	 * @param int $friendid	����ע��uid
	 */
	function delByUidAndFriendid($uid, $friendid) {
		$sql = "DELETE FROM " . $this->_tableName . " WHERE uid=" . $this->_addSlashes($uid) . " AND friendid=" . $this->_addSlashes($friendid);
		return $this->_db->update($sql);
	}
		
	/**
	 * ����ҹ�ע����/count
	 * 
	 * @param int $uid
	 */
	function countFollows($uid) {//fixed
		return $this->_db->get_value("SELECT count(uid) FROM " . $this->_tableName . " WHERE uid=" . $this->_addSlashes($uid));
	}
	
	/**
	 * ��ñ���ע����fans/count
	 * 
	 * @param int $uid
	 */
	function countFans($uid) {//fixed
		return $this->_db->get_value("SELECT count(uid) FROM " . $this->_tableName . " WHERE friendid=" . $this->_addSlashes($uid));
	}
		
	function findAttentions($uid, $offset, $perpage) {//fixed
		$sql = "SELECT m.uid,m.username,m.icon as face,m.honor,m.groupid,m.memberid,m.gender,md.thisvisit,md.lastvisit,md.fans" .
			" FROM " . $this->_tableName . " f ".
			" LEFT JOIN pw_members m ON f.friendid = m.uid".
			" LEFT JOIN pw_memberdata md ON f.friendid = md.uid".
			" WHERE f.uid = " . $this->_addSlashes($uid) . " ORDER BY joindate DESC" .
			$this->_Limit($offset, $perpage);
		$query = $this->_db->query($sql);
		return $this->_getAllResultFromQuery($query);
	}
	
	function findFans($uid, $offset, $perpage) {//fixed
		$sql = "SELECT m.uid,m.username,m.icon as face,m.honor,m.groupid,m.memberid,m.gender,md.thisvisit,md.lastvisit,md.fans FROM ".$this->_tableName. " f ".
			" LEFT JOIN pw_members m ON f.uid = m.uid".
			" LEFT JOIN pw_memberdata md ON f.uid = md.uid".
			" WHERE f.friendid=".$this->_addSlashes($uid)." ORDER BY joindate DESC".
			$this->_Limit($offset, $perpage);
		$query = $this->_db->query($sql);
		return $this->_getAllResultFromQuery($query);
	}
	
	/**
	 * ��ȡ�ҹ�ע����
	 * 
	 * @param int $uid
	 */
	function getFollowList($uid, $offset = 0, $limit = 20) {//fixed
		$offset = (int)$offset;
		$limit = (int)$limit;
		$sql = "SELECT * FROM " . $this->_tableName . " WHERE uid="
				. $this->_addSlashes($uid) . " ORDER BY joindate DESC" . $this->_Limit($offset, $limit);
		$query = $this->_db->query($sql);
		return $this->_getAllResultFromQuery($query);
	}
	
	/**
	 * ��ȡ��ע�ҵ���
	 * 
	 * @param int $uid
	 */
	function getFansList($uid) {//fixed
		$sql = "SELECT * FROM " . $this->_tableName . " WHERE friendid="
				. $this->_addSlashes($uid) . " ORDER BY joindate DESC";
		$query = $this->_db->query($sql);
		return $this->_getAllResultFromQuery($query);
	}
	
	function getFollowListByFriendids($uid, $friendids) {//fixed
		if (!$friendids) return array();
		$friendids = is_array($friendids) ? $this->_getImplodeString($friendids) : $this->_addSlashes($friendids);
		$sql = 'SELECT * FROM ' . $this->_tableName . ' WHERE uid = ' . $this->_addSlashes($uid) . " AND friendid IN(" . $friendids . ")";
		$query = $this->_db->query($sql);
		return $this->_getAllResultFromQuery($query);
	}
	
	function getUidsInFansListByFriendids($uid, $friendids) {//fixed
		if (!$friendids) return array();
		$friendids = is_array($friendids) ? $this->_getImplodeString($friendids) : $this->_addSlashes($friendids);
		$sql = 'SELECT * FROM ' . $this->_tableName . ' WHERE friendid=' . $this->_addSlashes($uid) . " AND uid IN(" . $friendids . ")";
		return $this->_getAllResultFromQuery($this->_db->query($sql));
	}
	
	/**
	 * ���������˿�û�����
	 * return array
	 */
	function getTopFansUser($time,$num){
		if(!$time || !$num) return array();
		$query = $this->_db->query("SELECT friendid,count(friendid) as counts FROM " . $this->_tableName . ' WHERE joindate > ' . S::sqlEscape($time) . ' GROUP BY friendid ORDER BY counts DESC'.S::sqlLimit($num));
		return array_keys($this->_getAllResultFromQuery($query,'friendid'));
	}
}
?>
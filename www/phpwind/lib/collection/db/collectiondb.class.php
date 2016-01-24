<?php
!defined('P_W') && exit('Forbidden');

/**
 * �ղ����ݲ�
 * 
 * @package PW_CollectionDB
 * @author	lmq
 * @abstract
 */

class PW_CollectionDB extends BaseDB {
	var $_tableName 	= 	"pw_collection";
	var $_primaryKey 	= 	'id';

	function insert($fieldDate) {
		return $this->_insert($fieldDate);
	}
	
	function update($fieldsData,$id){
		return $this->_update($fieldsData,$id);
	}

	function updateByCtid($ctid){
		if (!$ctid) return false; 
		return $this->_db->update("UPDATE " . $this->_tableName . " SET ctid = '-1' WHERE ctid = ".S::sqlEscape($ctid));
	}

	function delete($ids) {
		if (!$ids) return false; 
		$ids = is_array($ids) ? $this->_getImplodeString($ids) : $this->_addSlashes($ids);
		$sql = "DELETE FROM " . $this->_tableName . " WHERE id IN(" . $ids . ")";
		return $this->_db->update($sql);
	}
	
	function deleteByUids($uids) {
		if (!$uids) return false; 
		$uids = is_array($uids) ? $this->_getImplodeString($uids) : $this->_addSlashes($uids);
		return $this->_db->update("DELETE FROM " . $this->_tableName . " WHERE uid IN(" . $uids . ")");
	}
	
	function get($id) {
		return $this->_get($id);
	}

	function getUidsByIds($ids){
		$query = $this->_db->query("SELECT id,uid FROM ".$this->_tableName. " WHERE id IN (".S::sqlImplode($ids).")");
		return $this->_getAllResultFromQuery($query);
	}
	
	function countByUid($uid,$ftype = null) {
		if (!$uid) return false; 
		($ftype != 0) && $ctid = ' AND ctid = '.S::sqlEscape($ftype);
		$sql = "SELECT COUNT(id) FROM ".$this->_tableName. " WHERE uid=".$this->_addSlashes($uid).
				" AND type IN(".$this->_getImplodeString($this->getTypeMap()). ") AND ifhidden=0 ".$ctid;
		return $this->_db->get_value($sql);
	}
	
	function countByUidAndType($uid,$type,$ftype = null) {
		if (!$uid || !$type) return false; 
		($ftype != 0) && $ctid = ' AND ctid = '.S::sqlEscape($ftype);
		$sql = "SELECT COUNT(id) FROM ".$this->_tableName. " WHERE uid=".$this->_addSlashes($uid).
				"AND type=".$this->_addSlashes($type)." AND ifhidden=0 ".$ctid;
		return $this->_db->get_value($sql);
	}
	
	function findByUid($uid, $offset, $limit, $ftype = null) {
		if (!$uid) return false; 
		($ftype != 0) && $ctid = ' AND ctid = '.S::sqlEscape($ftype);
		$query = $this->_db->query("SELECT * FROM ".$this->_tableName. " WHERE uid=".$this->_addSlashes($uid).
				" AND type IN(".$this->_getImplodeString($this->getTypeMap()). ") AND ifhidden=0 ". $ctid ." ORDER BY postdate DESC" .$this->_Limit($offset, $limit));
		return $this->_getAllResultFromQuery($query);
	}
	
	function findByUidAndType($uid, $type, $offset, $limit, $ftype = null) {
		if (!$uid || !$type) return false; 
		($ftype != 0) && $ctid = ' AND ctid = '.S::sqlEscape($ftype);
		$query = $this->_db->query("SELECT * FROM ".$this->_tableName. " WHERE uid=".$this->_addSlashes($uid).
				"AND type=".$this->_addSlashes($type).
				" AND ifhidden=0 ". $ctid ." ORDER BY postdate DESC" .$this->_Limit($offset, $limit));
		return $this->_getAllResultFromQuery($query);
	}

	function getByTypeAndTypeid($uid ,$type, $typeid) {
		if (!$type || !$typeid) return false;
		$sql = "SELECT * FROM ".$this->_tableName. " WHERE uid=".$this->_addSlashes($uid)." AND type=".$this->_addSlashes($type).
				" AND typeid=".$this->_addSlashes($typeid);
		return $this->_db->get_one($sql);
	}

	function getByType($uid ,$type) {
		if (!$type) return false;
		$query = $this->_db->query("SELECT typeid,ctid FROM ".$this->_tableName. " WHERE uid=".$this->_addSlashes($uid)." AND type=".$this->_addSlashes($type));
		return $this->_getAllResultFromQuery($query);
	}	
	/**
	 * �ղ�����mapͼ 
	 */
	function getTypeMap(){
		$typeList = array(
			'0' => 'weibo',//������
			'1' => 'diary',//��־
			'2' => 'photo', //���
			'3' => 'group', //Ⱥ��
			'4' => 'active',//��ܹ�
			'5' => 'web', //��ҳ
			'6' => 'multimedia',//��ý��
			'7' => 'cms',//����
			'8' => 'postfavor',//���� @modify panjl@2010-11-9
			'9' => 'tucool',//ͼ��
		);
		return $typeList;
	}
	/**
	 * �����û�uidͳ�Ƹ������ղ���
	 * 
	 * @param  int $uid �û�uid
	 * @return array  ����ctid��count��
	 */
	function countTypesByUid($uid){
		$uid = (int) $uid;
		if (!$uid) return array();
		$query = $this->_db->query("SELECT ctid,COUNT(*) AS count FROM pw_collection WHERE uid = ".S::sqlEscape($uid). " GROUP BY ctid");
		return $this->_getAllResultFromQuery($query);
	}

	/**
	 * �ı��ղط���
	 * 
	 * @param array $ids �ղ�ID
	 * @param int $ctid ����ID
	 * @return int ��������
	 */
	function remove($ids,$ctid) {
		$ctid = (int) $ctid;
		if (!$ids) return false; 
		$ids = is_array($ids) ? S::sqlImplode($ids) : S::sqlEscape($ids);
		$sql = "UPDATE " . $this->_tableName . " SET ctid = " .S::sqlEscape($ctid). " WHERE id IN (" . $ids . ")";
		return $this->_db->update($sql);
	}
}
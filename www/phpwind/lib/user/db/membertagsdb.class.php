<?php
!defined('P_W') && exit('Forbidden');

/**
 * ��ǩ���ݲ�
 * @package  PW_MemberTagsDB
 * @author panjl @2010-12-27
 */
class PW_MemberTagsDB extends BaseDB {
	var $_tableName 	= 	'pw_membertags';
	var $_primaryKey 	= 	'tagid';
	var $_membertags_relations = "pw_membertags_relations";

	/**
	 * ���
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @return int
	 */
	function insert($fieldsData) {
		if (!S::isArray($fieldsData)) return false;
		return $this->_insert($fieldsData);
	}

	/**
	 * ����
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @return int
	 */
	function update($fieldsData,$tagid) {
		$tagid = intval($tagid);
		if ($tagid < 1 || !S::isArray($fieldsData)) return false;
		return $this->_update($fieldsData,$tagid);
	}
	
	/**
	 * ���±�ǩʹ������
	 * 
	 * @param int $tagid ��ǩ
	 * @return boolean
	 */
	function updateNumByTagId($tagid,$num) {
		$tagid = intval($tagid);
		$num = intval($num);
		if ($tagid < 1 || !$num) return false;
		return (bool)$this->_db->update("UPDATE " . $this->_tableName . " SET num = num + " . $num . " WHERE tagid = " . $this->_addSlashes($tagid));
	}

	/**
	 * ����ɾ����ǩ
	 * 
	 * @param array $tagids
	 * @return boolean
	 */
	function deleteTagsByTagIds($tagids) {
		if(!S::isArray($tagids)) return false;
		return pwQuery::delete($this->_tableName, "tagid in(:tagid)", array($tagids));
	}
	
	/**
	 * ���ݱ�ǩID��ȡ��ǩ��Ϣ
	 * 
	 * @param int $tagid ��ǩ
	 * @return array
	 */
	function getTagsByTagid($tagid) {
		$tagid = intval($tagid);
		if ($tagid < 1) return array();
		return $this->_db->get_one("SELECT * FROM " . $this->_tableName . " WHERE tagid = " . $this->_addSlashes($tagid));
	}

	
	/**
	 * �����û�uid��ȡ��ǩ��Ϣ
	 * 
	 * @param int $tagid ��ǩ
	 * @return array
	 */
	function getTagsByUid($uid) {
		$uid = intval($uid);
		if ($uid < 1) return array();
		$query = $this->_db->query("SELECT t.tagid,t.tagname,mt.userid,mt.userid as uid FROM " . $this->_membertags_relations . " mt LEFT JOIN " . $this->_tableName . " t USING(tagid) WHERE mt.userid = " . $this->_addSlashes($uid) . " ORDER BY mt.crtime DESC");
		return $this->_getAllResultFromQuery($query);
	}

	/**
	 * �����û�uids������ȡ��ǩ��Ϣ
	 * 
	 * @param int $uids �û�uids����
	 * @return array
	 */
	function getTagsByUids($uids) {
		if(!S::isArray($uids)) return array();
		$query = $this->_db->query("SELECT t.tagid,t.tagname,mt.userid FROM " . $this->_membertags_relations . " mt LEFT JOIN " . $this->_tableName . " t USING(tagid) WHERE mt.userid IN(" . S::sqlImplode($uids) . ")");
		return $this->_getAllResultFromQuery($query);
	}
	
	/**
	 * ���ݱ�ǩ����ȡ��ǩ��Ϣ
	 * 
	 * @param string $tagName ��ǩ��
	 * @return int stidֵ
	 */
	function getTagsByTagName($tagName) {
		$tagName = trim($tagName);
		if ($tagName == '') return array();
		return $this->_db->get_one("SELECT * FROM " . $this->_tableName . " WHERE tagname = " . $this->_addSlashes($tagName));
	}
	
	/**
	 * �����Ƿ��������ű�ǩ
	 * 
	 * @param array $tagids
	 * @return boolean
	 */
	function setHotByTagids($tagids,$ifhot) {
		$ifhot = intval($ifhot);
		if($ifhot < 0 || !S::isArray($tagids)) return false;
		return pwQuery::update($this->_tableName, "tagid in(:tagid)", array($tagids), array('ifhot'=>$ifhot));
	}
	
	/**
	 * ���ű�ǩtop100
	 * 
	 * @param int $num
	 * @return array
	 */
	function getTagsByNum($num) {
		$num = intval($num);
		if($num < 0) return array();
		$query = $this->_db->query("SELECT tagid,tagname,num FROM  " . $this->_tableName . " WHERE ifhot = 1 ORDER BY num DESC " . S::sqlLimit($num));
		return $this->_getAllResultFromQuery($query);
	}
	
	/**
	 * ��̨��������������ǩ��Ϣ
	 * @return int
	 */
	function countTagsByCondition($name, $ifhot, $startnum, $endnum) {
		$addsql = " WHERE 1";
		if ($name != '') $addsql .= ' AND tagname like ' . $this->_addSlashes('%'.$name.'%');
		if ($startnum != '') $addsql .= ' AND num >= ' . $this->_addSlashes($startnum);
		if ($endnum != '') $addsql .= ' AND num <= ' . $this->_addSlashes($endnum);
		if ($ifhot != '') $addsql .= ' AND ifhot = ' . $this->_addSlashes($ifhot);
		$sql = 'SELECT count(*) FROM  ' . $this->_tableName . ' ' . $addsql;
		return $this->_db->get_value('SELECT count(*) FROM  ' . $this->_tableName . ' ' . $addsql);
	}

	/**
	 * ��̨��������������ǩ��Ϣ
	 * 
	 * @param string $name ��ǩ��
	 * @param int $ifhot �Ƿ��������ű�ǩ
	 * @param int $startnum minʹ���� 
	 * @param int $endnum maxʹ���� 
	 * @param int $page
	 * @param int $perpage
	 * @return array
	 */
	function getTagsByCondition($name, $ifhot, $startnum, $endnum, $start, $num) {
		$start = intval($start);
		$num = intval($num);
		if ($start < 0 || $num < 1) return array();
		$addsql = " WHERE 1";
		if ($name != '') $addsql .= ' AND tagname like ' . $this->_addSlashes('%'.$name.'%');
		if ($startnum != '') $addsql .= ' AND num >= ' . $this->_addSlashes($startnum);
		if ($endnum != '') $addsql .= ' AND num <= ' . $this->_addSlashes($endnum);
		if ($ifhot != '') $addsql .= ' AND ifhot = ' . $this->_addSlashes($ifhot);
		$addsql .= ' ORDER BY num DESC';
		$addsql .= $this->_Limit($start,$num);
		$query = $this->_db->query('SELECT * FROM  ' . $this->_tableName .' '. $addsql);
		return $this->_getAllResultFromQuery($query);
	}
}
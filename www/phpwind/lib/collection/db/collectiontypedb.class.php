<?php
!defined('P_W') && exit('Forbidden');

/**
 * �ղط������ݲ�
 * 
 * @package PW_CollectionTypeDB
 * @author	panjl
 * @abstract
 */

class PW_CollectionTypeDB extends BaseDB {
	var $_tableName 	= 	"pw_collectiontype";
	var $_primaryKey 	= 	'ctid';

	/**
	 * ���
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @return int ����id
	 */
	function insert($fieldsData) {
		if (S::isArray($fieldsData)) {
			return $this->_insert($fieldsData);
		}
	}

	/**
	 * �༭
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @param int $ctid ����ID
	 * @return int ���±༭�ķ���ID
	 */
	function update($fieldsData,$ctid) {
		$ctid = intval($ctid);
		if ($ctid < 1) return null;
		if (S::isArray($fieldsData)) {
			return $this->_update($fieldsData,$ctid);
		}
	}

	/**
	 * ɾ��
	 * 
	 * @param int $ctid  ����ID
	 * @return int ɾ������
	 */
	function delete($ctid) {
		$ctid = intval($ctid);
		if ($ctid < 1) return null;
		return $this->_delete($ctid);
	}

	/**
	 * ���ݷ���IDȡ������Ϣ
	 * 
	 * @param int $ctid  ����ID
	 * @return array 
	 */
	function getTypeByCtid($ctid) {
		$ctid = intval($ctid);
		if ( $ctid < 1 ) return array();
		return $this->_db->get_one ("SELECT * FROM ".$this->_tableName." WHERE ctid = ". S::sqlEscape($ctid));
	}

	/**
	 * �����û�uidȡ������Ϣ
	 * 
	 * @param int $uid  �û�uid
	 * @return array 
	 */
	function getTypesByUid($uid) {
		$uid = intval($uid);
		if ( $uid < 1 ) return array();
		$query = $this->_db->query ( "SELECT * FROM ".$this->_tableName." WHERE uid = ". S::sqlEscape($uid));
		return $this->_getAllResultFromQuery ($query);
	}

	/**
	 * ��uid�������Ƿ����
	 * 
	 * @param int $userId �û�uid
	 * @param string $typeName
	 * @return int ctid
	 */
	function getCtidByUidAndName($uid, $typeName) {
		$uid = intval($uid);
		if ( $uid < 1 || !$typeName ) return 0;
		return $this->_db->get_value( "SELECT ctid FROM ".$this->_tableName." WHERE uid = ". S::sqlEscape($uid) . " AND name = " . S::sqlEscape($typeName));
	}

}
<?php
!defined('P_W') && exit('Forbidden');

/**
 * �ղط���service
 * 
 * @package PW_CollectionTypeService
 * @author	panjl
 * @abstract
 */

class PW_CollectionTypeService {
	
	/**
	 * ���
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @return int ����id
	 */
	function insert ($fieldsData) {
		if (S::isArray($fieldsData)) {
			$typeDb = $this->_getCollectionTypeDB();
			return $typeDb->insert($fieldsData);
		}
	}

	/**
	 * �༭
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @param int $ctid ����ID
	 * @return int ���±༭�ķ���ID
	 */
	function update ($fieldsData, $ctid) {
		$ctid = intval($ctid);
		if ($ctid < 1) return NULL;
		if (S::isArray($fieldsData)) {
			$typeDb = $this->_getCollectionTypeDB();
			return $typeDb->update($fieldsData, $ctid);
		}
	}

	/**
	 * ɾ��
	 * 
	 * @param int $ctid  ����ID
	 * @return int ɾ������
	 */
	function delete ($ctid) {
		$ctid = intval($ctid);
		if ($ctid != '-1' && $ctid < 1) return null;
		$typeDb = $this->_getCollectionTypeDB();
		return $typeDb->delete($ctid);
	}

	/**
	 * ���û�ID�ͷ�������ctid
	 *
	 * @param int $uid �û�uid
	 * @param string $typeName ������
	 * @return int ����ID
	 */
	function getCtidByUidAndName($uid, $typeName) {
		$uid = (int)$uid;
		if ( $uid < 1 || !$typeName ) return 0;
		$typeDb = $this->_getCollectionTypeDB();
		return $typeDb->getCtidByUidAndName($uid, $typeName);
	}

	/**
	 * ��uid�������Ƿ����
	 *
	 * @param int $uid �û�uid
	 * @param string $typeName ������
	 * @param int ctid ����ID
	 * @return boolen 
	 */
	function checkTypeExist($uid, $typeName, $ctid=null) {
		global $winduid;
		$uid = (int)$uid;
		$ctid   = (int)$ctid;
		if ( !$typeName || ($uid != $winduid) ) return false;
		if ( !$ctid ) {
			$isExistType = $this->getCtidByUidAndName($uid,$typeName);
			if ($isExistType > 0) return false;
		} else {
			$typeDb = $this->getTypeByCtid($ctid);
			if ($typeDb['name'] != $typeName) {
				$isExistType = $this->getCtidByUidAndName($uid,$typeName);
				if ($isExistType > 0) return false;
			}
		}
		 return true;
	}

	/**
	 * �����û�id �����ղط���
	 *
	 * @param int $uid �û�uid
	 * @return array �ղط���
	 */
	function getTypesByUid($uid) {
		$uid = (int)$uid;
		if ( $uid < 1 ) return array();
		$typeDb = $this->_getCollectionTypeDB();
		return $typeDb->getTypesByUid($uid);
	}

	/**
	 * ���ݷ���ID �����ղط���
	 *
	 * @param int $ctid ����ID
	 * @return array
	 */
	function getTypeByCtid($ctid) {
		$ctid = (int)$ctid;
		if ( $ctid < 1 ) return array();
		$typeDb = $this->_getCollectionTypeDB();
		return $typeDb->getTypeByCtid($ctid);
	}

	/**
	 * get PW_CollectionTypeDB
	 * 
	 * @access protected
	 * @return PW_CollectionTypeDB
	 */
	function _getCollectionTypeDB() {
		return L::loadDB('CollectionType', 'collection');
	}
}


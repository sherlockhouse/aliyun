<?php
! defined ( 'P_W' ) && exit ( 'Forbidden' );

class PW_RateResultDB extends BaseDB {
	var $_tableName = "pw_rateresult";

	function add($fieldData) {
		$this->_db->update ( "INSERT INTO " . $this->_tableName . " SET " . $this->_getUpdateSqlString ( $fieldData ) );
		return $this->_db->insert_id ();
	}

	/**
	 * ���ѡ��ID�����ID��ȡ�����¼
	 *
	 * @param int $optionId
	 * @param int $objectId
	 * @return array
	 */
	function getByOptionId($optionId, $objectId) {
		return $this->_db->get_one ( "SELECT * FROM " . $this->_tableName . " WHERE optionid=" . $this->_addSlashes ( $optionId ) . " AND objectid=" . $this->_addSlashes ( $objectId ) . "  LIMIT 1" );
	}

	/**
	 * ����ĳ�������ѡ���������
	 *
	 * @param int $optionId
	 * @param int $objectId
	 * @return unknown
	 */
	function updateByOptionId($optionId, $objectId) {
		$this->_db->update ( "UPDATE " . $this->_tableName . " SET num=num+1 WHERE optionid=" . $this->_addSlashes ( $optionId ) . " AND objectid=" . $this->_addSlashes ( $objectId ) . "  LIMIT 1" );
		return $this->_db->affected_rows ();
	}

	/**
	 * ��ȡĳ�������ĳ�����������ѡ����
	 *
	 * @param unknown_type $typeId
	 * @param unknown_type $objectId
	 * @return unknown
	 */
	function getByTypeId($typeId, $objectId) {
		$query = $this->_db->query ( "SELECT * FROM " . $this->_tableName . " WHERE typeid=" . $this->_addSlashes ( $typeId ) . " AND objectid=" . $this->_addSlashes ( $objectId ) );
		return $this->_getAllResultFromQuery ( $query );
	}
}

?>
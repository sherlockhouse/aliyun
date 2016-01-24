<?php
!defined('P_W') && exit('Forbidden');
@include_once (R_P . 'lib/base/basedb.php');

class PW_SharelinksDB extends BaseDB {
	var $_tableName = "pw_sharelinks";

	/**
	 * �������������������
	 * @param unknown_type $num
	 * @return multitype:
	 */
	function getNewData($num,$haveLogo=false) {
		$num = (int) $num;
		$_sqlAdd = $haveLogo ? " AND logo <> '' " : " AND logo='' ";
		$query = $this->_db->query("SELECT * FROM $this->_tableName WHERE ifcheck = '1' $_sqlAdd ORDER BY threadorder ASC LIMIT 0,$num");
		return $this->_getAllResultFromQuery($query);
	}

	/**
	 * ���շ��ࡢ�Ƿ���logo����������Ϣ
	 * 
	 * @param int $num ����
	 * @param bool false �Ƿ���logo
	 * @param array $sids ����ID����
	 * @return array �������ӷ�����Ϣ
	 */
	function getData($num,$sids,$haveLogo=false) {
		$num = (int) $num;
		$num && $limit = $this->_Limit(0,$num);
		$haveLogo && $_sqlAdd = " AND logo <> '' ";
		is_array($sids) && $_sqlsids = " AND sid IN(" . S::sqlImplode($sids) . ")";
		$query = $this->_db->query("SELECT * FROM $this->_tableName WHERE ifcheck = '1' ".$_sqlsids. $_sqlAdd ." ORDER BY threadorder ASC $limit");
		return $this->_getAllResultFromQuery($query);
	}
}
?>
<?php
!defined('P_W') && exit('Forbidden');

/**
 * ��������ȫ�ֵ�����
 * @2010-4-19 zhudong
 */

class PW_ToolCenter {

	var $_db = null;
	var $_timestamp = 0;

	function PW_ToolCenter(){
		global $db,$timestamp;
		$this->_db = $db;
		$this->_timestamp = $timestamp;		
	}

	/**
	 * �����ȡָ�������ĵ�����Ϣ
	 * @param $num ��ȡ�ĸ���
	 * @return array 
	*/

	function getToolsByRandom($num){
		$tools = array();
		$query = $this->_db->query("SELECT * FROM pw_tools WHERE state=1 ORDER BY RAND() LIMIT ".intval($num));
		while ($rt = $this->_db->fetch_array($query)) {
			if(empty($rt['logo'])) {
				$rt['logo'] = $GLOBALS['imgpath'] . '/nopic.gif';
			} else {
				$rt['logo'] = "u/images/toolcenter/tool/$rt[id].gif";
			}
			$rt['subdescrip'] = substrs($rt['descrip'],20);
			$tools[] = $rt;
		}
		return $tools;
	}

	/**
	 * ��ȡָ��������ָ���û��ĵĵ�����Ϣ
	 * @param $uid ��ȡ���û�
	 * @param $num ��ȡ�ĸ���
	 * @return array 
	*/

	function getToolsByUidAndNum($uid,$num){
		$tools = array();
		$query = $this->_db->query("SELECT u.*,t.name,t.price,t.creditype,t.stock,t.descrip,t.type,t.logo FROM pw_usertool u LEFT JOIN pw_tools t ON t.id=u.toolid WHERE u.uid=".S::sqlEscape($uid)." AND u.nums>0 LIMIT ".intval($num));
		while ($rt = $this->_db->fetch_array($query)) {
			if(empty($rt['logo'])) {
				$rt['logo'] = $GLOBALS['imgpath'] . '/nopic.gif';
			} else {
				$rt['logo'] = "u/images/toolcenter/tool/$rt[toolid].gif";
			}
			$rt['subdescrip'] = substrs($rt['descrip'],20);
			$tools[] = $rt;
		}
		return $tools;
	}
}
<?php
!defined('P_W') && exit('Forbidden');
/**
 * ��������־����
 */
class PW_Errors {
	var $_errors = array(); //���󼯺�
	

	var $_logs = array(); //��־����
	

	/**
	 * ���һ��������Ϣ
	 * @param $errorInfo	������Ϣ
	 */
	function addError($errorInfo) {
		$this->_errors[] = $errorInfo;
	}
	/**
	 * ���һ��������Ϣ
	 * @param $logInfo
	 */
	function addLog($logInfo) {
		$this->_logs[] = $logInfo;
	}
	/**
	 * ��¼������Ϣ
	 */
	function writeLog($method = 'rb+') {
		$logFile = D_P.'data/error.log';
		if (!$this->_logs) return false;
		$temp = pw_var_export($this->_logs);
		pwCache::writeover($logFile,$temp, 'rb+');
	}
	/**
	 * ����Ƿ��д�����Ϣ���еĻ���ʱ����
	 */
	function checkError($jumpurl = '') {
		foreach ($this->_errors as $error) {
			$this->showError($error,$jumpurl);
		}
	}
	/**
	 * ��ʱ����
	 * @param $error ������Ϣ
	 */
	function showError($error, $jumpurl = '') {
		Showmsg($error, $jumpurl);
	}
	
	function __destruct() {
		if (!defined('SHOWLOG')) return false;
		$this->writeLog();
	}
}
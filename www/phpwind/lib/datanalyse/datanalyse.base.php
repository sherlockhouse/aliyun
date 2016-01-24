<?php
!defined('P_W') && exit('Forbidden');

class PW_Datanalyse {
	var $datanalyseDB;
	var $actions = array();
	var $tags = array();
	var $nums = array();
	var $pk = 'id';
	
	var $overtime = 30; //��ʱʱ��30��
	var $top = 200;

	function PW_Datanalyse() {
		$this->__construct();
	}

	function __construct() {
		$this->datanalyseDB = L::loadDB('datanalyse', 'datanalyse');
		/* @var $this->datanalyseDB PW_DatanalyseDB */
		$this->_setActions();
	}

	/**
	 * ����action���ָ������������
	 * @param string/array $action
	 * @param int $num
	 * @param int $time
	 */
	function getDataAndNumsByAction($action, $num, $time = '') {
		if (!$this->_filterAction($action)) return array();
		$this->_getTagsByAction($action, $num, $time);
		$data = $this->_getDataByTags();
		$this->_clearNotExistData($data,$action);
		return $this->_sortResultData($data);
	}
	
	/**
	 * ����action��ȡ���������б�
	 * @param string/array $action
	 * @param int $num
	 * @param int $time
	 */
	function getHotArticleByAction($action, $num, $time = '') {
		if (!$this->_filterAction($action)) return array();
		$this->_formatResultData($this->datanalyseDB->getDataOderByTag($action, $num, $time));
		return $this->_getHotArticlesByTags();
	}

	function _clearNotExistData($data, $action) {
		if (count($data) == count($this->tags)) return;
		if (is_array($action)) return;
		$_notExist = $_data = array();
		foreach ($data as $key => $value) {
			$_data[] = $value[$this->pk];
		}
		foreach ($this->tags as $v) {
			if (!in_array($v, $_data)) {
				$_notExist[] = $v;
			}
		}
		if ($_notExist) {
			$this->datanalyseDB->deleteDataByActionAndTag($action, $_notExist);
		}
		return;
	}

	/**
	 * @param array $data
	 */
	function _sortResultData($data) {
		$_tmp = array();
		foreach ($this->nums as $key => $value) {
			foreach ((array) $data as $k => $var) {
				if ($var[$this->pk] == $key) {
					$var['num'] = $value;
					$_tmp[] = $var;
					unset($data[$k]);
					break;
				}
			}
		}
		return $_tmp;
	}

	/**
	 * ����һ��actions
	 */
	function _setActions() {
		$this->actions = array_merge($this->actions, (array) $this->_getExtendActions());
	}

	/**
	 * ������۷�����
	 * @return multitype:
	 */
	function _getExtendActions() {
		return array();
	}

	/**
	 * �������ͻ�ȡ�Ȱ�����
	 * @param string $action
	 * @param int $num
	 */
	function _getTagsByAction($action, $num, $time) {
		if (is_array($action)) {
			$this->_formatResultData($this->datanalyseDB->getTagsByActionsAndTime($action, $num, $time));
		} else {
			$this->_formatResultData($this->datanalyseDB->getTagsByActionAndTime($action, $num, $time));
		}
	}

	/**
	 * ��ʽ���Y��
	 * @param array $data
	 */
	function _formatResultData($data) {
		foreach ((array) $data as $key => $value) {
			$this->tags[] = $value['tag'];
			$this->nums[$value['tag']] = $value['nums'];
		}
	}

	/**
	 * ���˷Ƿ�action���ͣ����action�����ڷ��ؿ�
	 * @param string $action
	 */
	function _filterAction($actions) {
		!is_array($actions) && $actions = (array) $actions;
		foreach ($actions as $var) {
			if (!in_array($var, $this->actions)) return false;
		}
		return true;
	}

	/**
	 * ����$action���������Ȱ�����
	 * @param string $action
	 */
	function clearData($action) {
		$_overTime = $this->_getCurrentTime() - 86400 * $this->overtime;
		$this->_clearOverTimeData($_overTime);
		$this->_clearOtherData($_overTime, $action);
	}

	/**
	 * ��ȡ��ǰʱ���
	 * @return Ambigous <number, string, unknown>
	 */
	function _getCurrentTime() {
		global $timestamp;
		return PwStrtoTime(get_date($timestamp, 'Y-m-d'));
	}

	/**
	 * @param int $overtime
	 * @param string $action
	 */
	function _clearOtherData($overtime, $action) {
		for ($index = 0; $index <= $this->overtime; $index++) {
			$time = $overtime + $index * 24 * 60 * 60;
			$rt = $this->datanalyseDB->getMaxNumByActionAndTime($action, $time, $this->top);
			if ($rt) {
				$this->datanalyseDB->deleteDataByTimeAndAction($action, $time, $rt);
			}
		}
	}

	/**
	 * ����ʱ����
	 */
	function _clearOverTimeData($time) {
		$this->datanalyseDB->_deleteDataByTime($time);
	}

	/**
	 * ������һ�������ʱ�䣬�������0��ʼ������
	 * ͬʱд�����ڵ�����ʱ��
	 * @return number
	 */
	function _getLastClearTime($action) {
		return $this->_readFileByKey($action, $this->_getCurrentTime());
	}

	/**
	 * ����KEY=>VALUE��д�ļ�
	 * ��ȡԭ�е�KEY��ֵ��д���µ�ֵ
	 * @param string/array $key
	 * @param string $value
	 * @return string
	 */
	function _readFileByKey($key, $value = '') {
		$_filename = D_P . "data/bbscache/datanalyse.php";
		//* if (file_exists($_filename)) include pwCache::getPath($_filename);
		if (file_exists($_filename)) extract(pwCache::getData($_filename, false));
		$_data = "\$overtimes=array(\r\n";
		$_result = '';
		foreach ((array) $overtimes as $k => $var) {
			if ($key == $k) {
				$_result = $var;
				$_data .= $value ? "\t\t'" . $k . "'=>'" . $value . "',\r\n" : "\t\t'" . $k . "'=>'" . $var . "',\r\n";
			} else {
				$_data .= "\t\t'" . $k . "'=>'" . $var . "',\r\n";
			}
		}
		$_data .= "\t)";
		pwCache::setData($_filename, "<?php\r\n" . $_data . "\r\n?>");
		return $_result;
	}

}
?>
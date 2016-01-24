<?php
!function_exists('readover') && exit('Forbidden');
/**
 * ����ͳ��
 */
class PW_KeywordStatisticDatabase {
	var $_keyword;
	var $_tableName = 'pw_temp_keywords';
	var $_db = null;
	var $_timestamp;
	var $_lastUpdateTime;
	var $_statisticTimeNode = 86400; //86400 =  24*3600 ��λ��  ��ǰʱ��-�ϴ�ʱ��>1�죬ִ��ͳ�Ʋ���
	
	function PW_KeywordStatisticDatabase() {
		global $timestamp,$db;
		$this->_db = $db;
		$this->_timestamp = $timestamp;
	}
	
	function init($keyword) {
		$this->_keyword = $keyword;
		$this->_lastUpdateTime = $this->_getLastUpdateTime();
	}
	
	function execute() {
		$this->_writeover();
		$this->update();
	}
	
	/**
	 * д�����ݿ���
	 */
	function _writeover() {
		$this->_keyword = trim($this->_filterCheckKeyword($this->_keyword));
		if (!$this->_keyword) return false;
		$fields = array(
			'keyword' => $this->_keyword,
			'created_time' => $this->_timestamp
		);
		$databasedb = $this->_getKeywordStatisticDatabaseDb();
		return $databasedb->insert($fields);
	}
		
	function update() {
		if ($this->_checkIfSysc()) {
			if ($this->_updateDb()) {
				$this->_clearData();
			}
		}
		return true;
	}
	
	/**
	 * ���µ�pw_searchstatistic����
	 */
	function _updateDb() {
		$fileContent = $this->_getAllKeywords();
		$data = s::isArray($fileContent) ? array_count_values($fileContent) : array();
		$nowtime = PwStrtoTime(get_date($this->_timestamp,'Y-m-d'));
		$sql = array();
		foreach ($data as $key => $val) {
			$key = trim($this->_filterCheckKeyword($key));
			if (!$key) continue;
			$sql[] = array($key,$val,$nowtime);
		}
		if (!$sql) return false;
		$this->_db->query("INSERT INTO pw_searchstatistic(keyword,num,created_time) VALUES " . S::sqlMulti($sql));
		$deleteTime = $this->_timestamp - 86400*90;
		$this->_db->query("DELETE FROM pw_searchstatistic WHERE created_time < $deleteTime");
		return true;
	}
	
	/**
	 * ������е���ʱ�ؼ���
	 */
	function _getAllKeywords() {
		$databasedb = $this->_getKeywordStatisticDatabaseDb();
		$keywords = $databasedb->getAllKeywords();
		$allWords = array();
		foreach($keywords as $v) {
			$allWords[] = $v['keyword'];
		}
		return $allWords;
	}


	function _filterCheckKeyword($keyword) {
		if (!$keyword) return false;
		return s::stripTags(str_replace ( array ("&#160;", "&#61;", "&nbsp;", "&#60;", "<", ">", "&gt;", "(", ")", "&#41;" ), array (" " ), $keyword ));
	}

	
	/**
	 * �����ݿ��л��������ʱ��
	 * @return string 
	 */
	function _getLastUpdateTime() {
		$databasedb = $this->_getKeywordStatisticDatabaseDb();
		$lastTime = $databasedb->getLastUpdateTime();
		return $lastTime ? $lastTime : $this->_timestamp;
	}
	
	/**
	 * �����ʱ���е�����
	 * @return boolean
	 */
	function _clearData() {
		$databasedb = $this->_getKeywordStatisticDatabaseDb();
		return $databasedb->deleteAll();
	}
	
	/**
	 * ִ���Ƿ����ͬ���Ĳ���
	 * ���ʱ���Խ�������ǵ����趨��24Сʱ���򷵻�true
	 * ����
	 * �����ǰ���ܼ����ϴθ��µ��ܼ�������ͬһ�죬�򷵻�true ���򣺷���false
	 * @author xiaoxia.xu @2011-05-30
	 * @return boolean
	 */
	function _checkIfSysc() {
		if (($this->_timestamp - $this->_lastUpdateTime) >= $this->_statisticTimeNode) return true;
		$startWeek = date('w',$this->_timestamp);
		$endWeek = date('w',$this->_lastUpdateTime);
		return ($endWeek != $startWeek);
	}
	
	function _getKeywordStatisticDatabaseDb() {
		return L::loadDB('KeywordStatisticDatabase', 'search');
	}
}
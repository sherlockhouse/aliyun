<?php
!function_exists('readover') && exit('Forbidden');
/**
 * �������������
 * ֧��MySQL���ݿ�������Sphinxȫ����������
 * @author liuhui 2010-4-21
 * @version phpwind 8.0
 */
class PW_Searcher {
	var $_sphinx  = null;
	var $_service = null;
	function PW_Searcher(){
		global $db_sphinx;
		$this->_sphinx = &$db_sphinx;
		$this->_init();
	}
	/**
	 * ��ʼ����������ӿ�
	 * @return unknown_type
	 */
	function _init(){
		$this->_service = ($this->_sphinx['isopen'] > 0) ? $this->_serviceFactory("sphinx") : $this->_serviceFactory("mysql");
	}
	function checkUserLevel(){
		return $this->_service->checkUserLevel();
	}
	function checkWaitSegment(){
		return $this->_service->checkWaitSegment();
	}
	/**
	 * ������������
	 * @return unknown_type
	 */
	function searchThreads($keywords,$range,$userNames="",$starttime="",$endtime="",$forumIds=array(),$page=1,$perpage=20,$expand=array()){
		if (!$keywords && $userNames) {
			$service = $this->mysqlService();
			return $service->searchThreads($keywords,$range,$userNames,$starttime,$endtime,$forumIds,$page,$perpage,$expand);
		}
		return $this->_service->searchThreads($keywords,$range,$userNames,$starttime,$endtime,$forumIds,$page,$perpage,$expand);
	}
	/**
	 * �����������
	 * @return unknown_type
	 */
	function searchForums($keywords,$page=1,$perpage=20){
		return $this->_service->searchForums($keywords,$page,$perpage);
	}
	/**
	 * ��������������
	 * @return unknown_type
	 */
	function searchWeibo($keywords,$userNames="",$starttime="",$endtime="",$page=1,$perpage=20){
		return $this->_service->searchWeibo($keywords,$userNames,$starttime,$endtime,$page,$perpage);
	}
	/**
	 * ����������־
	 * @return unknown_type
	 */
	function searchDiarys($keywords,$range,$userNames="",$starttime="",$endtime="",$page=1,$perpage=20){
		if (!$keywords && $userNames) {
			$service = $this->mysqlService();
			return $service->searchDiarys($keywords,$range,$userNames,$starttime,$endtime,$page,$perpage);
		}
		return $this->_service->searchDiarys($keywords,$range,$userNames,$starttime,$endtime,$page,$perpage);
	}
	/**
	 * ���������û�
	 * @return unknown_type
	 */
	function searchUsers($keywords,$page=1,$perpage=20){
		return $this->_service->searchUsers($keywords,$page,$perpage);
	}
	/**
	 * ��������Ⱥ��
	 * @return unknown_type
	 */
	function searchGroups($keywords,$page = 1,$perpage = 20){
		return $this->_service->searchGroups($keywords,$page,$perpage);
	}
	
	/**
	 * ��������������Ϣ
	 * @return unknown_type
	 */
	function searchSpecial($type='latest', $uid, $page=1, $perpage=50, $expandCondition = array()){
		return $this->_service->getSpecialThreads($type, $uid, $page, $perpage, $expandCondition);
	}
	
	/**
	 * ��������Ĭ����Ϣ
	 * @return unknown_type
	 */
	function searchDefault($type='thread', $page=1, $perpage=50, $expandConditions = array()){
		return $this->_service->getDefaultByType($type, $page, $perpage, $expandConditions);
	}
	
	/**
	 * �����������ͳ����Ϣ
	 * @return unknown_type
	 */
	function searchForumGroups($keywords,$range,$userNames="",$starttime="",$endtime="",$forumIds=array(),$page=1,$perpage=20,$expand=array()) {
		$service = $this->sphinxService();
		return $service->searchForumGroups($keywords,$range,$userNames,$starttime,$endtime,$forumIds,$page,$perpage,$expand);
	}
	
	/**
	 * ��̨���ӹ������
	 * @param $keywords
	 * @param $range
	 * @param $userNames
	 * @param $starttime
	 * @param $endtime
	 * @param $forumIds
	 * @param $page
	 * @param $perpage
	 * @return array(total,threadIds)
	 */
	function manageThreads($keywords,$range,$userNames="",$starttime="",$endtime="",$forumIds = array(),$page=1,$perpage=20){
		$service = $this->sphinxService();
		return $service->manageThreads($keywords,$range,$userNames,$starttime,$endtime,$forumIds,$page,$perpage);
	}
	
	function sphinxService(){
		return $this->_serviceFactory("sphinx");
	}
	
	function mysqlService(){
		return $this->_serviceFactory("mysql");
	}
	
	/**
	 * ˽�м�����Ϣ���ķ������
	 * @param $name
	 * @return unknown_type
	 */
	function _serviceFactory($name) {
		static $classes = array();
		$name = strtolower($name);
		$filename = R_P . "lib/search/search/" . $name . ".search.php";
		if (!is_file($filename)) {
			return null;
		}
		$class = 'Search_' . ucfirst($name);
		if (isset($classes[$class])) {
			return $classes[$class];
		}
		if (!class_exists('Search_Base')) require (R_P . 'lib/search/search/base.search.php');
		if (!class_exists($class)) include S::escapePath($filename);
		$classes[$class] = new $class();
		return $classes[$class];
	}
}
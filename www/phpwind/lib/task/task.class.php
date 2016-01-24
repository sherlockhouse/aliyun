<?php
!function_exists('readover') && exit('Forbidden');

/**
 * ����ϵͳ��������
 * @2009-11-23
 * 
 * @package Task
 */
class PW_Task {
	var $_db = null;
	var $_tasks = array();
	var $_interval = 6; /*ÿ6Сʱͨ��һ��*/
	var $_runInterval = 12; /*�������м��ÿ12Сʱ*/
	var $_filename = 'tasks.php';
	var $_cache = false;
	var $_openVerity = true; /*�Ƿ���ʱ������*/
	var $_timestamp = null;
	var $_hour = 3600;
	function PW_Task() {
		global $db, $timestamp;
		$this->_db = & $db;
		$this->_timestamp = $timestamp;
	}
	/**
	 * ��������
	 */
	function run() {
		if ($this->verify()) {
			$this->doTask();
		}
		return true;
	}
	/**
	 * ִ������
	 */
	function doTask() {
		$tasks = $this->gets();
		if (!$tasks) {
			return null;
		}
		foreach($tasks as $task) {
			$this->tasklist($task['task']);
		}
	}
	/**
	 * �����б�
	 */
	function tasklist($task) {
		switch ($task) {
			case 'alteradver':
				$this->alterAdver();
				break;

			default:
				break;
		}
		return true;;
	}
	/**
	 * ����һ����浽����������
	 */
	function alterAdver() {
		list($id, $name, $task, $next) = array_values($this->getDefaultTask('alteradver'));
		$class = $this->_loadTask($task);
		$finish = $class->run();
		if ($finish) {
			$this->update($id, $name, $task, $next);
		}
		return true;
	}
	/**
	 * ��������
	 */
	function check($id, $name, $task, $next) {
		$tasks = $this->get($id);
		if (!$tasks) {
			$next = $this->_timestamp + $next;
			$this->add($id, $name, $task, $next);
			return true;
		}
		if ($tasks['next'] <= $this->_timestamp) {
			return true;
		}
		return false;
	}
	/**
	 * ϵͳ����Ĭ������
	 */
	function getDefaultTask($k) {
		$current = $this->_timestamp;
		$next = $current + $this->_interval * $this->_hour; /*Ĭ�����ü��ʱ��*/
		$tasks = array(
			'alteradver' => array(
				'id' => 1,
				'name' => '��浽������',
				'task' => 'alteradver',
				'next' => $next
			)
		);
		return $tasks[$k];
	}
	/**
	 * ����һ�������¼
	 */
	function add($id, $name, $task, $next) {
		$this->_db->update("INSERT INTO pw_task" . " SET " . S::sqlSingle(array(
			'id' => ($id) ? $id : '',
			'name' => $name,
			'task' => $task,
			'count' => 1,
			'last' => $this->_timestamp,
			'next' => $next,
			'ctime' => $this->_timestamp,
		)));
	}
	/**
	 * ����һ�������¼
	 */
	function update($id, $name, $task, $next) {
		$this->_db->update("UPDATE pw_task SET count=count+1," . S::sqlSingle(array(
			'name' => $name,
			'task' => $task,
			'last' => $this->_timestamp,
			'next' => $next,
		)) . " WHERE id=" . S::sqlEscape($id));
	}
	/**
	 * ��ȡ��Ҫִ�е������б�
	 */
	function gets($current = null) {
		$current = $current ? $current : $this->_timestamp;
		$current = intval($current);
		if ($current < 1) {
			return array();
		}
		$result = array();
		$query = $this->_db->query("SELECT * FROM pw_task WHERE next<=" . $current);
		while ($rs = $this->_db->fetch_array($query)) {
			$result[$rs['id']] = $rs;
		}
		return $result;
	}
	/**
	 * ��ȡ����������Ϣ
	 */
	function get($id) {
		$id = intval($id);
		if ($id < 1) {
			return array();
		}
		return $this->_db->get_one("SELECT * FROM pw_task WHERE id=" . $id);
	}
	/*��֤�����Ƿ���Կ�ʼ*/
	function verify() {
		if (!$this->_openVerity) {
			return true;
		}
		$current = $this->_timestamp;
		$configs = $this->getFileCache();
		if ($configs['next'] <= $current) {
			$this->setFileCache();
			return true;
		}
		return false;
	}
	/*��ȡ��������*/
	function taskConfig() {
		$current = $this->_timestamp;
		$interval = $this->_runInterval;
		$last = $current;
		$next = $current + $interval * $this->_hour;
		return array(
			'interval' => $interval,
			'last' => $last,
			'next' => $next
		);
	}
	/**
	 * �����ļ�����
	 */
	function setFileCache() {
		$configs = $this->taskConfig();
		$tasks = "\$tasks=" . pw_var_export($configs) . ";";
		pwCache::setData($this->getCacheFileName(), "<?php\r\n" . $tasks . "\r\n?>");
		return $configs;
	}
	/**
	 * ��ȡ�ļ�����
	 */
	function getFileCache() {
		if (!$this->_cache) {
			return $this->taskConfig(); /*not open cache*/
		}
		@include S::escapePath($this->getCacheFileName());
		if ($tasks) {
			return $tasks;
		}
		return $this->setFileCache();
	}
	/*��ȡ�����ļ�·��*/
	function getCacheFileName() {
		return D_P . "data/bbscache/" . $this->_filename;
	}
	/**
	 * ��ȡ�����ļ���
	 */
	function _loadTask($name) {
		static $classes = array();
		$name = strtolower($name);
		$filename = R_P . "lib/task/task/" . $name . ".task.php";
		if (!is_file($filename)) {
			return null;
		}
		$class = 'Task_' . ucfirst($name);
		if (isset($classes[$class])) {
			return $classes[$class];
		}
		include S::escapePath($filename);
		$classes[$class] = new $class();
		return $classes[$class];
	}
}

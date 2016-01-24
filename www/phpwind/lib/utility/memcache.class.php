<?php
!defined('P_W') && exit('Forbidden');

/**
 * memcached�������������������֧��
 * 
 * @package Cache
 */
class PW_Memcache {
	var $cache = null;
	var $connected = null;
	var $config = array();

	/**
	 * ����Ĭ�������Զ�����Memcahce������,������ΪTRUEʱ
	 *
	 * @param bool $connect
	 * @return PW_Memcache
	 */
	function PW_Memcache($connect = true) {
		if ($this->exists()) {
			$this->cache = new Memcache;
			if ($connect) {
				$this->config = $GLOBALS['db_memcache'] ? $GLOBALS['db_memcache'] : array('host'=>'localhost','port'=>11211);
				$this->connect();
			}
		}
	}

	function addServer($host,$port) {
		$this->config[] = array('host'=>$host,'port'=>$port);
	}

	/**
	 * ����Memcache������
	 *
	 */
	function connect ($force = false) {
		if ($force && $this->isConnected()) {
			$this->close();
		}
		if (is_null($this->connected)) {
			$this->connected = true;
			if (isset($this->config[0])) {
				if (method_exists($this->cache, 'addServer')) {
					foreach ($this->config as $value) {
						$this->cache->addServer($value['host'],$value['port']);
					}
				} elseif (!$this->cache->connect($this->config[0]['host'],$this->config[0]['port'])) {
					$this->connected = false;
				}
			} elseif (!$this->cache->connect($this->config['host'],$this->config['port'])) {
				$this->connected = false;
			}
		}
	}

	function close() {
		if ($this->isConnected()) {
			$this->cache->close();
			$this->connected = null;
		}
	}

	/**
	 * ���memcache����
	 *
	 * @return bool
	 */
	function flush() {
		if (!$this->isConnected()) return false;
		return $this->cache->flush();
	}

	/**
	 * ɾ��ָ��KEY������
	 *
	 * @param string $key
	 * @return bool
	 */
	function delete($key) {
		if (!$this->isConnected()) return false;
		if(is_array($key)){
			foreach($key as $k){
				$k = $this->_getKeyPrefix($k);
				$this->cache->delete($k);
			}
		}else{
			$key = $this->_getKeyPrefix($key);
			$this->cache->delete($key);
		}
		return true;
	}

	/**
	 * ��������memcache��������
	 *
	 * @param array $data ��������,array('KEY'=>'VALUE')
	 * @param int $expire ���������Զ�����ʱ��(��)
	 * @return bool
	 */
	function update($data,$expire=86400) {
		if (!$this->isConnected()) return false;
		foreach ($data as $key=>$value) {
			$this->set($key,$value,$expire);
		}
		return true;
	}

	/**
	 * ����ָ��KEY�Ļ�������
	 *
	 * @param string $key ����KEY
	 * @param string $value
	 * @param int $expire
	 * @return bool
	 */
	function set($key,$value,$expire=86400) {
		if (!$this->isConnected()) return false;
		$key = $this->_getKeyPrefix($key);
		return $this->cache->set($key,$value,MEMCACHE_COMPRESSED,$expire);
	}

	/**
	 * ��ȡָ��KEY������
	 *
	 * @param string|array $keys
	 * @return string|array
	 */
	function get($keys) {
		if (!$this->isConnected()) return false;
		if (is_array($keys)) {
			$data = array();
			foreach ($keys as $key) {
				$result = $this->_get($key);
				if($result){
					$data[$key] = $result;
				}
			}
			return $data;
		} else {
			return $this->_get($keys);
		}
	}

	/**
	 * ��ȡMemcacheʵ��������
	 *
	 * @return object
	 */
	function &getMemcache() {
		if (!is_object($this->cache)) {
			$this->cache = new Memcache;
		}
		return $this->cache;
	}

	function isConnected() {
		return $this->connected === true ? true : false;
	}

	/**
	 * ��黷���Ƿ�֧��memcache���
	 *
	 * @return bool
	 */
	function exists() {
		if (class_exists('Memcache')) {
			return true;
		}
		return false;
	}
	function _getKeyPrefix($key){
		if(is_array($key)){
			$keys = array();
			foreach($key as $t_key){
				$keys[] = $this->__getKeyPrefix($t_key);
			}
			return $keys;
		}
		return $this->__getKeyPrefix($key);
	}
	
	function __getKeyPrefix($key){
		static $_prefix=null;
		if (!$_prefix) {
			$_prefix = substr(md5($GLOBALS['db_hash']),18,5);
		}
		return $_prefix.'_'.$key;
	}

	function _get($key) {
		if (!$this->isConnected()) return false;
		$key = $this->_getKeyPrefix($key);
		return $this->cache->get($key);
	}
}
?>
<?php
!defined('P_W') && exit('Forbidden');
require_once (R_P.'require/functions.php');
class PW_DataSourceService{
	/**
	 * ��ȡ����֧�ֵ��Զ����õ���������
	 */
	function getSourceTypes() {
		global $db_modes;
		$bbsTypes = $this->_getBBsSourceTypes();
		foreach ($db_modes as $key => $value) {
			$sourceTypesFile = S::escapePath(R_P . 'mode/' . $key . '/config/sourcetype.php');
			if (!file_exists($sourceTypesFile)) continue;
			$sourceTypes = include ($sourceTypesFile);
			$sourceTypes = $this->_cookModeSourceTypes($sourceTypes,$key);
			$bbsTypes = array_merge($bbsTypes,$sourceTypes);
		}
		return $bbsTypes;
	}
	function _getBBsSourceTypes() {
		static $sourceTypes = array();
		if (!$sourceTypes) {
			$sourceTypes = include_once(R_P.'require/sourcetype.php');
		}
		return $sourceTypes;
	}
	function _cookModeSourceTypes($sourceTypes,$mode) {
		foreach ($sourceTypes as $key=>$value) {
			$value['mode'] = $mode;
			$sourceTypes[$key] = $value;
		}
		return $sourceTypes;
	}
	/**
	 * ��ȡ����Դ������
	 * @param string $sourceType
	 * @param config $config
	 * @param int $num
	 * return array
	 */
	function getSourceData($piece) {
		$temp = $this->_getSourceData($piece['action'],$piece['config'],$piece['num']);
		if (!isset($piece['param']) || !is_array($piece['param'])) return $temp;
		return $this->_analyseResults($temp,$piece['param']);
	}
	/**
	 * ��ȡ����Դ����
	 * @param string $sourceType
	 * return array
	 */
	function getSourceConfig($sourceType) {
		$source = $this->_sourceFactory($sourceType);
		if (!is_object($source)) return array();
		return $source->getSourceConfig();
	}
	/**
	 * ��ȡ����Դ����˵��
	 * @param string $key
	 * @param string $sourceType
	 * return string
	 */
	function getSourceLang($key,$sourceType = '') {
		$source = $this->_sourceFactory($sourceType);
		if (!is_object($source)) return $this->_getDefalutLangByKey($key);

		$temp = $source->getSourceLang($key);
		if ($temp) return $temp;
		
		return $this->_getDefalutLangByKey($key);
	}
	/**
	 * ͨ������id��ȡ����Դ��Ϣ
	 * @param $sourceType
	 * @param $key
	 * @param $param
	 * return array
	 */
	function getRelateInfoByKey($sourceType,$key,$param) {
		$relateData = $this->_getRelateDataBySourceType($sourceType);
		if (!is_object($relateData)) return array();
		$temp = $relateData->getRelateDataByKey($key);
		
		return $this->_analyseResult($param,$temp);
	}
	/**
	 * ��ȡ��������Դ��html����ǰ̨��ȡ����
	 * @param $sourceType
	 * @param $default
	 * return array('title'=>string,'html'=>string)
	 */
	function getRelateHtmlForView($sourceType,$default=0) {
		$relateData = $this->_getRelateDataBySourceType($sourceType);
		if (!is_object($relateData)) return array();
		
		return $relateData->getHtmlForView($default);
	}
	
	function _getSourceData($sourceType,$config,$num) {
		$source = $this->_sourceFactory($sourceType);
		if (!is_object($source)) return array();
		return $source->getSourceData($config,$num);
	}
	
	function _getRelateDataBySourceType($sourceType) {
		$source = $this->_sourceFactory($sourceType);
		if (!is_object($source)) return false;
		$relateType = $source->getRelateType();
		return $this->_relateDataFactory($relateType);
	}
	
	function _getDefalutLangByKey($key) {
		$lang = $this->_getDefaultLang();
		return isset($lang[$key]) ? $lang[$key] : '';
	}
	
	function _getDefaultLang() {
		static $lang = array();
		if ($lang) return $lang;
		$lang = include(R_P.'mode/area/config/element_lang_config.php');
		return $lang;
	}
	
	function _analyseResults($results,$parameter){
		if (!is_array($results)) return array();
		if ($parameter && is_array($parameter)) {
			$temp = array();
			foreach ($results as $key=>$value) {
				$temp[$key] = $this->_analyseResult($parameter,$value);
			}
			$results = $temp;
		}
		return $results;
	}
	
	function _analyseResult($parameter,$value) {
		if (!$parameter) return array();
		$temp = array();
		foreach ($parameter as $k=>$val) {
			if (in_array($k,array('url','title','image','value','forumname','forumurl'))) {
				$temp_2 = $value[$k];
			} elseif ($k == 'descrip' && !isset($value[$k])) {//TODO
				$temp_2 = getDescripByTid($value['addition']['tid']);
			} elseif ($k == 'tagrelate') {
				$temp_2 = array();
			} elseif (isset($value[$k])) {
				$temp_2 = $value[$k];
			} elseif (isset($value['addition'][$k])) {
				$temp_2 = $value['addition'][$k];
			} elseif ($k == 'icon') {
				if (isset($value['uid'])) {
					$uid = $value['uid'];
				} elseif (isset($value['addition']['uid'])) {
					$uid = $value['addition']['uid'];
				} elseif (isset($value['authorid'])) {
					$uid = $value['authorid'];
				} elseif (isset($value['addition']['authorid'])) {
					$uid = $value['addition']['authorid'];
				}
				$temp_2 = $uid ? $this->_getUserIconByUid($uid) : '';
			} else {
				$temp_2 = '';
			}
			$temp[$k] = $this->_analyseResultByParameter($temp_2,$val,$k);
		}
		return $temp;
	}
	
	function _getUserIconByUid($uid) {
		$userInfo = getUserByUid($uid);
		if (!$userInfo) return '';
		require_once (R_P . 'require/showimg.php');
		$result = showfacedesign($userInfo['icon'], 1, 's');
		return $result[0];
	}
	
	function _analyseResultByParameter($result,$param,$addtion=''){
		if (is_array($result)) return $result;
		if ($param =='default') {
			$temp = $result;
		} elseif (is_numeric($param)) {
			$result = str_replace(array('&nbsp;','&lt;','&gt;'),' ',$result);
			$temp = substrs($result,$param,'');
		} elseif (preg_match('/^\d{1,3},\d{1,3}$/',$param)) {
			list($width,$height) = explode(',',$param);
			$temp = minImage($result,$width,$height);
		} elseif (preg_match('/^\w{1,4}(:|-)\w{1,4}((:|-)\w{1,4})?$/',$param)) {
			$temp = $result ? get_date($result,$param) : '';
		}
		return $temp;
	}
	
	function _relateDataFactory($type) {
		$white = array('subject');
		if (!in_array($type,$white)) return false;
		$className = $type.'relatedata';
		
		return L::loadClass($className,'area/relate');
	}
	
	function _sourceFactory($sourceType) {
		$sourceWhite = $this->getSourceTypes();
		if (!isset($sourceWhite[$sourceType])) return false;
		$className = $sourceType.'source';
		if (!isset($sourceWhite[$sourceType]['mode'])) return L::loadClass($className,'area/source');
		return $this->_getModeSourceFactory($className,$sourceWhite[$sourceType]['mode']);
	}
	
	function _getModeSourceFactory($className,$mode) {
		static $classes = array();
		if (isset($classes[$className])) return $classes[$className];
		$coreFile = S::escapePath(R_P . 'mode/'.$mode.'/require/core.php');
		if (file_exists($coreFile)) require_once $coreFile;
		
		$class = 'PW_' . $className;
		if (!class_exists($class)) {
			$fileDir = R_P . 'mode/'.$mode.'/lib/source/'.$className.'.class.php';
			if (file_exists($fileDir)) require_once S::escapePath($fileDir);
			if (!class_exists($class)) { //�ٴ���֤�Ƿ����class
				$GLOBALS['className'] = $class;
				Showmsg('���������Ͳ�����');
			}
		}
		
		$classes[$className] = &new $class(); //ʵ����
		return $classes[$className];
	}
	
}
<?php
!defined('P_W') && exit('Forbidden');

/**
 * ���۷���
 *
 * @package Rate
 */
class PW_Rate {
	var $_cache = TRUE; //�Ƿ����ļ�����

	/**
	 * ��ȡһ������ѡ��(����)��¼
	 *
	 */
	function getRateConfig($typeId, $id) {
		$id = intval($id);
		if ($id < 1) {
			return null;
		}
		$rateConfigDb = $this->_getRateConfigDB();
		return ($this->_cache) ? $this->_get_RateConfigCache($typeId, $id) : $rateConfigDb->get($id);
	}
	/**
	 * ����һ������ѡ��(����)��¼
	 *
	 */
	function addRateConfig($fieldData) {
		$fieldData = $this->_checkRateConfig($fieldData);
		if ($fieldData === FALSE) {
			return null;
		}
		$rateConfigDB = $this->_getRateConfigDB();
		$result = $rateConfigDB->add($fieldData);
		if ($this->_cache && $result) {
			$this->_set_RateConfigCache();
		}
		return $result;
	}
	function _checkRateConfig($fieldData) {
		$fieldData['title'] = trim($fieldData['title']);
		$fieldData['icon'] = trim($fieldData['icon']);
		$fieldData['isopen'] = intval($fieldData['isopen']); //�Ƿ���
		$fieldData['typeid'] = intval($fieldData['typeid']); //����
		if ($fieldData['title'] == "" || $fieldData['icon'] == "" || !in_array($fieldData['typeid'], $this->_getRateType()) || !in_array($fieldData['isopen'], array(
			0,
			1
		))) {
			return FALSE;
		}
		$fieldData['creditset'] = intval($fieldData['creditset']); //��ߵ�Լ��
		$fieldData['voternum'] = intval($fieldData['voternum']); //�����߻���
		$fieldData['authornum'] = intval($fieldData['authornum']); //���߻���
		if ($fieldData['voternum'] > 10 || $fieldData['voternum'] < - 10 || $fieldData['authornum'] > 10 || $fieldData['authornum'] < - 10) {
			return FALSE;
		}
		$fieldData['creator'] = trim($fieldData['creator']);
		$fieldData['updater'] = trim($fieldData['updater']);
		$rateConfigDB = $this->_getRateConfigDB();
		if (!$this->_isAllowFields($fieldData, $rateConfigDB->getStruct())) {
			return FALSE;
		}
		$fieldData['created_at'] = $fieldData['update_at'] = time();
		return $fieldData;
	}
	function _isAllowFields($sourceFields, $allowFields) {
		foreach($sourceFields as $key => $value) {
			if (!in_array($key, $allowFields)) {
				return FALSE;
			}
		}
		return TRUE;
	}
	/**
	 * ����һ������ѡ��(����)��¼
	 *
	 */
	function updateRateConfig($fieldData, $id) {
		$id = intval($id);
		$rateConfigDB = $this->_getRateConfigDB();
		if ($id < 1 || !$this->_isAllowFields($fieldData, $rateConfigDB->getStruct())) {
			return null;
		}
		$result = $rateConfigDB->update($fieldData, $id);
		if ($this->_cache && $result) {
			$this->_set_RateConfigCache();
		}
		return $result;
	}
	/**
	 * ɾ��һ������ѡ��(����)��¼
	 *
	 */
	function deleteRateConfig($id) {
		$id = intval($id);
		if ($id < 1) {
			return null;
		}
		$rateConfigDB = $this->_getRateConfigDB();
		$result = $rateConfigDB->delete($id);
		if ($this->_cache && $result) {
			$this->_set_RateConfigCache();
		}
		return $result;
	}
	/**
	 * �������ͻ�ȡ����(����)ѡ���¼
	 *
	 */
	function getsRateConfigByTypeId($typeId) {
		$typeId = intval($typeId);
		if ($typeId < 1) {
			return null;
		}
		$rateConfigDB = $this->_getRateConfigDB();
		//@todo �Ƿ����ļ�����
		if (!$this->_cache || !$rateConfigs = $this->_get_RateConfigCache($typeId)) {
			$rateConfigs = $rateConfigDB->getsByTypeId($typeId);
		}
		if (!$rateConfigs) {
			return null;
		}
		$tmp = array();
		foreach($rateConfigs as $config) {
			$config['tips'] = $this->_buildTips($config);
			$tmp[] = $config;
		}
		return $tmp;
	}
	function _buildTips($config) {
		$creditNames = $this->getCreditDefaultMap();
		$creditName = $creditNames[$config['creditset']];
		$tips = array();
		$tips[] = "������" . $creditName . $this->_setPrefix($config['voternum']);
		$tips[] = "����" . $creditName . $this->_setPrefix($config['authornum']);
		return implode("��", $tips);
	}
	function _setPrefix($value) {
		return ($value > 0) ? "+" . $value : $value;
	}
	/**
	 * ����һ�����ۼ�¼
	 * �ȼӵ��ٴ�log
	 *
	 */
	function addRate($userId, $objectId, $optionId, $typeId, $ip, $anonymity = FALSE) {
		//��¼�Ƿ��Ѿ����� û�����������������
		//�ȼ���û��Ƿ�����
		//@todo ���������Ч�� ����ȡ�������ߵ�ID
		global $credit;
		if (!$authorId = $this->_checkObjectByTypeId($typeId, $objectId)) {
			return null;
		}
		//�ټ�����۽����¼�Ƿ����
		if (!$this->getRateResultByOptionId($optionId, $objectId)) {
			$fieldData = array(
				'objectid' => $objectId,
				'optionid' => $optionId,
				'typeid' => $typeId,
				'num' => 1
			);
			$this->addRateResult($fieldData);
		} else {
			$this->updateRateResultByOptionId($optionId, $objectId);
		}
		$fieldData = array(
			'objectid' => $objectId,
			'optionid' => $optionId,
			'typeid' => $typeId,
			'uid' => $userId,
			'created_at' => time(),
			'ip' => $ip
		);
		$fieldData = $this->_checkRate($fieldData, $anonymity);
		if ($fieldData === FALSE) {
			return null;
		}
		$rateDB = $this->_getRateDB();
		$rateDB->add($fieldData);
		// TODO ���Ӷ�Ӧ�Ľ�һ���ֵ�
		//��ȡ������Ϣ �Ƿ��ȡ���棯�����ݿ�
		if (!$this->_cache || !$config = $this->_get_RateConfigCache($typeId, $optionId)) {
			$config = $this->getRateConfig($typeId, $optionId);
		}
		require_once S::escapePath(R_P . "require/credit.php");
		if ($config['creditset'] < 0) {
			$creditMap = array_flip($this->_getCreditMap());
			$cType = $creditMap[$config['creditset']];
		} else {
			$cType = $config['creditset'];
		}
		(!$anonymity) && $credit->set($userId, $cType, $config['voternum']);
		$credit->set($authorId, $cType, $config['authornum']);
		//�Ȱ����@todo
		$this->_addRateForHot($objectId, $typeId, $optionId);
		return $this->_buildTips($config);
	}
	/*******************************���۹����Ȱ�*start*****************************************/
	/**
	 * ��ȡĳ����������ѡ���б����ָ��ѡ��ID��ֱ�ӻ�ȡѡ���action
	 * ��������ʵ����array("rateThread_1" => "��ж�","rateThread_2" => "�����")
	 * ������־ʵ����array("rateDiary_10" => "��ж�","rateDiary_12" => "�����")
	 * ������Ƭʵ����array("ratePicture_20" => "��ж�","ratePicture_21" => "�����")
	 * ����KEY(�磺rateThread_1)Ϊ�Ȱ�app���ݱ��е�actionֵ
	 * ��ֱ����װ<select></select>
	 * @param int $typeId
	 * @param int $optionId
	 * @return array or string
	 */
	function _getRateTypesForHot($typeId, $optionId = FALSE) {
		$configs = $this->getsRateConfigByTypeId($typeId);
		$prefix = ($typeId == 1) ? "rateThread_" : (($typeId == 2) ? "rateDiary_" : "ratePicture_");
		$newConfigs = $actions = array();
		foreach($configs as $config) {
			$ratePrefix = $prefix . $config['id'];
			$actions[$config['id']] = $ratePrefix;
			$newConfigs[$ratePrefix] = $config['title'];
		}
		return ($optionId) ? $actions[$optionId] : $newConfigs;
	}
	function _addRateForHot($objectId, $typeId, $optionId) {
		$action = $this->_getRateTypesForHot($typeId, $optionId);
		require_once (R_P . 'require/functions.php');
		updateDatanalyse($objectId, $action, 1);
	}
	// ����
	function getRateThreadHotTypes() {
		return $this->_getRateTypesForHot(1);
	}
	// ��־
	function getRateDiaryHotTypes() {
		return $this->_getRateTypesForHot(2);
	}
	// ��Ƭ
	function getRatePictureHotTypes() {
		return $this->_getRateTypesForHot(3);
	}
	/*******************************���۹����Ȱ�*end*****************************************/
	function _checkRate($fieldData, $anonymity) {
		$fieldData['objectid'] = intval($fieldData['objectid']);
		$fieldData['optionid'] = intval($fieldData['optionid']);
		$fieldData['typeid'] = intval($fieldData['typeid']);
		$fieldData['uid'] = intval($fieldData['uid']);
		if (!$anonymity && $fieldData['uid'] < 0) {
			return FALSE;
		}
		$rateDB = $this->_getRateDB();
		if ($fieldData['objectid'] < 0 || !in_array($fieldData['typeid'], $this->_getRateType()) || $fieldData['optionid'] < 0 || !$this->_isAllowFields($fieldData, $rateDB->getStruct())) {
			return FALSE;
		}
		$fieldData['created_at'] = time();
		return $fieldData;
	}
	/**
	 * ����û�����ȡ���ۼ�¼
	 *
	 */
	function getsRateByUserId($userId, $objectId, $typeId) {
		$userId = intval($userId);
		$objectId = intval($objectId);
		$typeId = intval($typeId);
		if ($typeId < 1 || $userId < 1 || $objectId < 1) {
			return null;
		}
		$rateDB = $this->_getRateDB();
		return $rateDB->getsByUserId($userId, $objectId, $typeId);
	}
	/**
	 * ͳ�Ʊ���֮��
	 *
	 */
	function getRateByWeek($typeId) {
		$typeId = intval($typeId);
		if ($typeId < 1) {
			return null;
		}
		$rateDB = $this->_getRateDB();
		$RateResult = $rateDB->getFromTmpTableByWeek($typeId);
		//$RateResult = $this->_getRateDB ()->getByWeek ( $typeId );
		if (!$RateResult) {
			return null;
		}
		$tmp = array();
		foreach($RateResult as $result) {
			//��駶���ID��ȡ������������
			$result['objectInfo'] = $this->_getObjectByTypeId($typeId, $result['objectid']);
			$tmp[$result['optionid']] = array_merge($result['objectInfo'], $result);
		}
		return $tmp;
	}
	/*************************���ݴ������� start********************************/
	function _getObjectByTypeId($typeId, $objectId) {
		switch ($typeId) {
			case 1:
				return $this->_getThreadById($objectId);
				break;

			case 2:
				return $this->_getDiaryByById($objectId);
				break;

			case 3:
				return $this->_getPhotoById($objectId);
				break;

			default:
				return array();
		}
		return array();
	}
	function _getThreadById($tid) {
		$rateDB = $this->_getRateDB();
		$thread = $rateDB->_db->get_one("SELECT * FROM pw_threads WHERE tid={$tid}");
		if (!$thread) {
			return array();
		}
		$result = array();
		$result['title'] = $thread['subject'];
		$result['href'] = "/read.php?tid=" . $tid;
		$result['author'] = $thread['author'];
		$result['authorUrl'] = "/".USER_URL. $thread['authorid']; //authorid
		return $result;
	}
	function _getDiaryByById($did) {
		$rateDB = $this->_getRateDB();
		$diary = $rateDB->_db->get_one("SELECT * FROM pw_diary WHERE did={$did}");
		if (!$diary) {
			return array();
		}
		$result = array();
		$result['title'] = $diary['subject'];
		$result['href'] = "/apps.php?q=diary&u=" . $diary['uid'] . "&did=" . $did;
		$result['author'] = $diary['username'];
		$result['authorUrl'] = "/".USER_URL. $diary['uid']; //1
		return $result;
	}
	function _getPhotoById($pid) {
		$rateDB = $this->_getRateDB();
		$photo = $rateDB->_db->get_one("SELECT * FROM pw_cnphoto WHERE pid={$pid}");
		if (!$photo) {
			return array();
		}
		$album = $rateDB->_db->get_one("SELECT * FROM pw_cnalbum WHERE aid=" . $photo['aid']);
		$result = array();
		$result['title'] = (isset($photo['pintro']) && trim($photo['pintro']) != "") ? $photo['pintro'] : '��������';
		//$result ['href'] = "/apps.php?q=photos&a=view&pid=" . $pid;
		$result['href'] = "/apps.php?q=photos&space=1&u=" . $album['ownerid'] . "&a=view&pid=" . $pid;
		$result['author'] = $photo['uploader'];
		$result['authorUrl'] = "/".USER_URL. $album['ownerid'];
		return $result;
	}
	function _checkObjectByTypeId($typeId, $objectId) {
		switch ($typeId) {
			case 1:
				return $this->_checkThreadById($objectId);
				break;

			case 2:
				return $this->_checkDiaryByById($objectId);
				break;

			case 3:
				return $this->_checkPhotoById($objectId);
				break;

			default:
				return FALSE;
		}
		return TRUE;
	}
	function _checkThreadById($tid) {
		$rateDB = $this->_getRateDB();
		$thread = $rateDB->_db->get_one("SELECT * FROM pw_threads WHERE tid={$tid}");
		if (!$thread || !isset($thread['authorid'])) {
			return FALSE;
		}
		return $thread['authorid'];
	}
	function _checkDiaryByById($did) {
		$rateDB = $this->_getRateDB();
		$diary = $rateDB->_db->get_one("SELECT * FROM pw_diary WHERE did={$did}");
		if (!$diary || !isset($diary['uid'])) {
			return FALSE;
		}
		return $diary['uid'];
	}
	function _checkPhotoById($pid) {
		$rateDB = $this->_getRateDB();
		$photo = $rateDB->_db->get_one("SELECT * FROM pw_cnphoto WHERE pid={$pid}");
		if (!$photo) {
			return FALSE;
		}
		$album = $rateDB->_db->get_one("SELECT * FROM pw_cnalbum WHERE aid=" . $photo['aid']);
		if (!$album || !isset($album['ownerid'])) {
			return FALSE;
		}
		return $album['ownerid'];
	}
	function addConfigPower($powerData, $groupData) {
		setConfig('db_ratepower', serialize($powerData));
		setConfig('db_rategroup', serialize($groupData));
		updatecache_c();
	}
	function getUserGroupLevel() {
		$rateDB = $this->_getRateDB();
		$query = $rateDB->_db->query("SELECT gid,gptype,grouptitle,groupimg,grouppost FROM pw_usergroups ORDER BY grouppost,gid");
		$userGroups = $rateDB->_getAllResultFromQuery($query);
		$tmp = array();
		foreach($userGroups as $group) {
			if ($group['gptype'] == 'default' && $group['gid'] != 2) {
				continue;
			}
			if ($group['gptype'] == 'system' && $group['gid'] == 3) {
				continue;
			}
			$group['defaultTimes'] = ($group['gptype'] == 'member' && $group['gid'] == 8) ? 5 : 20;
			$tmp[$group['gptype']][] = $group;
		}
		//����
		$groups = array();
		$groups['member'] = $tmp['member'];
		$groups['system'] = $tmp['system'];
		$groups['special'] = $tmp['special'];
		$groups['default'] = $tmp['default'];
		return $groups;
	}
	function _getBaseDB() {
		require_once S::escapePath(dirname(__FILE__) . "/base/basedb.php");
		return new BaseDB();
	}
	/*************************���ݴ������� end********************************/
	/**
	 * ����һ�����۽����¼
	 *
	 */
	function addRateResult($fieldData) {
		$fieldData = $this->_checkRateResult($fieldData);
		if ($fieldData === FALSE) {
			return null;
		}
		$rateResultDB = $this->_getRateResultDB();
		return $rateResultDB->add($fieldData);
	}
	function _checkRateResult($fieldData) {
		$fieldData['objectid'] = intval($fieldData['objectid']);
		$fieldData['optionid'] = intval($fieldData['optionid']);
		$fieldData['typeid'] = intval($fieldData['typeid']);
		if ($fieldData['objectid'] < 1 || $fieldData['optionid'] < 1 || !in_array($fieldData['typeid'], $this->_getRateType())) {
			return FALSE;
		}
		$fieldData['num'] = 1;
		return $fieldData;
	}
	function _getRateType() {
		return array(
			1,
			2,
			3
		);
	}
	/**
	 * ��駶���ID��ѡ��ID��ȡ���ۼ�¼
	 *
	 */
	function getRateResultByOptionId($optionId, $objectId) {
		$objectId = intval($objectId);
		$optionId = intval($optionId);
		if ($optionId < 1 || $objectId < 1) {
			return null;
		}
		$rateResultDB = $this->_getRateResultDB();
		return $rateResultDB->getByOptionId($optionId, $objectId);
	}
	/**
	 * ��駶���ID��ѡ��ID�������ۼ�¼
	 *
	 */
	function updateRateResultByOptionId($optionId, $objectId) {
		$objectId = intval($objectId);
		$optionId = intval($optionId);
		if ($optionId < 1 || $objectId < 1) {
			return null;
		}
		$rateResultDB = $this->_getRateResultDB();
		return $rateResultDB->updateByOptionId($optionId, $objectId);
	}
	/**
	 * �������ID��ѡ��ID��ȡ���ۼ�¼
	 *
	 */
	function getRateResultByTypeId($typeId, $objectId) {
		$typeId = intval($typeId);
		$objectId = intval($objectId);
		if ($typeId < 1 || $objectId < 1) {
			return null;
		}
		$tmp = array();
		$total = 0; //��������
		$rateResultDB = $this->_getRateResultDB();
		$rateResults = $rateResultDB->getByTypeId($typeId, $objectId);
		if (!$rateResults) {
			return array(
				$tmp,
				$total
			);
		}
		foreach($rateResults as $result) {
			$total += $result['num'];
			$tmp[$result['optionid']] = $result;
		}
		return array(
			$tmp,
			$total
		);
	}
	/**
	 * ��ȡ�������ö�Ӧ��
	 * ��Ҫ�Ǹ�ÿ������ѡ������һ��Ψһ��key���������ݴ洢
	 * ϵͳĬ�ϵ�Ϊ�������Զ����Ϊ��Ψһ������
	 * ͬʱ�ύĳ�ض�key������
	 * @return unknown
	 */
	function getCreditDefaultMap($creditKey = null) {
		$map = $this->_getCreditMap();
		$creditNames = pwCreditNames();
		$tmp = array();
		foreach($creditNames as $key => $value) {
			if (in_array($key, array_keys($map))) {
				$tmp[$map[$key]] = $value;
				continue;
			}
			$tmp[$key] = $value;
		}
		return (array_key_exists($creditKey, $tmp)) ? $tmp[$creditKey] : $tmp;
	}
	function _getCreditMap() {
		return array(
			"money" => "-1",
			"rvrc" => "-2",
			"credit" => "-3",
			"currency" => "-4"
		);
	}
	/************************************�ļ���������start***********************************************/
	function _set_RateConfigCache() {
		$rateConfigDB = $this->_getRateConfigDB();
		$configs = $rateConfigDB->gets();
		if (!$configs) {
			return null;
		}
		$tmp = array();
		foreach($configs as $config) {
			$tmp[$config['typeid']][] = $config;
		}
		//д�뻺���ļ�
		$result = serialize($tmp);
		pwCache::setData($this->_getReteConfigFilePath(), $result, false, 'w');
		return $result;
	}
	// ���ָ�������򷵻ض�Ӧ������
	function _get_RateConfigCache($typeId = FALSE, $optionId = FALSE) {
		if (!file_exists($this->_getReteConfigFilePath()) || !$result = readover($this->_getReteConfigFilePath())) {
			$result = $this->_set_RateConfigCache();
		}
		$rateConfigs = unserialize($result);
		if (!$rateConfigs) {
			return FALSE;
		}
		//ȡ�ض�ĳ�����������ض��ĵ�������
		if ($optionId && $typeId && isset($rateConfigs[$typeId])) {
			foreach($rateConfigs[$typeId] as $config) {
				if ($config['id'] == $optionId) {
					return $config;
				}
			}
		}
		//ֻȡĳ�����͵�����
		if (isset($rateConfigs[$typeId]) && in_array($typeId, $this->_getRateType())) {
			return $rateConfigs[$typeId];
		}
		return $rateConfigs;
	}
	function _getReteConfigFilePath() {
		return D_P . 'data/bbscache/rate_config.php';
	}
	/***********************************************************************************/
	function countByUserId($userId) {
		if (intval($userId) < 1) {
			return -1;
		}
		$rateDB = $this->_getRateDB();
		return $rateDB->countByUserId($userId);
	}
	function countByIp($ip) {
		if ($ip == "") {
			return -1;
		}
		$rateDB = $this->_getRateDB();
		return $rateDB->countByIp($ip);
	}
	function getsByIp($ip, $objectId, $typeId) {
		$rateDB = $this->_getRateDB();
		return $rateDB->getsByIp($ip, $objectId, $typeId);
	}
	/**********************************************************/
	function getWeekData($typeId, $hotSource = true) {
		if ($hotSource) {
			return $this->getWeekResultHtmlFromHot($typeId);
		}
		return $this->getRateByWeek($typeId);
	}
	function getWeekResultHtmlFromHot($typeId) {
		$datanalyse = $this->_getDatanalyseService();
		$result = $datanalyse->getDatanalyseForRateByType($typeId);
		if (!$result) {
			return '';
		}
		$tmp = array();
		foreach($result as $objectId => $object) {
			$info = array();
			$info['title'] = $object['title'];
			$info['href'] = "/" . $object['url'];
			$info['author'] = $object['author'];
			$info['authorUrl'] = "/u.php?username=" . $object['author'];
			$optionId = substr($object['action'], strrpos($object['action'], "_") + 1);
			$info['objectid'] = $objectId;
			$info['optionid'] = $optionId;
			$info['typeid'] = $typeId;
			$tmp[$optionId]['objectInfo'] = $info;
		}
		return $tmp;
	}
	function _getDatanalyseService() {
		L::loadClass('datanalyse','datanalyse',false);
		return new Datanalyse();
	}
	/***********************************************************/
	function _getRateConfigDB() {
		return L::loadDB('RateConfig', 'rate');
	}
	function _getRateDB() {
		return L::loadDB('Rate', 'rate');
	}
	function _getRateResultDB() {
		return L::loadDB('RateResult', 'rate');
	}
}
?>

<?php
! defined ( 'P_W' ) && exit ( 'Forbidden' );

class PW_PortalPageService {
	/**
	 * ����ģ��
	 * @param $sign
	 */
	function updateInvokesByModuleConfig($sign) {
		$moduleConfig = $this->_getModuleConfigBySign($sign);
		$moduleConfig->updateInvokesByModuleConfig($sign);
	}
	/**
	 * ��ȡĳ��ģ�����
	 * @param $sign
	 * @param $invokeName
	 */
	function getPiecesCode($sign,$invokeName) {
		$moduleConfig = $this->_getModuleConfigBySign($sign);
		return $moduleConfig->getPiecesCode($sign,$invokeName);
	}
	/**
	 * ����ĳ��ģ�����
	 * @param $sign
	 * @param $name
	 * @param $code
	 */
	function updateModuleCode($sign,$name,$code) {
		$moduleConfig = $this->_getModuleConfigBySign($sign);
		$moduleConfig->updateModuleCode($sign,$name,$code);
	}

	/**
	 * ͨ�����������޸�ģ�����
	 * @param $sign
	 * @param $name
	 * @param $pieceConfig
	 * @param $title
	 */
	function updateModuleByConfig($sign,$name,$pieceConfig,$title='') {
		$moduleConfig = $this->_getModuleConfigBySign($sign);
		$moduleConfig->updateModuleByConfig($sign,$name,$pieceConfig,$title);
	}
	/**
	 * ʵʱ����ģ��
	 * @param $sign
	 */
	function updateTemplateCache($sign) {
		$moduleConfig = $this->_getModuleConfigBySign($sign);
		$moduleConfig->afterUpdate($sign);
	}
	/**
	 * ��֤�Ƿ����������ӻ�ҳ��
	 * @param $sign
	 */
	function checkPortal($sign) {
		$portalPages = $this->getOtherPortalPages();
		return isset($portalPages[$sign]);
	}
	/**
	 * ��ȡҳ������
	 * @param $sign
	 */
	function getSignType($sign) {
		static $channels = array();
		if (!$channels) {
			$channelService = L::loadClass('channelService', 'area');
			$channels = $channelService->getChannels();
		}
		if (isset($channels[$sign])) {
			return 'channel';
		}
		return 'other';
	}
	/**
	 * ��ȡ��Ч��channelid����ǰ̨����
	 * @param $sign
	 */
	function getSignForManage($sign) {
		$signType = $this->getSignType($sign);
		if ($signType == 'other') return $sign;

		$channelService = $this->_getChannelService();
		$channelInfo = $channelService->getChannelInfoByAlias($sign);
		if (!$channelInfo) return 0;
		return $channelInfo['id'];
	}
	/**
	 * ��ȡ���ӻ�ҳ�漰��ҳ���ģ��
	 * @param $ifverify
	 */
	function getPortalInvokes($ifverify,$ifHTML = 0) {
		$channels = $this->_getChannelsInvokes($ifverify,$ifHTML);
		$otherPages = $this->_getOtherPageInvokes($ifverify,$ifHTML);
		return $channels + $otherPages;
	}
	/**
	 * ����ĳ�����ӻ�ҳ����Ƿ���Ҫ��̬����״̬
	 * @param $sign
	 * @param $state
	 */
	function setPortalStaticState($sign,$state=0) {
		require_once(R_P.'admin/cache.php');
		$portal_staticstate = $this->getPortalStaticState();
		$portal_staticstate[$sign] = (int) $state;
		setConfig('portal_staticstate', $portal_staticstate, null,true);
		updatecache_conf('portal', true);
	}

	function getPortalStaticState() {
		static $result = array();
		if (!isset($result['state']) && is_file(D_P.'data/bbscache/portal_config.php')) {
			//* include pwCache::getPath(D_P.'data/bbscache/portal_config.php');
			extract(pwCache::getData(D_P.'data/bbscache/portal_config.php', false));
			$result['state'] = $portal_staticstate;
		}
		return $result['state'] ? $result['state'] : array();
	}

	function getPageInvokesForSelect($sign, $ifverify = 0,$ifHTML=0) {
		$signType = $this->getSignType($sign);
		$invokeService = L::loadClass('invokeservice', 'area');
		return $invokeService->getPageInvokesForSelect($signType,$sign,$ifverify,$ifHTML);
	}

	function getPortalPages() {
		$channelService = L::loadClass('channelService', 'area');
		$channels = $channelService->getChannels();

		$otherPages = $this->getOtherPortalPages();
		foreach ($otherPages as $key=>$value) {
			$channels[$key] = array('name'=>$value);
		}
		return $channels;
	}


	function getOtherPortalPages() {
		static $portalPages = array();
		if (!$portalPages) {
			$portalPages = $this->_getOtherPortalPages();
		}
		return $portalPages;
	}
	/**
	 * �����ݿ����һ������
	 * @param string $sign
	 * @return array
	 */
	function getPortalPageInfo($sign) {
		$portalPageDB = $this->_getPortalPageDB();
		return $portalPageDB->getData($sign);
	}
	/**
	 * ��ȡ���ݿ��е�ҳ��洢����
	 * @return array
	 */
	function getPortalPagesFromDB() {
		$portalPageDB = $this->_getPortalPageDB();
		return $portalPageDB->getAll();
	}
	/**
	 * �����ݱ������һ������
	 * @param array $array
	 * @return int | bool
	 */
	function addPortalPage($array) {
		$array = $this->_cookPortalPageData($array);
		if (!$array) return false;
		$portalPageDB = $this->_getPortalPageDB();
		return $portalPageDB->add($array);
	}
	
	function deletePortalPage($sign) {
		$portalPageDB = $this->_getPortalPageDB();
		$portalPageDB->deleteBySign($sign);
		
		$invokeService = L::loadClass('invokeservice', 'area');
		$pageInvokes = $invokeService->getPortalPageInvokes($sign);
		foreach ($pageInvokes as $invoke) {
			$invokeService->deleteInvoke($invoke['name']);
		}
	}
	
	function _cookPortalPageData($array) {
		$temp = array();
		if (!$array['sign'] || !$array['title']) return array();
		$temp['sign'] = $array['sign'];
		$temp['title'] = $array['title'];
		return $temp;
	}

	function _getOtherPortalPages() {
		global $db_modes;
		$result = array();
		if (file_exists(R_P.'require/portalpages.php')) {
			$result = include(R_P.'require/portalpages.php');
		}
		foreach ($db_modes as $key=>$value) {
			$portalPagesFile = S::escapePath(R_P . 'mode/' . $key . '/config/portalpages.php');
			if (!file_exists($portalPagesFile)) continue;
			$pages = include ($portalPagesFile);
			$result = array_merge($result,$pages);
		}
		$portalPagesFromDB = $this->getPortalPagesFromDB();
		foreach ($portalPagesFromDB as $value) {
			$result[$value['sign']] = $value['title'];
		}
		return $result;
	}

	function moduleConfigFactory($type) {
		switch ($type) {
			case 'channel':
				return L::loadClass('channelmoduleconfig','area/moduleconfig');
			default :
				return L::loadClass('othermoduleconfig','area/moduleconfig');
		}
	}

	function _getOtherPageInvokes($ifverify=0,$ifHTML = 0) {
		$portalPages = $this->getOtherPortalPages();

		$invokeService = L::loadClass('invokeservice', 'area');

		$result = array();
		foreach ($portalPages as $key=>$val) {
			$temp = $invokeService->getPortalInvokesForSelect($key,$ifverify,$ifHTML);
			if (!$temp) continue;
			$result[$key]['name']=$val;
			$result[$key]['invokes']= $this->_cookInvokes($temp);
		}
		return $result;
	}


	function _getChannelsInvokes($ifverify=0,$ifHTML = 0) {
		$channels_info_array = array();
		$channelService = $this->_getChannelService();
		$channels_info=$channelService->getChannels();
		$invokeService = L::loadClass('invokeservice', 'area');
		if (!$channels_info) return $channels_info_array;

		foreach ($channels_info as $val) {
			$temp = $invokeService->getChannelInvokesForSelect($val['alias'],$ifverify,$ifHTML);
			if (!$temp) continue;
			$channels_info_array[$val['id']]['name']=$val['name'];
			$channels_info_array[$val['id']]['invokes']= $this->_cookInvokes($temp);
		}
		return $channels_info_array;
	}

	function _getModuleConfigBySign($sign) {
		$type = $this->getSignType($sign);
		return $this->moduleConfigFactory($type);
	}

	function _cookInvokes($invokes) {
		$temp = array();
		foreach ($invokes as $key=>$value) {
			$temp[$value['invokename']] = $value['title'];
		}
		return $temp;
	}

	function _getChannelService() {
		return L::loadClass('channelservice','area');
	}
	
	function _getPortalPageDB() {
		return L::loadDB('portalpage','area');
	}
}
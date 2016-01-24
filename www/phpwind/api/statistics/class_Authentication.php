<?php
/**
 * phpwind ʵ����֤����ͳ��
 * 
 * @author phpwind team
 * @version 1.0
 * @package api
 */
!defined('P_W') && exit('Forbidden');
require_once(R_P . 'api/class_Statistics.php');

class Statistics_Authentication extends Statistics {
	
	/**
	 * ÿ��ͨ��ʵ����֤���û���
	 * @param string $day 'Y-m-d'
	 * @return int 
	 */
	function getUserCountOfDay($day = null) {
		$platformApiClient = $this->_getPlatformApiClient();
		$response = $this->_jsonDecode($platformApiClient->get('credit.statistics.usercount', array('day' => $day)));
		$day == null && $day = get_date(time(),'Y-m-d');
		if (isset($response['count'][$day])) {
			return new ApiResponse($response['count'][$day]);
		}
		return new ApiResponse(0);
	}

	/**
	 * ʵ����֤�ķ�ʽ����״̬ͼ
	 * @return array 
	 */
	function getUserPercentCount() {
		$platformApiClient = $this->_getPlatformApiClient();
		$response = $this->_jsonDecode($platformApiClient->get('credit.statistics.userpercentcount', array()));
		if (isset($response['data'])) {
			return new ApiResponse($response['data']);
		}
		return new ApiResponse(array());
	}

	/**
	 * ͳ����֤�û�����Դ
	 * @return array 
	 */
	function getUserFrom() {
		$platformApiClient = $this->_getPlatformApiClient();
		$response = $this->_jsonDecode($platformApiClient->get('credit.statistics.getuserfrom', array()));
		if (isset($response['list'])) {
			return new ApiResponse($response['list']);
		}
		return new ApiResponse(array());
	}
	
	/**
	 * ĳ�յ�ʵ����֤��֤�뷢����
	 * @param string $day 'Y-m-d'
	 * @return int
	 */
	function getCodeSentNumOfDay($day = null) {
		$platformApiClient = $this->_getPlatformApiClient();
		$day == null && $day = get_date(time(),'Y-m-d');
		$response = (int) $this->_jsonDecode($platformApiClient->get('credit.statistics.countsitemobileverifybyday', array('day' => $day)));
		return new ApiResponse($response);	
	}

	/**
	 * @return PlatformApiClient
	 */
	function _getPlatformApiClient() {
		static $client = null;
		if (null === $client) {
			global $db_sitehash, $db_siteownerid;
			L::loadClass('client', 'utility/platformapisdk', false);
			$client = new PlatformApiClient($db_sitehash, $db_siteownerid);
		}
		return $client;
	}

	function _jsonDecode($response) {
		require_once(R_P . 'api/class_json.php');
		$json = new Services_JSON(true);
		return $json->decode($response);
	}
}
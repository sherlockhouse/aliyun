<?php
/**
 * phpwind ����΢������ͳ��
 * 
 * @author phpwind team
 * @version 1.0
 * @package api
 */
!defined('P_W') && exit('Forbidden');
require_once(R_P . 'api/class_Statistics.php');

class Statistics_Sinaweibo extends Statistics {
	
	/**
	 * ÿ����û�����
	 * @param string $day 'Y-m-d'
	 * @return int 
	 */

	function getBindOfDay($day = null) {
		$platformApiClient = $this->_getPlatformApiClient();
		$response = $platformApiClient->get('weibo.stat.site', array('time' => $day, 'type' => 'bound'));
		return new ApiResponse($response);
	}
	
	/**
	 * վ��ÿ�յ����˵�΢������
	 * @param string $day 'Y-m-d'
	 * @return int 
	 */

	function getSiteToSinaWeiboOfDay($day = null) {
		$platformApiClient = $this->_getPlatformApiClient();
		$response = $platformApiClient->get('weibo.stat.site', array('time' => $day, 'type' => 'siteToSinaWeibo'));
		return new ApiResponse($response);
	}

	/**
	 * վ��ÿ�յ����˵���������
	 * @param string $day 'Y-m-d'
	 * @return int 
	 */

	function getSiteToSinaCommentOfDay($day = null) {
		$platformApiClient = $this->_getPlatformApiClient();
		$response = $platformApiClient->get('weibo.stat.site', array('time' => $day, 'type' => 'siteToSinaComment'));
		return new ApiResponse($response);
	}

	/**
	 * վ��ÿ�յ����˵�ת������
	 * @param string $day 'Y-m-d'
	 * @return int 
	 */

	function getSiteToSinaForwardOfDay($day = null) {
		$platformApiClient = $this->_getPlatformApiClient();
		$response = $platformApiClient->get('weibo.stat.site', array('time' => $day, 'type' => 'siteToSinaForward'));
		return new ApiResponse($response);
	}

	/**
	 * ����ÿ�յ�վ���΢������
	 * @param string $day 'Y-m-d'
	 * @return int 
	 */

	function getSinaToSiteWeiboOfDay($day = null) {
		$platformApiClient = $this->_getPlatformApiClient();
		$response = $platformApiClient->get('weibo.stat.site', array('time' => $day, 'type' => 'sinaToSiteWeibo'));
		return new ApiResponse($response);
	}

	/**
	 * ����ÿ�յ�վ�����������
	 * @param string $day 'Y-m-d'
	 * @return int 
	 */

	function getSinaToSiteCommentOfDay($day = null) {
		$platformApiClient = $this->_getPlatformApiClient();
		$response = $platformApiClient->get('weibo.stat.site', array('time' => $day, 'type' => 'sinaToSiteComment'));
		return new ApiResponse($response);
	}

	/**
	 * ����ÿ�յ�վ���ת������
	 * @param string $day 'Y-m-d'
	 * @return int 
	 */

	function getSinaToSiteForwardOfDay($day = null) {
		$platformApiClient = $this->_getPlatformApiClient();
		$response = $platformApiClient->get('weibo.stat.site', array('time' => $day, 'type' => 'sinaToSiteForward'));
		return new ApiResponse($response);
	}
	
	/**
	 * (30��)վ��ת�����˵��û�top10����������
	 * @param int $days
	 * @param int $num
	 * @return array
	 */
	function getTopUsersForSiteToSinaWeibo($days = 30, $num = 10) {
		return $this->_getTopUsers('siteToSinaWeibo', $days, $num);
	}
	
	/**
	 * (30��)վ��ת�����˵������û�top10����������
	 * @param int $days
	 * @param int $num
	 * @return array
	 */
	function getTopUsersForSiteToSinaComment($days = 30, $num = 10) {
		return $this->_getTopUsers('siteToSinaComment', $days, $num);
	}
	
	/**
	 * (30��)վ��ת�����˵�ת���û�top10����������
	 * @param int $days
	 * @param int $num
	 * @return array
	 */
	function getTopUsersForSiteToSinaForward($days = 30, $num = 10) {
		return $this->_getTopUsers('siteToSinaForward', $days, $num);
	}
	
	/**
	 * (30��)����ת��վ����û�top10����������
	 * @param int $days
	 * @param int $num
	 * @return array
	 */
	function getTopUsersForSinaToSiteWeibo($days = 30, $num = 10) {
		return $this->_getTopUsers('sinaToSiteWeibo', $days, $num);
	}
	
	/**
	 * (30��)����ת��վ��������û�top10����������
	 * @param int $days
	 * @param int $num
	 * @return array
	 */
	function getTopUsersForSinaToSiteComment($days = 30, $num = 10) {
		return $this->_getTopUsers('sinaToSiteComment', $days, $num);
	}
	
	/**
	 * (30��)����ת��վ���ת���û�top10����������
	 * @param int $days
	 * @param int $num
	 * @return array
	 */
	function getTopUsersForSinaToSiteForward($days = 30, $num = 10) {
		return $this->_getTopUsers('sinaToSiteForward', $days, $num);
	}
	
	/**
	 * ����΢����װ���
	 */
	function getInstallInfo() {
		$platformApiClient = $this->_getPlatformApiClient();
		$response = $this->_jsonDecode($platformApiClient->get('weibo.stat.siteinfo'));
		return new ApiResponse($response);
	}
	
	/**
	 * (30��)������վ����û�top10�ķ�װ����������
	 * @param string $type ͳ������
	 * @param int $days
	 * @param int $num
	 * @return array
	 */
	function _getTopUsers($type, $days = 30, $num = 10) {
		$platformApiClient = $this->_getPlatformApiClient();
		$topUsers = $this->_jsonDecode($platformApiClient->get('weibo.stat.user', array('type' => $type, 'day' => $days, 'offset' => $num)));
		if (!$topUsers || !is_array($topUsers)) return new ApiResponse(array());
		
		$userService = L::loadClass('userService', 'user'); /* @var $userService PW_UserService */
		$uids = $uidToStats = $userSort =  array();
		$i = 0;
		foreach ($topUsers as $topUser) {
			 $uids[] = $topUser['uid'];
			 $uidToStats[$topUser['uid']] = array($topUser['total']);
		}

		require_once R_P.'require/showimg.php';
		foreach ($userService->getByUserIds($uids) as $rt) {
			$uidToStats[$rt['uid']] = array_merge($uidToStats[$rt['uid']], array(
				$rt['uid'],
				$rt['username'],
				showfacedesign($rt['icon']),
			));
		}
		return new ApiResponse($uidToStats);
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
	
	function _jsonDecode ($response) {
		require_once(R_P . 'api/class_json.php');
		$json = new Services_JSON(true);
		return $json->decode($response);
	}
}
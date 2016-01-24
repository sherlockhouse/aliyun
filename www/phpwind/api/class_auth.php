<?php

!defined('P_W') && exit('Forbidden');

class Auth {
	
	var $base;
	var $db;
	var $result;
	
	function Credit($base) {
		$this->base = $base;
		$this->db = $base->db;
		$this->result = array('error' => 1);
	}
	
	function addmobileauth($uid,$mobile){
		if (!preg_match('/^\d{11}$/',$mobile)){
			$this->result['message'] = '�ֻ�����Ƿ�';
			return new ApiResponse($this->result);
		}
		$userService = L::loadClass('userservice', 'user');/* @var $userService PW_Userservice */
		$uid = intval($uid);
		$userinfo = $userService->get($uid);
		if (!S::isArray($userinfo)){
			$this->result['message'] = '�û���"' . $username . '"δ�ҵ�';
			return new ApiResponse($this->result);
		} elseif ($userinfo['authmobile'] && getstatus($userinfo['userstatus'], PW_USERSTATUS_AUTHMOBILE)) {
			$this->result['message'] = '���û�������ֻ�ʵ����֤';
			$this->result['authmobile'] = $userinfo['authmobile'];
			return new ApiResponse($this->result);
		}
		$userService->update($userinfo['uid'], array('authmobile' => $mobile));
		$userService->setUserStatus($userinfo['uid'], PW_USERSTATUS_AUTHMOBILE, true);
		//�䷢ѫ��
		if ($db_md_ifopen) {
			$medalService = L::loadClass('medalservice','medal');
			$medalService->awardMedalByIdentify($userinfo['uid'],'shimingrenzheng');
		}
		require_once R_P.'require/functions.php';
		initJob($userinfo['uid'],'doAuthMobile');
		$this->result['error'] = 0;
		return new ApiResponse($this->result);
	}
	
	function deleteauth($uid) {
		$uid = intval($uid);
		$userService = L::loadClass('userservice', 'user');/* @var $userService PW_Userservice */
		$userinfo = $userService->get($uid);
		if (!S::isArray($userinfo)){
			$this->result['message'] = '�û�Id:"' . $uid . '"δ�ҵ�';
			return new ApiResponse($this->result);
		/*	
		} elseif (!getstatus($userinfo['userstatus'], PW_USERSTATUS_AUTHMOBILE)) {
			$this->result['message'] = '���û�δ�����ֻ�ʵ����֤';
			return new ApiResponse($this->result);
			*/
		}
		$userService->update($userinfo['uid'], array('authmobile' => ''));
		$userService->setUserStatus($userinfo['uid'], PW_USERSTATUS_AUTHMOBILE, false);
		$userService->setUserStatus($userinfo['uid'], PW_USERSTATUS_AUTHALIPAY, false);
		$userService->setUserStatus($userinfo['uid'], PW_USERSTATUS_AUTHCERTIFICATE, false);
		$this->result['error'] = 0;
		return new ApiResponse($this->result);
	}
}
<?php
!defined('P_W') && exit('Forbidden');
/**
 * ���ֻ���
 */
class Helper {
	/**
	 * ��ȡȫ�ֱ��� ȫ�ְ�ȫ���
	 * @param $key ����
	 */
	function _getGlobal($key) {
		return isset($GLOBALS[$key]) ? $GLOBALS[$key] : '';
	}
	/**
	 * ��װ��ͼģ��
	 * @param $controller
	 * @param $action
	 */
	function _buildTemplate($controller,$action){
		return $controller.'.'.$action;
	}
	
}
<?php
!defined('P_W') && exit('Forbidden');
/*
 * ·�ɷַ���
 */
class PW_Router {
	function run($configs) {
		list($controller, $action, $viewerPath, $className, $actionName, $path) = $this->init($configs);
		if (!is_file($path)) {
			Error::showError("·��������" . $path);
		}
		require_once S::escapePath($path);
		if (!class_exists($className, true)) {
			Error::showError("����������" . $className);
		}
		$obj = new $className();
		if ($action && !is_callable(array($obj,	$action))) {
			Error::showError("������������" . $action);
		}
		if (in_array($action, array($className,"execute","__construct","init","before","after"))) {
			Error::showError("������������" . $className);
		}
		$obj->execute($controller, $action, $viewerPath);
	}
	function init($configs) {
		$this->_check();
		if (!S::IsArray($configs)) {
			Error::showError("��ָ��·������");
		}
		$controller = ctype_alpha($configs['c']) ? strtolower(trim($configs['c'])) : "index";
		$action = ctype_alpha($configs['a']) ? strtolower(trim($configs['a'])) : "run";
		$className = $controller . "controller";
		$actionName = $action;
		$path = APP_CONTROLLER . $className . ".php";
		$viewerPath = APP_VIEWER;
		return array($controller,$action,$viewerPath,$className,$actionName,$path);
	}
	
	/**
	 * ����ʼ�������Ƿ���
	 */
	function _check() {
		if (!defined('APP_VIEWER') || !defined('APP_CONTROLLER')) {
			Error::showError("you shoule config APP_VIEWER|APP_CONTROLLER");
		}
	}
}
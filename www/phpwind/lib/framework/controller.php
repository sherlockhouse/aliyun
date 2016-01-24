<?php
!defined('P_W') && exit('Forbidden');
/**
 * ������Ʋ�
 */
class Controller {
	var $_viewer; //ȫ����ͼ����
	var $_controller; //������
	var $_action; //����
	var $_layoutFile; //�����ļ�
	var $_layoutExt = 'htm'; //�����ļ���׺
	var $_viewPath; //��ͼ·��
	var $_template; //��ͼģ��
	var $_partial;//����ģ��Ŀ¼
	
	function __construct() {
		$this->_viewer = new stdClass();
		$this->_layoutExt = 'htm';
	}
	
	function Controller() {
		$this->__construct();
	}
	
	function execute($controller, $action, $viewerPath) {
		$this->_init($controller, $action, $viewerPath);
		if ($this->_before()) {
			$this->$action();
			$this->_after();
		}
		$this->_render();
		$this->_output();
	}
	/**
	 * ��ʼ��������
	 */
	function _init($controller, $action, $viewerPath) {
		$this->_controller = $controller;
		$this->_action = $action;
		$this->_setViewPath($viewerPath);
	}
	/**
	 * ִ�ж���ǰ�Ĳ���
	 */
	function _before() {
		return true;
	}
	/**
	 * Ĭ��ִ�ж���
	 */
	function run() {
	}
	/**
	 * ִ�ж�����Ĳ���
	 */
	function _after() {
	}
	/**
	 * ִ�ж���������
	 */
	function _output() {
	}
	
	function _render() {
		$layoutService = L::loadClass('layout', 'framework');
		$layoutService->init($this->_viewPath, $this->_layoutFile, $this->_layoutExt);
		$layoutService->setPartial($this->_partial);
		$layoutService->setTemplate(($this->_template) ? $this->_template : $this->_controller . '.' . $this->_action);
		$layoutService->display($this->_layoutFile, $this->_viewer);
	}
	
	function _setTemplate($template){
		$this->_template = $template;
	}
	
	function _setViewPath($path){
		$this->_viewPath = $path;
	}
	
	function _setLayoutFile($file){
		$this->_layoutFile = $file;
	}
	
	function _setLayoutExt($ext){
		$this->_layoutExt = $ext;
	}
	
	function _setPartial($partial){
		$this->_partial = $partial;
	}
	
	/**
	 * ��ȡȫ�ֱ��� ȫ�ְ�ȫ���
	 * @param $key ����
	 */
	function _getGlobal($key) {
		return isset($GLOBALS[$key]) ? $GLOBALS[$key] : '';
	}
	/**
	 * ��ȡ$_POST��$_GET����
	 * @param array $params ��������
	 */
	function _gp($params) {
		if (!S::isArray($params)) return array();
		S::gp($params,null,1,false);
		$tmp = array();
		foreach ($params as $param) {
			$tmp[] = $this->_getGlobal($param);
		}
		return $tmp;
	}
	function _isPost() {
		return (strtolower($_SERVER['REQUEST_METHOD']) === 'post');
	}
}
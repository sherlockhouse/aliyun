<?php
!defined('P_W') && exit('Forbidden');

class PW_ModuleConfig{
	
	function afterUpdate($sign) {}
	function getPath($sign) {}
	function getType() {}
	/*
	 * ���������ļ��޸����ݿ��е�����
	 */
	function updateInvokesByModuleConfig($sign) {
		$templateFile = $this->_getTemplateFile($sign);
		$configFile = $this->_getConfigFile($sign);
		
		$moduleConfigService = $this->_getModuleConfigService();
		$moduleConfigService->updateInvokesByModuleConfig($templateFile,$configFile,$this->getType(),$sign);
	}

	/**
	 * ��ȡƵ����ĳ��ģ���ģ��
	 * @param $sign
	 * @param $invokeName
	 * return string
	 */
	function getPiecesCode($sign,$invokeName) {
		$file = $this->_getConfigFile($sign);
		$moduleConfigService = $this->_getModuleConfigService();
		return $moduleConfigService->getPiecesCode($file,$invokeName);
	}
	/**
	 * ����ĳ��Ƶ��ĳģ���ģ��
	 * @param $sign
	 * @param $name
	 * @param $code
	 */
	function updateModuleCode($sign,$name,$code) {
		$configFile = $this->_getConfigFile($sign);
		$moduleConfigService = $this->_getModuleConfigService();
		$moduleConfigService->updateModuleCode($configFile,$name,$code);
		$this->_updateIndexFileTime($sign);
		$this->afterUpdate($sign);
	}
	
	function updateModuleByConfig($sign,$name,$pieceConfig,$title='') {
		$configFile = $this->_getConfigFile($sign);
		$moduleConfigService = $this->_getModuleConfigService();
		$moduleConfigService->updateModuleByConfig($configFile,$name,$pieceConfig,$title);
		$this->_updateIndexFileTime($sign);
		$this->afterUpdate($sign);
	}

	function _updateIndexFileTime($sign) {
		$indexFile = $this->_getIndexFile($sign);
		touch($indexFile,'1000000000');
	}
	
	function _getIndexFile($sign) {
		return $this->getPath($sign).'/index.html';
	}
	
	function _getTemplateFile($sign) {
		return $this->getPath($sign).'/'.PW_PORTAL_MAIN;
	}
	function _getConfigFile($sign) {
		return $this->getPath($sign).'/'.PW_PORTAL_CONFIG;
	}
	
	function _getModuleConfigService() {
		return L::loadClass('moduleconfigservice', 'area');
	}
}
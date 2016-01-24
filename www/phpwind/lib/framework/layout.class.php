<?php
!defined('P_W') && exit('Forbidden');
/**
 * ������
 */
class PW_Layout {
	var $_viewer;
	var $_directory;
	var $_layout;
	var $_ext;
	var $_template;
	var $_partial;
	
	/**
	 * ��ʼ�����ֲ���
	 * @param $directory
	 * @param $layout
	 * @param $ext
	 */
	function init($directory, $layout = "layout", $ext = 'htm') {
		$this->_directory = ($directory) ? $directory : dirname(__FILE__) . DIRECTORY_SEPARATOR;
		$this->_layout = $layout;
		$this->_ext = $ext;
	}
	
	function setTemplate($template){
		$this->_template = $template;
	}
	
	function setPartial($partial){
		$this->_partial = $partial;
	}
	/**
	 * ����ģ��
	 * @param $template
	 * @param $viewer
	 */
	function display($layout, $viewer = '', $return = false) {
		$layoutFile = $this->_getLayoutFile($layout);
		if (!$return) {
			$this->renderFile($layoutFile, $viewer, false);
			return '';
		}
		$output = $this->renderFile($layoutFile, $viewer, true);
		echo $output;
	}
	
	function renderFile($__file_, $__viewer_ = '', $__return_ = false) {
		if (!is_file($__file_)) {
			Error::showError("�ļ� " . $__file_ . " ������");
		}
		$_viewer = $__viewer_;
		if ($__return_) {
			ob_start();
			ob_implicit_flush(false);
			require S::escapePath($__file_);
			return ob_get_clean();
		}
		require S::escapePath($__file_);
	}
	/**
	 * ��ȡ�����ļ�
	 * @param $template
	 */
	function _getLayoutFile($template) {
		if (!$this->_directory) {
			Error::showError("����Ŀ¼����Ϊ��");
		}
		if (!$template) {
			Error::showError("����ģ�岻����");
		}
		$layout = $this->_getCustomLayout();
		if (!is_file($layout)) {
			Error::showError("�����ļ� " . $layout . " ������");
		}
		return $layout;
	}
	/**
	 * ���������ļ�,����Ƿ��ж���Ŀ¼,����ΪĬ��Ŀ¼
	 */
	function _getCustomLayout(){
		$directory = ($this->_partial) ? $this->_partial : $this->_directory;
		$pathfile = S::escapePath($directory . 'layout/' . $this->_layout . '.' . $this->_ext);
		(!is_file($pathfile)) && $pathfile = S::escapePath($this->_directory . 'layout/' . $this->_layout . '.' . $this->_ext);
		return $pathfile;
	}
	/**
	 * ��ȡģ��Ƭ���ļ�
	 * @param $segment
	 */
	function getSegmentFile($segment) {
		$filePath = $this->_getCustomSegment($segment);
		if (!is_file($filePath)) {
			Error::showError("����ģ���ļ� " . $segment . " ������");
		}
		return $filePath;
	}
	
	/**
	 * ����ģ��Ƭ���ļ�,����Ƿ��ж���Ŀ¼,����ΪĬ��Ŀ¼
	 */
	function _getCustomSegment($segment){
		$directory = ($this->_partial) ? $this->_partial : $this->_directory;
		$pathfile = S::escapePath($directory . $segment . '.' . $this->_ext);
		(!is_file($pathfile)) && $pathfile = S::escapePath($this->_directory . $segment . '.' . $this->_ext);
		return $pathfile;
	}
	/**
	 * ��ȡģ��Ƭ��
	 * @param $segment
	 */
	function segment($segment, $viewer = '', $return = false) {
		$segmentFile = $this->getSegmentFile($segment);
		if (!$return) {
			return $this->renderFile($segmentFile,$viewer,false);
		}
		$output = $this->renderFile($segmentFile,$viewer,true);
		echo $output;
	}
	
	function getParamForSegment($segment) {
		$segmentFile = $this->_getSegmentParamFile($segment);
		
		return require S::escapePath($segmentFile);
	}
	
	function _getSegmentParamFile($segment) {
		$filePath = $this->_getCustomSegmentParamFile($segment);
		if (!is_file($filePath)) {
			Error::showError("��Ƭ�����ļ� " . $segment . " ������");
		}
		return $filePath;
	}
	
	/**
	 * ����ģ��Ƭ���ļ�,����Ƿ��ж���Ŀ¼,����ΪĬ��Ŀ¼
	 */
	function _getCustomSegmentParamFile($segment){
		$directory = ($this->_partial) ? $this->_partial : $this->_directory;
		$pathfile = S::escapePath($directory . '_segment/' . $segment . '.php');
		(!is_file($pathfile)) && $pathfile = S::escapePath($this->_directory . '_segment/' . $segment . '.php');
		return $pathfile;
	}
	/**
	 * ����ȫ�ְ����ļ�
	 * @param $fileName
	 */
	function parse($fileName) {
		foreach ($GLOBALS as $key => $value) {
			if (!in_array($key, array('GLOBALS','_POST','_GET','_COOKIE','_SERVER','_FILES'))) {
				${$key} =& $GLOBALS[$key];
			}
		}
		require S::escapePath($fileName);
	}
	
	/**
	 * ��ӡ���������ע�ⰲȫ���˷���
	 * @param $key
	 */
	function G($key) {
		return isset($GLOBALS[$key]) ? $GLOBALS[$key] : '';
	}
}
<?php
!defined('P_W') && exit('Forbidden');
class SystemData/*abstruct*/ {
	var $_lang=array();
	/**
	 * ��ȡ���ݶ���ӿ�
	 * @param array $config
	 * @param int $num
	 * return array
	 */
	function getSourceData($config,$num) {
	}
	/**
	 * ��ȡ������Դ�����������
	 * return string
	 */
	function getRelateType() {
	}
	/**
	 * ��ȡ������Դ������Ϣ
	 */
	function getSourceConfig() {
	}
	/**
	 * ��ȡ����Դ�ĸ������ݵ�����
	 * @param string $key
	 * return string
	 */
	function getSourceLang($key) {
		$lang = $this->_lang;
		return isset($lang[$key]) ? $lang[$key] : '';
	}

}
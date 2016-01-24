<?php
!function_exists('readover') && exit('Forbidden');
class PW_ExtendSearcherAbstract {
	
	function getSearchResult() {
	}
	
	/**
	 * HTML�������
	 * @param $htmlFile HTMLģ��·��
	 * @param $params   ҳ�����
	 */
	function _outputHtml($htmlFile, $params = array()) {
		ob_start();
		ob_implicit_flush(false);
		require S::escapePath($htmlFile);
		return ob_get_clean();
	}
	
	/**
	 * ��ȡ��չ������HTMLģ��·��
	 * @param $direcotry ��չ�����Ŀ¼,������ǰΪ dirname(__FILE__)�������Զ���Ŀ¼
	 * @param $htmlname  ��չ��������HTMLģ������
	 */
	function _getHtmlFile($direcotry, $htmlname) {
		$filePath = S::escapePath($direcotry . '/template/' . $htmlname);
		if (!is_file($filePath)) return '';
		return $filePath;
	}
}
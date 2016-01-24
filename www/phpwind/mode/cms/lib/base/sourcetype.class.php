<?php
!defined('M_P') && exit('Forbidden');

class PW_SourceType /*Abstract class*/ {
	/**
	 * ��ȡ���ݣ����Ҵ���articleModule
	 * @param $articleModule
	 * @param $sourceId
	 */
	function cookArticleModule(&$articleModule,$sourceId) {
		$data = $this->getSourceData($sourceId);
		if (!$data) return $articleModule;
		foreach ($this->getSourceMap() as $key => $value) {
			$articleModule->{$value} = $data[$key];
		}
		$articleModule->setSourceType($this->getSourceType());
		$articleModule->setSourceId($sourceId);

		return $articleModule;
	}
	/**
	 * ��ȡ���ݺ�ArticleModule��ӳ���ϵ
	 */
	function getSourceMap() {
		return array(
			'subject' => 'subject',
			'content' => 'content',
			'descrip' => 'descrip',
			'author' => 'author',
			'frominfo' => 'fromInfo',
		);
	}
	/**
	 * ͨ�����ݵ�ַ
	 * @param $sourceId
	 */
	function getSourceUrl($sourceId) /*Abstract function*/ {
		
	}
	/**
	 * ͨ��id��ȡ����
	 * @param $sourceId
	 */
	function getSourceData($sourceId) /*Abstract function*/ {
		
	}
	/**
	 * ��ȡ��������
	 */
	function getSourceType() /*Abstract function*/ {
		
	}
}
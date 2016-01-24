<?php
!defined('P_W') && exit('Forbidden');
L::loadClass('upload', '', false);

/**
 * ���ͼƬ�ϴ���
 * 
 * @package upload
 * @author	zhuli
 * @abstract
 */
class AdvUpload extends uploadBehavior{
	var $db;
	var $fileInputId;
	var $atype;
	var $attachs;
	
	/**
	 * ��ʼ��
	 * 
	 * @param int $fileInputId �����ֱ�ʶ
	 * @return 
	 */
	function AdvUpload($fileInputId) {
		global $db;
		parent::uploadBehavior();
		$this->db =& $db;
		$this->fileInputId = $fileInputId;
		
		$o_maxfilesize = 2000;

		$this->ftype = array(
			'gif'  => $o_maxfilesize,				'jpg'  => $o_maxfilesize,
			'jpeg' => $o_maxfilesize,				'bmp'  => $o_maxfilesize,
			'png'  => $o_maxfilesize
		);
	}

	/**
	 * ��file����
	 * 
	 * @param string $key
	 * @return string
	 */
	function allowType($key) {
		return $key = "uploadurl_".$fileInputId;
	}

	/**
	 * �Ƿ���Ҫ����ͼ
	 * 
	 * @return bool
	 */
	function allowThumb() {
		return false;
	}

	/**
	 * ��ȡ�ļ�·��
	 * 
	 * @return array
	 */
	function getFilePath($currUpload) {
		global $timestamp,$o_mkdir;
		$filename	= date("YmdHis", time()).'_'. $this->fileInputId .'.'. $currUpload['ext'];
		$savedir	= 'advpic/';
		return array($filename, $savedir);
	}
	
	/**
	 * ��߿��Խ������ݿ���²�����
	 * 
	 * @return bool
	 */
	function update($uploaddb) {
		return $uploaddb;
	}
	
	/**
	 * ��ȡͼƬ·��
	 * 
	 * @return string
	 */
	function getImagePath() {
		$imagePath = geturl($this->fileName);
		if ($imagePath[0]) return $imagePath[0];
		return '';
	}

	function getAttachs() {
		return $this->attachs;
	}
}
?>
<?php
!defined('P_W') && exit('Forbidden');
L::loadClass('upload', '', false);

/**
 * ר��ͼƬ�ϴ���
 * 
 * @package upload
 * @author	zhuli
 * @abstract
 */
class StopicUpload extends uploadBehavior{
 	var $db;
	var $atype;
	var $attachs;
	var $inputId;
	
	/**
	 * ��ʼ��
	 * 
	 * @return 
	 */
	 function StopicUpload($inputId) {
		global $db;
		parent::uploadBehavior();
		$this->db =& $db;
		$this->inputId = $inputId;
		
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
	 * @return bool
	 */
	function allowType($key) {
		return $key == "image_upload_".$this->inputId;
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
		$filename	= date("YmdHis", time()).'_'. $this->inputId .'.'. $currUpload['ext'];
		$savedir	= 'stopic/img/';
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
<?php
/*
 * �޸�ͷ��
 */
!function_exists('readover') && exit('Forbidden');
class JOB_DoUpdateAvatar{
	
	var $step = 1;
	var $hour = 3600;
	
	var $_timestamp = null;
	
	function JOB_DoUpdateAvatar(){
		global $timestamp;
		$this->_timestamp = $timestamp;
	}
	
	/*
	 * ��������
	 */
	function getUrl($job){
		return "profile.php?action=modify&info_type=face";
	}

	function finish($job,$jober,$factor){
		/*ʱ������*/
		$factors = unserialize($job['factor']);
		if(isset($factors['limit']) && $factors['limit'] > 0){
			if($jober['last']+$factors['limit'] * $this->hour < $this->_timestamp){
				return 5;/*ʧ��*/
			}
		}
		return 2;
	}
	
}
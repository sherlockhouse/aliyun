<?php
/*
 * ���¸�������
 */
!function_exists('readover') && exit('Forbidden');
class JOB_DoUpdateData{
	
	var $step = 1;
	var $hour = 3600;
	var $_timestamp = null;
	
	function JOB_DoUpdateData(){
		global $timestamp;
		$this->_timestamp = $timestamp;
	}
	
	/*
	 * ��������
	 */
	function getUrl($job){
		return "profile.php?action=modify";
	}
	
	function finish($job,$jober,$factor){
		/*ʱ������*/
		$factors = unserialize($job['factor']);
		if(isset($factors['limit']) && $factors['limit'] > 0){
			//�Ƚ�ʱ��
			if($jober['last']+$factors['limit'] * $this->hour < $this->_timestamp ){
				return 5;/*ʧ��*/
			}
		}
		return 2;
	}
	
}
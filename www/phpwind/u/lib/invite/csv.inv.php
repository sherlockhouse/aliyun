<?php
!defined('P_W') && exit('Forbidden');
/**
 * foxmail�ʼ���ϵ�˵���
 * @package  PW_Csv 
 * @author suqian
 */
class INV_Csv{
	
	var $_filename;
	var $_uid = 0;
	var $_emailList = array();
	var $_fp = NULL;

	
	function INV_Csv(){
		global $winduid;
		$this->_uid = $winduid;
	}
	/**
	 * �ϴ�csv�ļ�����������
	 *
	 */
	function _uploadCsv(){
		L::loadClass('csvupload', 'upload', false);
		$csvupload = new CsvUpload($this->_uid);
		PwUpload::upload($csvupload);
		$this->_filename = $csvupload->pathname;
	}
	
	 /**
	 * ȡ��foxmail��ϵ��email�б�
	 */ 
	function getEmailAddressList(){		
		$this->_uploadCsv();
		$point = $this->_getPoint();
		if(empty($point) || !isset($point['email'])){
			return array();
		}
		$this->_open();
		$filesize = filesize($this->_filename);
		$i = 0;
		while($data = fgets($this->_fp, $filesize)){
			$data = explode(',',$data);
			if($i != 0){	
				$value = '';
				$key = $data[$point['email']];
				$key = str_replace('"','',$key);
				$point['nick'] && $value = $data[$point['nick']];
				!$value &&  $value = $data[$point['name']];
				!$value &&  $value = $data[$point['email']];						
				$key && $this->_emailList[$key] = $value;
			}
			$i++;
		}
		$this->_close();
		$this->_del();
		return $this->_emailList;
	}
	/**
	 * �õ�foxmail���ʼ����ǳơ������б�
	 * @return array ������������
	 */ 
	function _getPoint(){
		if(!$this->_isCsv()){
			return array();
		}
		$array = array();
		$this->_open();
		$filesize = filesize($this->_filename);
		$i = 0;
		while($data = fgets($this->_fp, $filesize)){
			$array = $this->_result($i,$data,$array);
			if($i > 2){
				break;
			}
			$i++;
		}
		$this->_close();
		return $array;
	}
	
	function _result($i,$data,$array=array()){
		$data = explode(',',$data);
		$count = count($data);
		for($j = 0;$j<$count;$j++){
			if($i == 0){
				switch($data[$j]){
					case "�ǳ�":$array['nick'] = $j;break;
					case "����":$array['name'] = $j;break;
					case "�����ʼ���ַ":$array['email'] = $j;break;
					default:break;
				}
				continue;
			}
			if($data[$array['email']] && $this->_isEmail($data[$array['email']])){
				break;
			}
			if($this->_isEmail($data[$j])){
				$array['email'] = $j;
				break;
			}
		}
		return $array;
	}
	/**
	 * �ж���foxmail������ϵ���б����ļ��Ƿ���csv��ʽ
	 *
	 */ 
	function _isCsv(){		
		if(!$this->_filename || !is_file($this->_filename)){
			return false;
		}
		$ext = strtolower(substr(strrchr($this->_filename, '.'), 1));
		if(!in_array($ext,array('csv'))){
			return false;
		}
		return true;
	}
	/**
	 * ��Email��ʽ������֤
	 */ 
	function _isEmail($email){
		return preg_match("/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i",$email);	
	}
	
	/**
	 *���ļ�
	 * @param string $method ���ļ���ʽ
	 */ 
	function _open($method = 'r'){
		if(!is_resource($this->_fp)){
			$this->_fp = fopen($this->_filename,$method);			
		}
	}
	/**
	 * �ر��ļ�
	 */ 
	function _close(){
		if(is_resource($this->_fp)){
			fclose($this->_fp);
			$this->_fp = NULL;
		}
	}
	/**
	 * �ӷ�����ɾ���û��ϴ��ļ�
	 */ 
	function _del(){
		if($this->_filename && is_file($this->_filename)){
			P_unlink($this->_filename);
		}
	}
}
?>
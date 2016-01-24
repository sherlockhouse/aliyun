<?php
!defined('P_W') && exit('Forbidden');

/**
*  Ⱥ�����з��ദ��
* 
*  @package Ⱥ��
*  @author zhudong
*/

class GroupStyle {
	
	var $db;

	/** 
	*  Construct
	*/

	function GroupStyle(){
		global $db;
		$this->db =& $db;
	}

	/** 
	*  ��ȡ���еķ���
	* 
	*  @return $styledb array ��������� 
	*/

	function getAllStyles(){

		$query = $this->db->query("SELECT * FROM pw_cnstyles");
		while ($rt = $this->db->fetch_array($query)) {
			$styledb[$rt['id']] = $rt;
		}
		return $styledb;

	}


	/** 
	*  ��ȡ���п����ķ���
	* 
	*  @return $styledb array ��������� 
	*/

	function getOpenStyles(){

		$query = $this->db->query("SELECT * FROM pw_cnstyles WHERE ifopen=1");
		while ($rt = $this->db->fetch_array($query)) {
			$styledb[$rt['id']] = $rt;
		}
		return $styledb;

	}


	/** 
	*  ��ȡһ������id
	* 
	*  @return $styledb array ����id������ 
	*/

	function getFirstGradeStyleIds(){

		$query = $this->db->query("SELECT * FROM pw_cnstyles WHERE upid='0'");
		while ($rt = $this->db->fetch_array($query)) {
			$styledb[] = $rt['id'];
		}
		return $styledb;

	}
	
	function getFirstGradeStyles(){
		$temp = array();
		$query = $this->db->query("SELECT * FROM pw_cnstyles WHERE upid='0'");
		while ($rt = $this->db->fetch_array($query)) {
			$temp[$rt['id']] = $rt;
		}
		return $temp;

	}

	

	/** 
	*  ͨ���ϼ�ID��ȡ����
	* 
	*  @param  $upids   array �ϼ�����ID����
	*  @return $styledb array ��������� 
	*/

	function getGradeStylesByUpid($upids){
		
		if(empty($upids)) return array();

		$query = $this->db->query("SELECT * FROM pw_cnstyles WHERE upid IN (".S::sqlImplode($upids).") ORDER BY vieworder ASC");
		while ($rt = $this->db->fetch_array($query)) {
			//if($rt['ifopen'] == 0) continue; 
			$styledb[$rt['upid']][$rt['id']] = $rt;
		}
		return $styledb;

	}

	/** 
	*  ��Ӷ�������
	* 
	*  @param  $newSubStyle   array ����ӵĶ������������
	*  @return null
	*/

	function addNewSubStyle($newSubStyle) {
		$this->db->update("INSERT INTO pw_cnstyles(cname,ifopen,upid,vieworder) VALUES ". S::sqlMulti($newSubStyle));
	}
}
?>
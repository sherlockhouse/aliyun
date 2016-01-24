<?php
/*
 * ��浽����������
 */
!function_exists('readover') && exit('Forbidden');
class Task_AlterAdver {
	
	var $_db = null;
	
	function Task_AlterAdver(){
		global $db;
		$this->_db = &$db;
	}
	
	function run(){
		return $this->doTask();
	}
	
	function doTask(){
		$configs = $this->_getConfig();
		if(!$configs['alterstatus']){/*is open adver alter */
			return null;
		}
		/*get advers */
		$alterbefore = $configs['alterbefore'];
		$before = $alterbefore*3600*24;
		$current = time();
		$result = array();
		$query = $this->_db->query("SELECT * FROM pw_advert WHERE type=1 AND ifshow=1 AND etime>".S::sqlEscape($current)." AND etime<=".S::sqlEscape($before+$current));
		while($rs = $this->_db->fetch_array($query)){
			$result[$rs['uid']][] = $rs;
		}
		if(!$result){
			return null;
		}
		/* send short message or email */
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
		foreach($result as $uid => $advers){
			$content = "��ã�";
			foreach($advers as $adver){
				$content .= $this->_getContent($adver,$alterbefore);
			}
			$subject = $this->_getSubject();
			if($configs['alterway'] == 1){
				$this->sendShortMessage($adver['uid'],$subject,$content);
			}else{
				$user = $userService->get($uid);
				if ($user && $user['email']) $this->sendEmail($user['email'],$subject,$content);
			}
		}
		return true;
	}
	
	
	/*
	 * ��ȡ����
	 */
	function _getContent($adver,$alterbefore){
		$descrip = $adver['descrip'];
		list($up,$down,$title) = $this->_getAdvers($adver['ckey']);
		$html = "<br />����Ϊ  {$descrip} �Ĺ�潫�� {$alterbefore} ����ڣ��ù�����ڵĹ��λ  {$title} �У��ѿ����Ĺ���� {$up} �����ѹرյĹ���� {$down} ����<br />";
		return $html;
	}
	
	function _getAdvers($ckey){
		$query = $this->_db->query("SELECT * FROM pw_advert WHERE ckey=".S::sqlEscape($ckey));
		$current = time();
		$title = $descrip = '';/* adver title*/
		$up = $down = 0;
		while($rs = $this->_db->fetch_array($query)){
			if($rs['type'] == 0){
				list($title,$descrip) = explode("~\t~",$rs['descrip']);
				continue;
			}
			if($rs['ifshow'] == 1 && $current>=$rs['stime'] && $current<= $rs['etime'] ){
				$up++;
			}else{
				$down++;
			}
		}
		return array($up,$down,$title);
	}
	
	/*
	 * ��ȡ����
	 */
	function _getSubject(){
		return '[ע��] ��浽������';
	}
	
	/*
	 * ���Ͷ���Ϣ����
	 */
	function sendShortMessage($touid,$subject,$content){
		global $db;
		$subject = S::escapeChar($subject);
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
		$toUserName = $userService->getUserNameByUserId($touid);
		M::sendNotice(
			array($toUserName),
			array(
				'title' => $subject,
				'content' => $content,
			)
		);
	}
	
	/*
	 * �����ʼ�����
	 */
	function sendEmail($toemail,$subject,$message){
		require_once(R_P.'require/sendemail.php');/*register.php*/
		$sendinfo = sendemail($toemail,$subject,$message,$additional=null);
	}
	
	/*
	 * ��ȡ��浽����������
	 */
	function _getConfig(){
		global $db_alterads;
		if($db_alterads){
			return $db_alterads;
		}
		$adverClass = L::loadClass('adver', 'advertisement');
		return $adverClass->getDefaultAlter();
	}
	
	
		
	
	
}
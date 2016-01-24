<?php
/**
 * create by liaohu 2010-06-11
 */
!defined('P_W') && exit('Forbidden');
S::gp(array('fid','tid'), null, 2);
/**
 * ʵ�����࣬���ִ�н��
 * @var unknown_type
 */
$commend = new commend($fid, $tid);
if (($return = $commend->execute()) !== true) {
	Showmsg($return);
}

echo $commend->toString();
ajax_footer();

/**
 * ������
 * @author hu.liaoh
 */
class commend{
	/**
	 * �������
	 * @var unknown_typell
	 */
	var $table;
	var $commend;
	var $forumset;
	var $db;
	var $result;
	var $fid;
	var $msg; 
	
	/**
	 * ���캯������ʼ������
	 * @param $fid
	 * @return unknown_type
	 */
	function commend($fid, $tid){
		$this->msg = array('success'=>false,'msg');
		$this->table = 'pw_forumsextra';
		$this->commend = null;
		$this->forumset = null;		
		$this->db = $GLOBALS['db'];
		$this->result = null;
		$this->fid = $fid;
		$this->tid = $tid;
		
		$this->init();
	}
	/**
	 * ���ʼ��
	 * init
	 */
	function init(){
		$this->getResult();
	}
	/**
	 * ��ȡForumset
	 * @return unknown_type
	 */
	function setForumset(){
		$this->forumset = unserialize($this->result['forumset']);
	}
	/**
	 * ��ȡcommend
	 * @return unknown_type
	 */
	function setCommend(){
		$this->commend = unserialize($this->result['commend']);
	}
	/**
	 * ���ݰ��id��ȡ����Ƽ�����Ϣ
	 * @return unknown_type
	 */
	function getResult(){
		$sql = "SELECT forumset,commend FROM " . $this->table . " WHERE fid=" . S::sqlEscape($this->fid);
		$this->result = $this->db->get_one($sql);
		if($this->result){
			$this->setForumset();
			$this->setCommend();
		}
	}
	/**
	 * ����ɾ�����
	 * @return unknown_type
	 */
	function setResult(){
		require_once(R_P.'admin/cache.php');
		/**
		 * �������ݿ� 
		 * @var unknown_type
		 */
		$sql = "UPDATE " . $this->table . " SET ".
			S::sqlSingle(array(
				"forumset" => serialize($this->forumset),
				"commend" => serialize($this->commend)
			))
		. "WHERE fid=" . S::sqlEscape($this->fid);
		if($this->db->update($sql)){
			$this->setMsg(true);
		}
		/**
		 * ���»���
		 */
		updatecache_f();
	}
	/**
	 * ��commendlist��ɾ���Ƽ���tid
	 * @param $tid
	 * @return unknown_type
	 */
	function delItemFromList($tid){
		$cmdlist = explode(",", $this->forumset['commendlist']);
		$pos = array_search($tid, $cmdlist);
		if(false !== $pos){
			$cmdlist = $this->delInArray($cmdlist, $pos);
			$this->forumset['commendlist'] = implode(",", $cmdlist);
		}		
		$this->delCommend($tid);
	}
	/**
	 * ɾ���Ƽ�������
	 * @param $tid
	 * @return unknown_type
	 */
	function delCommend($tid){
		foreach($this->commend as $key=>$value){
			if($tid == $value['tid']){
				$this->commend = $this->delInArray($this->commend, $key);
				break;
			}
		}
		$this->setResult();
	}
	/**
	 * ������Ϣ
	 * @param $success
	 * @param $msg
	 * @return unknown_type
	 */
	function setMsg($success,$msg = ''){
		$this->msg = array('success'=>$success,'msg'=>$msg);
	}
	/**
	 * ��������ɾ��
	 * @param $arr
	 * @param $pos
	 * @param $num
	 * @return unknown_type
	 */
	function Delinarray($arr, $pos, $num = 1){
		array_splice($arr, $pos, $num);
		return $arr;
	}
	/**
	 * �����Ϣ
	 * @return unknown_type
	 */
	function toString(){
		return pwJsonEncode($this->msg);
	}
	
	function execute() {
		if (($return = $this->_check()) !== true) {
			return $return;
		}
		
		$this->delItemFromList($this->tid);
		return true;
	}
	
	
	function _check() {
		$admincheck = $this->_getPermission();
		if ($admincheck) return true;
		return 'ûȨ��ɾ������';
	}
	
	function _getPermission() {
		global $windid;
		$isGM = $this->isGM();
		if ($isGM) return true;
		L::loadClass('forum', 'forum', false);
		$pwforum = new PwForum($this->fid);
		$isBM = $pwforum->isBM($windid);
		return $isBM  ? true : false;
	}
	
	function isGM() {
		global $windid, $manager;
		$isGM = S::inArray($windid,$manager);
		return $isGM;
	}
}

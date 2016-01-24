<?php
!defined('P_W') && exit('Forbidden');

/**
 * ���۷����
 * @package  PW_CommentService
 * @author phpwind @2011-7-5
 */
class PW_CommentService {

	/**
	 * ���
	 * 
	 * @param array $fieldsData
	 * @return int 
	 */
	function insert($fieldsData) {
		if (!S::isArray($fieldsData)) return false;
		$commentDb = $this->_getCommentDB();
		return $commentDb->insert($fieldsData);
	}
	
	/**
	 * ����
	 * 
	 * @param array $fieldsData
	 * @param int $commentid 
	 * @return boolean 
	 */
	function updateByCommentid($fieldsData,$commentid) {
		$commentid = intval($commentid);
		if($commentid < 1 || !S::isArray($fieldsData)) return false;
		$commentDb = $this->_getCommentDB();
		return $commentDb->update($fieldsData,$commentid);
	}
	
	/**
	 * �ӻظ���
	 * 
	 * @param int $num
	 * @param int $commentid 
	 * @return boolean 
	 */
	function addReplyNumByCommentid($num,$commentid) {
		$num = intval($num);
		$commentid = intval($commentid);
		if($num < 1 || $commentid < 1) return false;
		$commentDb = $this->_getCommentDB();
		return $commentDb->addReplyNumByCommentid($num,$commentid);
	}
	
	/**
	 * ���ظ���
	 * 
	 * @param int $num
	 * @param int $commentid 
	 * @return boolean 
	 */
	function reduceReplyNumByCommentid($num,$commentid) {
		$num = intval($num);
		$commentid = intval($commentid);
		if($num < 1 || $commentid < 1) return false;
		$commentDb = $this->_getCommentDB();
		return $commentDb->reduceReplyNumByCommentid($num,$commentid);
	}
	
	/**
	 * ���»ظ���
	 * 
	 * @param string $expnum -1|+1
	 * @param int $commentid 
	 * @return boolean 
	 */
	function updateReplynumByCommentid($expnum,$commentid) {
		$commentid = intval($commentid);
		if($commentid < 1 || !$expnum) return false;
		$num = intval(trim($expnum,'+-'));
		if (strpos($expnum,'-') !== false) {
			return $this->reduceReplyNumByCommentid($num,$commentid);
		}
		return $this->addReplyNumByCommentid($num,$commentid);
	}
	
	/**
	 * ɾ��
	 * 
	 * @param int $commentid 
	 * @return boolean
	 */
	function deleteByCommentid($commentid) {
		$commentid = intval($commentid);
		if ($commentid < 1) return false;
		$commentDb = $this->_getCommentDB();
		return $commentDb->delete($commentid);
	}
	
	/**
	 * ����commentid��ȡ����
	 * 
	 * @param int $commentid
	 * @return array
	 */
	function getByCommentid($commentid) {
		$commentid = intval($commentid);
		if ($commentid < 1) return array();
		$commentDb = $this->_getCommentDB();
		return $commentDb->getByCommentid($commentid);
	}

	/**
	 * ����stopic_id��ȡ����
	 * 
	 * @param int $commentid
	 * @return array
	 */
	function getCommentsCountByStopicId($stopic_id){
		$stopic_id = intval($stopic_id);
		if ($stopic_id < 1) return false;
		$commentDb = $this->_getCommentDB();
		return $commentDb->getCommentsCountByStopicId($stopic_id);
	}

	/**
	 * ����stopic_id��ȡ����
	 * 
	 * @param int $stopic_id
	 * @param int $page
	 * @param int $perpage
	 * @return array
	 */
	function getCommentsByStopicId($stopic_id,$page,$perpage){
		$stopic_id = intval($stopic_id);
		$page = intval($page);
		$perpage = intval($perpage);
		if (!$stopic_id || $page < 0 || $perpage < 1) return array();
		$commentDb = $this->_getCommentDB();
		return $this->buildReplyData($commentDb->getCommentsByStopicId($stopic_id,($page - 1) * $perpage,$perpage));	
	}

	/**
	 * ��װ����
	 * 
	 * @param array $data
	 * @return array
	 */
	function buildReplyData($data) {
		if(!S::isArray($data)) return array();
		$uids = $comment = array();
		foreach ($data as $v) {
			$uids[] = $v['uid'];
		}
		$userService = L::loadClass('UserService', 'user');
		$userInfo = $userService->getUserInfoWithFace($uids);
		foreach ($data as $value) {
			list($value['postdate'], $value['postdate_s']) = getLastDate($value['postdate']);
			$comment[] = array_merge((array)$value, (array)$userInfo[$value['uid']]);
		}
		return $comment;
	}
	
	/**
	 * �������
	 * 
	 * @param string $content
	 * @param int $groupid
	 * @return array
	 */
	function addCheck($content, $groupid) {
		global $winduid;
		if (!$winduid) return '����δ��¼!';
		if ($groupid == '6') return '���ѱ�����!';
		if (!$content) return '���ݲ�Ϊ��';
		if (strlen($content) > 255) return '���ݲ��ܶ���255�ֽ�';
		$filterService = L::loadClass('FilterUtil', 'filter');
		if (($GLOBALS['banword'] = $filterService->comprise($content)) !== false) {
			return 'content_wordsfb';
		}
		return true;
	}

	function _getCommentDB() {
		return L::loadDB('Comment', 'stopic');
	}
}
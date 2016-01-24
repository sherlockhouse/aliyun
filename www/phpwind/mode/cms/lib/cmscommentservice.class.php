<?php
!defined('P_W') && exit('Forbidden');

/**
 * ���۷����
 * @package  PW_CmsCommentService
 * @author phpwind @2011-6-24
 */
class PW_CmsCommentService {

	/**
	 * ���
	 * 
	 * @param array $fieldsData
	 * @return int 
	 */
	function insert($fieldsData) {
		if (!S::isArray($fieldsData)) return false;
		$cmsCommentDb = $this->_getCmsCommentDB();
		return $cmsCommentDb->insert($fieldsData);
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
		$cmsCommentDb = $this->_getCmsCommentDB();
		return $cmsCommentDb->update($fieldsData,$commentid);
	}
	
	/**
	 * ���»ظ���
	 * 
	 * @param array $fieldsData
	 * @param int $commentid 
	 * @return boolean 
	 */
	function updateReplynumByCommentid($num,$commentid) {
		$commentid = intval($commentid);
		if($commentid < 1 || !$num) return false;
		$cmsCommentDb = $this->_getCmsCommentDB();
		return $cmsCommentDb->updateReplynumByCommentid($num,$commentid);
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
		$cmsCommentDb = $this->_getCmsCommentDB();
		return $cmsCommentDb->delete($commentid);
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
		$cmsCommentDb = $this->_getCmsCommentDB();
		return $cmsCommentDb->getByCommentid($commentid);
	}

	function getCommentsCountByArticleId($article_id){
		$article_id = intval($article_id);
		if ($article_id < 1) return false;
		$cmsCommentDb = $this->_getCmsCommentDB();
		return $cmsCommentDb->getCommentsCountByArticleId($article_id);
	}

	function getCommentsByArticleId($article_id,$page,$perpage){
		$article_id = intval($article_id);
		$page = intval($page);
		$perpage = intval($perpage);
		if (!$article_id || $page < 0 || $perpage < 1) return array();
		$cmsCommentDb = $this->_getCmsCommentDB();
		return $this->buildReplyData($cmsCommentDb->getCommentsByArticleId($article_id,$page,$perpage));	
	}

	function buildReplyData($data) {
		global $db_windpost,$timestamp;
		if(!S::isArray($data)) return array();
		$uids = $comment = array();
		foreach ($data as $v) {
			$uids[] = $v['uid'];
		}
		$userService = L::loadClass('UserService', 'user');
		$userInfo = $userService->getUserInfoWithFace($uids);
		require_once (R_P . 'require/bbscode.php');
		foreach ($data as $value) {
			$value['content'] = convert($value[content],$db_windpost);
			$value['postdate'] == $timestamp && $value['postdate'] = $value['postdate']-1;
			list($value['postdate'], $value['postdate_s']) = getLastDate($value['postdate']);
			$comment[] = array_merge((array)$value, (array)$userInfo[$value['uid']]);
		}
		return $comment;
	}
	
	function addCheck($content, $groupid) {
		if ($groupid == '6') return '���ѱ�����!';
		if (!$content) return '���ݲ�Ϊ��';
		if (strlen(pwHtmlspecialchars_decode($content)) > 255) return '���ݲ��ܶ���255�ֽ�';
		$filterService = L::loadClass('FilterUtil', 'filter');
		if (($GLOBALS['banword'] = $filterService->comprise($content)) !== false) {
			return 'content_wordsfb';
		}
		return true;
	}

	/**
	 *����dao
	 * 
	 * @return PW_CmsCommentDB
	 */
	function _getCmsCommentDB() {
		return C::loadDB('CmsComment');
	}
}
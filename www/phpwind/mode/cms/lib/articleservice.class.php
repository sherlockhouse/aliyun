<?php
!defined('P_W') && exit('Forbidden');

/**
 * ������ط�����
 * @author xiejin
 *
 */

class PW_ArticleService {
	/**
	 * ��������IDɾ������
	 * @param array $aids
	 */
	function deleteArticlesByIds($aids) {
		if (!is_array($aids)) $aids = array($aids);
		$articleDAO = $this->_getArticleDAO();
		/* @var $articleDAO PW_ArticleDB */
		return $articleDAO->deleteArticles($aids);
	}

	/**
	 * �������µ����
	 * @param int $aid
	 */
	function updateArticleHits($aid) {
		if (empty($aid)) return false;
		$articleDAO = $this->_getArticleDAO();
		return $articleDAO->updateArticleHits($aid);
	}

	/**
	 * ɾ�����µ�����վ
	 * @param array $aids
	 * @return string
	 */
	function deleteArticlesToRecycle($aids) {
		if (!is_array($aids)) $aids = array($aids);
		$articleDAO = $this->_getArticleDAO();
		return $articleDAO->deleteArticleIntoRecycle($aids);
	}

	/**
	 * �ӻ���վ�ﻹԭ����
	 * @param array $aids
	 * @return string
	 */
	function revertArticleFromRecycle($aids) {
		if (!is_array($aids)) $aids = array($aids);
		$articleDAO = $this->_getArticleDAO();
		return $articleDAO->revertArticleFromRecycle($aids);
	}

	/**
	 * �����������ƶ���ĳһ����Ŀ��������
	 * @param array $aids
	 * @param int $columnId
	 */
	function moveArticlesByIds($aids, $columnId) {
		if (empty($aids) || !is_array($aids)) return false;
		if (empty($columnId)) return false;
		$articleDAO = $this->_getArticleDAO();
		return $articleDAO->moveArticlesToColumn($aids, $columnId);
	}

	/**
	 * ���24Сʱ�����������
	 * @param int $count
	 */
	function getTopArticles($count = 10, $column = '0') {
		if ($articles = $this->_getTopArticlesCache($count, $column)) return $articles;
		return $this->_getTopArticles($count, $column);
	}

	function _getTopArticlesCache($count, $column) {
		return array();
	}

	/**
	 * ���24Сʱ�����������
	 * @param int $count
	 */
	function _getTopArticles($count, $column) {
		global $timestamp;
		$today = PwStrtoTime(get_date($timestamp, 'Y-m-d'));
		$datanalyseService = $this->_getDatanalyseService();
		/* @var $datanalyseService PW_CMSDatanalyseService */
		$_action = 'article_' . $column;
		if (!$column) $_action = $datanalyseService->getAllActions('article');
		return $datanalyseService->getDataByActionAndTime('article', $_action, $count, $today);
	}

	/**
	 * ������������б�
	 * @param int $count
	 * @param int $column
	 */
	function getNewArticles($count = 20) {
		if ($articles = $this->_getNewArticlesCache($count)) return $articles;
		return $this->_getNewArticles($count);
	}

	function _getNewArticlesCache($count = 20) {
		return array();
	}

	function _getNewArticles($count = 20) {
		$articleDAO = $this->_getArticleDAO();
		return $articleDAO->search('', '', '', 1, 0, $count);
	}

	/**
	 * �������������
	 */
	function getArticlesCount() {
		$articleDAO = $this->_getArticleDAO();
		return $articleDAO->count();
	}

	/**
	 * �������������
	 */
	function searchArticleCount($cid = array(), $title = '', $author = '', $type = '', $user = '', $postdate = '') {
		$articleDAO = $this->_getArticleDAO();
		if (!empty($cid) && !is_array($cid)) $cid = array($cid);
		return $articleDAO->searchCount($cid, $title, $author, $type, $user, $postdate);
	}

	/**
	 * ���ĳһҳ�������б�
	 * @param int $start	��ʼλ��
	 * @param int $perpage  ÿҳ��ʾ
	 */
	function searchAtricles($cid = array(), $title = '', $author = '', $type = '', $user = '', $postdate = '', $start = 0, $perpage = 20) {
		$articleDAO = $this->_getArticleDAO();
		if (!empty($cid) && !is_array($cid)) $cid = array($cid);
		return $this->_bulidResult($articleDAO->search($cid, $title, $author, $type, $user , $postdate , $start, $perpage));
	}
	
	/**
	 * ����ͼƬ�����µ���
	 * $data ��������
	 * return array
	 */
	function getArticlesIncludePic($data){
		if (!S::isArray($data)) return array();
		$tmpAttach = array();
		$attachService = $this->_getCmsAttachService(); 
		$result = array();
		foreach($data as $k => $v){
			if($v['ifattach'] == 1 && $v['article_id']){
				$attachData = array_values($attachService->getArticlePics(intval($v['article_id'])));
				if(!$attachData) continue;
				$v['image'] = $attachData[0]['attachurl'];
			}	
			$result[] = $v;
		}
		return $result;
	}
	
	/**
	 * 
	 * ��ȡ��ͼƬ������
	 * $columnid int �������
	 * $num	int ����
	 * $time int ʱ��
	 * return array
	 */
	function getArticlesHavePic($columnid,$num){
		$num = intval($num);
		if($num < 0) return array();
		$articleDAO = $this->_getArticleDAO();
		$articles = $articleDAO->getArticleHasAttach((array)$columnid,$num);
		if(!$articles) return array();
		return $this->getArticlesIncludePic($articles);
	}

	function cleanRecycle() {
		$articleDAO = $this->_getArticleDAO();
		return $articleDAO->cleanArticleRecycle();
	}

	/**
	 * �������
	 * @param object $articleModule
	 * return int
	 */
	function addArticle($articleModule) {
		if (!$this->_checkArticleModule($articleModule)) return false;
		
		$articleId = $this->_insertArticle($articleModule);
		if (!$articleId) return false;
		
		$this->_insertArticleContent($articleId, $articleModule->content, $articleModule->relate);
		$this->_insertArticleExtend($articleId);
		
		$this->_updateArticleAttach($articleId, $articleModule->attach);
		$articleModule->attach && $this->_updateUserInfo();
		return $articleId;
	}
	/**
	 * ��������
	 * @param object $articleModule
	 * return bool
	 */
	function updateArticle($articleModule) {
		if (!$this->_checkArticleModule($articleModule)) return false;
		
		$this->_updateArticle($articleModule);
		$this->_updateArticleContent($articleModule->articleId, $articleModule->content, $articleModule->relate);
		/* ���������Ҫ����
		$this->_updateArticleExtend($articleModule);
		*/
		
		$this->_updateArticleAttach($articleModule->articleId, $articleModule->attach);
		$articleModule->attach && $this->_updateUserInfo();		
		return true;
	}

	function _updateArticle($articleModule) {
		$articleDAO = $this->_getArticleDAO();
		$data = $this->_cookArticleModuleToDAO($articleModule);
		$articleDAO->update($data, $articleModule->articleId);
	}

	function _updateArticleContent($articleId, $content, $relate) {
		$articleContentDAO = $this->_getArticleContentDAO();
		$articleContentDAO->update(array('content' => $content, 'relatearticle' => $relate), $articleId);
	}

	function _updateArticleExtend($articleId) {
		$articleExtendDAO = $this->_getArticleExtendDAO();
	}

	function _bulidResult($results) {
		foreach ($results as $key => $value) {
			$value['postdate'] = get_date($value['postdate']);
			$results[$key] = $value;
		}
		return $results;
	}
	
	/**
	 * ��ȡ����bean
	 * @param ini $articleId
	 * return object
	 */
	function getArticleModule($articleId) {
		$articleModule = C::loadClass('articleModule'); /* @var $articleModule PW_ArticleModule */
		$articleId = (int) $articleId;
		
		$articleDAO = $this->_getArticleDAO();
		$articleInfo = $articleDAO->get($articleId);
		if (!$articleInfo) return false;
		
		$this->_initArticleModuleByArticle($articleModule, $articleInfo);
		$this->_initArticleModuleByContend($articleModule);
		$this->_initArticleModuleByExtend($articleModule);
		
		$this->_initArticleModuleByAttach($articleModule);
		
		return $articleModule;
	}

	/**
	 * ͨ����ͬ���͵����ݣ���ȡ����bean
	 * @param $sourceType
	 * @param $sourceId
	 * return object
	 */
	function getArticleModuleFromSource($sourceType, $sourceId) {
		$articleModule = C::loadClass('articleModule'); /* @var $articleModule PW_ArticleModule */
		$source = $articleModule->sourceFactory($sourceType);
		return $source->cookArticleModule($articleModule, $sourceId);
	}

	function _initArticleModuleByArticle(&$articleModule, $articleInfo) {
		$map = $this->_getArcticleMap();
		foreach ($map as $key => $value) {
			$articleModule->{$value} = $articleInfo[$key];
		}
	}

	function _initArticleModuleByContend(&$articleModule) {
		$articleContentDAO = $this->_getArticleContentDAO();
		$contentInfo = $articleContentDAO->get($articleModule->articleId);
		
		$map = $this->_getArticleContentMap();
		unset($map['article_id']);
		foreach ($map as $key => $value) {
			$articleModule->{$value} = $contentInfo[$key];
		}
	}

	function _initArticleModuleByExtend(&$articleModule) {
		$articleExtendDAO = $this->_getArticleExtendDAO();
		$extendInfo = $articleExtendDAO->get($articleModule->articleId);
		
		$map = $this->_getArticleExtendMap();
		unset($map['article_id']);
		foreach ($map as $key => $value) {
			$articleModule->{$value} = $extendInfo[$key];
		}
	}

	function _initArticleModuleByAttach(&$articleModule) {
		if (!$articleModule->ifAttach) return false;
		
		$cmsAttachService = $this->_getCmsAttachService();
		$articleModule->attach = $cmsAttachService->getArticleAttachs($articleModule->articleId);
	}

	function _insertArticle($articleModule) {
		$articleDAO = $this->_getArticleDAO();
		/* @var $articleModule PW_ArticleModule */
		$data = $this->_cookArticleModuleToDAO($articleModule);
		return $articleDAO->insert($data);
	}

	function _cookArticleModuleToDAO($articleModule) {
		$map = $this->_getArcticleMap();
		unset($map['article_id']);
		$temp = array();
		foreach ($map as $key => $value) {
			$temp[$key] = $articleModule->{$value};
		}
		return $temp;
	}

	function _insertArticleContent($articleId, $content, $relate) {
		$articleContentDAO = $this->_getArticleContentDAO();
		$articleContentDAO->insert(array('article_id' => $articleId, 'content' => $content, 'relatearticle' => $relate));
	}

	function _insertArticleExtend($articleId) {
		$articleExtendDAO = $this->_getArticleExtendDAO();
		
		$articleExtendDAO->insert(array('article_id' => $articleId, 'hits' => 0));
	}

	function _updateArticleAttach($articleId, $attach) {
		if (!$attach) return false;
		$cmsAttachService = $this->_getCmsAttachService();
		$result = $cmsAttachService->updateAttachs($articleId, $attach);
		
		list($ifattach, $uploadIds) = $result;
		$ifattach = $result ? 1 : 0;
		
		$articleDAO = $this->_getArticleDAO();
		$articleDAO->update(array('ifattach' => $ifattach), $articleId);
		
		$this->_cookArticleContentByAttach($articleId, $uploadIds);
	}

	function _updateUserInfo() {
		global $winddb;
		$pwSQL = array(
			'uploadtime'	=> $winddb['uploadtime'],
			'uploadnum'		=> $winddb['uploadnum']
		);
		$userService = L::loadClass('UserService', 'user');
		$userService->update($winddb['uid'], array(), $pwSQL);
	}
	
	function _cookArticleContentByAttach($articleId, $uploadIds) {
		if (!$uploadIds) return false;
		$articleContentDAO = $this->_getArticleContentDAO();
		$articleContentInfo = $articleContentDAO->get($articleId);
		if (!$articleContentInfo) return false;
		
		foreach ($uploadIds as $key => $value) {
			$articleContentInfo['content'] = str_replace("[attachment=$key]", "[attachment=$value]", $articleContentInfo['content']);
		}
		$articleContentDAO->update(array('content' => $articleContentInfo['content']), $articleId);
	}
	
	function _checkArticleModule($articleModule) {
		return is_object($articleModule) && strtolower(get_class($articleModule)) == 'pw_articlemodule';
	}

	function _getArcticleMap() {
		return array('article_id' => 'articleId', 'subject' => 'subject', 'descrip' => 'descrip', 'author' => 'author', 
			'username' => 'user', 'userid' => 'userId', 'jumpurl' => 'jumpUrl', 'frominfo' => 'fromInfo', 
			'fromurl' => 'fromUrl', 'column_id' => 'columnId', 'ifcheck' => 'ifcheck', 'postdate' => 'postDate', 
			'modifydata' => 'modifyDate', 'ifattach' => 'ifAttach', 'sourcetype' => 'sourceType', 
			'sourceid' => 'sourceId');
	}

	function _getArticleContentMap() {
		return array('article_id' => 'articleId', 'content' => 'content', 'relatearticle' => 'relate');
	}

	function _getArticleExtendMap() {
		return array('article_id' => 'articleId', 'hits' => 'hits');
	}

	function filterArticles($articles) {
		global $timestamp;
		if (!S::isArray($articles)) return array();
		foreach ($articles as $v) {
			if ($v['postdate'] > $timestamp) continue;
			$tmpArticle[] = $v;
		}
		return $tmpArticle;
	}
	
	function _getArticleDAO() {
		return C::loadDB('article');
	}

	function _getArticleContentDAO() {
		return C::loadDB('articlecontent');
	}

	function _getArticleExtendDAO() {
		return C::loadDB('articleextend');
	}

	function _getCmsAttachService() {
		return C::loadClass('cmsattachservice');
	}

	function _getDatanalyseService() {
		return C::loadClass('cmsdatanalyseservice', 'datanalyse');
	}
}
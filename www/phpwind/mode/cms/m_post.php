<?php
!defined('M_P') && exit('Forbidden');
S::gp(array('action', 'step', 'cid', 'ajax','iscontinue'));
if ($ajax == 1) define('AJAX', '1');

if (!getPostPurview($windid, $_G) && !checkEditPurview($windid, $cid)) Showmsg('��û�з������µ�Ȩ��');
$articleService = C::loadClass('articleservice'); /* @var $articleService PW_ArticleService */
cmsSeoSettings();

if (!$action) {
	if (!$step) {
		$pagePosition = $cms_sitename ? "<a href='index.php?m=cms'>$cms_sitename</a>":'<a href="index.php?m=cms">��Ѷ</a>';
		S::gp(array('sourcetype', 'sourceid'));
		$columnService = C::loadClass('columnservice');
		/* @var $columnService PW_columnService */
		$columns = $columnService->getAllOrderColumns(0,$windid);
		$purviews = $columnService->getAllPurviewColumns($windid);

		$hasSource = isGM($windid) || checkEditPurview($windid) ? true : false;//��Ŀ�༭��ʼ�˲���Ȩ��ʹ���Զ�����
		if (!$hasSource) {
			$sourcetype = $sourceid = null;
		}
		$articleModule = $articleService->getArticleModuleFromSource($sourcetype, $sourceid);
		$atc_content = $articleModule->content;
		$articleModule->setColumnId($cid);

		list($attachAllow, $imageAllow) = initFileTypeInfo($db_uploadfiletype);
		require_once (M_P . 'require/header.php');
	} else {
		S::gp(array('cms_subject', 'atc_content', 'cms_descrip'), 'P', 0);
		S::gp(array('cms_sourcetype', 'cms_sourceid', 'cid', 'cms_jumpurl', 'cms_author', 'cms_frominfo',
			'cms_fromurl', 'cms_relate', 'addnewpage', 'cms_timelimit', 'flashatt'));
		$cms_timelimit = ($cms_timelimit && (isGM($windid) || checkEditPurview($windid))) ? PwStrtoTime($cms_timelimit) : $timestamp;
		$cms_jumpurl = ($cms_jumpurl && (isGM($windid) || checkEditPurview($windid))) ? $cms_jumpurl : '';
		
		PostCheck();

		$columnService = C::loadClass('columnservice');
		$column = $columnService->findColumnById((int)$cid);
		$purviews = $columnService->getAllPurviewColumns($windid);
		if (!windid && !isGM($windid) && !in_array($column['column_id'], $purviews) && (!$column['allowoffer'] || !getPostPurview($windid, $_G))) {
			Showmsg('��û��Ȩ������Ŀ�������');
		}

		$articleModule = C::loadClass('articleModule'); /* @var $articleModule PW_ArticleModule */

		$articleModule->setSubject($cms_subject);
		$articleModule->setContent($atc_content);
		$articleModule->setDescrip($cms_descrip);
		$articleModule->setColumnId($cid);
		$articleModule->setJumpUrl($cms_jumpurl);
		$articleModule->setPostDate($cms_timelimit);
		$articleModule->setModifyDate($timestamp);
		$articleModule->setFromInfo($cms_frominfo);
		$articleModule->setFromUrl($cms_fromurl);
		$articleModule->setAuthor($cms_author);
		$articleModule->setUser($windid);
		$articleModule->setUserId($winduid);
		$articleModule->setIfCheck(1);
		$articleModule->setSourceType($cms_sourcetype);
		$articleModule->setSourceId($cms_sourceid);
		$articleModule->setRelate($cms_relate);
		$articleModule->showError();//���������¸�����ʧ����
		$articleModule->setAttach($flashatt);
		$articleModule->showError();

		$result = $articleService->addArticle($articleModule);
		

		if ($result) {
			$jumpUrl = $addnewpage ? $basename . "q=post&action=edit&id=" . $result . "&page=add" : $basename . "q=view&id=" . $result;

			$columnService = C::loadClass('columnservice'); /* @var $columnService PW_ColumnService */
			$cname = $columnService->getColumnNameByCIds($cid);
			$weiboService = L::loadClass('weibo','sns');/* @var $weiboService PW_Weibo */
			$weiboContent = substrs(stripWindCode($weiboService->escapeStr($articleModule->descrip)), 125);
		//	print_r ($weiboContent);exit;
			$weiboExtra = array(
							'title' => stripslashes($cms_subject),
							'cid' => $cid,
							'cname' => $cname,
							);
			$weiboService->send($winduid,$weiboContent,'cms',$result,$weiboExtra);
			
		    $msg = defined('AJAX') ?  "success\t".urlRewrite($jumpUrl) : '������³ɹ�!';	
		    refreshto($jumpUrl, $msg);
		} else {
			Showmsg('�������ʧ��');
		}
	}
} elseif ($action == 'edit') {
	S::gp(array('id', 'page'));
	$articleModule = $articleService->getArticleModule($id);
	$userid = $articleModule->userId;
	if (!checkEditPurview($windid, $articleModule->columnId) && $userid != $winduid) Showmsg('��û��Ȩ�ޱ༭����Ŀ������');
	if (!$step) {
		if (!$page) $page = 1;
		if (!is_object($articleModule)) Showmsg('���²�����');
		
		$pagePosition = getPosition($articleModule->columnId,'','',$cms_sitename);

		$columnService = C::loadClass('columnservice'); /* @var $columnService PW_columnService */
		$columns = $columnService->getAllOrderColumns(0,$windid);

		$attach = initAttach($articleModule->attach);
		$postdate = get_date($articleModule->postDate);

		$atc_content = $articleModule->getPageContent($page);
		$articleModule->showError();

		$pages = $articleModule->getPages($page, CMS_BASEURL.'q=post&action=edit&id=' . $id . '&');

		list($attachAllow, $imageAllow) = initFileTypeInfo($db_uploadfiletype);
		require_once (M_P . 'require/header.php');
	} else {
		S::gp(array('cms_subject', 'atc_content', 'cms_descrip'), 'P', 0);
		S::gp(array('cms_sourcetype', 'cms_sourceid', 'cid', 'cms_jumpurl', 'cms_author', 'cms_frominfo',
			'cms_fromurl', 'cms_relate', 'flashatt', 'oldatt_desc', 'addnewpage', 'cms_timelimit'));
		$cms_timelimit = ($cms_timelimit && (isGM($windid) || checkEditPurview($windid))) ? PwStrtoTime($cms_timelimit) : $timestamp;
		$cms_jumpurl = ($cms_jumpurl && (isGM($windid) || checkEditPurview($windid))) ? $cms_jumpurl : '';
		PostCheck();
		$articleModule->setSubject($cms_subject);
		$articleModule->setContent($atc_content, $page);
		$articleModule->setDescrip($cms_descrip);
		$articleModule->setColumnId($cid);
		$articleModule->setJumpUrl($cms_jumpurl);
		$articleModule->setPostDate($cms_timelimit);
		$articleModule->setModifyDate($timestamp);
		$articleModule->setFromInfo($cms_frominfo);
		$articleModule->setFromUrl($cms_fromurl);
		$articleModule->setAuthor($cms_author);
		$articleModule->setUser($windid);
		$articleModule->setUserId($winduid);
		$articleModule->setRelate($cms_relate);
		$articleModule->setSourceType($cms_sourcetype);
		$articleModule->setSourceId($cms_sourceid);
		$articleModule->showError();//���������¸�����ʧ����
		$articleModule->setAttach($flashatt, $oldatt_desc);
		$articleModule->showError();

		$result = $articleService->updateArticle($articleModule);
		if ($result) {
			$jumpUrl = $addnewpage ? $basename . "q=post&action=edit&id=" . $id . "&page=add" : $basename . "q=view&id=" . $id;
			$msg = defined('AJAX') ?  "success\t".$jumpUrl : '�޸����³ɹ�!';	
		    refreshto($jumpUrl, $msg);
		} else {
			Showmsg('�޸�����ʧ��');
		}
	}
} elseif ($action == 'deletepage') {
	S::gp(array('id', 'page'));
	$articleModule = $articleService->getArticleModule($id);

	if (!checkEditPurview($windid, $articleModule->columnId) && $articleModule->user != $windid) Showmsg('��û��Ȩ�ޱ༭����Ŀ������');

	$articleModule->deletePage($page);
	$articleModule->showError();
	$result = $articleService->updateArticle($articleModule);
	if ($result) {
		refreshto("{$basename}q=post&action=edit&id=$id&page=1", 'operate_success', 2);
	} else {
		Showmsg('ɾ����ҳʧ��');
	}
}
require cmsTemplate::printEot('post');
footer();

function initFileTypeInfo($db_uploadfiletype) {
	$uploadfiletype = ($db_uploadfiletype) ? unserialize($db_uploadfiletype) : array();
	$attachAllow = pwJsonEncode($uploadfiletype);
	$imageAllow = pwJsonEncode(getAllowKeysFromArray($uploadfiletype, array('jpg','jpeg','gif','png','bmp')));
	return array($attachAllow, $imageAllow);
}

function initAttach($attachs) {
	$attach = '';
	if ($attachs) {
		foreach ($attachs as $key => $value) {
			$attach .= "'$key' : ['$value[name]', '$value[size]', '$value[attachurl]', '$value[type]', '0', '0', '', '$value[descrip]'],";
		}
		$attach = rtrim($attach, ',');
	}
	return $attach;
}


?>
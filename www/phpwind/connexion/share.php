<?php
require_once('../global.php');

InitGP(array('type','action', 'tid', 'shareContent', 'isfollow', 'photo'));

if (!$winduid || !in_array($type, array('sinaweibo'))) return ;
// վ���Ƿ�󶨸�����
$weiboSiteBindService = L::loadClass('WeiboSiteBindService', 'sns/weibotoplatform/service');
if (!$weiboSiteBindService->isBind($type)) return ;
$db_bbsurl.="/";
if ($tid) { // ȡ��������
	//$threads = L::loadClass('Threads', 'forum');
	//$read = $threads->getByThreadId($tid);
	$read = $db->get_one("SELECT t.* ,tm.* FROM pw_threads t LEFT JOIN ".S::sqlMetadata(GetTtable($tid))." tm ON t.tid=tm.tid WHERE t.tid=" . S::sqlEscape($tid));
	if (!empty($read)) {
		$sinaWeiboContentTranslator = L::loadClass('SinaWeiboContentTranslator', 'sns/weibotoplatform/');
		$shareContent = $sinaWeiboContentTranslator->translate('article', array('content'=>preg_replace(array('/(&nbsp;){1,}/', '/( ){1,}/'), array(' ', ' '), substrs(stripWindCode(str_replace("\n", " ", strip_tags($read['content']))), 100)), 'objectid'=>$tid), array('title'=>$read['subject']));
		$title = urlencode(pwConvert($shareContent, 'UTF8', $db_charset));
		$query = $db->query("SELECT aid,attachurl,pid,type,ifthumb FROM pw_attachs WHERE pid=0 AND tid=" . S::sqlEscape($tid));
        $attachImg = '';
		while($rt = $db->fetch_array($query)){
			if ($rt['type'] != 'img') continue;
			$tmpUrl = geturl($rt['attachurl'],$rt['ifthumb']);
			if (is_array($tmpUrl)) $attachImg[] = false !== strpos($tmpUrl[0], 'http://') ? $tmpUrl[0] : $db_bbsurl . $tmpUrl[0];
		}
		$photoCount = count($attachImg);
	}
}

// �û��Ƿ��Ѿ����˸����͵��ʺ� û��������
$weiboUserBindService = L::loadClass('WeiboUserBindService', 'sns/weibotoplatform/service');
$userInfo = $weiboUserBindService->getBindInfo($winduid, $type);
if (empty($userInfo)) { // ������
	$userBindList = $weiboUserBindService->getBindList($winduid);
	$bindUrl = $userBindList[$type]['bindUrl'];
	$action = 'bind';
} else {

	$weiboName = $userInfo['info']['name'];
	
	if ($action == 'share' && !empty($shareContent)) { // ����
		$weiboSyncerService = L::loadClass('WeiboSyncer', 'sns/weibotoplatform');
		$result = $weiboSyncerService->shareContent($winduid, $shareContent, $photo);
		// ������ע�ٷ��ʺ�
		if ($result) {
			if (!$weiboUserBindService->isFollow($type, $winduid)) ObHeader($db_bbsurl . "connexion/share.php?type={$type}&action=isfollow");
			$action = 'sharesuccess';
		} else {
			$action = 'sharefail';
		}
	} elseif ($action == 'isfollow') { // �йٷ�΢���ʺ���������ע û������ʾ����ɹ�
		$weiboSiteBindInfoService = L::loadClass('WeiboSiteBindInfoService', 'sns/weibotoplatform/service');
		$weiboAccount = $weiboSiteBindInfoService->getOfficalAccount($type);
		if (!$weiboAccount) $action = 'sharesuccess';
	} elseif ($action == 'follow') { // ��ע
		$weiboSiteBindInfoService = L::loadClass('WeiboSiteBindInfoService', 'sns/weibotoplatform/service');
		$weiboAccount = $weiboSiteBindInfoService->getOfficalAccount($type);
		if ($weiboAccount && $isfollow) $result = $weiboUserBindService->follow($type, $winduid);
		$action = $result ? 'followsuccess' : 'followfail';
	}
}
include PrintTemplate('share_sina');
pwOutPut();

function PrintTemplate($template, $EXT = 'htm') {
	return R_P.'connexion/template/'.$template.".$EXT";
}

function getWeiboUserBindService() {
	return L::loadClass('WeiboUserBindService', 'sns/weibotoplatform/service');
}
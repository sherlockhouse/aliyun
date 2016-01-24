<?php
!defined('W_P') && exit('Forbidden');
!$windid && wap_msg('not_login');
require_once (R_P . 'require/functions.php');
require_once (R_P . 'require/bbscode.php');
require_once R_P . 'u/require/core.php';
require_once (R_P . 'require/showimg.php');
require_once (R_P . 'u/lib/space.class.php');
$messageServer = L::loadClass('message', 'message');
InitGP(array('action', 'page'));
!empty($winduid) && $userId = $winduid;
$sms_type = $messageServer->getConst('sms_message');
empty($page) && $page = 1;

/*���������Ϣ*/
//$smsAllCount = (int) $messageServer->countAllMessage($userId);
$smsAllCount = (int) $messageServer->countInBox($userId);
$notReadCount = (int) $messageServer->countMessagesNotRead($userId);
$smsCount = $messageServer->countMessagesBySelf($userId, $sms_type);
$basename .= empty($action) ? '' : '?action=' . $action;
if ($action == 'send') {
	InitGP(array('step'));
	if (!$_G['allowmessege']) wap_msg('�����ڵ��û��鲻�ܷ�����Ϣ', 'index.php?a=ms_index');
	if ($step == '2') {
		InitGP(array('pwuser', 'title', 'content'));
		$pwuser = is_array($pwuser) ? $pwuser : explode(",", $pwuser);
		empty($pwuser) && wap_msg('�û�������Ϊ��', $basename);
		empty($title) && wap_msg('���ⲻ��Ϊ��', $basename);
		empty($content) && wap_msg('���ݲ���Ϊ��', $basename);
		if (in_array($windid, $pwuser)) wap_msg('�����ܸ��Լ�����Ϣ', $basename);
		list($bool, $message) = $messageServer->checkReceiver($pwuser);
		if (!$bool) {
			wap_msg($message, $basename);
		}
		if (isset($_G['messagecontentsize']) && $_G['messagecontentsize'] > 0 && strlen($content) > $_G['messagecontentsize']) {
			wap_msg('���ݳ����޶�����' . $_G['messagecontentsize'] . '�ֽ�', $basename);
		}
		$filterUtil = L::loadClass('filterutil','filter');
		$content = $filterUtil->convert($content);
		$title = $filterUtil->convert($title);
		$messageInfo = array('create_uid' => $winduid, 'create_username' => $windid, 'title' => $title, 
			'content' => $content);
		$messageService = L::loadClass("message");
		if (($messageId = $messageService->sendMessage($winduid, $pwuser, $messageInfo))) {
			initJob($winduid, 'doSendMessage', array('user' => $usernames));
			wap_sms_msg('���ͳɹ���');
		}
		wap_msg('��Ϣ����ʧ��', $basename);
	}
} elseif ($action == 'all') {
	//$smsCount = $messageServer->countMessage($userId, $sms_type);
	//$smsList = $messageServer->getMessages($userId, $sms_type, $page, $wap_perpage);
	$smsCount = (int) $messageServer->countInBox($userId);
	$smsList = $messageServer->getInBox($userId, $page, $wap_perpage);
	//print_r($smsList);exit;
	!$smsCount && $emptyListTip = "�����κ�վ���ţ�";
	$messageServer->resetStatistics(array($winduid),'sms_num');
	$pages = getPages($page, count($smsList), "$basename&a=ms_index&");
} elseif ($action == 'self') {
	$smsCount = $messageServer->countMessagesBySelf($userId, $sms_type);
	$smsList = $messageServer->getMessagesBySelf($userId, $sms_type, $page, $wap_perpage);
	$pages = getPages($page, count($smsList), "$basename&");
} elseif ($action == 'info') {
	InitGP(array('mid', 'rid', 'redirect', 'page'));
	empty($mid) && wap_msg("�Ƿ�����");
	$_url = "index.php?a=ms_index&action=$redirect&page=$page";
	if (!$relation = $messageServer->getRelation($userId, $rid)) {
		wap_msg("������Ϣ����Ȩ�鿴", $_url);
	}
	if (!($message = $messageServer->getMessage($mid))) {
		wap_msg("������Ϣ������", $_url);
	}
	if($relation['relation'] == 2){
		$expand = (isset($message['expand'])) ? unserialize($message['expand']) : array();
		$message = $messageServer->getMessage($expand['parentid']);
		!$message && wap_msg("������Ϣ������");
		$mid = $message['mid'];
	}
	$message['rid'] = $rid;
	$userListHtml = getAllUsersHtml($message);
	$smsList = $messageServer->getReplies($userId, $mid, $rid);
} elseif ($action == 'up') {
	InitGP(array('rid','redirect'), 'GP');
	list($isown,$_url) = wap_redirect_init($redirect);
	empty($rid) && wap_msg("�Ƿ�����");
	if (!($message = $messageServer->getUpInfoByType($userId, $rid,$isown))) {
		wap_sms_msg("�Ѿ��ǵ�һ��");
	} else {
		$userListHtml = getAllUsersHtml($message);
		$smsList = $messageServer->getReplies($userId, $message['mid'], $rid);
	}
} elseif ($action == 'down') {
	InitGP(array('rid','redirect'), 'GP');
	list($isown,$_url) = wap_redirect_init($redirect);
	empty($rid) && wap_msg("�Ƿ�����");
	if (!($message = $messageServer->getDownInfoByType($userId, $rid,$isown))) {
		wap_sms_msg("�Ѿ������һ��");
	} else {
		$userListHtml = getAllUsersHtml($message);
		$smsList = $messageServer->getReplies($userId, $message['mid'], $rid);
	}
} elseif ($action == 'post') {
	InitGP(array('parentMid', 'atc_content', 'rid'), 'GP');
	$_url = "index.php?a=ms_index&action=info&mid=$parentMid&rid=$rid&";
	if (!$_G['allowmessege']) wap_msg('�����ڵ��û��鲻�ܷ�����Ϣ', $_url);
	empty($parentMid) && wap_msg('�Ƿ�����', $_url);
	empty($atc_content) && wap_msg('�ظ����ݲ���Ϊ��', $_url);
	$atc_content = trim(strip_tags($atc_content));
	$messageInfo = array('create_uid' => $winduid, 'create_username' => $windid, 'title' => $windid, 
		'content' => $atc_content);
	if (!($message = $messageServer->sendReply($winduid, $rid, $parentMid, $messageInfo))) {
		wap_msg('�ظ�ʧ��', $_url);
	}
	wap_sms_msg('�ظ��ɹ���');
}
wap_header();
require_once PrintWAP('ms_index');
wap_footer();

function wap_redirect_init($redirect) {
	if ($redirect=='self') {
		$isown = 1;
		$_url = "index.php?a=ms_index&action=self";
	} else {
		$isown = 0;
		$_url = "index.php?a=ms_index&action=all";
	}
	return array($isown,$_url);
}

function getAllUsersHtml($message, $type = '') {
	global $windid;
	$userList = (array) unserialize($message['extra']);
	if (!in_array($message['create_username'], $userList)) {
		$userList = array_merge(array($message['create_username']), $userList);
	}
	$userListHtml = "";
	for ($i = 0; $i < count($userList); $i++) {
		$_userName = $userList[$i] == $windid ? '��' : $userList[$i];
		if ($i == 0) {
			$userListHtml .= '<a href="index.php?a=myhome&username=' . urlencode($userList[$i]) . '">' . $_userName . '</a> �� ';
		} else {
			$userListHtml .= '<a href="index.php?a=myhome&username=' . urlencode($userList[$i]) . '">' . $_userName . '</a>, ';
		}
	}
	$userListHtml = trim($userListHtml, ', ');
	return $userListHtml;
}

function wap_sms_msg($msg, $url = "") {
	$ysmsg = is_array($msg) ? array_pop($msg) : $msg;
	$msg = getWapLang('wap', $ysmsg);
	if (!empty($msg) && $msg == $ysmsg) {
		$msg = getLangInfo('msg', $ysmsg);
		$msg = strip_tags($msg);
	}
	wap_header($url);
	if ($msg) {
		$str = '<br><div class="warning">' . $msg . '</div>';
		$str .= '<div>
				 <a href="index.php?a=ms_index&action=send">������Ϣ</a><br />
				 <a href="index.php?a=ms_index&action=all">����������Ϣ</a><br />
				 <a href="index.php?a=ms_index&action=self">�����ѷ���Ϣ</a></div>';
		echo $str;
	} else {
		echo $ysmsg;
	}
	wap_footer();
}
?>
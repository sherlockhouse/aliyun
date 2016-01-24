<?php
!defined('A_P') && exit('Forbidden');

!$db_kmd_ifkmd  && Showmsg('kmd_close');
S::gp(array('a', 'action'));
S::gp(array('page'), 'GP', 2);
!S::inArray($a, array('info', 'records', 'buy', 'addthread', 'getthread', 'ajax', 'help')) && $a = 'info';
!$winduid && $a != 'help' && Showmsg('not_login');

$kmdService = L::loadClass('KmdService', 'forum');
$basename = "apps.php?q=$q&";
$current[$a] = 'class="current"'; 

if ($a == 'info') {
	$unPayedInfos = $kmdInfos = array();
	!$page && $page = 1;
	$kmdService->setPayLogsInvalidUsingTimestamp($winduid);
	$unPayedInfos = $kmdService->getUnPayedLogsByUid($winduid);
	$kmdInfos = $kmdService->getKmdInfoDetailByUid($winduid, ($page - 1) * $db_perpage, $db_perpage);
	$countKmdNum = $kmdService->countKmdInfosWithCondition(array('uid' => $winduid));
	$numofpages = numofpage($countKmdNum, $page, ceil($countKmdNum / $db_perpage), "{$basename}a=info&");

	if (!S::isArray($unPayedInfos) && !S::isArray($kmdInfos)) ObHeader($basename . 'a=buy');
	require_once PrintEot('m_kmd');
	pwOutPut();
} elseif ($a == 'records') {
	$records = array();
	!$page && $page = 1;
	$records = $kmdService->searchPayLogs(array('uid' => $winduid), ($page - 1) * $db_perpage, $db_perpage);
	$recordsNum = $kmdService->countPayLogs(array('uid' => $winduid));
	$numofpages = numofpage($recordsNum, $page, ceil($recordsNum / $db_perpage), "{$basename}a=records&");

	require_once PrintEot('m_kmd');
	pwOutPut();
} elseif ($a == 'buy') {
	!$_G['allowbuykmd'] && Showmsg('��Ǹ�����������û���û��Ȩ�޹��������');
	S::gp(array('kid', 'pid'), 'GP', 2);
	if (!$action || S::inArray($action, array('renew', 'pay'))) {
		pwCache::getData(D_P . 'data/bbscache/ol_config.php');
		
		$forumService = L::loadClass('forums','forum');
		$getForums = $forumService->getKmdForums();
		$kmdForumsHtml = getKmdForumsHtml($getForums);
		$spreads = $kmdService->getSpreads();
		$jsonSpreadsArray = array();
		foreach ($spreads as $value) {
			$jsonSpreadsArray[] = $value;
		}
		$jsonSpreads = pwJsonEncode($jsonSpreadsArray);
		$userBuyInfo = $kmdService->getUserInfoByUid($winduid);
		$alipayChecked = $bankChecked = $cashChecked = '';
		$ol_onlinepay && $ol_payto && $alipayChecked = 'checked';
		!$alipayChecked && $db_kmd_account && $db_kmd_bank && $bankChecked = 'checked';
		!$alipayChecked && !$bankChecked && $db_kmd_address && $cashChecked = 'checked';
		
		if ($action == 'renew') {
			!$kid && Showmsg('��ѡ��Ҫ���ѵĿ�����');
			$kmdInfo = $kmdService->getKmdInfoByKid($kid);
			!$kmdInfo && Showmsg('��ѡ��Ŀ����Ʋ�����');
			$kmdInfo['endtime'] <= $timestamp && Showmsg('�ÿ������ѹ���');
			$forumcache = str_replace("value=\"$kmdInfo[fid]\"", "value=\"$kmdInfo[fid]\" selected", $forumcache);
			$disabled = 'disabled';
		} elseif ($action == 'pay') {
			!$pid && Showmsg('��ѡ��Ҫ֧���Ŀ�����');
			$payLog = $kmdService->getPayLogById($pid);
			!$payLog && Showmsg('֧����¼������');
			L::loadClass('forum', 'forum', false);
			$forumInfo = new PwForum($payLog['fid']);
			$tmpForumInfo = '<a href="thread.php?fid=' . $forumInfo->fid . '" target="_blank">' . $forumInfo->name . '</a>';
			$tmpSpread = $spreads[$payLog['sid']];
			$tmpSpreadDiscount = (0 < $tmpSpread['discount'] && $tmpSpread['discount'] < 10) ? $tmpSpread['discount'] . '��' : '���ۿ�' ;
			$tmpSpreadMoney = (0 < $tmpSpread['discount'] && $tmpSpread['discount'] < 10) ? ($tmpSpread['price'] * $tmpSpread['discount'] / 10) : $tmpSpread['price'];
			$tmpSpreadMoney = round($tmpSpreadMoney, 2);
			$tmpSpreadInfo = 'ԭ�� ' . $tmpSpread['price'] . 'Ԫ��<span class="s2">' . $tmpSpreadDiscount . '</span>��Ӧ�� <span class="s2">' . $tmpSpreadMoney . 'Ԫ</span>';
		}
		
		require_once PrintEot('m_kmd');
		pwOutPut();
	} elseif ($action == 'save') {
		S::gp(array('realname', 'invoice', 'address', 'phone'));
		S::gp(array('fid', 'spread', 'paytype'), 'GP', 2);

		if (!$pid) {
			!$spread && Showmsg('��ѡ���ƹ��ײ�');
			!$realname && Showmsg('����д��ʵ����');
			!$phone && Showmsg('����д�ֻ�����');
			!preg_match('/^1\d{10}$/is', $phone) && Showmsg('�ֻ������ʽ����ȷ');
			if ($kid) {
				$kmdInfo = $kmdService->getKmdInfoByKid($kid);
				!$kmdInfo && Showmsg('��ѡ��Ŀ����Ʋ�����');
				$kmdInfo['endtime'] <= $timestamp && Showmsg('�ÿ������ѹ���');
				$fid = $kmdInfo['fid'];
			}
			$fid < 1 && Showmsg('��ѡ��Ҫ�ƹ�İ��');
		} else {
			$payLog = $kmdService->getPayLogById($pid);
			!$payLog && Showmsg('֧����¼������');
			list($fid, $spread) = array($payLog['fid'], $payLog['sid']);
		}
		
		!$paytype && Showmsg('��ѡ��֧����ʽ');
		L::loadClass('forum', 'forum', false);
		$forumInfo = new PwForum($fid);
		!$forumInfo->forumset['ifkmd'] && Showmsg('�ð��δ����������');
		$leftKmdNum = $kmdService->getLeftKmdNumsByFid($fid);
		(!$leftKmdNum && !$kid && !$pid) && Showmsg('��ѡ���ƹ�İ�飬������λ����������ѡ���������');
		$spreadInfo = $kmdService->getSpreadById($spread);
		!$spreadInfo && Showmsg('��ѡ����ƹ��ײͲ�����');
		pwCache::getData(D_P . 'data/bbscache/ol_config.php');
		((!$ol_onlinepay || !$ol_payto) && (!$db_kmd_account || !$db_kmd_bank) && !$db_kmd_address) && Showmsg('վ��δ����֧����ʽ�����ܹ���');
		!S::inArray($paytype, array(KMD_PAY_TYPE_ALIPAY, KMD_PAY_TYPE_BANK, KMD_PAY_TYPE_CASH)) && Showmsg('ѡ���֧����ʽ����ȷ');
		($paytype == KMD_PAY_TYPE_ALIPAY && (!$ol_onlinepay || !$ol_payto)) && Showmsg('վ��δ����֧����֧��');
		($paytype == KMD_PAY_TYPE_BANK && (!$db_kmd_account || !$db_kmd_bank)) && Showmsg('վ��δ��������ת����Ϣ');
		($paytype == KMD_PAY_TYPE_CASH && !$db_kmd_address) && Showmsg('վ��δ���ð����ַ');
		
		if (!$pid) {
			$money = (0 < $spreadInfo['discount'] && $spreadInfo['discount'] < 10) ? ($spreadInfo['price'] * $spreadInfo['discount'] / 10) : floatval($spreadInfo['price']);
			$money = round($money, 2);
			$userInfo = array('uid' => $winduid, 'phone' => $phone, 'realname' => $realname, 'invoice' => $invoice, 'address' => $address);
			$tmpKid = $kid ? $kid : 0;
			$payLog = array('fid' => $fid, 'uid' => $winduid, 'sid' => $spread, 'kid' => $tmpKid, 'type' => $paytype, 'money' => $money, 'status' => KMD_PAY_STATUS_NOTPAY, 'createtime' => $timestamp);
		
			$kmdService->setUserInfoByUid($userInfo);
			$payLogId = $kmdService->addPayLog($payLog);
			
			$tmpMessageContent = array('username' => $windid, 'fid' => $fid, 'forumname' => $forumInfo->name, 'money' => $money);
			if ($db_kmd_reviewperson) {
				$kmdReviewPerson = explode(',', $db_kmd_reviewperson);
				$kmdReviewPerson = array_unique(array_merge($kmdReviewPerson, $manager));
				sendKmdMessages($kmdReviewPerson, array('kmd_review_title', array('username' => $windid)), array('kmd_review_content', $tmpMessageContent));
			}
			sendKmdMessages(array($windid), array('kmd_review_user_title'), array('kmd_review_user_content', $tmpMessageContent));
			
			if (!$money) { //֧����ǮΪ0ʱ��ֱ��֧���ɹ�
				$updatePayLog = array('status' => KMD_PAY_STATUS_PAYED);
				if (!$kid) { //�¹���
					$endtime = $timestamp + $spreadInfo['day'] * 86400;
					$newKmdInfo = array('fid' => $fid, 'uid' => $winduid, 'tid' => 0, 'status' => KMD_THREAD_STATUS_EMPTY, 'starttime' => $timestamp, 'endtime' => $endtime);
					$kmdService->addKmdInfo($newKmdInfo);
				} else { //����
					$endtime = $kmdInfo['endtime'] + $spreadInfo['day'] * 86400;
					$updateKmdInfo = array('endtime' => $endtime);
					$kmdService->updateKmdInfo($updateKmdInfo, $kid);
				}
				$kmdService->updatePayLog($updatePayLog, $payLogId);
				refreshto("{$basename}a=info", '����ɹ�!');
			}
			
			$successMessage = $kid ? '���Ŀ����������������ύ����ȴ�����Աȷ��֧����' : '���Ŀ����ƹ����������ύ����ȴ�����Աȷ��֧����';
			$paytype != KMD_PAY_TYPE_ALIPAY && refreshto("{$basename}a=info", $successMessage);
		} else {
			$updatePayLog = array('type' => $paytype, 'status' => KMD_PAY_STATUS_NOTPAY, 'createtime' => $timestamp);
			$kmdService->updatePayLog($updatePayLog, $payLog['id']);
			
			$tmpMessageContent = array('username' => $windid, 'fid' => $fid, 'forumname' => $forumInfo->name, 'money' => $payLog['money']);
			if ($db_kmd_reviewperson) {
				$kmdReviewPerson = explode(',', $db_kmd_reviewperson);
				$kmdReviewPerson = array_unique(array_merge($kmdReviewPerson, $manager));
				sendKmdMessages($kmdReviewPerson, array('kmd_review_title', array('username' => $windid)), array('kmd_review_content', $tmpMessageContent));
			}
			sendKmdMessages(array($windid), array('kmd_review_user_title'), array('kmd_review_user_content', $tmpMessageContent));
			
			$paytype != KMD_PAY_TYPE_ALIPAY && refreshto("{$basename}a=info", '����֧����Ϣ���ύ����ȴ�����Աȷ��֧����');
			list($money, $payLogId) = array($payLog['money'], $payLog['id']);
		}
		
		$order_no = str_pad('0', 10, "0", STR_PAD_LEFT) . get_date($timestamp, 'YmdHis') . num_rand(5);
		$email = $winddb ? $winddb['email'] : '';
		$db->update("REPLACE INTO pw_clientorder SET " . S::sqlSingle(array(
			'order_no'	=> $order_no,
			'type'		=> 5,
			'uid'		=> $winduid,
			'price'		=> $money,
			'payemail'	=> $email,				
			'number'	=> 1,
			'date'		=> $timestamp,
			'state'		=> 0,
			'extra_1'   => $payLogId,
		)));
					
		require_once(R_P . 'require/onlinepay.php');
		$olpay = new OnlinePay($ol_payto);			
		ObHeader($olpay->alipayurl($order_no, $money, 5, "{$basename}a=info"));
	}
} elseif ($a == 'addthread') {
	S::gp(array('originalaction', 'tpcurl'));
	S::gp(array('kid', 'threadid', 'originaltid'), 'GP', 2);
	$kid < 1 && kmdAjaxMessage('�����Ʋ�����');

	$kmdInfo = $kmdService->getKmdInfoByKid($kid);
	!$kmdInfo && kmdAjaxMessage('�����Ʋ�����');
	$kmdInfo['uid'] != $winduid && kmdAjaxMessage('����Ȩ�������˵Ŀ�����');
	$kmdInfo['endtime'] <= $timestamp && kmdAjaxMessage('�ÿ������ѹ���');
	
	if (!$action || ($action == 'changethread' && !$originaltid)) {
		$title = $content = $tid = '';
		$getThreadUrl = $basename . 'a=getthread';
		if ($action == 'changethread') {
			$threadCacheService = Perf::gatherCache('pw_threads');
			$threadInfo = $threadCacheService->getThreadAndTmsgByThreadId($kmdInfo['tid']);
			$tid = $threadInfo['tid'];
			$threadUrl = $db_bbsurl . '/read.php?tid=' . $tid;
			$title = $threadInfo['subject'];
			$content = substrs(stripWindCode($threadInfo['content']), 100);
		}
		require_once PrintEot('m_kmd_ajax');
		ajax_footer();
	} elseif ($action == 'save') {
		if (!$threadid && $tpcurl) {
			$tpcurl = html_entity_decode($tpcurl);
			$urlInfo = parse_url($tpcurl);
			$urlInfo['host'] != $pwServer['HTTP_HOST'] && kmdAjaxMessage('���Ӳ���ȷ');
			preg_match("/tid=(\d+)/i", $tpcurl, $data) || preg_match("/tid-(\d+)/i", $tpcurl, $data) || preg_match("/\/(\d+)\.(htm|html)/i", $tpcurl, $data);
			$threadid = $data[1];
		}
		!$threadid && kmdAjaxMessage('��������������');
		$originaltid == $threadid && kmdAjaxMessage('�滻�����Ӳ��ܸ�ԭ������ͬ');
		($originalaction == 'changethread' && (!$originaltid || $originaltid != $kmdInfo['tid'])) && kmdAjaxMessage('�������');
		$threadInfo = checkKmdThread($threadid);
		$threadInfo['fid'] != $kmdInfo['fid'] && kmdAjaxMessage('�����Ӳ����ڵ�ǰ���������ڰ��');
		$threadInfo['topped'] && kmdAjaxMessage('�������Ѿ����ö������������Ϊ������');
		if ($originalaction == 'changethread') {
			(!$originaltid || $originaltid != $kmdInfo['tid']) && kmdAjaxMessage('�������');
			($db_kmd_deducttime && (($timestamp + $db_kmd_deducttime * 3600) >= $kmdInfo['endtime'])) && kmdAjaxMessage('�ƹ�ʱ�䲻�㣬�޷�������');
		}
		
		$kmdUpdateInfo = array('tid' => $threadid, 'status' => KMD_THREAD_STATUS_CHECK);
		$originalaction == 'changethread' && ($kmdUpdateInfo['endtime'] = $kmdInfo['endtime'] - $db_kmd_deducttime * 3600);
		$kmdService->updateKmdInfo($kmdUpdateInfo, $kid);
		$originaltid && $kmdService->updateKmdThreadByTid($originaltid, 0);
		
		$tmpMessageContent = array('username' => $windid, 'tid' => $threadid, 'threadtitle' => $threadInfo['subject']);
		if ($db_kmd_reviewperson) {
			$kmdReviewPerson = explode(',', $db_kmd_reviewperson);
			$kmdReviewPerson = array_unique(array_merge($kmdReviewPerson, $manager));
			$messageTitle = $originalaction == 'changethread' ? 'kmd_review_thread_change_title' : 'kmd_review_thread_add_title';
			sendKmdMessages($kmdReviewPerson, array($messageTitle, array('username' => $windid)), array('kmd_review_thread_content', $tmpMessageContent));
		}
		sendKmdMessages(array($windid), array('kmd_review_user_thread_title'), array('kmd_review_user_thread_content', $tmpMessageContent));
		
		require_once(R_P . 'require/updateforum.php');
		updatetop();
		kmdAjaxMessage('�����ɹ���', 'success');
	}
} elseif ($a == 'getthread') {
	S::gp(array('tpcurl'));
	$tpcurl = html_entity_decode(urldecode($tpcurl));
	!$tpcurl && kmdAjaxMessage('��������������');
	
	$urlInfo = parse_url($tpcurl);
	$urlInfo['host'] != $pwServer['HTTP_HOST'] && kmdAjaxMessage('���Ӳ���ȷ');
	preg_match("/tid=(\d+)/i", $tpcurl, $data) || preg_match("/tid-(\d+)/i", $tpcurl, $data) || preg_match("/\/(\d+)\.(htm|html)/i", $tpcurl, $data);
	(!$data || $data[1] < 1) && kmdAjaxMessage('�����Ӳ����ڣ���ȷ��URL�Ƿ���ȷ');
	$threadInfo = checkKmdThread($data[1]);
	$threadInfo['topped'] && kmdAjaxMessage('�������Ѿ����ö������������Ϊ������');
	
	$content = substrs(stripWindCode($threadInfo['content']), 100);
	$info = array('tid' => $threadInfo['tid'], 'title' => $threadInfo['subject'], 'content' => $content);
	kmdAjaxMessage(pwJsonEncode($info), 'success'); 
} elseif ($a == 'ajax') {
	S::gp(array('action'));
	S::gp(array('fid'), 'GP', 2);
	if ($fid < 1) kmdAjaxMessage('��ѡ��Ҫ�ƹ�İ��');
	
	L::loadClass('forum', 'forum', false);
	$forumInfo = new PwForum($fid);
	if (!$forumInfo->forumset['ifkmd']) kmdAjaxMessage('�ð��δ����������');
	$leftKmdNum = $kmdService->getLeftKmdNumsByFid($fid);
	$value = $leftKmdNum > 0 ? $forumInfo->name : 0;
	$value = S::inArray($action, array('renew', 'pay')) ? 1 : $value;
	kmdAjaxMessage($value, 'success');
} elseif ($a == 'help') {
	require_once(R_P.'require/header.php');
	
	$openforum = $spreads = array();
	foreach ($forum as $value){
		$foruminfo = array();
		pwCache::getData(S::escapePath(D_P . "data/forums/fid_{$value['fid']}.php"));
		$foruminfo['forumset']['ifkmd'] && $openforum[]= $value ;
	}
	$spreads = $kmdService->getSpreads();
	
	//�ж��Ƿ��֧����
	$siteBindService = L::loadClass('WeiboSiteBindService', 'sns/weibotoplatform/service'); /* @var $siteBindService PW_WeiboSiteBindService */
	$isBindAlipay =0;
	if($siteBindService->isBind('alipay')){
		$isBindAlipay =1;
	}
	
	//��ȡalipay��¼����js����
	L::loadClass('WeiboLoginViewHelper', 'sns/weibotoplatform/viewhelper', false);
	$windowOpenScript = WeiboLoginViewHelper_WindowOpenScript('alipay'); 

	require_once PrintEot('m_kmd_help'); //�����¼ģ��
	footer();
}

function sendKmdMessages($user, $title, $content) {
	M::sendNotice($user, array(
		'title' => getLangInfo('writemsg', $title[0], $title[1]),
		'content' => getLangInfo('writemsg', $content[0], $content[1]))
	);
}

function checkKmdThread($tid) {
	global $winduid, $kmdService;
	$threadCacheService = Perf::gatherCache('pw_threads');
	$threadInfo = $threadCacheService->getThreadAndTmsgByThreadId($tid);
	!$threadInfo && kmdAjaxMessage('�����Ӳ����ڣ���ȷ��URL�Ƿ���ȷ');
	$threadInfo['authorid'] != $winduid && kmdAjaxMessage('������ֻ�ܶ��Լ�������ʹ�ã���ȷ�ϸ����ӹ���');
	$threadExists = $kmdService->getKmdInfoByTid($tid);
	$threadExists && kmdAjaxMessage('�������Ѿ��ǿ������������������');
	return $threadInfo;
}

function kmdAjaxMessage($message, $type = 'error') {
	$message = getLangInfo('msg', $message);
	echo $type . "\t" . $message;
	ajax_footer();
}

function getKmdForumsHtml($kmdForums){
	$html = '';
	if(S::isArray($kmdForums)) {
		foreach ($kmdForums as $k=>$v) {
			$html .= '<option value="'.$k.'">'.$v['name'].'</option>';
		}
	}
	return $html;
}
?>
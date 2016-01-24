<?php
!defined('P_W') && exit('Forbidden');
S::gp(array('action'));
define('FRIEND_SEPARATER', '#%');
!in_array($action, array('friend', 'mark', 'del', 'postReply', 'overlook', 'post', 'agree', 'markgroup', 'shield',
	'unshield','open','close','replay')) && ajaxExport("�Ƿ������뷵��");
if (in_array($action, array('friend', 'agree','overlook'))) {
	L::loadClass('friend', 'friend', false);
	$friendObj = new PW_Friend(FRIEND_SEPARATER);
}
if(!$winduid) ajaxExport(array('bool' => $bool, 'message' => '�㻹û�е�¼'));
if ('friend' == $action) {
	/*
	S::gp(array('gname'));
	if ($gname == '-2') {
		 $friend = $friendObj->getFriends($winduid);
		 $group	 = $friendObj->getFriendColonys($winduid);
		 $json	 = array('friend'=>$friend,'group'=>$group);
		 ajaxExport($json);
	} elseif ($gname == '0') {
		 ajaxExport($friendObj->getFriendsByColony($winduid, 0));
	} elseif ($gname) {
		 ajaxExport($friendObj->getFriendsByColony($winduid, $gname, 'name'));
	} else {
		$friends = array();
		$attentionService = L::loadClass('attention','friend');
		$attentionList = $attentionService->getUidsInFollowList($winduid);
		if(S::isArray($attentionList)) {
			$userService = L::loadClass('userservice','user');
			$friends = $userService->getUserNamesByUserIds($attentionList);
		}
		ajaxExport($friends);
	}
	*/
	$attention = array();
	$attentionService = L::loadClass('attention','friend');
	$attentionList = $attentionService->getUidsInFollowList($winduid);
	if(S::isArray($attentionList)) {
		$userService = L::loadClass('userservice','user');
		$attention = $userService->getUserNamesByUserIds($attentionList);
	}
	$friend = $friendObj->getFriends($winduid);
	$json	 = array('friend'=>$friend,'attention'=>implode(FRIEND_SEPARATER,$attention));
	ajaxExport($json);
	
} elseif ('mark' == $action) {
	S::gp(array('rids'), 'GP');
	empty($rids) && ajaxExport("�Ƿ������뷵��");
	!is_array($rids) && $rids = explode(',', trim($rids, ','));
	if (!($messageServer->markMessages($winduid, $rids))) {
		ajaxExport("����Ѷ�����ʧ��");
	}
	ajaxExport("����Ѷ������ɹ�!");
} elseif ('markgroup' == $action) {
	S::gp(array('rids'), 'GP');
	empty($rids) && ajaxExport("�Ƿ������뷵��");
	!is_array($rids) && $rids = explode(',', trim($rids, ','));
	if (!($messageServer->markGroupMessages($winduid, $rids))) {
		ajaxExport("����Ѷ�����ʧ��");
	}
	ajaxExport("����Ѷ������ɹ�!");
} elseif ('del' == $action) {
	S::gp(array('rids'), 'GP');
	empty($rids) && ajaxExport("�Ƿ������뷵��");
	!is_array($rids) && $rids = explode(',', trim($rids, ','));
	if (!($messageServer->deleteMessages($winduid, $rids))) {
		ajaxExport("ɾ������ʧ��");
	}
	ajaxExport("ɾ�������ɹ�!");
} elseif ('postReply' == $action) {
	S::gp(array('parentMid', 'atc_content','rid','gdcode','flashatt','tid','ifMessagePostReply'), 'GP');
	if(!$_G['allowmessege']) ajaxExport(array('bool' => false, 'message' => '�����ڵ��û��鲻�ܷ�����Ϣ'));
	if(($db_gdcheck & 8) && false === GdConfirm($gdcode,true)){
		ajaxExport(array('bool' => false, 'message' => '�����֤�벻��ȷ�����'));
	}
	empty($parentMid) && ajaxExport(array('bool' => false, 'message' => '�Ƿ������뷵��'));
	empty($atc_content) && $atc_content !== '0' && ajaxExport(array('bool' => false, 'message' => '�ظ����ݲ���Ϊ��'));
	$atc_content = trim(strip_tags($atc_content));
	$filterUtil = L::loadClass('filterutil', 'filter');
	$atc_content = $filterUtil->convert($atc_content);
	$messageInfo = array('create_uid' => $winduid, 'create_username' => $windid, 'title' => $windid,
		'content' => $atc_content);
	if (!($message = $messageServer->sendReply($winduid, $rid, $parentMid, $messageInfo))) {
		ajaxExport(array('bool' => false, 'message' => '�ظ�ʧ��'));
	} else {
		L::loadClass('messageupload', 'upload', false);
		if ($db_allowupload && $_G['allowupload'] && (PwUpload::getUploadNum() || $flashatt)) {
			S::gp(array('savetoalbum', 'albumid'), 'P', 2);
			$messageAtt = new messageAtt($parentMid,$rid);
			$messageAtt->setFlashAtt($flashatt, $savetoalbum, $albumid);
			$attachData = PwUpload::upload($messageAtt);
		}
	}
	if ($ifMessagePostReply) {
		$pingService = L::loadClass('ping', 'forum');
		if (($pingService->checkReplyRight($tid)) !== true) {
			ajaxExport(array('bool' => false, 'message' => '�����ܶ����ӽ��лظ�'));
		}
		$atc_content = $atc_content."\r\n\r\n[size=2][color=#a5a5a5]��������[����Ϣ][/color] [/size]";
		if ($result = $pingService->addPost($tid, $atc_content) !== true) {
			ajaxExport(array('bool' => false, 'message' => $result));
		}
	}
	ajaxExport(array('bool' => true, 'message' => '��Ϣ�ѷ���'));
} elseif ($action == 'overlook') {
	/* �������� */
	S::gp(array('rids', 'typeid','fuid'), 'GP');
	empty($rids) && ajaxExport("�Ƿ������뷵��");
	!is_array($rids) && $rids = explode(',', trim($rids, ','));
	$ignoreType = $messageServer->getReverseConst($typeid);
	switch($ignoreType){
		case 'request_friend' : $msg = getLangInfo('message','friend_add_ignore');
								$friendObj->deleteMeFromFriends($winduid,$fuid);
								break;
		case 'request_group' : $msg = getLangInfo('message','colony_add_ignore');break;
		case 'request_app' : $msg = getLangInfo('message','app_add_ignore');break;
		default:$msg = getLangInfo('message','request_ignore');break;
	}
	$messageServer->overlookRequests($winduid, $rids);
	ajaxExport($msg);
} elseif ($action == 'post') {
	S::gp(array('_usernames', 'atc_title', 'atc_content','flashatt','gdcode'));
	$usernames = $_usernames;/*specia;*/
	$atc_title = trim($atc_title);
	$atc_content = trim($atc_content);
	if(($db_gdcheck & 8) && false === GdConfirm($gdcode,true)){
		ajaxExport(array('bool' => false, 'message' => '�����֤�벻��ȷ�����'));
	}
	if(!$_G['allowmessege']){
		ajaxExport(array('bool' => false, 'message' => '�����ڵ��û��鲻�ܷ�����Ϣ'));
	}
	if ("" == $usernames) {
		ajaxExport(array('bool' => false, 'message' => '�ռ��˲���Ϊ��'));
	}
	if (in_array($windid,$usernames)) {
		ajaxExport(array('bool' => false, 'message' => '�㲻�ܸ��Լ�����Ϣ'));
	}
	if (count($usernames) > 1 && intval($_G['multiopen']) < 1 ) {
		ajaxExport(array('bool' => false, 'message' => '�㲻�ܷ��Ͷ�����Ϣ'));
	}
	if($_FILES['attachment']){
		unset($_FILES['attachment']);
	}
	if( count($_FILES) > $db_attachnum ){
		ajaxExport(array('bool' => false, 'message' => '�����ϴ�����'.$db_attachnum.'��'));
	}
	$usernames = is_array($usernames) ? $usernames : explode(",", $usernames);
	if (in_array($windid, array($usernames))) {
		unset($usernames[$windid]);
	}
	if(!($messageServer->checkUserMessageLevle('sms',1))){
		ajaxExport(array('bool' => false, 'message' => '���ѳ���ÿ�շ�����Ϣ���������Ϣ��������'));
	}
	list($bool,$message) = $messageServer->checkReceiver($usernames);
	if(!$bool){
		ajaxExport(array('bool' => $bool, 'message' => $message));
	}
	if ("" == $atc_title) {
		ajaxExport(array('bool' => false, 'message' => '���ⲻ��Ϊ��'));
	}
	if (200 < strlen($atc_title)) {
		ajaxExport(array('bool' => false, 'message' => '���ⲻ�ܳ����޶�'));
	}
	if ("" == $atc_content) {
		ajaxExport(array('bool' => false, 'message' => '���ݲ���Ϊ��'));
	}
	if( isset($_G['messagecontentsize']) && $_G['messagecontentsize'] > 0 && strlen($atc_content) > $_G['messagecontentsize']){
		ajaxExport(array('bool' => false, 'message' => '���ݳ����޶�����'.$_G['messagecontentsize'].'�ֽ�'));
	}
	$filterUtil = L::loadClass('filterutil', 'filter');
	$atc_content = $filterUtil->convert($atc_content);
	$atc_title   = $filterUtil->convert($atc_title);
	$messageInfo = array('create_uid' => $winduid, 'create_username' => $windid, 'title' => $atc_title,
		'content' => $atc_content);
	$messageService = L::loadClass("message", 'message');
	if (($messageId = $messageService->sendMessage($winduid, $usernames, $messageInfo))) {
		initJob($winduid,'doSendMessage',array('user'=>$usernames));
		define('AJAX',1);
		L::loadClass('messageupload', 'upload', false);
		if ($db_allowupload && $_G['allowupload'] && (PwUpload::getUploadNum() || $flashatt)) {
			S::gp(array('savetoalbum', 'albumid'), 'P', 2);
			$messageAtt = new messageAtt($messageId);
			$messageAtt->setFlashAtt($flashatt, $savetoalbum, $albumid);
			PwUpload::upload($messageAtt);
		}
	}
	ajaxExport(array('bool' => true, 'message' => '��Ϣ�ѷ���'));
} elseif ('agree' == $action) {
	/* ����ͬ��  */
	S::gp(array('rids', 'typeid', 'fid','cyid','check'), 'GP');
	empty($rids) && ajaxExport("�Ƿ������뷵��");
	!is_array($rids) && $rids = explode(',', trim($rids, ','));
	$fid && !is_array($fid) && $fid = array($fid);
	$agreeType = $messageServer->getReverseConst($typeid);
	switch ($agreeType) {
		case 'request_friend' :
			$return = $friendObj->argeeAddedFriends($winduid, $fid);
			/*xufazhang 2010-07-22 */
			$friendService = L::loadClass('Friend', 'friend'); /* @var $friendService PW_Friend */
			foreach($fid as $value){
			$friendService->addFriend($winduid, $value);
			$friendService->addFriend($value, $winduid);
			}
			$fid = $fid[0];
			$msg = getLangInfo('message',$return);
			break;
		case 'request_group' :
			$return = $check == 1 ? $friendObj->checkJoinColony($cyid,$fid):$friendObj->agreeJoinColony($cyid,$winduid, $windid);
			if($return == 'colony_check_fail'){
				/*��Ȩ����ˣ�ֱ�ӽ�����Ϣɾ��*/
				$messageServer->deleteMessages($winduid,$rids);
			}
			$msg = getLangInfo('message',$return);
			break;
		case 'request_app' :
			$return = $friendObj->agreeWithApp(0);
			$msg = getLangInfo('message',$return);
			break;
		default :
			break;
	}
	if (in_array($return,array('app_add_success','colony_joinsuccess','friend_add_success','colony_check_success'))) {
		$messageServer->agreeRequests($winduid, $rids);
	}else{
		$messageServer->updateRequest(array('status'=>0),$winduid, $rids[0]);
	}
	ajaxExport($msg);
} elseif ('shield' == $action) {
	/* ���ζ�����Ϣ */
	S::gp(array('rid', 'mid'), 'GP');
	(empty($rid) || empty($mid)) && ajaxExport("�Ƿ������뷵��");
	if (!($messageServer->shieldGroupMessage($winduid, $rid, $mid))) {
		ajaxExport("���ζ�����Ϣʧ��");
	}
	ajaxExport("���β����ɹ�!");
} elseif ('unshield' == $action) {
	/* �ָ�������Ϣ */
	S::gp(array('rid', 'mid'), 'GP');
	(empty($rid) || empty($mid)) && ajaxExport("�Ƿ������뷵��");
	if (!($messageServer->recoverGroupMessage($winduid, $rid, $mid))) {
		ajaxExport("�ָ�������Ϣʧ��");
	}
	ajaxExport("�ָ������ɹ�!");
} elseif ('close' == $action) {
	/* ����Ⱥ����Ϣ */
	S::gp(array('gid', 'mid'), 'GP');
	empty($gid) && ajaxExport("Ⱥ����ɾ��");
	empty($mid) && ajaxExport("�Ƿ������뷵��");
	if (!($messageServer->closeGroupMessage($winduid, $gid, $mid))) {
		ajaxExport("����Ⱥ����Ϣʧ��");
	}
	ajaxExport("����Ⱥ����Ϣ�ɹ�!");
} elseif ('open' == $action) {
	/* ����Ⱥ����Ϣ */
	S::gp(array('gid', 'mid'), 'GP');
	(empty($gid) || empty($mid)) && ajaxExport("�Ƿ������뷵��");
	if (!($messageServer->openGroupMessage($winduid, $gid, $mid))) {
		ajaxExport("����Ⱥ����Ϣʧ��");
	}
	ajaxExport("����Ⱥ����Ϣ�ɹ�!");
} elseif ('replay' == $action){


}

?>
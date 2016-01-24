<?php
!function_exists('readover') && exit('Forbidden');

/**
 * ������������Ϣ��ϵͳ֪ͨ
 * �ع�����Ϣ���� 
 * @param array $msg ��Ϣ��ʽ����:
 * 	$msg = array(
 *		'toUser'	=> 'admin', //�������û���,��Ϊ����Ⱥ��:array('admin','abc')
 *		'toUid'		=> 1,		//������uid,��Ϊ����Ⱥ��:array(1,2),���� toUser ͬʱ����ʱ����ȻʧЧ
 *		'fromUid'	=> 2,		//������UID,��fromUserͬʱ���ڲ���Ч (��ѡ,Ĭ��Ϊ'0')
 *		'fromUser'	=> 'pwtest',//�������û���,��fromUidͬʱ���ڲ���Ч(��ѡ,Ĭ��Ϊ'SYSTEM')
 *		'subject'	=> 'Test',	//��Ϣ����
 *		'content'	=> '~KO~',	//��Ϣ����
 *		'other'		=> array()	//������Ϣ����
 *	);
 * @return boolean ������Ϣ�����Ƿ����
 */
function pwSendMsg($msg) {
	global $db,$timestamp;
	if ((!$msg['toUser'] && !$msg['toUid']) || !$msg['subject'] || !$msg['content']) {
		return false;
	}
	$msg['subject'] = getLangInfo('writemsg',$msg['subject'],$msg);
	$msg['content'] = getLangInfo('writemsg',$msg['content'],$msg);
	$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
	$usernames = ($msg['toUser']) ? $msg['toUser'] : $userService->getUserNameByUserId($msg['toUid']);
	$usernames = (is_array($usernames)) ? $usernames : array($usernames);
	if(!$msg['fromUid'] || !$msg['fromUser']){
		M::sendNotice($usernames,array('title' => $msg['subject'],'content' => $msg['content']));
	}else{
		M::sendMessage($msg['fromUid'],$usernames,array('create_uid'=>$msg['fromUid'],'create_username'=>$msg['fromUser'],'title' => $msg['subject'],'content' => $msg['content']));
	}
	return true;
}

function delete_msgc($ids = null) {
	return true;
}

function send_msgc($msg,$isNotify=true) {
	global $db;
	if (!is_array($msg)) return;
	$uid = $sql = $mc_sql = array();
	$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
	foreach ($msg as $k => $v) {
		$username = $userService->getUserNameByUserId($v[0]);
		if (!$username) continue;
		M::sendNotice(
			array($username),
			array(
				'title' => $v[6],
				'content' => $v[7]
			)
		);
	}
}
?>
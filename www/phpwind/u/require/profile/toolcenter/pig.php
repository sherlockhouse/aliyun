<?php
!function_exists('readover') && exit('Forbidden');

/**
 * @name:��ͷ��
 * @type:��Ա��
 * @effect:ʹ�ú��öԷ�ͷ���Ϊ��ͷ����ƬЧ������24Сʱ�򵽶Է�ʹ�û�ԭ��Ϊֹ��
 */

S::gp(array('uid'),'GP',2);
if ($tooldb['type'] != 2){
	Showmsg('tooluse_type_error');  // �жϵ��������Ƿ����ô���
}
if (!$uid){
	Showmsg('�����ڱ�Ϊ��ͷ�Ķ���');
}

$rt = $db->get_one("SELECT MAX(time) AS tooltime FROM pw_toollog WHERE touid=".S::sqlEscape($uid)."AND filename='defend'");

if($rt && $rt['tooltime'] > $timestamp-3600*48){
	Showmsg('�û�Ա�Ѿ�ʹ���˻����,���ܶ���ʹ����ͷ��');
}

$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
$rt = $userService->get($uid);//icon
$user_a = explode('|',addslashes($rt['icon']));
if ($user_a[4]){
	Showmsg('�Ѿ���Ϊ��ͷ������ͷ��ʧЧ');
}

$db->update("UPDATE pw_usertool SET nums=nums-1 WHERE uid=".S::sqlEscape($winduid)."AND toolid=".S::sqlEscape($toolid));
if(empty($rt['icon'])){
	$userface="||0||1";
} else{
	//$userface="$user_a[0]|$user_a[1]|$user_a[2]|$user_a[3]|1";
	$user_a[4] = 1;
	$userface = implode('|',$user_a);
}

$userService->update($uid, array('icon' => $userface), array(), array('tooltime' => $timestamp));

$logdata = array(
	'type'		=>	'use',
	'descrip'	=>	'tool_18_descrip',
	'uid'		=>	$winduid,
	'username'	=>	$windid,
	'ip'		=>	$onlineip,
	'time'		=>	$timestamp,
	'toolname'	=>	$tooldb['name'],
);
writetoollog($logdata);
//* $_cache = getDatastore();
//* $_cache->delete('UID_'.$uid);
Showmsg('toolmsg_success');
?>
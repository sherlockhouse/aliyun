<?php
!function_exists('readover') && exit('Forbidden');

/**
 * @name:͸�Ӿ�
 * @type:��Ա��
 * @effect:�鿴�û�IP.
 */

S::gp(array('uid'),'GP',2);
if($tooldb['type'] != 2){
	Showmsg('tooluse_type_error');  // �жϵ��������Ƿ����ô���
}
if(!$uid){
	Showmsg('tooluse_noiper');
}
$ipdb = '';
$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
$rt = $userService->get($uid, false, true); //onlineip
$ipdb = explode('|',$rt['onlineip']);
$db->update("UPDATE pw_usertool SET nums=nums-1 WHERE uid=".S::sqlEscape($winduid)."AND toolid=".S::sqlEscape($toolid));
$logdata = array(
	'type'		=>	'use',
	'descrip'	=>	'tool_20_descrip',
	'uid'		=>	$winduid,
	'username'	=>	$windid,
	'ip'		=>	$onlineip,
	'time'		=>	$timestamp,
	'toolname'	=>	$tooldb['name'],
);
writetoollog($logdata);
Showmsg($ipdb[0]);
?>
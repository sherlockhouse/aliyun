<?php
!function_exists('readover') && exit('Forbidden');

/****

@name:������
@type:��Ա��
@effect:ʹ�ú�����Ӽ����֡�

****/

if ($tooldb['type'] != 2) {
	Showmsg('tooluse_type_error');  // �жϵ��������Ƿ����ô���
}

$condition = unserialize($tooldb['conditions']);
$lucktype = $condition['luck']['lucktype'];
$num = $newcredit = '';
$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
$rt = $userService->get($winduid, false, true); //$lucktype
$num = mt_rand($condition['luck']['range1'],$condition['luck']['range2']);
$newluck = $rt[$lucktype] + $num;


$credit->set($winduid,$lucktype,$num);
if ($num != 0) {
	$creditType = $num > 0 ? 'hack_creditluckadd'  : 'hack_creditluckdel';
	$credit->addLog($creditType, array($lucktype => $num), array(
		'uid'		=> $winduid,
		'username'	=> $windid,
		'ip'		=> $onlineip,
		'operator'	=> $windid
	));
	$credit->writeLog();
}
$db->update("UPDATE pw_usertool SET nums=nums-1 WHERE uid=".S::sqlEscape($winduid)."AND toolid=".S::sqlEscape($toolid));
$logdata = array(
	'type'		=>	'use',
	'descrip'	=>	'tool_15_descrip',
	'uid'		=>	$winduid,
	'username'	=>	$windid,
	'ip'		=>	$onlineip,
	'time'		=>	$timestamp,
	'toolname'	=>	$tooldb['name'],
	'newluck'	=>	$newluck,
);

writetoollog($logdata);
$msg = '';
if ($num > 0) {
	$msg = 'ף�������'.$num.'��'.pwCreditNames($lucktype);
} elseif ($num < 0) {
	$msg = '���ң�����'.pwCreditNames($lucktype).'���۳�'.abs($num).'��';
} elseif ($num == 0) {
	$msg = '����������'.pwCreditNames($lucktype).'û�з����仯';
}
Showmsg($msg);
?>
<?php
!function_exists('readover') && exit('Forbidden');

/**
 * @name:���㿨
 * @type:��Ա��
 * @effect:�ɽ��������и�������
 */

if ($tooldb['type'] != 2) {
	Showmsg('tooluse_type_error');  // �жϵ��������Ƿ����ô���
}
$updateMemberData[$winduid] = array();
foreach ($usercreditdb as $key => $value) {
	if (is_numeric($value) && $value<0) {
		$updateMemberData[$winduid][$key] = 0;
	}
}
unset($usercreditdb,$key,$value);

if (!empty($updateMemberData[$winduid])) {
	$credit->runsql($updateMemberData,false);
	$db->update("UPDATE pw_usertool SET nums=nums-1 WHERE uid=".S::sqlEscape($winduid)."AND toolid=".S::sqlEscape($toolid));
	$logdata=array(
		'type'		=>	'use',
		'nums'		=>	'',
		'money'		=>	'',
		'descrip'	=>	'tool_2_descrip',
		'uid'		=>	$winduid,
		'username'	=>	$windid,
		'ip'		=>	$onlineip,
		'time'		=>	$timestamp,
		'toolname'	=>	$tooldb['name'],
	);
	writetoollog($logdata);

	//* $_cache = getDatastore();
	//* $_cache->delete('UID_'.$winduid);
	perf::gatherInfo('changeMembersWithUserIds', array('uid'=>$winduid));
	Showmsg('toolmsg_2_success');
} else{
	Showmsg('toolmsg_2_failed');
}
?>
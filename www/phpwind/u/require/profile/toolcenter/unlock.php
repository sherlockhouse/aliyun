<?php
!function_exists('readover') && exit('Forbidden');

/****

@name:�����������
@type:������
@effect:���Խ���Լ���������������������Ա���Իظ�������

****/

if($tooldb['type']!=1){
	Showmsg('tooluse_type_error');  // �жϵ��������Ƿ����ô���
}
if($tpcdb['authorid'] != $winduid){
	Showmsg('tool_authorlimit');
}
//$db->update("UPDATE pw_threads SET locked='0',toolinfo=".S::sqlEscape($tooldb['name'],false)."WHERE tid=".S::sqlEscape($tid));
pwQuery::update('pw_threads', 'tid=:tid', array($tid), array('locked'=>0, 'toolinfo'=>$tooldb['name']));
$db->update("UPDATE pw_usertool SET nums=nums-1 WHERE uid=".S::sqlEscape($winduid)."AND toolid=".S::sqlEscape($toolid));
$logdata=array(
	'type'		=>	'use',
	'descrip'	=>	'tool_12_descrip',
	'uid'		=>	$winduid,
	'username'	=>	$windid,
	'ip'		=>	$onlineip,
	'time'		=>	$timestamp,
	'toolname'	=>	$tooldb['name'],
	'subject'	=>	substrs($tpcdb['subject'],15),
	'tid'		=>	$tid,
);
writetoollog($logdata);
Showmsg('toolmsg_success');
?>
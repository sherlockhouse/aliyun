<?php
!function_exists('readover') && exit('Forbidden');

/*****

@name:ʱ�տ�
@type:������
@effect:������ʹ�ã������ӷ�����12Сʱ��,ʹ��12Сʱ�ڲ�����

****/

if($tooldb['type']!=1){
	Showmsg('tooluse_type_error');  // �жϵ��������Ƿ����ô���
}
$uptime = 43200;
//$db->update("UPDATE pw_threads SET lastpost=lastpost+$uptime,toolinfo=".S::sqlEscape($tooldb['name'],false)."WHERE tid=".S::sqlEscape($tid));
$db->update(pwQuery::buildClause("UPDATE :pw_table SET lastpost=lastpost+$uptime,toolinfo=:toolinfo WHERE tid=:tid", array('pw_threads', $tooldb['name'], $tid)));
# memcache refresh
$fid = $db->get_value("SELECT fid FROM pw_threads WHERE tid=".S::sqlEscape($tid));
//* $threadList = L::loadClass("threadlist", 'forum');
//* $threadList->updateThreadIdsByForumId($fid,$tid,$uptime);
//* $threads = L::loadClass('Threads', 'forum');
//* $threads->delThreads($tid);
Perf::gatherInfo('changeThreadWithForumIds', array('fid'=>$fid));
require_once (R_P . 'require/updateforum.php');
delfcache($fid, $db_fcachenum);

$db->update("UPDATE pw_usertool SET nums=nums-1 WHERE uid=".S::sqlEscape($winduid)."AND toolid=".S::sqlEscape($toolid));
$logdata=array(
	'type'		=>	'use',
	'descrip'	=>	'tool_22_descrip',
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
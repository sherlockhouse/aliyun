<?php 
!defined('A_P') && exit('Forbidden');
/* ������� */
define('AJAX','1');
S::gp(array('id'));
if (!$winduid) Showmsg('����δ��¼');
$id = (int) $id;
if ($id < 1 || !$db_md_ifapply)  Showmsg('�Ƿ�����'); 
$medalService = L::loadClass('MedalService', 'medal'); /* @var $medalService PW_MedalService */
$medalInfo = $medalService->getMedal($id);
if (!in_array($winddb['memberid'], (array)$medalInfo['allow_group']) && $medalInfo['allow_group']) Showmsg('�������û�����ʱ�޷������ѫ��'); 
$result = $medalService->applyMedal($winduid, $id);
if (is_array($result)) {
	Showmsg($result[1]);
} else {
	Showmsg('����ɹ���');
}
?>  
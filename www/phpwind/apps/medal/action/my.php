<?php 
!defined('A_P') && exit('Forbidden');
if (!$space =& $newSpace->getInfo()) {
	Showmsg('�����ʵĿռ䲻����!');
}

/* ǰ̨ѫ��ҳ�� */
$medalService = L::loadClass('MedalService', 'medal'); /* @var $medalService PW_MedalService */
if ($a == 'all') {
	$userApply = $medalService->getUserApplys($winduid);
	$awardMedal = $medalService->getAwardMedalUsers(array(),1,10);//��̬�����б�
	$medalAll  = $medalService->getAllMedals();
}
$medalTemp = $medalService->getUserMedals($winduid,'all'); //��ȡ��Ա�Ѿ�ӵ�е�ѫ��
$medalCount = count($medalTemp); //����
$userMedalId = $medal = array();
foreach ($medalTemp as $v) {
	if ($v['is_have'] == 1) {
		$userMedal[$v['medal_id']] = $v;
	}
	$v['have_apply'] = $medalService->getApplyByUidAndMedalId($winduid,$v['medal_id']);
	$medal[$v['medal_id']] = $v;
}

$userMedalCount = count($userMedal); //������
if ($a == 'my') $medal = $userMedal;

require_once PrintEot('m_medal'); 
pwOutPut();

?>
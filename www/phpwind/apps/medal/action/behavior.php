<?php 
!defined('A_P') && exit('Forbidden');
/* �û���Ϊ */
define('AJAX', 1);
S::gp(array('id', 'atype'));      
$id = (int) $id;      
if (!$winduid || $id < 1 || !$db_md_ifopen) exit;
//��ȡѫ����Ϣ���û��Ѿ���ȡ��ѫ����Ϣ���Ѿ������ѫ����Ϣ
$medalService = L::loadClass('MedalService', 'medal'); /* @var $medalService PW_MedalService */
$medalInfo = $medalService->getMedal($id);
$userMedal = $medalService->getUserMedals($winduid); //��ȡ��Ա��ѫ��
$isUserApply = $medalService->getApplyByUidAndMedalId($winduid,$medalInfo['medal_id']); //�û��Ѿ�����
$userMedalInfo = $userMedalIdArr = array();
if (is_array($userMedal)) {
	foreach ($userMedal as $v) {
		$userMedalIdArr[] = $v['medal_id']; //����û�ѫ��ID���ж��û��Ƿ�����ɹ�
		$userMedalInfo[$v['medal_id']] = $v;
	}
}
//HTML��װ
if ($medalInfo['type'] == 2) { //�ֶ�����
	if ($db_md_ifapply) {
		$ifApply = (in_array($groupid, (array)$medalInfo['allow_group']) || !$medalInfo['allow_group']) ? '<a href="javascript:;" onclick="sendmsg(\'apps.php?q=medal&a=apply&id='.$id.'\',\'\',null,function(){setTimeout(function(){window.location.reload();},10)})" class="medal_pop_btn fr" >����ѫ��</a>' : '�������û����޷���ȡ��ѫ�£��뾡��������';
	} else {
		$ifApply = '';
	}
	
	$otherHtml = ($isUserApply) ? '<a href="javascript:" class="medal_pop_bt fr">������</a>' : $ifApply;
	if (in_array($id, $userMedalIdArr)) {
		$awardMedal = $medalService->getAwardMedalByUidAndMedalId($winduid, $id); //
		if ($medalInfo['confine'] == 0) {
			$otherHtml = '<p class="mb5">���ʱ�䣺' . date('Y-m-d', $awardMedal['timestamp']) . '</p><p>��Ч�ڣ�����</p>';
		} else {
			$otherHtml = '<p class="mb5">���ʱ�䣺' . date('Y-m-d', $awardMedal['timestamp']) . '</p><p><span class="mr20">��Ч�ڣ�' . $medalInfo['confine'] . ' ��</span>'. '����ʱ�䣺' . date('Y-m-d', $awardMedal['deadline']) . '</p>';
		}
	}
} else { //�Զ�����
	$attention = $notice = $nowhave = '';
	if (in_array($medalInfo['associate'], array('continue_login', 'continue_post', 'continue_thread_post'))) {
		//��ȡ�û���Ϊ��Ϣ
		$behaviorService = L::loadClass('behaviorService', 'user'); /* @var $medalService PW_MedalService */ 
		$behavior = $behaviorService->getBehaviorStatistic($winduid, $medalInfo['associate']);
		$num = ($behavior) ? $behavior['num'] : 0;
		$needNum = $medalInfo['confine'] - $num;
		if ($medalInfo['associate'] == 'continue_login') {
			$attention = '<p class="gray">ע�⣺1�첻��¼�������������1</p>';
			$notice = ($needNum > 0) ? '�㻹��������¼'.$needNum.'��' : '���µ�¼һ�μ��ɻ�ô�ѫ��';
			$notice = $notice . '������������'.$num.'��';
			$nowhave = '������������¼������'.$num.'��';
		} elseif ($medalInfo['associate'] == 'continue_thread_post') {
			$attention = '<p class="gray">ע�⣺1�첻��¼������������¼�������1</p>';
			$notice = ($needNum > 0) ? '�㻹������������'.$needNum.'��' : '�ٷ�1���������ɻ�ô�ѫ��';
			$notice = $notice . '������������'.$num.'��';
			$nowhave = '��������������������'.$num.'��';
		} elseif ($medalInfo['associate'] == 'continue_post') {
			$attention = '<p class="gray">ע�⣺1�첻�����������������1</p>';
			$notice = ($needNum > 0) ? '�㻹����������'.$needNum.'��' : '�ٷ�1�����ɻ�ô�ѫ��';
			$notice = $notice . '������������'.$num.'��';
			$nowhave = '��������������������'.$num.'��';
		}
	} else {
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService*/
		$userInfo = $userService->get($winduid, true, true, true);
		if ($medalInfo['associate'] == 'post') {
			$needNum = $medalInfo['confine'] -$userInfo['postnum'];
			$notice = ($needNum > 0) ? '�㻹�跢'.$needNum.'������' : '�ٷ�1�����ɻ�ô�ѫ��';
			$notice = $notice . '������������'.$userInfo['postnum'].'��';
		} elseif ($medalInfo['associate'] == 'fans') {
			$needNum = $medalInfo['confine'] -$userInfo['fans'];
			$notice = ($needNum > 0) ? '�㻹������'.$needNum.'����˿' : '������1����˿���ɻ�ô�ѫ��';
			$notice = $notice . '�����з�˿����'.$userInfo['fans'].'��';
		} elseif ($medalInfo['associate'] == 'shafa') {
			$needNum = $medalInfo['confine'] -$userInfo['shafa'];
			$notice = ($needNum > 0) ? '�㻹����'.$needNum.'��ɳ��' : '����1��ɳ�����ɻ�ô�ѫ��';
			$notice = $notice . '������ɳ������'.$userInfo['shafa'].'��';
		}
	}
	if (in_array($id, $userMedalIdArr)) {
		$notice = '<p class="medal_pop_tips mb5">'.'��ϲ���'.$medalInfo['name'].'ѫ��'.$nowhave.'</p>';
	} else {
		//û�����û���Ȩ�޵����
		if ($medalInfo['allow_group'] && !in_array($groupid, (array)$medalInfo['allow_group'])) {
			$notice = '<p class="gray">�������û����޷���ȡ��ѫ�£��뾡��������</p>';
		}
		$notice =  '<p class="medal_pop_tips mb5">'.$notice.'</p>';
	}
	$otherHtml = $notice . $attention;
}
$html = '<div class="medal_pop">
<span class="fl"><span class="medal_pop_angle"></span></span>
<div class="medal_pop_cont">
<div class="medal_pop_top">
<p class="mb10"><img src="'.$medalInfo['smallimage'].'" width="30" height="30" id="medal_img" align="absmiddle" class="mr10" /><span class="b s5 mr10">'.$medalInfo['name'].'</span><span class="s6">('.$typeArr[$medalInfo['type']].')</span></p>
<p class="s5">'.$medalInfo['descrip'].'</p>
</div>
<div class="medal_pop_bot cc" >
'.$otherHtml.'
</div>
</div>
</div>';
echo "success\t".$html;
ajax_footer();

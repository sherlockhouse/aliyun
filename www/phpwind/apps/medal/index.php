<?php
!defined('A_P') && exit('Forbidden');
if (!$db_md_ifopen)  Showmsg('ѫ�¹���δ����'); 
/* ѫ��ǰ̨��ʾ */
S::gp(array('a')); 
!$winduid && Showmsg('not_login');
(!$a || !in_array($a, array('apply', 'my', 'all', 'behavior'))) && $a = 'all';
$basename =  'apps.php?q=' . $q;
$current[$a] = 'class="current"'; 
$typeArr = array('ϵͳ����', '�Զ�����', '�ֶ�����');
if ($a == 'my' || $a == 'all') {
	require_once S::escapePath($appEntryBasePath . 'action/my.php'); //�ҵ�ѫ��
} elseif ($a == 'apply') {
	require_once S::escapePath($appEntryBasePath . 'action/apply.php'); //�ҵ�ѫ��
} elseif ($a == 'behavior') {
	require_once S::escapePath($appEntryBasePath . 'action/behavior.php'); //�û���ΪAJAX�ύ
}    


 
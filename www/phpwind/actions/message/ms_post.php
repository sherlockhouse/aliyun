<?php
!function_exists('readover') && exit('Forbidden');
S::gp(array('step','username'));
$username = urldecode($username);
if(!$_G['allowmessege']) Showmsg ( '�����ڵ��û��鲻�ܷ�����Ϣ' );
//if(!($messageServer->checkUserMessageLevle('sms',1))) Showmsg ( '���ѳ���ÿ�շ�����Ϣ���������Ϣ��������' );
$normalUrl = $baseUrl."?type=post";
include_once 'ms_header.php';

$uploadfiletype = ($db_uploadfiletype) ? unserialize($db_uploadfiletype) : array();
$attachAllow = pwJsonEncode($uploadfiletype);
$imageAllow = pwJsonEncode(getAllowKeysFromArray($uploadfiletype, array('jpg','jpeg','gif','png','bmp')));

require messageEot('post');
pwOutPut();
?>
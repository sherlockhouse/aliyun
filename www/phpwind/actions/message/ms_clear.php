<?php
!defined('P_W') && exit('Forbidden');
if(isset($_GET['ajax']) && $_GET['ajax'] == 1){
	define('AJAX','1');
}
empty($subtype) && $subtype = 'shield';
$normalUrl = $baseUrl."?type=clear";
!empty($winduid) && $userId = $winduid;
S::gp(array('action'), 'GP');
if(empty($action)){
	if($_POST['step'] == 2){
		PostCheck();	
		S::gp(array('clear'), 'GP');
		if(!$clear){
			refreshto($normalUrl,'����ûѡ��Ҫ��յ�����');
		}

		$messageServer = L::loadClass('message', 'message');
		$messageServer->clearMessages($userId,$clear);
		//Showmsg("operate_success");
		
	}

}
!defined('AJAX') && include_once R_P.'actions/message/ms_header.php';

$numbers =$messageServer->statisticUsersNumbers(array($winduid));
$totalMessage = isset($numbers[$winduid]) ? $numbers[$winduid] : 0;
$tip = '��Ŀǰ����Ϣ'.$totalMessage.'��';
$tip .= $_G['maxsendmsg'] ? ',ÿ�տɷ�����Ϣ'.$_G['maxsendmsg'].'��' : ',ÿ�տɷ�����Ϣ20��' ;
$_G['maxmsg'] && $tip .= $percentTip;

require messageEot('clear');
if (defined('AJAX')) {
	ajax_footer();
} else {
	pwOutPut();
}
?>
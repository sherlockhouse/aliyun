<?php
define('SCR', 'job');
require_once ('global.php');

S::gp(array(
	'action'
));
$whiteActions = array(
	'attach', //�����ϴ�
	'image',//ͼƬ
	'modifyattach',//�޸ĸ���
);
if (in_array($action, $whiteActions)) {
	require S::escapePath(R_P . 'actions/pweditor/' . $action . '.php');
} else {
	Showmsg('undefined_action');
}
?>
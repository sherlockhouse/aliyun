<?php
!defined('P_W') && exit('Forbidden');
S::gp(array('action'));
$columnService = C::loadClass('columnservice');
/* @var $columnService PW_ColumnService */
if (empty($action)) {
	$result = $columnService->getAllOrderColumns();
} elseif ($action == 'add') {
	define('AJAX', 1);
	$options = $columnService->getColumnOptions();
	ifcheck(0, 'allowoffer');
	$_action = "addsubmit";
} elseif ($action == 'addsubmit') {
	define('AJAX', 1);
	S::gp(array('name', 'parentId', 'allowoffer', 'order', 'seotitle', 'seodesc', 'seokeywords'));
	if (empty($name)) Showmsg('��Ŀ���Ʋ���Ϊ��');
	if (strlen($name) > 20) Showmsg('��Ŀ���Ƴ��Ȳ��ܳ���20���ֽ�');
	//if ($columnService->getColumnByName($name)) Showmsg('��Ŀ�����Ѿ�����');
	$datas = array(array($parentId, $name, (int)$order, $allowoffer, $seotitle, $seodesc, $seokeywords));
	if (!$columnService->insertColumns($datas)) Showmsg('�����Ŀʧ��');
	Showmsg('ajaxma_success');
} elseif ($action == 'edit') {
	define('AJAX', 1);
	S::gp(array('cid'));
	if (empty($cid)) Showmsg('�Ƿ������뷵��');
	$options = $columnService->getColumnOptions($cid);
	$column = $columnService->findColumnById($cid);
	ifcheck($column['allowoffer'], 'allowoffer');
	$_action = "editsubmit";
} elseif ($action == 'editsubmit') {
	define('AJAX', 1);
	S::gp(array('cid', 'name', 'parentId', 'allowoffer', 'order', 'seotitle', 'seodesc', 'seokeywords'));
	if (empty($cid)) Showmsg('�Ƿ������뷵��');
	if (empty($name)) Showmsg('��Ŀ���Ʋ���Ϊ��');
	if (strlen($name) > 20) Showmsg('��Ŀ���Ƴ��Ȳ��ܳ���20���ֽ�');
	if (!is_numeric($order)) Showmsg('�������Ϊ��������');
	$data = array($parentId, $name, $order, $allowoffer, $seotitle, $seodesc, $seokeywords);
	if (!$columnService->updateColumn($cid, $data)) Showmsg('�༭��Ŀʧ��');
	Showmsg('ajaxma_success');
} elseif ($action == 'delete') {
	S::gp(array('cid'));
	if (empty($cid)) Showmsg('�Ƿ������뷵��', $basename);
	if ($articles = $columnService->getArticlesByColumeId($cid)) Showmsg('����Ŀ���Ѵ����������ݣ���ɾ����Ŀ�����£����������վ����ɾ����Ŀ');
	if ($columns = $columnService->getSubColumnsById($cid)) Showmsg('����Ŀ��������Ŀ������ɾ�������Ƴ�����Ŀ����ɾ��');
	if (!$columnService->deleteColumn($cid)) Showmsg('ɾ����Ŀʧ��');
	Showmsg('ɾ����Ŀ�����ɹ�!', $basename);
} elseif ($action == 'editOrder') {
	S::gp(array('orders'));
	if (!$columnService->updateColumnOrders($orders)) Showmsg('����ʧ��');
	Showmsg('�����ɹ�!', $basename);
}

/**
 * @param unknown_type $level
 */
function getColumnLevelHtml($level,$cid) {
	global $columnService;
	if ($level == 0) {
		$subcolumns = $columnService->getSubColumnsById($cid);
		if (empty($subcolumns)) return '<i class="expand expand_d"></i>';
		return '<i id="column_'.$cid.'" class="expand expand_b" onclick="closeAllSubColumns('.$cid.')"></i>';
	} else {
		$html .= '';
		for ($i = 1; $i < $level; $i++) {
			$html .= '<i id="" class="lower lower_a"></i>';
		}
		$html .= '<i id="" class="lower"></i>';
	}
	return $html;
}

include PrintMode('column');
if (defined('AJAX')) ajax_footer();
exit();
?>
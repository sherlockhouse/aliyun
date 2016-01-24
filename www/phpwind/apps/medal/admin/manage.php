<?php
!function_exists('adminmsg') && exit('Forbidden');
!$action && $action = 'medal';

$medalService = L::loadClass('MedalService', 'medal'); /* @var $medalService PW_MedalService */
$medalCondition = $medalService->getAutoMedalType(); //ѫ���Զ��䷢����
$actionCurrent[$action] = 'class="current"';
$typeArr  = array('ϵͳ����', '�Զ�����', '�ֶ�����');
$issueWay = array('ϵͳ�Զ�����', '�û�����ͨ��', '����Ա����'); //ѫ�·���;��
S::gp(array('type'));

/* ѫ�¹����б���ӡ��༭��ɾ������������ */
if ($action == 'medal') {
	(!in_array($type, array('list', 'add', 'adddo', 'edit', 'editdo', 'del', 'batch','img'))) && $type = 'list';
	
	/* ѫ�¹���-�б�ҳ�� */
	if ($type == 'list') {
		$medal = $medalService->getAllMedals();
		require_once PrintApp('admin_medal');
	
	/* ѫ�¹���-ѫ����� */
	} elseif ($type == 'add') {
		$creategroup = getGroup(); //��ȡ�û���
		$openMedal   = $medalService->getAllOpenAutoMedals(); //��ȡ���п�����ѫ��
		$openMedal   = getMedalJson($openMedal);
		require_once PrintApp('admin_medal_add');
		
	/* ѫ�¹���-ѫ����Ӳ��� */
	} elseif ($type == 'adddo') {
		S::gp(array('name', 'image', 'descrip', 'tp', 'day', 'associate', 'confine', 'allow_group'));
		if ($name == '') adminmsg('ѫ�����Ʋ���Ϊ��',"$basename&type=add");
		if ($image == '') adminmsg('medal_image_is_not_select',"$basename&type=add");
		if ($descrip == '') adminmsg('ѫ����������Ϊ��',"$basename&type=add");
		if (!$allow_group) $allow_group = array(); //�����Ա��Ϊ�գ��������
		if ($tp == 2) $confine = $day; //�ֶ����
		if ($confine < 0) $confine = 0; //����С��0
		$info = array(
			'name'        => $name,
			'descrip'     => $descrip,
			'type'        => (int) $tp,
			'image'       => $image,
			'associate'   => $associate,
			'confine'     => (int) $confine,
			'allow_group' => $allow_group
		);
		$result = $medalService->addMedal($info);
		if (is_array($result)) {
			adminmsg($result[1],"$basename&type=add");
		} else {
			adminmsg('operate_success',"$basename");
		}
		
	/* ѫ�¹���-ѫ�±༭ */	
	} elseif ($type == 'edit') {
		S::gp(array('id'));
		$id = (int) $id;
		if ($id < 1) adminmsg('operate_error',"$basename");
		$medal       = $medalService->getMedal($id); //��ȡmedal��Ϣ
		if ($medal['type'] == 0) adminmsg('medal_system_is_not_edit',"$basename");
		$creategroup = getGroup($medal['allow_group']); //��ȡ�û���
		$openMedal   = $medalService->getAllOpenAutoMedals(); //��ȡ���п�����ѫ��
		$openMedal   = getMedalJson($openMedal);
		require_once PrintApp('admin_medal_add');
	
	/* ѫ�¹���-ѫ�±༭���� */
	} elseif ($type == 'editdo') {
		S::gp(array('name', 'image', 'descrip', 'day', 'confine', 'allow_group', 'id'));
		$id = (int) $id;
		if ($id < 1) adminmsg('operate_error',"$basename&type=add");
		if ($name == '') adminmsg('ѫ�����Ʋ���Ϊ��',"$basename&type=add");
		if ($image == '') adminmsg('medal_image_is_not_select',"$basename&type=edit&id=" . $id);
		if ($descrip == '') adminmsg('ѫ����������Ϊ��',"$basename&type=add");
		$medal = $medalService->getMedal($id); //��ȡmedal��Ϣ
		if ($medal['type'] == 0) adminmsg('medal_system_is_not_edit',"$basename");
		if ($medal['type'] == 2) $confine = $day; //�ֶ����
		if (!$allow_group) $allow_group = array();
		if ($confine < 0) $confine = 0; //����С��0
		$info = array(
			'name'        => $name,
			'descrip'     => $descrip,
			'image'       => $image,
			'confine'     => (int) $confine,
			'allow_group' => $allow_group
		);
		$result = $medalService->updateMedal($id, $info);
		if (is_array($result)) { //��ϵͳ�ĺ����ж�
			adminmsg($result[1],"$basename&type=edit&id=" . $id);
		} else {
			adminmsg('operate_success',"$basename");
		}
		
	/* ѫ�¹���-ѫ��ɾ������ */
	} elseif ($type == 'del') {
		S::gp(array('id'));
		$id = (int) $id;
		if ($id < 1) adminmsg('operate_error',"$basename");
		$medal = $medalService->getMedal($id); //��ȡmedal��Ϣ
		if ($medal['type'] == 0) adminmsg('medal_system_is_not_del',"$basename");
		$result = $medalService->deleteMedal($id);
		if (is_array($result)) {
			adminmsg($result[1],"$basename");
		} else {
			adminmsg('operate_success',"$basename");
		}
	
	/* ѫ�¹���-ѫ���������� */
	} elseif ($type == 'batch') {
		S::gp(array('name', 'sortorder', 'descrip','selid'));
		foreach ($name as $k => $v) {
			if ($k < 1) continue;
			$info = array(
				'name'      => $name[$k],
				'sortorder' => $sortorder[$k],
				'descrip'   => $descrip[$k],
				'is_open'   => $selid[$k]
			);
			$medalService->updateMedal((int)$k, $info);
		}
		adminmsg('operate_success',"$basename");
		
	/* ѫ�¹���-ѫ��ͼƬAJAX��ȡ */
	} elseif ($type == 'img') { //ͼƬ
		define('AJAX', 1);
		//��ȡͼƬ
		$medalImg = getMedalImgList();
		require_once PrintApp('admin_medal_add');
		ajax_footer();
	}
	
/* ѫ�»�Ա */
} elseif ($action == 'user') {
	(!in_array($type, array('list', 'del','deldo', 'batchdel', 'batch', 'add', 'adddo'))) && $type = 'list';
		
	/* ѫ�»�Ա�б� */
	if ($type == 'list') {
		S::gp(array('page','searchName', 'searchUsername', 'searchType'));
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService*/
		//ѫ�·�ҳ�б�
		$condition = array(); //װ����������
		if ($searchName) $condition['medal_id'] = (int) $searchName;
		if ($searchUsername) {
			$user = $userService->getByUserName($searchUsername);
			$condition['uid'] = ($user) ? $user['uid'] : -1;
		}
		if (is_numeric($searchType)) $condition['type'] = (int) $searchType;

		(!is_numeric($page) || $page<1) && $page = 1;
		list($medalAward, $medalAwardCount) = $medalService->getAwardMedalUsers($condition,$page,20);
		$pages = numofpage($medalAwardCount, $page,ceil($medalAwardCount/20),"$basename&action=user&searchName=" . $searchName . "&searchUsername=" . $searchUsername .'&searchType=' . $searchType . '&');

		//ѫ����Ϣ
		$openMedal = $medalService->getAllMedals(); //��ȡ���е�ѫ��
		require_once PrintApp('admin_user');
	
	/* �ֶ���ӻ�Ա-ajax������ģʽ */
	} elseif ($type == 'add') {
		define('AJAX', 1);
		$openManualMedals =  $medalService->getAllOpenManualMedals();//��ȡ�ֶ�ѫ��
		require_once PrintApp('admin_user');
		ajax_footer();
	
	/* �ֶ���ӻ�Ա���� */
	} elseif ($type == 'adddo') {
		define('AJAX', 1);
		S::gp(array('username','medal_id', 'descrip'));
		$medal_id = (int) $medal_id;
		if ($medal_id < 1) adminmsg('operate_error', "$basename&action=user");
		if (!$username) adminmsg('medal_username_error', "$basename&action=user");
		
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService*/
		$user = $userService->getByUserName($username);
		if (!$user) adminmsg('medal_username_error', "$basename&action=user");
		if ($appliInfo = $medalService->getApplyByUidAndMedalId($user['uid'], $medal_id)) {
			$result = $medalService->adoptApplyMedal($appliInfo['apply_id']); //���ͨ�����Ѿ�����˱��еĻ�
		} else {
			$result = $medalService->awardMedal($user['uid'], $medal_id, 0, array(),$descrip);//�䷢ѫ��
		}
		if (is_array($result)) {
			adminmsg($result[1], "$basename&action=user");
		} else {
			//������Ϣ
			adminmsg('medal_ajax_operate_success');
		}
		ajax_footer();
	
	/* ɾ������ */	
	} elseif ($type == 'del') {
		define('AJAX', 1);
		S::gp(array('id'),'',1);
		$id = (int) $id;
		if ($id < 1) adminmsg('operate_error', "$basename&action=user");
		require_once PrintApp('admin_user');
		ajax_footer();
		
	/* ɾ������ */		
	} elseif ($type == 'deldo') {
		define('AJAX', 1);
		S::gp(array('id','descrip'));
		$id = (int) $id;
		$descrip = substrs($descrip, 200);
		if ($id < 1) adminmsg('operate_error', "$basename&action=user");
		$awardMedalInfo = $medalService->getAwardMedalById($id);
		$medal       = $medalService->getMedal($awardMedalInfo['medal_id']); //��ȡmedal��Ϣ
		if ($medal['type'] == 1) adminmsg('medal_error');
		$result = $medalService->recoverMedal($id,$descrip);//ժ������
		if (is_array($result)) {
			adminmsg($result[1], "$basename&action=user");
		} else {
			adminmsg('medal_ajax_operate_success');
		}
		ajax_footer();
	
	/* ����������ʾҳ�� */
	} elseif ($type == 'batchdel') {
		define('AJAX', 1);
		S::gp(array('id'));
		if ($id == '') adminmsg('medal_is_not_select', "$basename&action=user");
		require_once PrintApp('admin_user');
		ajax_footer();
		
	/* �������� */
	} elseif ($type == 'batch') {
		define('AJAX', 1);
		S::gp(array( 'id','descrip'));
		if ($id == '') adminmsg('medal_is_not_select',"$basename&action=user");
		$id = explode('|', $id);
		$uidArr = array();
		foreach ($id as $v) {
			$v = (int) $v;
			if ($v < 1) continue;
			$awardMedalInfo = $medalService->getAwardMedalById($v);
			$uidArr[]       = $awardMedalInfo['uid']; //��Ϣ���͵Ķ���
			$medalService->recoverMedal($v,'����ɾ��ѫ��');
		}
		adminmsg('medal_ajax_operate_success');
		ajax_footer();
	}
	
/* ѫ����� */
} elseif ($action == 'verify') {
	(!in_array($type, array('list', 'pass', 'batch'))) && $type = 'list';
	
	/* ����б� */
	if ($type == 'list') {
		S::gp(array('searchName', 'searchUsername', 'page'));
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService*/
		//��������
		$condtion = array(); //װ����������
		if ($searchName) $condtion['medal_id'] = (int) $searchName;
		if ($searchUsername) {
			$user = $userService->getByUserName($searchUsername);
			$condtion['uid'] = ($user) ? $user['uid'] : 0;
		}
		(!is_numeric($page) || $page<1) && $page = 1;
		list($medalApply, $medalApplyCount) = $medalService->getApplyMedalUsers($condtion,$page,20);
		$pages = numofpage($medalApplyCount, $page,ceil($medalApplyCount/20),"$basename&action=verify&searchName=" . $searchName . "&searchUsername=" . $searchUsername .'&');
		//ѫ����Ϣ
		$openMedal = $medalService->getAllOpenManualMedals(); //��ȡ���п�����ѫ��
		require_once PrintApp('admin_verify');
	
	/* ���ͨ��-����-��ͨ�� */
	} elseif ($type == 'pass') {
		S::gp(array('val', 'applyid'),'',2);
		if ($applyid < 1) adminmsg('operate_error',"$basename&action=verify");
		$result = ($val == 1) ? $medalService->adoptApplyMedal($applyid) : $medalService->refuseApplyMedal($applyid); 
		if (!$result) adminmsg('operate_error',"$basename&action=verify");
		adminmsg('operate_success',"$basename&action=verify");
		
	/* �������� ����ͨ�����߲�ͨ�� */
	} elseif ($type == 'batch') {
		S::gp(array('passid', 'selid'));
		if (!$selid) adminmsg('medal_is_not_select',"$basename&action=verify");
		$passid = (int) $passid;
		$functionName = ($passid == 1) ? 'adoptApplyMedal' : 'refuseApplyMedal';
		foreach ($selid as $v) {
			$v = (int) $v;
			if ($v < 1) continue;
			$medalService->$functionName($v);
		}
		adminmsg('operate_success',"$basename&action=verify");
	}
	
	
/* ѫ������ */
} elseif ($action == 'set') {
	S::gp(array('step'), 'P');
	if(!$step){
		ifcheck($db_md_ifopen,'ifopen');
		ifcheck($db_md_ifapply,'ifapply');
		require_once PrintApp('admin_set');
	} else {
		S::gp(array('config'),'P');
		foreach($config as $key=>$value){
			setConfig($key, $value);
		}
		updatecache_c();
		adminmsg('operate_success',"$basename&action=set");
	}
}

/**
 * JSON����
 * 
 * @return Ambigous <multitype:, string>
 */
function getMedalJson($medal) {
	$openMedalTemp = array();
	foreach ($medal as $v) { 
		$openMedalTemp[] = $v;
	}
	return pwJsonEncode($openMedalTemp);
}

/**
 * ��ȡѫ���ļ����µ�ѫ��ͼƬ
 * 
 * @return Ambigous <multitype:, string>
 */
function getMedalImgList() {
	$medalImg = array();
	global $imgdir;
	if ($fp = opendir("$imgdir/medal/big")) { //
		while (($file = readdir($fp))) {
			if (!is_dir($file) && in_array(substr($file, -4), array('.gif', '.png'))) {
				$imgId = substr($file, 0, -4);
				$medalImg[$imgId] = $file;
			}
		}
		closedir($fp);
	}
	ksort($medalImg);
	return $medalImg;
}

/**
 * ��ȡ��Ա����Ϣ
 * 
 * @param $allow_group �༭��ʱ��ѡ�е�������
 */
function getGroup($allow_group = array()) {
	$creategroup = ''; $num = 0;
	global $ltitle;
	foreach ($ltitle as $key => $value) {
		if ($key != 1 && $key != 2 && $key !='6' && $key !='7' && $key !='3') {
			$num++;
			$htm_tr = $num % 4 == 0 ? '' : '';
			$g_checked = in_array($key,$allow_group) ? 'checked' : '';
			$creategroup .= "<li><input type=\"checkbox\" name=\"allow_group[]\" value=\"$key\" $g_checked>$value</li>$htm_tr";
		}
	}
	$creategroup && $creategroup = "<ul class=\"list_A list_120 cc\">$creategroup</ul>";	
	return $creategroup;
}



?>
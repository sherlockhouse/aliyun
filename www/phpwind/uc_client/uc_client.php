<?php

// uc_client ���ĸ�Ŀ¼
define('UC_CLIENT_ROOT', dirname(__FILE__) . '/');
// uc_client ��ʹ�õ� lib �����ڵ�Ŀ¼
// define('UC_LIB_ROOT', dirname(__FILE__) . '/../lib/');
// uc_client ���İ汾
define('UC_CLIENT_VERSION', '0.1.0');
// uc_client ʹ�õ�API�淶��
define('UC_CLIENT_API', '20090609');

/**
 �û���¼
  @param  string $username   - �û���
  @param  string $pwd        - ����(md5)
  @param  int $logintype     - ��¼���� 0,1,2�ֱ�Ϊ �û���,uid,�����¼
  @param  boolean $checkques - �Ƿ�Ҫ��֤��ȫ����
  @param  string $question   - ��ȫ����
  @param  string $answer     - ��ȫ�ش�
  @return array ͬ����¼�Ĵ���
*/
function uc_user_login($username, $password, $logintype, $checkques = 0, $question = '', $answer = '') {
	return uc_data_request('user', 'login', array($username, $password, $logintype, $checkques, $question, $answer));
}

/**
 ͬ���˳�
  @return string ͬ���˳��Ĵ���
*/
function uc_user_synlogout() {
	return uc_data_request('user', 'synlogout');
}

/**
 ע��
  @param  string $username - ע���û���
  @param  string $password - ע������(md5)
  @param  string $email	   - ����
  @return int ע���û�uid
*/
function uc_user_register($username, $password, $email) {
	$args = func_get_args();
	return uc_data_request('user', 'register', $args);
}
/**
 ��ȡ�û���Ϣ
  @param  string $username - �û���
  @param  int $bytype - ��ȡ��ʽ 0,1,2�ֱ�Ϊ �û���,uid,����
  @return array uid,�û���,����
*/
function uc_user_get($username, $bytype = 0) {
	return uc_data_request('user', 'get', array($username, $bytype));
}

/**
 ��֤
  @param  string $uid - �û���
  @checkstr string password - uc_key+passwrord
  @return array uid,�û���,����,����
*/
function uc_user_check($uid, $checkstr) {
	$args = func_get_args();
	return uc_data_request('user', 'check', $args);
}

/**
 �༭�û�����
  @param  int $uid - �û�uid
  @param  string $oldname - ԭ�û���
  @param  string $newname - ���û���
  @param  string $pwd - ������
  @param  string $email - ������
*/
function uc_user_edit($uid, $oldname, $newname, $pwd, $email) {
	return uc_data_request('user', 'edit', array($uid, $oldname, $newname, $pwd, $email));
}

/**
 ɾ��ָ�� uid ���û�
  @param  mixed $uids - �û�uid���У�֧�ֵ���uid,���uid��������á�,���������ַ�������
  @param  int $del
*/
function uc_user_delete($uids) {
	return uc_data_request('user', 'delete', array($uids));
}

/**
 �����û���������
  @param  array $credit array($uid1 => array($ctype1 => $point1, $ctype2 => $point2), $uid2 => array())
  return array
 */
function uc_credit_add($credit, $isAdd = true) {
	return uc_data_request('credit', 'add', array($credit, $isAdd));
}

function uc_credit_get($uid) {
	return uc_data_request('credit', 'get', array($uid));
}


function uc_data_request($class,$method,$args = array()) {
	static $uc = null;
	if (empty($uc)) {
		require_once UC_CLIENT_ROOT . 'class_core.php';
		$uc = new UC();
	}
	$class = $uc->control($class);

	if (method_exists($class, $method)) {
		return call_user_func_array(array(&$class, $method), $args);
	} else {
		return 'error';
	}
}
?>
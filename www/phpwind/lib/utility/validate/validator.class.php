<?php
!defined('P_W') && exit('Forbidden');

/**
 * ������֤��
 */
class PW_Validator {
	
	/**
	 * ��֤�ַ���
	 * 
	 * @param string $data �����ַ���
	 * @param string $type ����
	 * @return bool �Ƿ�Ϸ�
	 */
	function validate($data, $type) {
		if (empty($type)) return false;
		if (null !== ($reg = PW_Validator::_getRegValidator($type))) {
			return PW_Validator::_validateByReg($data, $reg);
		} elseif (null !== ($specify = PW_Validator::_getSpecifyValidator($type))) {
			return PW_Validator::_validateBySpecify($data, $specify);
		}
		return false;
	}
	
	/**
	 * ��ȡ������֤��
	 * 
	 * @access protected
	 * @param string $type ����
	 * @return string|null ������ʽ
	 */
	function _getRegValidator($type) {
		$regValidateConfig = array(
			"username" => "/^[a-zA-Z0-9_]{4,20}$/",
			"email" => "/^[-a-zA-Z0-9_\.]+@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$/",
			"url" => "/^http:\/\/[A-Za-z0-9]*\.[A-Za-z0-9]*[\/=\?%\-&_~@\.A-Za-z0-9]*$/",
		);
		return isset($regValidateConfig[$type]) ? $regValidateConfig[$type] : null;
	}
	
	/**
	 * ��ȡ���Ƶ���֤��
	 * @param string $type ����
	 * @return Object ���Ƶ���֤������
	 */
	function _getSpecifyValidator($type) {
		return L::loadClass('Validate' . ucfirst(strtolower($type)), 'utility/validate/specify');
	}
	
	/**
	 * ͨ��������֤
	 * 
	 * @param string $data ����
	 * @param string $reg ����
	 * @return bool �Ƿ�Ϸ�
	 */
	function _validateByReg($data, $reg) {
		return (bool) preg_match($reg, $data);
	}
	/**
	 * ͨ�����Ƶ���֤����֤
	 * 
	 * @param string $data ����
	 * @param Object $specify ��֤������
	 * @return bool �Ƿ�Ϸ�
	 */
	function _validateBySpecify($data, $specify) {
		return $specify->validate($data);
	}
}

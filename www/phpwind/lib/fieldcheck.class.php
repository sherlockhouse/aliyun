<?php
!defined('P_W') && exit('Forbidden');
/**
 * �������ֶμ��
 * @author zuojie
 *
 */
class FieldCheck {
	/**
	 * @var mix ������ص�����ֵ
	 * @access protected
	 */
	var $errorValue;

	/**
	 * ��ô�����ص�����ֵ
	 * @return mix ������ص�����ֵ
	 * @access public
	 */
	function getErrorValue() {
		return $this->errorValue;
	}
	/**
	 * ���ô�����ص�����ֵ
	 * @param mix $value ֵ
	 * @return FieldCheck this
	 * @access public
	 */
	function setErrorValue($value) {
		$this->errorValue = $value;
		return $this;
	}
	/**
	 * ��ȡĬ���ֶεĴ�����Ϣ
	 * @param string $fieldType �ֶ�����
	 * @param mix $data �ֶ�ֵ
	 * @param mix $rules ����
	 */
	function getError($fieldType, $data, $rules = NULL) {
		$errorKey = false;
		switch ($fieldType) {
			case 'number' : 
				$errorKey = $this->getNumberError($data, $rules['minnum'], $rules['maxnum']);
				break;
			case 'text' :
			case 'textarea' :
				break;
			case 'radio' : //radio��selectʹ��ͬһ����֤����
			case 'select' :
				$errorKey = $this->getSelectionError($data);
				break;
			case 'checkbox' :
				$errorKey = $this->getCheckboxError($data);
				break;
			case 'calendar' :
				$errorKey = $this->getCalendarError($data);
				break;
			case 'email' : 
				$errorKey = $this->getEmailError($data);
				break;
			case 'url' : 
			case 'img' : 
				break;
			case 'upload' :
				break;
			case 'range' : 
				$errorKey = $this->getRangeError($data);
				break;
			default :
				break;
		}
		return $errorKey;
	}
	/**
	 * ����ȡֵ��Χ����ֵ�ֶ�
	 * @param float $value ��ֵ
	 * @param float $min �������Сֵ
	 * @param float $max ��������ֵ
	 * @return string|bool �����ش���key�����򷵻�false
	 */
	function getRangeError($value, $min = '', $max = '') {
		$errorKey = $this->getNumberError($value);
		if ($errorKey) {
			return $errorKey;
		} elseif (is_numeric($min) && is_numeric($max) && ($value < $min || $value > $max)) {
			$this->setErrorValue($value);
			return 'act_number_limit';
		} else {
			return false;
		}
	}
	/**
	 * �����ֵ�ֶ�
	 * @param float $value ��ֵ
	 * @return string|bool �����ش���key�����򷵻�false
	 */
	function getNumberError($value) {
		if (!is_numeric($value) && $value) {
			return 'act_number_error';
		} else {
			return false;
		}
	}
	/**
	 * ���email�ֶ�
	 * @param string $email email��ַ
	 * @return string|bool �����ش���key�����򷵻�false
	 */
	function getEmailError($email) {
		if (!preg_match('/^[-a-zA-Z0-9_\.]+@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$/', $email)) {
			$this->setErrorValue($email);
			return 'illegal_email';
		} else {
			return false;
		}
	}
	/**
	 * ���ѡ���ࣨradio, checkbox, select����ֵ
	 * @param int $value ֵ
	 * @return string|bool �����ش���key�����򷵻�false
	 */
	function getSelectionError($value) {
		if (!is_numeric($value) || $value != (int)$value) { //Ϊ����ֵ
			return 'selection_not_int';
			$this->setErrorValue($value);
		} else {
			return false;
		}
	}
	/**
	 * ���checkbox������ֵ
	 * @param array $values checkbox��ֵ
	 * @return string|bool �����ش���key�����򷵻�false
	 */
	function getCheckboxError($values) {
		if (!is_array($values)) {
			return 'checkbox_not_array';
		} else {
			foreach ($values as $value) {
				$errorKey = $this->getSelectionError($value);
				if ($errorKey) {
					return $errorKey;
				}
			}
			return false;
		}
	}
	/**
	 * ���ʱ���ֶ�
	 * @param string $string ʱ���ַ�����'2010-4-9 13:00:00'
	 * @return string|bool �����ش���key�����򷵻�false
	 */
	function getCalendarError ($string) {
		if ($string) {
			$time = strtotime($string);
			if (!$time || -1 == $time) { //strtotime()��PHP 5.1.0��ǰʧ��ʱ����-1
				$this->setErrorValue($string);
				return 'calendar_wrong_format';
			} else {
				return false;
			}
		}
	}
	/**
	 * �����ֶα��������ݿ��ֵ
	 * @param string $fieldType �ֶ�����
	 * @param mix $data ֵ
	 * @return string ���������ݿ��ֵ
	 */
	function getValueForDb($fieldType, $data) {
		$returnValue = $data;
		switch ($fieldType) {
			case 'number' : 
			case 'range' : 
				break;
			case 'text' :
			case 'textarea' :
				break;
			case 'radio' : //radio��selectʹ��ͬһ����
			case 'select' :
				$returnValue = (int)$data;
				break;
			case 'checkbox' :
				$returnValue = '';
				foreach ($data as $selection) {
					$returnValue .= (int)$selection.',';
				}
				break;
			case 'calendar' :
				$returnValue = PwStrtoTime($data);
				break;
			case 'email' : 
			case 'url' : 
			case 'img' : 
			case 'upload' :
			default :
				break;
		}
		return $returnValue;
	}
}
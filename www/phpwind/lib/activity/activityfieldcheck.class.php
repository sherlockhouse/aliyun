<?php
!defined('P_W') && exit('Forbidden');
L::loadClass('fieldcheck', '', false);
/**
 * ���ص������ֶμ��
 * @author zuojie
 *
 */
class PW_ActivityFieldCheck extends Fieldcheck {
	/**
	 * @var array ������Ϣ����
	 * @access protected
	 */
	var $errorMessage;
	var $feesArray;
	var $feesDetailArray;
	var $telephones;

	/**
	 * ���ݴ���key��ȡ��Ӧ�����Ĵ�����ʾ
	 * @param string $key ������Ϣ�Ĵ��ƣ�Ӣ�ģ�
	 * @access public
	 * @return string human-readable�Ĵ�����Ϣ�����ģ�
	 */
	function getErrorMessageByKey($key) {
		global $db_actname;
		$db_actname = $this->getErrorValue();
		$errorMessage = $this->getErrorMessage();
		if ($errorMessage[$key] && is_string($errorMessage[$key])) {
			$keyErrorMessage = str_replace('{value}', $db_actname, $errorMessage[$key]);
			return $keyErrorMessage;
		} elseif ($key) {
			return 'unknown error';
		} else {
			return '';
		}
	}
	/**
	 * ��ȡ���д�����ʾ
	 * @access public
	 * @return array ������ʾ
	 */
	function getErrorMessage() {
		if (!$this->errorMessage) {
			$this->_presetErrorMessage();
		}
		return $this->errorMessage;
	}
	/**
	 * ����$errorMessage��ʼֵ
	 * @param array $errorMessage
	 * @access protected
	 * @return bool|FieldCheck ������false
	 */
	function _presetErrorMessage() {
		require_once S::escapePath(GetLang('fielderror'));
		$errorMessage = $lang['fielderror'];
		if ($errorMessage) {
			$this->_setErrorMessage($errorMessage);
			return $this;
		} else {
			return false;
		}
	}
	/**
	 * ����$errorMessage
	 * @param array $errorMessage
	 * @access protected
	 * @return bool|FieldCheck ������false
	 */
	function _setErrorMessage($errorMessage) {
		if (!is_array($errorMessage)) {
			return false;
		} else {
			$this->errorMessage = $errorMessage;
			return $this;
		}
	}
	/**
	 * ��ʼʱ���Ƿ����ڽ���ʱ��
	 * @param string $start ��ʼʱ�䣬��'2010-04-09 11:00:00'
	 * @param string $end ����ʱ�䣬��'2010-04-09 12:00:00'
	 * @access protected
	 * @return bool ʱ���Ƿ���Ч
	 */
	function _isValidStartAndEndTime ($start, $end) {
		if ($this->getCalendarError($start) || $this->getCalendarError($end)) {
			return false;
		} else {
			$startTimestamp = PwStrtoTime($start);
			$endTimestamp = PwStrtoTime($end);
			if ($startTimestamp > $endTimestamp) {
				return false;
			} else {
				return true;
			}
		}
	}
	/**
	 * ��鿪ʼ�ͽ���ʱ��
	 * @param string $start ��ʼʱ�䣬��'2010-04-09 11:00:00'
	 * @param string $end ����ʱ�䣬��'2010-04-09 12:00:00'
	 * @return string|bool �����ش���key�����򷵻�false
	 */
	function getTimeRangeError($start, $end) {
		if (!$this->_isValidStartAndEndTime($start, $end)) {
			return 'start_time_later_than_end_time';
		} else {
			return false;
		}
	}
	/**
	 * ��鱨������ʱ��ͻ��ʼʱ���Ƿ��г�ͻ
	 * @param string $signupEnd ��������ʱ�䣬'2010-04-09 11:00:00'
	 * @param string $activityStart ���ʼʱ�䣬��'2010-04-09 12:00:00'
	 * @return string|bool �����ش���key�����򷵻�false
	 */
	function getActivityAndSignupTimeConflictError($signupEnd, $activityStart) {
		if (!$this->_isValidStartAndEndTime($signupEnd, $activityStart)) {
			return 'signup_end_time_later_than_activity_start_time';
		} else {
			return false;
		}
	}
	/**
	 * �Ƿ�Ϊ��Ч�Ľ�Ǯ��
	 * @param float ��Ǯ������1.53
	 * @access protected
	 * @return bool �Ƿ���Ч
	 */
	function _isValidMoney($money) {
		if (!is_numeric($money) || 0 >= $money) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * ����Ǯֵ
	 * @param float $money
	 * @return string|bool �����ش���key�����򷵻�false
	 */
	function getMoneyError($money) {
		if (!$this->_isValidMoney($money) && $money) {
			return 'invalid_money';
		} else {
			return false;
		}
	}
	function getLocationError($locations) {
		
	}
	function getContactError($contacts) {
		
	}
	/**
	 * �����ַ����ã���ȫ�Ƕ��ţ������ȼ�����,����Ƕ��ţ��ָ�������
	 * @param string $string �ַ���
	 * @access protected
	 * @return array ����
	 */
	function _getExplodedArrayFromString($string) {
		if (strpos($string, '��') !== false) {
			$delimiter = '��';
		} else {
			$delimiter = ',';
		}
		$array = explode($delimiter, $string);
		foreach ($array as $key => $element) {
			$array[$key] = trim($element);
			if (!$array[$key]) {
				unset($array[$key]);
			}
		}
		return $array;
	}
	/**
	 * ����������,����Ƕ��ţ��ָ����ַ���
	 * @param array $array ����
	 * @access protected
	 * @return string �ַ���
	 */
	function _getImplodedStringFromArray($array) {
		return implode(',', $array);
	}
	/**
	 * ����Ƿ�Ϊ��Ч�ĵ绰����
	 * @param string $telephone �绰���룬����ĸ�ʽ��13123456789, +8613123456789, 0571-12345678, (0571)12345678��0578-12345678p123��p��Ϊ�ֻ�����
	 * @access protected
	 * @return bool �Ƿ���Ч
	 */
	function _isValidTelephoneNumber($telephone) {
		if (preg_match('/^[0-9p\(\)\-\+]+$/i', $telephone)) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * �����ϵ�绰�ֶ�
	 * @param string $telephones �绰���룬���������','�����ţ��ָ��'0571-12345678,13123456789'
	 * @return string|bool �����ش���key�����򷵻�false
	 */
	function getTelephoneError($telephones) {
		$telephoneArray = $this->_getExplodedArrayFromString($telephones);
		foreach ($telephoneArray as $element) {
			if (!$this->_isValidTelephoneNumber($element)) {
				$this->setErrorValue($element);
				return 'invalid_telephone_format';
			}
		}
		$this->telephones = $this->_getImplodedStringFromArray($telephoneArray);
		return false;
	}
	function getTelephones () {
		return $this->telephones;
	}
	/**
	 * @param int $payMethod ֧����ʽ
	 * @param int|string $min ��С����
	 * @param int|string $max �������
	 * @param int $peopleAlreadySignup �ѱ�������
	 */
	function getParticipantError($payMethod, $min = '', $max = '', $peopleAlreadySignup = 0) {
		//1=֧������2=�ֽ�
		$errorKey = $this->getPayMethodError($payMethod);
		if ($errorKey) {
			return $errorKey;
		}
		$payMethodIsAlipay = $payMethod == 1 ? true : false;
		foreach (array($min, $max) as $value) {
			if ($value && (!is_numeric($value) || $value != (int)$value || $value < 0)) { //��ֵ�Ҳ��Ǵ���0������
				return 'invalid_participant_number';
			}
		}
		if ($max && $min > $max) {
			return 'minimum_larger_than_maximum';
		} elseif ($peopleAlreadySignup && $max < $peopleAlreadySignup) {
			return 'max_less_than_people_already_signup';
		} else {
			return false;
		}
	}
	function getUserLimitError($onlyFriend, $specificLimit) {
		
	}
	function getGenderLimitError($gender) {
		
	}
	function getFeesError($fees) {
		$feesArray = array();
		if (!is_array($fees)) {
			return 'invalid_fees_format';
		} else {
			foreach ($fees['condition'] as $key => $value) {
				if ($value && $fees['money'][$key]) {
					$feesArray[$key]['condition'] = $value;
					$errorKey = $this->getMoneyError($fees['money'][$key]);
					if ($errorKey) {
						$this->setErrorValue($fees['money'][$key]);
						return $errorKey;
					} else {
						$feesArray[$key]['money'] = $fees['money'][$key];
					}
				}
			}
			
			$this->feesArray = $feesArray;
			return false;
		}
	}
	function getFeesArray () {
		return $this->feesArray;
	}
	function getFeesDetailError($feesDetail) {
		$feesDetailArray = array();
		if (!is_array($feesDetail)) {
			return 'invalid_fees_detail_format';
		} else {
			foreach ($feesDetail['item'] as $key => $value) {
				if ($feesDetail['money'][$key] && $value) {
					$feesDetailArray[$key]['item'] = $value;
					$errorKey = $this->getMoneyError($feesDetail['money'][$key]);
					if ($errorKey) {
						$this->setErrorValue($feesDetail['money'][$key]);
						return $errorKey;
					} else {
						$feesDetailArray[$key]['money'] = $feesDetail['money'][$key];
					}
				}
			}
			$this->feesDetailArray = $feesDetailArray;
			return false;
		}
	}
	function getFeesDetailArray() {
		return $this->feesDetailArray;
	}
	function getPayMethodError($payMethod) {
		if ($payMethod != 1 && $payMethod != 2) {
			return 'invalid_pay_method';
		} else {
			return false;
		}
	}
}

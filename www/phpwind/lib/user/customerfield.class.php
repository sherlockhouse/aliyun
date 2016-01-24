<?php
!defined('P_W') && exit('Forbidden');

class PW_CustomerField {
	
	var $typeMap = array();
	var $flipTypeMap = array();
	var $fieldMap = array();
	var $memberData = array();

	function PW_CustomerField(){
		$this->_initTypeMap();
		$this->_initFieldMap();
	}

	/**
	 * �����ֶ�id��ȡ�ֶ���Ϣ
	 * @param int $fieldId
	 * @return array
	 */
	function getFieldByFieldId($fieldId){
		$fieldId = intval($fieldId);
		if ($fieldId < 1) return array();
		$dao = $this->_getCustomerFieldDao();
		return $dao->get($fieldId);
	}
	
	function getFieldByFieldName($fieldName) {
		if (!$fieldName) return false;
		if (preg_match('/field_(\d+)/', $fieldName,$m)) {
			return $this->getFieldByFieldId($m[1]);
		} else {
			$dao = $this->_getCustomerFieldDao();
			return $dao->getFieldByFieldName($fieldName);
		}
	}
	/**
	 * 
	 * ������ȡ�ֶ��б�
	 * @param string $categoryName
	 * @return array
	 */
	function getFieldsByCategoryName($categoryName){
		$fields = array();
		$categories = $this->_initCategoryMap();
		if (!$categoryName || !isset($categories[$categoryName])) return $fields;
		$dao = $this->_getCustomerFieldDao();
		$fields = (array)$dao->getFieldsByCategoryName($categoryName);
		foreach ($fields as $key => $value) {
			$fields[$key] = $this->formatFieldInfo($value);
		}
		return $fields;
	}
	/**
	 * 
	 * ������ȡ�ֶ��б�
	 * @param string $categoryName
	 * @return array
	 */
	function getFieldsnameByCategoryName($categoryName){
		$fields = $tmpfields = array();
		$categories = $this->_initCategoryMap();
		if (!$categoryName || !isset($categories[$categoryName])) return $fields;
		$dao = $this->_getCustomerFieldDao();
		$fields = (array)$dao->getFieldsByCategoryName($categoryName);
		foreach ($fields as $key => $value) {
			if (!$value['fieldname']) continue;
			$tmpfields[] = $value['fieldname'];
		}
		return $tmpfields;
	}
	/**
	 * 
	 * ���������״���д�����ȡ�ֶ��б�
	 * @param int $complement ��д����
	 * @param bool $needClassify �Ƿ���Ҫ����
	 * @return array
	 */
	function getFieldsByComplement($complement, $needClassify = false){
		$fields = array();
		$complement = (int) $complement;
		if (!S::inArray($complement, array(0,1,2))) return $fields;
		$dao = $this->_getCustomerFieldDao();
		$fields = $dao->getFieldsByComplement($complement);
		foreach ($fields as $key => $value) {
			$fields[$key] = $this->formatFieldInfo($value);
		}
		if (!$needClassify) return $fields;
		return $this->formatFieldsByCategory($fields);
	}
	
	/**
	 * ��ҳȡ�������ֶ���Ϣ
	 * @param int $start ��ʼλ��
	 * @param int $num	 ����
	 * @return array
	 */
	function getAllFieldsWithPages($start, $num) {
		$start = (int) $start;
		$num = (int) $num;
		if ($start < 0 || $num < 1) return array();
		$dao = $this->_getCustomerFieldDao();
		return $dao->getAllFieldsWithPages($start, $num);
	}
	
	/**
	 * ͳ�������ֶ���Ŀ
	 * @return int
	 */
	function countAllFields() {
		$dao = $this->_getCustomerFieldDao();
		return $dao->countAllFields();
	}
	
	/**
	 * �����ֶ�idɾ���ֶ�
	 * @param int $fieldId �ֶ�id
	 * @return bool
	 */
	function deleteFieldByFieldId($fieldId) {
		$fieldId = (int) $fieldId;
		if ($fieldId < 1) return false;
		$dao = $this->_getCustomerFieldDao();
		return $dao->delete($fieldId);
	}
	
	/**
	 * ���ö�������
	 * @param array $data
	 * @param int $fieldId Ϊ��ʱ������һ������
	 * @return mixed
	 */
	function setField($data,$fieldId){
		if (!S::isArray($data)) return false;
		$fieldId = intval($fieldId);
		$fieldData = array();
		$fields = array('category','title','descrip','state','type','vieworder','maxlen','required','viewinread','editable','viewright','options','complement');
		foreach ($fields as $v ) {
			if(!isset($data[$v])) continue;
			$fieldData[$v] = $data[$v];
		}
		$fieldData = $this->_checkOptionsAndMaxlen($fieldData);
		$dao = $this->_getCustomerFieldDao();
		if($fieldId > 0){
			return $dao->update($fieldData,$fieldId);
		}
		return $dao->insert($fieldData);
	}


	function formatFieldInfo($fieldInfo){
		if (!S::isArray($fieldInfo)) return array();
		//���ȿ���
		$fieldInfo['maxlen'] = intval($fieldInfo['maxlen']);
		if ($fieldInfo['maxlen'] > 255 || !$fieldInfo['maxlen']) $fieldInfo['maxlen'] = 255;
		
		//�ֶ���
		$fieldInfo['ifsys'] or $fieldInfo['fieldname'] = 'field_' . $fieldInfo['id'];
		//ѡ������
		switch ($fieldInfo['type']) {
			case $this->typeMap['radio']:
			case $this->typeMap['select']:
			case $this->typeMap['checkbox']:
				if (!is_array($fieldInfo['options'])) {
					$options = explode("\n", $fieldInfo['options']);
					$fieldInfo['options'] = array();
					foreach ($options as $v){
						list($tmpKey,$tmpVal) = explode('=', $v);
						if(!$tmpVal) continue;
						$fieldInfo['options'][$tmpKey] = $tmpVal;
					}
				}
				break;
			case $this->typeMap['year']:
				S::isArray($fieldInfo['options']) or $fieldInfo['options'] = @(array)unserialize($fieldInfo['options']);
				if (!$fieldInfo['options']['enddate']) {
					$date = getdate($GLOBALS['timestamp']);
					$fieldInfo['options']['enddate'] = $date['year'];
				}
				if (!$fieldInfo['options']['startdate']) {
					$fieldInfo['options']['startdate'] = $fieldInfo['options']['enddate'] - 20;
				}
				/*
				$fieldInfo['options'] = trim($fieldInfo['options']);
				if(!preg_match('/^(19|20)\d{2}-(19|20)\d{2}$/',$fieldInfo['options'])) {
					$fieldInfo['options'] = array();
				} else {
					list($minYear,$maxYear) = explode('-', $fieldInfo['options']);
					if ($minYear < $maxYear ) {
						$fieldInfo['options']['min'] = $minYear;
						$fieldInfo['options']['max'] = $maxYear;
					} else {
						$fieldInfo['options'] = array();
					}
				}
				*/
				break;
			case $this->typeMap['area']:
				$fieldInfo['options'] = S::isArray($fieldInfo['options']) ? $fieldInfo['options'] : @(array)unserialize($fieldInfo['options']);
				break;
			default:
				//TODO;
				break;
		}
		return $fieldInfo;
	}
	
	/**
	 * ���ֶΰ������������
	 * @param array $fields
	 * @return array
	 */
	function formatFieldsByCategory($fields) {
		if (!S::isArray($fields)) return array();
		$classifiedFields = array();
		$categoryMap = $this->_initCategoryMap();
		foreach ($fields as $value) {
			if (!S::isArray($value)) continue;
			!$classifiedFields[$value['category']]['categoryname'] && $classifiedFields[$value['category']]['categoryname'] = $categoryMap[$value['category']];
			$classifiedFields[$value['category']][] = $value;
		}
		return $classifiedFields;
	}
	
	function _initTypeMap(){
		$this->typeMap = array(
			'input' => 1,
			'textarea' => 2,
			'radio' => 3,
			'checkbox' => 4,
			'select' => 5,
			'year' => 6,
			'area' => 7,
			'education' => 8,
			'career' => 9,
		);
		$this->flipTypeMap = array_flip($this->typeMap);
	}

	function _initFieldMap(){
		$this->fieldMap = array(
			'realname' => 'pw_members',
			'gender' => 'pw_members',
			'bday' => 'pw_members',
			'apartment' => 'pw_members',
			'home' => 'pw_members',
			'education' => 'pw_user_education',
			'career' => 'pw_user_career',
			'oicq' => 'pw_members',
			'aliww' => 'pw_members',
			'msn' => 'pw_members',
			'yahoo' => 'pw_members',
			'alipay' => 'pw_memberinfo',
		);
	}
	
	/*
	function _initSystemFieldMap() {
		return array(
			'realname' => '��ʵ����',
			'gender' => '�Ա�',
			'bday' => '����',
			'apartment' => '�־�ס��',
			'home' => '����',
			'education' => '��������',
			'career' => '��������',
			'oicq' => 'QQ',
			'alipay' => '֧�����˺�',
		);
	}
	*/
	
	function _initCategoryMap() {
		return array(
			'basic' => '��������',
			'contact' => '��ϵ��ʽ',
			'education' => '��������',
			'other' => '��������'
		);
	}
	
	/**
	 * ����options��maxlen�ֶε�����
	 * @param array $data �ֶ���Ϣ
	 * @return array
	 */
	function _checkOptionsAndMaxlen($data) {
		if (!S::isArray($data) || (S::isArray($data) && (!$data['options'] || !$data['type']))) return $data;
		switch ($data['type']) {
			case $this->typeMap['input']://�����ı���
			case $this->typeMap['textarea']://�����ı���
				$data['options'] = '';
				break;
			case $this->typeMap['radio']://��ѡ��
			case $this->typeMap['checkbox']://��ѡ��
			case $this->typeMap['select']://����ѡ���
				$data['maxlen'] = '';
				$data['options'] = $data['options']['text'];
				break;
			case $this->typeMap['year']://������д����
				$data['maxlen'] = '';
				$data['options'] = serialize(array('startdate' => $data['options']['startdate'], 'enddate' => $data['options']['enddate']));
				break;
			case $this->typeMap['area']://������д����
				$data['maxlen'] = '';
				$data['options'] = serialize(array('province' => $data['options']['province'], 'city' => $data['options']['city'], 'area' => $data['options']['area']));
				break;
		}
		return $data;
	}
	
	/**
	 * get PW_CustomerFieldDB
	 * 
	 * @return PW_CustomerFieldDB
	 */
	function _getCustomerFieldDao(){
		return L::loadDB('CustomerField', 'user');
	}
}
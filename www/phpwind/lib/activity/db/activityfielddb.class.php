<?php
!defined('P_W') && exit('Forbidden');
/**
 * �������
 * 
 * @package activity
 */

class PW_ActivityFieldDB extends BaseDB {
	var $_tableName = 'pw_activityfield';
	var $_primaryKey = 'fieldid';
	function getFieldsByModelId ($modelId) {
		$query = $this->_db->query('SELECT * FROM ' . $this->_tableName . ' WHERE actmid=' . $this->_addSlashes($modelId) . ' ORDER BY ifdel ASC, vieworder ASC, ' . $this->_primaryKey . ' ASC');
		$fields = $this->_getAllResultFromQuery($query, $this->_primaryKey);
		foreach ($fields as $key=>$field) {
			$fields[$key] = $this->_getParsedField($field);
		}
		return $fields;
	}
	
	function getDefaultSearchFields() {
		$searchFieldDb = array (
			array (
				'fieldname' => 'starttime',
				'name' => 
					array (
						'�ʱ��',
						NULL,
						NULL,
					),
				'type' => 'calendar',
				'rules' => 
					array (
						'precision' => 'minute',
					),
				'ifsearch' => '1',
				'ifasearch' => '1',
				'vieworder' => '1',
				'textwidth' => '18',
			),
			array (
				'fieldname' => 'endtime',
				'name' => 
					array (
						'-',
						NULL,
						NULL,
					),
				'type' => 'calendar',
				'rules' => 
					array (
						'precision' => 'minute',
					),
				'ifsearch' => '1',
				'ifasearch' => '1',
				'vieworder' => '1',
				'textwidth' => '18',
			),
			array (
				'fieldname' => 'location',
				'name' => 
					array (
						'��ص�',
						NULL,
						NULL,
					),
				'type' => 'text',
				'rules' => '',
				'ifsearch' => '1',
				'ifasearch' => '1',
				'vieworder' => '2',
				'textwidth' => '40',
			),
			array (
				'fieldname' => 'contact',
				'name' => 
					array (
						'��ϵ��',
						NULL,
						NULL,
					),
				'type' => 'text',
				'rules' => '',
				'ifsearch' => '0',
				'ifasearch' => '1',
				'vieworder' => '4',
				'textwidth' => '20',
			),
			array (
				'fieldname' => 'telephone',
				'name' => 
					array (
						'��ϵ�绰',
						NULL,
						NULL,
					),
				'type' => 'text',
				'rules' => '',
				'ifsearch' => '0',
				'ifasearch' => '1',
				'vieworder' => '5',
				'textwidth' => '18',
			),
			array (
				'fieldname' => 'signupstarttime',
				'name' => 
					array (
						'����ʱ��',
						NULL,
						NULL,
					),
				'type' => 'calendar',
				'rules' => 
					array (
						'precision' => 'minute',
					),
				'ifsearch' => '0',
				'ifasearch' => '1',
				'vieworder' => '6',
				'textwidth' => '18',
			),
			array (
				'fieldname' => 'signupendtime',
				'name' => 
					array (
						'-',
						NULL,
						NULL,
					),
				'type' => 'calendar',
				'rules' => 
					array (
						'precision' => 'minute',
					),
				'ifsearch' => '0',
				'ifasearch' => '1',
				'vieworder' => '6',
				'textwidth' => '18',
			),
			array (
				'fieldname' => 'userlimit',
				'name' => 
					array (
						'��������',
						NULL,
						NULL,
					),
				'type' => 'radio',
				'rules' => 
					array (
						0 => '1=�����û�',
						1 => '2=������',
					),
				'ifsearch' => '0',
				'ifasearch' => '1',
				'vieworder' => '8',
			),
			array (
				'fieldname' => 'specificuserlimit',
				'name' => 
					array (
						'��������������',
						NULL,
						NULL,
					),
				'type' => 'text',
				'rules' => '',
				'ifsearch' => '0',
				'ifasearch' => '1',
				'vieworder' => '8',
				'textwidth' => '14',
			),
			array (
				'fieldname' => 'genderlimit',
				'name' => 
					array (
						'�Ա�����',
						NULL,
						NULL,
					),
				'type' => 'radio',
				'rules' => 
					array (
						0 => '1=ȫ��',
						1 => '2=������',
						2 => '3=��Ů��',
					),
				'ifsearch' => '0',
				'ifasearch' => '1',
				'vieworder' => '9',
			),
			array (
				'fieldname' => 'paymethod',
				'name' => 
					array (
						'֧����ʽ',
						NULL,
						NULL,
					),
				'type' => 'radio',
				'rules' => 
					array (
						0 => '1=֧����',
						1 => '2=�ֽ�֧��',
					),
				'ifsearch' => '1',
				'ifasearch' => '1',
				'vieworder' => '12',
			)
		);
		return $searchFieldDb;
	}
	
	/**
	 * ���ش�������ֶ�
	 * 
	 * ��rule���л���name�ֽ��3����
	 * @param array $field
	 * @return array ��������ֶ�
	 * @access private
	 */
	function _getParsedField($field) {
		if (!is_array($field)) {
			return false;
		}
		if ($field['rules']) {
			$field['rules'] = $this->_unserialize($field['rules']);
		}
		$field['nameInDb'] = $field['name'];
		$field['name'] = $this->_getNamePartsByName($field['name']);
		return $field;
	}
	
	/**
	 * ����3��������
	 * @param string $name ԭ���֣���ʽ������1{#}����2{@}����3
	 * @return array 3��������
	 * @access private
	 */
	function _getNamePartsByName($name) {
		list($name1, $name3) = explode('{#}',$name);
		list($name1, $name2) = explode('{@}',$name1);
		return array($name1, $name2, $name3);
	}
	
	function getField($id) {
		$field = $this->_get($id);
		return $this->_getParsedField($field);
	}
	
	function update($id, $fieldData) {
		$this->_update($fieldData,$id);
	}
	
	function getFieldsByIds($ids) {
		$query = $this->_db->query('SELECT * FROM ' . $this->_tableName . ' WHERE ' . $this->_primaryKey . ' IN (' . $this->_getImplodeString($ids) . ')');
		$fields = $this->_getAllResultFromQuery($query, $this->_primaryKey);
		foreach ($fields as $key=>$field) {
			$fields[$key] = $this->_getParsedField($field);
		}
		return $fields;
	}
	
	function insert($fieldData) {
		return $this->_insert($fieldData);
	}
	
	function delete($id) {
		return $this->_delete($id);
	}
	
	function getFieldByModelIdAndName($modelId, $fieldName) {
		$field = $this->_db->get_one('SELECT * FROM ' . $this->_tableName . ' WHERE actmid=' . $this->_addSlashes($modelId) . ' AND fieldname=' . $this->_addSlashes($fieldName));
		return $this->_getParsedField($field);
	}
}
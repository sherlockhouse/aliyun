<?php
!defined('P_W') && exit('Forbidden');

/**
 * ���������
 * @package  PW_AreasService
 * @author phpwind @2010-1-18
 */
class PW_AreasService {

	/**
	 * ���
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @return int 
	 */
	function addArea($fieldsData) {
		$fieldsData = $this->checkFieldsData($fieldsData);
		if (!S::isArray($fieldsData)) return false;
		$areaDb = $this->_getAreasDB();
		$result = $areaDb->insert($fieldsData);
		$this->setAreaCache();
		return $result;
	}

	/**
	 * �������
	 * 
	 * @param array $fieldsData ��ά��������
	 * @return int 
	 */
	function addAreas($fieldsData) {
		foreach ($fieldsData as $v) {
			$tmpData = $this->buildAddData($v);
			$fieldsDatas[] = $this->checkFieldsData($tmpData);
		}
		if (!S::isArray($fieldsDatas)) return false;
		$areaDb = $this->_getAreasDB();
		$result = $areaDb->addAreas($fieldsDatas);
		$this->setAreaCache();
		return $result;
	}
	
	/**
	 * ����
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @param int $areaid  ����ID
	 * @return boolean 
	 */
	function updateArea($fieldsData,$areaid) {
		$areaid = intval($areaid);
		$fieldsData = $this->buildAddData($fieldsData);
		$fieldsData = $this->checkFieldsData($fieldsData);
		if ($areaid < 1 || !S::isArray($fieldsData)) return false;
		$areaDb = $this->_getAreasDB();
		$result = $areaDb->update($fieldsData,$areaid);
		$this->setAreaCache();
		return $result; 
	}
	
	/**
	 * ����ɾ��
	 * 
	 * @param int $areaid  ����ID
	 * @return boolean
	 */
	function deleteAreaByAreaId($areaid) {
		$areaid = intval($areaid);
		if ($areaid < 1) return false;
		$areaDb = $this->_getAreasDB();
		$result = $areaDb->delete($areaid);
		$this->setAreaCache();
		return $result; 
	}
	
	/**
	 * ����ɾ��
	 * 
	 * @param array $areaids  ����IDs
	 * @return boolean
	 */
	function deleteAreaByAreaIds($areaids) {
		if(!S::isArray($areaids)) return false;
		$areaDb = $this->_getAreasDB();
		$result = $areaDb->deleteByAreaIds($areaids);
		$this->setAreaCache();
		return $result; 
	}
	
	/**
	 * ���ݵ���ID��ȡ��Ϣ
	 * 
	 * @param int $areaid  ����ID
	 * @return array
	 */
	function getAreaByAreaId($areaid) {
		$areaid = intval($areaid);
		if ($areaid < 1) return array();
		$areaDb = $this->_getAreasDB();
		return $areaDb->getAreaByAreaId($areaid);
	}
	
	function getFullAreaByAreaIds($areaids){
		if (!S::isArray($areaids)) return array();
		$areaDb = $this->_getAreasDB();
		return $areaDb->getFullAreaByAreaIds($areaids);
	}
	
	/**
	 * ���ݶ������id��ȡ��Ϣ
	 * @param array $areaids
	 * @return array
	 */
	function getAreasByAreadIds($areaids) {
		if (!S::isArray($areaids)) return array();
		$areaDb = $this->_getAreasDB();
		return $areaDb->getAreasByAreadIds($areaids);
	}
	
	/**
	 * ���ݵ�������ȡ��Ϣ
	 * 
	 * @param string $areaName ������
	 * @return array
	 */
	function getAreaByAreaName($areaName) {
		$areaName = trim($areaName);
		if (!$areaName) return array();
		$areaDb = $this->_getAreasDB();
		return $areaDb->getAreaByAreaName($areaName);
	}
	
	/**
	 * ����level��ȡ����,��ʱû��
	 * 
	 * @param int $level  1����2ʡ��3��4����
	 * @return array
	 */
	function getAreaByAreaLevel($level) {
		$level = intval($level);
		if ($level < 1) return array();
		$areaDb = $this->_getAreasDB();
		return $areaDb->getAreaByAreaLevel($level);
	}
	
	/**
	 * ��ȡ����������ϼ�����������ids
	 * @param array $areaids
	 * @return array
	 */
	function getParentidByAreaids($areaids) {
		if (!S::isArray($areaids)) return array();
		$tempResult = $this->getAreasByAreadIds($areaids);
		$upids = $upperids = $tempids = array();
		foreach ($tempResult as $key => $value) {
			$upids[$key] = $tempids[] = $value['parentid'];
		}
		$tempids = array_filter(array_unique($tempids));
		if (!S::isArray($tempids)) return array($upids, $upperids);
		$anotherTempResult = $this->getAreasByAreadIds($tempids);
		foreach ($anotherTempResult as $k => $v) {
			$upperids[$k] = $v['parentid'];
		}
		return array($upids, $upperids);
	}
	
	/**
	 * ����parent��ȡ����
	 * 
	 * @param int $parentid ��һ��areaid
	 * @return array
	 */
	function getAreaByAreaParent($parentid = 0) {
		$parentid = intval($parentid);
		if ($parentid < 0) return array();
		$areaDb = $this->_getAreasDB();
		return $areaDb->getAreaByAreaParent($parentid);
	}
	
	/**
	 * ��װ����������
	 * 
	 * @param int $parentid ��һ��areaid
	 * @param int $defaultValue Ĭ��ѡ��ֵ��id 
	 * @return array
	 */
	function getAreasSelectHtml($parentid = null, $defaultValue = null) {
		$parentid = intval($parentid);
		if ($parentid < 0) return null;
		$areas = $this->getAreaByAreaParent($parentid);
		if (!S::isArray($areas)) return null;
		$areaSelect = '';
		foreach ($areas as $value) {
			$selected = ($defaultValue && $value['areaid'] == $defaultValue) ? 'selected' : '';
			$areaSelect .= "<option value=\"$value[areaid]\" $selected>{$value[name]}</option>\r\n";
		}
		return $areaSelect;
	}

	/**
	 * ��ȡ���ݿ������е���
	 * @return array
	 */
	function getAllAreas() {
		$areaDb = $this->_getAreasDB();
		return $areaDb->getAllAreas();
	}
	
	/**
	 * �������select��
	 * @param array $initValues Ĭ��ѡ�п� ����ʽ�磺array(array('parentid'=>0,'selectid'=>'country','defaultid'=>''));
	 * 										����parentidΪ�ϼ�id,selectidΪselect���id,defaultidΪĬ��ѡ��ֵid
	 * @return string ��װ���ַ���
	 */
	function buildAllAreasLists($initValues = array(),$forJs = false) {
		static $sHasArea = null, $sKey = 0;
		$areaString = $forJs?'':'<script type="text/javascript">';
		if (!isset($sHasArea)) {
			$areas = $this->getAllAreas();
			//if (!$areas) return false;
			!$forJs && $areaString .= "\r\n var initValues = new Array();\r\n";
			$areaString .= "var areas = new Array();\r\n";
			foreach ($areas as $value) {
				$areaString .= "areas['$value[areaid]']=['$value[name]','$value[parentid]','$value[vieworder]'];\r\n";
			}
			$sHasArea = true;
		}
		if ($initValues && S::isArray($initValues)) {
			foreach ($initValues as $v) {
				!$v['defaultid'] && $v['defaultid'] = -1;
				!$v['hasfirst'] && !$v['hasfirst'] = 0;
				!$forJs && $areaString .= "initValues[$sKey] = {'parentid':'$v[parentid]','selectid':'$v[selectid]','defaultid':$v[defaultid],'hasfirst':$v[hasfirst]};\r\n";
				$sKey++;
			}
		}
		!$forJs && $areaString .= '</script>';
		return $areaString;
	}
	
	function setAreaCache(){
		$file = D_P .'data/bbscache/areadata.js';
		$basicValue = array(array('parentid'=>0,'selectid'=>'province','defaultid'=>''));
		$data = $this->buildAllAreasLists($basicValue,true);
		$data && writeover($file,$data);
	}
	
	/**
	 *�������key
	 * 
	 * @return array ����$fieldsData
	 */
	function checkFieldsData($fieldsData){
		$data = array();
		if(isset($fieldsData['areaid'])) $data['areaid'] = intval($fieldsData['areaid']);
		if(isset($fieldsData['name'])) {
			$data['name'] = trim($fieldsData['name']);
			$data['name'] = trim(substrs($data['name'], 60, 'N'), ' &nbsp;');
		}
		if(isset($fieldsData['joinname'])) $data['joinname'] = trim($fieldsData['joinname']);
		if(isset($fieldsData['parentid'])) $data['parentid'] = intval($fieldsData['parentid']);
		if(isset($fieldsData['vieworder'])) $data['vieworder'] = intval($fieldsData['vieworder']);
		return $data;
	}
	
	/**
	 *����parent��ȡjoinnameֵ
	 * 
	 * @param int $parentid ��һ��areaid
	 * @return array ����$fieldsData
	 */
	function buildAddData($fieldsData){
		if (!isset($fieldsData['parentid']) || !$fieldsData['parentid']) {
			$fieldsData['joinname'] = $fieldsData['name'];
			return $fieldsData;
		}
		$parentData = $this->getAreaByAreaId($fieldsData['parentid']);
		$fieldsData['joinname'] = $parentData['joinname'].','.$fieldsData['name'];
		return $fieldsData;
	}
	
	/**
	 *����dao
	 * 
	 * @return PW_AreasDB
	 */
	function _getAreasDB() {
		return L::loadDB('areas', 'utility');
	}
}
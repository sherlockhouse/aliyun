<?php
/**
 * ר��ģ�������
 * 
 * @package STopic
 */

!defined('P_W') && exit('Forbidden');

/**
 * ר��ģ�������
 * 
 * @package STopic
 */
class PW_ParseStopicTpl {
	/**
	 * html����
	 * 
	 * ר��ģ����һЩ����������Ԫ�ص�class��
	 * 
	 * @var array
	 */
	var $_htmlConfig = array(
		'packclass'		=> 'itemDroppable',
		'itemclass'		=> 'itemDraggable',
		'headclass'		=> 'itemHeader',
		'layoutheadclass' => 'layoutHeader',
		'editclass'		=> 'editEl',
		'closeclass'	=> 'closeEl',
		'contentclass'	=> 'itemContent',
	);
	
	/**
	 * ר��������
	 * 
	 * @var PW_STopicService
	 */
	var $service;
	
	/**
	 * ר����������
	 * 
	 * @var array
	 */
	var $stopic;
	
	/**
	 * ר��ģ���б�����
	 * 
	 * @var array
	 */
	var $units;
	
	/**
	 * ר��ģ�������б�����
	 * 
	 * @var array
	 */
	var $blocks;
	
	/**
	 * �Ƿ��Ǻ�̨����
	 * 
	 * @var bool
	 */
	var $ifadmin;
	
	/**
	 * Ҫɾ����ר��ģ���б�
	 * 
	 * @var array
	 */
	var $delunits;
	
	/**
	 * ����html����
	 * 
	 * @var array
	 */
	var $layoutStrings = array();

	/**
	 * ���õ�ǰ�Ƿ��ڴ����̨�����ģ��
	 * 
	 * @param bool $ifadmin
	 */
	function setIfAdmin($ifadmin) {
		$this->ifadmin = $ifadmin;
	}
	
	/**
	 * ��ȡ����html
	 * 
	 * @param string $layout ������
	 * @return string
	 */
	function getLayoutString($layout) {
		if (!isset($this->layoutStrings[$layout])) {
			if ($layout && file_exists(S::escapePath(A_P.'data/layout/'.$layout.'/layout.htm'))) {
				//* $this->layoutStrings[$layout] = readover(S::escapePath(A_P.'data/layout/'.$layout.'/layout.htm'));
				$this->layoutStrings[$layout] = pwCache::readover(S::escapePath(A_P.'data/layout/'.$layout.'/layout.htm'));
			} else {
				$this->layoutStrings[$layout] = '';
			}
		}
		return $this->layoutStrings[$layout];
	}
	
	/**
	 * ����ר��ģ�壬�õ�ר��html����
	 * 
	 * @param PW_STopicService $stopic_service ר�����
	 * @param array $stopic ר������
	 * @param array $units ר��ģ������
	 * @param array $blocks ģ����������
	 * @param bool $ifadmin �Ƿ��̨����
	 * @return string ר��html����
	 */
	function exute($stopic_service,$stopic,$units,$blocks,$ifadmin) {
		global $ifDelOldUnit;
		$this->_register($stopic_service,$stopic,$units,$blocks,$ifadmin);

		$content = '';
		foreach ($this->stopic['block_config'] as $layout_id => $blocks) {
			$tmp = $this->_getLayoutContent($layout_id,$stopic['stopic_id']);
			$head = $this->ifadmin ? $this->_getLayoutHeadData($layout_id) : '';
			$tmp = '<div id="'.$layout_id.'" class="layoutDraggable cc" width="100%">'.$head.$tmp.'</div>';
			$content .= $tmp;
		}
		$ifDelOldUnit || $this->_delOverageUnits();
		return $content;
	}
	
	/**
	 * ��ȡ���ֵ�html����
	 * 
	 * @access protected
	 * @param string $layout_id ����html��id
	 * @return string
	 */
	function _getLayoutContent($layout_id,$stopic_id) {
		list($layout_type, ) = explode('_', $layout_id);
		$string = $this->getLayoutString($layout_type);

		$string = str_replace('{REPLACE_LAYOUT_ID}', $layout_id, $string);

		preg_match_all('/<div(.+?)>([^\x00]+?)<\/div>/is',$string,$match);
		$search = $replace = array();
		foreach ($match[1] as $key=>$value) {
			if (strpos($value,$this->_htmlConfig['packclass'])===false) {
				continue;
			}
			$id	= $this->_getId($value);
			if (!$id) {
				continue;
			}
			$search[]	= $match[0][$key];

			$replace[]	= $this->_getReplace($id,$value,$stopic_id);
		}

		return str_replace($search,$replace,$string);
	}
	
	/**
	 * ��ȡ���ֵ�ͷ��html����
	 * 
	 * @access protected
	 * @param string $layout_id ����id
	 */
	function _getLayoutHeadData($layout_id) {
		list($layout_type, ) = explode('_', $layout_id);
		$layout_info = $this->service->getLayoutInfo($layout_type);
		return '<div class="'.$this->_htmlConfig['layoutheadclass'].'">
<span>'.$layout_info['desc'].'</span>
<a class="closeEl" href="javascript:void(0);">[x]</a>
</div>';
	}

	/**
	 * ע�����
	 * 
	 * @access protected
	 * @param PW_STopicService $stopic_service ר�����
	 * @param array $stopic ר������
	 * @param array $units ר��ģ������
	 * @param array $blocks ģ����������
	 * @param bool $ifadmin �Ƿ��̨����
	 */
	function _register($stopic_service,$stopic,$units,$blocks,$ifadmin) {
		$this->service	=&$stopic_service;
		$this->setIfAdmin($ifadmin);

		$this->stopic	= $stopic;
		$this->units	= $this->delunits = $units;
		$this->blocks	= $blocks;
	}

	/**
	 * ɾ��ʧЧ��ģ������
	 * 
	 * @access protected
	 */
	function _delOverageUnits() {
		if ($this->delunits) {
			$keys = array_keys($this->delunits);
			$this->service->deleteUnits($this->stopic['stopic_id'],$keys);
		}
	}

	/**
	 * ��������ȡģ��html�е�id����
	 * 
	 * @access protected
	 * @param string $string ģ��html
	 * @return string
	 */
	function _getId($string) {
		preg_match('/id=\s?("|\')([\w]*?)\\1/is',$string,$match);
		return $match[2];
	}
	
	/**
	 * ��ȡ������������html����
	 * 
	 * @access protected
	 * @param string $id ������������id����
	 * @param string $divconfig ����������div��������
	 * @return string
	 */
	function _getReplace($id,$divconfig,$stopic_id) {
		$temp = '';
		$temp .= '<div'.$divconfig.'>';
		$temp .= $this->ifadmin ? '&nbsp;' : '';
		$temp .= $this->_getUnitsByPack($id);
		$temp .= '</div>';
		return (strrpos($temp,'{REPLACE_STOPIC_ID}') !== false) ? str_replace('{REPLACE_STOPIC_ID}', $stopic_id, $temp) : $temp;
	}
	
	/**
	 * ��ȡ��������������ģ���html����
	 * 
	 * @access protected
	 * @param string $id ����������id����
	 * @return string
	 */
	function _getUnitsByPack($id) {
		list($layout_type, $layout_id, $layout_part) = explode("_",$id);
		if (!isset($this->stopic['block_config'][$layout_type.'_'.$layout_id][$layout_part])) return '';
		$temp	= '';
		foreach ($this->stopic['block_config'][$layout_type.'_'.$layout_id][$layout_part] as $html_id) {
			$temp	.= $this->_getUnitHTML($html_id);
		}
		return $temp;
	}
	
	/**
	 * ��ȡ����ģ���html����
	 * 
	 * @access protected
	 * @param string $html_id ģ��html��id����
	 * @return string
	 */
	function _getUnitHTML($html_id) {
		$html_id	= trim($html_id);
		list(,$unitType,) = explode("_", $html_id);
		unset($this->delunits[$html_id]);
		$itemStyle = $this->_isUnitNoBorder($unitType) ? 'style="padding:0;margin:0;border:0;"' : '';
		$contentStyle = $this->_isUnitNoBorder($unitType) ? 'style="padding:0;margin:0;border:0;"' : '';
		$temp	= '<div class="'.$this->_htmlConfig['itemclass'].'" id="'.$html_id.'" '.$itemStyle.'>';
		$temp	.= $this->_getHeadData($html_id);
		$temp	.= '<div class="'.$this->_htmlConfig['contentclass'].'" '.$contentStyle.'>';
		$temp	.= $this->_getHtmlData($html_id);
		$temp	.= '</div>';
		$temp	.= '</div>';
		return $temp;
	}
	
	/**
	 * ģ���Ƿ���Ҫ�߿�
	 * 
	 * @access protected
	 * @param string $unitType ģ�����ͣ�����͵���������Ҫ�߿�
	 * @return bool
	 */
	function _isUnitNoBorder($unitType) {
		return in_array($unitType, array('banner', 'nvgt'));
	}
	
	/**
	 * ��ȡģ��ͷ��html����
	 * 
	 * @access protected
	 * @param $html_id
	 */
	function _getHeadData($html_id) {
		$temp	= '';
		if (!$this->units[$html_id]['title'] && !$this->ifadmin) return '';
		$temp	.= '<div class="'.$this->_htmlConfig['headclass'].'"><span>';
		$temp	.= $this->units[$html_id]['title'];
		$temp	.= '</span>';
		if ($this->ifadmin) {
			$temp	.= '<a href="javascript:void(0);" class="'.$this->_htmlConfig['editclass'].'">'.getLangInfo('other','stopic_edit').'</a>';
			$temp	.= '<a href="javascript:void(0);" class="'.$this->_htmlConfig['closeclass'].'">[x]</a>';
		}
		$temp	.= '</div>';
		return $temp;
	}
	
	/**
	 * ����ģ���html����
	 * 
	 * @access protected
	 * @param string $html_id ģ���html��id����
	 * @return string
	 */
	function _getHtmlData($html_id){
		$block_data	= $this->units[$html_id]['data'];
		list(,$block_type,) = explode("_", $html_id);
		//$blockid	= $this->units[$html_id]['block_id'];
		//$block	= $this->blocks[$blockid];
		return $this->service->getHtmlData($block_data,$block_type,$html_id);
	}
}

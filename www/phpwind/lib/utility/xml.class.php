<?php
!function_exists('readover') && exit('Forbidden');

require_once(R_P.'require/posthost.php');

/**
 * XML����
 * 
 * @package XML
 */
class XML{
	var $parser;
	var $XMLData;
	var $error;
	var $encode;
	var $stack;

	function __construct($encode='') {
		$this->encode	= $encode ? $encode : '';
		$this->XMLData	= '';
		$this->error	= '';
		$this->stack	= array();
	}

	function XML($encode=''){
		$this->__construct($encode);
	}

	/**
	 * ԴXML����
	 *
	 * @param string $data
	 */
	function setXMLData($data){
		$this->XMLData = trim($data);
	}

	/**
	 * ����ָ��URL��ȡXML����
	 *
	 * @param string $url
	 */
	function setXMLUrl($url){
		$this->XMLData = trim(PostHost($url));
	}

	/**
	 * Sets an option in an XML parser
	 *
	 * @param int $option
	 * @param mixed $value
	 */
	function setOption($option, $value) {
		xml_parser_set_option($this->parser, $option, $value);
	}

	/**
	 * �Ƿ�Ϊxml��ʽ�ļ�
	 *
	 * @return unknown
	 */
	function isXMLFile(){
		if(strpos(strtolower($this->XMLData),'<?xml')===false){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * ����XML����
	 *
	 * @param string $encode
	 */
	function setEncode($encode){
		$this->encode = $encode;
	}

	/**
	 * ȡ������XML����
	 *
	 * @return string
	 */
	function getEncode(){
		if(empty($this->encode)){
			$this->getXMLEncode();
		}
		return $this->encode;
	}

	/**
	 * ȡ��XML���ݵı���
	 *
	 */
	function getXMLEncode(){
		$start = strpos($this->XMLData,'<?xml');
		$end = strpos($this->XMLData,'>');
		$str = substr($this->XMLData,$start,$end-$start);
		$pos = strpos($str,'encoding');
		if($pos !== false){
			$str = substr($str,$pos);
			$pos = strpos($str,'=');
			$str = substr($str,$pos+1);
			$str = trim($str);
			$pos = 0;
			$this->encode = '';
			while(!empty($str[$pos]) && $str[$pos] != '?'){
				if($str[$pos] != '"' && $str[$pos] != "'"){
					$this->encode .= $str[$pos];
				}
				$pos++;
			}
		}
		return $this->encode;
	}
	/**
	 * Gets the current line number for the given XML parser
	 *
	 * @return int
	 */
	function getLineNumber() {
		return xml_get_current_line_number($this->parser);
	}

	/**
	 * Gets the current column number of the given XML parser
	 *
	 * @return int
	 */
	function getColumnNumber() {
		return xml_get_current_column_number($this->parser);
	}

	/**
	 * Gets the current byte index of the given XML parser
	 *
	 * @return int
	 */
	function getCharacterOffset() {
		return xml_get_current_byte_index($this->parser);
	}

	function _start_element($parser, $name, $attribs) {
		$tag = array('TagName'=>$name,'attribute'=>$attribs);
		if(empty($this->stack)){
			$tag['parent'] = null;
			$tag['depth'] = 1;
		}
		array_push($this->stack,$tag);
	}

	function _end_element($parser, $name) {
		$total = count($this->stack);
		if($total > 1){
			$this->stack[$total-1]['depth'] = $this->stack[$total-2]['depth']+1;
			$this->stack[$total-1]['parent'] = &$this->stack[$total-2];
			$this->stack[$total-2]['children'][] = $this->stack[$total-1];
			array_pop($this->stack);
		}

	}

	function _character_data($parser,$data) {
		$total = count($this->stack);
		if(isset($this->stack[$total-1]['data'])) {
			$this->stack[$total-1]['data'] .= trim($data);
		}else {
			$this->stack[$total-1]['data'] = trim($data);
		}
	}

	function _create_parser(){
		if(empty($this->parser)){
			$this->parser = xml_parser_create($this->encode);
			xml_parser_set_option($this->parser,XML_OPTION_CASE_FOLDING,0);
			xml_set_object($this->parser,&$this);
			xml_set_element_handler($this->parser,'_start_element','_end_element');
			xml_set_character_data_handler($this->parser,'_character_data');
		}
	}

	/**
	 * XML�������������
	 *
	 * @param string $data
	 * @return bool
	 */
	function parse($data = '') {
		$this->_create_parser();
		$data && $this->XMLData = $data;
		if(empty($this->XMLData)){
			$this->error = "XML error: XMLData is empty";
			return false;
		}

		if (!xml_parse($this->parser, $this->XMLData, true)) {
			$column = $this->getColumnNumber();
			$line = $this->getLineNumber();
			$errorCode = xml_get_error_code($this->parser);
			$errorString = xml_error_string($errorCode);
			$this->error = "XML error: $column at line $line: $errorString";
			return false;
		}
		xml_parser_free($this->parser);
		return true;
	}

	/**
	 * ���ظ��ڵ�
	 *
	 * @return array
	 */
	function getXMLRoot(){
		return $this->stack[0];
	}

	/**
	 * ���ؽ�����������ĵ�����
	 *
	 * @return array
	 */
	function getXMLDocument(){
		return $this->stack;
	}

	/**
	 * ����ָ�����ڵ��µ������ӽڵ�
	 *
	 * @param string $parentTagName
	 * @return array
	 */
	function getTagChild($parentTagName=''){
		if(empty($parentTagName)){
			return $this->stack[0]['children'];
		}else{
			$vector = array();
			$parentTag = $this->getElementsByTagName($parentTagName);
			foreach ($parentTag as $tag){
				if(count($tag['children'])){
					array_push($vector,$tag['children']);
				}
			}
			return $vector;
		}
	}

	function getTagByTagName($TagName){
		return XML::_getTagByTagName($this->stack[0],$TagName);
	}

	function _getTagByTagName($tree,$TagName){
		if ($tree['TagName'] == $TagName) {
			return $tree;
		}else{
			$total = count($tree['children']);
			for($i=0;$i<$total;$i++){
				$result = XML::_getTagByTagName($tree['children'][$i],$TagName);
				if($result){
					return $result;
				}
			}
		}
		return false;
	}

	function getElementsByTagName($TagName){
		$vector = array();
		XML::_getElementByTagName($this->stack[0],$TagName,$vector);
		return $vector;
	}

	function _getElementByTagName($tree,$TagName,&$vector){
		if ($tree['TagName'] == $TagName) {
			array_push($vector,$tree);
		}
		$total = count($tree['children']);
		for($i=0;$i<$total;$i++){
			XML::_getElementByTagName($tree['children'][$i],$TagName,$vector);
		}
	}

	/**
	 * ����������name���ҽڵ�
	 *
	 * @param array $stack
	 * @param string $name
	 * @return array
	 */
 	function getChildByName($stack,$name){
		$total = count($stack['children']);
		for($i=0;$i<$total;$i++){
			if($stack['children'][$i]['attribute']['name'] == $name){
				return $stack['children'][$i];
			}
		}
		return false;
	}

	function getChildByTagName($stack,$TagName){
		foreach ($stack['children'] as $key=>$value){
			if($value['TagName'] == $TagName){
				return $stack['children'][$key];
			}
		}
		return false;
	}

	/**
	 * ��ָ���ڵ��¸��ݱ�ǩ�������ӽڵ�
	 *
	 * @param array $stack
	 * @param string $TagName
	 * @return array
	 */
	function getChildrenByTagName($stack,$TagName){
		$vector = array();
		foreach ($stack['children'] as $key=>$value){
			if($value['TagName'] == $TagName){
				$vector[] = $stack['children'][$key];
			}
		}
		return $vector;
	}

	/**
	 * ��ǰ�ڵ���ӽڵ�
	 *
	 * @param array $stack
	 * @return array
	 */
	function getChild($stack){
		return $stack['children'];
	}

	/**
	 * �ڵ����Ա�
	 *
	 * @param array $stack
	 * @return array
	 */
	function getAttribute($stack){
		return $stack['attribute'];
	}

	/**
	 * �ڵ�ָ������ֵ
	 *
	 * @param array $stack
	 * @param string $name
	 * @return string
	 */
	function getProperty($stack,$name){
		return $stack['attribute'][$name];
	}

	/**
	 * �ڵ�����
	 *
	 * @param array $stack
	 * @return string
	 */
	function getData($stack){
		return $stack['data'];
	}

	/**
	 * ��ǰ�ڵ�ĸ��ڵ�
	 *
	 * @param array $stack
	 * @return array
	 */
	function getParent($stack){
		return $stack['parent'];
	}

	/**
	 * �ڵ��ǩ����
	 *
	 * @param array $stack
	 * @return string
	 */
	function getTagName($stack){
		return $stack['TagName'];
	}
	/**
	 * ���ش�����ʾ
	 *
	 * @return string
	 */
	function getXMLError(){
		return $this->error;
	}
}
?>
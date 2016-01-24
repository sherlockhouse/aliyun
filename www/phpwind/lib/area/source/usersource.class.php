<?php
!defined('P_W') && exit('Forbidden');
require_once (R_P.'lib/base/systemdata.php');
class PW_UserSource extends SystemData {
	var $_element;
	var $_lang = array(
		'title'	=> '�û���',
	);
	function getSourceData($config,$num) {
		$tagsService = L::loadClass('memberTagsService', 'user');
		$config = $this->_initConfig($config);
		if ($config['usersort'] == 'newUser') {
			$userService = L::loadClass('UserService', 'user');
			$data = $userService->findNotBannedNewUsers($num);
			$data = $tagsService->addUserTags($data);
			return $this->_cookData($data) ;
		}
		$element = $this->_getElement();
		$data = $element->userSort($config['usersort'],$num);
		return $tagsService->addUserTags($data); 
	}
	
	function getRelateType() {
		return false;
	}
	
	function getSourceConfig() {
		return array(
			'usersort' 	=> array(
				'name' 	=> '��Ա����',
				'type'	=> 'select',
				'value'	=> array(
					'money'		=>	$GLOBALS['db_moneyname'],
					'rvrc'		=>	$GLOBALS['db_rvrcname'],
					'credit'	=>	$GLOBALS['db_creditname'],
					'currency'	=>	$GLOBALS['db_currencyname'],
					'todaypost'	=>	'���շ���',
					'monthpost'	=>	'һ�·���',
					'postnum'	=>	'��������',
					'monoltime'	=>	'һ������',
					'onlinetime'=>	'��������',
					'newUser'  =>	'���»�Ա',
					'postMostUser'  =>	'��Ծͼ��',		
				),
			),
		);
	}
	
	/**
	 * 
	 * ���ݴ���
	 * @param int $fid
	 * @return 
	 */
	function _cookData($data) {
		if (!S::isArray($data)) return array();
		require_once(R_P.'require/showimg.php');
		foreach ($data as $k => $v){
			$tem = array();
			$tem['url'] 	= USER_URL.$v['uid'];
			$tem['title'] 	= $v['username'];
			$tem['value'] 	= $v['uid'];
			if (array_key_exists('icon',$v)) {
				$pic = showfacedesign($v['icon'],true,'s');
				if (is_array($pic)) {
					$tem['image'] = $pic[0];
				} else {
					$tem['image'] = '';
				}
			} else {
				$tem['image'] = '';
			}
			$tem['addition']= $v;
			$userInfo[] = $tem;
		}
		return $userInfo;
	}
	
	function _getElement() {
		if (!$this->_element) {
			$this->_element = L::loadClass('element');
		}
		return $this->_element;
	}
	
	function _initConfig($config) {
		$temp = array();
		$temp['usersort'] = $config['usersort'];

		return $temp;
	}
}
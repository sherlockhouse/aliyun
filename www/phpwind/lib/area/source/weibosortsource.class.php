<?php
!defined('P_W') && exit('Forbidden');
require_once (R_P . 'lib/base/systemdata.php');
class PW_WeiboSortSource extends SystemData {
	
	/**
	 * 
	 * ����������Ϣ��û�����������
	 * @param array $config 
	 * @param int $num
	 */
	function getSourceData($config,$num) {
		$config = $this->_initConfig($config);
		return $this->_getDataBySortType($config['sorttype'],$num);
	}
	
	/**
	 * 
	 * ��ȡ����
	 * @param array $config 
	 * @param array $sortType
	 * @param int $num
	 */
	function _getDataBySortType($sortType,$num) {
		$weiboService = $this->_getWeiboService();
		$topicService = $this->_getTopicService();
		$attentionService = $this->_getAttentionService();
		$data = array();
		switch ($sortType) {
			case 'hottransmit':
				$data = $weiboService->getHotTransmit($num);
				break;
			case 'hotcomment':
				$data = $weiboService->getHotComment($num);
				break;
			case 'hottopic';
				$data = $topicService->getWeiboHotTopics();
				break;
			case 'hotuser';
				$data = $attentionService->getTopFansUsers($num);
				break;
		}
		return  $this->_cookData($data);
	}
	
	function getSourceConfig() {
		return array(
			'sorttype' => array(
				'name' => '΢������',
				'type' => 'select',
				'value' => array(
					'hottransmit'	=> '����ת��',
					'hotcomment'	=> '��������',
					'hottopic'		=> '���Ż���',
					'hotuser'		=> '������˿',
				)
			)
		);
	}

	/**
	 * ��ʽ������ͳһ���
	 * @param array $data
	 * @return array
	 */
	function _cookData($data) {
		$cookData = array();
		foreach($data as $k => $v){	
			if(isset($v['password'])) unset($v['password']);
			if($v['topicid']){
				if (strpos($v['topicname'],'[s:') !== false && strpos($v['topicname'],']') !== false) {
					unset($data[$k]);
					continue;
				}
				$v['title']	 	= $v['descrip'] = strip_tags($v['topicname']);
				$v['url']		= 'apps.php?q=weibo&do=topics&topic='.$v['topicname'];
				$v['postdate']  = get_date($v['crtime'],'Y-m-d');
			}elseif($v['mid']){
				$v['url'] 	= 'apps.php?q=weibo&do=detail&mid='.$v['mid'].'&uid='.$v['uid'];
				$v['title']	= $v['extra']['title'] ? strip_tags($v['extra']['title']) : strip_tags($v['content']);
				$v['descrip'] = strip_tags($v['content']);
				$v['authorurl']	= 'u.php?uid='.$v['uid'];
				$v['author'] = $v['username'];
				$v['authorid'] = $v['uid'];

				$v['postdate']  = $v['postdate_s'];
				if(S::isArray($v['extra']['photos'])){
					$image = $v['extra']['photos'][0];
					$temp = geturl($image['path']);
					$v['image'] = $temp[0] ? $temp[0] : '';
				}
				$pic = showfacedesign($v['icon'],true,'s');
				$v['icon'] = S::isArray($pic) ? $pic[0] : '';
			}else{
				$v['url'] 	= 'u.php?uid='.$v['uid'];
				$v['title'] = $v['username'];
				$v['uid'] = $v['uid'];
				$v['tags']	= $v['tags'] ? $v['tags'] : "TA��û�б�ǩ";
				$v['image'] = $v['icon'] ? $v['icon'] : '';
			}
			if(!$v['title']){
				unset($data[$k]);
				continue;
			}
			$cookData[$k] = $v;
		}
		return $cookData;
	}
	
	/**
	 * @param array $config
	 * @return array
	 */
	function _initConfig($config) {
		$temp = array();
		$temp['sorttype'] = $config['sorttype'];
		return $temp;
	}
	
	function _getWeiboService() {
		return L::loadClass('weibo', 'sns'); /*@var PW_Weibo*/
	}
	function _getTopicService() {
		return L::loadClass('topic', 'sns'); /*@var PW_Topic*/
	}
	function _getAttentionService(){
		return L::loadClass('attention', 'friend'); /*@var PW_Topic*/
	}
}
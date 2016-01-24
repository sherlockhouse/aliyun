<?php
!defined('P_W') && exit('Forbidden');

class PW_MedalService {
	/**
	 * ��ȡ����ѫ��
	 * 
	 * @return array
	 */
	function getAllMedals() {
		return $this->_getAllMedal();
	}
	/**
	 * ��ȡ���п�����ѫ�£��л��棩
	 * 
	 * @return array
	 */
	function getAllOpenMedals() {
		$data = pwCache::getData($this->_getAllOpenMedalsCacheKey(),false);
		return $data ? $this->_cookCacheData($data) : $this->_initAllOpenMedals();
	}
	/**
	 * ��ȡ�����Զ�ѫ��(������)
	 * @param string $type ��������
	 * @return array
	 */
	function getAllOpenAutoMedals($type = '') {
		$data = pwCache::getData($this->_getAllOpenAutoMedalsCacheKey(),false);
		$result = $data ? $this->_cookCacheData($data) : $this->_initAllOpenAutoMedals();
		if (!$type) return $result;
	
		$temp = array();
		foreach ($result as $key=>$value) {
			if ($value['associate'] == $type) {
				$temp[] = $value;
			}
		}
		return $temp;
	}
	
	/**
	 * ��ȡ�������ֶ��䷢��ѫ��(�л���)
	 * 
	 * @return array
	 */
	function getAllOpenManualMedals() {
		$data = pwCache::getData($this->_getAllOpenManualMedalsCacheKey(),false);
		return $data ? $this->_cookCacheData($data) : $this->_initAllOpenManualMedals();
	}
	
	/**
	 * ��ȡһ���û�������ѫ��
	 * @param int $uid
	 * @param ��ȡ���� $type {all:��ȡ���У������Լ�δ�õ���ѫ��,own:��ȡ�Լ��ѻ�ȡ��ѫ��}
	 * @param string $medals
	 * @return array('12'=>array('name'=>'ѫ������','smallimage'=>'images/2.jpg'),'14'=>array('name'=>'ѫ������','smallimage'=>'images/1.jpg'))
	 */
	function getUserMedals($uid,$type='own',$cacheMedals='') {
		$medalInfos = $this->getAllOpenMedals();
		$result = $unHave = array();

		$medals = $cacheMedals ? explode(',', $cacheMedals) : $this->getUserMedalIds($uid);
		foreach ($medalInfos as $key=>$value) {
			$value['is_have'] = in_array($value['medal_id'],$medals);
			unset($value['allow_group']);
			if ($type=='all' && !$value['is_have']) {
				$unHave[] = $value;
			}
			if ($value['is_have']) $result[] = $value;
		}
		//�����쳣����
		if (!$cacheMedals) {
			$userinfo = $this->_getUserInfo($uid);
			$cacheMedals = $userinfo['medals'];
		}
		$cacheMedals = $cacheMedals ? explode(',', $cacheMedals) : array();
		$change = 0;
		if (count($cacheMedals)!=count($medals)) $change = 1;
		foreach ($cacheMedals as $value) {
			if (!isset($medalInfos[$value]) || !in_array($value,$medals)) {
				if (!isset($medalInfos[$value])) {
					$tempMedal = $this->getMedal($value);
					if ($tempMedal && $tempMedal['type']==0) continue; 
				}
				$change = 1;
				$medalAwardDb = $this->_getMedalAwardDb();
				$medalAwardDb->deleteByMedalIdAndUid($value, $uid);
			}
		}
		if ($change) $this->_updateUserMedal($uid, $medals,2);
		return $type==all ? array_merge($result,$unHave) : $result;
	}
	/**
	 * ��ȡ�û�������ѫ��id
	 * @param int $uid
	 * @return array
	 */
	function getUserMedalIds($uid) {
		$uid = (int) $uid;
		$medalAwardDb = $this->_getMedalAwardDb();
		return $medalAwardDb->getUserMedals($uid);
	}
	/**
	 * ��ȡ�û������ѫ��
	 * @param int $uid
	 * @return array('12','23','11');  ѫ��id�б�
	 */
	function getUserApplys($uid) {
		$applyDb = $this->_getMedalApplyDb();
		return $applyDb->getUserMedalids($uid);
	}
	/**
	 * ���һ��ѫ��
	 * type{0:ϵͳѫ��,1:�Զ�ѫ��,2:�ֶ�ѫ��}
	 * array(
	 * 	'identify'=>'new_1',//�Զ�ѫ�º��ֶ�ѫ��������Բ���Ҫ����
	 *	'name'	=> '�µĲ���',
	 *	'descrip'=>'����������������',
	 *	'type'	=> '0',
	 *	'image' => 'xx.jpg',
	 *	'allow_group' => array(1,2,3,5)
	 *	'associate'=>'continue_login',
	 *	'confine'=>'20'
	 * );
	 * @param array $medal
	 * @return
	 */
	function addMedal($medal) {
		if (!S::isArray($medal)) return array(false,'��������');
		$medal = $this->_cookMedalDataForAdd($medal);
		if (!$medal) return array(false,'��������');
		if (!isset($medal['identify']) || !$medal['identify']) {
			$medal['identify'] = $this->_produceIdentify();
		} else {
			if ($this->getMedalByIdentify($medal['identify'])) return array(false,'�ñ�ʶ�Ѵ���');
		}
		$medalInfoDb = $this->_getMedalInfoDb();
		$result = $medalInfoDb->insert($medal);
		$this->_initAllCache();
		return $result;
	}
	
	/**
	 * ͨ��id��ȡһ��ѫ����Ϣ
	 * @param int $medalId
	 * @return array
	 */
	function getMedal($medalId) {
		$medalInfoDb = $this->_getMedalInfoDb();
		$result = $medalInfoDb->get($medalId);
		return $this->_initInfoForView($result);
	}
	/**
	 * ͨ��Ψһ��ʶ��ȡһ��ѫ����Ϣ
	 * @param string $identify	Ψһ��ʶ
	 * @return array
	 */
	function getMedalByIdentify($identify) {
		$medalInfoDb = $this->_getMedalInfoDb();
		$result = $medalInfoDb->getByIdentify($identify);
		return $this->_initInfoForView($result);
	}
	/**
	 * �޸�һ��ѫ��
	 * @param int $medalId
	 * @param array $medal
	 * @return bool
	 */
	function updateMedal($medalId,$medal) {
		$medal = $this->_cookMedalDataForUpdate($medal);
		if (!$medal) return array(false,'��������');
		$medalInfoDb = $this->_getMedalInfoDb();
		$oldMedalInfo = $medalInfoDb->get($medalId);
		if ($oldMedalInfo['type']==2 && $medal['confine'] && $medal['confine']!=$oldMedalInfo['confine']) {
			$medalAwardDb = $this->_getMedalAwardDb();
			$medalAwardDb->updateDeadline($medalId, $medal['confine']*86400);
		}
		
		if ($oldMedalInfo['type']!=0 && $oldMedalInfo['is_open'] && array_key_exists('is_open',$medal) && !$medal['is_open']) {
			$this->_recoverUserMedals($medalId);
		}
		$result = $medalInfoDb->update($medalId, $medal);
		$this->_initAllCache();
		return $result;
	}
	
	/**
	 * ɾ��һ��ѫ��
	 * @param int $medalId
	 * @return bool
	 */
	function deleteMedal($medalId) {
		$medalInfoDb = $this->_getMedalInfoDb();
		$result = $medalInfoDb->delete($medalId);
		$this->_recoverUserMedals($medalId);
		
		$this->_initAllCache();
		return $result;
	}
	/**
	 * ͨ��Ψһ��ʶɾ��һ��ѫ������
	 * @param string $identify
	 * @return
	 */
	function deleteMedalByIdentify($identify) {
		$medalInfoDb = $this->_getMedalInfoDb();
		$medalInfo = $medalInfoDb->getByIdentify($identify);
		if (!$medalInfo) return false;
		return $this->deleteMedal($medalInfo['medal_id']);
	}
	/**
	 * ��ȡ�Զ�ѫ�¿������õ��������
	 * @return array
	 */
	function getAutoMedalType() {
		return array(
			'continue_login'=>'������¼����',
			'continue_post'=>'������������',
			'continue_thread_post'=>'��������������',
			'post'=>'������',
			//'thread_post'=>'������',
			'shafa'=>'��ɳ����',
			'fans'=>'��˿��',
		);
	}
	/**
	 * �Զ��䷢�ͻ����Զ�ѫ��
	 * $medalService->runAutoMedal($userInfo,'continue_post',5,1)//����ʹ��
	 * $medalService->runAutoMedal($uid,'fans','fans',1)	//��һ�������������û�uid  ���������������Ǹ��û����ĸ��ֶ�
	 * @param array|int $userInfo	�û���Ϣ|uid
	 * @param string $medalType ��Ϊ����
	 * @param int $num
	 * @param int $change
	 * @return
	 */
	function runAutoMedal($userInfo,$medalType,$num,$change) {
		if (is_numeric($userInfo)) $userInfo=$this->_getUserInfo($userInfo);
		if (!is_numeric($num) && isset($userInfo[$num])) $num = $userInfo[$num];
		$num = (int) $num;
		$autoMedals = $this->getAllOpenAutoMedals($medalType);
		if (!$userInfo || !$autoMedals) return false;

		if ($change>=0) {
			$this->_autoAwardMedal($userInfo,$autoMedals,$num);	//�Զ��䷢ѫ��
		} elseif ($change<0) {
			$this->_autoRecoverMedal($userInfo,$autoMedals,$num);	//�Զ�����ѫ��
		}
	}
	/**
	 * �������չ��ڵ��ֶ�ѫ��
	 */
	function recoverOverdueMedals() {
		$awardMedalDb = $this->_getMedalAwardDb();
		$result = $awardMedalDb->getAllOverdues();
		foreach ($result as $key=>$value) {
			$this->recoverMedal($value['award_id'],'ѫ�¹��ڣ�ϵͳ�Զ��ջ�');
		}
	}
	/**
	 * ͨ��awardId��ȡ�ѻ�ȡ��ѫ����Ϣ
	 * @param int $awardId
	 * @return array
	 */
	function getAwardMedalById($awardId) {
		$awardId = (int) $awardId;
		if (!$awardId) return array();
		$awardMedalDb = $this->_getMedalAwardDb();
		return $awardMedalDb->get($awardId);
	}
	/**
	 * ͨ��uid��ѫ��id��ȡ�ѻ�ȡ��ѫ����Ϣ
	 * @param int $uid
	 * @param int $medalId
	 * @return array
	 */
	function getAwardMedalByUidAndMedalId($uid,$medalId) {
		$uid = (int) $uid;
		$medalId = (int) $medalId;
		$awardMedalDb = $this->_getMedalAwardDb();
		return $awardMedalDb->getByUidAndMedalId($uid, $medalId);
	}
	/**
	 * ��һ���û��䷢ѫ��
	 * type{0:ϵͳ�Զ��䷢,1:�û�����,2:����Ա�䷢}
	 * @param int $uid
	 * @param int $medalId
	 * @param bool $isApply �Ƿ�������ѫ��
	 * @param array $medalInfo ѫ����Ϣ����ѡ��
	 * @
	 * @return bool
	 */
	function awardMedal($uid,$medalId,$isApply=false,$medalInfo = array(),$descrip='') {
		global $timestamp;
		if ($this->getAwardMedalByUidAndMedalId($uid,$medalId)) return array(false,'ѫ���Ѵ���');
		$medalInfo = $medalInfo ? $medalInfo : $this->getMedal($medalId);
		if (!$medalInfo) return array(false,'ѫ�²�����');
		$awardData = array('uid'=>$uid,'medal_id'=>$medalId,'timestamp'=>$timestamp);
		if ($medalInfo['type']==2 && $medalInfo['confine']) $awardData['deadline'] = $timestamp+$medalInfo['confine']*86400;
		$awardData['type'] = $medalInfo['type'] == 2 ? ($isApply ? 1 : 2) : 0;
		
		$awardMedalDb = $this->_getMedalAwardDb();
		$temp = $awardMedalDb->insert($awardData);
		$this->_updateUserMedal($uid, $medalId);
		$this->_sendAwardNotice($uid, $medalInfo, $awardData['type'], $descrip);
		return $temp;
	}
	/**
	 * ��һ���û��䷢ѫ�£�ͨ��ѫ��Ψһ��ʶ��
	 * @param int $uid
	 * @param int $medalId	
	 * @param bool $isApply ʱ��ͨ��ѫ������
	 * @return bool
	 */
	function awardMedalByIdentify($uid,$identify,$isApply = false) {
		$medalInfo = $this->getMedalByIdentify($identify);
		if (!$medalInfo) return array(false,'ѫ�²���������');
		return $this->awardMedal($uid,$medalInfo['medal_id'],$isApply,$medalInfo);
	}
	/**
	 * ��ȡ�ѻ�ȡѫ�µ��û��б�
	 * @param array $condition
	 * array(
	 * 	'uid'=>1,
	 *  'medal_id'=>2,	//ѫ��id
	 *  'type'=>0,		//type{0:ϵͳ�Զ�����,1:�û�����ͨ��,2:����Ա�ֶ�����}
	 * )
	 * @param int $page
	 * @param int $perpage
	 * @return array($list,$count)
	 */
	function getAwardMedalUsers($condition,$page,$prePage = 20) {
		$page = (int) $page;
		$page<=0 && $page =1;
		$awardMedalDb = $this->_getMedalAwardDb();
		$result = $awardMedalDb->getAll($condition,$page,$prePage);
		$result = $this->_decorateUsername($result);
		$count = $awardMedalDb->count($condition);
		return array($result,$count);
	}
	function _decorateUsername($data) {
		$uids = array(); 
		if (!$data) return $data;
		foreach ($data as $v) {
			$uids[] = $v['uid'];
		}
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService*/
		$userInfos = $userService->getByUserIds($uids);
		foreach ($data as $key=>$value) {
			$value['username'] = $userInfos[$value['uid']]['username'];
			$data[$key] = $value;
		}
		return $data;
	}
	/**
	 * ��ȡ����ѫ�µ��û��б�
	 * @param array $condition
	 * array(
	 * 	'uid'=>1,
	 *  'medal_id'=>2,	//ѫ��id
	 * )
	 * @param int $page
	 * @param int $perpage
	 * @return array($list,$count)
	 */
	function getApplyMedalUsers($condition,$page,$prePage=20) {
		$page = (int) $page;
		$page<=0 && $page =1;
		$applyDb = $this->_getMedalApplyDb();
		$result = $applyDb->getAll($condition,$page,$prePage);
		$result = $this->_decorateUsername($result);
		$count = $applyDb->count($condition);
		return array($result,$count);
	}
	/**
	 * ͨ��uid��ѫ��id��ȡ����ѫ�µ���Ϣ
	 * @param int $uid
	 * @param int $medalId
	 * @return
	 */
	function getApplyByUidAndMedalId($uid,$medalId) {
		$applyDb = $this->_getMedalApplyDb();
		return $applyDb->getByUidAndMedalId($uid,$medalId);
	}
	/**
	 * ����һ��ѫ��
	 * @param int $uid
	 * @param int $medalId
	 * @param int $reason
	 * @return
	 */
	function applyMedal($uid,$medalId) {
		global $timestamp,$db_md_ifapply;
		$uid = (int) $uid;
		$medalId = (int) $medalId;
		if (!$uid || !$medalId) return array(false,'��������');
		if ($this->getAwardMedalByUidAndMedalId($uid, $medalId)) return array(false,'���Ѿ�ӵ�и�ѫ��');
		if ($this->getApplyByUidAndMedalId($uid, $medalId)) return array(false,'���Ѿ��������ѫ��');
		$medalInfo = $this->getMedal($medalId);
		if (!$db_md_ifapply || !$medalInfo || $medalInfo['type']!=2 || !$medalInfo['is_apply']) return array(false,'��ѫ�²���������');
		
		$userInfo = $this->_getUserInfo($uid);
		if (!$this->_checkAllowGroup($userInfo['groupid'], $medalInfo['allow_group'])) array(false,'�����ڵ��û��鲻������ѫ��');
		$data = array('uid'=>$uid,'medal_id'=>$medalId,'timestamp'=>$timestamp);
		$applyDb = $this->_getMedalApplyDb();
		return $applyDb->insert($data);
	}
	/**
	 * ͨ��һ��ѫ������
	 * @param int $applyId
	 * @return
	 */
	function adoptApplyMedal($applyId) {
		$applyDb = $this->_getMedalApplyDb();
		$applyInfo = $applyDb->get($applyId);
		if (!$applyInfo) return array(false,'��Ϣ������');
		$result = $this->awardMedal($applyInfo['uid'],$applyInfo['medal_id'],1);
		if (!$result) return array(false,'δ֪����');
		$applyDb->delete($applyId);
		return $result;
	}
	/**
	 * �ܾ�һ��ѫ������
	 * @param int $applyId
	 * @return
	 */
	function refuseApplyMedal($applyId) {
		$applyDb = $this->_getMedalApplyDb();
		$applyInfo = $applyDb->get($applyId);
		if (!$applyInfo) return false;
		$this->_sendRefuseNotice($applyInfo['uid'], $applyInfo['medal_id']);
		return $applyDb->delete($applyId);
	}
	/**
	 * ͨ���û�id��ѫ��id����һ��ѫ��
	 * @param int $uid
	 * @param int $medalId
	 * @return
	 */
	function recoverMedalByUidAndMedalId($uid,$medalId,$descrip='') {
		$uid = (int) $uid;
		$medalId = (int) $medalId;
		if (!$uid || !$medalId) return array(false,'��������');
		$awardMedalDb = $this->_getMedalAwardDb();
		$awardInfo = $awardMedalDb->getByUidAndMedalId($uid, $medalId);
		if (!$awardInfo) return array(false,'��������');
		return $this->_recoverMedal($awardInfo,$descrip);
	}
	/**
	 * ͨ���û�id��ѫ��Ψһ��ʶ����һ��ѫ��
	 * @param int $uid
	 * @param int $medalId
	 * @return
	 */
	function recoverMedalByUidAndIdentify($uid,$identify,$descrip='') {
		$medalInfo = $this->getMedalByIdentify($identify);
		if (!$medalInfo) return array(false,'��������');
		return $this->recoverMedalByUidAndMedalId($uid,$medalInfo['medal_id'],$descrip);
	}
	/**
	 * ����һ��ѫ��
	 * @param int $id
	 * @return
	 */
	function recoverMedal($id,$descrip='') {
		$awardMedalDb = $this->_getMedalAwardDb();
		$awardInfo = $awardMedalDb->get($id);
		if (!$awardInfo) return array(false,'��������');
		return $this->_recoverMedal($awardInfo,$descrip);
	}
	
	/*------------˽�к���---------------*/
	function _autoAwardMedal($userInfo,$autoMedals,$num) {
		foreach ($autoMedals as $value) {
			if ($num>=$value['confine'] && $this->_checkAllowGroup($userInfo['groupid'], $value['allow_group']) && !$this->_checkIsHaveMedal($userInfo['medals'],$value['medal_id'])) {
				$this->awardMedal($userInfo['uid'], $value['medal_id'],false,$value);
			}
		}
	}
	
	function _autoRecoverMedal($userInfo,$autoMedals,$num) {
		foreach ($autoMedals as $value) {
			if ($num<$value['confine'] && $this->_checkIsHaveMedal($userInfo['medals'],$value['medal_id'])) {
				$this->recoverMedalByUidAndMedalId($userInfo['uid'], $value['medal_id']);
			}
		}
	}
	
	function _checkIsHaveMedal($medals,$medalId) {
		if (!$medals) return false;
		$medals = explode(',', $medals);
		return in_array($medalId,$medals);
	}
	
	function _checkAllowGroup($groupid,$config) {
		return !$config || in_array($groupid,$config);
	}
	
	/**
	 * ����members����û���ѫ������
	 * @param int $uid
	 * @param int|array $medalId
	 * @param int $type {0:�䷢,1:����,2:����}
	 * @return
	 */
	function _updateUserMedal($uid,$medalId,$type=0) {
		$userinfo = $this->_getUserInfo($uid);
		$oldMedals = $userinfo['medals'] ? explode(',', $userinfo['medals']) : array();
		$medalId = (array)$medalId;
		if (!$type) {
			foreach ($medalId as $value) {
				if (in_array($value,$oldMedals)) continue;
				$oldMedals[] = $value;
			}
		} elseif ($type == 2) {
			$oldMedals = $medalId;
		} else {
			foreach ($oldMedals as $key=>$value) {
				if (in_array($value,$medalId)) unset($oldMedals[$key]);
			}
		}
		$userService = L::loadClass('userservice','user');
		$userService->update($uid,array('medals'=>implode(',', $oldMedals)));
	}
	
	function _getUserInfo($uid) {
		global $winduid,$winddb;
		static $result = array();
		if (isset($result[$uid])) return $result[$uid];
		if ($winduid == $uid && $winddb) {
			return $winddb;
		}
		$userService = L::loadClass('userservice','user');
		$result[$uid] = $userService->get($uid,true,true);
		return $result[$uid];
	}
	
	function _recoverMedal($awardInfo,$descrip='') {
		global $timestamp;
		if (!S::isArray($awardInfo)) return array(false,'��������');
		
		$awardMedalDb = $this->_getMedalAwardDb();
		$result = $awardMedalDb->delete($awardInfo['award_id']);
		if (!$result) return array(false,'δ֪����');
		$this->_updateUserMedal($awardInfo['uid'], $awardInfo['medal_id'],1);
		$medalLogDb = $this->_getMedalLogDb();
		$logData = array('award_id'=>$awardInfo['award_id'],'medal_id'=>$awardInfo['medal_id'],'timestamp'=>$timestamp,'type'=>1,'descrip'=>$descrip);
		$medalLogDb->insert($logData);
		$this->_sendRecoverNotice($awardInfo['uid'],$awardInfo['medal_id'],$descrip);
		return $result;
	}
	function _cookMedalDataForAdd($medal) {
		$medal['type'] = (int) $medal['type'];
		if (!in_array($medal['type'],array(0,1,2))) $medal['type'] = 0;
		
		if (isset($medal['sortorder'])) $medal['sortorder'] = (int) $medal['sortorder'];
		if (isset($medal['is_apply'])) $medal['is_apply'] = (int) $medal['is_apply'];
		if (isset($medal['is_open'])) $medal['is_open'] = (int) $medal['is_open'];
		if ($medal['type']==1) {
			$allowTypes = $this->getAutoMedalType();
			if (!isset($medal['associate']) || !$medal['associate'] || in_array($medal['associate'], $allowTypes)) return array();
		} else {
			$medal['associate'] = '';
		}
		if ($medal['type']==0 && (!isset($medal['identify']) || !$medal['identify'])) return array();
		
		return $medal;
	}
	
	function _cookMedalDataForUpdate($medal) {
		if (isset($medal['sortorder'])) $medal['sortorder'] = (int) $medal['sortorder'];
		if (isset($medal['is_apply'])) $medal['is_apply'] = (int) $medal['is_apply'];
		if (isset($medal['is_open'])) $medal['is_open'] = (int) $medal['is_open'];
		if (isset($medal['type'])) unset($medal['type']);
		if (isset($medal['associate'])) unset($medal['associate']);
		if (isset($medal['identify'])) unset($medal['identify']);
		return $medal;
	}
	/**
	 * ��ȡ���п�����ѫ��
	 * 
	 * @return array
	 */
	function _getAllOpenMedals() {
		return $this->_getAllMedal(array('is_open'=>1));
	}
	/**
	 * ��ȡ�����Զ�ѫ��
	 * @return array
	 */
	function _getAllOpenAutoMedals() {
		return $this->_getAllMedal(array('is_open'=>1,'type'=>1),'confine');
	}
	/**
	 * ��ȡ�������ֶ��䷢��ѫ��
	 * 
	 * @return array
	 */
	function _getAllOpenManualMedals() {
		return $this->_getAllMedal(array('is_open'=>1,'type'=>2));
	}
	function _getAllMedal($condition = array(),$order = false) {
		$medalInfoDb = $this->_getMedalInfoDb();
		$result = $medalInfoDb->getAll($condition,$order);
		foreach ($result as $key=>$value) {
			$result[$key] = $this->_initInfoForView($value);
		}
		return $result;
	}
	
	function _cookCacheData($data) {
		$result = array();
		foreach ($data as $key=>$value) {
			$temp = (int) str_replace('_', '', $key);
			$result[$temp] = $value;
		}
		return $result;
	}
	
	function _initInfoForView($info) {
		if (!$info) return $info;
		$info['bigimage'] = $this->_getMedalImage($info['image'],'big');
		$info['smallimage'] = $this->_getMedalImage($info['image'],'small');
		return $info;
	}
	
	function _getMedalImage($image,$type) {
		global $db_picpath;
		$basePath = $db_picpath ? $db_picpath : 'images';
		$type = in_array($type,array('big','small')) ? $type : 'big';
		if (strpos($image, 'http:')===false) {
			return Pcv($basePath.'/medal/'.$type.'/'.$image);
		} else {
			list($big,$small) = explode('|', $image);
			return $type == 'big' ? $big : $small;
		}
	}
	/**
	 * ������ѫ�»���key
	 * @return string
	 */
	function _getAllOpenMedalsCacheKey() {
		return D_P . 'data/bbscache/medal_all_open_medals.php';
	}
	/**
	 * �����������ֶ�ѫ�µĻ���key
	 * @return string
	 */
	function _getAllOpenManualMedalsCacheKey() {
		return D_P . 'data/bbscache/medal_all_open_manual_medals.php';
	}
	/**
	 * �����������Զ�ѫ�µĻ���key
	 * @return string
	 */
	function _getAllOpenAutoMedalsCacheKey() {
		return D_P . 'data/bbscache/medal_all_open_auto_medals.php';
	}
	function _initAllCache() {
		$this->_initAllOpenManualMedals();
		$this->_initAllOpenMedals();
		$this->_initAllOpenAutoMedals();
		require_once R_P.'admin/cache.php';
		updatecache_mddb();
	}
	function _initAllOpenMedals() {
		$file = $this->_getAllOpenMedalsCacheKey();
		$data = $this->_getAllOpenMedals();
		pwCache::setData($file,$data,true);
		return $data;
	}
	function _initAllOpenManualMedals() {
		$file = $this->_getAllOpenManualMedalsCacheKey();
		$data = $this->_getAllOpenManualMedals();
		pwCache::setData($file,$data,true);
		return $data;
	}
	function _initAllOpenAutoMedals() {
		$file = $this->_getAllOpenAutoMedalsCacheKey();
		$data = $this->_getAllOpenAutoMedals();
		pwCache::setData($file,$data,true);
		return $data;
	}
	/**
	 * ����һ��Ψһ��ʶ
	 * @return string
	 */
	function _produceIdentify() {
		$identify = $this->_generateRand(15);
		if ($this->getMedalByIdentify($identify)) return $this->_produceIdentify();
		return $identify;
	}
	/**
	 * ����һ������ַ���
	 * @param int $length
	 * @return string
	 */
	function _generateRand($length){
		$c= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		srand((double)microtime()*1000000);
		$rand = '';
		for($i=0; $i<$length; $i++) {
			$rand.= $c[rand()%strlen($c)];
		}
		return $rand;
	}
	
	function _recoverUserMedals($medalId) {
		$medalAwardDb = $this->_getMedalAwardDb();
		$medalAwardDb->deleteByMedalId($medalId);
		$medalApplyDb = $this->_getMedalApplyDb();
		$medalApplyDb->deleteByMedalId($medalId);
	}
	
	function _sendAwardNotice($uid, $medalInfo, $type, $adminDescrip = '') {
		$userInfo = $this->_getUserInfo ( $uid );
		$title = '��ϲ�������ѫ��';
		$descrip = "��ϲ������� " . $this->_medalUrl($medalInfo ['name']) . " ѫ��\r\n�䷢ԭ��";
		if ($type==0) {
			if ($medalInfo['type']==0) {
				$descrip .= "�������� " . $medalInfo ['name'] . "ѫ�»�ȡ���� ";
			} elseif ($medalInfo['type']==1) {
				$types = $this->getAutoMedalType();
				if ($types[$medalInfo['associate']]) $descrip .= "����".$types[$medalInfo['associate']]."�ﵽ��".$medalInfo['confine']."���ط���ѫ�����ȹ�����";
			}
		} elseif ($type==1) {
			$descrip .= "����ѫ��������ͨ����";
		} elseif ($type==2) {
			global $admin_name;
			$descrip .= $adminDescrip ? $adminDescrip : "��������ͻ�����֣�����Ա".$admin_name."��������һ�� ".$medalInfo ['name']." ѫ��";
		}
		M::sendNotice (
			array ($userInfo ['username']),
			array ('title' => $title, 'content' => $descrip )
		);
	}
	function _sendRecoverNotice($uid, $medalId, $adminDescrip='') {
		$userInfo = $this->_getUserInfo ( $uid );
		$medalInfo = $this->getMedal($medalId);
		$title = 'ѫ�»���֪ͨ';
		$descrip = "���� " . $this->_medalUrl($medalInfo ['name'],false) . " ѫ�±�����\r\n����ԭ��";
		if ($medalInfo['type']==0 || $medalInfo['type']==1) {
			if ($medalInfo['type']==1) {
				$types = $this->getAutoMedalType();
				if ($types[$medalInfo['associate']]) $descrip .= "����".$types[$medalInfo['associate']]."����ѫ���趨ֵ".$medalInfo['confine'];
			}
		} elseif ($medalInfo['type']==2) {
			global $admin_name;
			$descrip .= $adminDescrip ? $adminDescrip : "����Ա".$admin_name."���������� ".$medalInfo ['name']." ѫ��";
		}
		M::sendNotice (
			array ($userInfo ['username']),
			array ('title' => $title, 'content' => $descrip )
		);
	}
	function _sendRefuseNotice($uid, $medalId) {
		$userInfo = $this->_getUserInfo ( $uid );
		$medalInfo = $this->getMedal($medalId);
		$title = '����ѫ������δͨ��';
		$descrip = "���� " . $this->_medalUrl($medalInfo ['name'],false) . " ѫ������δͨ��";
		M::sendNotice (
			array ($userInfo ['username']),
			array ('title' => $title, 'content' => $descrip )
		);
	}
	
	function _medalUrl($medalName,$ifOwn=true) {
		global $db_bbsurl;
		$url = $ifOwn ? $db_bbsurl."/apps.php?q=medal&a=my" : $db_bbsurl."/apps.php?q=medal";
		return "[url=".$url."]".$medalName."[/url]";
	}
	/**
	 * @return PW_MedalInfoDB
	 */
	function _getMedalInfoDb() {
		return L::loadDB('medalinfo','medal');
	}
	/**
	 * @return PW_MedalAwardDB
	 */
	function _getMedalAwardDb() {
		return L::loadDB('medalaward','medal');
	}
	/**
	 * @return PW_MedalLogDB
	 */
	function _getMedalLogDb() {
		return L::loadDB('medallog','medal');
	}
	/**
	 * @return PW_MedalApplyDB
	 */
	function _getMedalApplyDb() {
		return L::loadDB('medalapply','medal');
	}
}
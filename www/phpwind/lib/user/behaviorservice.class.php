<?php
class PW_BehaviorService {
	/**
	 * ��¼һ����Ϊ
	 * @param int $uid
	 * @param string $behavior
	 * @param int $lasttime
	 * @param bool $clear δ����ʱ����
	 * @return array($num,$change)
	 */
	function doBehavior($uid,$behavior,$lasttime=0,$clear = false) {
		global $tdtime;
		$uid = (int) $uid;
		$behavior = $this->_initBehavior($behavior);
		if (!$behavior || !$uid) return array(false,'��������');
		if ($lasttime && $lasttime>$tdtime) array(false,'�������');

		$statistic = $this->getBehaviorStatistic($uid, $behavior);
		if (!$statistic) {
			$this->addBehaviorStatistic(array('uid'=>$uid,'behavior'=>$behavior,'lastday'=>$tdtime,'num'=>1));
			//$this->addBehaviorLog($uid, $behavior, 1);
			return array(1,1);
		} else {
			if ($statistic['lastday']>=$tdtime) return array($statistic['num'],0);
			$changeDay = ($tdtime-$statistic['lastday'])/86400;
			$change = $changeDay == 1 ? 1 : 2-$changeDay;
			if ($clear) {
				$newNum = $changeDay == 1 ? $statistic['num'] + 1 : 1;
			} else {
				$newNum = $statistic['num'] + $change;
			}
			if ($newNum<=0) $newNum = 1;
			$this->updateBehaviorStatistic(array('lastday'=>$tdtime,'num'=>$newNum), $uid, $behavior);
			//if ($change) $this->addBehaviorLog($uid, $behavior, $change);
			return array($newNum,$change);
		}
	}
	
	/**
	 * ��ȡͳ����Ϣ
	 * @param int $uid
	 * @param string $behavior
	 * @return
	 */
	function getBehaviorStatistic($uid,$behavior) {
		$uid = (int) $uid;
		$behavior = $this->_initBehavior($behavior);
		if (!$behavior || !$uid) return array();
		$behaviorStatisticDb = $this->_getBehaviorStatisticDb();
		return $behaviorStatisticDb->get($uid, $behavior);
	}
	/**
	 * ����һ����¼
	 * @param array $data
	 * @param int $uid
	 * @param int $behavior
	 * @return
	 */
	function updateBehaviorStatistic($data,$uid,$behavior) {
		$uid = (int) $uid;
		$behavior = $this->_initBehavior($behavior);
		if (!$behavior || !$uid) return array(false,'��������');
		$behaviorStatisticDb = $this->_getBehaviorStatisticDb();
		return $behaviorStatisticDb->update($data, $uid, $behavior);
	}
	/**
	 * ���һ��ͳ����Ϣ
	 * @param array $data
	 * @return
	 */
	function addBehaviorStatistic($data) {
		$data['uid'] = (int) $data['uid'];
		$data['num'] = (int) $data['num'];
		$data['lastday'] = (int) $data['lastday'];
		$data['behavior'] = $this->_initBehavior($data['behavior']);
		if (!$data['lastday']) $data['lastday'] = intval(get_date($GLOBALS['tdtime'],'Ymd'));
		if (!$data['uid'] || !$data['behavior'] || !$this->_checkBehavior($data['behavior'])) return array(false,'��������');
		$behaviorStatisticDb = $this->_getBehaviorStatisticDb();
		$behaviorStatisticDb->insert($data);
		return true;
	}
	
	/**
	 * ��ȡ���е�����
	 * @return array
	 */
	function getBehaviorTypes() {
		return array(
			1=>'continue_login',		//'������¼����'
			2=>'continue_post',			//'������������'
			3=>'continue_thread_post',	//'��������������'
			4=>'continue_punch',		//'����������'
			5=>'continue_user_upgrade',	//'�û�������¼'
			6=>'today_add_follow',		//'��¼���չ�ע'
			
		);
	}
	/**
	 * �����������һ���¼
	 * @return array
	 */
	function getBehaviorList($behavior,$num=10){
		$behavior=$this->_initBehavior($behavior);
		$behaviorStatisticDb = $this->_getBehaviorStatisticDb();
		return $behaviorStatisticDb->gets($behavior,$num);
	}
	function getFansOrder($behavior,$lastday,$num){
		if (empty($behavior) && empty($lastday)){
			return false;
		}
		$behaviorStatisticDb = $this->_getBehaviorStatisticDb();
		return $behaviorStatisticDb->getFansList($behavior,$lastday,$num);
	}
	function _checkBehavior($behavior) {
		$behavior = $this->_initBehavior($behavior);
		$behaviors = $this->getBehaviorTypes();
		return isset($behaviors[$behavior]);
	}
	
	function _initBehavior($behavior) {
		return is_numeric($behavior) ? $behavior : $this->_getBehaviorKey($behavior);
	}
	
	function _getBehaviorKey($behavior) {
		foreach ($this->getBehaviorTypes() as $key=>$value) {
			if ($value==$behavior) return $key;
		}
		return false;
	}
	/**
	 * @return PW_MemberBehaviorStatisticDB
	 */
	function _getBehaviorStatisticDb() {
		return L::loadDb('memberbehaviorstatistic','user');
	}
	/**
	 * ���һ����¼
	 * @param int $uid
	 * @param int $behavior
	 * @param int $change
	 * @return
	 
	function addBehaviorLog($uid,$behavior,$change) {
		global $timestamp;
		$uid = (int) $uid;
		$change = (int) $change;
		$behavior = $this->_initBehavior($behavior);
		if (!$behavior || !$uid || !$change) return array(false,'��������');
		$behaviorLogDb = $this->_getBehaviorLogDb();
		return $behaviorLogDb->insert(array('uid'=>$uid,'behavior'=>$behavior,'change'=>$change,'timestamp'=>$timestamp));
	}
	*/
	/**
	 * @return PW_MemberBehaviorLogDB
	 
	function _getBehaviorLogDb() {
		return L::loadDb('memberbehaviorlog','user');
	}
	*/
	
}
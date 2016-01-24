<?php
!defined('P_W') && exit('Forbidden');
class PW_Activity {
	var $activitycatedb;
	var $activitymodeldb;

	function setActCache() {
		//* include pwCache::getPath(D_P.'data/bbscache/activity_config.php');
		extract(pwCache::getData(D_P.'data/bbscache/activity_config.php', false));
		$this->activitycatedb = $activity_catedb;
		$this->activitymodeldb = $activity_modeldb;
	}
	
	function getActivityCateDb() {
		if (!$this->activitycatedb) {
			$this->setActCache();
		}
		return $this->activitycatedb;
	}
	
	function getActivityModelDb() {
		if (!$this->activitymodeldb) {
			$this->setActCache();
		}
		return $this->activitymodeldb;
	}
	/**
	 * ���ػ�ӷ���select��HTML
	 * @param int $selectedActmid ѡ�еĻ����
	 * @param bool $withEmptySelection �Ƿ���������з��ࡱѡ��
	 * @param string $selectName select��name��ֵ�����ޣ����ص�HTML������select���Tag
	 * @return HTML
	 */
	function getActmidSelectHtml ($selectedActmid = 0, $withEmptySelection = 1, $selectTagName = 'actmid') {
		$options = array();
		if ($withEmptySelection) {
			$options['0'] = getLangInfo('other','act_activity_class');
		}
		$activityCateDb = $this->getActivityCateDb();
		$activityModelDb = $this->getActivityModelDb();
		$newModelDb = array();
		foreach ($activityModelDb as $value) {
			$newModelDb[$value['actid']][] = $value;
		}

		foreach($activityCateDb as $value) {
			foreach($newModelDb[$value['actid']] as $val){
				$options[$value['name']][$val['actmid']] = $val['name'];
			}
		}
		
		$return = getSelectHtml($options, $selectedActmid, $selectTagName);
		return $return;
	}
	/**
	 * ��ȡ�״̬��Key
	 * @param array $data �����
	 * @param int $currentTimestamp ��ǰʱ���
	 * @param int $numberOfPeopleAlreadySignup �ѱ�������
	 * @return string �״̬Key
	 */
	function getActivityStatusKey ($data, $currentTimestamp, $numberOfPeopleAlreadySignup) {
		if ($data['iscancel']) {
			return 'activity_is_cancelled';//�ȡ��
		} elseif ($data['signupstarttime'] > $currentTimestamp) {
			return 'signup_not_started_yet';// ����δ��ʼ
		} elseif ($data['endtime'] < $currentTimestamp) {
			return 'activity_is_ended';//�����
		} elseif ($currentTimestamp > $data['starttime'] && $currentTimestamp < $data['endtime']) {
			return 'activity_is_running';//�������
		} elseif ($data['signupendtime'] < $currentTimestamp) {
			return 'signup_is_ended';//�����������δ��ʼ
		} elseif ($numberOfPeopleAlreadySignup >= $data['maxparticipant'] && $data['maxparticipant']) {
			return 'signup_number_limit_is_reached';//������������
		} else {
			return 'signup_is_available';
		}
	}
}
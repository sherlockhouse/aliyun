<?php
!function_exists('readover') && exit('Forbidden');

/**
 * �û�����ϵͳ
 * 
 * @package UserJobs
 */
class PW_Job {
	var $_db = null;
	var $_hour = 3600;
	var $_timestamp = null;
	var $_cache = true;
	function PW_Job() {
		global $db, $timestamp;
		$this->_db = & $db;
		$this->_timestamp = $timestamp;
	}
	/*
	function run($userid, $groupid) {
		//$this->jobAutoController($userid, $groupid);
	}
	*/
	/**
	 * ����Ƿ������������
	 */
	function checkApply($id, $userId, $groupid, $job = array()) {
		$id = intval($id);
		if ($id < 1) {
			return array(
				false,
				$this->getLanguage("job_id_error"),
				''
			);
		}
		$job = $job ? $job : $this->getJob($id);
		if (!$job) {
			return array(
				false,
				$this->getLanguage("job_not_exist"),
				''
			);
		}
		/*�����Ƿ�ر�*/
		if ($job['isopen'] == 0) {
			return array(
				false,
				$this->getLanguage("job_close"),
				''
			);
		}
		/*�û�������*/
		if (isset($job['usergroup']) && $job['usergroup'] != "") {
			if (!in_array($groupid, explode(",", $job['usergroup']))) {
				return array(
					false,
					$this->getLanguage("job_usergroup_limit"),
					''
				);
			}
		}
		/*�������� ��ǰ��������*/
		if (isset($job['number']) && $job['number'] != 0) {
			$number = $this->countJoberByJobId($job['id']);
			if ($number >= $job['number']) {
				return array(
					false,
					$this->getLanguage("job_apply_number_limit"),
					''
				);
			}
		}
		$current = $next = $this->_timestamp;
		/*ʱ������ ��ǰ��������*/
		if ((isset($job['endtime']) && $job['endtime'] != 0 && $job['endtime'] < $current)) {
			return array(
				false,
				$this->getLanguage("job_time_limit"),
				''
			);
		}
		if ((isset($job['starttime']) && $job['starttime'] != 0 && $job['starttime'] > $current)) {
			return array(
				false,
				$this->getLanguage("job_time_early"),
				''
			);
		}
		/*�Ƿ����ǰ������*/
		if (isset($job['prepose']) && $job['prepose'] != 0) {
			$prepose = $this->getJob($job['prepose']);
			if ($prepose) {
				/*�Ƿ��Ѿ����ǰ������*/
				$jober = $this->getJoberByJobId($userId, $prepose['id']);
				if (!$jober) {
					return array(
						false,
						$this->getLanguage("job_has_perpos") . $prepose['title'],
						''
					);
				}
				if ($jober['status'] != 3) {
					return array(
						false,
						$this->getLanguage("job_has_perpos_more") . $prepose['title'],
						''
					);
				}
			}
		}
		//�Ƿ��Ѿ�����
		$hasApply = $this->getJoberByJobId($userId, $id);
		//һ�����������
		if ($hasApply && $hasApply['total'] > 0 && $job['period'] < 1) {
			return array(
				false,
				"���Ѿ�����������",
				""
			);
		}
		//ʵ����֤����
		if (S::inArray($job['job'],array('doAuthAlipay','doAuthMobile'))) {
			global $db_authstate;
			$userService = $this->_getUserService();
			$userdb = $userService->get($userId, true, false, false);
			if (!$db_authstate)  return array(false,'վ��δ����ʵ����֤','');
			
			if ($job['job'] == 'doAuthAlipay' && getstatus($userdb['userstatus'], PW_USERSTATUS_AUTHALIPAY)){
				return array(false,'���Ѿ���֧����','');
			}
			if ($job['job'] == 'doAuthMobile' && getstatus($userdb['userstatus'], PW_USERSTATUS_AUTHMOBILE)){
				return array(false,'���Ѿ����ֻ���','');
			}
		}
		//�����Ƿ�Ϊ���������� ���û��Ƿ��Ѿ����룬�´ο�ʼ��ʱ��
		$again = 0;
		if (isset($job['period']) && $job['period'] != 0) {
			//����Ѿ����룬����Ƿ��´�����ʱ��
			if ($hasApply && $hasApply['next'] > $current) {
				return array(
					false,
					$this->getLanguage("job_apply_next_limit"),
					''
				);
			}
			if ($hasApply && $hasApply['next'] < $current) {
				$again = 1;
			}
			$next = $current + $job['period'] * $this->_hour;
		}
		$job['next'] = $next;
		//���û���������
		if (($hasApply && $again == 0)) {
			return array(
				false,
				$this->getLanguage("job_has_apply"),
				''
			);
		}
		return array(
			true,
			$this->getLanguage("job_apply_success"),
			$job
		);
	}
	
	function appendJobDetailInfo($jobs) {
		$list = array();
		foreach ($jobs as $key => $job) {
			if ($job['isopen'] == 0)  continue;
			if ((isset($job['isuserguide']) && $job['isuserguide'])) continue; //���û���������
			$job['icon'] = (isset($job['icon']) && $job['icon'] != "") ? "attachment/job/" . $job['icon'] : "images/job/" . strtolower($job['job']) . ".gif";
			
			$reward = '';
			if (isset($job['reward'])) $reward = $this->getCategoryInfo($job['reward']);
			$job['reward'] = $reward ? $reward : array();
		
			if ($job['status'] < 2) {
				$jobClass = $this->loadJob(strtolower($job['job']));
				$job['link'] = $jobClass ? $jobClass->getUrl($job) : "";
			}
			
			if (isset($job['status'])) {
				$operationTypes = array(0 => 'start', 1 => 'start', 2 => 'gain');
				$job['operationType'] = isset($operationTypes[$job['status']]) ? $operationTypes[$job['status']] : 'start';
				if (!$job['factor']) $job['operationType'] = 'gain';
			} else {
				$job['operationType'] = 'apply';
			}
			
			$job['condition'] = $this->getCondition($job);
			$list[$key] = $job;
		}

		return $list;
	}
	
	function buildLists($joblists, $action, $userId, $groupid) {
		if (!$joblists) {
			return array();
		}
		global $winduid;
		if($userId != $winduid) return array();
		$jobs = array();
		foreach($joblists as $job) {
			//��ʾ���� �Ƿ���ʾ
			if ($job['isopen'] == 0) {
				continue;
			}
			$lists = array();
			$lists['id'] = $job['id'];
			$lists['title'] = $job['title'];
			$lists['step'] = $job['step'];
			$lists['description'] = html_entity_decode($job['description']);
			$lists['period'] = ($job['period']) ? 'ÿ��' . $job['period'] . 'Сʱ��������һ��' : "һ��������";
			$reward = '';
			if (isset($job['reward'])) {
				$reward = implode(' ', $this->getCategoryInfo($job['reward']));
			}
			$lists['reward'] = $reward ? $reward : "��";
			$lists['number'] = (isset($job['number']) && $job['number'] != 0) ? $job['number'] . "��" : "";
			$isFactor = (isset($job['factor']) && $job['factor'] != '') ? true : false;
			if ($isFactor) {
				$factor = unserialize($job['factor']);
				$lists['factor'] = $factor;
			}
			$lists['timelimit'] = (isset($factor['limit']) && $factor['limit'] != "") ? $factor['limit'] : "������";
			/*ǰ������*/
			$prepose = $doPrepose = '';
			if (isset($job['prepose']) && $job['prepose'] != 0) {
				$prepose = $this->getJob($job['prepose']); /*�Ƿ����*/
				$prepose = "(������� " . $prepose['title'] . " ��������)";
				$preposeJob = $this->getJoberByJobId($userId, $job['prepose']);
				$doPrepose = ($preposeJob && $preposeJob['total'] > 0) ? true : false;
			}
			$lists['prepose'] = $prepose ? $prepose : "";
			$lists['icon'] = (isset($job['icon']) && $job['icon'] != "") ? "attachment/job/" . $job['icon'] : "images/job/" . strtolower($job['job']) . ".gif";
			$lists['condition'] = $this->getCondition($job);
			$lists['usergroup'] = (isset($job['usergroup']) && $job['usergroup'] != '') ? $this->getUserGroup($job['usergroup']) : '';
			if ($action == "list") { /*������*/
				/*����û�п�ʼ*/
				if (!$this->checkJobCondition($userId, $groupid, $job) || ($prepose && !$doPrepose)) { /*��������*/
					$lists['btn'] = $this->getJobBtn($job['id'], "apply_old");
				} else {
					$lists['btn'] = $this->getJobBtn($job['id'], "apply");
				}
			} elseif (empty($action) || $action == "applied") { /*������*/
				$lists['gain'] = $lists['qbtn'] = '';
				if (isset($job['status'])) {
					$status = $job['status'];
					$info = ($job['period'] > 0) ? "�Ƿ�ȷ�Ϸ�����������" : "��������Ϊһ�������񣬷����󽫲����ٴ����롣�Ƿ�ȷ�Ϸ�����������";
					if ($status < 2) { /*url ����*/
						$jobClass = $this->loadJob(strtolower($job['job']));
						$link = $jobClass ? $jobClass->getUrl($job) : "";
					}
					if ($status == 0) {
						/*�Ƿ�������*/
						$lists['btn'] = $this->getJobBtn($job['id'], "start", $link);
						($job['finish'] == 0) && $lists['qbtn'] = $this->getJobBtn($job['id'], "quit", $info); /*�Ƿ�ɷ���*/
					} elseif ($status == 1) {
						if ($factor && isset($factor['limit']) && $factor['limit'] > 0) {
							$jober = $this->getJoberByJobId($userId, $job['id']);
							if ($jober['last'] + $factor['limit'] * $this->_hour < $this->_timestamp) {
								$this->updateJober(array(
									'status' => 5
								), $jober['id']);
								$this->reduceJobNum($userid);
								continue;
							}
						}
						$lists['btn'] = $this->getJobBtn($job['id'], "start_old", $link);
						($job['finish'] == 0) && $lists['qbtn'] = $this->getJobBtn($job['id'], "quit", $info);
					} elseif ($status == 2) {
						$lists['gain'] = "(100%)"; /*�����ʾ*/
						$lists['btn'] = $this->getJobBtn($job['id'], "gain");
					}
				}
				if (!$isFactor) {
					$lists['gain'] = "(100%)";
					$lists['btn'] = $this->getJobBtn($job['id'], "gain");
					$lists['qbtn'] = '';
				}
				//������ɽ�����
				$lists['degree'] = '100%';
			} elseif ($action == "finish") { /*�����*/
				$lists['lastfinish'] = "�������� " . get_date($job['last'], "Y-m-d H:i");
				$lists['btn'] = '';
				if (isset($job['period']) && $job['period'] != 0) {
					$lists['btn'] = '<a  href="javascript:;" id="apply_' . $job['id'] . '" class="tasks_again">�ٴ�����</a>';
				}
			} elseif ($action == "quit") { /*�ѷ���*/
			/*	if (isset($job['period']) && $job['period'] != 0) {
					$lists['btn'] = $this->getJobBtn($job['id'], "apply");
					
				}*/
				if (!$this->checkJobCondition($userId, $groupid, $job) || ($prepose && !$doPrepose)) { /*��������*/				
					$lists['btn'] = $this->getJobBtn($job['id'], "apply_old");
				} else {
					$jober = $this->getJoberByJobId($job['userid'],$job['jobid']);
					$lists['btn'] = $this->getJobBtn($job['id'], "apply_again",'',$jober['id']);
				}

				/*ʧ�ܻ����*/
				$lists['info'] = ($job['status'] == 5) ? "����ʧ��" : "������ " . get_date($job['last'], "Y-m-d H:i");
			}
			$jobs[] = $lists;
		}
		return $jobs;
	}
		
	function getJobBtn($id, $k, $info = '', $jobId = null) {
		$job && $job = strtolower($job);
		$btn = array();
		$btn['apply'] = '<a href="javascript:;" class="tasks_apply" hidefocus="true" id="apply_' . $id . '">��������</a>';
		$btn['apply_old'] = '<a href="javascript:;" class="tasks_apply_old" hidefocus="true" title="��������������" id="apply_' . $id . '">��������</a>';
		$btn['apply_again'] =  '<a href="jobcenter.php?action=reapply&step=2&id=' .$id .'&joberid=' .$jobId .'" class="tasks_apply" hidefocus="true" id="apply_' . $id . '">��������</a>';	
		$btn['start'] = '<a href="javascript:;" link="' . $info . '" class="tasks_startB" hidefocus="true" id="start_' . $id . '">������ʼ</a>';
		$btn['start_old'] = '<a href="javascript:;" link="' . $info . '" class="tasks_startB_old" hidefocus="true" id="start_' . $id . '">������ʼ</a>';
		$btn['quit'] = '<a href="javascript:;" class="tasks_quit" hidefocus="true" id="quit_' . $id . '" info="' . $info . '">����</a>';
		$btn['gain'] = '<a href="javascript:;"  hidefocus="true" id="gain_' . $id . '" class="tasks_receiving">��ȡ����</a>';
		return $btn[$k];
	}
	/*
	* ����ʼ�������ģ���ȡ����ʼ������
	*/
	function jobStartController($userid, $jobid) {
		$jobid = intval($jobid);
		if ($jobid < 1) {
			return array(
				false,
				"��Ǹ������ID��Ч",
				''
			);
		}
		//�Ƿ�����������
		$job = $this->getJob($jobid);
		if (!$job) {
			return array(
				false,
				"��Ǹ�����񲻴���",
				''
			);
		}
		$current = $this->_timestamp;
		if (isset($job['end']) && $job['end'] != 0 && $job['end'] > $current) {
			return array(
				false,
				"��Ǹ��������������Ѿ�����",
				''
			);
		}
		$jober = $this->getJoberByJobId($userid, $jobid);
		if (!$jober) {
			return array(
				false,
				"��Ǹ���㻹û�������������",
				''
			);
		}
		if ($jober['status'] > 1) {
			return array(
				false,
				"��Ǹ�����Ѿ���ʼ���������",
				''
			);
		}
		//if($jober['next']>$current){
		//	return array(false,"��Ǹ����û�е�ִ�������ʱ��",'');
		//}
		$jobClass = $this->loadJob(strtolower($job['job']));
		if (!$jobClass) {
			return array(
				false,
				"��Ǹ����������������",
				''
			);
		}
		$link = $jobClass->getUrl($job);
		//��������״̬
		if ($jober['status'] == 0) {
			$this->updateJober(array(
				"status" => 1
			), $jober['id']);
		}
		return array(
			true,
			"",
			$link
		);
	}
	/*
	* ������ȿ�������
	*/
	function jobController($userid, $jobName, $factor = array()) {
		$jobName = trim($jobName);
		$jobs = $this->getJobByJobName($jobName);
		if (!$jobs) {
			return array();
		}
		$jobIds = $tmp = array();
		foreach($jobs as $job) {
			$jobIds[] = $job['id'];
			$tmp[$job['id']] = $job;
		}
		$jobers = $this->getInProcessJobersByUserIdAndJobIds($userid, $jobIds);
		if (!S::isArray($jobers)) return false;
		$isSuccess = false;
		foreach ($jobers as $jober) { //��������Ѿ���ʼ�Ĳ��ҷ�������������
			//�������
			if ($jober['status'] >= 2) continue;
			$job = $tmp[$jober['jobid']]; /*��ǰ����*/
			if ($jober['total'] > 0 && $job['period'] < 1) continue;
			$current = $next = $this->_timestamp;
			/*�Ƿ�����������*/
			if (isset($job['period']) && $job['period'] != 0) {
				$next = $current + $job['period'] * $this->_hour;
			}
			$status = $this->jobFinishController($job, $jober, $factor); /*�������״̬*/
			if ($status == 0) continue;
			$data = array();
			//($status > 2 ) &&  $data['last'] = $current;
			$data['current'] = $jober['current'] + 1; /*��ǰ����*/
			$data['step'] = $jober['step'] + 1; /*�ܲ���*/
			$data['next'] = $next; /*������������һ��ʱ�俪ʼ��*/
			$data['status'] = $status;
			$this->updateJober($data, $jober['id']);
			$isSuccess = true;
		}
		return $isSuccess;
	}
	
	function jobFinishController($job, $jober, $factor = array()) {
		if (!$factor) {
			return 2; /*Ϊ�ձ�ʾֱ���������*/
		}
		/*����״̬*/
		/*û������������*/
		$isFactor = (isset($job['factor']) && $job['factor'] != "") ? true : false;
		if (!$isFactor) {
			return 2;
		}
		$jobClass = $this->loadJob($job['job']);
		return $jobClass->finish($job, $jober, $factor);
	}
	function jobApplyController($userId, $jobId) {
		$job = $this->getJob($jobId);
		if (!$job) {
			return array();
		}
		$current = $this->_timestamp;
		$jober = $this->getJoberByJobId($userId, $jobId);
		if (isset($job['period']) && $job['period'] != 0) {
			$next = $current + $job['period'] * $this->_hour;
		}
		//��������Ƿ����
		if ($jober && $jober['total'] > 0 && $job['period'] < 1) {
			return array();
		}
		if ($jober && $job['period'] > 0 && $jober['status'] > 1 && ($jober['total'] > 0 || ($jober['status'] == 4))) {
			return $this->_againJober($userId, $jobId, $next, $current, $jober);
		}
		if (!$jober) {
			return $this->_createJober($userId, $jobId, $next, $current, $jober);
		}
		return array();
	}
	function _createJober($userId, $jobId, $next, $current, $jober = array()) {
		$jober = $jober ? $jober : $this->getJoberByJobId($userId, $jobId);
		if ($jober) {
			return array();
		}
		$data = array();
		$data['jobid'] = $jobId;
		$data['userid'] = $userId;
		$data['current'] = 1; /*��ǰ����*/
		$data['step'] = 0; /*�ܲ���*/
		$data['last'] = $current;
		$data['next'] = $next; /*������������һ��ʱ�俪ʼ��*/
		$data['status'] = 0;
		$data['creattime'] = $current;
		return $this->addJober($data);
	}
	function _againJober($userId, $jobId, $next, $current, $jober = array()) {
		$jober = $jober ? $jober : $this->getJoberByJobId($userId, $jobId);
		if (!$jober) {
			return array();
		}
		$data = array();
		$data['current'] = 1; /*��ǰ����*/
		$data['step'] = 0; /*�ܲ���*/
		$data['last'] = $current;
		$data['next'] = $next; /*������������һ��ʱ�俪ʼ��*/
		$data['status'] = 0;
		$result = $this->updateJober($data, $jober['id']);
		if ($result) {
			$this->increaseJobNum($userId);
		}
		return $result;
	}
	/*
	* չʾ����
	*/
	function jobDisplayController($userid, $groupid, $action) {
		return $this->buildLists($this->getCanApplyJobs($userid, $groupid), $action, $userid, $groupid);
	}
	function getCanApplyJobs($userid, $groupid) {
		$joblists = $this->getJobAll();
		if (!$joblists) {
			return array();
		}
		$current = $this->_timestamp;
		$jobs = array();
		/*���˲���*/
		$jobIds = array();
		foreach($joblists as $job) {
			$jobIds[] = $job['id'];
		}
		/*�Ƿ��Ѿ��μ�*/
		$joins = $this->getJobersByJobIds($userid, $jobIds);
		$jobers = array();
		if ($joins) {
			foreach($joins as $join) {
				$jobers[$join['jobid']] = $join;
			}
		}
		foreach($joblists as $job) {
			/*�Ƿ�������*/
			if ($job['isopen'] == 0) {
				continue;
			}
			/*����������������ʾ*/
			if ($job['display'] == 1) {
				if (!$this->checkJobCondition($userid, $groupid, $job)) {
					continue;
				}
			}
			/*�����Ƿ��Ѿ�����*/
			//$isApplied = $this->getJoberByJobId($userid,$job['id']);
			$isApplied = (isset($jobers[$job['id']])) ? $jobers[$job['id']] : '';
			if ($isApplied && $isApplied['status'] <= 2) {
				continue;
			}
			/*����������*/
			if ($isApplied && $job['period'] == 0) {
				continue;
			}
			if ((isset($job['isuserguide']) && $job['isuserguide'])) continue; //���û���������
			if ((isset($job['endtime']) && $job['endtime'] != 0 && $job['endtime'] < $current)) {
				continue;
			}
			$jobs[] = $job;
		}
		return $jobs;
	}
	/*������������Ƿ����*/
	function checkJobCondition($userId, $groupid, $job) {
		//�û�����������
		if (isset($job['usergroup']) && $job['usergroup'] != '') {
			$usergroups = explode(",", $job['usergroup']);
			if (!in_array($groupid, $usergroups)) {
				return false;
			}
		}
		//����������������
		if (isset($job['number']) && $job['number'] > 0) {
			$number = $this->countJoberByJobId($job['id']);
			if ($number >= $job['number']) {
				return false;
			}
		}
		//ǰ������
		if (isset($job['prepose']) && $job['prepose'] > 0) {
			$prepose = $this->getJob($job['prepose']);
			if ($prepose) {
				$jober = $this->getJoberByJobId($userId, $prepose['id']);
				if (!$jober) {
					return false; /*ǰ������û���*/
				}
				if ($jober['status'] != 3) {
					return false;
				}
			}
		}
		//ʵ����֤
		if (S::inArray($job['job'],array('doAuthAlipay','doAuthMobile'))) {
			global $db_authstate;
			if (!$db_authstate) return false;
			$userService = $this->_getUserService();
			$userdb = $userService->get($userId, true, false, false);
			if ($job['job'] == 'doAuthAlipay' && getstatus($userdb['userstatus'], PW_USERSTATUS_AUTHALIPAY)){
				return false;
			}
			if ($job['job'] == 'doAuthMobile' && getstatus($userdb['userstatus'], PW_USERSTATUS_AUTHMOBILE)){
				return false;
			}
		}
		return true;
	}
	/*
	* �Զ��������
	*/
	/*
	function jobAutoController($userid, $groupid) {
		$userid = intval($userid);
		$groupid = intval($groupid);
		if ($groupid < 1 || $userid < 1) {
			return;
		}
		if (!$jobLists = $this->_jobAutoFilterHandler($userid, $groupid)) {
			return;
		}
		$current = $this->_timestamp;
		foreach($jobLists as $job) {
			$this->_jobAutoCreateHandler($userid, $job, $current);
		}
	}
	*/
	
	/*
	* �Զ���������һ�����ظ�������û�����
	*/
	/*
	function _jobAutoAgainHandler($userid, $job, $current) {
		$next = $current;
		if (isset($job['period']) && $job['period'] != 0) {
			$next = $current + $job['period'] * $this->_hour;
		}
		$job['next'] = $next ? $next : $current;
		$this->_againJober($userid, $job['id'], $job['next'], $current);
	}
	*/
	/*
	* �Զ������������
	* ��SQL��ѯ����
	* �������Զ�����������ֱ�Ӹ���/��������������������ֱ�Ӳ�ѯ
	*/
//	function _jobAutoFilterHandler($userid, $groupid) {
//		$jobs = $this->getJobsAuto();
//		if (!$jobs) {
//			return false;
//		}
//		$current = $this->_timestamp;
//		$jobLists = $jobIds = $periods = $preposes = array();
//		/*������������*/
//		foreach($jobs as $job) {
//			/*����״̬����*/
//			if ($job['isopen'] == 0) {
//				continue;
//			}
//			/*ʱ�����ƹ���*/
//			if ((isset($job['endtime']) && $job['endtime'] != 0 && $job['endtime'] < $current)) {
//				continue;
//			}
//			if ((isset($job['starttime']) && $job['starttime'] != 0 && $job['starttime'] > $current)) {
//				continue;
//			}
//			if (isset($job['usergroup']) && $job['usergroup'] != '') { /*�û������*/
//				$usergroups = explode(",", $job['usergroup']);
//				if (!in_array($groupid, $usergroups)) {
//					continue;
//				}
//			}
//			if (isset($job['period']) && $job['period'] > 0) {
//				$periods[] = $job['id']; /*�������������*/
//			}
//			if (isset($job['prepose']) && $job['prepose'] > 0) {
//				$preposes[$job['prepose']] = $job['id']; /*ǰ���������*/
//			}
//			/*�������� ��ǰ��������*/
//			if (isset($job['number']) && $job['number'] != 0) {
//				$number = $this->countJoberByJobId($job['id']);
//				if ($number >= $job['number']) {
//					continue;
//				}
//			}
//			$jobLists[$job['id']] = $job;
//			$jobIds[] = $job['id'];
//		}
//		if (!$jobLists) {
//			return false;
//		}
//		/*�Ƿ��Ѿ��μӹ���������Ƿ�������������*/
//		$joins = $this->getJobersByJobIds($userid, $jobIds);
//		if ($joins) {
//			foreach($joins as $join) {
//				//����������Ե��ظ�������ֱ���Զ���������
//				$t_job = array();
//				$t_job = $jobLists[$join['jobid']];
//				if (in_array($join['jobid'], $periods)) {
//					if ($join['status'] >= 3 && $join['total'] > 0) {
//						/*ʱ�������� ��һ��ִ��ʱ��*/
//						if ($join['next'] < $current) {
//							$this->_jobAutoAgainHandler($userid, $t_job, $current);
//						}
//					}
//				}
//				unset($t_job);
//				unset($jobLists[$join['jobid']]); /*����Ѿ��μӵļ�¼����������������*/
//			}
//		}
//		if (!$jobLists) {
//			return false;
//		}
//		/*�Ƿ���ǰ������*/
//		if ($preposes) {
//			$joins = $this->getJobersByJobIds($userid, array_keys($preposes));
//			if ($joins) {
//				foreach($joins as $join) {
//					if ($join['total'] > 0) {
//						unset($preposes[$join['jobid']]); /*�Ź��Ѿ���ɵ�����*/
//					}
//				}
//			}
//			/*ʣ�¶���Щû�����ǰ�������*/
//			if ($preposes) {
//				foreach($preposes as $jobid) {
//					unset($jobLists[$jobid]); /*����*/
//				}
//			}
//		}
//		return $jobLists;
//	}
	/*
	* �����������
	*/
	function jobQuitController($userid, $jobId) {
		$jobId = intval($jobId);
		if ($jobId < 1) {
			return array(
				false,
				"����ID��Ч"
			);
		}
		$job = $this->getJob($jobId);
		if (!$job) {
			return array(
				false,
				"���񲻴���"
			);
		}
		if ($job['finish'] == 1) {
			return array(
				false,
				"�����������ɣ����ܷ���"
			);
		}
		$jober = $this->getJoberByJobId($userid, $jobId);
		if (!$jober) {
			return array(
				false,
				"��Ǹ���㻹û�������������"
			);
		}
		if ($jober && $jober['total'] > 0 && $job['period'] < 1) {
			return array(
				false,
				"��Ǹ������Ϊһ�����������Ѿ����"
			);
		}
		if ($jober && $jober['status'] == 3) {
			return array(
				false,
				"��Ǹ�����Ѿ�����������"
			);
		}
		if ($jober && $jober['status'] > 1) {
			return array(
				false,
				"��Ǹ�������Ƿ��������"
			);
		}
		$result = $this->updateJoberByJobId(array(
			'status' => 4
		), $jobId, $userid);
		if (!$result) {
			return array(
				false,
				"��������ʧ�ܣ�������"
			);
		}
		$this->reduceJobNum($userid);
		return array(
			true,
			"�����������"
		);
	}
	/*
	* ��ȡ��������
	*/
	function jobGainController($userid, $jobid) {
		$jobid = intval($jobid);
		if ($jobid < 1) {
			return array(
				false,
				"��Ǹ������ID��Ч"
			);
		}
		//�Ƿ�����������
		$job = $this->getJob($jobid);
		if (!$job) {
			return array(
				false,
				"��Ǹ�����񲻴���"
			);
		}
		if (procLock('job_save', $userid)) {
			$jober = $this->getJoberByJobId($userid, $jobid);
			if (!$jober) {
				return $this->_unlockUserJob($userid, array(
					false,
					"��Ǹ���㻹û�������������"
				));
			}
			/*����Ƿ���һ������������*/
			if (!$job['period'] && $jober['total'] > 1) {
				return $this->_unlockUserJob($userid, array(
					false,
					"��Ǹ�����Ѿ�����������"
				));
			}
			/*����ʱ������ start*/
			$timeout = 0;
			$factor = (isset($job['factor']) && $job['factor'] != "") ? unserialize($job['factor']) : array();
			if ($factor && isset($factor['limit']) && $factor['limit'] > 0) {
				if ($jober['last'] + $factor['limit'] * $this->_hour < $this->_timestamp) {
					$timeout = 1;
				}
			}
			/*�´�ִ��ʱ��*/
			if (isset($job['period']) && $job['period'] > 0) {
				$next = $this->_timestamp + $job['period'] * $this->_hour;
				$next = $next ? $next : $this->_timestamp;
			}
			if ($timeout) {
				$this->updateJober(array(
					'status' => 5,
					'next' => $next
				), $jober['id']);
				$this->reduceJobNum($userid);
				return $this->_unlockUserJob($userid, array(
					true,
					"��Ǹ������û���ڹ涨��ʱ�������"
				));
			}
			/*����ʱ������ end */
			if ($factor) {
				if ($jober['status'] < 2) {
					return $this->_unlockUserJob($userid, array(
						true,
						"��Ǹ���㻹û���������"
					));
				}
				if ($jober['status'] > 3) {
					return $this->_unlockUserJob($userid, array(
						true,
						"��Ǹ�����ݴ���������"
					));
				}
			}
			if ($jober['status'] == 3) {
				return $this->_unlockUserJob($userid, array(
					true,
					"��Ǹ�����Ѿ���ȡ�������������ظ���ȡ"
				));
			}

			$data = array();
			$data['status'] = 3; /*�������*/
			$data['total'] = $jober['total'] + 1;
			$data['next'] = $next;
			$result = $this->updateJober($data, $jober['id']);
			if (!$result) {
				return $this->_unlockUserJob($userid, array(
					false,
					"��Ǹ����ȡ����ʧ�ܣ�������"
				));
			}
			if (isset($job['reward'])) {
				$this->jobRewardHandler($userid, $job);
			}
			$this->reduceJobNum($userid); /*�������*/
			$information = implode(' ', $this->getCategoryInfo($job['reward']));
			$information = $information ? "��" . $information : "";
			return $this->_unlockUserJob($userid, array(
				true,
				"��ϲ���������" . $information
			));
		} else {
			return array(
				false,
				"��Ǹ����ȡ����ʧ�ܣ�������"
			);
		}
	}
	
	function _unlockUserJob($userId, $returnData) {
		procUnLock('job_save', $userId);
		return $returnData;
	}
	
	/*��ȡ������*/
	function jobRewardHandler($userid, $job) {
		if (!isset($job['reward'])) {
			return array();
		}
		$reward = unserialize($job['reward']);
		$category = $reward['category'];
		switch ($category) {
			case "credit":
				$this->jobRewardCredit($userid, $reward, $job);
				break;

			case "tools":
				$this->jobRewardTools($userid, $reward);
				break;

			case "medal":
				$this->jobRewardMedal($userid, $reward);
				break;

			case "usergroup":
				$this->jobRewardUsergroup($userid, $reward);
				break;

			case "invitecode":
				$this->jobRewardInviteCode($userid, $reward);
				break;

			default:
				return "��";
				break;
		}
		require_once R_P.'u/require/core.php';
		updateMemberid($userid, false);
	}
	/*���ֽ���*/
	function jobRewardCredit($userid, $reward, $job) {
		global $credit;
		(!S::isObj($credit)) && require_once R_P . "require/credit.php";
		$userService = $this->_getUserService();
		$user = $userService->get($userid);
		$GLOBALS[job] = $job['title']; /*��������*/
		$credit->addLog('other_finishjob', array(
			$reward['type'] => $reward['num']
		), array(
			'uid' => $userid,
			'username' => $user['username'],
			'ip' => $GLOBALS['onlineip']
		));
		$credit->set($userid, $reward['type'], $reward['num']);
	}
	/*���߽���*/
	function jobRewardTools($userid, $reward) {
		/*���ݳ�ʼ��*/
		$toolid = $reward['type'];
		$nums = $reward['num'];
		$this->_db->pw_update("SELECT uid FROM pw_usertool WHERE uid=" . S::sqlEscape($userid) . " AND toolid=" . S::sqlEscape($toolid), "UPDATE pw_usertool SET nums=nums+" . S::sqlEscape($nums) . " WHERE uid=" . S::sqlEscape($userid) . " AND toolid=" . S::sqlEscape($toolid), "INSERT INTO pw_usertool SET " . S::sqlSingle(array(
			'nums' => $nums,
			'uid' => $userid,
			'toolid' => $toolid,
			'sellstatus' => 0
		)));
	}
	/*ѫ�½���*/
	function jobRewardMedal($userid, $reward) {
		$medalId = $reward['type'];
		$medalService = L::loadClass('medalservice','medal');
		$medalService->awardMedal($userid,$medalId);
	}
	/*�û��齱��*/
	function jobRewardUsergroup($userid, $reward) {
		global $winddb;
		$gid = $reward['type'];
		$days = $reward['day'];
		$timestamp = $this->_timestamp;
		
		$userService = $this->_getUserService();
		$mb = $userService->get($userid);
		$groups = $mb['groups'] ? $mb['groups'] . $gid . ',' : ",$gid,";
		$userService->update($userid, array('groups' => $groups));
		
		$this->_db->pw_update("SELECT uid FROM pw_extragroups WHERE uid=" . S::sqlEscape($userid) . " AND gid=" . S::sqlEscape($gid), "UPDATE pw_extragroups SET " . S::sqlSingle(array(
			'togid' => $winddb['groupid'],
			'startdate' => $timestamp,
			'days' => $days
		)) . " WHERE uid=" . S::sqlEscape($userid) . "AND gid=" . S::sqlEscape($gid), "INSERT INTO pw_extragroups SET " . S::sqlSingle(array(
			'uid' => $userid,
			'togid' => $winddb['groupid'],
			'gid' => $gid,
			'startdate' => $timestamp,
			'days' => $days
		)));
	}
	/*ע�������뽱��*/
	function jobRewardInviteCode($userid, $reward) {
		$timestamp = $this->_timestamp;
		$invnum = $reward['num'];
		$day = $reward['day'];
		for ($i = 0; $i < $invnum; $i++) {
			$invcode = randstr(16);
			$this->_db->update("INSERT INTO pw_invitecode" . " SET " . S::sqlSingle(array(
				'invcode' => $invcode,
				'uid' => $userid,
				'usetime' => $day,
				'createtime' => $timestamp
			)));
		}
	}
	/*
	* ��ȡ��������������
	*/
	function jobDetailHandler($userid, $jobid) {
		$total = $this->countJobersByJobIdAndUserId($userid, $jobid);
		if (!$total) {
			return array(
				'',
				0
			);
		}
		$others = $this->getJobersByJobIdAndUserId($userid, $jobid);
		$userIds = array();
		foreach($others as $other) {
			$userIds[] = $other['userid'];
		}
		if (!$userIds) {
			return array(
				'',
				0
			);
		}
		/*��ȡ�û���Ϣ*/
		require_once (R_P . 'require/showimg.php');
		$userService = $this->_getUserService();
		$users = array();
		foreach ($userService->getByUserIds($userIds) as $rs) {
			list($rs['face']) = showfacedesign($rs['icon'], 1, 's');//ͳһСͼ��
			$users[] = $rs;
		}
		return array(
			$users,
			$total
		);
	}
	function getUserGroup($usergroup) {
		//list($result , $selects) = $this->getLevels();
		list($result, $selects) = $this->getCacheLevels();
		$usergroups = explode(",", $usergroup);
		$groupinfo = '';
		foreach($usergroups as $usergroup) {
			if (isset($selects[$usergroup])) {
				$groupinfo .= $selects[$usergroup] . ',';
			}
		}
		$groupinfo = trim($groupinfo, ',');
		return $groupinfo;
	}
	/* ���ݲ������� */
	function addJob($fields) {
		$jobDao = $this->_getJobDao();
		$result = $jobDao->add($fields);
		if ($result) {
			$this->setFileCache();
		}
		return $result;
	}
	function updateJob($fields, $id) {
		$jobDao = $this->_getJobDao();
		$result = $jobDao->update($fields, $id);
		if ($result) {
			$this->setFileCache();
		}
		return $result;
	}
	function getJobs($page, $prepage) {
		if ($page < 1) return false;
		$start = ($page - 1) * $prepage;
		$jobDao = $this->_getJobDao();
		return $jobDao->gets($start, $prepage);
	}
	function countJobs() {
		$jobDao = $this->_getJobDao();
		return $jobDao->count();
	}
	function getJobAll() {
		//file cache
		if ($this->_cache) {
			$jobs = $this->getFileCache();
			if ($jobs) {
				return $jobs;
			}
		}
		$jobDao = $this->_getJobDao();
		return $jobDao->getAll();
	}
	function getJob($id) {
		//file cache
		if ($this->_cache) {
			$jobs = $this->getFileCache();
			if ($jobs) {
				foreach($jobs as $job) {
					if ($job['id'] == $id) {
						return $job;
					}
				}
			}
		}
		$jobDao = $this->_getJobDao();
		return $jobDao->get($id);
	}
	/*
	function getJobsAuto() {
		//file cache
		if ($this->_cache) {
			$jobs = $this->getFileCache();
			if ($jobs) {
				$autos = array();
				foreach($jobs as $job) {
					if ($job['auto'] == 1) {
						$autos[] = $job;
					}
				}
				return $autos;
			}
		}
		$jobDao = $this->_getJobDao();
		return $jobDao->getByAuto();
	}
	*/
	function getJobByJobName($jobName) {
		//file cache
		if ($this->_cache) {
			$jobs = $this->getFileCache();
			if ($jobs) {
				$result = array();
				foreach($jobs as $job) {
					if ($job['job'] == $jobName) {
						$result[] = $job;
					}
				}
				return $result;
			}
		}
		$jobDao = $this->_getJobDao();
		return $jobDao->getByJobName($jobName);
	}
	function getJoberByJobIds($userid, $jobIds) {
		$joberDao = $this->_getJoberDao();
		return $joberDao->getsByJobIds($userid, $jobIds);
	}
	
	function getInProcessJobersByUserIdAndJobIds($userid, $jobIds) {
		$joberDao = $this->_getJoberDao();
		return $joberDao->getInProcessJobersByUserIdAndJobIds($userid, $jobIds);
	}
	
	function getJobersByJobIds($userid, $ids) {
		$joberDao = $this->_getJoberDao();
		return $joberDao->getJobersByJobIds($userid, $ids);
	}
	function countJobersByJobIdAndUserId($userid, $jobid) {
		$joberDao = $this->_getJoberDao();
		return $joberDao->countJobersByJobIdAndUserId($userid, $jobid);
	}
	function getJobersByJobIdAndUserId($userid, $jobid) {
		$joberDao = $this->_getJoberDao();
		return $joberDao->getJobersByJobIdAndUserId($userid, $jobid);
	}
	function deleteJob($id) {
		$jobDao = $this->_getJobDao();
		$result = $jobDao->delete($id);
		if ($result) {
			$this->setFileCache();
		}
		return $result;
	}
	function addJober($fields) {
		$fields['userid'] = intval($fields['userid']);
		$fields['jobid'] = intval($fields['jobid']);
		if ($fields['userid'] < 1 || $fields['jobid'] < 1) {
			return null;
		}
		$joberDao = $this->_getJoberDao();
		$result = $joberDao->add($fields);
		if ($result) {
			$this->increaseJobNum($fields['userid']);
		}
		return $result;
	}
	function getJoberByJobId($userId, $jobId) {
		$joberDao = $this->_getJoberDao();
		return $joberDao->getByJobId($userId, $jobId);
	}
	function updateJober($fields, $id) {
		$joberDao = $this->_getJoberDao();
		return $joberDao->update($fields, $id);
	}
	function countJoberByJobId($jobid) {
		$joberDao = $this->_getJoberDao();
		return $joberDao->countByJobId($jobid);
	}
	function updateJoberByJobId($fieldData, $jobid, $userid) {
		$joberDao = $this->_getJoberDao();
		return $joberDao->updateByJobId($fieldData, $jobid, $userid);
	}
	function deleteJober($id){
		$joberDao = $this->_getJoberDao();
		return $joberDao->delete($id);
	}
	/*
	* �����������б�
	*/
	function getAppliedJobs($userid) {
		$joberDao = $this->_getJoberDao();
		$jobers = $joberDao->getAppliedJobs($userid);
		if (!$jobers) {
			return array();
		}
		return $this->buildJobListByIds($jobers);
	}
	/*
	* ���������
	*/
	function getFinishJobs($userid) {
		$joberDao = $this->_getJoberDao();
		$jobers = $joberDao->getFinishJobs($userid);
		if (!$jobers) {
			return array();
		}
		return $this->buildJobListByIds($jobers);
	}
	/*
	* �ѷ�������
	*/
	function getQuitJobs($userid) {
		$joberDao = $this->_getJoberDao();
		$jobers = $joberDao->getQuitJobs($userid);
		if (!$jobers) {
			return array();
		}
		return $this->buildJobListByIds($jobers);
	}
	function buildJobListByIds($jobers) {
		if (!$jobers) {
			return array();
		}
		$jobIds = $tmp = array();
		foreach($jobers as $job) {
			$jobIds[] = $job['jobid'];
			$tmp[$job['jobid']] = $job;
		}
		$jobs = $this->getJobsByIds($jobIds);
		if (!$jobs) {
			return array();
		}
		$result = array();
		foreach($jobs as $job) {
			if ((isset($job['isuserguide']) && $job['isuserguide'])) continue; //���û���������
			$result[] = array_merge($tmp[$job['id']], $job);
		}
		return $result;
	}
	/*����IDs��ȡ�����б�*/
	function getJobsByIds($jobIds) {
		//file cache
		if ($this->_cache) {
			$jobs = $this->getFileCache();
			if ($jobs) {
				$result = array();
				foreach($jobs as $job) {
					if (in_array($job['id'], $jobIds)) {
						$result[] = $job;
					}
				}
				return $result;
			}
		}
		$jobDao = $this->_getJobDao();
		return $jobDao->getByIds($jobIds);
	}
	/*
	* ����ͼƬ�ϴ�
	*/
	function upload($fileArray) {
		$pictureClass = L::loadClass('updatepicture');
		$pictureClass->init($this->_getDicroty());
		$filename = $pictureClass->upload($fileArray);
		return $filename;
	}
	/*
	* ����ͼƬ�ϴ�Ŀ¼
	*/
	function _getDicroty() {
		global $db_attachname;
		$attachment = $db_attachname ? $db_attachname : 'attachment';
		return R_P . $attachment . "/job/";
	}
	function _getJobDao() {
		$job = L::loadDB('job', 'job');
		return $job;
	}
	function _getJoberDao() {
		$job = L::loadDB('jober', 'job');
		return $job;
	}
	function _getJobDoerDao() {
		$job = L::loadDB('jobdoer', 'job');
		return $job;
	}
	function getConfig() {
		$config = $this->loadJob('config');
		return $config;
	}
	function getJobTypes($k = null) {
		$config = $this->getConfig();
		return $config->getJobType($k);
	}
	function getJobType($k = null) {
		$config = $this->getConfig();
		return $config->jobs($k);
	}
	function getCondition($job) {
		$config = $this->getConfig();
		return $config->condition($job);
	}
	function getJobLists($checked) {
		$config = $this->getConfig();
		$jobs = $config->jobs();
		$jobHtml = $jobInfo = "";
		foreach($jobs as $k => $v) {
			$jobHtml .= '<li id="' . $k . '"><a href="javascript:;" hidefocus="true">' . $v . '</a></li>';
			$jobInfo .= $this->getJobData($k, $config->$k(), $checked);
		}
		return array(
			$jobHtml,
			$jobInfo
		);
	}
	function getJobData($id, $data, $checked) {
		$html = '<ul id="job_' . $id . '" style="display:none;" class="list_A">';
		foreach($data as $k => $v) {
			$checkHtml = ($checked == $k) ? "checked" : "";
			$html .= '<li><input name="factor[job]" type="radio" value="' . $k . '" ' . $checkHtml . '/>' . $v . '</li>';
		}
		$html .= '</ul>';
		return $html;
	}
	/*
	* ��������ѡ��
	*/
	function getJobsSelect($select, $name, $id) {
		$jobs = $this->getJobAll();
		$result = array();
		foreach($jobs as $job) {
			$result[$job['id']] = $job['title'];
		}
		$result = ($result) ? $result : array(
			'-1' => "��������"
		);
		return $this->_buildSelect($result, $name, $id, $select, true);
	}
	/*
	* ��ȡ�û��鸴ѡ��
	*/
	function getLevelCheckbox($checkeds = array(), $name = 'usergroup[]', $id = 'usergroup') {
		list($result) = $this->getLevels();
		$html .= "<div class=\"admin_table_c\"><table cellpadding=\"0\" cellspacing=\"0\">";
		$html .= "<tr class=\"vt\"><th class=\"s4\">ϵͳ��</th><td><ul class=\"cc list_A list_120 fl\">" . $this->_buildCheckbox($result['system'], $name, $id, $checkeds) . "</ul></td></tr>";
		$html .= "<tr class=\"vt\"><th class=\"s4\">��Ա��</th><td><ul class=\"cc list_A list_120 fl\">" . $this->_buildCheckbox($result['member'], $name, $id, $checkeds) . "</ul></td></tr>";
		$html .= "<tr class=\"vt\"><th class=\"s4\">������</th><td><ul class=\"cc list_A list_120 fl\">" . $this->_buildCheckbox($result['special'], $name, $id, $checkeds) . "</ul></td></tr>";
		$html .= "</table></div>";
		//$html .= "Ĭ���飺".$this->_buildCheckbox($result['default'],$name,$id,$checkeds)."<br />";
		return $html;
	}
	/*
	* ��ȡ�û���������
	*/
	function getLevelSelect($select, $name, $id, $gptype = '') {
		list($result, $selects) = $this->getLevels();
		if ($gptype) {
			$selects = $result[$gptype];
		}
		return $this->_buildSelect($selects, $name, $id, $select);
	}
	/*
	* ��ȡ�û���
	*/
	function getLevels() {
		$query = $this->_db->query("SELECT * FROM pw_usergroups");
		$result = $selects = array();
		while ($rs = $this->_db->fetch_array($query)) {
			$result[$rs['gptype']][$rs['gid']] = $rs['grouptitle'];
			$selects[$rs['gid']] = $rs['grouptitle'];
		}
		return array(
			$result,
			$selects
		);
	}
	function getCacheLevels() {
		//* @include pwCache::getPath(R_P . "data/bbscache/level.php");
		extract(pwCache::getData(R_P . "data/bbscache/level.php", false));
		if ($ltitle) {
			return array(
				'',
				$ltitle
			);
		}
		return $this->getLevels();
	}
	/*
	* ��ȡ����������
	*/
	function getCreditSelect($select, $name, $id) {
		$credits = pwCreditNames();
		return $this->_buildSelect($credits, $name, $id, $select);
	}
	/*
	* ��������ʾ ǰ̨
	*/
	function getCategoryInfo($reward, $num = 1) {
		$reward = unserialize($reward);
		$category = $reward['category'];
		switch ($category) {
			case "credit":
				$title = explode(" ", $reward['information']);
				return array('prefix'=>$title[0], 'title'=>$title[1], 'num'=>$reward['num'] * $num, 'unit'=>pwCreditUnits($reward['type']));
				break;

			case "tools":
				$title = explode(" ", $reward['information']);
				return array('prefix'=>$title[0], 'title'=>$title[1], 'num'=>$reward['num'] * $num, 'unit'=>'��');
				break;

			case "medal":
				$title = explode(" ", $reward['information']);
				return array('prefix'=>$title[0], 'title'=>$title[1], 'suffix'=>$title[2]);
				break;

			case "usergroup":
				$title = explode(" ", $reward['information']);
				return array('prefix'=>$title[0], 'title'=>$title[1], 'suffix'=>$title[2], 'num'=>$reward['day'] * $num, 'unit'=>'��');
				break;

			case "invitecode":
				$title = explode(" ", $reward['information']);
				return array('prefix'=>$title[0], 'title'=>$title[1], 'num'=>$reward['num'] * $num, 'unit'=>'��');
				break;

			default:
				return array();
				break;
		}
	}
	/*��װ����ǰ׺��Ϣ ��̨*/
	function buildCategoryInfo($reward, $num = 1) {
		$category = $reward['category'];
		switch ($category) {
			case "credit":
				return "�ɻ�� " . pwCreditNames($reward['type']) . " ";
				break;

			case "tools":
				$tools = $this->getTools();
				return "�ɻ�õ��� " . $tools[$reward['type']] . " ";
				break;

			case "medal":
				$medals = $this->getMedals();
				return "�ɻ��ѫ�� " . $medals[$reward['type']];
				break;

			case "usergroup":
				list($result, $selects) = $this->getLevels();
				return "��Ϊ " . $selects[$reward['type']] . " ��Ч�� ";
				break;

			case "invitecode":
				return "�ɻ�� ����ע���� ";
				break;

			default:
				return "";
				break;
		}
	}
	/*��װǰ̨���������ϸҳ����*/
	function buildCountCategoryInfo($reward, $num = 1) {
		$reward = unserialize($reward);
		$category = $reward['category'];
		switch ($category) {
			case "credit":
				return "����� " . pwCreditNames($reward['type']) . " " . $reward['num'] * $num . " " . pwCreditUnits($reward['type']);
				break;

			case "tools":
				$tools = $this->getTools();
				return "����õ��� " . $tools[$reward['type']] . " " . $reward['num'] * $num . " ��";
				break;

			case "medal":
				$medals = $this->getMedals();
				return "�����ѫ�� " . $medals[$reward['type']] . " ��Ч�� " . $reward['day'] * $num . " ��";
				break;

			case "usergroup":
				list($result, $selects) = $this->getLevels();
				return "��Ϊ " . $selects[$reward['type']] . " ��Ч�� " . $reward['day'] * $num . " ��";
				break;

			case "invitecode":
				return "���������ע���� " . $reward['num'] * $num . " ��";
				break;

			default:
				return "";
				break;
		}
	}
	/*
	* ��ȡѫ��������
	*/
	function getMedalSelect($select, $name, $id) {
		$medials = $this->getMedals();
		return $this->_buildSelect($medials, $name, $id, $select);
	}
	/*
	* ��ȡѫ��
	*/
	function getMedals() {
		$medalService = L::loadClass('MedalService', 'medal'); /* @var $medalService PW_MedalService */
		$medials = $medalService->getAllOpenManualMedals();
		$result = array();
		foreach ($medials as $v) {
			$result[$v['medal_id']] = $v['name'];
		}
	
		return $result;
	}
	/*
	* ���԰�
	*/
	function getLanguage($k) {
		$data = array();
		$data['job_title_null'] = "�������Ʋ���Ϊ��";
		$data['job_description_null'] = "������������Ϊ��";
		$data['upload_icon_fail'] = "����ͼ���ϴ�ʧ��";
		$data['add_job_success'] = "�����������";
		$data['job_id_null'] = "����ID��Ч";
		$data['job_sequence_null'] = "����˳����С��0";
		$data['job_id_error'] = "��Ǹ������ID����"; /*��������*/
		$data['job_not_exist'] = "��Ǹ������������񲻴���";
		$data['job_usergroup_limit'] = "��Ǹ�������ڵ��û��鲻������";
		$data['job_time_limit'] = "��Ǹ��������������Ѿ�����";
		$data['job_time_early'] = "��Ǹ�������������û�п�ʼ";
		$data['job_close'] = "��Ǹ��������������Ѿ��ر�";
		$data['job_has_perpos'] = "��Ǹ������������������������������";
		$data['job_has_apply'] = "��Ǹ�����Ѿ��������������";
		$data['job_apply_next_limit'] = "��Ǹ����û����һ����������ʱ��";
		$data['job_apply_success'] = "��ϲ�������������";
		$data['job_apply_number_limit'] = "��Ǹ��������������������";
		$data['job_apply_fail'] = "��Ǹ����������ʧ��";
		$data['job_has_perpos_more'] = "��Ǹ���㻹û������������";
		$data['job_stime_r_etime'] = "��Ǹ������ʼʱ����ڽ���ʱ��";
		$data['use_not_exists'] = "��Ǹ��Ҫѡ����Ϣ��ָ����Ա����Ϊ��";
		return $data[$k];
	}
	/*
	* ��ȡ����������
	*/
	function getToolsSelect($select, $name, $id) {
		$tools = $this->getTools();
		return $this->_buildSelect($tools, $name, $id, $select);
	}
	/*
	* ��ȡ����
	*/
	function getTools() {
		$query = $this->_db->query("SELECT * FROM pw_tools");
		$result = $special = $member = $default = $system = array();
		while ($rs = $this->_db->fetch_array($query)) {
			$result[$rs['id']] = $rs['name'];
		}
		return $result;
	}
	/*
	* ��װ������
	*/
	function _buildSelect($arrays, $name, $id, $select = '', $isEmpty = false) {
		if (!is_array($arrays)) {
			return '';
		}
		$html = '<select name="' . $name . '" id="' . $id . '" class="select_wa">';
		($isEmpty == true) && $html .= '<option value=""></option>';
		foreach($arrays as $k => $v) {
			$selected = ($select == $k && $select != null) ? 'selected="selected"' : "";
			$html .= '<option value="' . $k . '" ' . $selected . '>' . $v . '</option>';
		}
		$html .= '</select>';
		return $html;
	}
	/*
	* ��װ��ѡ��
	*/
	function _buildCheckbox($arrays, $name, $id, $checkeds = array()) {
		if (!is_array($arrays)) {
			return '';
		}
		$html = '';
		foreach($arrays as $k => $v) {
			$checked = (in_array($k, $checkeds)) ? "checked" : "";
			$html .= '<li><input type="checkbox" value="' . $k . '" name="' . $name . '" id="' . $id . '" ' . $checked . '/>' . $v . "</li>";
		}
		return $html;
	}
	/*
	* �����ļ�����
	*/
	function setFileCache() {
		$jobDao = $this->_getJobDao();
		$jobs = $jobDao->getAll();
		$jobLists = "\$jobLists=" . pw_var_export($jobs) . ";";
		pwCache::setData($this->getCacheFileName(), "<?php\r\n" . $jobLists . "\r\n?>");
		return $jobs;
	}
	/*
	* ��ȡ�ļ�����
	*/
	function getFileCache() {
		if (!$this->_cache) {
			return array(); /*not open cache*/
		}
		static $jobLists = null;
		if(!isset($jobLists)){
			//* @include_once pwCache::getPath(S::escapePath($this->getCacheFileName()),true);
			extract(pwCache::getData(S::escapePath($this->getCacheFileName()), false));
			$jobLists = ($jobLists) ? $jobLists : $GLOBALS['jobLists'];
		}
		if ($jobLists) {
			return $jobLists;
		}
		return $this->setFileCache();
	}
	/*��ȡ�����ļ�·��*/
	function getCacheFileName() {
		return R_P . "data/bbscache/jobs.php";
	}
	function checkIsOpenMedal() {
		global $db_md_ifopen;
		return $db_md_ifopen ? true:false;
	}
	function checkIsOpenInviteCode() {
		$fileName = D_P . 'data/bbscache/dbreg.php';
		if (!is_file($fileName)) {
			return false;
		}
		//* @include pwCache::getPath(S::escapePath($fileName));
		extract(pwCache::getData(S::escapePath($fileName), false));
		if ($rg_allowregister == 2) {
			return true;
		}
		return false;
	}
	function countAppliedJobs($userid) {
		$joberDao = $this->_getJoberDao();
		return $joberDao->countAppliedJobs($userid);
	}
	/*����������*/
	function updateJobNum($userid) {
		$jobnum = $this->countJobnum($userid); /*ֱ�Ӳ�ѯ����*/
		($jobnum > 0) ? $jobnum : 0;
		$userService = $this->_getUserService();
		return $userService->update($userid, array(), array('jobnum' => $jobnum));
	}
	
	/**
	 * ͳ���������������
	 * @param int $userId
	 * 
	 */
	function countJobNum($userId) {
		if(!$userId) return false;
		$joblists = $this->getAppliedJobs($userId); 
		$joblists = $joblists ? $joblists : array();
		$num = 0;
		foreach($joblists as $job) {//��ʾ���� �Ƿ���ʾ
			if ($job['isopen'] == 0) continue;
			$num++;
		}
		return $num;
	}
	
	/*����һ��������*/
	function increaseJobNum($userid) {
		$this->updateJobNum($userid);
	}
	/*����һ��������*/
	function reduceJobNum($userid) {
		$this->updateJobNum($userid);
	}
	/*��װ�����������б�*/
	function buildApplieds($winduid, $groupid) {
		$joblists = $this->getAppliedJobs($winduid);
		$jobs = $this->buildLists($joblists, 'applied', $winduid, $groupid);
		if (!$jobs) {
			return '';
		}
		$html = '';
		foreach($jobs as $job) {
			$html .= $this->buildApplied($job);
		}
		return $html;
	}
	/*��װ����������������*/
	function buildApplied($list) {
		$list['title'] = substrs($list['title'],56);
		$html = '';
		$html .= '<div id="applied_' . $list[id] . '">';
		$html .= '<div class="jobpop_h current"><a href="javascript:;" class="menu_tasksA_title" hidefocus="true"><b></b>' . $list[title] . ' <span>' . $list[gain] . '</span></a></div>';
		$html .= '	<dl class="cc taskA_dl" style="display:none;">';
		$html .= '    <dt><img src="' . $list[icon] . '" /></dt>';
		$html .= '    <dd>';
		$html .= '    <table width="100%" style="table-layout:fixed;">';
		$html .= '        <tr class="vt">';
		$html .= '			<td width="80">�������:</td>';
		$html .= '    		<td id="job_condition_' . $list[id] . '">' . $list[condition] . '</td>';
		$html .= '		  </tr>';
		$html .= '        <tr class="vt">';
		$html .= '            <td>��ɽ���:</td>';
		$html .= '            <td class="s2">' . $list[reward] . '</td>';
		$html .= '        </tr>';
		$html .= '            <tr class="vt">';
		$html .= '            <td>��������:</td>';
		$html .= '                <td>' . $list[description] . '</td>';
		$html .= '         </tr>';
		$html .= '         <tr class="vt">';
		$html .= '            <td></td>';
		$html .= '                <td><span class="fr">' . $list[btn] . '</span></td>';
		$html .= '         </tr>';
		$html .= '   </table>';
		$html .= '   </dd>';
		$html .= '  </dl>';
		$html .= '</div>';
		return $html;
	}
	/*
	* ����������
	*/
	function loadJob($name) {
		static $classes = array();
		$name = strtolower($name);
		$filename = R_P . "lib/job/job/" . $name . ".job.php";
		if (!is_file($filename)) {
			return null;
		}
		$class = 'JOB_' . ucfirst($name);
		if (isset($classes[$class])) {
			return $classes[$class];
		}
		include S::escapePath($filename);
		$classes[$class] = new $class();
		return $classes[$class];
	}
	
	/**
	 * @return PW_UserService
	 */
	function _getUserService() {
		return L::loadClass('UserService', 'user');
	}
}

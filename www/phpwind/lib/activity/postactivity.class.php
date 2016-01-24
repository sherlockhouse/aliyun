<?php
!defined('P_W') && exit('Forbidden');

L::loadClass('Activity', 'activity', false);

class PW_PostActivity extends PW_Activity {

	var $db;
	var $post;
	var $forum;
	var $data;
	var $actid;
	var $actmid;
	var $tablename;
	var $timestamp;
	var $winduid;
	var $peopleAlreadySignup;
	var $peopleAlreadyPaid;
	/**
	 * @var Activity_FieldCheck �ֶδ������class
	 * @access protected
	 */
	var $fieldCheck;

	function PW_PostActivity($post = '') {
		global $actid,$actmid;
		$this->initGlobalValue();
		if (is_object($post)) {
			$this->post =& $post;
			$this->forum =& $post->forum;
		}
		$this->actid =& $actid;
		$this->actmid =& $actmid;
		$this->setPeopleAlreadySignup(0);
		$this->setPeopleAlreadyPaid(0);

		$this->fieldCheck = L::loadClass('ActivityFieldCheck', 'activity');

		$this->setActCache();
	}
	
	function initGlobalValue() {
		global $db,$timestamp,$winduid;
		$this->db =& $db;
		$this->winduid =& $winduid;
		$this->timestamp =& $timestamp;
		return $this;
	}

	/**
	 * ��ʼ���ϴ���Ϣ
	 */
	function initData() {
		global $db_actname,$tid,$limitnums;
		$act = S::getGP('act','P');

		$requiredTimes = array();
		$participantFields = array();
		$actdb = $data = array();
		$query = $this->db->query("SELECT fieldname,name,type,rules,ifmust,ifable,ifdel FROM pw_activityfield WHERE actmid=".S::sqlEscape($this->actmid)." ORDER BY ifdel ASC, vieworder ASC");
		while ($rt = $this->db->fetch_array($query)) {
			$data[] = $rt;
		}
		foreach ($data as $rt) {
			//�����л�����
			$rules = unserialize($rt['rules']);

			//����༭����²����ֶν�ֹ�޸ĵ����
			$defaultValueTableName = getActivityValueTableNameByActmid();
			if ($this->getPeopleAlreadySignup() || $this->getPeopleAlreadyPaid()) { //�������û�֧�����û���
				if ('paymethod' == $rt['fieldname'] && $this->getPeopleAlreadyPaid()) { //��ֹ�޸�֧����ʽ
					$act[$rt['fieldname']] = $this->db->get_value("SELECT paymethod FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));
					$act[$rt['fieldname']] || $act[$rt['fieldname']] = 2;
				} elseif ('fees' == $rt['fieldname']) { //��ֹ�޸ķ���
					continue;
				} elseif ('signupstarttime' == $rt['fieldname']) { //��ֹ�޸ı�����ʼʱ��
					$SignupStartTimestamp = $this->db->get_value("SELECT signupstarttime FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));
					$act[$rt['fieldname']] = $this->getTimeFromTimestamp($SignupStartTimestamp, $rules['precision']);
				}
			}

			//��ʼ������ص���ʾ����
			$this->fieldCheck->setErrorValue(str_replace(array('{@}', '{#}'), ' - ', $rt['name']));

			//������С��������Ϊ�Ǳ����֧����ʽΪ����������������������3�߹�ϵ�ر�ȡ������
			if (in_array($rt['fieldname'], array('minparticipant', 'maxparticipant', 'paymethod'))) {
				//��3���ֶε�ֵ���浽��ʱ������
				$participantFields[$rt['fieldname']] = $act[$rt['fieldname']];
				if (3 == count($participantFields)) { //3���ֶζ��ѱ���
					$errorKey = $this->fieldCheck->getParticipantError($act['paymethod'], $act['minparticipant'], $act['maxparticipant']);
				}
			}
			//�������ֶ��Ƿ�����д
			if ($rt['type'] != 'upload' && $rt['ifable'] && $rt['ifmust'] && $act[$rt['fieldname']] === '' && $rt['fieldname'] != 'minparticipant' && $rt['fieldname'] != 'maxparticipant') {//�������Ʊ��������ȥ��
				$db_actname = $this->fieldCheck->getErrorValue();
				Showmsg('act_field_must');
			} elseif ($rt['ifable']) {
				//Ԥ���ʱ������ֶΣ��ʱ�䣬����ʱ�䣩
				if (in_array($rt['fieldname'], array('starttime', 'endtime', 'signupstarttime', 'signupendtime'))) {
					$errorKey = $this->fieldCheck->getCalendarError($act[$rt['fieldname']]); //���ʱ���ʽ
					if (!$errorKey) { //��ʱ���ʽ�޴�
						//��4��ʱ���ֶε�ֵ���浽��ʱ������
						$requiredTimes[$rt['fieldname']] = $act[$rt['fieldname']];
						if (4 == count($requiredTimes)) { //��4��Ҫ����д��ʱ�䶼�ѱ��棬��ʼ����
							$this->fieldCheck->setErrorValue('�ʱ��');
							$errorKey = $this->fieldCheck->getTimeRangeError($requiredTimes['starttime'], $requiredTimes['endtime']);
							if (!$errorKey) {
								$this->fieldCheck->setErrorValue('����ʱ��');
								$errorKey = $this->fieldCheck->getTimeRangeError($requiredTimes['signupstarttime'], $requiredTimes['signupendtime']);
							}
							if (!$errorKey) {
								$this->fieldCheck->setErrorValue('');
								$errorKey = $this->fieldCheck->getActivityAndSignupTimeConflictError($requiredTimes['signupendtime'], $requiredTimes['starttime']);
							}
						}
					} else { //��ʾʱ���ʽ����
						$db_actname = $this->fieldCheck->getErrorValue();
						Showmsg($errorKey);
					}
				} elseif (in_array($rt['fieldname'], array('minparticipant', 'maxparticipant', 'paymethod'))) { //ǰ���Ѵ���
				} elseif ('fees' == $rt['fieldname']) { //����
					$act['fees'] && $errorKey = $this->fieldCheck->getFeesError($act['fees']);
					if (!$errorKey) { //�޴�
						$actdb[$rt['ifdel']][$rt['fieldname']] = serialize($this->fieldCheck->getFeesArray());
						continue; //����
					}
				} elseif ('feesdetail' == $rt['fieldname']) { //������ϸ
					$errorKey = $this->fieldCheck->getFeesDetailError($act['feesdetail']);
					if (!$errorKey && $this->fieldCheck->getFeesDetailArray()) {
						$actdb[$rt['ifdel']][$rt['fieldname']] = serialize($this->fieldCheck->getFeesDetailArray());
						continue;
					}
				} elseif ('telephone' == $rt['fieldname']) { //��ϵ�绰
					$errorKey = $this->fieldCheck->getTelephoneError($act['telephone']);
					if (!$errorKey) {
						$actdb[$rt['ifdel']][$rt['fieldname']] = $this->fieldCheck->getTelephones();
						continue;
					}
				} else { //�������⴦����ֶ�
					$errorKey = $this->fieldCheck->getError($rt['type'], $act[$rt['fieldname']], $rules);
					if ($errorKey) {
						$db_actname = $this->fieldCheck->getErrorValue();
						Showmsg($errorKey);
					}
				}
				if ($errorKey) {
					$errorMessage = $this->fieldCheck->getErrorMessageByKey($errorKey);
					Showmsg($errorMessage);
				} else {
					$fieldValueForDb = $this->fieldCheck->getValueForDb($rt['type'], $act[$rt['fieldname']]);
					$actdb[$rt['ifdel']][$rt['fieldname']] = $fieldValueForDb;
				}
			}
		}
		$this->data['act'] = $actdb;
	}

	/**
	 * �������ݿ�
	 * @param int $tid ����id
	 * @param int $fid ���id
	 */
	function insertData($tid,$fid) {/*�������ݿ�*/
		global $action,$atc_title;
		$this->data['default']				= $this->data['act']['0'];
		$this->data['default']['tid']		= $tid;
		$this->data['default']['fid']		= $fid;
		$this->data['default']['actmid']	= $this->actmid;
		$this->data['user'] = array();
		!S::isArray($this->data['act']['1']) &&  $this->data['act']['1'] = array();
		foreach ($this->data['act']['1'] as $key => $value) {
			if ($value) {
				$this->data['user'][$key] = $value;
			}
		}
		$this->data['user']['tid']			= $tid;
		$this->data['user']['fid']			= $fid;
		unset($this->data['act']);
		$defaultValueTableName = getActivityValueTableNameByActmid();
		$userDefinedValueTableName = getActivityValueTableNameByActmid($this->actmid, 1, 1);

		$this->db->pw_update(
			"SELECT tid FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid),
			"UPDATE $defaultValueTableName SET ".S::sqlSingle($this->data['default']) . "WHERE tid=".S::sqlEscape($tid),
			"INSERT INTO $defaultValueTableName SET " . S::sqlSingle($this->data['default'])
		);

		$this->db->pw_update(
			"SELECT tid FROM $userDefinedValueTableName WHERE tid=".S::sqlEscape($tid),
			"UPDATE $userDefinedValueTableName SET ".S::sqlSingle($this->data['user']) . "WHERE tid=".S::sqlEscape($tid),
			"INSERT INTO $userDefinedValueTableName SET " . S::sqlSingle($this->data['user'])
		);
		
		
		$subject = $this->db->get_value('SELECT subject FROM pw_threads WHERE tid=' . S::sqlEscape($tid));
		if ($subject){
			$this->db->update('UPDATE pw_activitypaylog SET subject=' . S::sqlEscape($subject) . ' WHERE tid=' . S::sqlEscape($tid));	
		}


		/*ѡ��֧����+û�а�֧����+û��ͨ��֧����ʵ����֤ or ����AA���*/
		if ($this->data['default']['paymethod'] == 1) {
			$tradeinfo		= $this->db->get_one("SELECT tradeinfo FROM pw_memberinfo WHERE uid=".S::sqlEscape($this->winduid));
			$tradeinfo		= unserialize($tradeinfo['tradeinfo']);
			$alipay			= $tradeinfo['alipay'];
			$isBinded		= $tradeinfo['isbinded'];
			$isCertified	= $tradeinfo['iscertified'];

			if (!$alipay || $isBinded != 'T' || $isCertified != 'T') {//ѡ��֧����+û�а�֧����+û��ͨ��֧����ʵ����֤
				$this->db->update("UPDATE $defaultValueTableName SET iscertified=0 WHERE tid=".S::sqlEscape($tid));
			} elseif ($alipay && $isBinded == 'T' && $isCertified == 'T') {//��֧����+ͨ��֧����ʵ����֤
				$this->db->update("UPDATE $defaultValueTableName SET iscertified=1 WHERE tid=".S::sqlEscape($tid));
				require_once(R_P . 'lib/activity/alipay_push.php');
				$alipayPush = new AlipayPush();
				if ($action == 'new') {
					$alipayPush->create_aa_payment($tid,$this->winduid,$this->actmid,$atc_title);//����AA���
				} elseif ($action == 'modify') {
					$alipayPush->modify_aa_payment($tid,$this->actmid,$atc_title);//�޸�AA���
				}
			}
		}
		/*ѡ��֧����+û�а�֧����+û��ͨ��֧����ʵ����֤ or ����AA���*/

		/*�����ϴ�*/
		L::loadClass('activityupload', 'upload', false);
		$img = new ActivityUpload($tid,$this->actmid);
		PwUpload::upload($img);
		require_once(R_P.'require/functions.php');
		pwFtpClose($GLOBALS['ftp']);

	}

	/**
	 * ������ͨ��־
	 * @param int $tid ����id
	 * @param int $actuid �����б�id
	 * @param int $status �״̬ 1������� 2����� 3�ȡ�� 4�ɾ��
	 * @param int $wherefrom ��Դ 0��̳ 1Ⱥ��
	 * @param bool true
	 * @access private
	 */
	function UpdatePayLog ($tid,$actuid = 0,$status = 1,$wherefrom = 0) {
		if (is_array($tid)){
			foreach ($tid as $tidValue){
				$tidValue = (int)$tidValue;
				$this->UpdatePayLog($tidValue,$actuid,$status,$wherefrom);
			}
		} else {
			$tid = (int)$tid;
			$status = (int)$status;
			if (!$wherefrom) {
				$this->db->query("SELECT tid FROM pw_activitypaylog WHERE tid=".S::sqlEscape($tid) . " AND actuid=" . S::sqlEscape($actuid));
				$affected_rows = $this->db->affected_rows();
				if ($affected_rows || $actuid == 0) {
					$this->db->update("UPDATE pw_activitypaylog SET status=".S::sqlEscape($status) ." WHERE tid=".S::sqlEscape($tid));
				} else {
					$read = $this->db->get_one("SELECT subject,author,authorid FROM pw_threads WHERE tid=".S::sqlEscape($tid));
					$userdb = $this->db->get_one("SELECT uid,username,totalcash,issubstitute,isadditional,isrefund,fromusername,fromuid,ifpay 
												FROM pw_activitymembers 
												WHERE actuid=".S::sqlEscape($actuid));
	
					if ($userdb['issubstitute'] == 1) {//�Ƿ����
						//$uid = $userdb['fromuid'];
						//$username = $userdb['fromusername'];
						$fromuid = $userdb['fromuid'];
						$fromusername = $userdb['fromusername'];
						$uid = $userdb['uid'];
						$username = $userdb['username'];
					} else {
						$uid = $userdb['uid'];
						$username = $userdb['username'];
					}
					
					
					if ($userdb['ifpay'] == 2) {//4ȷ��֧��
						if ($userdb['isadditional'] == 1){
							$costtype = 2;
						}else{
							$costtype = 4;
						}
					} elseif ($userdb['isrefund'] == 1) {//3�˿�ɹ�
						$costtype = 3;
					} elseif ($userdb['isadditional'] == 1) {//2׷��֧���ɹ�
						$costtype = 2;
					} else {//1��֧ͨ���ɹ�
						$costtype = 1;
					}
	
					$sqlArray = array(
						'tid'		=> $tid,
						'actuid'	=> $actuid,
						'uid'		=> $uid,
						'username'	=> $username,
						'authorid'	=> $read['authorid'],
						'author'	=> $read['author'],
						'cost'		=> $userdb['totalcash'],
						'costtype'	=> $costtype,
						'status'	=> $status,
						'createtime'=> $this->timestamp,
						'subject'	=> $read['subject'],
						'wherefrom'	=> $wherefrom,
						'fromuid'   => $fromuid,
						'fromusername' => $fromusername
					);
					
					$this->db->pw_update(
						"SELECT tid FROM pw_activitypaylog WHERE actuid=".S::sqlEscape($actuid)." AND costtype=".S::sqlEscape($costtype),
						"UPDATE pw_activitypaylog SET ".S::sqlSingle($sqlArray) . "WHERE actuid=".S::sqlEscape($actuid)." AND costtype=".S::sqlEscape($costtype),
						"INSERT INTO pw_activitypaylog SET " . S::sqlSingle($sqlArray)
					);
				}
			}
		}
		return true;
	}

	/**
	 * ����ͼƬ
	 * @param string $path ͼƬ��ַ
	 * @param bool $thumb �Ƿ�����ͼ
	 * @access private
	 */
	function getActivityImgUrl($path,$thumb = false) {
		global $attachdir,$db_ftpweb;
		$lastpos = strrpos($path,'/') + 1;
		$s_path = substr($path, 0, $lastpos) . 's_' . substr($path, $lastpos);
		
		if ($db_ftpweb && !file_exists("$attachdir/$path")) {
			//if ($fp = @fopen($db_ftpweb.'/'.$s_path,'rb')) { //fopen���ܵ��·�������ס
				//@fclose($fp);
				$newpath = $s_path;
			//} else {
				//$newpath = $path;
			//}
		} elseif (file_exists("$attachdir/$s_path") && $thumb) {
			$newpath = $s_path;
		} else {
			$newpath = $path;
		}
		list($newpath) = geturl($newpath, 'show');
		return $newpath;
	}
	
	/**
	 * @return int
	 */
	function getPeopleAlreadySignup () {
		return $this->peopleAlreadySignup;
	}
	/**
	 * @param int $number
	 * @return PW_PostActivity
	 */
	function setPeopleAlreadySignup ($number) {
		$this->peopleAlreadySignup = (int)$number;
		return $this;
	}
	/**
	 * @return int
	 */
	function getPeopleAlreadyPaid () {
		return $this->peopleAlreadyPaid;
	}
	/**
	 * @param int $number
	 * @return PW_PostActivity
	 */
	function setPeopleAlreadyPaid ($number) {
		$this->peopleAlreadyPaid = (int)$number;
		return $this;
	}
	
	/**
	 * ���ط���ģ����Ϣ
	 * @param int $actmid ��������id
	 * @param bool $keytype ���ز�ͬ����
	 * @return array ����
	 * @access private
	 */
	function getFieldData($actmid, $keytype = true) {
		$sql = '';
		$fielddb = array();
		if (is_array($actmid)) {
			$sql .= " WHERE actmid IN(".S::sqlImplode($actmid).")";
		} elseif ($actmid > 0 && is_numeric($actmid)) {
			$sql .= " WHERE actmid=".S::sqlEscape($actmid);
		} else {
			$sql .= '';
		}
		$query = $this->db->query("SELECT fieldid,name,fieldname,actmid,vieworder,type,rules,ifable,ifsearch,ifasearch,ifmust,threadshow,sectionname FROM pw_activityfield $sql ORDER BY ifdel ASC, vieworder ASC, fieldid ASC");
		while ($rt = $this->db->fetch_array($query)) {
			$rt['name'] = str_replace('{#}','',$rt['name']);
			if ($keytype == false) {
				$fielddb[$rt['fieldname']] = $rt['fieldid'];
			} else {
				$fielddb[$rt['fieldname']] = $rt;
			}
		}
		return $fielddb;
	}
	
	function getSearchvalue($field,$type,$alltidtype = false,$backtype = false) {/*��ȡ�������*/
		global $db_perpage,$page,$actmid,$fid,$basename;
		$field = unserialize(StrCode($field,'DECODE'));

		$sqladd = '';
		$defaultValueTableName = getActivityValueTableNameByActmid();

		$fid && $sqladd .= " $defaultValueTableName.fid=".S::sqlEscape($fid);
		$fielddb = PW_PostActivity::getFieldData($actmid,$type);

		if ($actmid) {
			$userDefinedTableName = getActivityValueTableNameByActmid($actmid, 1, 1);
		} else {
			$userDefinedTableName = '';
		}	

		foreach ($field as $key => $value) {
			if ($value) {
				if ($fielddb[$key]['ifdel']) {
					$tableName = $userDefinedTableName.'.';
				} elseif ($fielddb[$key]) {
					$tableName = $defaultValueTableName ? $defaultValueTableName.'.' : '';
				} else {
					continue;
				}
				if (in_array($fielddb[$key]['type'],array('number','radio','select'))) {
					$sqladd .= $sqladd ? " AND ".$tableName.$fielddb[$key]['fieldname']."=".S::sqlEscape($value) : $tableName.$fielddb[$key]['fieldname']."=".S::sqlEscape($value);
				} elseif ($fielddb[$key]['type'] == 'checkbox') {
					$checkboxs = '';
					foreach ($value as $cv) {
						$checkboxs .= $checkboxs ? ','.$cv : $cv;
					}
					$value = '%,'.$checkboxs.',%';
					$sqladd .= $sqladd ? " AND ".$tableName.$fielddb[$key]['fieldname'] ." LIKE(".S::sqlEscape($value).")" : $tableName.$fielddb[$key]['fieldname'] ." LIKE(".S::sqlEscape($value).")";
				} elseif ($fielddb[$key]['type'] == 'calendar') {
					$value && $value = PwStrtoTime($value);
					if (strpos($fielddb[$key]['fieldname'],'start') !== false){
						$sqladd .= $sqladd ? " AND ".$tableName.$fielddb[$key]['fieldname'].">=".S::sqlEscape($value): 
					                     $tableName.$fielddb[$key]['fieldname'].">=".S::sqlEscape($value);
					}elseif (strpos($fielddb[$key]['fieldname'],'end') !== false){
						$starttimeFlag = substr($fielddb[$key]['fieldname'],0,-7) . 'starttime';
						if ($value <= PwStrtoTime($field[$starttimeFlag]) && $field[$starttimeFlag]){
							Showmsg('calendar_error');
						}
						
						$sqladd .= $sqladd ? " AND ".$tableName.$starttimeFlag . "<=".S::sqlEscape($value): 
					                     $tableName.$starttimeFlag . "<=".S::sqlEscape($value);
					}else{
						$sqladd .= $sqladd ? " AND ".$tableName.$fielddb[$key]['fieldname'].">=".S::sqlEscape($value['start']).
										 " AND ".$tableName.$fielddb[$key]['fieldname']."<=".S::sqlEscape($value['end']) : 
					                     $tableName.$fielddb[$key]['fieldname'].">=".S::sqlEscape($value['start']).
										 " AND ".$tableName.$fielddb[$key]['fieldname']."<=".S::sqlEscape($value['end']);
					}
					
				} elseif (in_array($fielddb[$key]['type'],array('text','url','email','textarea'))) {
					$value = '%'.$value.'%';
					$sqladd .= $sqladd ? " AND ".$tableName.$fielddb[$key]['fieldname'] ." LIKE(".S::sqlEscape($value).")" : $tableName.$fielddb[$key]['fieldname'] ." LIKE(".S::sqlEscape($value).")";
				} elseif ($fielddb[$key]['type'] == 'range' && $value['min'] && $value['max']) {
					$sqladd .= $sqladd ? " AND ".$tableName.$fielddb[$key]['fieldname'].">=".S::sqlEscape($value['min'])." AND ".$tableName.$fielddb[$key]['fieldname']."<=".S::sqlEscape($value['max']) : $tableName.$fielddb[$key]['fieldname'].">=".S::sqlEscape($value['min'])." AND ".$tableName.$fielddb[$key]['fieldname']."<=".S::sqlEscape($value['max']);
				} else {
					$sqladd .= '';
				}
			}
		}
		if ($sqladd) {
			!$page && $page = 1;
			$start = ($page-1)*$db_perpage;
			$limit = S::sqlLimit($start,$db_perpage);


			$actmidSql = $actmid ? "AND actmid=" . S::sqlEscape($actmid) : '';

			$sqladd .= $sqladd ? " AND $defaultValueTableName.ifrecycle=0 " . $actmidSql : " $defaultValueTableName.ifrecycle=0 " . $actmidSql;
			$count = $this->db->get_value("SELECT COUNT(*) as count FROM $defaultValueTableName ".($userDefinedTableName ? "LEFT JOIN $userDefinedTableName USING (tid)" : "")." WHERE $sqladd");
			$query = $this->db->query("SELECT tid 
									FROM $defaultValueTableName ".($userDefinedTableName ? "LEFT JOIN $userDefinedTableName USING (tid)" : "")." 
									WHERE $sqladd $limit");
			while ($rt = $this->db->fetch_array($query)) {
				$tiddb[] = $rt['tid'];
			}
			if ($alltidtype) {
				$query = $this->db->query("SELECT tid FROM $defaultValueTableName ".($userDefinedTableName ? "LEFT JOIN $userDefinedTableName USING (tid)" : "")." WHERE $sqladd");
				while ($rt = $this->db->fetch_array($query)) {
					$alltiddb[] = $rt['tid'];
				}
			}
			!$count && $count = -1;
		} else {
			if ($backtype) {
				adminmsg('topic_search_none',"$basename&action=topic&actmid=$actmid");
			}
			Showmsg('topic_search_none');
		}

		return array($count,$tiddb,$alltiddb);
	}
	
	/**
	 * ���ػ��ص�ʱ��ֵ
	 * @param int $timestamp ʱ���
	 * @param string $precision ��ȷֵ����Ϊ'minute'��'day'
	 * @return string ʱ��
	 */
	function getTimeFromTimestamp ($timestamp, $precision = 'minute') {
		if ('minute' == $precision) { //ʱ�侫ȷ����
			return get_date($timestamp,'Y-n-j H:i');
		} else { //ʱ�侫ȷ����
			return get_date($timestamp,'Y-n-j');
		}
	}

	/**
	 * �����ڵ�ǰ���ӱ����б��е�uid
	 * @param int $tid ����id
	 * @return int ����uid
	 * @access private
	 */
	function getOrderMemberUid($tid) {// to do act
		$orderUid = $this->db->get_value("SELECT uid FROM pw_activitymembers WHERE tid=".S::sqlEscape($tid)." AND uid=".S::sqlEscape($this->winduid));
		return $orderUid;
	}
	/**
	 * ���ص�ǰ���ӱ�������
	 * @param int $tid ����id
	 * @return int ��������
	 * @access private
	 */
	function peopleAlreadySignup($tid) {
		$peopleAlreadySignup = $this->db->get_value("SELECT SUM(signupnum) as sum FROM pw_activitymembers WHERE tid=".S::sqlEscape($tid)." AND fupid=0 AND ifpay IN('0','1','2','4')");
		return $peopleAlreadySignup;
	}

	/**
	 * ���ص�ǰ�����Ѿ�֧������
	 * @param int $tid ����id
	 * @return int ��������
	 * @access private
	 */
	function peopleAlreadyPaid($tid) {
		$peopleAlreadyPaid = $this->db->get_value("SELECT SUM(signupnum) as sum FROM pw_activitymembers WHERE tid=".S::sqlEscape($tid)." AND ifpay IN('1','2','4')");
		return $peopleAlreadyPaid;
	}	
	
	/**
	 * ���ػ���ӵ�actmid
	 * @param int $tid ����id
	 * @return int actmid
	 * @access private
	 */
	function getActmid($tid) {
		$defaultValueTableName = getActivityValueTableNameByActmid();
		$actmid = $this->db->get_value("SELECT actmid FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));
		return (int)$actmid;
	}

	/**
	 * ����ĳ�����ĳ��actmid��ӵ�е�tid��
	 * @param int $actmid ģ��id
	 * @param int $fid ���id
	 * @return int array
	 * @access private
	 */
	function getActTidDb($actmid,$fid) {
		$defaultValueTableName = getActivityValueTableNameByActmid();
		$actTidDb = array();
		$query = $this->db->query("SELECT tid FROM $defaultValueTableName WHERE actmid=".S::sqlEscape($actmid)." AND fid=".S::sqlEscape($fid) ." AND ifrecycle=0");
		while ($rt = $this->db->fetch_array($query)) {
			$actTidDb[] = $rt['tid'];
		}
		return $actTidDb;
	}

	/**
	 * ����ÿ���ÿ���û�֧���������ı���
	 * @param int $tid ����id
	 * @param int $uid �û�id
	 * @return int ����
	 */
	function getAlipayPayedNum ($tid, $uid) {
		$payednum = 0;
		$query = $this->db->query("SELECT actuid,batch_detail_no FROM pw_activitymembers WHERE isrefund=0 AND tid=".S::sqlEscape($tid)." AND uid=".S::sqlEscape($uid));
		while ($rt = $this->db->fetch_array($query)) {
			if ($rt['batch_detail_no']) {
				$payednum++;
			}
		}
		return $payednum;
	}

	/**
	 * ����ӱ�ɾ��ʱ���Ͷ���Ϣ
	 * @param int $tid ����id
	 * @param int $uid �û�id
	 * @return int ����
	 */
	function activityDelSendmsg ($tiddb) {
		require_once R_P.'require/msg.php';
		$query = $this->db->query("SELECT subject,author FROM pw_threads WHERE tid IN(".S::sqlImplode($tiddb).")");
		while ($rt = $this->db->fetch_array($query)) {
			$msgdb[] = array(
				'toUser'	=> $rt['author'],
				'subject'	=> 'activity_delete_title',
				'content'	=> 'activity_delete_content',
				'other'		=> array(
					'subject'	=> $rt['subject'],
				)
			);
		}
		$query = $this->db->query("SELECT DISTINCT uid,username,subject FROM pw_activitymembers am LEFT JOIN pw_threads t ON am.tid=t.tid WHERE am.tid IN(".S::sqlImplode($tiddb).")");
		while ($rt = $this->db->fetch_array($query)) {
			$signupermsgdb[] = array(
				'toUser'	=> $rt['username'],
				'subject'	=> 'activity_delete_title',
				'content'	=> 'activity_delete_signuper_content',
				'other'		=> array(
					'subject'	=> $rt['subject'],
				)
			);
		}
		if ($msgdb) {
			foreach ($msgdb as $key => $value) {
				M::sendNotice(
					array($value['toUser']),
					array(
						'title' => getLangInfo('writemsg', $value['subject'], $value['other']),
						'content' => getLangInfo('writemsg', $value['content'], $value['other'])
					),'notice_active', 'notice_active'
				);
			}
		}
		if ($signupermsgdb) {
			foreach ($signupermsgdb as $key => $value) {
				M::sendNotice(
					array($value['toUser']),
					array(
						'title' => getLangInfo('writemsg', $value['subject'], $value['other']),
						'content' => getLangInfo('writemsg', $value['content'], $value['other'])
					),'notice_active', 'notice_active'
				);
			}
		}
	}

	/**
	 * ���ݽ���
	 * @param int $tid ����id
	 * @param int $actmid ���������id
	 * @return ''
	 */
	function pushActivityToAppCenter ($tid, $actmid) {
		global $db_siteid,$db_siteownerid,$db_sitehash,$db_bbsurl,$db_bbsname,$db_charset;
		$defaultValueTableName = getActivityValueTableNameByActmid();
		$this->db->update("UPDATE $defaultValueTableName SET pushtime=".S::sqlEscape($this->timestamp)." WHERE tid=".S::sqlEscape($tid));

		$i = $payMemberNums = $orderMemberNums = $payMemberCosts = $orderMemberCosts = $payRefundCouts = 0;
		$query = $this->db->query("SELECT am.tid,am.fupid,am.isrefund,am.ifpay,am.totalcash,am.signupnum,t.subject,t.authorid,t.author,t.postdate FROM pw_activitymembers am LEFT JOIN pw_threads t ON am.tid=t.tid WHERE am.tid=".S::sqlEscape($tid));
		while ($rt = $this->db->fetch_array($query)) {
			if ($rt['ifpay'] != 3 && $rt['fupid'] == 0) {//���ùرյĲ���
				$orderMemberNums += $rt['signupnum'];//�ѱ�������
			}
			if ($rt['ifpay'] != 3 && $rt['isrefund'] == 0) {//���ùرյĲ���
				$orderMemberCosts += $rt['totalcash'];//�漰����
			}
			if ($rt['ifpay'] != 0 && $rt['ifpay'] != 3 && $rt['fupid'] == 0) {//�Լ�֧��1��ȷ��֧��2����������4
				$payMemberNums += $rt['signupnum'];//�Ѿ����������
			}
			if ($rt['ifpay'] != 0 && $rt['ifpay'] != 3 && $rt['isrefund'] == 0) {//�Լ�֧��1��ȷ��֧��2����������4
				$payMemberTempCosts += $rt['totalcash'];//��֧������
			}
			if ($rt['isrefund'] == 1) {
				$payRefundCouts += $rt['totalcash'];//�˿����
			}
			if ($i == 0) {
				$tid		= $rt['tid'];
				$subject	= $rt['subject'];
				$authorid	= $rt['authorid'];
				$author		= $rt['author'];
				$postdate	= $rt['postdate'];
			}
		}

		if ($orderMemberNums) {//���˱����Ÿ���
			$author  = pwConvert($author,'gbk',$db_charset);
			$subject = pwConvert($subject,'gbk',$db_charset);
			$acttype = pwConvert($this->activitymodeldb[$actmid]['name'],'gbk',$db_charset);
			$db_bbsname = pwConvert($db_bbsname,'gbk',$db_charset);

			$partner = md5($db_siteid.$db_siteownerid);
			$payMemberCosts = $payMemberTempCosts - $payRefundCouts;//��֧������
			$para = array(
				'tid'				=> $tid,//�id
				'subject'			=> $subject,//�����
				'authorid'			=> $authorid,//�������id
				'author'			=> $author,//�������
				'postdate'			=> $postdate,//�����ʱ��
				'acttype'			=> $acttype,//���������
				'ordermembernums'	=> $orderMemberNums,//�ѱ�������
				'ordermembercosts'	=> $orderMemberCosts,//���������漰����
				'paymembernums'		=> $payMemberNums,//��֧������
				'paymembercosts'	=> $payMemberCosts,//��֧������
				'sitehash'			=> $db_sitehash,
				'bbsurl'			=> $db_bbsurl,
				'bbsname'			=> $db_bbsname,
			);
			
			ksort($para);
			reset($para);

			$arg = '';
			foreach ($para as $key => $value) {
				$arg .= "$key=$value&";
				$url .= "$key=".urlencode($value)."&";
			}
			$sign = md5(substr($arg,0,-1).$partner);
			$url .= 'sign='.$sign;
			require_once(R_P.'require/posthost.php');
			PostHost("http://stats.phpwind.com/api.php?m=app&job=alipayaa",$url,"POST");
		}
	}
}
?>

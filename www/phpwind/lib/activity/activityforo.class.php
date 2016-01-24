<?php
!defined('P_W') && exit('Forbidden');
L::loadClass('PostActivity', 'activity', false);

class PW_ActivityForO extends PW_PostActivity {
	/**
	 * ��ȡ��������
	 * @param string $see
	 * @return string
	 */
	function PW_ActivityForO() {
		$this->initGlobalValue();
	}
	function getSeeTitleBySee($see) {
		if ('fromgroup' == $see) {
			$seeTitle = '����Ⱥ��';
		} elseif ('feeslog' == $see) {
			$seeTitle = '������ͨ��־';
		} else {
			$seeTitle = '���԰��';
		}
		return $seeTitle;
	}
	/**
	 * ��������ʱ���Ԥ��ֵ
	 * @return multitype:multitype:string  
	 */
	function getTimeOptions() {
		return array (
			'+86400' => 'һ����',
			'+259200' => '������',
			'+604800' => 'һ����',
			'+2592000' => 'һ����',
		);
	}
	/**
	 * ����ʱ��select��HTML
	 * @param string $selected ѡ�е�option��value
	 * @param bool $withEmptySelection �Ƿ����'ʱ�䲻��'��option
	 * @param string $selectTagName select��name��ֵ�����ޣ����ص�HTML������select���Tag(ֻ��option Tag)
	 * @return HTML
	 */
	function getTimeSelectHtml($selected, $withEmptySelection=1, $selectTagName = 'time') {
		$options = array();
		if ($withEmptySelection) {
			$options[''] = 'ʱ�䲻��';
		}
		$options += $this->getTimeOptions();
		return getSelectHtml($options, $selected, $selectTagName);
	}
	/**
	 * ���ػ״̬
	 * @param int $status ״̬
	 * @return string|array ��$status��ֵ������string״̬�����򣬷���array����״̬
	 */
	function getFeesLogStatus($status = '') {
		$Array = array(0 => '���л', 1 => '������', 2 => '�ѽ���', 3 => '��ȡ��', 4 => '��ɾ��');
		if ($status !== '') {
			if (array_key_exists($status, $Array)) {
				return $Array[$status];
			} else {
				return '';
			}
		} else {
			return $Array;
		}
	}
	/**
	 * ���ػ��������
	 * @param int $type ����
	 * @return string|array ��$type��ֵ������string���ͣ����򣬷���array��������
	 */
	function getFeesLogCostType($type = '') {
		//$Array = array(1 => '֧������', 2 => '׷��', 3 => '�˿�', 4 => 'ȷ��֧��');
		$Array = array(1 => '', 2 => '׷��', 3 => '�˿�', 4 => '');
		if ($type !== '') {
			if (array_key_exists($type, $Array)) {
				return $Array[$type];
			} else {
				return '';
			}
		} else {
			return $Array;
		}
	}
	/**
	 * ���������֧������
	 * @param string $type ����
	 * @return string|array ��$type��ֵ������string���ͣ����򣬷���array��������
	 */
	function getExpenseOrIncomeName($type = '') {
		$Array = array('expense' => '<span class="s3">֧��</span>', 'income' => '<span class="s2">����</s3>');
		if ($type !== '') {
			if (array_key_exists($type, $Array)) {
				return $Array[$type];
			} else {
				return 'δ֪';
			}
		} else {
			return $Array;
		}
	}
	function getSignupHtml($data) {
		$tid = $data['tid'];
		$activityStatusKey = $this->getActivityStatusKey($data, $this->timestamp, $this->peopleAlreadySignup($tid));
		$replaceArray = array();
		if ('activity_is_ended' == $activityStatusKey) {
			/*֧���ɹ�������ͨ��־*/
			$this->UpdatePayLog($tid,0,2);
		} elseif ('activity_is_cancelled' == $activityStatusKey) {
			$replaceArray = array($data['minparticipant']);
		} elseif ('signup_is_available' == $activityStatusKey) {
			$replaceArray = array($tid, $data['authorid'], $this->actmid);
			if ($this->getOrderMemberUid($tid) == $this->winduid && $this->winduid) {
				$activityStatusKey = 'additional_signup_is_available_for_member';
			} elseif ($this->winduid) {
				$activityStatusKey = 'signup_is_available_for_member';
			} else {
				$activityStatusKey = 'signup_is_available_for_guest';
			}
		}
		$signupHtml = '<p class="t3">';
		$signupHtml .= $this->getSignupHtmlByActivityKey($activityStatusKey, $replaceArray);
		$signupHtml .= '</p>';
		return $signupHtml;
	}

	function getGroupSignupHtml($data) {//Ⱥ����״̬��ȡ
		global $winduid;
		$replaceArray = $data;
		require_once(A_P . 'groups/lib/active.class.php');
		$newActive = new PW_Active();
		if ($this->timestamp > $data['endtime']) {
			$activityStatusKey = 'activity_is_ended';
		} elseif ($this->timestamp > $data['deadline'] && $this->timestamp > $data['begintime']) {
			$activityStatusKey = 'activity_group_running';
		} elseif ($this->timestamp > $data['deadline']) {
			$activityStatusKey = 'signup_is_ended';
		} elseif ($data['limitnum'] && $data['members'] >= $data['limitnum']) {
			$activityStatusKey = 'signup_number_limit_is_reached';
		} elseif ($newActive->isJoin($data['id'], $winduid) && $winduid != $data['uid']) {
			$activityStatusKey = 'activity_is_joined';
		} elseif ($winduid == $data['uid']) {
			$activityStatusKey = 'activity_group_edit';
		} else {
			$activityStatusKey = 'group_is_available_for_member';
		}
		$signupHtml = '<p class="t3">';
		$signupHtml .= $this->getSignupHtmlByActivityKey($activityStatusKey, $replaceArray);
		$signupHtml .= '</p>';
		return $signupHtml;
	}
	
	function getSignupHtmlByActivityKey ($key, $replaceArray = NULL) {
		switch ($key) {
			case 'signup_not_started_yet': //δ��ʼ����
				$html = '<span class="bt"><span><button type="button" disabled>����δ��ʼ</button></span></span>';
				break;
			case 'activity_is_cancelled': //����������δ�ﵽ����������ƣ��ȡ��
				$html = '<span class="bt"><span><button type="button" disabled>���ȡ��</button></span></span>';
				break;
			case 'activity_is_running':
			case 'signup_is_ended': //��������±�������
				$html = '<span class="bt"><span><button type="button" disabled>�����ѽ���</button></span></span>';
				break;
			case 'activity_is_ended': //�����
				$html = '<span class="bt"><span><button type="button" disabled>��ѽ���</button></span></span>';
				break;
			case 'signup_number_limit_is_reached': //������������
				$html = '<span class="bt"><span><button type="button" disabled>������������</button></span></span>';
				break;
			case 'signup_is_available':
			case 'signup_is_available_for_guest': //δ��¼״̬������ʾ
				$text = '��Ҫ����';
			case 'signup_is_available_for_member': //��¼״̬����ǰδ������������ʾ
				$text || $text = '��Ҫ����';
			case 'additional_signup_is_available_for_member': //��¼״̬����ǰ�ѱ�����������ʾ
				$text || $text = '��Ҫ����';
				$html = "<span class=\"btn\"><span><button type=\"button\" onclick=\"window.location='read.php?tid=$replaceArray[0]'\">$text</button></span></span>";
				break;
			case 'group_is_available_for_member' :
				$text || $text = '��Ҫ����';
				$html = "<span class=\"btn\"><span><button type=\"button\" onclick=\"window.location='apps.php?q=group&a=active&job=view&cyid=".$replaceArray['cid']."&id=".$replaceArray['id']."'\">$text</button></span></span>";
				break;
			case 'activity_group_running'://Ⱥ��������״̬
				$html = '<span class=\"bt\"><span><button type=\"button\>�������</button></span></span>';
				break;
			case 'activity_is_joined':
				$html = "<span class=\"bt\"><span><button type=\"button\" id=\"active_quit\" onclick=\"sendmsg('apps.php?q=group&a=active&job=quit&cyid=".$replaceArray['cid']."&id=".$replaceArray['id']."', '', this.id);\">�˳��</button></span></span>";
				break;
			case 'activity_group_edit':
				$html = "<span class=\"btn\"><span><button type=\"button\" onclick=\"window.location='apps.php?q=group&a=active&job=edit&cyid=".$replaceArray['cid']."&id=".$replaceArray['id']."'\">�༭�</button></span></span>";
				break;
			default:
				$html = '';
		}
		return $html;
	}
	/**
	 * ��ȡ�û���������л
	 * @param int $uid �û�ID
	 * @return array �ID
	 */
	function getAllParticipatedActivityIdsByUid ($uid) {
		$activityIds = array();
		$query = $this->db->query("SELECT DISTINCT tid FROM pw_activitymembers WHERE uid = ". S::sqlEscape($uid));
		while ($rt = $this->db->fetch_array($query)) {
			$activityIds[] = $rt['tid'];
		}
		return $activityIds;
	}
	
	function getActivityNumberAndLastestTimestampByUid ($uid) {
		$allActivityIdsIHaveParticipated = $this->getAllParticipatedActivityIdsByUid($uid);
		$fids = trim(getSpecialFid() . ",'0'",',');
		!empty($fids) && $where .= ($where ? ' AND ' : '')."dv.fid NOT IN($fids)";
		$where .= ($where ? ' AND ' : ''). "(tr.authorid = ".S::sqlEscape($uid).($allActivityIdsIHaveParticipated ? ' OR tr.tid IN ('.S::sqlImplode($allActivityIdsIHaveParticipated).')' : '').')';
		$where && $where = " WHERE ".$where;
		$activitynum = $this->db->get_value("SELECT COUNT(*) FROM pw_threads tr LEFT JOIN pw_activitydefaultvalue dv USING (tid) $where");
		$activity_lastpost = $this->db->get_value("SELECT postdate FROM pw_threads tr LEFT JOIN pw_activitydefaultvalue dv USING (tid) $where ORDER BY postdate DESC LIMIT 1");
		return array ($activitynum, $activity_lastpost);
	}
	
	/**
	 * ��ȡ����֧��˵��
	 * @param int $ifpay  0=>δ֧�� 1=>��֧�� 2=>ȷ��֧�� 3=>���׹ر� 4=>�˿���� 
	 * @param int $fromuid ����֧�����û�ID
	 * @param string $fromusername ����֧�����û���
	 * @return string ˵���ַ���
	 */
	function getPayLogDescription(&$rt){
		global $winduid;
		$u = $winduid;
		if ($rt['ifpay'] == 1){
			if ($rt['issubstitute']){
				if ($rt['fromuid'] == $u){
					$rt['otherParty'] = $rt['author'];
					$rt['expenseOrIncome'] = 'expense';	
					$rt['payDescription'] = '�Ұ�<a href="'.USER_URL. $rt['uid'] . '" target="_blank">' .  $rt['username'] . '</a>֧��';
				}elseif($rt['uid'] == $u){
					$rt['otherParty'] = $rt['author'];
					$rt['expenseOrIncome'] = 'expense';	
					$rt['payDescription'] = '<a href="'.USER_URL. $rt['fromuid'] . '" target="_blank">' .  $rt['fromusername'] . '</a>����֧��';
				}elseif ($rt['authorid'] == $u){
					$rt['otherParty'] = $rt['username'];
					$rt['expenseOrIncome'] = 'income';	
					$rt['payDescription'] = '<a href="'.USER_URL. $rt['fromuid'] . '" target="_blank">' .  $rt['fromusername'] . '</a>��<a href="' .USER_URL . $rt['uid'] . '" target="_blank">' .  $rt['username'] . '</a>֧��';
				}
			}else{
				$rt['otherParty'] = $rt['uid'] == $u ? $rt['author'] : $rt['username'];
				$rt['expenseOrIncome'] = $rt['uid'] == $u ? 'expense' : 'income';
				$rt['payDescription'] = '֧����֧��';
			}
		} elseif ($rt['ifpay'] == 4){
				$rt['otherParty'] = $rt['uid'] == $u ? $rt['author'] : $rt['username'];
				$rt['expenseOrIncome'] = $rt['uid'] == $u ? 'expense' : 'income';
				$rt['payDescription'] = '֧����֧��';
		} else{
			$rt['otherParty'] = $rt['uid'] == $u ? $rt['author'] : $rt['username'];
			if ($rt['costtype'] == 3){
				$rt['expenseOrIncome'] = $rt['uid'] != $u ? 'expense' : 'income';
				$rt['payDescription'] = '֧����֧��';
			}else{
				$rt['expenseOrIncome'] = $rt['uid'] == $u ? 'expense' : 'income';
				$rt['payDescription'] = '������ʽ֧��';
			}
		}
		$rt['expenseOrIncome'] = $this->getExpenseOrIncomeName($rt['expenseOrIncome']);
	}
}
<?php
!defined('P_W') && exit('Forbidden');

/**
 * Ⱥ����
 * @author chenjm / sky_hold@163.com
 * @package colony
 */

class PwColony {
	
	var $_db;

	var $cyid;
	var $info  = array(); //Ⱥ��Ϣ
	var $right = array(); //Ⱥ��Ȩ��
	var $useDefault = false;//ʹ�ó����趨��ʼֵ

	function PwColony($cyid) {
		global $db;
		$this->_db =& $db;
		$this->cyid = $cyid;
		$this->_init();
	}

	/**
	 * ��ʼ��Ⱥ��Ϣ
	 * return array
	 */
	function _init() {
		global $winduid;
		$_sql_sel = $_sql_tab = '';
		if ($winduid) {
			$_sql_sel = ',cm.id AS ifcyer,cm.ifadmin,cm.lastvisit';
			$_sql_tab = ' LEFT JOIN pw_cmembers cm ON c.id=cm.colonyid AND cm.uid=' . S::sqlEscape($winduid);
		}
		$this->info = $this->_db->get_one("SELECT c.*{$_sql_sel} FROM pw_colonys c{$_sql_tab} WHERE c.id=" . S::sqlEscape($this->cyid));
		if ($this->info) {
			$this->info['ifFullMember'] = ($this->info['ifcyer'] && $this->info['ifadmin'] != -1) ? 1 : 0;
			list($this->info['cnimg'], $this->info['imgtype']) = PwColony::getColonyCnimg($this->info['cnimg']);
			$this->info['createtime_s'] = get_date($this->info['createtime'], 'Y-m-d');
			!$this->info['colonystyle'] && $this->info['colonystyle'] = 'skin_default';
			$this->info['descrip'] = str_replace('&#61;', '=', $this->info['descrip']);
			$this->_setRight();
		}
	}

	function &getInfo() {
		return $this->info;
	}
	
	/**
	 * �Ƿ���Ⱥ����Ȩ��
	 * return bool
	 */
	function getIfadmin() {
		global $windid,$SYSTEM,$manager,$groupid;
		/*�������Ȩ��*/
		$rForumAdmin = false;
		if($SYSTEM['forumcolonyright'] && $this->info['classid'] > 0){
			if($groupid == 5){
				L::loadClass('forum', 'forum', false);
				$rForum = new PwForum($this->info['classid']);
				$rForumAdmin = $rForum->isBM($windid);
			}else{
				$rForumAdmin = true;
			}
		}
		/* end */
		return ($rForumAdmin || $this->info['ifadmin'] == '1' || $this->info['admin'] == $windid || S::inArray($windid,$manager) || $SYSTEM['colonyright']);
	}
	
	/**
	 * ��ȡȺ��logo
	 * static function
	 * @param string $cnimg ͼƬ�洢��ַ
	 * return array(logo��ַ, ͼƬ�洢��ʽ)
	 */
	function getColonyCnimg($cnimg) {
		$imgtype = 'http';
		if (!strstr($cnimg, 'http://')) {
			if ($cnimg) {
				list($cnimg, $imgtype) = geturl("cn_img/$cnimg", 'lf');
			} else {
				$cnimg = $GLOBALS['imgpath'] . '/g/groupnopic.gif';
				$imgtype = 'none';
			}
		}
		return array($cnimg, $imgtype);
	}
	
	/**
	 * ����Ⱥ��bannerͼƬ·��
	 */
	function initBanner() {
		if ($this->info['banner']) {
			list($this->info['banner']) = geturl("cn_img/{$this->info[banner]}",'lf');
		}
	}

	/**
	 * ��ȡ����ʽ��Ⱥ������
	 * return string
	 */
	function getNameStyle() {
		return $this->styleFormat($this->info['cname'], $this->info['titlefont']);
	}

	/**
	 * ��ʼ��Ⱥ��ȼ�Ȩ��
	 */
	function _setRight() {
		$level = $this->info['speciallevel'] ? $this->info['speciallevel'] : $this->info['commonlevel'];
		empty($level) && $level = 1;
		$this->right = $this->_db->get_one("SELECT * FROM pw_cnlevel WHERE id=" . S::sqlEscape($level));
		if (empty($this->right)) {
			//$this->right = $this->_db->get_one("SELECT * FROM pw_cnlevel WHERE ltype='common' ORDER BY lpoint ASC LIMIT 1");
			$this->right = array(
				  'ltype' => 'common',
				  'ltitle' => '��ʼȺ��',
				  'lpoint' => 0,
				  'albumnum' => 10,
				  'maxphotonum' => 60,
				  'maxmember' => 100,
				  'bbsmode' => 0,
				  'allowmerge' => 1,
				  'allowattorn' => 1,
				  'allowdisband' => 0,
				  'pictopic' => 0,
				  'allowstyle' => 1,
				  'topicadmin' => array(),
				  'modeset'	=>
				  array(
				    'thread' =>
				    array(
				      'vieworder'=> 0,
				      'title' => '����'
				    ),
				    'active' =>
				    array(
				      'vieworder' => 0,
				      'title' => '�'
				    ),
				    'write' =>
				    array(
				      'ifopen'=>1,
				      'vieworder'=>1,
				      'title'=>'������'
				    ),
				    'galbum' =>
				    array(
				      'ifopen' =>1,
				      'vieworder'=>2,
				      'title'=>'���'
				    ),
				    'member'=>
				    array(
				      'ifopen'=>1,
				      'vieworder'=>3,
				      'title'=>'��Ա'
				    )
				  ),
				  'layout' =>
				  array(
				    'thread'=>
				    array('vieworder' => 0,
				      'num'=>5,
				    ),
				    'active'=>
				    array(
				      'vieworder'=>0,
				      'num'=>4
				    ),
				    'write'=>
				    array(
				      'ifopen'=>1,
				      'vieworder'=>1,
				      'num'=>5
				    ),
				    'galbum'=>
				    array(
				      'ifopen'=>1,
				      'vieworder'=>2,
				      'num'=>10
				    )
				  )
				);
				$this->useDefault = true;
		}
		if ($this->right && !$this->useDefault) {
			$this->right['modeset'] = $this->right['modeset'] ? unserialize($this->right['modeset']) : array();
			$this->right['layout'] = $this->right['layout'] ? unserialize($this->right['layout']) : array();
			$this->right['topicadmin'] = $this->right['topicadmin'] ? unserialize($this->right['topicadmin']) : array();
		}
	}

	/**
	 * ��ȡȨ��
	 * return array
	 */
	function &getRight() {
		return $this->right;
	}
	
	/**
	 * ��ȡ������
	 * @param string $searchadd ��������
	 * return int
	 */
	function getArgumentCount($searchadd = '') {
		$count = $this->_db->get_value('SELECT COUNT(*) AS count FROM pw_argument a LEFT JOIN pw_threads t ON a.tid=t.tid WHERE a.cyid=' . S::sqlEscape($this->cyid) . $this->_getThreadWhere() . $searchadd);
		return (int)$count;
	}
	
	/**
	 * ��ȡ�����б�
	 * @param string $searchadd ��������
	 * @param int $start ������¼��ʼ
	 * @param int $limit ��ȡ��¼����
	 * @param string $orderway �����ֶ�
	 * @param string $asc ����ʽ
	 * return array
	 */
	function getArgument($searchadd, $start, $limit, $orderway = 'lastpost', $asc = 'DESC') {
		$array = array();
		$query = $this->_db->query("SELECT t.*,a.topped,a.lastpost,a.titlefont,a.digest,a.toolfield FROM pw_argument a LEFT JOIN pw_threads t ON a.tid=t.tid WHERE a.cyid=" . S::sqlEscape($this->cyid) . $this->_getThreadWhere() . $searchadd . ' ORDER BY ' . ($orderway == 'lastpost' ? "a.topped $asc,a.lastpost" : "t.{$orderway}") . " $asc" . S::sqlLimit($start, $limit));
		while ($rt = $this->_db->fetch_array($query)) {
			$array[] = $rt;
		}
		return $array;
	}

	function _getThreadWhere() {
		$ifcheck = ($this->info['classid'] && $this->info['iftopicshowinforum']) ? 1 : 2;
		return ' AND t.fid=' . S::sqlEscape($this->info['classid']) . ' AND t.ifcheck=' . S::sqlEscape($ifcheck);
	}
	
	/**
	 * static function
	 * ����Ⱥ��Ļ���
	 * @param array $info Ⱥ����Ϣ
	 * return int
	 */
	function calculateCredit($info) {
		require_once(R_P . 'require/functions.php');
		$info['pnum'] -= $info['tnum'];
		return CalculateCredit($info, L::config('o_groups_upgrade','o_config'));
	}

	function _checkJoinCredit($uid) {
		global $credit;
		require_once(R_P.'require/credit.php');
		$o_groups_creditset = L::config('o_groups_creditset','o_config');
		if (empty($o_groups_creditset['Joingroup'])) {
			return true;
		}
		if(!is_array($o_groups_creditset['Joingroup'])) return true;
		foreach ($o_groups_creditset['Joingroup'] as $key => $value) {
			if ($value > 0 && $value > $credit->get($uid, $key)) {
				$GLOBALS['moneyname'] = $credit->cType[$key];
				$GLOBALS['o_joinmoney'] = $value;
				return 'colony_joinfail';
			}
		}
		return true;
	}
	
	function _checkJoinNum($uid) {
		global $_G;
		if ($_G['allowjoin'] > 0 && $_G['allowjoin'] <= $this->_db->get_value("SELECT COUNT(*) as sum FROM pw_cmembers WHERE uid=" . S::sqlEscape($uid))) {
			return 'colony_joinlimit';
		}
		return true;
	}

	function checkJoinStatus($uid) {
		if ($this->info['ifFullMember']) {
			return 'colony_alreadyjoin';
		}
		if ($this->info['ifcyer'] && $this->info['ifadmin'] == '-1') {
			return 'colony_joinsuccess_check2';
		}
		if (!$this->info['ifcheck']) {
			return 'colony_joinrefuse';
		}
		if ($this->right['maxmember'] > 0 && $this->info['members'] >= $this->right['maxmember']) {
			return 'colony_memberlimit';
		}
		if (($return = $this->_checkJoinNum($uid)) !== true) {
			return $return;
		}
		if (($return = $this->_checkJoinCredit($uid)) !== true) {
			return $return;
		}
		return true;
	}

	/**
	 * ����Ⱥ��
	 * @param int $uid �û�id
	 * @param string $username �û���
	 * return string 
		colony_joinsuccess			����ɹ�
		colony_joinsuccess_check	����ɹ�����Ҫ��֤
		colony_alreadyjoin			����ʧ�ܣ��Ѽ���
		colony_joinsuccess_check2	����ʧ�ܣ��Ѽ��룬δ��֤
		colony_joinrefuse			����ʧ�ܣ��ܾ�����
		colony_memberlimit			����ʧ�ܣ�Ⱥ��Ա�ﵽ����
		colony_joinlimit			����ʧ�ܣ��û������Ⱥ�ﵽ����
		colony_joinfail				����ʧ�ܣ��û����ֲ���
	 */
	function join($uid, $username) {
		if (($return = $this->checkJoinStatus($uid)) !== true) {
			return $return;
		}
		return $this->addColonyUser($uid, $username);
	}

	function addColonyUser($uid, $username, $frombbs=false) {
		global $credit;
		require_once(R_P.'require/credit.php');

		//���ֱ䶯
		$o_groups_creditset = L::config('o_groups_creditset','o_config');
		if (!empty($o_groups_creditset['Joingroup'])) {
			require_once(R_P . 'u/require/core.php');
			$credit->appendLogSet(L::config('o_groups_creditlog', 'o_config'), 'groups');
			$creditset = getCreditset($o_groups_creditset['Joingroup'], false);
			$credit->addLog('groups_Joingroup', $creditset, array(
				'uid'		=> $uid,
				'username'	=> $username,
				'ip'		=> $GLOBALS['onlineip']
			));
			$credit->sets($uid, $creditset);
		}
		/**
		$this->_db->update("INSERT INTO pw_cmembers SET " . S::sqlSingle(array(
			'uid'		=> $uid,
			'username'	=> $username,
			'ifadmin'	=> $this->info['ifcheck'] == 2 ? '0' : '-1',
			'colonyid'	=> $this->cyid,
			'addtime'	=> $GLOBALS['timestamp']
		)));
		**/
		pwQuery::insert('pw_cmembers', array(
			'uid'		=> $uid,
			'username'	=> $username,
			'ifadmin'	=> $this->info['ifcheck'] == 2 ? '0' : '-1',
			'colonyid'	=> $this->cyid,
			'addtime'	=> $GLOBALS['timestamp']
		));

		if ($this->info['ifcheck'] == 2) {
			$this->updateInfoCount(array('members' => 1));	
		}
		$this->_sendJoinMsg($frombbs);

		if ($this->info['ifcheck'] == 2) {
			updateUserAppNum($uid, 'group');
			return 'colony_joinsuccess';
		}
		return 'colony_joinsuccess_check';
	}

	function updateInfoCount($info) {
		if (empty($info) || !is_array($info)) {
			return false;
		}
		$_sql_set = $extra = '';
		foreach ($info as $key => $value) {
			if (in_array($key, array('tnum', 'pnum', 'members', 'albumnum', 'photonum', 'writenum', 'activitynum'))) {
				$_sql_set .= $extra . $key . '=' . $key . '+' . intval($value);
				$this->info[$key] += intval($value);
				$extra = ',';
			}
		}
		if (empty($_sql_set))
			return false;

		//* $this->_db->update('UPDATE pw_colonys SET ' . $_sql_set . ' WHERE id=' . S::sqlEscape($this->cyid));
		$this->_db->update(pwQuery::buildClause("UPDATE :pw_table SET {$_sql_set} WHERE id=:id", array('pw_colonys',$this->cyid)));
		require_once(R_P . 'u/require/core.php');
		updateGroupLevel($this->cyid, $this->info);
	}

	function _sendJoinMsg($frombbs) {
		global $winduid,$windid;
		$lang = array(
			'cname' => S::escapeChar($this->info['cname']),
			'colonyurl'
		);
		$group = $this->info['ifcheck'] == 1 ? '3' : '2';
		if ($frombbs) {
			$colonyurl = 'thread.php?cyid='.$this->cyid.'&showtype=member&group='.$group;
		} else {
			$colonyurl = 'apps.php?q=group&a=member&cyid='.$this->cyid.'&group='.$group;
		}
		$managers = $this->getUserNames($this->getManager());
		if ($this->info['ifcheck'] == 1) {
			M::sendRequest(
				$windid,
				$managers,
				array(
					'create_uid' => $winduid,
					'create_username' => $windid,
					'title' => getLangInfo('writemsg', 'colony_join_title_check', array(
						'cname'	=> S::escapeChar($this->info['cname'])
					)),
					'content' => getLangInfo('writemsg','colony_join_content_check',array(
						'uid' => $winduid,
						'username' => $windid,
						'cname'	=> S::escapeChar($this->info['cname']),
						'colonyurl' => $colonyurl
					)),
					'extra' => serialize(array('cyid' => $this->cyid,'check'=>1))
				),
				'request_group',
				'request_group'
			);
		} else {
			M::sendNotice($managers, array(
				'title' => getLangInfo('writemsg', 'colony_join_title',array(
					'cname'	=> S::escapeChar($this->info['cname'])
				)),
				'content' => getLangInfo('writemsg', 'colony_join_content',array(
					'cname'	=> S::escapeChar($this->info['cname']),
					'colonyurl' => $colonyurl
				))
			));
		}
	}
	
	/**
	 * ��ȡ����ÿ�
	 * return array
	 */
	function getVisitor($nums = null, $start = 0) {
		if (!is_array($this->info['visitor'])) {
			$this->info['visitor'] = $this->info['visitor'] ? (array)unserialize($this->info['visitor']) : array();
		}
		return $nums ? PwArraySlice($this->info['visitor'], $start, $nums, true) : $this->info['visitor'];
	}

	function getVisitorNum() {
		if (!is_array($this->info['visitor'])) {
			$num = count((array)unserialize($this->info['visitor']));
		} else {
			$num = count($this->info['visitor']);
		}
		return $num;
	}

	function appendVisitor($uid) {
		global $timestamp;
		$this->getVisitor();
		if ($uid && (!isset($this->info['visitor'][$uid]) || $timestamp - $this->info['visitor'][$uid] > 3600)) {
			$this->info['visitor'][$uid] = $timestamp;
			arsort($this->info['visitor']);
			while (count($this->info['visitor']) > 50) {
				array_pop($this->info['visitor']);
			}
			//* $this->_db->update("UPDATE pw_colonys SET visitor=" . S::sqlEscape(serialize($this->info['visitor'])) . ' WHERE id=' . S::sqlEscape($this->cyid));
			$this->_db->update(pwQuery::buildClause("UPDATE :pw_table SET visitor=:visitor WHERE id=:id", array('pw_colonys',serialize($this->info['visitor']), $this->cyid)));
		}
	}

	function styleFormat($str, $titlefont) {
		if ($titlefont) {
			$titledetail = explode("~", $titlefont);
			if ($titledetail[0]) $str = "<font color=\"$titledetail[0]\">$str</font>";
			if ($titledetail[1]) $str = "<b>$str</b>";
			if ($titledetail[2]) $str = "<i>$str</i>";
			if ($titledetail[3]) $str = "<u>$str</u>";
		}
		return $str;
	}

	function _sqlIn($ids) {
		return (is_array($ids) && $ids) ? ' IN (' . S::sqlImplode($ids) . ')' : '=' . S::sqlEscape($ids);
	}

	function _sqlMs($v) {
		$_sql_v = '';
		switch ($v) {
			case -1://δ��֤
				$_sql_v .= '=-1';break;
			case 0://��ͨ��Ա
			case 1://����Ա
				$_sql_v .= '=' . S::sqlEscape($value);break;
			case 2://�ǹ���Ա
				$_sql_v .= "!='1'";break;
			case 3://��֤��Ա
				$_sql_v .= ">=0";break;
			default:
				break;
		}
		return $_sql_v;
	}
	
	/**
	 * sql���������װ
	 * @param array $where ��������
	 * return string where����
	 */
	function _sqlCompound($where) {
		if (!$where || !is_array($where)) {
			return '';
		}
		$_sql_where = '';
		foreach ($where as $_sql_field => $value) {
			switch ($_sql_field) {
				case 'id':
				case 'uid':
					$_sql_where .= " AND $_sql_field" . $this->_sqlIn($value);break;
				case 'ifadmin':
					$_sql_where .= " AND $_sql_field" . $this->_sqlMs($value);break;
				case 'admin':
					$_sql_where .= " AND $_sql_field=" . S::sqlEscape($value);break;
			}
		}
		return $_sql_where;
	}

	/**
	 * ��ȡ�����ӵĹ���Ȩ��
	 * @author zhudong
	 * @param string $action ��������
	 * @param int $seltid �Ե������ӽ��в�����ʱ������Ե�����ID
	 * @return int $ifadmin ���նԸ����Ͳ���Ȩ��
	 */
	function checkTopicAdmin($action,$seltid) {
		global $manager,$SYSTEM,$windid;
		if (S::inArray($windid,$manager) || $SYSTEM['colonyright']) {
			$ifadmin = 1;
		} elseif ($this->info['ifadmin'] == '1' || $this->info['admin'] == $windid) {
			if ($action == 'type') {//Ⱥ�����Ա�������������Ȩ��
				$ifadmin = 1;
			} else {
				$ifadmin = $this->right['topicadmin'][$action];
			}
		}
		return $ifadmin;
	}

	/**
	 * ���action�Ƿ���ָ���ķ�Χ��
	 * @author zhudong
	 * @param string $action ��������
	 * @return null
	 */

	function checkAction($action) {
		$actionAarry = array('del','highlight','lock','pushtopic','downtopic','toptopic','digest','type');
		if (!in_array($action,$actionAarry)) {
			Showmsg('undefined_action');
		}
	}

	/**
	 * ��鱻��������ӵĺϷ��ԣ��Ƿ��Ǳ�Ⱥ�����ӣ��Ƿ�ԽȨ������
	 * @author zhudong
	 * @param array $tidarray �����������ӵ�tid����
	 * @return array
	 */

	function checkTopic($tidarray) {
		global $groupid;
		count($tidarray) > 500 && Showmsg('mawhole_count');
		foreach ($tidarray as $k => $v) {
			is_numeric($v) && $tids[] = $v;
		}
		if ($tids) {
			$tids = S::sqlImplode($tids);
			$query = $this->_db->query("SELECT a.*,t.* FROM pw_argument a LEFT JOIN pw_threads t ON a.tid=t.tid WHERE a.tid IN($tids)");
			while ($rt = $this->_db->fetch_array($query)) {
				if ($rt['cyid'] != $this->info['id']) {
					Showmsg('colony_topicadmin_other_colony');
				}
				//����Խ������
				if ($groupid != 3 && $groupid != 4) {
					$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
					$authordb = $userService->get($rt['authorid']);//groupid
					/**Begin modify by liaohu*/
					$pce_arr = explode(",",$GLOBALS['SYSTEM']['tcanedit']);
					if (($authordb['groupid'] == 3 || $authordb['groupid'] == 4 || $authordb['groupid'] == 5) && !in_array($authordb['groupid'],$pce_arr)) {
						Showmsg('modify_admin');
					}
					/**End modify by liaohu*/
				}
				$rt['date'] = get_date($rt['postdate']);
				$threaddb[$rt['tid']] = $rt;
			}
		} else {
			$threaddb = array();
		}
		return $threaddb;
	}
	
	/**
	 * ��ȡȺ����Ա�б�
	 * return array
	 */
	function getManager($num=0) {
		$array = array();
		$limit = $num ? 'LIMIT '.(int)$num : '';
		$query = $this->_db->query("SELECT uid,username FROM pw_cmembers WHERE colonyid=" . S::sqlEscape($this->cyid) . " AND ifadmin='1' ORDER BY addtime ASC $limit");
		while ($rt = $this->_db->fetch_array($query)) {
			$array[$rt['uid']] = $rt;
		}
		return $array;
	}
	
	/**
	 * ��ȡ�û�������
	 * return array
	 */
	function getUserNames($users) {
		$array = array();
		foreach ($users as $key => $value) {
			$array[] = $value['username'];
		}
		return $array;
	}
	
	/**
	 * ��ȡȺ��Ա�б�
	 * @param array $where ��������
	 * @param int nums ��ȡ��¼����
	 * @param int start 
	 * @param string $orderway �����ֶ�
	 * @param bool $count �Ƿ��ȡͳ����
	 * return array
	 */
	function getMembers($where, $nums = null, $start = 0, $orderway = '', $count = false) {
		$sql = $this->_sqlCompound($where);
		$limit = $order = '';
		if ($nums) {
			$limit = S::sqlLimit($start, $nums);
		}
		if ($orderway) {
			!in_array($orderway, array('lastvisit', 'lastpost', 'ifadmin')) && $orderway = 'ifadmin';
			$order = " ORDER BY $orderway DESC";
		}
		$array = array();
		$query = $this->_db->query("SELECT uid,username,lastvisit FROM pw_cmembers WHERE colonyid=" . S::sqlEscape($this->cyid) . $sql . $order . $limit);
		while ($rt = $this->_db->fetch_array($query)) {
			$array[$rt['uid']] = $rt;
		}
		return $array;
	}
	
	/**
	 * ��˻�Ա
	 * @param array $where ��������
	 * return array
	 */
	function checkMembers($uids) {
		if (!$this->getIfadmin() || empty($uids)) {
			return false;
		}
		!is_array($uids) && $uids = array($uids);
		$array = $this->getMembers(array('uid' => $uids, 'ifadmin' => -1));
		if ($array) {
			$ids = array_keys($array);
			require_once(R_P . 'u/require/core.php');
			//* $this->_db->update("UPDATE pw_cmembers SET ifadmin='0' WHERE colonyid=" . S::sqlEscape($this->cyid) . ' AND uid IN(' . S::sqlImplode($ids) . ") AND ifadmin='-1'");
			pwQuery::update('pw_cmembers', 'colonyid=:colonyid AND uid IN (:uid) AND ifadmin=:ifadmin', array($this->cyid, $ids, -1), array('ifadmin'=>0));
			updateUserAppNum($ids, 'group');
		}
		$newMemberCount = count($array);
		$this->updateInfoCount(array('members' => $newMemberCount));
		return $this->getUserNames($array);
	}
	
	/**
	 * ����Ⱥ��
	 * return array
	 */
	function getLikeGroup() {
		global $o_groups_upgrade;
		$array = array();
		$query = $this->_db->query("SELECT id,cname,cnimg,createtime,members,tnum,pnum,albumnum,photonum,writenum,activitynum FROM pw_colonys WHERE styleid=" . S::sqlEscape($this->info['styleid']) . " AND id!=" . S::sqlEscape($this->cyid) . " ORDER BY visit DESC LIMIT 4");
		while ($rt = $this->_db->fetch_array($query)) {
			list($rt['cnimg']) = PwColony::getColonyCnimg($rt['cnimg']);
			$rt['colonyNums'] = CalculateCredit($rt, $o_groups_upgrade);
			$rt['createtime'] = get_date($rt['createtime'], 'Y-m-d');
			$array[$rt['id']] = $rt;
		}
		return $array;
	}

	function getOwnDelRight($action,$authorid,$seltid) {
		global $winduid,$_G;
		if(empty($seltid)) {
			$right = 0;
		}

		if ($action == 'del' && $winduid == $authorid && $_G['allowdelatc']) {
			$right = 1;
		}
		return $right;
	}

	function getSetAble($t) {
		global $windid,$SYSTEM,$groupRight;
		$ifable = 0;
		$colony = $this->getInfo();
		switch ($t) {
			case 'style' :
				$ifable = $this->right['allowstyle'];
				break;
			case 'merge' :
				$ifable = ((1 == $SYSTEM['colonyright'] || $colony['admin'] == $windid) && $groupRight['allowmerge']) ? '1' : '0';
				break;
			case 'attorn' :
				$ifable = ((1 == $SYSTEM['colonyright'] || $colony['admin'] == $windid) && $groupRight['allowattorn']) ? '1' : '0';
				break;
			case 'disband' :
				$ifable = ((1 == $SYSTEM['colonyright'] || $colony['admin'] == $windid) && $groupRight['allowdisband']) ? '1' : '0';
				break;
			default :
				$ifable = 1;
				break;
		}
		return $ifable;
	}

	function jumpToBBS($q,$a,$cyid) {
		if($q == 'group') {
			$showtype = $a;	
		} elseif ($q == 'galbum') {
			$showtype = 'galbum';	
		}
		if($showtype) {
			ObHeader("thread.php?cyid=$cyid&showtype=$showtype");
		} else {
			ObHeader("thread.php?cyid=$cyid");
		}
	}

	function jumpToColony($showtype,$cyid) {
		if($showtype != 'galbum') {
			$q = 'group';
			$a = $showtype;
		} elseif ($showtype == 'galbum') {
			$q = 'galbum';
		}
		if($q == 'group' && $a) {
			ObHeader("apps.php?q=group&a=$a&cyid=$cyid");
		} elseif ($q == 'galbum') {
			ObHeader("apps.php?q=galbum&cyid=$cyid");
		} else {
			ObHeader("apps.php?q=group&cyid=$cyid");
		}
	}

	/**
	 * ��ȡ�������б�Ĺ���Ȩ��
	 * @author zhudong
	 * @return 
	 */

	function getManageCheck ($ifbbsadmin,$ifcolonyadmin) {
		if($ifbbsadmin || array_sum($this->right['topicadmin'])>0 && $ifcolonyadmin){
			return true;
		}
	}

	function getColonyAdmin() {
		global $windid;
		$right = ($this->info['ifadmin'] == '1' || $this->info['admin'] == $windid) ? 1 : 0;
		return $right;
	}

	function getBbsAdmin ($isGM) {
		global $SYSTEM;
		$right = ($isGM || $SYSTEM['colonyright']) ? 1 : 0;
		return $right;
	}
}
?>
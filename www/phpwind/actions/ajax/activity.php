<?php
!defined('P_W') && exit('Forbidden');

S::gp(array('job'),'GP');
if (!$windid && !in_array($job,array('memberlist'))) {
	Showmsg('not_login');
}

if ($job == 'user_authentication') {//�û������֤
	$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
	$data = $userService->get($winduid, false, false, true);
	$tradeinfo = $data['tradeinfo'] ? $data['tradeinfo'] : '';
	$tradeinfo = unserialize($tradeinfo);

	$user_id = $tradeinfo['user_id'];
	$isBinded = $tradeinfo['isbinded'];
	$isCertified = $tradeinfo['iscertified'];
	if ($user_id && $isBinded == 'T' && $isCertified != 'T') {//����󶨣���δʵ����֤
		require_once(R_P . 'lib/activity/alipay_push.php');
		$alipayPush = new AlipayPush();
		$is_success = $alipayPush->user_query($winduid);//��ѯ�Ƿ�ʵ����֤
		if ($is_success == 'T') {
			$tradeinfo['iscertified'] = $is_success;
			$tradeinfo = addslashes(serialize($tradeinfo));
			$userService->update($winduid, array(), array(), array('tradeinfo'=>$tradeinfo));
			echo 'success';
		} else {
			echo 'iscertified_fail';
		}
	} elseif ($isBinded != 'T') {
		echo 'isbinded_fail';
	}
	ajax_footer();
} elseif ($job == 'upload') {//�����ϴ�
	S::gp(array('actmid'),GP,2);
	if ($_POST['step'] == 2){
		L::loadClass('activityupload', 'upload', false);
		require_once(R_P.'require/functions.php');
		$img = new ActivityUpload(0,$actmid);
		PwUpload::upload($img);
		pwFtpClose($GLOBALS['ftp']);
		$fileuploadurl = $img->attachs['fileuploadurl'];
		if ($fileuploadurl) {
			echo "success\t".$fileuploadurl;
		} else {
			echo "error\t";
		}
	} else {
		require_once PrintEot('ajax');
	}
	ajax_footer();

} elseif ($job == 'delimg') {//ɾ������

		S::gp(array('tid','actmid','fieldid'),GP,2);
		S::gp(array('attachment'),GP);
		if (!$actmid || !$fieldid) {
			echo 'fail';ajax_footer();
		}

		if ($tid) {
			$fieldService = L::loadClass('ActivityField', 'activity');
			$fielddata = $fieldService->getField($fieldid);
			$fieldname = $fielddata['fieldname'];
			if ($fielddata['ifdel'] == 1) {
				$tableName = getActivityValueTableNameByActmid($actmid, 1, 1);
			} else {
				$tableName = getActivityValueTableNameByActmid($actmid, 0, 0);
			}

			$path = $db->get_value("SELECT $fieldname FROM $tableName WHERE tid=". S::sqlEscape($tid));
			!$path && $path = $attachment;
		} else {
			$path = $attachment;
		}
		

		if (strpos($path,'..') !== false) {
			return false;
		}
		$lastpos = strrpos($path,'/') + 1;
		$s_path = substr($path, 0, $lastpos) . 's_' . substr($path, $lastpos);

		if (!file_exists("$attachpath/$path")) {
			if (pwFtpNew($ftp,$db_ifftp)) {
				$ftp->delete($path);
				$ftp->delete($s_path);
				require_once(R_P.'require/functions.php');
				pwFtpClose($ftp);
			}
		} else {
			P_unlink("$attachdir/$path");
			if (file_exists("$attachdir/$s_path")) {
				P_unlink("$attachdir/$s_path");
			}
		}

		if ($tid) {
			$db->update("UPDATE $tableName SET $fieldname='' WHERE tid=". S::sqlEscape($tid));
		}

		echo 'success';
		ajax_footer();

} elseif ($job == 'recommend') {//�Ƽ�

	S::gp(array('tid','actmid'),G,2);
	if (!S::inArray($windid, $manager) && !$SYSTEM['recommendactive']) {
		echo "noright\t";
	}
	$defaultValueTableName = getActivityValueTableNameByActmid();
	$rt = $db->get_one("SELECT recommend FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid) . " AND actmid=" .S::sqlEscape($actmid));
	if ($rt) {
		if ($rt['recommend'] == 1) {
			$newrecommend = 0;
		} else {
			$newrecommend = 1;
		}
		$db->update("UPDATE $defaultValueTableName SET recommend=".S::sqlEscape($newrecommend)." WHERE tid=".S::sqlEscape($tid)." AND actmid=" .S::sqlEscape($actmid));

		echo "success\t".$newrecommend;
	} else {
		echo "fail\t";
	}
	ajax_footer();

} elseif ($job == 'signup') {//����

	S::gp(array('tid','thelast','authorid','actmid'),GP,2);

	L::loadClass('ActivityForBbs', 'activity', false);
	$postActForBbs = new PW_ActivityForBbs($data);

	$data = array();

	$defaultValueTableName = getActivityValueTableNameByActmid();
		$defaultValue = $db->get_one("SELECT iscertified,iscancel,signupstarttime,signupendtime,endtime,minparticipant,maxparticipant,userlimit,fees,paymethod,batch_no,genderlimit,t.subject,t.authorid FROM $defaultValueTableName dv LEFT JOIN pw_threads t USING(tid) WHERE dv.tid=".S::sqlEscape($tid));

	if ($defaultValue['iscancel']) {//�ж��Ƿ�ȡ��
		Showmsg('act_signup_iscancel_error');
	}
	if ($defaultValue['signupstarttime'] > $timestamp || $defaultValue['signupendtime'] < $timestamp) {//δ�ڱ���ʱ����
		Showmsg('act_signup_time_error');
	}
	$defaultValue['authorid'] == $winduid && Showmsg('act_signup_owner_error');//�������޷����뱨��
	
	$signRulesGener = array (1,2,3);
	($defaultValue['genderlimit'] != 1 && $defaultValue['genderlimit'] != $signRulesGener[$winddb[gender]]) && Showmsg('�׸û�������Ա�,���޷�����Ŷ');//�Ա�����
	
	$feesdb = unserialize($defaultValue['fees']);//����
	$isFree = count($feesdb) > 0 ? false : true;//�жϸû�Ƿ����
	$paymethod = $defaultValue['paymethod'];//֧����ʽ

	if ($defaultValue['paymethod'] == 1) {//ʵ����֤�ʹ������
		if (!$defaultValue['batch_no']) {
			Showmsg('act_signup_batch_no_error');
		}
		if (!$defaultValue['iscertified']) {
			Showmsg('act_signup_iscertified_error');
		}
	}

	if ($thelast != 1) {//������һ��

		//�ѱ�������
		$orderMemberNums = $postActForBbs->peopleAlreadySignup($tid);
		if ($defaultValue['maxparticipant']) {
			$theMoreNum = $defaultValue['maxparticipant'] - $orderMemberNums;//ʣ�౨������
			$theMoreNum == 0 && Showmsg('act_signup_is_full');//������������
		}

		if (empty($_POST['step'])) {
			$memberdb = $db->get_one("SELECT nickname,mobile FROM pw_activitymembers WHERE tid=".S::sqlEscape($tid)."AND uid=".S::sqlEscape($winduid). " AND fupid=0 AND isadditional=0 ORDER BY signuptime DESC" );

			$signupType = array();//������������
			foreach ($feesdb as $key => $value) {
				$signupType[$key] = $value['condition'];
			}
			$fieldService = L::loadClass('ActivityField', 'activity');
			$userlimitIfable = $fieldService->getFieldByModelIdAndName($actmid, 'userlimit');

			$isU = (!$userlimitIfable || $defaultValue['userlimit'] == 2 && isFriend($authorid,$winduid) || $defaultValue['userlimit'] == 1) ? 1 : 0;//��������
				
			require_once PrintEot('ajax');
			ajax_footer();
		} elseif ($_POST['step'] == '2') {
			PostCheck();
			S::gp(array('signup','telephone','mobile','address','message','ifanonymous','nickname'));

			$totalsignupnums = 0;
			$totalcash = 0;
			$newsignup = array();
			foreach ($signup as $key => $value) {
				$value = (int)$value;
				$totalcash += $feesdb[$key]['money'] * $value;//�ܷ���
				$totalsignupnums += $value;//������
				$newsignup[$key] = (int)$value;
			}
			$signup = serialize($newsignup);

			if ($totalsignupnums == 0) {//������������Ϊ1��
				Showmsg('act_signupnums_error');
			} elseif ($totalsignupnums > 65000) {//������������
				Showmsg('act_signupnums_error_max');
			}
			if (!$mobile || !$nickname) {//�ƺ��ֻ�����
				Showmsg('act_mobile_nickname_error');
			}
			if ($defaultValue['maxparticipant'] && $defaultValue['maxparticipant'] - $orderMemberNums < $totalsignupnums) {//�ܱ�����������
				Showmsg('act_num_overflow');
			}

			$sqlarray = array(
				'tid'			=> $tid,
				'uid'			=> $winduid,
				'actmid'		=> $actmid,
				'username'		=> $windid,
				'signupnum'		=> $totalsignupnums,
				'signupdetail'	=> $signup,
				'nickname'		=> $nickname,
				'totalcash'		=> $totalcash,
				'mobile'		=> $mobile,
				'telephone'		=> $telephone,
				'address'		=> $address,
				'message'		=> $message,
				'ifanonymous'	=> $ifanonymous,
				'signuptime'	=> $timestamp
			);
			$db->update("INSERT INTO pw_activitymembers SET " . S::sqlSingle($sqlarray));
			$actuid = $db->insert_id();
			$nextto = 'signup';
			$db->update("UPDATE $defaultValueTableName SET updatetime=".S::sqlEscape($timestamp)." WHERE tid=".S::sqlEscape($tid));//�����б�̬ʱ��

			$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
			/*����Ϣ֪ͨ ���� ������*/
			$authorUserName = $userService->getUserNameByUserId($authorid);
			if (!$authorUserName) { 
				Showmsg('user_not_exists');
			}
			//�����Ϣ������ʱ��/
			$threadInfo = $db->get_one("SELECT * FROM pw_threads WHERE tid=".S::sqlEscape($tid));
			$createTime = get_date($threadInfo['postdate']);
			//* require_once pwCache::getPath(D_P.'data/bbscache/forum_cache.php');
			pwCache::getData(D_P.'data/bbscache/forum_cache.php');
			M::sendNotice(
				array($authorUserName),
				array(
					'title' => getLangInfo('writemsg', 'activity_signup_new_title', array(
							'username' => $windid
						)
					),
					'content' => getLangInfo('writemsg', 'activity_signup_new_content', array(
							'username' => $windid,
							'uid' => $winduid,
							'tid' => $tid,
							'fid' => $threadInfo['fid'],
							'createtime' =>$createTime,
							'fname' => $forum[$threadInfo['fid']]['name'],
							'subject' => $defaultValue['subject']
						)
					),
				),'notice_active','notice_active'
			);
			
			
			Showmsg('act_signup_nextstep');
		}
	} elseif ($thelast == 1) {
		S::gp(array('totalsignupnums','totalcash','actuid'));
		$fees = '';
		foreach ($feesdb as $value) {
			$fees .= ($fees ? '��' : '') .$value['money'] . getLangInfo('other','act_RMB') . '/' . $value['condition'];
		}
		!$fees && $fees = getLangInfo('other','act_free');

		$signupdetail = $db->get_value("SELECT signupdetail FROM pw_activitymembers WHERE actuid=".S::sqlEscape($actuid));
		$signupnumsdb = unserialize($signupdetail);//��������
		$signupdetail = '';
		foreach ($signupnumsdb as $key => $value) {
			$signupdetail .= ($signupdetail ? '��' : '') .$feesdb[$key]['condition'].$value.getLangInfo('other','act_people');
		}
		require_once PrintEot('ajax');ajax_footer();
	}
} elseif ($job == 'memberlist') {//չʾ������Ϣ�б�
	S::gp(array('page','tid','fid','authorid','paymethod','actmid'),GP,2);

	L::loadClass('ActivityForBbs', 'activity', false);
	$postActForBbs = new PW_ActivityForBbs($data);

	$data = array();
	$author = $db->get_value('SELECT authorid FROM pw_threads WHERE tid = ' . S::sqlEscape($tid));
	$isAdminright = $postActForBbs->getAdminRight($author);
		
	$db_perpage = 20;

	$count = $payMemberNums = $orderMemberNums = 0;
	$query = $db->query("SELECT signupnum,ifpay FROM pw_activitymembers WHERE fupid=0 AND tid=".S::sqlEscape($tid));
	while ($rt = $db->fetch_array($query)) {
		if ($rt['ifpay'] != 3) {//���ùرյĲ���
			$orderMemberNums += $rt['signupnum'];//�ѱ�������
		}
		if ($rt['ifpay'] != 0 && $rt['ifpay'] != 3) {//�Լ�֧��1��ȷ��֧��2����������4
			$payMemberNums += $rt['signupnum'];//�Ѿ����������
		}
		$count++;
	}

	if ($winduid) {
		$page < 1 && $page = 1;
		$numofpage = ceil($count/$db_perpage);
		if ($numofpage && $page > $numofpage) {
			$page = $numofpage;
		}
		$start = ($page-1)*$db_perpage;
		$limit = S::sqlLimit($start,$db_perpage);
		$pages = numofpage($count, $page, $numofpage, "pw_ajax.php?action=$action&job=$job&tid=$tid&authorid=$authorid&paymethod=$paymethod&", null, 'ajaxview');

		$defaultValueTableName = getActivityValueTableNameByActmid();
		$defaultValue = $db->get_one("SELECT fees,endtime,iscancel FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));
		$feesdb = unserialize($defaultValue['fees']);
		$isFree = count($feesdb) > 0 ? false : true;//�жϸû�Ƿ����

		$iscancel = $defaultValue['iscancel'];//�ȡ��
		$endtimeStatus = $defaultValue['endtime'] + 30*86400 - $timestamp;//����ʱ���һ����,>0 ����Բ���,< 0�޷�����

		$memberlistdb = $addmemberlistdb = $refundmemberlistdb = array();
		$query = $db->query("SELECT * FROM pw_activitymembers WHERE fupid=0 AND tid=".S::sqlEscape($tid)." ORDER BY (uid=".S::sqlEscape($winduid).") DESC,ifpay ASC,actuid DESC $limit");//��������
		while ($rt = $db->fetch_array($query)) {
			$rt['signuptime'] = get_date($rt['signuptime'],'n-j H:i');
			if ($rt['signupdetail']) {
				$rt['signupdetail'] = unserialize($rt['signupdetail']);
				foreach ($rt['signupdetail'] as $key => $value) {
					$rt['signupmember'] .= ($rt['signupmember'] ? '��' : '') .$feesdb[$key]['condition'].$value.'��';
				}
			}
			$isFree && $rt['totalcash'] = getLangInfo('other','act_free');//������ѣ�������Ϊ���
			$memberlistdb[$rt['actuid']] = $rt;
		}
		if ($paymethod == 1 && $memberlistdb) {
			$query = $db->query("SELECT * FROM pw_activitymembers WHERE isadditional=1 ORDER BY ifpay ASC,actuid DESC");//׷������
			while ($rt = $db->fetch_array($query)) {
				$rt['signuptime'] = get_date($rt['signuptime'],'n-j H:i');
				$addmemberlistdb[$rt['fupid']][] = $rt;
			}
			$query = $db->query("SELECT * FROM pw_activitymembers WHERE isrefund=1 ORDER BY actuid DESC");
			while ($rt = $db->fetch_array($query)) {
				$rt['signuptime'] = get_date($rt['signuptime'],'n-j H:i');
				$refundmemberlistdb[$rt['fupid']][] = $rt;
			}
			
			foreach ($memberlistdb as $value) {
				$rowspannum = 0;
				$rowspannum += count($addmemberlistdb[$value['actuid']]) + count($refundmemberlistdb[$value['actuid']]);
				if ($addmemberlistdb[$value['actuid']]) {
					foreach ($addmemberlistdb[$value['actuid']] as $val) {
						if ($refundmemberlistdb[$val['actuid']]) {
							$rowspannum += count($refundmemberlistdb[$val['actuid']]);
						}
					}
				}
				$memberlistdb[$value['actuid']]['rowspannum'] = $rowspannum;
			}
		}
	}
		
	require_once PrintEot('ajax');ajax_footer();
} elseif ($job == 'detailshow') {//չʾ������Ϣ�����飩
		S::gp(array('actuid','authorid','tid','paymethod','actmid'),GP,2);
		$data = array();

		L::loadClass('ActivityForBbs', 'activity', false);
		$postActForBbs = new PW_ActivityForBbs($data);

		$isAdminright = $postActForBbs->getAdminRight($authorid);

		$detailinfo = $db->get_one("SELECT * FROM pw_activitymembers WHERE actuid=".S::sqlEscape($actuid));
		if ($detailinfo['isrefund'] || $detailinfo['isadditional']  || $detailinfo['ifanonymous'] && !$isAdminright && $detailinfo['uid'] != $winduid) {//׷�ӵ��޷��鿴���˿���޷��鿴��������û��Ȩ�޵��޷��鿴
			Showmsg('act_detailshow_error');
		}
		$defaultValueTableName = getActivityValueTableNameByActmid();
		$defaultValue = $db->get_one("SELECT fees,endtime FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));
		$endtimeStatus = $defaultValue['endtime'] + 30*86400 - $timestamp;//����ʱ���һ����,>0 ����Բ���,< 0�޷�����
		$feesdb = unserialize($defaultValue['fees']);
		$isFree = count($feesdb) > 0 ? false : true;//�жϸû�Ƿ����
		$detailinfo['signupdetail'] = unserialize($detailinfo['signupdetail']);
		foreach ($detailinfo['signupdetail'] as $key => $value) {
			$detailinfo['signupmember'] .= ($detailinfo['signupmember'] ? '��' : '') .$feesdb[$key]['condition'].$value.'��';
		}
		
		if ($paymethod == 1) {
			$addmemberlistdb = $refundfupdb = array();
			$query = $db->query("SELECT actuid,totalcash,ifpay,refundcost FROM pw_activitymembers WHERE isadditional=1 AND fupid=".S::sqlEscape($detailinfo['actuid']));
			while ($rt = $db->fetch_array($query)) {
				$addmemberlistdb[] = $rt;
				if ($rt['refundcost']) {
					$refundfupdb[$rt['actuid']] = $rt['actuid'];
					
				}
				$refundfupdb[$detailinfo['actuid']] = $detailinfo['actuid'];
			}
			if ($refundfupdb) {
				$refundmemberlistdb = array();
				$query = $db->query("SELECT actuid,totalcash,ifpay FROM pw_activitymembers WHERE isrefund=1 AND fupid IN(".S::sqlImplode($refundfupdb).")");
				while ($rt = $db->fetch_array($query)) {
					$refundmemberlistdb[] = $rt;
				}
			}
		}
		
		require_once PrintEot('ajax');ajax_footer();
} elseif ($job == 'modify') {//�޸ı�����Ϣ
		S::gp(array('actmid','actuid','tid'),GP,2);

		$defaultValueTableName = getActivityValueTableNameByActmid();
		$defaultValue = $db->get_one("SELECT signupstarttime,signupendtime,endtime,minparticipant,maxparticipant,userlimit,fees,paymethod FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));

		$feesdb = unserialize($defaultValue['fees']);//����
		$paymethod = $defaultValue['paymethod'];//֧����ʽ

		//�ѱ�������
		$orderMemberNums = $db->get_value("SELECT SUM(signupnum) as sum FROM pw_activitymembers WHERE tid=".S::sqlEscape($tid)." AND fupid=0 AND ifpay IN('0','1','2','4')");

		if (empty($_POST['step'])) {

			$defaultValue['endtime'] + 30*86400 < $timestamp && Showmsg('act_modify_error');//���������30���޷��޸�

			$signupinfo = $db->get_one("SELECT * FROM pw_activitymembers WHERE actuid=".S::sqlEscape($actuid)." AND uid=".S::sqlEscape($winduid));
			if ($signupinfo['isrefund'] || $signupinfo['isadditional']  || !$signupinfo) {//׷�ӵ��޷��޸ġ��˿���޷��޸ġ�������Ϣ������
				Showmsg('act_modify_error');
			}
			$signupinfo['signupdetail'] = unserialize($signupinfo['signupdetail']);
			$ownernums = $signupinfo['signupnum'];//���˱�������
			$signupinfo['ifanonymous'] && $checked = 'checked';
			
			$signupType = array();//������������
			foreach ($feesdb as $key => $value) {
				$signupType[$key] = $value['condition'];
			}
			$defaultValue['maxparticipant'] && $theMoreNum = $defaultValue['maxparticipant'] - $orderMemberNums;//ʣ�౨������

			require_once PrintEot('ajax');ajax_footer();
		} elseif ($_POST['step'] == '2') {
			PostCheck();
			S::gp(array('ownernums'),P,2);
			S::gp(array('signup','telephone','mobile','address','message','ifanonymous','nickname'),P);

			$totalsignupnums = 0;
			$totalcash = 0;
			$newsignup = array();
			foreach ($signup as $key => $value) {
				$value = (int)$value;
				$totalcash += $feesdb[$key]['money'] * $value;//�ܷ���
				$totalsignupnums += $value;//������
				$newsignup[$key] = (int)$value;
			}
			$signup = serialize($newsignup);

			if ($totalsignupnums == 0) {//������������Ϊ1��
				echo 'act_signupnums_error';ajax_footer();
			} elseif ($totalsignupnums > 65000) {//������������
				echo 'act_signupnums_error_max';ajax_footer();
			}
			if (!$mobile || !$nickname) {//�ƺ��ֻ�����
				echo 'act_mobile_nickname_error';ajax_footer();
			}
			if ($defaultValue['maxparticipant'] && $defaultValue['maxparticipant'] - $orderMemberNums + $ownernums < $totalsignupnums) {//�ܱ�����������
				echo 'act_num_overflow';ajax_footer();
			}

			$sqlarray = array(
				'signupnum'		=> $totalsignupnums,
				'signupdetail'	=> $signup,
				'nickname'		=> $nickname,
				'totalcash'		=> $totalcash,
				'mobile'		=> $mobile,
				'telephone'		=> $telephone,
				'address'		=> $address,
				'message'		=> $message,
				'ifanonymous'	=> $ifanonymous
			);

			$db->update("UPDATE pw_activitymembers SET " . S::sqlSingle($sqlarray)." WHERE actuid=".S::sqlEscape($actuid)." AND uid=".S::sqlEscape($winduid));
			$db->update("UPDATE $defaultValueTableName SET updatetime=".S::sqlEscape($timestamp)." WHERE tid=".S::sqlEscape($tid));//�����б�̬ʱ��

			echo "success";
			ajax_footer();
		}
} elseif ($job == 'close') {//�رձ�����Ϣ
	S::gp(array('actuid','paymethod','tid'),GP,2);

	$memberdb = $db->get_one("SELECT am.ifpay,am.uid,am.username,am.tid,am.isadditional,am.isrefund,t.subject,t.authorid,t.author FROM pw_activitymembers am LEFT JOIN pw_threads t ON am.tid=t.tid WHERE actuid=".S::sqlEscape($actuid));
	$isadditional = $memberdb['isadditional'];//�Ƿ�׷��

	if (empty($_POST['step'])) {
		$defaultValueTableName = getActivityValueTableNameByActmid();
		$defaultValue = $db->get_one("SELECT endtime FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));
		$defaultValue['endtime'] + 30*86400 < $timestamp && Showmsg('act_endtime_toolong');//����ʱ���һ����,>0 ����Բ���,< 0�޷�����

		$memberdb['ifpay'] != 0 && Showmsg('act_close_payment_error');//ֻ��δ֧��״̬�¿��Բ���
		$winduid != $memberdb['authorid'] && Showmsg('act_close_payment_noright');//ֻ�з����˿��Բ���
		$memberdb['isrefund'] && Showmsg('act_close_payment_noright');//�˿���޷��ر�

		require_once PrintEot('ajax');ajax_footer();			
	} elseif ($_POST['step'] == 2) {
		PostCheck();
		if ($paymethod == 1) {//�����֧�����������Ҫ֧�����ӿ�ͨ��
			require_once(R_P . 'lib/activity/alipay_push.php');
			$alipayPush = new AlipayPush();
			$is_success = $alipayPush->close_aa_detail_payment($tid,$actuid);
			echo $is_success;
			ajax_footer();
		} else {
			$db->update("UPDATE pw_activitymembers SET ifpay=3 WHERE actuid=".S::sqlEscape($actuid));//���ùر�
			$defaultValueTableName = getActivityValueTableNameByActmid();
			$db->update("UPDATE $defaultValueTableName SET updatetime=".S::sqlEscape($timestamp)." WHERE tid=".S::sqlEscape($tid));//�����б�̬ʱ��
			//�ֽ�֧��
			/*����Ϣ֪ͨ ɾ�������� ������*/
			M::sendNotice(
				array($memberdb['author']),
				array(
					'title' => getLangInfo('writemsg', 'activity_signup_close_title', array(
							'username' => $memberdb['username']
						)
					),
					'content' => getLangInfo('writemsg', 'activity_signup_close_content', array(
							'username' => $memberdb['username'],
							'uid'      => $memberdb['uid'],
							'tid'      => $memberdb['tid'],
							'subject'  => $memberdb['subject']
						)
					)
				), 'notice_active', 'notice_active'
			);
			
			/*����Ϣ֪ͨ ɾ�������� ������*/			
			M::sendNotice(
				array($memberdb['username']),
				array(
					'title' => getLangInfo('writemsg', 'activity_signuper_close_title', array(
							'username' => $memberdb['author']
						)
					),
					'content' => getLangInfo('writemsg', 'activity_signuper_close_content', array(
							'username' => $memberdb['author'],
							'uid'      => $memberdb['authorid'],
							'tid'      => $memberdb['tid'],
							'subject'  => $memberdb['subject']
						)
					)
				),'notice_active', 'notice_active'
			);
			
			echo "success";
			ajax_footer();
		}
	}
} elseif ($job == 'confirmpay') {//ȷ��֧��
	S::gp(array('tid','actuid','authorid','actmid'),GP,2);
	
	$memberdb = $db->get_one("SELECT am.ifpay,am.uid,am.username,am.tid,am.totalcash,am.isadditional,am.isrefund,t.subject,t.authorid,t.author FROM pw_activitymembers am LEFT JOIN pw_threads t ON am.tid=t.tid WHERE am.actuid=".S::sqlEscape($actuid));
	$defaultValueTableName = getActivityValueTableNameByActmid();

	if (empty($_POST['step'])) {
		
		$defaultValue = $db->get_one("SELECT endtime FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));
		$defaultValue['endtime'] + 30*86400 < $timestamp && Showmsg('act_endtime_toolong');//����ʱ���һ����,>0 ����Բ���,< 0�޷�����
		$memberdb['ifpay'] != 0 && Showmsg('act_confirmpay_error');
		$winduid != $memberdb['authorid'] && Showmsg('act_confirmpay_noright');//ֻ�з����˿��Բ���
		$memberdb['isrefund'] && Showmsg('act_confirmpay_noright');//�˿���޷��ر�

		require_once PrintEot('ajax');ajax_footer();
	} elseif ($_POST['step'] == 2) {
		PostCheck();

		if ($memberdb['ifpay'] == 0) {
			/*��ѯ����״̬*/
			require_once(R_P . 'lib/activity/alipay_push.php');
			$alipayPush = new AlipayPush();
			$alipayPush->query_aa_detail_payment($tid,$actuid);
			/*��ѯ����״̬*/
		}
		$ifpay = $db->get_value("SELECT ifpay FROM pw_activitymembers WHERE actuid=".S::sqlEscape($actuid));
		if ($ifpay == 0) {
			$db->update("UPDATE pw_activitymembers SET ifpay=2 WHERE actuid=".S::sqlEscape($actuid));//����֧����ifpay=2
			$db->update("UPDATE $defaultValueTableName SET updatetime=".S::sqlEscape($timestamp)." WHERE tid=".S::sqlEscape($tid));//�����б�̬ʱ��
		}

		/*֧���ɹ�������ͨ��־*/
		L::loadClass('ActivityForBbs', 'activity', false);
		$postActForBbs = new PW_ActivityForBbs($data);

		$data = array();
		$statusValue = $postActForBbs->getActivityStatusValue($tid);
		$postActForBbs->UpdatePayLog($tid,$actuid,$statusValue);
		/*֧���ɹ�������ͨ��־*/

		/*����Ϣ֪ͨ ȷ��֧�� ������*/
		//ֻ�ʺ�֧����֧��
		$contentText = $memberdb['isadditional'] ? 'activity_confirmpay2_content' : 'activity_confirmpay_content';
		M::sendNotice(
			array($memberdb['author']),
			array(
				'title' => getLangInfo('writemsg', 'activity_confirmpay_title', array(
						'username' => $memberdb['username']
					)
				),
				'content' => getLangInfo('writemsg', $contentText, array(
						'username'  => $memberdb['username'],
						'uid'       => $memberdb['uid'],
						'tid'       => $tid,
						'subject'   => $memberdb['subject'],
						'totalcash'	=> $memberdb['totalcash']
					)
				)
			),
			'notice_active', 
			'notice_active'
		);

		/*����Ϣ֪ͨ ȷ��֧�� ������*/
		//ֻ�ʺ�֧����֧��
		$signuperContentText = $memberdb['isadditional'] ? 'activity_confirmpay2_signuper_content' : 'activity_confirmpay_signuper_content';
		M::sendNotice(
			array($memberdb['username']),
			array(
				'title' => getLangInfo('writemsg', 'activity_confirmpay_signuper_title', array(
						'username' => $memberdb['author']
					)
				),
				'content' => getLangInfo('writemsg', $signuperContentText, array(
						'username'  => $memberdb['author'],
						'uid'       => $memberdb['authorid'],
						'tid'       => $tid,
						'subject'   => $memberdb['subject'],
						'totalcash'	=> $memberdb['totalcash']
					)
				)
			),
			'notice_active', 
			'notice_active'
		);
		
		echo "success";
		ajax_footer();
	}
} elseif ($job == 'toalipay') {//ȥ֧��������
	S::gp(array('actuid','tid','actmid','signuper'),GP,2);
	if (empty($_POST['step'])) {
		L::loadClass('ActivityForBbs', 'activity', false);
		$postActForBbs = new PW_ActivityForBbs($data);

		$data = array();

		$memberdb = $db->get_one("SELECT am.ifpay,am.isrefund,am.uid,am.ifanonymous,t.authorid FROM pw_activitymembers am LEFT JOIN pw_threads t USING(tid) WHERE am.actuid=".S::sqlEscape($actuid));
		$memberdb['authorid'] == $winduid && Showmsg('act_toalipay_authorid');//�������޷������֧��
		$isAdminright = $postActForBbs->getAdminRight($memberdb['authorid']);
		if ($memberdb['isrefund'] || $memberdb['ifanonymous'] && !$isAdminright && $memberdb['uid'] != $winduid) {//�˿���޷�֧����������û��Ȩ�޵��޷�֧��
			Showmsg('act_toalipay_error');
		}

		if ($memberdb['ifpay'] == 0) {
			/*��ѯ����״̬*/
			require_once(R_P . 'lib/activity/alipay_push.php');
			$alipayPush = new AlipayPush();
			$ifpay = $alipayPush->query_aa_detail_payment($tid,$actuid);
			/*֧���ɹ�������ͨ��־*/
			if (is_numeric($ifpay) && $ifpay > 0) {
				$statusValue = $postActForBbs->getActivityStatusValue($tid);
				$postActForBbs->UpdatePayLog($tid,$actuid,$statusValue);
			}
			/*֧���ɹ�������ͨ��־*/
			/*��ѯ����״̬*/
		}
			
		$fromuid = $signuper == $winduid ? '-1' : $winduid;
		$defaultValueTableName = getActivityValueTableNameByActmid();
		$defaultValue = $db->get_one("SELECT dt.fees,dt.paymethod,dt.iscancel,dt.endtime,am.signupdetail,am.ifpay,am.totalcash,am.additionalreason FROM $defaultValueTableName dt LEFT JOIN pw_activitymembers am USING(tid) WHERE am.tid=".S::sqlEscape($tid)." AND am.actuid=".S::sqlEscape($actuid));
		$additionalreason = $defaultValue['additionalreason'];

		!$defaultValue && Showmsg('undefined_action');//���ײ�����
		$defaultValue['paymethod'] != 1 && Showmsg('act_toalipay_paymethod');//ֻ��֧����ʽΪ֧�����ſ���֧��
		$defaultValue['endtime'] + 30*86400 < $timestamp && Showmsg('act_endtime_toolong');//����ʱ���һ����,>0 ����Բ���,< 0�޷�����
		$defaultValue['ifpay'] != 0 && Showmsg('act_toalipay_payed');//ֻ��δ֧��״̬�ſ���֧��
		$defaultValue['iscancel'] == 1 && Showmsg('act_iscancelled_y');//���ȡ���޷�֧��

		$feesdb = unserialize($defaultValue['fees']);//����
		$fees = '';
		foreach ($feesdb as $value) {
			$fees .= ($fees ? '��' : '') .$value['money'] . getLangInfo('other','act_RMB') . '/'.$value['condition'];
		}
		$signupdetail = $db->get_value("SELECT signupdetail FROM pw_activitymembers WHERE actuid=".S::sqlEscape($actuid));
		if ($signupdetail) {
			$signupnumsdb = unserialize($signupdetail);//��������
			$signupdetail = '';
			foreach ($signupnumsdb as $key => $value) {
				$signupdetail .= ($signupdetail ? '��' : '') .$feesdb[$key]['condition'].$value.getLangInfo('other','act_people');
			}
		}
		
		$totalcash = $defaultValue['totalcash'];
		require_once PrintEot('ajax');ajax_footer();
	}
	require_once PrintEot('ajax');ajax_footer();
} elseif ($job == 'sendmsg') {//Ⱥ������Ϣ
		S::gp(array('tid','actmid','authorid'));
		$data = array();
		L::loadClass('ActivityForBbs', 'activity', false);
		$postActForBbs = new PW_ActivityForBbs($data);

		$isAdminright = $postActForBbs->getAdminRight($authorid);
		$isAdminright != 1 && Showmsg('act_sendmsg_noright');		

		if (empty($_POST['step'])) {
			$tid = $db->get_value("SELECT tid FROM pw_activitymembers WHERE tid=".S::sqlEscape($tid));
			!$tid && Showmsg('act_sendmsg_fail');

			require_once PrintEot('ajax');ajax_footer();
		} elseif ($_POST['step'] == 2) {
			PostCheck();
			S::gp(array('subject','atc_content','tid','ifsave'));
			require_once(R_P.'require/common.php');

			$msg_title   = trim($subject);
			$atc_content = trim($atc_content);
			if (empty($atc_content) || empty($msg_title)) {
				Showmsg('msg_empty');
			} elseif (strlen($msg_title) > 75 || strlen($atc_content) > 1500) {
				Showmsg('msg_subject_limit');
			}
			require_once(R_P.'require/bbscode.php');
			$wordsfb = L::loadClass('FilterUtil', 'filter');
			if (($banword = $wordsfb->comprise($msg_title)) !== false) {
				Showmsg('title_wordsfb');
			}
			if (($banword = $wordsfb->comprise($atc_content, false)) !== false) {
				Showmsg('content_wordsfb');
			}

			$query = $db->query("SELECT uid FROM pw_activitymembers WHERE tid=".S::sqlEscape($tid)." GROUP BY uid");
			$ifuids = $sqladd = $msglog = $uiddb = array();
			while ($rt = $db->fetch_array($query)) {
				$uiddb[] = $rt['uid'];
			}
		//	$uids = S::sqlImplode($uiddb);
			if ($uiddb) {
				$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
				$userNames = $userService->getUserNamesByUserIds($uiddb);
				M::sendNotice(
					$userNames,
					array(
						'create_uid'		=> $winduid,
						'create_username'	=> $windid,
						'title' 			=> $msg_title,
						'content' 			=> $atc_content
					),
					'notice_active',
					'notice_active'
				);
			}
			Showmsg('send_success');
		}
} elseif ($job == 'refund') {//�˿�
	S::gp(array('actuid','actmid','tid','authorid','thelast'),GP,2);
	$memberdb = $db->get_one("SELECT am.ifpay,am.isrefund,am.username,am.totalcash,t.authorid FROM pw_activitymembers am LEFT JOIN pw_threads t USING(tid) WHERE am.actuid=".S::sqlEscape($actuid));
	if ($memberdb['isrefund'] || $memberdb['authorid'] != $winduid) {//�˿���޷����������Ƿ������޷�����
		Showmsg('act_refund_noright');
	}
	$memberdb['ifpay'] != 1 && Showmsg('act_refund_error');//֧����֧���ɹ������˿�

	if ($thelast != 1) {
		$defaultValueTableName = getActivityValueTableNameByActmid();
		$defaultValue = $db->get_one("SELECT paymethod,endtime,fees FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));
		$feesdb = unserialize($defaultValue['fees']);//����
		$isFree = count($feesdb) > 0 ? false : true;//�жϸû�Ƿ����
		$isFree && Showmsg('act_refund_free');//��ѵĻ�����˿�

		$defaultValue['endtime'] + 30*86400 < $timestamp && Showmsg('act_endtime_toolong');//����ʱ���һ����,>0 ����Բ���,< 0�޷�����
		$paymethod = $defaultValue['paymethod'];
		$paymethod != 1 && Showmsg('act_toalipay_paymethod');//֧����֧�������˿�
	}

	$tempcost = $db->get_value("SELECT SUM(totalcash) as sum FROM pw_activitymembers WHERE isrefund=1 AND fupid=".S::sqlEscape($actuid));//���˷���
	$morecost = $memberdb['totalcash'] - $tempcost;//ʣ�����
	if ($morecost == 0) {//�˿����
		$db->update("UPDATE pw_activitymembers SET ifpay=4 WHERE actuid=".S::sqlEscape($actuid));
		Showmsg('act_refund_cost_finish');
	}

	if ($thelast != 1) {//�˿��һ��
		if (empty($_POST['step'])) {

			require_once PrintEot('ajax');ajax_footer();
		} elseif ($_POST['step'] == 2) {
			S::gp(array('reason','cost'));

			if ($cost == 0 || number_format($cost, 2, '.', '') > number_format($morecost, 2, '.', '') || !preg_match("/^(([1-9]\d*)|0)(\.\d{0,2})?$/", $cost)) {
				Showmsg('act_refund_costerror');
			}

			if (strlen($reason) > 250 || $reason == '') {
				Showmsg('act_refund_reasonlenthlimit');
			}

			$sqlArray = array(
				'refundcost'	=> $cost,
				'refundreason'	=> $reason,
			);

			$db->update("UPDATE pw_activitymembers SET ".S::sqlSingle($sqlArray)." WHERE actuid=".S::sqlEscape($actuid));

			$nextto = 'refund';
			Showmsg('act_refund_nextstep');
		}
	} elseif ($thelast == 1) {
		S::gp(array('cost'));
		require_once PrintEot('ajax');ajax_footer();
	}	
} elseif ($job == 'additional') {//׷�ӷ���
	S::gp(array('actuid','tid','more','authorid','actmid'),GP,2);

	if (empty($more)) {
		$memberdb = $db->get_one("SELECT isrefund,isadditional,uid,actmid,username,actuid FROM pw_activitymembers WHERE actuid=".S::sqlEscape($actuid));
	}
	$defaultValueTableName = getActivityValueTableNameByActmid();

	if (empty($_POST['step'])) {
		$defaultValue = $db->get_one("SELECT dt.paymethod,dt.iscancel,dt.endtime,dt.fees,t.authorid FROM $defaultValueTableName dt LEFT JOIN pw_threads t USING(tid) WHERE dt.tid=".S::sqlEscape($tid));
		$feesdb = unserialize($defaultValue['fees']);//����
		$isFree = count($feesdb) > 0 ? false : true;//�жϸû�Ƿ����
		$isFree && Showmsg('act_additional_free');//��ѵĻ����׷�ӷ���

		if ($memberdb['isrefund'] || $memberdb['isadditional'] || $defaultValue['authorid'] != $winduid) {//�˿���޷�׷�ӡ�׷�ӵĽ����޷�������ֻ�з����˲���׷��
			$defaultValue['authorid'] != $winduid && Showmsg('act_additional_noright');
		}
		$defaultValue['paymethod'] != 1 && Showmsg('act_toalipay_paymethod');//ֻ��֧����֧������׷��
		$defaultValue['iscancel'] == 1 && Showmsg('act_iscancelled_y');//�ȡ���޷�׷��
		$defaultValue['endtime'] + 30*86400 < $timestamp && Showmsg('act_endtime_toolong');//����ʱ���һ����,>0 ����Բ���,< 0�޷�����

		if (!empty($more)) {

			$memberlist = array();
			$query = $db->query("SELECT uid,username FROM pw_activitymembers WHERE fupid=0 AND tid=".S::sqlEscape($tid). " GROUP BY uid");
			while ($rt = $db->fetch_array($query)) {
				$memberlist[$rt['uid']] = $rt['username'];
			}
		}
			
		require_once PrintEot('ajax');ajax_footer();
	} elseif ($_POST['step'] == 2) {
		S::gp(array('totalcost','uids','cost_','additionalreason'),P);
			
		$db->update("UPDATE $defaultValueTableName SET updatetime=".S::sqlEscape($timestamp)." WHERE tid=".S::sqlEscape($tid));//�����б�̬ʱ��

		require_once R_P.'require/msg.php';
		$thread = $db->get_one("SELECT subject FROM pw_threads WHERE tid=".S::sqlEscape($tid));

		if (empty($more)) {
			if (!preg_match("/^(([1-9]\d*)|0)(\.\d{0,2})?$/", $totalcost) || $totalcost == 0) {
				echo "totalcost_error";ajax_footer();
			}
			$sqlarray = array(
				'fupid'				=> $actuid,
				'tid'				=> $tid,
				'uid'				=> $memberdb['uid'],
				'actmid'			=> $memberdb['actmid'],
				'username'			=> $memberdb['username'],
				'totalcash'			=> $totalcost,
				'signuptime'		=> $timestamp,
				'isadditional'		=> 1,
				'additionalreason'	=> $additionalreason,
			);
			$db->update("INSERT INTO pw_activitymembers SET " . S::sqlSingle($sqlarray));

			/*����Ϣ֪ͨ ׷�ӷ��� ������*/
			M::sendNotice(
				array($memberdb['username']),
				array(
					'title' => getLangInfo('writemsg', 'activity_additional_title', array(
							'username' => $windid
						)
					),
					'content' => getLangInfo('writemsg', 'activity_additional_content', array(
							'username'  => $windid,
							'uid'       => $winduid,
							'tid'       => $tid,
							'subject'   => $thread['subject'],
							'totalcash'	=> $totalcost
						)
					)
				),
				'notice_active', 
				'notice_active'
			);
			echo "success";
			ajax_footer();
		} else {//����׷��		
			$query = $db->query("SELECT * FROM pw_activitymembers WHERE fupid=0 AND isrefund=0 AND tid=".S::sqlEscape($tid)." AND uid IN(".S::sqlImplode($uids). ") GROUP BY uid ORDER BY signuptime DESC");
			while ($rt = $db->fetch_array($query)) {
				$actmid = $rt['actmid'];
				$memberdb[$rt['uid']] = $rt;
			}
			foreach ($uids as $uid) {
				if (!preg_match("/^(([1-9]\d*)|0)(\.\d{0,2})?$/", $cost_[$uid])) {
					continue;
				}
				if (isset($memberdb[$uid]) && $cost_[$uid] > 0) {
					$sqlarray[] = array(
						'fupid'				=> $memberdb[$uid]['actuid'],
						'tid'				=> $tid,
						'uid'				=> $memberdb[$uid]['uid'],
						'actmid'			=> $memberdb[$uid]['actmid'],
						'username'			=> $memberdb[$uid]['username'],
						'totalcash'			=> $cost_[$uid],
						'signuptime'		=> $timestamp,
						'isadditional'		=> 1,
						'additionalreason'	=> $additionalreason,
					);
				} elseif (!isset($memberdb[$uid]) && $cost_[$uid] > 0) {
					$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
					$username = $userService->getUserNameByUserId($uid);
					$sqlarray[] = array(
						'fupid'				=> 0,
						'tid'				=> $tid,
						'uid'				=> $uid,
						'actmid'			=> $actmid,
						'username'			=> $username,
						'totalcash'			=> $cost_[$uid],
						'signuptime'		=> $timestamp,
						'isadditional'		=> 1,
						'additionalreason'	=> $additionalreason,
					);
				}
				
				M::sendNotice(
					array($memberdb[$uid]['username']),
					array(
						'title' => getLangInfo('writemsg', 'activity_additional_title', array(
								'username' => $windid
							)
						),
						'content' => getLangInfo('writemsg', 'activity_additional_content', array(
								'username'  => $windid,
								'uid'       => $winduid,
								'tid'       => $tid,
								'subject'   => $thread['subject'],
								'totalcash'	=> $cost_[$uid]
							)
						)
					),
					'notice_active', 
					'notice_active'
				);

			}
			$db->update("INSERT INTO pw_activitymembers (fupid,tid,uid,actmid,username,totalcash,signuptime,isadditional,additionalreason) VALUES " . S::sqlMulti($sqlarray));
			echo "success";
			ajax_footer();
		}
	}
} elseif ($job == 'addnewmember') {//׷�ӷ���ʱ������û�
	S::gp(array('tid'),P,2);
	S::gp(array('username'),P);
	
	$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
	$uid = $userService->getUserIdByUserName($username);
	$readcheck = $db->get_value("SELECT authorid FROM pw_threads WHERE tid=".S::sqlEscape($tid)." AND authorid=". S::sqlEscape($uid));
	$check = $db->get_value("SELECT uid FROM pw_activitymembers WHERE fupid=0 AND tid=".S::sqlEscape($tid)." AND uid=". S::sqlEscape($uid));
	if ($readcheck) {
		echo "authorerror\t";
	} elseif ($check) {
		echo "exist\t";
	} elseif ($uid) {
		echo "success\t$uid\t$username";
	} else {
		echo "error\t";
	}
	ajax_footer();
}

function isFriend($uid,$friend) {
	global $db;
	if ($db->get_value("SELECT uid FROM pw_friends WHERE uid=" . S::sqlEscape($uid) . ' AND friendid=' . S::sqlEscape($friend) . " AND status='0'")) {
		return true;
	}
	return false;
}
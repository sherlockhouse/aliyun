<?php
/**
 * ��Էǹ���������ļ�
 * Ҫ����һ�����ܣ����ڴ�����ļ�����������ϣ�Ȼ���ڶ�Ӧ�����������û���Ȩ�޿���.
 * @package  operate.php
 */
if (isset($_GET['ajax'])) {
	define('AJAX','1');
}
require_once('global.php');

!$windid && Showmsg('not_login');
S::gp(array('action','atc_content'));
if (!S::inArray($action, array('showping', 'toweibo', 'report', 'commentsPic'))) {
	Showmsg('undefined_action');
}

$template = 'ajax_operate';
if (empty($_POST['step']) && !defined('AJAX')) {
	require_once(R_P.'require/header.php');
	$template = 'operate';
}

if ($action == 'showping') {

	//require_once(R_P . 'require/forum.php');
	//require_once(R_P . 'require/bbscode.php');
	//require_once R_P . 'require/pingfunc.php';
	S::gp(array('tid','selid','pid','page'));
	if (empty($selid) && empty($pid)) {
		Showmsg('selid_illegal');
	}
	$isGM = CkInArray($windid,$manager);
	$jump_pid = $pid ? $pid : $selid[0];
	empty($selid) && $selid = array($pid);
	!is_array($selid) && $selid = array($selid);

	$pingService = L::loadClass("ping", 'forum'); /* @var $pingService PW_Ping */
	$pingService->init($tid, $selid);
	if (($return = $pingService->check($_POST['step'])) !== true) {
		Showmsg($return);
	}

	if (empty($_POST['step'])) {

		$ratelist = $noneJsonList = $raterange = array();
		$creditselect = '';
		$postData =& $pingService->postData;
		$showReply = $pingService->checkReply($tid) === true ? true : false;
		$userGroupsService = L::loadClass('UserGroups', 'user'); 
		$systemGroup = $userGroupsService->getUserGroupIds('system');
		//�»�Ա ����ʱ������
		if (!S::inArray($groupid,$systemGroup) && $db_postallowtime && ($timestamp - $winddb['regdate']) < $db_postallowtime*60) $showReply = false;
		$creditselect = array();
		foreach ($pingService->markset as $cid => $value) {
			$creditselect[$cid] = $credit->cType[$cid];
			$raterange[$cid] = array('min'=>$value['minper'],'max'=>$value['maxper'],'mrpd'=>$value['leavepoint']);
		}
		$creditselect == '' && showmsg('markright_set_error');
		$noneJsonList = getratelist($raterange, $pingService->markset);
		$ratelist = pwJsonEncode($noneJsonList);
		$jscredit = pwJsonEncode($pingService->markset);
		$reason_sel = '';
		$reason_a = explode("\n",$db_admingradereason);
		foreach ($reason_a as $k => $v) {
			if ($v = trim($v)) {
				$reason_sel .= "<option value=\"$v\">$v</option>";
			} else {
				$reason_sel .= "<option value=\"\">-------</option>";
			}
		}
		
		/*
		if (!$pingService->ifmsg && $groupid!='3') {
			$check_Y = 'disabled';
			$check_N = 'checked';
		} else {*/
			$check_Y = 'checked';
			$check_N = '';
		//}
		require_once PrintEot($template);footer();

	} elseif ($_POST['step'] == 1) {
		if ($SYSTEM['enterreason'] && !$atc_content) {
			Showmsg('enterreason');
		}
		//����
		InitGP(array('cid','addpoint','ifmsg','atc_content','ifpost'), 'P');
		if($ifpost){
			$len = strlen($atc_content);
			list($postq,$showq) = explode("\t", $db_qcheck);
			PostCheck(1, ($db_gdcheck & 4) && (!$db_postgd || $winddb['postnum'] < $db_postgd), ($db_ckquestion & 4 && (!$postq || $winddb['postnum'] < $postq) && $db_question));
			if($len < $db_postmin){
				Showmsg("�ظ����ݳ��Ȳ���С��{$db_postmin}�ֽ�");
			}elseif($len > $db_postmax){
				Showmsg("�ظ����ݳ��Ȳ��ܴ���{$db_postmax}�ֽ�");
			}
		}
		$return = $pingService->doPing($cid, $addpoint, array(
			'ifmsg'			=> 1,
			'ifpost'		=> $ifpost,
			'atc_content'	=> $atc_content
		));
		if ($return === true) {
			if (defined('AJAX')) {
				if (is_array($pingLog)){
					//��ȡ��������Ϣ
					foreach ($pingLog as $k=>$log){
						$pid = is_numeric($k)? $k : 0;
						$pingTotal = $pingService->getPingLogAll($tid,$pid);
						$pingLog[$k] = array(
							'detail' => $log,
							'total'	=> (array)$pingTotal
						);
					}
				}
				$pingLog = pwJsonEncode($pingLog);
				echo "success\t{$pingLog}";
				ajax_footer();
			} else {
				refreshto("read.php?tid=$tid&ds=1&page=$page#$jump_pid",'operate_success');
			}
		} else {
			showmsg($return);
		}
	}
} elseif ($action == 'toweibo') {

	$messageService = L::loadClass("message", 'message'); /* @var $messageService PW_Message */
	$numbers = $messageService->statisticUsersNumbers(array($winduid));
	$totalMessage = isset($numbers[$winduid]) ? $numbers[$winduid] : 0;
	$max = (int)$_G['maxmsg'];
	S::gp(array('type','id','tid','cyid','tucool'));
	require_once(R_P. 'apps/weibo/lib/sendweibo.class.php');
	$sendWeiboServer = getWeiboFactory($type);
	$sendWeiboServer->init($id);
	$content = $sendWeiboServer->getContent();
	if ($tucool) $content = '������ӵ�ͼƬ����Ŷ~'.$content;
	$mailSubject = $sendWeiboServer->getMailSubject();
	$mailContent = $sendWeiboServer->getMailContent();
	$pids = $sendWeiboServer->getPids();
	require_once PrintEot('ajax_operate');ajax_footer();

} elseif ($action == 'report') {

	!$_G['allowreport'] && Showmsg('report_right');
	S::gp(array('pid','page'),'GP',2);
	$rt  = $db->get_one("SELECT tid FROM pw_report WHERE uid=".S::sqlEscape($winduid).' AND tid='.S::sqlEscape($tid).' AND pid='.S::sqlEscape($pid));
	$rt && Showmsg('have_report');

	if (empty($_POST['step'])) {

		require_once PrintEot($template);footer();

	} else {
		PostCheck();
		S::gp(array('ifmsg','type','reason'),'P');

		$pwSQL = S::sqlSingle(array(
			'tid'	=> $tid,
			'pid'	=> $pid,
			'uid'	=> $winduid,
			'type'	=> $type,
			'reason'=> $reason
		));
		$db->update("INSERT INTO pw_report SET $pwSQL");

		if ($ifmsg) {
			if ($pid > 0) {
				$pw_posts = GetPtable('N',$tid);
				$sqlsel = "t.content as subject,t.postdate,";
				$sqltab = "$pw_posts t";
				$sqladd = 'WHERE t.pid='.S::sqlEscape($pid);
			} else {
				$sqlsel = "t.subject,t.postdate,";
				$sqltab = "pw_threads t";
				$sqladd = 'WHERE t.tid='.S::sqlEscape($tid);
			}
			$rs = $db->get_one("SELECT $sqlsel t.fid,f.forumadmin FROM $sqltab LEFT JOIN pw_forums f USING(fid) $sqladd");

			if ($rs['forumadmin']) {
				//* include_once pwCache::getPath(D_P.'data/bbscache/forum_cache.php');
				pwCache::getData(D_P.'data/bbscache/forum_cache.php');
				$admin_a = explode(',',$rs['forumadmin']);
				$iftpc = $pid ? '0' : '1';
				M::sendMessage(
					$winduid,
					array($admin_a),
					array(
						'create_uid'	=> $winduid,
						'create_username'	=> $windid,
						'title' => getLangInfo('writemsg','report_title'),
						'content' => getLangInfo('writemsg','report_content_'.$type.'_'.$iftpc,array(
							'fid'		=> $rs['fid'],
							'tid'		=> $tid,
							'pid'		=> $pid,
							'postdate'	=> get_date($rs['postdate']),
							'forum'		=> $forum[$rs['fid']]['name'],
							'subject'	=> $rs['subject'],
							'admindate'	=> get_date($timestamp),
							'reason'	=> stripslashes($reason)
						)),
					)
				);
			}
		}
		if (defined('AJAX')) {
			Showmsg('report_success');
		} else {
			refreshto("read.php?tid=$tid&ds=1&page=$page",'report_success');
		}
	}
} elseif ($action == 'commentsPic') {
	S::gp(array('tid','aid'));
	require_once PrintEot('ajax_operate');ajax_footer();

} else {
	Showmsg('undefined_action');
}

function getphotourl($path,$thumb = false) {
	global $pwModeImg;
	if (!$path) {
		return "$imgpath/nophoto.gif";
	}
	$lastpos = strrpos($path,'/') + 1;
	$thumb && $path = substr($path, 0, $lastpos) . 's_' . substr($path, $lastpos);
	list($path) = geturl($path, 'show');
	if ($path == 'imgurl' || $path == 'nopic') {
		return "$imgpath/nophoto.gif";
	}
	return $path;
}
function getShareType ($type) {
	switch ($type) {
		case 'article' :
		case 'diary' :
			return 'diary';
		case 'album' :
		case 'photo' :
			return 'photo';
		case 'topic' :
		case 'reply' :
			return 'post';
		case 'groups' :
		case 'group'  :
			return 'group';
		case 'video' :
			return 'video';
		case 'music' :
			return 'music';
		default :
			return 'link';
	}
}

function getratelist($raterange, $markset) {
	$ratelist = $result = array();
	foreach($raterange as $id => $rating) {
		if(isset($markset[$id])) {
			$increaseOffset = floor((abs($rating['max'])+1) /4);
			$decreaseOffset = floor((abs($rating['min'])+1)/ 4);
			if($rating['min'] >= 0){							//�����Сֵ����0
				$rating['min'] == 0 && $rating['min'] = 1;
				$min[$id] = $rating['min'];						//����$min[$id]���
				$increaseOffset = floor(($rating['max'] - $rating['min'])/4);   //���ӵĲ����ı�
			}
			if($rating['max'] < 0){								//������ֵС��0
				$rating['max'] == 0 && $rating['max'] = 1;
				$max[$id] = $rating['max'];						//����$max[$id]���
				$decreaseOffset = floor(abs($rating['min'] - $rating['max'])/4);
			}

			if($increaseOffset == 0) $increaseOffset = 1;
			if($decreaseOffset == 0) $decreaseOffset = 1;
			if($rating['max'] > $rating['min']) {
				$ratelist[$id][$rating['max']] = $rating['max'];
				for($i=1; $i<5; $i++){			//�׺�β����ֵ�̶���ֻ��ѭ��4��
					if($min[$id]){	//�����Сֵ����0
						$ratelist[$id]['max'][$i] = $i > 1 ? '+'.(strval($ratelist[$id]['max'][$i-1])+$increaseOffset) : '+'.$min[$id];
						$ratelist[$id]['min'] = array();
						if($ratelist[$id]['max'][$i] >= $rating['max']) $ratelist[$id]['max'][$i] = '+'.$rating['max']; 
					}elseif($max[$id]){	////������ֵС��0
						$ratelist[$id]['max'] = array();
						$ratelist[$id]['min'][$i] = $i > 1 ? (strval($ratelist[$id]['min'][$i-1])-$decreaseOffset) : $max[$id];
						if($ratelist[$id]['min'][$i] && ($ratelist[$id]['min'][$i] <= $rating['min'])) $ratelist[$id]['min'][$i] = $rating['min'];
					}else{
						$ratelist[$id]['max'][$i] = $i > 1 ? '+'.(strval($ratelist[$id]['max'][$i-1])+$increaseOffset) : '+1';
						$ratelist[$id]['min'][$i] = $i > 1 ? (strval($ratelist[$id]['min'][$i-1])-$decreaseOffset) : '-1';
						if($ratelist[$id]['min'][$i] && ($ratelist[$id]['min'][$i] <= $rating['min'])) $ratelist[$id]['min'][$i] = $rating['min'];
						if($ratelist[$id]['max'][$i] >= $rating['max']) $ratelist[$id]['max'][$i] = '+'.$rating['max']; 
					}
				}
				array_push($ratelist[$id]['max'], '+'.$rating['max']);	//��ĩβ�������ֵ
				array_push($ratelist[$id]['min'], $rating['min']);		//��ĩβ������Сֵ
				$ratelist[$id]['max'] = array_unique($ratelist[$id]['max']);
				$ratelist[$id]['min'] = array_unique($ratelist[$id]['min']);
				if($min[$id]) $ratelist[$id]['min'] = array();		//��Сֵ����0�����ֵС��0������в���ʾ
				if($max[$id]) $ratelist[$id]['max'] = array();
				
			} elseif ($rating['max'] == $rating['min']) {
				$ratelist[$id]['max'] = array($rating['max']);
				$ratelist[$id]['min'] = array();
			}
		}
	}
	foreach ($ratelist as $key =>$v) {
		$result[$key] = array_values($v);
	}
	return $ratelist;
}

function getWeiboFactory($type) {
	switch ($type) {
		case 'diary':
			$obj = new diaryWeibo();break;
		case 'group':
			$obj = new groupWeibo();break;
		case 'groupactive':
			$obj = new groupActiveWeibo();break;
		case 'album':
			$obj = new albumWeibo();break;
		case 'photo':
			$obj = new photoWeibo();break;
		case 'topic':
			$obj = new topicWeibo();break;
		case 'reply':
			$obj = new replyWeibo();break;
		default:
			$obj = getWeiboOtherFactory($type);
	}
	return $obj;
}

function getWeiboOtherFactory($name) {
	$classes = array();
	$name = strtolower($name);
	$filename = R_P . "apps/weibo/lib/sendweibo/" . $name . ".weibo.php";
	if (!is_file($filename)) Showmsg('undefined_action');
	$class = 'weibo_' . ucfirst($name);
	if (isset($classes[$class])) return $classes[$class];
	if (!class_exists($class)) include S::escapePath($filename);
	$classes[$class] = new $class();
	return $classes[$class];
}

function getAdminReasonOptions(){
	global $db_adminreason;
	$reason_sel  = '';
	$reason_a=explode("\n",$db_adminreason);
	foreach($reason_a as $k=>$v){
		if($v=trim($v)){
			$reason_sel .= "<option value=\"$v\">$v</option>";
		} else{
			$reason_sel .= "<option value=\"\">-------</option>";
		}
	}
	return $reason_sel;
}
?>
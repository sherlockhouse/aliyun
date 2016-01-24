<?php
!defined('P_W') && exit('Forbidden');
/**
 * ������SERVICE
 * 
 * @package PW_Weibo
 * @author suqian && sky_hold@163.com
 */
class PW_Weibo {

	var $_map = array();
	var $_mapflip = array();
	var $_mapDescript = array();
	var $_timestamp = 0;

	function __construct(){
		global $timestamp;
		global $windid;
		$this->_windid = $windid;
		$this->_timestamp = $timestamp;
		$this->_typeMap();
	}

	function PW_Weibo(){
		$this->__construct();
	}

	/**
	 * ��֤�û��Ƿ����÷���ĳ���Ͳ���(���ӡ���־����ᡢȺ��)ͬʱת�ص�������
	 * @param int $uid
	 * @param string $type
	 * return bool
	 */
	function checkSendPrivacy($uid, $type) {
		$privacyService = L::loadClass('privacy','sns');
		return $privacyService->getIsFeed($uid, $type);
	}
	/*
	function getAtPrivacyByUserNames($usernames){
		$privacyService = L::loadClass('privacy','sns');
		return $privacyService->getAtFeed($uid);
	}
	*/
	/**
	 * ������˽���õ��û�����������@���û���
	 * @return array $uids
	 */
	function filterPrivacyAtUsers($usernames,$uid = 0){
		if (!$GLOBALS[_G]['allowat']) return array();
		$uid = intval($uid);
		$uid < 1 && $uid = $GLOBALS['winduid'];
		$privacyService = L::loadClass('privacy','sns');
		$attentionService = L::loadClass('attention','friend');
		$checkAttentioned = array();
		$returnUids = array();
		$filterUsers = $privacyService->getAtFeedByUserNames($usernames);
		//����������
		$blackList = $attentionService->getBlackListToMe($uid,array_keys($filterUsers));
		if (S::isArray($blackList)) {
			foreach ($blackList as $v) {
				unset($filterUsers[$v]);
			}
		}
		foreach($filterUsers as $k=>$v) {
			if ($v['at_isfeed'] == 0){
				//����������@
				$returnUids[$k] = $v['username'];
			} elseif ($v['at_isfeed'] == 1) {
				//��ע���˿�@
				$checkAttentioned[$k] = $v['username'];
			}
		}
		//check attention
		if ($checkAttentioned) {
			foreach ($checkAttentioned as $k=>$v){
				if ($attentionService->isFollow($k,$uid)){
					$returnUids[$k] = $v;
				} 
			}
		}
		return $returnUids;
	}
	/**
	 * �鿴�û��ռ估���Ӧ����˽
	 * @param int $uid
	 * @param string $type Ӧ������
	 * return bool
	 */
	function checkUserSpacePrivacy($uid, $type = null) {
		$privacyService = L::loadClass('privacy','sns');
		return $privacyService->getIsPriacy($uid, $type);
	}
	
	/**
	 * �����·�����֤
	 * @param str $content ��֤����
	 * @param int $groupid ��֤�û���
	 * @param boolean $ifempty �ж������Ƿ�Ϊ��
	 */
	function sendCheck($content, $groupid,$ifempty = false) {
		if ($groupid == '6') return '���ѱ�����!';
		if (!$this->groupCheck($groupid)) return 'weibo_group_right';
		$content = $this->escapeStr($content);
		if (!$content && empty($ifempty)) return '���������ݲ�Ϊ��';
		if (strlen($content) > 255) return '���������ݲ��ܶ���255�ֽ�';
		$filterService = L::loadClass('FilterUtil', 'filter');
		//����Ц��
		$smileService = L::loadClass('smile','smile');
		$tmpSmiles = $smileTags = array();
		$tmpSmiles = $smileService->findByType();
		foreach ($tmpSmiles as $v) {
			$smileTags[] = strtolower($v['tag']);
		}
		$content = $smileTags ? str_replace($smileTags, '', $content) : $content;
		if (($GLOBALS['banword'] = $filterService->comprise($content)) !== false) {
			return 'content_wordsfb';
		}
		return true;
	}

	function groupCheck($groupid) {
		global $o_weibo_groups;
		return ($groupid == 3 || empty($o_weibo_groups) || strpos($o_weibo_groups,",$groupid,") !== false);
	}
	 
	 function checkReplyRight($tid) {
	 	global $isGM,$winddb,$isBM;
	 	$threadService = L::loadClass('threads', 'forum');
	 	L::loadClass('forum', 'forum', false);
	 	$read = $threadService->getByThreadId($tid);
	 	$pwforum = new PwForum($read['fid']);	
	 	$forumset =& $pwforum->forumset;
	 	if (getstatus($read['tpcstatus'], 7)) {
			$robbuildService = L::loadClass('RobBuild', 'forum'); /* @var $robbuildService PW_RobBuild */
			$robbuild = $robbuildService->getByTid($tid);
			if ($robbuild['starttime'] > $this->_timestamp) return false;
		}
	 	$tpc_locked = $read['locked']%3<>0 ? 1 : 0;
	 	$admincheck = ($isGM || $isBM) ? 1 : 0;
	 	$isAuthStatus = $admincheck || (!$forumset['auth_allowrp'] || $pwforum->authStatus($winddb['userstatus'],$forumset['auth_logicalmethod']) === true);
	 	if ($isAuthStatus && (!$tpc_locked || $SYSTEM['replylock']) && ($admincheck || $pwforum->allowreply($winddb, $groupid))) {
	 		return true;
	 	}
	 	return false;
	 }

	function escapeStr($str) {
		if (!$str = trim($str)) return '';
		$tmp = preg_replace('/(&nbsp;){1,}/', ' ', $str);
		return preg_replace_callback('/#([^#]+)#/', array(&$this,'_callbackTrimTopicStr'), $tmp);
	}

	function _callbackTrimTopicStr($matches){
		return '#'.trim($matches[1]).'#';
	}
	/**
	 * ����������
	 * @param int $uid ������
	 * @param string $content ��������Ϣ����
	 * @param string $type �������������
	 * @param string $typeid �������������ID
	 * @param array  $extra ��չ�ֶ�
	 * @return boolean
	 * @access public
	 */
	function send($uid, $content, $type = 'weibo' ,$typeid = 0, $extra = array()) {
		if (!isset($this->_map[$type]) || !$this->_isLegalId($uid)) {
			return 0;
		}
		if ($this->_map[$type] > 9 && !$this->checkSendPrivacy($uid, $this->_privacyMapping($type))) {
			return 0;
		}
		$fromThread = $extra['fid'] && $extra['title'];
/*
		if ($fromThread && $extra['atusers']) {
			$extra['atusers'] = $this->filterPrivacyAtUsers($extra['atusers']);
		}
*/
		//��@�û�,�ظ�������������
		if (!$extra['atusers'] && $extra['pid']) return 0;
		$content = $this->escapeStr($content);
		$extra = $fromThread ? (array)$extra : array_merge((array)$extra, $this->_analyseContent($uid, $content));
		$message = array(
			'uid' => $uid,
			'username' => $this->_windid,
			'content' => $content,
			'postdate' => $this->_timestamp,
			'type' => $this->_map[$type],
			'objectid' => intval($typeid),
			'contenttype' => isset($extra['photos']) ? 1 : 0,
			'extra' => $extra ? addslashes(serialize($extra)) : ''
		);
		$contentDao = L::loadDB('weibo_content','sns');
		if (!$mid = $contentDao->insert($message)) {
			return 0;
		}
		$this->_addRelation($uid, $mid, $type);

		if ($fromThread && $extra['atusers']) {
			$extra['atusers'] && $this->addRefer(array_keys($extra['atusers']), $mid);
		} elseif ($extra['refer']) {
			$this->addRefer(array_keys($extra['refer']), $mid);
		}
		
		if ($extra['cyid']) {
			$this->_addCnRelation($extra['cyid'], $mid);
		}
		if ($extra['topics']) {
			$this->addTopics($extra['topics'],$mid);
		}
		$userCache = L::loadClass('Usercache', 'user');
		$userCache->delete($uid, 'weibo');

		//platform weibo app
		$siteBindService = L::loadClass('WeiboSiteBindService', 'sns/weibotoplatform/service'); /* @var $siteBindService PW_WeiboSiteBindService */
		if ($siteBindService->isOpen() && !$siteBindService->isBind($type) && !$extra['noSync']) {
			$userBindService = L::loadClass('WeiboUserBindService', 'sns/weibotoplatform/service'); /* @var $userBindService PW_WeiboUserBindService */
			if ($userBindService->isBindOne($uid)) {
				unset($message['extra']);
				$syncer = L::loadClass('WeiboSyncer', 'sns/weibotoplatform'); /* @var $syncer PW_WeiboSyncer */
				$syncer->send($mid, $type, $message, $extra);
			}
		}

		return $mid;
	}
	
	/**
	 * ���������������е������ǩ
	 * @param int $uid ������
	 * @param string $content ����������
	 * @return array
	 */
	function _analyseContent($uid, $content) {
		$array = array();
		if ($refer = $this->_analyseRefer($uid, $content)) {
			$array['refer'] = $refer;
		}
		if ($topics = $this->_analyseTopics($content)) {
			$array['topics'] = $topics;
		}
		return $array;
	}

	/**
	 * ���������������е�#����#
	 * @param string $content ����������
	 * @return array $topics
	 */
	function _analyseTopics($content) {
		$topics = array();
		//preg_match_all('/#([^#]+)#/U',$content,$matches) && $topics = $matches[1];
		preg_match_all('/#([^@&#!*\(\)]+)#/U',$content,$matches) && $topics = $matches[1];
		foreach ($topics as $k=>$v) {
			$v = trim($v);
			//�����ڲ���������
			if(preg_match("/(https?|ftp|gopher|news|telnet|mms|rtsp):\/\/[a-z0-9\/\-_+=.~!%@?%&;:$\\��\|]+(#.+)?/ie", $v)) continue;
			if(!isset($v)) {
				unset($topics[$k]);
				continue;
			}
			$topics[$k] = $v;
		}
		return $topics;
	}
	
	/**
	 * ���������������е�@����
	 * @param int $uid �����·�����
	 * @param string $content ����������
	 * @return array
	 */
	function _analyseRefer($uid, $content) {
		if (!$GLOBALS[_G]['allowat']) return array();
		preg_match_all('/@([^\\&\'"\/\*,<>\r\t\n\s#%?@:��]+)\s?/i', $content, $matchs);
		$array = array();
		if ($matchs[1]) {
			($GLOBALS[_G]['atnum'] > 0 && count((array)$matchs[1]) > $GLOBALS[_G]['atnum']) && $matchs[1] = array_slice($matchs[1], 0 ,$GLOBALS[_G]['atnum']);
			$userService = L::loadClass('UserService', 'user');
			$uInfo = $userService->getByUserNames($matchs[1]);
			foreach ($uInfo as $rt) {
				$array[$rt['uid']] = $rt['username'];
			}
		}
		return $array;
	}

	/**
	 * ��������¹�ϵ��
	 * @param int $uid
	 * @param int $mid
	 * @param string $type
	 * @access public
	 */
	function _addRelation($uid, $mid, $type) {
		global $db;
		$privacyService = L::loadClass('privacy','sns');
		if ($privacyService->getIsFollow($uid, 'self')) {
			$relationDao = L::loadDB('weibo_relations','sns');
			$relationDao->insert(array(
				'uid' => $uid,
				'mid' => $mid,
				'authorid' => $uid,
				'type' => $this->_map[$type],
				'postdate' => $this->_timestamp
			));
		}
		$typeKey = $this->_privacyMapping($type);
	//	$_sql_add = in_array($typeKey, array('article','diary','photos','group')) ? " AND o.{$typeKey}_isfollow=1" : '';
		//todo �����Ժ�����ٵ���
		$db->update("INSERT INTO pw_weibo_relations (uid,mid,authorid,type,postdate) SELECT a.uid, ".S::sqlEscape($mid).", ".S::sqlEscape($uid).", ".S::sqlEscape($this->_map[$type]).", ".S::sqlEscape($this->_timestamp)." FROM pw_attention a LEFT JOIN pw_friends f ON a.uid=f.uid AND a.friendid=f.friendid AND f.status=0 LEFT JOIN pw_ouserdata o ON a.uid=o.uid WHERE a.friendid=" . S::sqlEscape($uid) . " AND a.uid!=a.friendid AND (o.uid IS NULL OR (o.friend_isfollow=1 AND f.uid IS NOT NULL OR o.cnlesp_isfollow=1 AND f.uid IS NULL)$_sql_add) ORDER BY a.joindate DESC LIMIT 1000");
	}

	/**
	 * ��������¹�ϵ��
	 * @param array $data �����¹�ϵ������
	 * @return int
	 * @access public
	 */
	function addRelation($data) {
		if (!is_array($data)) {
			return 0;
		}
		$relationDao =  L::loadDB('weibo_relations','sns');
		return $relationDao->addRelation($data);
	}
	
	/**
	 * ����ᵽ�ҵ������¹�ϵ 
	 * @param array $data ��ӵ�����
	 * @return int
	 * @access public
	 */
	function addRefer($uids, $mid) {
		if (empty($uids) || !is_array($uids)) {
			return 0;
		}
		$data = array();
		foreach ($uids as $key => $uid) {
			$data[] = array($uid, $mid);
		}
		$referDao = L::loadDB('weibo_referto','sns');
		$affect = $referDao->addRefer($data);
		
		$userService = L::loadClass('UserService', 'user');
		$userService->updatesByIncrement($uids, array(), array('newreferto' => 1));
		return $affect;
	}
	
	/**
	 * ��ӻ���
	 * @param array $topics �������а����Ļ���
	 * @param int mid ������id
	 * @return int
	 * @access public
	 */
	function addTopics($topics, $mid) {
		if (!$topics || !is_array($topics) || !$mid) {
			return false;
		}
		$topicService = L::LoadClass('topic','sns'); /* @var $topicService PW_Topic */
		$array = $topicService->addTopic($topics);
		if ($array) {
			//���topic ��weibo ��ϵ
			foreach ($array as $v)
				$topicService->addTopicRelations($v,$mid);
		}
	}
	function _addCnRelation($cyid,$mid){
		if(!$this->_isLegalId($cyid) || !$this->_isLegalId($mid)){
			return 0;
		}
		$cnData['cyid'] = $cyid;
		$cnData['mid'] = $mid;
		$referDao =  L::loadDB('weibo_cnrelations','sns');
		return $referDao->insert($cnData);
	}
	/**
	 * ��ӹ�עʱ�����͵�����
	 * @param int $uid ��ӹ�ע��
	 * @param int $auid ����ע����
	 * @parmm int $num Ĭ�����͵�$uid������
	 * @return int 
	 * @access public
	 */
	function pushData($uid, $auid, $num = 20) {
		if (!$this->_isLegalId($uid) || !$this->_isLegalId($auid) || !$this->_isLegalId($num)) {
			return 0;
		}
		$contentDao =  L::loadDB('weibo_content','sns');		
		$weibos = $contentDao->getUserWeibos($auid, 1, $num);
		if (empty($weibos)) {
			return 0;
		}
		$rData = array();
		foreach($weibos as $key => $value){
			$rData[] = array(
				'uid' => $uid,
				'mid' => $value['mid'],
				'authorid' => $auid,
				'type' => $value['type'],
				'postdate' => $value['postdate']
			);
		}
		return $this->addRelation($rData);
	}
	
	/**
	 * ȡ����עʱ��ɾ������ĳ���˵������¹�ϵ
	 * @param int $uid ������ID
	 * @param int $authorid ��������ID
	 * @return int
	 * @access public
	 */
	function removeRelation($uid,$authorid){
		if(!$this->_isLegalId($uid) || !$this->_isLegalId($authorid)){
			return 0;
		}
		$relationDao =  L::loadDB('weibo_relations','sns');
		return $relationDao->removeRelation($uid,$authorid);
	}
	
	function deleteAttentionRelation($uid, $num) {
		if ($num <= WEIBO_RELATION_NUM) return 0;
		$num = min($num - WEIBO_RELATION_NUM, 1000);
		$relationDao =  L::loadDB('weibo_relations','sns');
		return $relationDao->deleteAttentionRelation($uid, $num);
	}

	/**
	 * ȡ�����ݿ��¼ָ���ĵ�����¼
	 * @param array $records ��¼
	 * @param string $key ָ���ļ�¼
	 * @return array
	 * @access private
	 */
	function _getFieldOfRecords($records, $key) {
		$field = array();
		if (!is_array($records)) {
			return array();
		}
		foreach ($records as $rkey => $value) {
			if (isset($value[$key])) {
				$field[] = $value[$key];
			}
		}
		return $field;
	}

	/**
	 * ���������¹�ϵ����
	 * @param array $attentioner ��ע�ҵ���uid�б�
	 * @param array $data ����
	 * @return array
	 * @access private
	 */
	function _getRelationsData($attentioner,$data){
		$relationsData = array();
		foreach($attentioner as $key => $value){
			$data['uid'] = $value;
			$relationsData[] = $data;
		}
		return $relationsData;
	}

	function getWeibosByType($type, $page = 1, $perpage = 10) {
		if (!isset($this->_map[$type])) {
			return array();
		}
		$typeId = $this->_map[$type];
		$contentDao = L::loadDB('weibo_content','sns');
		$weibos = $contentDao->getWeibosByType($typeId, ($page - 1) * $perpage, $perpage);
		return $weibos;
	}
	
	function getWeibosByObjectIdsAndType($objectIds,$type){
		if (!isset($this->_map[$type]) || (!$this->_isLegalId($objectIds) && !is_array($objectIds))) {
			return array();
		}
		$type = $this->_map[$type];
		$contentDao = L::loadDB('weibo_content','sns');
		$weibos =  $contentDao->getWeibosByObjectIdsAndType($objectIds, $type);
		return is_array($objectIds) ? $weibos : current($weibos);
	}

	function getWeibosByMid($mids) {
		if (empty($mids) || (!is_numeric($mids) && !is_array($mids))) {
			return array();
		}
		if (perf::checkMemcache()){
			$_cacheService = Perf::gatherCache('pw_weibo_content');
			$array =  $_cacheService->getWeibosByMids($mids);			
		} else {
			$contentDao = L::loadDB('weibo_content','sns');
			$array = $contentDao->getWeibosByMid($mids);
		}
		return is_array($mids) ? $array : current($array);
	}
	
	/**
	 * ȡ��ȫվ����������������
	 * @param int $perpage ҳ��¼��
	 * @param int $page ҳ��
	 * @return array
	 * @access public
	 */
	function getWeibos($page = 1,$perpage =20){
		$contentDao = L::loadDB('weibo_content','sns');
		$weibos = $contentDao->getWeibos($page,$perpage);
		return $this->buildData($weibos,'uid');
	}
	

	/**
	 * ȡ��������ֱ��
	 */
	function getWeiboLives($num = 10){
		if (!$num) return false;
		$contentDao = L::loadDB('weibo_content','sns');
		$type = $this->_map;
		unset($type['transmit']);
		if (!$type || !is_array($type)) return false;
		$weibos = $contentDao->getWeibosByTypesAndNum($type, $num);
		return $this->buildData($weibos,'uid');
	}
	

	function getWeibosCount(){
		$contentDao = L::loadDB('weibo_content','sns');
		return $contentDao->getWeibosCount();
	}
	
	/**
	 * ȡ������������µ��û�
	 * @param int $perpage ҳ��¼��
	 * @return array
	 */
	function getWeiboAuthors($num, $exclude = array()) {
		$contentDao = L::loadDB('weibo_content','sns');
		return $contentDao->getWeiboAuthors($num, $exclude);
	}
	
	/**
	 * ȡ��7���ڱ�ת��������������
	 * @param int $num ��ȡ��¼����
	 * @return array
	 */
	function getAuthorSort($num) {
		$contentDao = L::loadDB('weibo_content','sns');
		if (!$user = $contentDao->getAuthorSort($num, $this->_timestamp - 604800)) {
			return array();
		}
		$userService = L::loadClass('UserService', 'user');
		$uinfo = $userService->getByUserIds($this->_getFieldOfRecords($user, 'uid'));
		$array = array();
		foreach ($user as $key => $value) {
			list($uinfo[$value['uid']]['icon']) = showfacedesign($uinfo[$value['uid']]['icon'], 1, 'm');
			$array[] = array(
				'uid' => $value['uid'],
				'username' => $uinfo[$value['uid']]['username'],
				'icon' => $uinfo[$value['uid']]['icon'],
				'counts' => $value['counts']
			);
		}
		return $array;
	}
	
	/**
	 * ȡ��n���ڵ�����������ת��
	 * @param int $topicId
	 * @return 
	 */
	function getHotTransmit($num){
		$num = $num ? intval($num) : 20; 
		if(!$num) return array();
		extract (pwCache::getData(D_P.'data/bbscache/o_config.php',false));
		$time = $this->_timestamp - ($o_weibo_hottransmitdays ? intval($o_weibo_hottransmitdays) * 86400 : 86400);
		$contentDao = L::loadDB('weibo_content','sns');
		$objectId = $contentDao -> getHotTransmit($num,$time);
		if(!$objectId) return array();
		$contentData = $contentDao -> getWeibosByMid($objectId);
		if(!$contentData) return array();
		$data = array();
		foreach($objectId as $key => $v){
			if(!$contentData[$v]){
				unset($key);
				continue; 
			}
			$data[] = $contentData[$v];
		}
		return $this->buildData($data,'uid');
	}
	
	/**
	 * ȡ��n���ڵ���������������
	 * @param int $topicId
	 * @return 
	 */
	function getHotComment($num){
		$num = $num ? intval($num) : 20; 
		if(!$num) return array();
		extract (pwCache::getData(D_P.'data/bbscache/o_config.php',false));
		$time = $this->_timestamp - ($o_weibo_hotcommentdays ? intval($o_weibo_hotcommentdays) * 86400 : 86400);
		$contentDao = L::loadClass('comment','sns');
		$objectIds = $contentDao -> getHotComment($num,$time);
		if(!$objectIds) return array();
		$contentDao = L::loadDB('weibo_content','sns');
		$commentData = $contentDao -> getWeibosByMid($objectIds);
		if(!$commentData) return array();
		$data = array();
		foreach($objectIds as $key => $v){
			if(!$commentData[$v]){
				unset($key);
				continue; 
			}
			$data[] = $commentData[$v];
		}
		return $this->buildData($data,'uid');
	}
	
	/**
	 * ȡ���û����������б�
	 * @param int $uid �û�ID
	 * @param int $perpage ҳ��¼��
	 * @param int $page ҳ��
	 * @return array
	 * @access public
	 */
	function getUserWeibos($uid,$page = 1,$perpage = 20){
		if(!$this->_isLegalId($uid)){
			return array();
		}
		$contentDao = L::loadDB('weibo_content','sns');
		$userWeibos = $contentDao->getUserWeibos($uid,$page,$perpage);
		return $this->buildData($userWeibos, 'uid');
	}
	
	function getUserWeibosCount($uid){
		if(!$this->_isLegalId($uid)){
			return 0;
		}
		$contentDao = L::loadDB('weibo_content','sns');
		return $contentDao->getUserWeibosCount($uid);
	}
	
	function getUserAttentionWeibosCount($uid,$filter=array()) {
		if (!$this->_isLegalId($uid)) {
			return 0;
		}
		if (($sqlArr = $this->_filterSql($uid, $filter)) === false) {
			return 0;
		}
		$contentDao = L::loadDB('weibo_content','sns');/* @var $contentDao PW_Weibo_ContentDB */
		return $contentDao->getUserAttentionWeibosCount($uid, $sqlArr);
	}
	
	/**
	 * ȡ���û���ע��������
	 * @param int $uid �û�ID
	 * @param array $filter �û���������
	 * @param int $perpage ҳ��¼��
	 * @param int $page ҳ��
	 * @return array
	 * @access public
	 */
	function getUserAttentionWeibos($uid,$filter = array(),$page = 1,$perpage = 20) {
		if (!$this->_isLegalId($uid)) {
			return array();
		}
		if (($sqlArr = $this->_filterSql($uid, $filter)) === false) {
			return array();
		}
		$contentDao = L::loadDB('weibo_content','sns');
		$attention = $contentDao->getUserAttentionWeibos($uid, $sqlArr, $page, $perpage);
		return $this->buildData($attention, 'authorid');
	}

	function _filterSql($uid, $filter) {
		if (empty($filter)) {
			return array();
		}
		if (empty($filter['relation']) || empty($filter['contenttype'])) {
			return false;
		}
		$array = array_merge($this->_relationSql($uid, $filter['relation']), $this->_sourceSql($filter['source']));
		if (count($filter['contenttype']) == 1) {
			$array['contenttype'] = isset($filter['contenttype']['string']) ? 0 : 1;
		}
		return $array;
	}

	function arrayOp($array1, $array2, $op) {
		return $op ? array_merge($array1, $array2) : array_diff($array1, $array2);
	}

	function _relationSql($uid, $relation) {
		if (!is_array($relation) || count($relation) >= 3) {
			return array();
		}
		$array = array();
		if ($relation['friend'] != $relation['attention']) {
			$friendDao = L::loadDB('friend', 'friend');
			$uArr = $this->_getFieldOfRecords($friendDao->getFriendsByUid($uid), 'friendid');
			if ($relation['friend']) {
				$array['uidsIn'] = $this->arrayOp($uArr, array($uid), $relation['self']);
			} else {
				$array['uidsNotIn'] = $this->arrayOp($uArr, array($uid), !$relation['self']);
			}
		} else {
			$array[$relation['self'] ? 'uidIn' : 'uidNotIn'] = $uid;
		}
		return $array;
	}
	
	function _sourceSql($source) {
		$source = $source ? $source : array();
		if (!is_array($source)) {
			return array();
		}
		$array = array(0, 1, 2);
		$map = $this->_compositeMap();
		if (count($source) >= (count($map) - 5)) return array();
		
		foreach ($source as $key => $value) {
			if (is_array($map[$key])) {
				$array = array_merge($array, array_values($map[$key]));
			} else {
				$array[] = $map[$key];
			}
		}
		return array('source' => $array);
	}
	
	function getUserAttentionWeibosNotMe($uid,$page = 1,$perpage = 20){
		if (!$this->_isLegalId($uid) || !$this->_isLegalId($page) || !$this->_isLegalId($perpage)) {
			return array();
		}
		$contentDao = L::loadDB('weibo_content','sns');
		$attention = $contentDao->getUserAttentionWeibosNotMe($uid,$page,$perpage);
		return $this->buildData($attention, 'authorid');
	}
	
	function getUserAttentionWeibosNotMeCount($uid){
		if(!$this->_isLegalId($uid)){
			return 0;
		}
		$contentDao = L::loadDB('weibo_content','sns');
		return $contentDao->getUserAttentionWeibosNotMeCount($uid);
	}

	function getPrevWeiboByType($uid, $type, $time = 30) {
		$contentDao = L::loadDB('weibo_content','sns');
		return $contentDao->getPrevWeiboByType($uid, $this->getTypeKey($type), ($this->_timestamp - $time));
	}

	/**
	 * ����չʾ������������
	 * @param array $data ����������
	 * @param string $field �û�id�ֶ�����
	 * return array
	 */
	function buildData($data, $field = 'uid') {
		$uids = $tids = $tArr = array();
		foreach ($data as $key => $value) {
			$uids[] = $value[$field];
			$type = $this->getType($value['type']);
			if ($type == 'transmit' && $value['objectid']) {
				$tids[] = $value['objectid'];
			}
			$data[$key]['content'] = strip_tags($value['content'],'<a>');
		}
		if ($tids) {
			$tArr = $this->getWeibosByMid($tids);
			$uids = array_merge($uids, $this->_getFieldOfRecords($tArr, 'uid'));
		}
		
		$uinfo = $this->_getUserInfo($uids);
		
		/* platform weibo app */
		$siteBindService = L::loadClass('WeiboSiteBindService', 'sns/weibotoplatform/service'); /* @var $siteBindService PW_WeiboSiteBindService */
		if ($siteBindService->isOpen()) {
			$userBindService = L::loadClass('WeiboUserBindService', 'sns/weibotoplatform/service'); /* @var $userBindService PW_WeiboUserBindService */
			$usersBindInfo = $userBindService->getUsersLocalBindInfo(array_keys($uinfo));
		}
		foreach ($data as $key => $value) {
			$value = $this->formatRecord($value, $uinfo[$value[$field]]['groupid']);
			$type = $this->getType($value['type']);
			if ($type == 'transmit' && ($transmit = $tArr[$value['objectid']])) {
				$value['transmits'] = array_merge((array)$this->formatRecord($transmit, $uinfo[$transmit['uid']]['groupid']), (array)$uinfo[$transmit['uid']]);
			}
			!is_array($uinfo[$value[$field]]) && $uinfo[$value[$field]] = array();
			$data[$key] = array_merge((array)$value, $uinfo[$value[$field]]);
			
			/* platform weibo app */
			if ($siteBindService->isOpen() && $siteBindService->isBind($type)) {
				$data[$key]['bindUserInfo'] = $usersBindInfo[$type][$value[$field]]['info'];
				$data[$key]['bindSiteInfo'] = $siteBindService->getBindType($type);
				$data[$key]['bindUserInfo']['url'] = $data[$key]['bindSiteInfo']['uidUrlPrefix'] . $data[$key]['bindUserInfo']['id'];
				if (isset($data[$key]['extra']['sinaPhotos'])) $data[$key]['extra']['photos'] = $data[$key]['extra']['sinaPhotos']; //for compatible
			}
		}
		return $data;
	}

	function formatRecord($value, $gid) {
		list($value['lastdate'], $value['postdate_s']) = getLastDate($value['postdate']);
		$value['extra'] = $value['extra'] ? unserialize($value['extra']) : array();
		!$value['authorid'] && $value['authorid'] = $value['uid'];
		if ($gid == '6') {
			if (isset($value['extra']['title'])) {
				$value['extra']['title'] = "<span style=\"color:black;background-color:#ffff66\">�������ѱ�����Ա���Σ�</span>";
				$value['content'] = '';
			} else {
				$value['content'] = "<span style=\"color:black;background-color:#ffff66\">�������ѱ�����Ա���Σ�</span>";
			}
			isset($value['extra']['photos']) && $value['extra']['photos'] = array();
		} else {
			$value['content'] = $this->parseContent($value['content'], $value['extra']);
		}
		return $value;
	}
	
	/**
	 * ��������
	 * @param string $content ����������
	 * @param array $extra ��չ��Ϣ
	 * return string
	 */
	function parseContent($content, &$extra) {
		global $topic;
		$this->_hasVideo = array();
		$content = $this->_parseLink($content);
		if ($this->_hasVideo) {
			$extra['_hasVideo'] = $this->_hasVideo;
		}
		if ($extra['refer']) {
			$uArray  = array_flip($extra['refer']);
			$content = preg_replace('/@([^\\&\'"\/\*,<>\r\t\n\s#%?@:��]+)(?=\s?)/ie', "\$this->_parseRefer('\\1', \$uArray)", $content);
		}
		if ($extra['topics']) {
			$content = pwHtmlspecialchars_decode($content,false);
			if(preg_match('/^#\s+#$/', $content)) return $content;
			$content = preg_replace_callback('/#([^@&#!*\(\)]+)#/U',array(&$this,'_callback_add_topic_url'),$content);
		}
		if (strpos($content,'[s:') !== false && strpos($content,']') !== false) {
			$content = $this->_parseSmile($content);
		}
		
		if ($topic && !$extra['topics']) {
			$content = strip_tags($content);
			$content = preg_replace('/' . preg_quote($topic,'/') . '/i', "<span class='s2'>$topic</span>", $content);
		}
		return $content;
	}
	
	function _callback_add_topic_url($matches){
		//global $topic;
		//if ($topic) $matches[0] = preg_replace('~' . preg_quote($topic) . '~i', "<span class='s2'>$topic</span>", $matches[0]);
		$pattern = "/(https?|ftp|gopher|news|telnet|mms|rtsp):\/\/[a-z0-9\/\-_+=.~!%@?%&;:$\\��\|]+(#.+)?/ie";
		if (preg_match($pattern, $matches[1])){
			return $matches[0];
		}
		return '<a href="apps.php?q=weibo&do=topics&topic=' . urlencode(strip_tags($matches[1],'<span>')) . '">' . strip_tags($matches[0],'<span>') . '</a>';
	}
	
	/**
	 * �������������ݵ����ӵ�ַ
	 * @param string $content
	 * @param int $mid
	 * return string
	 */
	function _parseLink($content) {
		if (strpos($content,'[/URL]') !== false || strpos($content,'[/url]') !== false) {
			$content = preg_replace("/\[url=([^\[]+?)\](.*?)\[\/url\]/is","<a href=\"\\1\" target=\"_blank\">\\2</a>", $content);
		}
		//return preg_replace("/(?<!\shref=['\"])((https?|ftp|gopher|news|telnet|mms|rtsp):\/\/[a-z0-9\/\-_+=.~!%@?#%&;:$\\��\|]+)/ie", "\$this->_parseLinkContent('\\1')", $content);
		return preg_replace("/(?<!\shref=['\"])((https?|ftp|gopher|news|telnet|mms|rtsp):\/\/[a-z0-9\/\-_+=.~!%@?%&;:$\\��\|]+(#.+)?)/ie", "\$this->_parseLinkContent('\\1')", $content);
	}
	
	/**
	 * ������ҳ����Ƶ�����֡�flash������
	 */
	function _parseLinkContent($url) {
		if ($return = $this->_parseVideo($url)) {
			return $return;
		}
		if (preg_match("/\.(mp3|wma)\??.*$/i", $url)) {
			return $this->_parseMusic($url);
		}
		return $this->_parseWebUrl($url);
	}
	
	/**
	 * �������������ݵ�flash��Ƶ
	 * @param string $url
	 * @param int $mid
	 * return string
	 */
	function _parseVideo($url) {
		static $sNum = 0;
		if (!($videoAddr = $this->_parseVideoWebSiteAddr($url)) && preg_match("/\.swf\??.*$/i", $url)) {
			$videoAddr = $url;
		}
		if ($videoAddr) {
			empty($this->_hasVideo) && $this->_hasVideo = array(++$sNum, $videoAddr);
			return "<img src=\"u/images/share_s.png\" width=\"16\" class=\"mr5\" style=\"vertical-align:middle;\" /><a class=\"cp\" onclick=\"mediaPlayer.showVideo('$videoAddr','$sNum');return false;\">$url</a>";
		}
		return false;
	}
	
	/**
	 * ����������Ƶ��վ�����ӵ�ַ
	 * @param string $url
	 * return string
	 */
	function _parseVideoWebSiteAddr($url) {
		if (!preg_match("/(youku.com|youtube.com|sohu.com|sina.com.cn)/i", $url, $hosts)) {
			return false;
		}
		$videoRules = array(
			'youku.com'		=> '/v_show\/id_([\w=]+)\.html/',
			'youtube.com'	=> '/v\=([\w\-]+)/',
			'sina.com.cn'	=> '/\/(\d+)-(\d+)\.html/',
			'sohu.com'		=> '/\/(\d+)\/*$/'
		);
		if (isset($videoRules[$hosts[1]]) && preg_match($videoRules[$hosts[1]], $url, $matches)) {
			return $this->_getVideoWebSiteAddr($hosts[1], $matches[1]);
		}
		return false;
	}
	
	/**
	 * ��ȡ������Ƶ��վ��flash��ʵ���ӵ�ַ
	 * @param string $hosts
	 * @param string $hash
	 * return string
	 */
	function _getVideoWebSiteAddr($hosts, $hash) {
		switch ($hosts) {
			case 'youku.com':
				$videoAddr = 'http://player.youku.com/player.php/sid/' . $hash . '=/v.swf';break;
			case 'youtube.com':
				$videoAddr = 'http://www.youtube.com/v/' . $hash;break;
			case 'sina.com.cn':
				$videoAddr = 'http://vhead.blog.sina.com.cn/player/outer_player.swf?vid=' . $hash;break;
			case 'sohu.com':
				$videoAddr = 'http://v.blog.sohu.com/fo/v4/' . $hash;break;
			default:
				$videoAddr = false;
		}
		return $videoAddr;
	}
	
	/**
	 * ������������
	 * @param string $url
	 * return string
	 */
	function _parseMusic($url) {
		static $sNum = 0;
		$sNum++;
		return "<span><img title=\"����\" class=\"cp mr5\" src=\"u/images/music.png\" style=\"vertical-align:middle;\" onclick=\"mediaPlayer.showMusic('$url', '$sNum', this)\" /></span>";
	}

	/**
	 * ������ͨ����
	 * @param string $url
	 * return string
	 */
	function _parseWebUrl($url) {
		return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
	}

	/**
	 * ��������
	 */
	function _parseSmile($content) {
		$sParse = L::loadClass('smileparser', 'smile');
		return $sParse->parse($content);
	}

	/**
	 * ����������@����
	 * @param string $username �û���
	 * @param array @�б�
	 * return string
	 */
	function _parseRefer($username, $uArray) {
		return isset($uArray[$username]) ? '<a href="'.USER_URL. $uArray[$username] . '">@' . $username . '</a>' : '@' . $username;
	}

	/**
	 * ��ȡ�û���Ϣ
	 * @param array $uids �û�id����
	 * return array
	 */
	function _getUserInfo($uids) {
		if (empty($uids) || !is_array($uids)) {
			return array();
		}
		require_once(R_P . 'require/showimg.php');
		$newUsersInfo = array();

		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
		$users = $userService->getByUserIds($uids); //'m.uid','m.username','m.icon','m.groupid'
		foreach ($users as $key => $value) {
			list($value['icon']) = showfacedesign($value['icon'], 1, 's');
			$newUsersInfo[$value['uid']] = $value;
		}
		return $newUsersInfo;
	}

	/**
	 * ȡ��@�û���������
	 * @param int $uid �û�ID
	 * @param int $perpage ҳ��¼��
	 * @param int $page ҳ��
	 * @return array
	 * @access public
	 */
	function getRefersToMe($uid,$page = 1,$perpage = 20){
		if(!$this->_isLegalId($uid)){
			return 0;
		}
		$referDao = L::loadDB('weibo_referto','sns');
		$refers = $referDao->getRefersToMe($uid,$page,$perpage);
		return $this->buildData($refers, 'uid');
	}
	
	function getRefersToMeCount($uid){
		if(!$this->_isLegalId($uid)){
			return 0;
		}
		$referDao = L::loadDB('weibo_referto','sns');
		return $referDao->getRefersToMeCount($uid);
	}
	
	/**
	 * ȡ��Ⱥ��������������б�
	 * @param mixed $cyids Ⱥ��ID
	 * @param int $perpage ҳ��¼��
	 * @param int $page ҳ��
	 * @return array
	 * @access public
	 */
	function getConloysWeibos($cyids,$page = 1,$perpage = 20){
		if ($cyids == 'nocyids') {
			$referDao = L::loadDB('weibo_cnrelations','sns');
			$conloyWeibos = $referDao->getConloysWeibos('nocyids',$page,$perpage);
			return $this->buildData($conloyWeibos, 'uid');
		}
		$cyids = is_array($cyids) ? $cyids : array($cyids);
		if(empty($cyids)){
			return array();
		}
		$referDao = L::loadDB('weibo_cnrelations','sns');
		$conloyWeibos = $referDao->getConloysWeibos($cyids,$page,$perpage);
		return $this->buildData($conloyWeibos, 'uid');
	}
	
	function getConloysWeibosCount($cyids){
		$cyids = is_array($cyids) ? $cyids : array($cyids);
		if(empty($cyids)){
			return 0;
		}
		$referDao = L::loadDB('weibo_cnrelations','sns');
		return $referDao->getConloysWeibosCount($cyids);
	}
	/**
	 * ɾ��������
	 * @param int $mid ������ID
	 * @return int
	 */
	function deleteWeibos($mids){
		if (empty($mids)) {
			return false;
		}
		$mids = is_array($mids) ? $mids : array($mids);
		$contentDao = L::loadDB('weibo_content','sns');
		$relationsDao = L::loadDB('weibo_relations','sns');
		$referstDao = L::loadDB('weibo_referto','sns');
		$cnrelationsDao = L::loadDB('weibo_cnrelations','sns');
		$contentDao->deleteWeibosByMid($mids);
		$relationsDao->delRelationsByMid($mids);
		$referstDao->deleteRefersByMid($mids);	
		$cnrelationsDao->deleteCnrelationsByMid($mids);
		$topicDao = L::loadDB('topic','sns');
		//ɾ��΢����Ӧ������
		$commentService = L::loadClass("comment","sns"); /* @var $commentService PW_Comment */
		$commentService->unionDeleteCommentsByMid($mids);
		//ɾ���뻰�� �Ķ�Ӧ��ϵ
		$topicRelationsDao = L::loadDB('weibo_topicrelations','sns');
		foreach ($mids as $mid) {
			$topicIds = $topicRelationsDao->getTopicIdsByMid($mid);
			if(!$topicIds) continue;
			$topicRelationsDao->deleteRelationByMid($mid);
			$topicDao->decreaseTopicNum($topicIds);
		}
		return true;
	}
	
	/**
	 * ��������������
	 * @param array $data ��������
	 * @param int $mid ������ID
	 * @return int
	 */
	function update($data, $mid) {
		$mid = intval($mid);
		if ($mid < 1 || !is_array($data)) {
			return false;
		}
		$contentDao = L::loadDB('weibo_content','sns');
		$contentDao->update($data, $mid);
	}

	/**
	 * ����������ͳ����
	 * @param array $data ��������
	 * @param int $mid ������ID
	 * @return int
	 */
	function updateCountNum($data,$mid) {
		$mid = intval($mid);
		if ($mid < 1 || !is_array($data)) {
			return false;
		}
		$contentDao = L::loadDB('weibo_content','sns');
		$contentDao->updateCountNum($data, $mid);
	}
	
	function _isLegalId($id){
		return intval($id) > 0;
	}

	/**
	 * ��������mapͼ 
	 */
	function _typeMap(){
		$this->_map = array(
			'weibo' => 0,//������
			'transmit' => 1,//ת��
			'sendweibo' => 2, //���͵�������
			'cms' => 3, //����ģʽ
			'honor' => 4,
			'article' => 10, //����
			'diary' => 20,//��־
			'photos' => 30,//���
			'group_article' => 40,//Ⱥ�黰��
			'group_photos' => 41,//Ⱥ�����
			'group_active' => 42,//Ⱥ��
			'group_write' => 43,//Ⱥ���¼/����
			//NOTE please keep 50-59 for external weibo types
		);
		$this->_mapDescript = array(
			'weibo' => '������',
			'transmit' => 'ת��������',
			'sendweibo' => '���͵�������',
			'honor' => 'ǩ��',
			'article' => '����',
			'diary' => '��־',
			'photos' => '���',
			'group_article' => 'Ⱥ�黰��',
			'group_photos' => 'Ⱥ�����',
			'group_active' => 'Ⱥ��',
			'group_write' => 'Ⱥ���¼',
			'cms' => '����',
		);
		
		/* platform weibo app */
		$siteBindService = L::loadClass('WeiboSiteBindService', 'sns/weibotoplatform/service'); /* @var $siteBindService PW_WeiboSiteBindService */
		if ($siteBindService->isOpen()) {
			foreach ($siteBindService->getBindTypes() as $key => $config) {
				$this->_map[$key] = $config['typeId'];
				$this->_mapDescript[$key] = $config['title'];
			}
		}
		
		$this->_mapflip = array_flip($this->_map);
	}
	
	function getTypeDescript($type){
		$type = $this->getType($type);
		return $this->_mapDescript[$type];
	}
	
	function getValueMapDescript(){
		$tmpMap = array();
		foreach($this->_map as $key => $value){
			$tmpMap[$value] = $this->_mapDescript[$key];
		}
		return $tmpMap;
	}

	function _privacyMapping($type){
		list($tmp) = explode('_', $type);
		return $tmp;
	}
	
	function _compositeMap() {
		$map = array();
		foreach ($this->_map as $key => $value) {
			$tmp = explode('_', $key);
			if (count($tmp) > 1) {
				$map[$tmp[0]][$tmp[1]] = $value;
			} else {
				$map[$key] = $value;
			}
		}
		return $map;
	}
	
	function getTypeKey($type) {
		return isset($this->_map[$type]) ? $this->_map[$type] : 0;
	}
	/**
	 * ȡ������������
	 */
	function getType($type) {
		return isset($this->_mapflip[$type]) ? $this->_mapflip[$type] : 'weibo';
	}

	/**
	 * ��ȡ������չʾ���ͣ���Щ���������Ϳ���ͬһչʾģ�棩
	 */
	function getViewType($type) {
		$weiboType = $this->getType($type);
		
		/* platform weibo app */
		$siteBindService = L::loadClass('WeiboSiteBindService', 'sns/weibotoplatform/service'); /* @var $siteBindService PW_WeiboSiteBindService */
		if ($siteBindService->isBind($weiboType)) return 'bindweibo';

		return $weiboType;
	}
	
	function adminSearch($usernames,$contents,$startDate,$endDate,$type = 0 ,$orderby = 'desc',$page = 1,$perpage = 20){
		if($usernames){
			$usernames = is_array($usernames) ? $usernames : array($usernames);
		}
		$uids = array();
		if(is_array($usernames) && count($usernames) > 0){
			$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
			$users = $userService->getByUserNames($usernames);
			$uids = $this->_getFieldOfRecords($users,'uid');
		}
		$startDate && !is_numeric($startDate) && $startDate = PwStrtoTime($startDate);
		$endDate && !is_numeric($endDate) && $endDate = PwStrtoTime($endDate);
		$type = intval($type);
		$contentDao = L::loadDB('weibo_content','sns');
		$result = $contentDao->adminSearch($uids,$contents,$startDate,$endDate,$type,$orderby,$page,$perpage);
		foreach($result[1] as $key => $value){
			$result[1][$key]['content'] = substr(stripWindCode($value['content']),0,30);
		}
		$weibos = $this->buildData($result[1],'uid');
		
		return array($result[0],$weibos);
	}
	
	/**
	 * ��̨��Աɾ���������  ---ɾ��΢��
	 * 
	 * @param $Uids
	 */
	function deleteWeibosByUids($uids){
		if(!$uids || !is_array($uids)) return false;
		$mids = array();
		$midTems  = $this->findMidsByUids($uids);
		foreach($midTems as $mid) {
			$mids[] = $mid['mid'];
		}
		return $this->deleteWeibos($mids);
	}

	function deleteWeibosByObjectIdsAndType($objectIds, $type) {
		if (!isset($this->_map[$type]) || (!$this->_isLegalId($objectIds) && !is_array($objectIds))) {
			return array();
		}
		$type = $this->_map[$type];
		$mids = $tempMids = array();
		$contentDao = L::loadDB('weibo_content','sns');
		$tempMids = $contentDao->getMidsByObjectIdsAndType($objectIds, $type);
		foreach ($tempMids as $mid) {
			$mids[] = $mid['mid'];
		}
		if (!$mids) return false;
		return $this->deleteWeibos($mids);
	}
	
	function findMidsByUids($uids) {
		if(!$uids || !is_array($uids)) return false;
		$contentDao = L::loadDB('weibo_content','sns'); /* @var $contentDao PW_Weibo_ContentDB */
		return $contentDao->findMidsByUids($uids);
	}

}
?>
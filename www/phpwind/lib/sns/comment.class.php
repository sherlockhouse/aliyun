<?php
!defined('P_W') && exit('Forbidden');
/**
 * ����������SERVICE
 * @package PW_Comment
 * @author  suqian && sky_hold@163.com
 * @access  public
 */
class PW_Comment {

	var $_timestamp = 0;
	var $cid = 0;

	function __construct(){
		global $timestamp;
		$this->_timestamp = $timestamp;
	}

	function PW_Comment(){
		$this->__construct();
	}
	
	/**
	 *��������
	 *@param int $uid �û�ID
	 *@param int $mid ��ϢID
	 *@param string $content ������
	 *@param array $extra ��չ�ֶ�
	 *@return boolean �Ƿ��ͳɹ�
	 *@access public
	 */
	function comment($uid,$mid,$content,$extra = array()){
		if (!$this->_isLegalId($uid)  || !$this->_isLegalId($mid)  || empty($content)) {
			return 0;
		}
		$weiboService = L::loadClass('weibo', 'sns'); /* @var $weiboService PW_Weibo */
		$weibos = $weiboService->getWeibosByMid($mid);
		if (empty($weibos)) {
			return 0;
		}
		$content = $this->escapeStr($content);
		$extra = array_merge((array)$extra, $this->_analyseContent($uid, $content));
		/*$ruid = array($weibos['uid']);
		if ($extra['refer']) {
			$ruid = array_merge($ruid,array_keys($extra['refer']));
		}*/
		$ruid = $extra['refer'] ? array_keys($extra['refer']) : array($weibos['uid']);
		$blacklist = $this->_actionBlackList($uid,$ruid,$extra);
		if (empty($ruid) || in_array($weibos['uid'],$blacklist)) {
			return 0;
		}
		$comment = array();
		$comment['uid'] = $uid;
		$comment['mid'] = $mid;
		$comment['content'] = $content;
		$comment['postdate'] = $this->_timestamp;
		$comment['extra'] = $extra ? serialize($extra) : '';
		$commentDao = L::loadDB('weibo_comment','sns');
		$cid = $commentDao->insert($comment);
		if (empty($cid)) {
			return 0;
		}
		$this->cid = $cid;
		$data = $this->_getCmRelationsData($cid,$ruid);
		$this->_addCmRelations($data);
		$userService = L::loadClass('UserService', 'user');
		if($uid !== $weibos['uid']) $userService->updatesByIncrement($ruid, array(), array('newcomment' => 1));

		//platform weibo app
		$siteBindService = L::loadClass('WeiboSiteBindService', 'sns/weibotoplatform/service'); /* @var $siteBindService PW_WeiboSiteBindService */
		if ($siteBindService->isOpen() && !$extra['noSync']) {
			$userBindService = L::loadClass('WeiboUserBindService', 'sns/weibotoplatform/service'); /* @var $userBindService PW_WeiboUserBindService */
			if ($userBindService->isBindOne($uid)) {
				unset($comment['extra']);
				$syncer = L::loadClass('WeiboSyncer', 'sns/weibotoplatform'); /* @var $syncer PW_WeiboSyncer */
				$syncer->sendComment($cid, $comment);
			}
		}
		
		return $cid;
	}
	
	/**
	 * ����������
	 *@param int $uid ������ID
	 *@param array $ruid �����ҵ��û�ID
	 *@param array $extra ����������@�ҵĹ���
	 *@return array ����������Ϊ���������û�
	 *@access private
	 */
	function _actionBlackList($uid,&$ruid,&$extra){
		$ruid = array_unique($ruid);
		$attentionService = L::loadClass('Attention', 'friend');/* @var $attentionService PW_Attention */
		$blackList = $attentionService->getBlackListToMe($uid, $ruid);
		foreach($ruid as $key => $value){
			if(in_array($value,$blackList)){
				unset($ruid[$key]);
				if($extra['refer'][$value]){
					unset($extra['refer'][$value]);
				}
			}
		}
		return $blackList;
	}
	
	/**
	 * ���۷�����֤
	 * @param string $content ��֤����
	 */
	function commentCheck($content) {
		if ($GLOBALS['groupid'] == '6') return '���ѱ�����!';
		$content = $this->escapeStr($content);
		if (!$content) return '�������ݲ�Ϊ��';
		if (strlen($content) > 255) return '�������ݲ��ܶ���255�ֽ�';
		$filterService = L::loadClass('FilterUtil', 'filter');
		if (($GLOBALS['banword'] = $filterService->comprise($content)) !== false) {
			return 'content_wordsfb';
		}
		return true;
	}
	
	function escapeStr($str) {
		if (!$str = trim($str)) return '';
		return preg_replace('/(&nbsp;){1,}/', ' ', $str);
	}
	
	/**
	 * ȡ�������¶�Ӧ�������б�
	 * @param int $mid ��ϢID
	 * @param int $perpage ҳ��¼��
	 * @param int $page ҳ��
	 * @return array
	 * @access public
	 */
	function getCommentsByMid($mid,$page = 1,$perpage = 20){
		$commentDao = L::loadDB('weibo_comment','sns');
		$comments = $commentDao->getCommentsByMid($mid,$page,$perpage);
		return $this->_buildData($comments,false);
	}
	
	function getCommentsCountByMid($mid){
		$commentDao = L::loadDB('weibo_comment','sns');
		return $commentDao->getCommentsCountByMid($mid);
	}
	
	/**
	 * ȡ���û��յ�������
	 * @param int $uid �û���ID
	 * @param int $perpage ҳ��¼��
	 * @param int $page ҳ��
	 * @return array
	 * @access public
	 */
	function getUserReceiveComments($uid,$page = 1,$perpage = 20){
		$commentDao = L::loadDB('weibo_comment','sns');
		$comments = $commentDao->getUserReceiveComments($uid,$page,$perpage);
		return $this->_buildData($comments,true);
	}
	
	function getUserReceiveCommentsCount($uid){
		$commentDao = L::loadDB('weibo_comment','sns');
		return $comments = $commentDao->getUserReceiveCommentsCount($uid);
	}
	
	/**
	 *  ȡ���û����������۵Ļظ�
	 *@param int $uid  �û�UID
	 *@param int $mid  ������ID
	 *@param int $cuid �ظ���UID
	 *@param int $page
	 *@param int $perpage
	 *@return array
	 */
	function getUserCommentOfRelpays($uid,$mid,$cuid,$page = 1,$perpage = 20 ){
		$commentDao = L::loadDB('weibo_comment','sns');
		$comments = $commentDao->getUserCommentOfRelpays($uid,$mid,$cuid,$page,$perpage);
		return $this->_buildData($comments,false);
	}
	
	function getUserCommentOfRelpaysCount($uid,$mid,$cuid){
		$commentDao = L::loadDB('weibo_comment','sns');
		return $commentDao->getUserCommentOfRelpaysCount($uid,$mid,$cuid);
	}
	/**
	 * ȡ���û����͵�����
	 * @param int $uid �û���ID
	 * @param int $perpage ҳ��¼��
	 * @param int $page ҳ��
	 * @return array
	 * @access public
	 */
	function getUserSendComments($uid,$page = 1,$perpage = 20){
		$commentDao = L::loadDB('weibo_comment','sns');
		$comments = $commentDao->getUserSendComments($uid,$page,$perpage);
		return $this->_buildData($comments,true);
	}
	
	/**
	 * ɾ������
	 * @param array $cid ����ID
	 * @return boolean
	 */
	function deleteComment($cids){
		$commentDao = L::loadDB('weibo_comment','sns');
		$commentDao->deleteCommentByCids($cids);
		$cmrelationsDao = L::loadDB('weibo_cmrelations','sns');
		$cmrelationsDao->deleteCmRelationsByCids($cids);
		return true;
	}
	
	function checkCommentAuthor($cid,$uid=0){
		$cid = intval($cid);
		$uid = intval($uid);
		$uid < 1 && $uid = $GLOBALS['winduid'];
		$commentDao = L::loadDB('weibo_comment','sns');
		$comment = $commentDao->get($cid);
		if (!$comment) return false;
		return $comment['uid'] == $uid;
	}
	/**
	 * ɾ��ĳ�������������е�����
	 * @param int $mid ������ID
	 * @return boolean
	 */
	function unionDeleteCommentsByMid($mids){
		if(empty($mids)){
			return false;
		}
		$mids = is_array($mids) ? $mids : array($mids);
		$commentDao = L::loadDB('weibo_comment','sns');
		if ($GLOBALS['db']->server_info() > '4') {
			return $commentDao->unionDeleteCommentsByMid($mids);
		}
		$cids = $commentDao->getCidsOfCommentsByMid($mids);
		return $this->deleteComment($cids);
	}
	
	/**
	 * ���ĳ�û���Ӧ�������µ����۹�ϵ
	 * @param int $uid �û�ID
	 * @param int $cid ����ID
	 * @return boolean
	 */
	function removeCmRelation($uid,$cid){
		$cmrelationsDao = L::loadDB('weibo_cmrelations','sns');
		return $cmrelationsDao->removeCmRelation($uid,$cid);
	}
	
	function _getCmRelationsData($cid,$ruid = array()){
		$data = $tmp = array();
		foreach($ruid as  $value){
			$tmp['uid'] = $value;
			$tmp['cid'] = $cid;
			$data[] = $tmp;
		}
		return $data;
	}
	
	function _addCmRelations($data){
		if(!is_array($data) || empty($data)){
			return array();
		}
		$cmrelationsDao = L::loadDB('weibo_cmrelations','sns');
		return $cmrelationsDao->addCmRelations($data);
	}
	
	function _buildData($data,$ifweibo){
		$uids = $mids = $uinfo = $winfo = array();
		foreach ($data as $key => $value) {
			if (!$value['uid']) continue;
			$uids[] = $value['uid'];
			$mids[] = $value['mid'];
		}
		$uinfo = $this->_getUsersInfo($uids);
		if($ifweibo){
			$winfo = $this->_getWeiBosInfo($mids);
		}
		foreach($data as $key => $value){
			if (!$value['uid']) continue;
			$value = $this->_formatRecord($value, $uinfo[$value['uid']]['groupid']);
			if($winfo){
				$value['weibo'] = $winfo[$value['mid']];
			}
			$uinfo[$value['uid']] = $uinfo[$value['uid']] ? $uinfo[$value['uid']] : array();
			$data[$key] = array_merge($value, $uinfo[$value['uid']]);
		}
		return $data;
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
	 * ��ȡ�û���Ϣ
	 * @param array  $uids  �û���������
	 * @param string $type ���
	 * return array
	 */
	function _getUsersInfo($uids,$type = 'uid') {
		if (empty($uids) || !is_array($uids)) {
			return array();
		}
		$newUsersInfo = $users =  array();
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
		if($type == 'uid'){
			$users = $userService->getByUserIds($uids); //'m.uid','m.username','m.icon','m.groupid'
		}elseif($type == 'username'){
			$users = $userService->getByUserNames($uids);
		}
		foreach ($users as $key => $value) {
			list($value['icon']) = showfacedesign($value['icon'], 1, 'm');
			$newUsersInfo[$value['uid']] = $value;
		}
		return $newUsersInfo;
	}
	/**
	 * ��ȡ��������Ϣ
	 * @param array $mids �û�id����
	 * return array
	 */
	function _getWeiBosInfo($mids){
		if (empty($mids) || !is_array($mids)) {
			return array();
		}
		$weiboService = L::loadClass('weibo', 'sns'); /* @var $weiboService PW_Weibo */
		$weibos = $weiboService->getWeibosByMid($mids);
		return $weiboService->buildData($weibos,'uid');
		
	}
	
	/**
	 * ȡ��n�������۴�������������Id
	 * @param int $num ��ȡ��¼����
	 * @return array
	 */
	function getHotComment($num,$time){
		$time = intval($time);
		$num = intval($num);
		if($time < 0 || $num < 0) return array();
		$commentDao = L::loadDB('weibo_comment','sns');
		return $commentDao->getHotComment($num=20,$time);
	}
	
	/**
	 * ��������
	 * @param string $content ��������
	 * @param array $extra ��չ��Ϣ
	 * return string
	 */
	function _parseContent($content, &$extra) {
	
		if ($extra['refer']) {
			$uArray  = array_flip($extra['refer']);
			$content = preg_replace('/@([^\\&\'"\/\*,<>\r\t\n\s#%?@:��]+)(?=\s?)/ie', "\$this->_parseRefer('\\1', \$uArray)", $content);
		}
		/*
		if ($extra['upload']) {
			$content = preg_replace('/\[upload=(\d+)\]/ie', "\$this->_parseUpload('\\1', \$extra['upload'])", $content);
		}
		*/
		if (strpos($content,'[s:') !== false && strpos($content,']') !== false) {
			$content = $this->_parseSmile($content);
		}
		return $content;
	}
	
	/**
	 * ��������
	 */
	function _parseSmile($content) {
		$sParse = L::loadClass('smileparser', 'smile');
		return $sParse->parse($content);
	}
	

	/**
	 * ����������@����(����)
	 * @param string $username �û���
	 * @param array @�б�
	 * return string
	 */
	function _parseRefer($username, $uArray) {
		return isset($uArray[$username]) ? '<a href="'.USER_URL. $uArray[$username] . '">@' . $username . '</a>' : '@' . $username;
	}
	
	/**
	 * �������������е������ǩ
	 * @param int $uid ������
	 * @param string $content ����������
	 * @return array
	 */
	function _analyseContent($uid, $content) {
		$array = array();
		if ($refer = $this->_analyseRefer($uid,$content)) {
			$array['refer'] = $refer;
		}
		return $array;
	}
	
	/**
	 * �������������е�@����
	 * @param int $uid ���۷�����
	 * @param string $content ����������
	 * @return array
	 */
	function _analyseRefer($uid,$content) {
		preg_match_all('/@([^\\&\'"\/\*,<>\r\t\n\s#%?@:��]+)\s?/i', $content, $matchs);
		$refer = array();
		if ($matchs[1]) {
			$uInfo = $this->_getUsersInfo($matchs[1],'username');
			foreach ($uInfo as $rt) {
				$refer[$rt['uid']] = $rt['username'];
			}
		}
		return $refer;
	}
	
	function _formatRecord($record, $gid){
		list($record['lastdate'], $record['postdate_s']) = getLastDate($record['postdate']);
		$record['extra'] = $record['extra'] ? unserialize($record['extra']) : array();
		if ($gid == '6') {
			$record['content'] = "<span style=\"color:black;background-color:#ffff66\">�������ѱ�����Ա���Σ�</span>";
		} else {
			$record['content'] = $this->_parseContent($record['content'], $record['extra']);
		}
		return $record;
	}

	function _isLegalId($id){
		return intval($id) > 0;
	}
	
	function adminSearch($usernames,$contents,$startDate,$endDate,$orderby = 'desc',$page = 1,$perpage = 20){
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
		$commentDao = L::loadDB('weibo_comment','sns');
		$result = $commentDao->adminSearch($uids,$contents,$startDate,$endDate,$orderby,$page,$perpage);
		foreach($result[1] as $key => $value){
			$result[1][$key]['content'] = substr(stripWindCode($value['content']),0,30);
		}
		$weibos = $this->_buildData($result[1]);
		return array($result[0],$weibos);
	}
}
?>
<?php
!defined('P_W') && exit('Forbidden');

class AlipayPush {
	var $sitehash;
	function AlipayPush() {
		global $db_sitehash,$winduid;
		$this->db		= $GLOBALS['db'];
		$this->sitehash	= $db_sitehash;
		$this->winduid	= $winduid;
	}

	/**
	 * ����AA���
	 * @param int $tid ����id
	 * @param int $uid ������id
	 * @param int $actmid ��������id
	 * @param string $subject ���ӱ���
	 * @return string T
	 * @access private
	 */
	function create_aa_payment($tid,$uid,$actmid = 0,$subject) {
		global $db_bbsname;

		require_once(R_P.'lib/activity/alipay.php');
		$AlipayInterface = new AlipayInterface('create_aa_payment');

		$out_biz_no = $this->sitehash.'_'.$tid.'_'.$this->generatestr(6);

		!$uid && $uid = $this->winduid;

		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
		$userInfo = $userService->get($uid, false, false, true);
		$tradeinfo = $userInfo['tradeinfo'];
		
		$tradeinfo = unserialize($tradeinfo);
		$user_id = $tradeinfo['user_id'];

		$param = array(
			/* ҵ����� */
			'out_biz_no'	=> $out_biz_no,
			'subject'		=> $subject,
			'detail'		=> '',
			'seller_id'		=> $user_id,
			'out_forum_no'	=> $this->sitehash,
			'out_forum_name'=> $db_bbsname,
		);

		require_once(R_P.'require/posthost.php');
		$returnResult = PostHost($AlipayInterface->alipayurl($param),'', 'POST');//��ȡXMLֵ

		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser,$returnResult,$arr_vals);
		xml_parser_free($xml_parser);

		foreach ($arr_vals as $value) {
			if ($value['tag'] == 'ERROR') {
				$error = $value['value'];
			} elseif ($value['tag'] == 'IS_SUCCESS') {
				$is_success = $value['value'];
			} elseif ($value['tag'] == 'BATCH_NO') {
				$batch_no = $value['value'];
			}
		}

		if ($error == 'AA_FAIL_TO_CREATE_AA_NEED_CERTIFY') {//δʵ����֤
			$tradeinfo['iscertified'] = 'F';
			$tradeinfo = addslashes(serialize($tradeinfo));
			
			$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
			$userService->update($uid, array(), array(), array('tradeinfo' => $tradeinfo));
			
			$defaultValueTableName = getActivityValueTableNameByActmid();
			$this->db->update("UPDATE $defaultValueTableName SET iscertified=0 WHERE tid=".S::sqlEscape($tid));//����ʵ����֤״̬
		} elseif ($is_success == 'T' && $batch_no) {
			$defaultValueTableName = getActivityValueTableNameByActmid();
			$sqlarray = array(
				'out_biz_no'	=> $out_biz_no,
				'batch_no'		=> $batch_no,
				'user_id'		=> $user_id
			);

			$this->db->update("UPDATE $defaultValueTableName SET " . S::sqlSingle($sqlarray)." WHERE tid=".S::sqlEscape($tid));
			return $is_success;
		}
		return $error;
	}

	/**
	 * �޸�AA���
	 * @param int $tid ����id
	 * @param int $actmid ��������id
	 * @param string $subject ���ӱ���
	 * @return string T
	 * @access private
	 */
	function modify_aa_payment($tid,$actmid = 0,$subject) {
		
		$defaultValueTableName = getActivityValueTableNameByActmid();
		$defaultvalue = $this->db->get_one("SELECT batch_no,user_id FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));//AA�ⲿ���

		if ($defaultvalue['batch_no']) {
			require_once(R_P.'lib/activity/alipay.php');
			$AlipayInterface = new AlipayInterface('modify_aa_payment');

			$param = array(
				/* ҵ����� */
				'batch_no'		=> $defaultvalue['batch_no'],
				'subject'		=> $subject,
				'detail'		=> '',
				'operator_id'	=> $defaultvalue['user_id'],
			);
			require_once(R_P.'require/posthost.php');
			$returnResult = PostHost($AlipayInterface->alipayurl($param),'', 'POST');//��ȡXMLֵ
		} else {
			$this->create_aa_payment($tid,$this->winduid,$actmid);
		}
		
	}

	/**
	 * �رն���
	 * @param int $tid ����id
	 * @param int $actmid ��������id
	 * @return string T
	 * @access private
	 */
	function close_aa_detail_payment($tid,$actuid) {
		global $timestamp;
		/*��ѯ����״̬ �Ȳ�ѯ�Ƿ���֧��������*/
		$this->query_aa_detail_payment($tid,$actuid);
		/*��ѯ����״̬*/

		$memberdb = $this->db->get_one("SELECT uid,username,totalcash,isadditional,batch_detail_no,ifpay FROM pw_activitymembers WHERE actuid=".S::sqlEscape($actuid));//AA������
		
		if (!$memberdb['ifpay']) {//ifpay = 0
			if ($memberdb['batch_detail_no']) {//batch_detail_no����
				require_once(R_P.'lib/activity/alipay.php');
				$AlipayInterface = new AlipayInterface('close_aa_detail_payment');
				$defaultValueTableName = getActivityValueTableNameByActmid();
				$defaultvalue = $this->db->get_one("SELECT user_id FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));//AA�ⲿ���
				$param = array(
					/* ҵ����� */
					'batch_detail_no'	=> $memberdb['batch_detail_no'],
					'operator_id'		=> $defaultvalue['user_id'],
				);

				require_once(R_P.'require/posthost.php');
				$returnResult = PostHost($AlipayInterface->alipayurl($param),'', 'POST');//��ȡXMLֵ

				$xml_parser = xml_parser_create();
				xml_parse_into_struct($xml_parser,$returnResult,$arr_vals);
				xml_parser_free($xml_parser);

				foreach ($arr_vals as $value) {
					if ($value['tag'] == 'ERROR') {
						$error = $value['value'];
					} elseif ($value['tag'] == 'IS_SUCCESS') {
						$is_success = $value['value'];
					}
				}
				if ($error) {
					$is_success = 'fail';
				} elseif ($is_success == 'T') {
					$this->db->update("UPDATE pw_activitymembers SET ifpay=3 WHERE actuid=".S::sqlEscape($actuid));//���ùر�
					$defaultValueTableName = getActivityValueTableNameByActmid();
					$this->db->update("UPDATE $defaultValueTableName SET updatetime=".S::sqlEscape($timestamp)." WHERE tid=".S::sqlEscape($tid));//�����б�̬ʱ��
					$is_success = 'success';
					$this->close_aa_detail_payment_sendmsg($memberdb,$tid);
				}
			} else {
				$this->db->update("UPDATE pw_activitymembers SET ifpay=3 WHERE actuid=".S::sqlEscape($actuid));//���ùر�
				$defaultValueTableName = getActivityValueTableNameByActmid();
				$this->db->update("UPDATE $defaultValueTableName SET updatetime=".S::sqlEscape($timestamp)." WHERE tid=".S::sqlEscape($tid));//�����б�̬ʱ��
				$is_success = 'success';
				$this->close_aa_detail_payment_sendmsg($memberdb,$tid);
			}
		} elseif ($memberdb['ifpay'] == 3) {
			$is_success = 'success';
			$this->close_aa_detail_payment_sendmsg($memberdb,$tid);
		} else {
			$is_success = 'payed';//�Ѿ�֧��
		}
		return $is_success;
	}

	/**
	 * �رն���������Ϣ
	 * @param array $memberdb ������Ϣ
	 * @param int $tid ����id
	 * @return string T
	 * @access private
	 */
	function close_aa_detail_payment_sendmsg($memberdb,$tid) {
		/*����Ϣ֪ͨ �رձ����� ������*/
		$thread = $this->db->get_one("SELECT subject,author,authorid FROM pw_threads WHERE tid=".S::sqlEscape($tid));
		if ($memberdb['isadditional']) {
			$titleText	= 'activity_close_pay_title';
			$contentText = 'activity_close_pay_content';
		} else {
			$titleText	= 'activity_signup_close_title';
			$contentText = 'activity_signup_close_content';
		}		
		M::sendNotice(
			array($thread['author']),
				array(
						'title' => getLangInfo('writemsg', $titleText, array(
								'uid'		=> $memberdb['uid'],
								'username'	=> $memberdb['username'],
								'tid'		=> $tid,
								'subject'	=> $thread['subject'],
								'totalcash'	=> $memberdb['totalcash']
							)
						),
						'content' => getLangInfo('writemsg', $contentText, array(
								'uid'		=> $memberdb['uid'],
								'username'	=> $memberdb['username'],
								'tid'		=> $tid,
								'subject'	=> $thread['subject'],
								'totalcash'	=> $memberdb['totalcash']
							)
						)
					),'notice_active', 'notice_active'
		);
		
		/*����Ϣ֪ͨ �رձ����� ������*/
		if ($memberdb['isadditional']) {
			$signuperTitleText	= 'activity_close_signuper_pay_title';
			$signuperContentText = 'activity_close_signuper_pay_content';
		} else {
			$signuperTitleText	= 'activity_signuper_close_title';
			$signuperContentText = 'activity_signuper_close_content';
		}
		
		M::sendNotice(
			array($memberdb['username']),
			array(
				'title' => getLangInfo('writemsg', $signuperTitleText, array(
						'uid'		=> $thread['authorid'],
						'username'	=> $thread['author'],
						'tid'		=> $tid,
						'subject'	=> $thread['subject'],
						'totalcash'	=> $memberdb['totalcash']
					)
				),
				'content' => getLangInfo('writemsg', $signuperContentText, array(
						'uid'		=> $thread['authorid'],
						'username'	=> $thread['author'],
						'tid'		=> $tid,
						'subject'	=> $thread['subject'],
						'totalcash'	=> $memberdb['totalcash']
					)
				)
			),'notice_active', 'notice_active'
		);
	}

	/**
	 * ��ѯ���ʶ���
	 * @param int $tid ����id
	 * @param int $actmid ��������id
	 * @return string T
	 * @access private
	 */
	function query_aa_detail_payment($tid,$actuid) {
		global $timestamp;
		require_once(R_P.'lib/activity/alipay.php');
		$AlipayInterface = new AlipayInterface('query_aa_detail_payment');

		$defaultValueTableName = getActivityValueTableNameByActmid();
		$batch_no = $this->db->get_value("SELECT batch_no FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));//AA�ⲿ���
		$memberdb = $this->db->get_one("SELECT out_trade_no,batch_detail_no FROM pw_activitymembers WHERE actuid=".S::sqlEscape($actuid));//AA������

		$param = array(
			/* ҵ����� */
			'batch_no'			=> $batch_no,
			'out_trade_no'		=> $memberdb['out_trade_no'],
			'batch_detail_no'	=> $memberdb['batch_detail_no'],
		);

		require_once(R_P.'require/posthost.php');
		$returnResult = PostHost($AlipayInterface->alipayurl($param),'', 'POST');//��ȡXMLֵ

		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser,$returnResult,$arr_vals);
		xml_parser_free($xml_parser);

		foreach ($arr_vals as $value) {
			if ($value['tag'] == 'ERROR') {
				$error = $value['value'];
			} elseif ($value['tag'] == 'IS_SUCCESS') {
				$is_success = $value['value'];
			} elseif ($value['tag'] == 'SIGN') {
				$sign = $value['value'];
			} elseif ($value['tag'] == 'BATCH_DETAIL_NO') {
				$batch_detail_no = $value['value'];
			} elseif ($value['tag'] == 'TRADE_STATUS') {
				$trade_status = $value['value'];
			}
		}

		if ($error) {
			return $error;
		} elseif ($is_success == 'T') {
			$payStatus = array(
				'I' => '0',//������
				'S' => '1',//��֧��
				'C' => '3',//���׹ر�
				'E' => '1',//���׳ɹ�
			);
			$this->db->update("UPDATE pw_activitymembers SET batch_detail_no=".S::sqlEscape($batch_detail_no).",ifpay=".S::sqlEscape($payStatus[$trade_status])." WHERE actuid=".S::sqlEscape($actuid));
			$this->db->update("UPDATE $defaultValueTableName SET updatetime=".S::sqlEscape($timestamp)." WHERE tid=".S::sqlEscape($tid));//�����б�̬ʱ��

			return $payStatus[$trade_status];
		}
	}

	/**
	 * �����Ƿ�ʵ����֤
	 * @param int $uid �û�id
	 * @return string T
	 * @access private
	 */
	function user_query($uid) {
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
		$userInfo = $userService->get($uid, false, false, true);
		$tradeinfo = $userInfo['tradeinfo'];
		
		$tradeinfo = unserialize($tradeinfo);

		$alipay		= $tradeinfo['alipay'];
		$isBinded	= $tradeinfo['isbinded'];
		$user_id	= $tradeinfo['user_id'];

		if ($alipay && $isBinded == 'T' && $user_id) {
			require_once(R_P.'lib/activity/alipay.php');
			$AlipayInterface = new AlipayInterface('user_query');
			$param = array(
				/* ҵ����� */
				'user_id'		=> $user_id,
			);
			require_once(R_P.'require/posthost.php');

			$xml_parser = xml_parser_create();
			$returnResult = PostHost($AlipayInterface->alipayurl($param),'', 'POST');//��ȡXMLֵ
			xml_parse_into_struct($xml_parser,$returnResult,$arr_vals);
			xml_parser_free($xml_parser);

			foreach ($arr_vals as $value) {
				if ($value['tag'] == 'IS_SUCCESS') {
					$is_success = $value['value'];
				} elseif ($value['tag'] == 'SIGN') {
					$sign = $value['value'];
				} elseif ($value['tag'] == 'IS_CERTIFIED') {
					$is_certified = $value['value'];
				}
			}

			if ($is_success == 'T' && $is_certified == 'T') {
				$tradeinfo['iscertified'] = 'T';
				$tradeinfo = addslashes(serialize($tradeinfo));
				
				$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
				$userService->update($uid, array(), array(), array('tradeinfo' => $tradeinfo));
				
				return $is_certified;
			}
		}
		return 'F';
	}


	/**
	 * ���������
	 * @param int $len λ��
	 * @param string �����
	 */
	function generatestr($len) {
		mt_srand((double)microtime()*1000000);
		$keychars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWYXZ";
		$maxlen = strlen($keychars)-1;
		$str = '';
		for ($i=0;$i<$len;$i++){
			$str .= $keychars[mt_rand(0,$maxlen)];
		}
		return substr(md5($str.microtime().$GLOBALS['HTTP_HOST'].$GLOBALS['pwServer']["HTTP_USER_AGENT"].$GLOBALS['db_hash']),0,$len);
	}
}
?>
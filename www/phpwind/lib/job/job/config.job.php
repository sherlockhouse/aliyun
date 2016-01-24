<?php
!function_exists('readover') && exit('Forbidden');
class JOB_Config {
	/*�����ļ�*/
		
	var $_members    = "members";
	var $_forums     = "forums";
	var $_modes      = "modes";
	var $_moderators = "moderators";
	var $_gifts      = "gifts";
	
	/*
	 * ������������������Ӧ
	 */
	function getJobType($k=null){
		$data  = array();
		$data[$this->_members] = $this->members();
		$data[$this->_forums] = $this->forums();
		$data[$this->_modes] = $this->modes();
		$data[$this->_moderators] = $this->moderators();
		$data[$this->_gifts] = $this->gifts();
		$types = array();
		foreach($data as $type =>$v){
			foreach($v as $job=>$v){
				$types[$job] = $type;
			}
		}
		return $k ? $types[$k] :$types;
	}
	
	function jobs($k = null){
		$data = array(
			$this->_members    => "��Ա��Ϣ��",
			$this->_forums     => "��̳������",
			//$this->_modes      => "Ȧ�Ӳ�����",
			//$this->_moderators => "����������",
			$this->_gifts     => "���������",
		);
		return $k ? $data[$k] : $data;
	}
	/*
	 * ��Ա��Ϣ��
	 */
	function members($k = null){
		$data = array(
			'doUpdatedata'    =>'��������',
			'doUpdateAvatar'  =>'�ϴ�ͷ��',
			'doSendMessage'   =>'������Ϣ',
			'doAddFriend'     =>'�Ӻ���',
			'doAuthAlipay'     =>'֧������֤',
			'doAuthMobile'     =>'�ֻ���֤',
		);
		return $k ? $data[$k] : $data;
	}
	/*
	 * ��̳������
	 */
	function forums($k = null){
		$data = array(
			'doPost'         =>'����',
			'doReply'        =>'�ظ�',
			//'doFavor'        =>'�ղ�',
			//'doForumShare'   =>'����',
			//'doVote'         =>'����',
			//'doUseTools'     =>'ʹ�õ���',
			//'doLookCard'     =>'�鿴�û���Ƭ',
		);
		return $k ? $data[$k] : $data;
	}
	/*
	 * Ȧ�Ӳ�����
	 */
	function modes($k = null){
		$data = array(
			//'doEntrySelf'    =>'������˿ռ�',
			//'doEntryFriend'  =>'�������Ѹ��˿ռ�',
			//'doWrite'        =>'����¼',
			//'doDiary'        =>'����־',
			//'doPhoto'        =>'����Ƭ',
			//'doModeShare'    =>'����',
			//'doComment'      =>'����',
		);
		return $k ? $data[$k] : $data;
	}
	/*
	 * ����������
	 */
	function moderators($k = null){
		$data = array(
			//'doPing'     =>'����',
			//'doHead'     =>'�ö�',
			//'doDigest'   =>'����',
			//'doLock'     =>'����',
			//'doUp'       =>'��ǰ',
			//'doDown'     =>'ѹ��',
			//'doHighline' =>'����',
			//'doPush'     =>'����',
		);
		return $k ? $data[$k] : $data;
	}
	/*
	 * ���������
	 */
	function gifts($k = null){
		$data = array(
			'doSendGift'    =>'�������',
		);
		return $k ? $data[$k] : $data;
	}
	
	/*
	 * �����������ģ��
	 */
	function condition($job){
		$jobName = $job['job'];
		$factor = unserialize($job['factor']);
		switch ($jobName){
			case 'doUpdatedata':
				return $this->finish_doUpdatedata($factor);
				break;
			case 'doUpdateAvatar':
				return $this->finish_doUpdateAvatar($factor);
				break;				
			case 'doSendMessage':
				return $this->finish_doSendMessage($factor);
				break;				
			case 'doAddFriend':
				return $this->finish_doAddFriend($factor);
				break;		
			case 'doPost':
				return $this->finish_doPost($factor);
				break;	
			case 'doReply':
				return $this->finish_doReply($factor);
				break;	
			case 'doSendGift':
				return $this->finish_doSendGift($factor);
				break;
			case 'doAuthAlipay':
				return $this->finish_doAuthAlipay($factor);
				break;
			case 'doAuthMobile':
				return $this->finish_doAuthMobile($factor);
				break;
			default :
				return '';
				break;
			
		}
	}
	
	function finish_doUpdatedata($factor){
		return '�����Լ��ĸ������Ϻ󼴿��������'.$this->getLimitTime($factor);
	}
	
	function finish_doUpdateAvatar($factor){
		return '�ɹ��ϴ�����ͷ��󼴿��������'.$this->getLimitTime($factor);
	}
	function finish_doAuthMobile($factor){
		return '�ɹ����ֻ����뼴���������'.$this->getLimitTime($factor);
	}
	function finish_doAuthAlipay($factor){
		return '�ɹ���֧�����ʺż����������'.$this->getLimitTime($factor);
	}
	
	function finish_doSendMessage($factor){
		return '�� '.$factor['user'].' ������Ϣ'.$this->getLimitTime($factor);
	}
	
	function finish_doAddFriend($factor){
		if($factor['type'] == 1){
			return '�� '.$factor['user'].' ��Ϊ����  '.$this->getLimitTime($factor);
		}else{
			return '�ɹ��� '.$factor['num'].' �����Ѻ󼴿��������'.$this->getLimitTime($factor);
		}
	}
	
	function finish_doPost($factor){
		$forum = L::forum($factor['fid']);
		$title = '<a target="_blank" href="thread.php?fid='.$forum['fid'].'">'.$forum['name'].'</a>';
		return '�� ��'.$title.'�� ��鷢 '.$factor['num'].' �����Ӽ���������� '.$this->getLimitTime($factor);
	}
	
	function finish_doReply($factor){
		if($factor['type'] == 1){
			$thread = $GLOBALS['db']->get_one("SELECT tid,subject FROM pw_threads WHERE tid=".S::sqlEscape($factor['tid']));
			if(!$thread){
				return '��Ǹ��ָ���Ļظ����Ӳ����ڣ�����ϵ����Ա';/*�������*/
			}
			$title = '<a href="read.php?tid='.$thread['tid'].'" target="_blank">'.$thread['subject'].'</a>';
			return '�� ��'.$title.'��������ӻظ� '.$factor['replynum'].' �μ����������  '.$this->getLimitTime($factor);
		}else{
			return '����'.$factor['user'].'�� �������������ӻظ� '.$factor['replynum'].' �μ����������'.$this->getLimitTime($factor);
		}
	}
	
	function finish_doSendGift($factor){
		return "��������󼴿�������񣬵õ�����";
	}
	function getLimitTime($factor){
		return (isset($factor['limit']) && $factor['limit']>0 ) ? ",����".$factor['limit']."Сʱ����� " : "";
	}
	
}
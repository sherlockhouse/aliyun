<?php !defined('P_W') && exit('Forbidden');
/**
 * @desc ���д�ɨ����
 *
 */
class PW_WordScan {
	/**
	 * @desc DB��
	 *
	 * @var object
	 */
	var $db;
	
	/**
	 * @desc ���дʹ�����
	 *
	 * @var object
	 */
	var $filter;
	
	/**
	 * @desc ������
	 *
	 * @var int
	 */
	var $skip = 0;
	
	/**
	 * @desc �Ƿ�����ת��: false=>��; true=>��
	 *
	 * @var BOOL
	 */
	var $convert = false;
	
	/**
	 * @desc ÿ��ɨ���¼����
	 *
	 * @var int
	 */
	var $pagesize;
	
	/**
	 * @desc ��δ���ɨ����:1=>����ֹͣ���; 0=>�����������
	 *
	 * @var int
	 */
	var $dispose = 1;
	
	/**
	 * @desc Ҫɨ���ָ�����id
	 *
	 * @var int
	 */
	var $fid ;
	
	/**
	 * @desc �ð��ļ�¼����
	 *
	 * @var int
	 */
	var $count;
	
	/**
	 * @desc �ð����ɨ��ļ�¼��
	 *
	 * @var int
	 */
	var $progress;
	
	/**
	 * �ô�ɨ�赽�İ������дʵļ�¼��
	 *
	 * @var int
	 */
	var $result;
	
	/**
	 * @desc �ð�������б����
	 *
	 * @var int
	 */
	var $table_progress;
	
	/**
	 * @desc ��ǰɨ������ݱ�
	 *
	 * @var string
	 */
	var $table;
	
	/**
	 * @desc ���һ��ɨ�赽�ļ�¼id
	 *
	 * @var int
	 */
	var $objid;
	
	function PW_WordScan() {
		global $db,$db_wordsfb_setting;
		$this->db = $db;
		$this->filter = L::loadClass('FilterUtil', 'filter');
		
		if (empty($db_wordsfb_setting)) {
			$this->dispose = 0;
		}
	}
	
	/**
	 * @desc ɨ�貢���ؽ��
	 *
	 * @param int $fid -- ���ID
	 * @param int $result  -- �Ƿ���մ���˼�¼
	 * @param int $restart -- �Ƿ�����ɨ��(���ɨ���¼)
	 * @param int $pagesize -- ÿ��ɨ���¼����
	 * @param int $skip -- ������
	 * @param int $convert -- �Ƿ�����ת��: 0=>��; 1=>��
	 * @return array -- ɨ�����:���ص�ǰ���ļ�¼��������ɨ���¼������ʾid
	 * 
	 * prompt: ��ʾ��Ϣid
	 * 1 ɨ�����
	 * 2 ���������
	 * 3 û������������
	 */
	function run($fid, $result = 0, $restart = 0, $pagesize = 1000, $skip = 0, $convert = false) {
		$this->fid = $fid;
		$this->skip = $skip;
		$this->convert = $convert;
		$this->pagesize = $pagesize;
		if ($restart == 1) {
			$this->ClearScanProgress();
		} else {
			$this->getProgress($result);
		}

		if (!$this->count) return array('prompt' => 2);
		
		if ($this->progress >= $this->count) return array('prompt' => 3);

		foreach ($this->table_progress as $table => $progress) {
			if ($table == 'pw_threads') {
				$sql = "SELECT COUNT(*) FROM $table WHERE tid>".S::sqlEscape($progress)." AND fid =".S::sqlEscape($this->fid);
				$new = $this->db->get_value($sql);
				if ($new) {//if (10 < $new) {
					$this->table = $table;
					$this->scanThreads();
					
					# ����ɨ�����
					$this->updateProgress();
					return array('progress' => $this->progress, 'count' => $this->count);
				}
			} else {
				$sql = "SELECT COUNT(*) FROM $table WHERE pid>".S::sqlEscape($progress)." AND fid =".S::sqlEscape($this->fid);
				$new = $this->db->get_value($sql);
				if ($new) {//if (10 < $new) {
					$this->table = $table;
					$finish = $this->scanPosts();

					# ����ɨ�����
					$this->updateProgress();
					return array('progress' => $this->progress, 'count' => $this->count);
				}
			}
		}
		return array('progress' => $this->progress, 'count' => $this->count, 'prompt' => 1);
	}
	
	/**
	 * @desc ɨ�������
	 */
	function scanThreads() {
		# ��ȡ������Ϣ,�ж��Ƿ��ظ���¼
		$sql = " SELECT t.tid, t.subject, t.ifcheck, t.postdate, t.author, f.id, f.state "
			 . " FROM $this->table AS t LEFT JOIN pw_filter AS f ON t.tid = f.tid"
			 . " WHERE t.tid>".S::sqlEscape($this->table_progress[$this->table])." AND t.fid =".S::sqlEscape($this->fid)
			 . " GROUP BY t.tid ORDER BY t.tid ASC LIMIT ".$this->pagesize;
		$query = $this->db->query($sql);
		
		$num = 0;
		while ($thread = $this->db->fetch_array($query)) {
			# ��ȡ��������
			$pw_tmsgs = GetTtable($thread['tid']);
			$sql = " SELECT content FROM $pw_tmsgs WHERE tid=".S::sqlEscape($thread['tid']);
			$thread['content'] = $this->db->get_value($sql);
			
			# ɨ�����
			$this->progress++;
			$this->objid = $thread['tid'];
						
			# ��������
			$content = $thread['subject'] . $thread['content'];
	
			# �������д�
			$result = $this->filter->paraseContent($content, $this->skip, $this->convert);
	
			# ����ɨ����
			if (is_array($result)) {
				$word  = $this->getWordString($result[1]);	
				$score = round($result[0], 2);
		
				if ($this->dispose && $score > 0 && $thread['ifcheck']) {
					# �������״̬
					//$sql = "UPDATE pw_threads SET ifcheck=0 WHERE tid = " .S::sqlEscape($thread['tid']);
					pwQuery::update('pw_threads', 'tid=:tid', array($thread['tid']), array('ifcheck'=>0));
					
					$num++;
							
					# ���°����Ϣ
					$this->updateCache();
						
					# ����Ϣ֪ͨ
					$msg = array(
						'subject' => $thread['subject'],
						'tid' 	  => $thread['tid'],
						'fid' 	  => $this->fid,
					);
					$this->sendMsg($thread['author'], $msg, 't');
				}

				if (!$thread['id']) {
					# ������ظ�,ɨ�赽�Ľ��+1
					$this->result++;
					
					$compart = $insertString ? ',' : '';
					# ��������
					$insertString .= $compart . "( " . S::sqlEscape($thread['tid']) . ", " . S::sqlEscape($word) . ", "
							      . S::sqlEscape($thread['postdate']) . ")";					
				} elseif ($thread['state']) {
					# ������Ѿ����ͨ�����ٴα�ɨ��,ɨ�赽�Ľ��+1
					$this->result++;
		
					# ��������
					$value = array(
						'state'  => 0,
						'filter' => $word,
						'created_at' => $thread['postdate'],
					);
					$value = S::sqlSingle($value);
		
					# ���¼�¼
					$sql = "UPDATE pw_filter SET {$value} WHERE pid=0 AND tid = " . S::sqlEscape($thread['tid']);
					$this->db->update($sql);
				}
			}
		}
		
		# �����¼
		if ($insertString) {
			$insertSql =  "INSERT INTO pw_filter (tid, filter, created_at) VALUES " . $insertString;
			$this->db->update($insertSql);
		}
		
		if ($this->dispose && $num) {
			$this->updateCache($num);
		}
	}
	
	/**
	 * @desc ����������״̬�ı��Ӱ��İ����Ϣ
	 */
	function updateCache($num) {
		/**
		$sql = "UPDATE pw_forumdata SET article=article-".S::sqlEscape($num,false).",topic=topic-".S::sqlEscape($num,false)." WHERE fid=".S::sqlEscape($this->fid);
		$this->db->update($sql);
		**/
		$this->db->update(pwQuery::buildClause("UPDATE :pw_table SET article=article-:article,topic=topic-:topic WHERE fid=:fid", array('pw_forumdata', $num, $num, $this->fid)));
	}
	
	/**
	 * @desc ɨ��ظ���
	 */
	function scanPosts()
	{
		# ��ȡ������Ϣ,�ж��Ƿ��ظ���¼
		$sql = "SELECT p.pid,p.content,p.subject,t.tid,t.subject AS title,p.author,p.postdate,p.ifcheck,f.id,f.state "
			 . "FROM $this->table AS p LEFT JOIN pw_threads AS t ON p.tid=t.tid LEFT JOIN pw_filter AS f ON p.pid = f.pid "
			 . "WHERE p.tid > 0 AND p.pid>".S::sqlEscape($this->table_progress[$this->table])
			 ." AND t.fid =".S::sqlEscape($this->fid)
			 . " GROUP BY p.pid ORDER BY p.pid ASC LIMIT ".$this->pagesize;
		$query = $this->db->query($sql);		
		while ($post = $this->db->fetch_array($query)) {
			
			# ɨ�����
			$this->progress++;
			$this->objid = $post['pid'];
		
			# ����
			$content = $post['subject'] . $post['content'];
			# �������д�
			$result = $this->filter->paraseContent($content, $this->skip, $this->convert);
		
			# ����ɨ����
			if(is_array($result)) {		
				$word  = $this->getWordString($result[1]);	
				$score = round($result[0], 2);
		
				if ($this->dispose && $score > 0 && $post['ifcheck']) {
					$tids[$post['tid']]++;
					# �����
					$sql = "UPDATE $this->table SET ifcheck=0 WHERE pid = " .S::sqlEscape($post['pid']);
					$this->db->update($sql);
						
					# ����Ϣ֪ͨ
					$msg = array(
						'subject' => $post['title'],
						'tid' => $post['tid'],
						'pid' => $post['pid'],
						'fid' => $this->fid,
					);
					$this->sendMsg($post['author'], $msg, 'p');
				}
		
				if (!$post['id']) {
					# ��������ظ���¼,ɨ�赽�Ľ��+1
					$this->result++;
		
					$compart = $insertSql ? ',' : '';
					# ��������
					$insertSql .= $compart . "( " . S::sqlEscape($post['tid']) . ", " . S::sqlEscape($post['pid']) 
									  . ", " . S::sqlEscape($word) . ", " . S::sqlEscape($post['postdate']) . ")";
				} elseif ($post['state']) {					
					# ������Ѿ����������˼�¼�ٴα�ɨ��,ɨ�赽�Ľ��+1
					$this->result++;
							
					# ��������
					$value = array(
						'state'  => 0,
						'filter' => $word,
						'created_at' => $post['postdate'],
					);
					$value = S::sqlSingle($value);
		
					# ���¼�¼
					$sql = "UPDATE pw_filter SET {$value} WHERE tid=".S::sqlEscape($post['tid'])." AND pid=" . S::sqlEscape($post['pid']);
					$this->db->update($sql);
				}
			}			
		}

		if ($this->dispose && $tids) {
			# ��������
			$article = 0;
				
			foreach ($tids as $key => $value) {				
				# �����������ظ���
				//$sql = "UPDATE pw_threads SET replies=replies-".S::sqlEscape($value,false)." WHERE tid=".S::sqlEscape($key);
				$sql = pwQuery::buildClause('UPDATE :pw_table SET replies=replies-:replies WHERE tid=:tid', array('pw_threads', $value, $key));
				$this->db->update($sql);
				
				$article += $value;
			}
			
			# ���°��������
			/**
			$sql = "UPDATE pw_forumdata SET article=article-".S::sqlEscape($article,false)." WHERE fid=".S::sqlEscape($this->fid);
			$this->db->update($sql);
			**/
			$this->db->update(pwQuery::buildClause("UPDATE :pw_table SET article=article-:article WHERE fid=:fid", array('pw_forumdata', $article, $this->fid)));
		}
		
		# �����¼
		if ($insertSql) {
			$sql =  "INSERT INTO pw_filter (tid, pid, filter, created_at) VALUES " . $insertSql;
			$this->db->update($sql);
		}
	}
	
	/**
	 * @desc ����Ϣ֪ͨ�û����ӱ���
	 *
	 * @param string $user -- �ռ����û���
	 * @param array $L     -- ��Ϣ������Ϣ
	 * @param string $type -- ��������:t=>����;p=>�ظ�
	 */
	function sendMsg($user, $L, $type = 't') {
		if ($type == 't') {		
			$title	 = getLangInfo('cpmsg','filtermsg_thread_title');
			$content = getLangInfo('cpmsg','filtermsg_thread_content', $L);
		} else {
			$title	 = getLangInfo('cpmsg','filtermsg_post_title');
			$content = getLangInfo('cpmsg','filtermsg_post_content', $L);
		}
	
		M::sendNotice(
			array($user),
			array(
				'title' => $title,
				'content' => $content,
			)
		);
	}
	
	/**
	 * @desc ���ɨ�����
	 */
	function ClearScanProgress() {
		global $db_plist;
		
		# ��ȡ����
		//* require_once pwCache::getPath(D_P.'data/bbscache/wordsfb_progress.php');
		extract(pwCache::getData(D_P.'data/bbscache/wordsfb_progress.php', false));
		$this->threaddb = unserialize($threaddb);
		$this->catedb   = unserialize($catedb);
		$temp_threaddb = unserialize($threaddb);
		
		# ��ȡ�����
		$sql = "SELECT COUNT(*) AS count FROM pw_threads WHERE fid =".S::sqlEscape($this->fid);
		$count = $this->db->get_value($sql);
		
		# ��ȡ�ظ���
		if ($db_plist && is_array($db_plist)) {
			foreach ($db_plist as $key=>$value) {
				if ($key>0) {
					$postslist[] = 'pw_posts'.(int)$key;
				} else {
					$postslist[] = 'pw_posts';
				}
			}
		} else {
			$postslist[] = 'pw_posts';
		}
			
		foreach ($postslist as $pw_posts) {
			$sql = "SELECT COUNT(*) AS count FROM $pw_posts WHERE fid =".S::sqlEscape($this->fid);
			$postcount = $this->db->get_value($sql);
			$count += $postcount;
		}
		
		foreach ($temp_threaddb as $key => $forums) {
			foreach ($forums  as $key2 => $value) {
				if ($this->fid == $key2) {
					
					foreach ($value['table_progress'] as $table => $progress) {
						$value['table_progress'][$table] = 0;
					}
					
					$this->count = $count;
					$this->progress = 0;
					$this->result = 0;
					$this->table_progress = $value['table_progress'];
					
					$temp_threaddb[$key][$key2]['count'] = $this->count;
					$temp_threaddb[$key][$key2]['progress'] = $this->progress;
					$temp_threaddb[$key][$key2]['result'] = $this->result;
					$temp_threaddb[$key][$key2]['table_progress'] = $this->table_progress;
				}
			}
		}
		$this->threaddb = $temp_threaddb;
		$threaddb = serialize($temp_threaddb);
	
		# д���ļ�	
		$filecontent = "<?php\r\n";
		$filecontent.="\$catedb=".pw_var_export($catedb).";\r\n";
		$filecontent.="\$threaddb=".pw_var_export($threaddb).";\r\n";
		$filecontent.="?>";
		$cahce_file = D_P.'data/bbscache/wordsfb_progress.php';
		pwCache::setData($cahce_file, $filecontent);
	}
	
	/**
	 * @desc ��ȡɨ�����
	 */
	function getProgress($result)
	{
		global $db_plist;
		
		# ��ȡ����
		//* require_once pwCache::getPath(D_P.'data/bbscache/wordsfb_progress.php');
		extract(pwCache::getData(D_P.'data/bbscache/wordsfb_progress.php', false));
		$this->threaddb = unserialize($threaddb);
		$this->catedb   = unserialize($catedb);
		$temp_threaddb = unserialize($threaddb);
		
		# ��ȡ����������
		$sql = "SELECT COUNT(*) AS count FROM pw_threads WHERE fid =".S::sqlEscape($this->fid);
		$count = $this->db->get_value($sql);
		
		# ��ȡ�ظ���
		if ($db_plist && is_array($db_plist)) {
			foreach ($db_plist as $key=>$value) {
				if ($key>0) {
					$postslist[] = 'pw_posts'.(int)$key;
				} else {
					$postslist[] = 'pw_posts';
				}
			}
		} else {
			$postslist[] = 'pw_posts';
		}
		
		# ��ȡ�ظ�������
		foreach ($postslist as $pw_posts) {
			$sql = "SELECT COUNT(*) AS count FROM $pw_posts WHERE fid =".S::sqlEscape($this->fid);
			$postcount = $this->db->get_value($sql);
			$count += $postcount;
		}

		foreach ($temp_threaddb as $key => $forums) {
			foreach ($forums  as $key2 => $value) {
				if ($this->fid == $key2) {
					$this->table_progress = $value['table_progress'];
					$this->count = $count;
					$this->progress = $value['progress'];
					if ($result == 1) {
						$this->result = 0;
					} else {
						$this->result = $value['result'];
					}
					
					$temp_threaddb[$key][$key2]['count'] = $this->count;
					$temp_threaddb[$key][$key2]['progress'] = $this->progress;
					$temp_threaddb[$key][$key2]['result'] = $this->result;
					$temp_threaddb[$key][$key2]['table_progress'] = $this->table_progress;
				}
			}
		}

		$this->threaddb = $temp_threaddb;
		$threaddb = serialize($temp_threaddb);
		$catedb   = serialize($this->catedb);
	
		# д���ļ�	
		$filecontent = "<?php\r\n";
		$filecontent.="\$catedb=".pw_var_export($catedb).";\r\n";
		$filecontent.="\$threaddb=".pw_var_export($threaddb).";\r\n";
		$filecontent.="?>";
		$cahce_file = D_P.'data/bbscache/wordsfb_progress.php';
		pwCache::setData($cahce_file, $filecontent);
	}
	
	/*function getProgress()
	{
		# ��ȡ����
		require_once(D_P.'data/bbscache/wordsfb_progress.php');
		$this->threaddb = unserialize($threaddb);
		$this->catedb   = unserialize($catedb);
				
		foreach ($this->threaddb as $key => $forums) {
			foreach ($forums  as $key2 => $value) {
				if ($this->fid == $key2) {
					$this->table_progress = $value['table_progress'];
					$this->count = $value['count'];
					$this->progress = $value['progress'];
					$this->result = $value['result'];
				}
			}
		}
	}*/
	
	/**
	 * @desc ����ɨ�����
	 */
	function updateProgress() {
		if ($this->progress > $this->count) $this->progress = $this->count;
		if ($this->objid) {
			foreach ($this->threaddb as $key => $forums) {
				foreach ($forums  as $key2 => $value) {
					if ($this->fid == $key2) {
						$this->threaddb[$key][$key2]['progress'] = $this->progress;
						$this->threaddb[$key][$key2]['result']   = $this->result;
						$this->threaddb[$key][$key2]['table_progress'][$this->table] = $this->objid;
					}
				}
			}
			$threaddb = serialize($this->threaddb);
			$catedb = serialize($this->catedb);
		
			# д���ļ�	
			$filecontent = "<?php\r\n";
			$filecontent.="\$catedb=".pw_var_export($catedb).";\r\n";
			$filecontent.="\$threaddb=".pw_var_export($threaddb).";\r\n";
			$filecontent.="?>";
			$cahce_file = D_P.'data/bbscache/wordsfb_progress.php';
			pwCache::setData($cahce_file, $filecontent);
		}
	}
	
	/**
	 * @desc ��ɨ��������д�������˳��ַ���
	 *
	 * @param unknown_type $strArray
	 * @return unknown
	 */
	function getWordString($strArray) {
		$array = array();
		
		foreach ($strArray as $value) {
			$array[] = $value[0];
		}
	
		$array = array_unique($array);
	
		$string='';
		foreach($array as $key=>$val) {
			if ($val) {
				$string .= $string ? ','.$val : $val;
			}
		}
		
		return $string;
	}
}
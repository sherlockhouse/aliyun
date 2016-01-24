<?php
!function_exists('readover') && exit('Forbidden');
/**
 * �������ݵ�ʵ�幤�ߣ��������˵�һϵ�ж�����ʵ��
 * �������ײ���ķ�װ�͵��ã�ÿ�����������Ե������ã�
 * ����ʵ���ⲿ�����󣬱�����Ҫ���б���ת������ôֱ�ӵ�
 * �����еı���ת�����ɻ�����ݣ�����������������
 *
 * @package Filter
 */
class PW_FilterUtil {
	/**
	 * ���л���������ֵ��ŵ�Ĭ��·��
	 * @var string
	 */
	var $dict_bin_path;
	/**
	 * Ĭ�������ֵ�Ĵ��λ��
	 * @var string
	 */
	var $dict_dir;
	/**
	 * Ĭ���ı���ʽ�ֵ���·��
	 * @var string
	 */
	var $dict_source_path;
	/**
	 * ���μ������д�Ȩ�صȼ�
	 * @var int
	 */
	var $filter_weight = 0;
	/**
	 * ���μ������д�����
	 * @var array
	 */
	var $filter_word;

	var $code;
	var $fbwords = null;
	var $replace = null;
	var $_list = array();

	function PW_FilterUtil($file = array()) {
		if ($file) {
			$this->dict_dir = $file['dir'];
			$this->dict_bin_path = $file['bin'];
			$this->dict_source_path = $file['source'];
		} else {
			$this->dict_dir = D_P.'data/bbscache/';
			$this->dict_path = $this->dict_dir . 'wordsfb.php';
			$this->dict_bin_path = $this->dict_dir . 'dict_all.dat';
			$this->dict_source_path = $this->dict_dir . 'dict_all.txt';
		}
		$this->code = $GLOBALS['db_wordsfb'];
	}

	function setFiles($file) {
		$this->dict_dir = $file['dir'];
		$this->dict_bin_path = $file['bin'];
		$this->dict_source_path = $file['source'];
	}

	/**
	 * ����ַ����Ƿ���Ҫ���������滻
	 */
	function ifwordsfb($str) {
		return ($this->comprise($str) === false) ? $this->code : 0;
	}

	function equal($currcode) {
		return ($currcode == $this->code);
	}

	function loadWords() {
		if (!is_array($this->fbwords)) {
			//* include pwCache::getPath(D_P."data/bbscache/wordsfb.php");
			extract(pwCache::getData(D_P."data/bbscache/wordsfb.php", false));
			$this->fbwords	= (array)$wordsfb;
			$this->replace	= (array)$replace;
			$this->alarm	= (array)$alarm;
		}
	}
	/**
	 * ����һ�����������滻���ַ���
	 */
	function convert($str, $wdstruct = array()) {
		$msg = $str;
		
		$file = array(
			'bin'    => $this->dict_bin_path,
			'source' => $this->dict_source_path
		);
		$trie = new Trie($file);
		$trie->nodes = $trie->getBinaryDict($trie->default_out_path);
		$msg = $trie->replaces($str);
		
		if ($wdstruct) {
			if ($msg == $str) {
				$this->addList('yes', $wdstruct['type'], $wdstruct['id']);
			} elseif ($wdstruct['code'] > 0) {
				$this->addList('no', $wdstruct['type'], $wdstruct['id']);
			}
		}
		return $msg;
	}
	function addList($key, $type, $id) {
		if (empty($this->_list)) {
			register_shutdown_function(array($this, 'updateWordsfb'));
		}
		$this->_list[$key][$type][] = $id;
	}
	/**
	 * �����������ݵĴ���ϵ��
	 */
	function updateWordsfb() {
		if ($this->_list['yes']) {
			$this->_update($this->_list['yes'], $this->code);
		}
		if ($this->_list['no']) {
			$this->_update($this->_list['no'], 0);
		}
		$this->_list = array();
	}

	function _update($arr, $val) {//private function
		global $db;
		foreach ($arr as $k => $v) {
			list($table, $field) = $this->tablestruct($k);
			if ($table && $v) {
				/**
				$db->update("UPDATE $table SET ifwordsfb=" . S::sqlEscape($val) . " WHERE $field IN (" . S::sqlImplode($v) . ')');
				**/
				pwQuery::update("{$table}", "$field IN (:$field)", array($v), array('ifwordsfb' => $val));
			}
		}
	}

	function tablestruct($type) {
		$struct = array(
			'topic'		=> array($GLOBALS['pw_tmsgs'], 'tid'),
			'posts'		=> array($GLOBALS['pw_posts'], 'pid'),
			'comments'	=> array('pw_comment', 'id'),
			'oboard'	=> array('pw_oboard','id'),
			'diary'		=> array('pw_diary','did')
		);
		return isset($struct[$type]) ? $struct[$type] : array('','');
	}

	/**
	 * ����ַ������Ƿ�������ô���
	 *
	 * @param $str
	 * @param $replace
	 * @param $alarm
	 * @return bool �Ƿ�������ô��trueΪ������falseΪû�н��ô���
	 */
	function comprise($str, $replace = true,$alarm = true) {
		if (empty($str)) {
			return false;
		}
		$this->getFilterResult($str);
		$titlelen = strlen($str);
		$arrWords = array_keys($this->filter_word);
		$title_filter_word = '';
		foreach ($arrWords as $key) {
			if ($key > $titlelen) break;
			$title_filter_word .= $title_filter_word ? ','.$this->filter_word[$key] : $this->filter_word[$key];
		}
		return $title_filter_word ? $this->getTrueBanword($title_filter_word) : false;
	}

	function getTrueBanword($word) {
		$word = stripslashes($word);
		//$word = substr($s_word,1,strlen($word)-3);
		$word = preg_replace('/\.\{0\,(\d+)\}/i', '', $word);
		return $word;
	}
	/**
	 * ����������Ƿ��б�������
	 */
	function alarm($title, $content = '') {
		if ($this->alarm) {
			foreach ($this->alarm as $key => $value) {
				if (preg_match($key,$title) || preg_match($key,$content)) {
					return true;
				}
			}
		}
		return false;
	}

    /**
	* @desc ����Ψһʵ��,����Ϊ����

	function getInstance() {
		static $instance = null;
		if (!isset($instance)) {
			$instance = new FilterUtil();
		}
		return $instance;
	}*/

    /**
     * �����ֵ�
     * @param $path ���л����ֵ���·��
     * @return $return int ����ɹ��󷵻ص�����
     */
    function buildDict($path = null) {
        if($path == null) {
           $path = array(
                'bin'    => $this->dict_bin_path,
                'source' => $this->dict_source_path
            );
        }
        $trie = new Trie($path);
        $return = $trie->build();
        return $return;
    }

    /**
     * ��������
     * 1.����HTML���� 2. ���˻����� 3.����Ӣ�ı��
     * 4. ����ת�� 5. ���ʺϲ� 6. �ִ�ƥ�� 7.����Ȩ��
     * ע��:����Ӣ�ı������ȫ�Ƿ��ţ����ܻ���ִ���
     * @param $content string �ɾ��Ĵ��ִʵ�����
     * @param $skip int  ���ʾ���
     * @param $convert boll  ��ת��
     * @param $dic_path string ���л��ֵ���·��
     * @return $weight int �������ĵ�������Ȩ��
     */
	function paraseContent($content, $skip = 0, $convert = false, $dict_path = null) {

		//�����û������ı�������UBB��ǩ
		//$content = $this->filterWindCode($content);

		//�����û������ı�������HTML��ǩ
		$content = $this->filterHtml($content);

		//��������״̬�����ż�������,���������Ƭ����
		//$content = $this->filterChineseCode($content);

		//���˼��������ó����ĸ��ֱ����ţ�����ȫ�ǺͰ��
		// $content = $this->filterSymbol($content);

		if($convert){
			//���б���ת����������Ҫ���ڷ���ת����
			$content = $this->convertCode($content);
		}

		if ($skip >= 1) {
			$skip = intval($skip);
			//���ʴ���
			$content = $this->skipWords($skip,$content);
		}
		$file = array(
			'bin'    => $this->dict_bin_path,
			'source' => $this->dict_source_path
		);
		$trie = new Trie($file);

		//�����ṩ���ҹؼ��ֵķ���
		$result = $trie->search($content, $dict_path);
		if (empty($result)) {
            return 0;
        }

		$bayes = new Bayes();
		//��ȡ����Ȩ��
		$weight = $bayes->getWeight($result);
		return array($weight,$result);
    }

	function getFilterResult($content, $skip = 0, $convert = false, $dict_path = null ) {
		//�ж����д�
		$result = $this->paraseContent($content);
		$array = array();
		//�����жϽ�����
		if (is_array($result)) {
			foreach ($result[1] as $key=>$value) {
				$array[$key] = $value[0];
			}
			$array = array_unique($array);

			$this->filter_weight = $result[0] >= 1 ? 1 : ($result[0] >= 0.8 ? 2 : 3);
		}
		$this->filter_word = $array;
	}

	/**
	 * @desc ����filter��¼
	 *
	 * @param int $tid -- ����id
	 * @param int $pid -- �ظ�id
	 * @param string $filter -- �������д�
	 */
	function insert($tid, $pid, $filter, $state=0) {
    	global $db,$timestamp;

    	//�ж��Ƿ��ظ���¼
    	$sql = "SELECT id,state FROM pw_filter WHERE tid=".S::sqlEscape($tid)." AND pid=".S::sqlEscape($pid);
    	$record = $db->get_one($sql);

	    if (!$record) {
	    	//��������
	    	$value = array(
	    	    'tid'    => $tid,
	    	    'pid'    => $pid,
	            'filter' => $filter,
	            'state'  => ($state!=3 ? 0 : 3),
				'assessor'=> ($state!=3 ? '' : 'SYSTEM'),
	            'created_at' => $timestamp,
				'updated_at' => $timestamp,
	        );
	        //�����¼�¼
	        $db->update("INSERT INTO pw_filter SET " . S::sqlSingle($value));
    	} else {
    		if ($record['state'] == 2 || $record['state'] == 1) {
    			//��������
				$value = array(
					'state'  => 0,
					'filter' => $filter,
					'created_at' => $timestamp,
				);
				$value = S::sqlSingle($value);

    			//���¼�¼
				$sql = "UPDATE pw_filter SET {$value} WHERE tid=".S::sqlEscape($tid)." AND pid=" . S::sqlEscape($pid);
				$db->update($sql);
    		}
    	}
    }

	/**
	 * @desc ɾ��filter
	 *
	 * @param int $tid ����id
	 * @param int $pid �ظ�id
	 */
	function delete($tid, $pid) {
		global $db;
		$db->update("DELETE FROM pw_filter WHERE tid=" . S::sqlEscape($tid) . " AND pid=" . S::sqlEscape($pid));
	}

	/**
	 * ���˼��������ó����ĸ��ֱ����ţ�����ȫ�ǺͰ��
	 * @param $content ��������HTML��ǩ���˵�����
	 * @return $ret string ���ع��˺�Ľ��
	 */
	function filterSymbol($content) {
		$length = strlen($content);
		$i = 0;
		$ret = '';
		while ($i < $length) {
			$c = ord($content[$i]);
			if($c<48 || ($c>58 && $c <65) || ($c>90 && $c <97) ||($c>122 && $c<127) ) {
				$i++;
				continue;  //ASCII���й涨�ķ�������ĸ����
			}
			$ret .= chr($c);
			$i++;
		}
		return $ret;
	}

    /**
     * ���б���ת����������Ҫ���ڷ���ת����
     * @param $fcode ��from code ��ԭ���ı���,����"BIG5"
     * @param $tcode ��to code��Ŀ�����,����"GB2312"
     * @param $content ���Ѿ���������ı�����ת��
     * @param $dict_dir ת���ַ����ձ���λ��
     * @return $ret string ����ת������ı�
     */
    function convertCode($content, $fcode = 'CHST', $tcode = 'CHSS', $dict_dir = null) {
        if(is_null($dict_dir)) {
            $dict_dir = $this->dict_dir;
        }
        L::loadClass('Chinese', 'utility/lang', false);
        $ch = new Chinese($fcode, $tcode, true);
        $ret = $ch->Convert($content);
        return $ret;
    }

    /**
     * ���ʴ���
     * @param $skip ��Ծ����
     * @param $content ������������ı�
     * @param $dict_dir �ֵ��ı��ļ�λ��
     * @return $ret ���ʴ������ı�
     */
    function skipWords($skip, $content, $dict_dir=null) {
        $ret = $content;
        if(is_null($dict_dir)) {
            $dict_dir = $this->dict_source_path;
        }

        $handle = fopen($dict_dir,"r");
        while (!feof($handle)) {
            $lines = fgets($handle);
			//echo $lines;
			//exit;
			//echo $lines;
			//$lines = "�ŷ����� 1";
            preg_match('/^(.*?)\s+(.*)/i', $lines, $key);
            $len = strlen($key[1]); //����ؼ��ʳ���
            for($i=0; $i<$len;$i++) { //��ʼƴװ����
                if($i == 0) {
					if(ord($key[1][$i]) > 127){
						$rgx = substr($key[1], $i,2);
						$i++;
					}else{
						$rgx = substr($key[1], $i,1);
					}
                } else  {
					if(ord($key[1][$i]) > 127){
						$rgx .= "(.{0,".$skip."}?)". substr($key[1], $i,2);
						$i++;
					}else{
						$rgx .=  substr(str_replace(array('/','.'),array('\/','\.'),$key[1]), $i,1);
					}
                    if($i == $len-1) {
                        $rgx ="/" . $rgx ."/";
                    }
                }
            }
			//echo "$rgx, $key[1], $ret";

			//echo $rgx;exit;
            $ret = preg_replace($rgx, $key[1], $ret);
        }
        fclose($handle);
        return $ret;
    }

    /**
     * �����û������ı�������UBB��ǩ
     * @param $content string �û�������ı�
     * @return string  ���ر����˺��ı�
     */
    function filterWindCode($content) {
    	$pattern = array();
    	if (strpos($content,"[post]")!==false && strpos($content,"[/post]")!==false) {
    		$pattern[] = "/\[post\].+?\[\/post\]/is";
    	}
    	if (strpos($content,"[hide=")!==false && strpos($content,"[/hide]")!==false) {
    		$pattern[] = "/\[hide=.+?\].+?\[\/hide\]/is";
    	}
    	if (strpos($content,"[sell")!==false && strpos($content,"[/sell]")!==false) {
    		$pattern[] = "/\[sell=.+?\].+?\[\/sell\]/is";
    	}
    	$pattern[] = "/\[[a-zA-Z]+[^]]*?\]/is";
    	$pattern[] = "/\[\/[a-zA-Z]*[^]]\]/is";

    	$content = preg_replace($pattern,'',$content);
    	return trim($content);
    }

    /**
     * �����û������ı�������HTML��ǩ
     * @param $content string �û�������ı�
     * @return $ret string  ���ر����˺��ı�
     */
    function filterHtml($content) {
        $ret = strip_tags($content);
        return $ret;
    }

    /**
     * ��������״̬�����ż�������,���������Ƭ����
     * @param $content  string  ��Ҫ���˵��ַ���
     * @return $ret string ���˺���ַ���
     */
    function filterChineseCode($content) {
        $ret = "";
        $chars = array();
        //Ϊ�Ƿ�Ϊ���ı�������
        $is_code = false;
        $length = iconv_strlen($content,"GBK");
        for ($i=0; $i<$length; $i++) {
            $chars[] = iconv_substr($content, $i, 1, "GBK");
        }

        foreach($chars as $char){

            for($byte = 0xA0; $byte<= 0xA9; $byte++) {
                if(strlen($char) == 2 && ord($char[0]) == $byte) {
                    $is_code = true;
                    continue;
                }
            }
            if(!$is_code) {
                $ret .= $char;
            }
            //���ݱ��λ
            $is_code = false;
        }
        return $ret;
    }
}

class Trie {
    //Ĭ�����л����ֵ���·��
    var $default_out_path ;
    //Ĭ��ԭʼ�ֵ���·��
    var $default_dict_path ;
    //�ڵ����顣ÿ���ڵ�Ϊ��Ԫ�飬����Ϊ�Ƿ�Ҷ�ӽڵ㣬�ӽڵ�.
    var $nodes ;

    function Trie($file) {
        $this->default_out_path  = $file['bin'];
        $this->default_dict_path = $file['source'];
    }

    /**
     * ���������洢���л��ı��Ȳ�����װ
     * @param $path  string �ֵ���λ��
     * @param $out_path  string ���л�����λ��
     * @return $ret mixed �Ƿ�ɹ������ɹ�����false
     */
    function build($path = null, $out_path = null) {
        if(empty($path)) {
            $path = $this->default_dict_path;
        }
        if(empty($out_path)) {
            $out_path = $this->default_out_path;
        }

        $words = $this->getDict($path);
        $tree = $this->getTree($words);
        $ret = $this->putBinaryDict($out_path, $tree);
        $a = true;
        return $ret;
    }

    /**
     * �����ṩ���ҹؼ��ֵķ���
     * @param $content string ��Ҫ���ҵ��ı�
     * @param $dict_path ���л��ֵ�·��
     * @return $matchs array ���ҵ��Ĺؼ��ֺ�Ȩ��
     */
    function search($content, $dict_path) {
        if(empty($dict_path)) {
            $dict_path = $this->default_out_path;
            $ifUpperCase = 1;
        }else{
        	$ifUpperCase = 0;
        }
        $words = $this->getBinaryDict($dict_path);
		if ($words) {
			$this->nodes = $words;
			$matchs = $this->match($ifUpperCase,$content);
			return $matchs;
		} else {
			return false;
		}
    }

    /**
     * ���ļ��е��ֵ����зŵ�������ȥ
     * @param $path string �ֵ�·��
     * @return $words array �ֵ�
     */
    function getDict($path) {
        $i = 0;
        $words = array();

        $handle = fopen($path, "r");

        if($handle == false) {
            return $words;
        }
        while(!feof($handle)) {
            $words[$i] = trim(fgets($handle));
            $i++;
        }
        fclose($handle);
        return $words;
    }

    /**
     * ��ȡ���л�����ֵ䲢�����л�
     * @param $path string ���л��ֵ���·��
     * @return $words array �����л��������
     */
    function getBinaryDict($path = null) {
        if(empty($path)) {
            $path = $this->default_out_path;
        }
		$words = readover($path);
        if(!$words) {
            return array();
        }
        $words = unserialize ($words);
        return $words;
    }

    /**
     * ���ֵ����л��󱣴浽�ļ���
     * @param $path string ����·��
     * @param $words array ������ʽ���ֵ�
     * @return $ret mixed û�б���ɹ�����false
     */
    function putBinaryDict($path, $words) {
        if(empty($path)) {
            $path = $this->default_out_path;
        }
        if(!$words) {
            return ;
        }
        $words = serialize($words);
        $handle = fopen($path, 'wb');
        $ret = fwrite($handle, $words);
        if($ret == false) {
            return false;
        }
        fclose($handle);
        return $ret;

    }

    /**
     * �������Ĺ��̷���
     * @param $words array �ֵ��Ȩ������
     */
    function getTree($words) {
        $this->nodes = array( array(false, array()) ); //��ʼ������Ӹ��ڵ�
        $p = 1; //��һ��Ҫ����Ľڵ��
        foreach ($words as $word) {
			$cur = 0; //��ǰ�ڵ��
			//preg_match('/^(.*?)\s+(.*)/i', $word, $weight); //��ȡ�ؼ��ֺ�Ȩ��
			//$weight = explode("|", $word);
			//$word = trim($weight[0]);
			list($word, $weight, $replace) = $this->split($word);
			for ($len = strlen($word), $i = 0; $i < $len; $i++) {
				$c = ord($word[$i]);
				if (isset($this->nodes[$cur][1][$c])) { //�Ѵ��ھ�����
					$cur = $this->nodes[$cur][1][$c];
					continue;
				}
				$this->nodes[$p]= array(false, array()); //�����½ڵ�
				$this->nodes[$cur][1][$c] = $p; //�ڸ��ڵ��¼�ӽڵ��
				$cur = $p; //�ѵ�ǰ�ڵ���Ϊ�²����
				$p++; //
			}
			$this->nodes[$cur][0] = true; //һ���ʽ��������Ҷ�ӽڵ�
			$this->nodes[$cur][2] = trim($weight); //��Ȩ�ط���Ҷ�ӽڵ�
			$this->nodes[$cur][3] = trim($replace);
		}
		return $this->nodes;
	}

	function split($str) {
		if (($pos = strrpos($str, '|')) === false) {
			return array($str, 0);
		}
		return explode('|',$str);
	}

    /**
     * ���������ؼ��ֵķ���
     * @param $s string ��Ҫ���ҵ��ı�
     * @return $ret array ���ҵ��Ĺؼ��ʼ�Ȩ��
     */
    function match($ifUppCase,$s) {
    	$ifUppCase == 1 && $s = strtolower($s);
        $isUTF8 = strtoupper(substr($GLOBALS['db_charset'],0,3)) === 'UTF' ? true : false;
        $ret = array();
        $cur = 0; //��ǰ�ڵ㣬��ʼΪ���ڵ�
        $i = 0; //�ַ�����ǰƫ��
        $p = 0; //�ַ�������λ��
        $len = strlen($s);
        while($i < $len) {
            $c = ord($s[$i]);
            if (isset($this->nodes[$cur][1][$c])) { //�������
                $cur = $this->nodes[$cur][1][$c]; //���Ƶ�ǰ�ڵ�
                if ($this->nodes[$cur][0]) { //��Ҷ�ӽڵ㣬����ƥ�䣡
                    $ret[$p] = array(substr($s, $p, $i - $p + 1), $this->nodes[$cur][2]); //ȡ��ƥ��λ�ú�ƥ��Ĵ��Լ��ʵ�Ȩ��
                    $p = $i + 1; //������һ������λ��
                    $cur = 0; //���õ�ǰ�ڵ�Ϊ���ڵ�
                }
				$i++; //��һ���ַ�
            } else { //��ƥ��
				$cur = 0; //���õ�ǰ�ڵ�Ϊ���ڵ�
                if (!$isUTF8 && ord($s[$p]) > 127 && ord($s[$p+1]) > 127) {
					$p += 2; //������һ������λ��
				} else {
					$p += 1; //������һ������λ��
				}
				$i = $p; //�ѵ�ǰƫ����Ϊ����λ��
            }
        }
        return $ret;    
    }

 	function replaces($s,$ifUppCase = 0) {
    	$ifUppCase && $s = strtolower($s);
        $isUTF8 = strtoupper(substr($GLOBALS['db_charset'],0,3)) === 'UTF' ? true : false;
        $ret = array();
        $cur = 0; //��ǰ�ڵ㣬��ʼΪ���ڵ�
        $i = 0; //�ַ�����ǰƫ��
        $p = 0; //�ַ�������λ��
        $len = strlen($s);
        while($i < $len) {
            $c = ord($s[$i]);
            if (isset($this->nodes[$cur][1][$c])) { //�������
                $cur = $this->nodes[$cur][1][$c]; //���Ƶ�ǰ�ڵ�
                if ($this->nodes[$cur][0]) { //��Ҷ�ӽڵ㣬����ƥ�䣡
                    $s = ($this->nodes[$cur][2] == 0.6 && isset($this->nodes[$cur][3])) ? substr_replace($s, $this->nodes[$cur][3], $p, $i - $p + 1) : $s; //ȡ��ƥ��λ�ú�ƥ��Ĵ��Լ��ʵ�Ȩ��
                    $p = $i + 1; //������һ������λ��
                    $cur = 0; //���õ�ǰ�ڵ�Ϊ���ڵ�
                }
				$i++; //��һ���ַ�
            } else { //��ƥ��
				$cur = 0; //���õ�ǰ�ڵ�Ϊ���ڵ�
                if (!$isUTF8 && ord($s[$p]) > 127 && ord($s[$p+1]) > 127) {
					$p += 2; //������һ������λ��
				} else {
					$p += 1; //������һ������λ��
				}
				$i = $p; //�ѵ�ǰƫ����Ϊ����λ��
            }
        }
        return $s;    
    }
}

/**
 * ���ݸ�������Ȩ�ض��ĵ��������֣�Ŀǰʹ��Bayes�㷨�������Ǵ�ƵӰ��
 * �㷨���£�
 * �����ĵ����зִ�t1,t2,t3,����tn,��Ȩ�طֱ�Ϊw1,w2,w3,����,wn
 * �����Bayes�㷨���ĵ�Ȩ��Ϊ��
 * ��p1 = w1*w2*w3*����*wn
 * ��p2 = (1-w1)*(1-w2)*(1-w3)*����*(1-wn)
 * ���ĵ�Ȩ�� w = p1/(p1+p2)
 * ���p1+p2=0,�ĵ�Ȩ��Ϊ1
 * Ȩ�ص���0.5�Ĺؼ��ʻή������Ȩ�أ�����0.5����������Ȩ��
 * ��0.9, 0.8, 0.5, 0.6 ����Bayes�����Ȩ��Ϊ0.98��
 * ��0.9, 0.8, 0.5, 0.1 ���������Ȩ�ؽ�Ϊ0.8
 */
class Bayes {

    /**
     * ��ȡ����Ȩ��
     * @param $keys �ĵ���ƥ��Ĺؼ������鼰Ȩ����Ϣ
     * @return  $weight ����Bayes�㷨�������Ȩ��
     */
    function getWeight($keys) {
		//print_r($keys);
        $p1 = 1;
        $p2 = 1;
        foreach($keys as $key) {
            if( empty($key[1]) ) {
                continue;
            }
            $weight = floatval($key[1]);
            $p1 *= $weight;
            $p2 *= (1- $weight);
        }
        if( ($p1 + $p2) == 0 ) {
            $weight = 1;
            return $weight;
        }

        $weight = $p1 / ($p1 + $p2);
        return $weight;
    }
}
?>
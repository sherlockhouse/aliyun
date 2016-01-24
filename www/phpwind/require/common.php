<?php
defined('P_W') || exit('Forbidden');
define('WIND_VERSION', '8.7,20111111');
define('PW_USERSTATUS_BANUSER', 1); //�Ƿ����λ (������)
define('PW_USERSTATUS_CFGFRIEND', 3); //��������λ
//define('PW_USERSTATUS_NEWPM', 5); //�Ƿ����¶���Ϣ
define('PW_USERSTATUS_NEWRP', 6); //�Ƿ��»ظ�֪ͨ
define('PW_USERSTATUS_PUBLICMAIL', 7); //�Ƿ񹫿�����
define('PW_USERSTATUS_RECEIVEMAIL', 8); //�Ƿ�����ʼ�
define('PW_USERSTATUS_SIGNCHANGE', 9); //ǩ���Ƿ���Ҫת��
define('PW_USERSTATUS_SHOWSIGN', 10); //�Ƿ���ǩ��չʾ����
define('PW_USERSTATUS_EDITOR', 11); //����������
define('PW_USERSTATUS_USERBINDING', 12); //�û��Ƿ��а��ʺ�
define('PW_USERSTATUS_SHOWWIDTHCFG', 13); //�л���խ��ģʽ
define('PW_USERSTATUS_SHOWSIDEBAR', 14); //��������б������л�
define('PW_USERSTATUS_BANSIGNATURE', 15);//�Ƿ��ֹǩ��
define('PW_USERSTATUS_AUTHMOBILE', 16);//�Ƿ��ֻ�ʵ����֤
define('PW_USERSTATUS_AUTHALIPAY', 17);//�Ƿ�֧����ʵ����֤
define('PW_USERSTATUS_REPLYEMAIL', 18);//�ظ������ʼ�֪ͨ
define('PW_USERSTATUS_REPLYSITEEMAIL', 19);//�ظ�վ��֪ͨ
define('PW_USERSTATUS_NOTICEVPICE', 20);//������ʾ��
define('PW_USERSTATUS_AUTHCERTIFICATE', 21);//�Ƿ�֤��ʵ����֤
define ('PW_COLUMN', 'column' ); //��ѯ�ֶ�
define ('PW_EXPR', 'expr' ); //��ѯ���ʽ
define ('PW_ORDERBY', 'orderby' ); //����
define ('PW_GROUPBY', 'groupby' ); //����
define ('PW_LIMIT', 'limit' ); //��ҳ
define ('PW_ASC', 'asc' ); //����
define ('PW_DESC', 'desc' ); //����
define ('PW_CACHE_MEMCACHE', 'memcache' ); //�ڴ滺��
define ('PW_CACHE_FILECACHE', 'filecache' ); //�ļ�����
define ('PW_CACHE_DBCACHE', 'dbcache' ); //���ݿ⻺��
define ('PW_OVERFLOW_NUM',2000000000);	//���������С�ٽ���

define('PW_THREADSPECIALSORT_KMD',50);
define('PW_THREADSPECIALSORT_TOP1',101);
define('PW_THREADSPECIALSORT_TOP2',102);
define('PW_THREADSPECIALSORT_TOP3',103);

//* portal Start*//
define ('PW_PORTAL_MAIN', 'main.htm' ); //���ӻ��ṹ�ļ�
define ('PW_PORTAL_CONFIG', 'config.htm' ); //���ӻ������ļ�
//* portal end*//



require_once(R_P.'require/security.php');


//�������

/**
 * ��ȡ�ͻ���IP
 *
 * @global array $pwServer ȫ��$_SERVER�������
 * @global int $db_xforwardip �Ƿ�������ip
 * @return string
 */
function pwGetIp() {
	global $pwServer, $db_xforwardip;
	if ($db_xforwardip) {
		if ($pwServer['HTTP_X_FORWARDED_FOR'] && $pwServer['REMOTE_ADDR']) {
			if (strstr($pwServer['HTTP_X_FORWARDED_FOR'], ',')) {
				$x = explode(',', $pwServer['HTTP_X_FORWARDED_FOR']);
				$pwServer['HTTP_X_FORWARDED_FOR'] = trim(end($x));
			}
			if (preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $pwServer['HTTP_X_FORWARDED_FOR'])) {return $pwServer['HTTP_X_FORWARDED_FOR'];}
		} elseif ($pwServer['HTTP_CLIENT_IP'] && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $pwServer['HTTP_CLIENT_IP'])) {return $pwServer['HTTP_CLIENT_IP'];}
	}
	$db_xforwardip = 0;
	if (preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $pwServer['REMOTE_ADDR'])) {return $pwServer['REMOTE_ADDR'];}
	return 'Unknown';
}

/**
 * �������л�ȡ$_GET��$_POST����������keyΪ������ע��Ϊȫ�ֱ���
 *
 * @param array|string $keys ����key����key��ɵ����飬����ΪGLOBALS
 * @param string $method P|G
 * @param int $cvtype 0��ʾ��������1��ʾ��Char_cv�����ַ�����2��ʾǿ��ת��Ϊint //TODO ��Ϊ����
 */
function InitGP($keys, $method = null, $cvtype = 1) {
	S::gp($keys, $method, $cvtype);
}

/**
 * �������л�ȡ$_GET��$_POST����
 *
 * @param string $key ����
 * @param string $method P|G
 * @return mixed
 */
function GetGP($key, $method = null) {
	return S::getGP($key, $method);
}

/**
 * ��ȡָ����$_SERVER����
 *
 * @param string|array $keys �������������������ֵ
 * @return string|array ���ݲ�����������ָ����������ֵ
 */
function GetServer($keys) {
	return S::getServer($keys);
}

/**
 * �������л�ȡcookieֵ
 *
 * @param string $cookieName cookie��
 * @return string
 */
function GetCookie($cookieName) {
	return $_COOKIE[CookiePre() . '_' . $cookieName];
}

//��Ӧ�����ݴ���


/**
 * ����cookie
 *
 * @global string $db_ckpath
 * @global string $db_ckdomain
 * @global int $timestamp
 * @global array $pwServer
 * @param string $cookieName cookie��
 * @param string $cookieValue cookieֵ
 * @param int|string $expireTime cookie����ʱ�䣬ΪF��ʾ1������
 * @param bool $needPrefix cookie���Ƿ��ǰ׺
 * @return bool �Ƿ����óɹ�
 */
function Cookie($cookieName, $cookieValue, $expireTime = 'F', $needPrefix = true) {
	global $db_ckpath, $db_ckdomain, $timestamp, $pwServer;
	static $sIsSecure = null;
	if ($sIsSecure === null) {
		if (!$pwServer['REQUEST_URI'] || ($parsed = @parse_url($pwServer['REQUEST_URI'])) === false) {
			$parsed = array();
		}
		if ($parsed['scheme'] == 'https' || (empty($parsed['scheme']) && ($pwServer['HTTP_SCHEME'] == 'https' || $pwServer['HTTPS'] && strtolower($pwServer['HTTPS']) != 'off'))) {
			$sIsSecure = true;
		} else {
			$sIsSecure = false;
		}
	}

	if (P_W != 'admincp') {
		$cookiePath = !$db_ckpath ? '/' : $db_ckpath;
		$cookieDomain = $db_ckdomain;
	} else {
		$cookiePath = '/';
		$cookieDomain = '';
	}
	$isHttponly = false;
	if ($cookieName == 'AdminUser' || $cookieName == 'winduser') {
		$agent = strtolower($pwServer['HTTP_USER_AGENT']);
		if (!($agent && preg_match('/msie ([0-9]\.[0-9]{1,2})/i', $agent) && strstr($agent, 'mac'))) {
			$isHttponly = true;
		}
	}
	$cookieValue = str_replace("=", '', $cookieValue);
	strlen($cookieValue) > 512 && $cookieValue = substr($cookieValue, 0, 512);
	$needPrefix && $cookieName = CookiePre() . '_' . $cookieName;
	if ($expireTime == 'F') {
		$expireTime = $timestamp + 31536000;
	} elseif ($cookieValue == '' && $expireTime == 0) {return setcookie($cookieName, '', $timestamp - 31536000, $cookiePath, $cookieDomain, $sIsSecure);}

	if (PHP_VERSION < 5.2) {
		return setcookie($cookieName, $cookieValue, $expireTime, $cookiePath . ($isHttponly ? '; HttpOnly' : ''), $cookieDomain, $sIsSecure);
	} else {
		return setcookie($cookieName, $cookieValue, $expireTime, $cookiePath, $cookieDomain, $sIsSecure, $isHttponly);
	}
}

/**
 * ѹ�����ݣ���������ӦͷΪѹ����ʽ
 *
 * @global string $db_obstart
 * @param string $output Ҫѹ��������
 * @return string
 */
function ObContents($output) {
	ob_end_clean();
	$getHAE = S::getServer('HTTP_ACCEPT_ENCODING');
	if (!headers_sent() && $GLOBALS['db_obstart'] && $getHAE && N_output_zip() != 'ob_gzhandler') {
		$encoding = '';
		if (strpos($getHAE, 'x-gzip') !== false) {
			$encoding = 'x-gzip';
		} elseif (strpos($getHAE, 'gzip') !== false) {
			$encoding = 'gzip';
		}
		if ($encoding && function_exists('crc32') && function_exists('gzcompress')) {
			header('Content-Encoding: ' . $encoding);
			$outputLen = strlen($output);
			$outputZip = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
			$outputZip .= substr(gzcompress($output, $GLOBALS['db_obstart']), 0, -4);
			$outputZip .= @pack('V', crc32($output));
			$output = $outputZip . @pack('V', $outputLen);
		} else {
			ObStart();
		}
	} else {
		ObStart();
	}
	return $output;
}

/**
 * �����������
 *
 * @return bool
 */
function ObStart() {
	ObGetMode() == 1 ? ob_start('ob_gzhandler') : ob_start();
}

/**
 * �ж����ģʽ�Ƿ�Ϊ��ѹ��
 *
 * @global string $db_obstart
 * @return int 1Ϊ��ѹ��
 */
function ObGetMode() {
	static $sOutputMode = null;
	if ($sOutputMode !== null) {return $sOutputMode;}
	$sOutputMode = 0;
	if ($GLOBALS['db_obstart'] && function_exists('ob_gzhandler') && N_output_zip() != 'ob_gzhandler' && (!function_exists('ob_get_level') || ob_get_level() < 1)) {
		$sOutputMode = 1;
	}
	return $sOutputMode;
}

/**
 * ����������е�����ˢ��
 *
 * @param bool $ob �Ƿ�ʹ��ob_flush
 */
function N_flush($ob = null) {
	if (php_sapi_name() != 'apache2handler' && php_sapi_name() != 'apache2filter') {
		if (N_output_zip() == 'ob_gzhandler') {return;}
		if ($ob && ob_get_length() !== false && ob_get_status() && !ObGetMode($GLOBALS['db_obstart'])) {
			@ob_flush();
		}
		flush();
	}
}

/**
 * �ж�����������������
 *
 * @return string
 */
function N_output_zip() {
	static $sOutputHandler = null;
	if ($sOutputHandler === null) {
		if (@ini_get('zlib.output_compression')) {
			$sOutputHandler = 'ob_gzhandler';
		} else {
			$sOutputHandler = @ini_get('output_handler');
		}
	}
	return $sOutputHandler;
}

/**
 * ����������е�������ajax��ʽ��������жϳ���
 *
 * @global string $db_charset
 */
function ajax_footer() {
	global $db_charset,$db_htmifopen;
	if (defined('SHOWLOG')) Error::writeLog();
	$output = str_replace(array('<!--<!--<!---->','<!--<!---->','<!---->-->','<!---->','<!-- -->'),'', ob_get_contents());
	if (P_W == 'admincp') {
		$output = preg_replace(
			"/\<form([^\<\>]*)\saction=['|\"]?([^\s\"'\<\>]+)['|\"]?([^\<\>]*)\>/ies",
			"FormCheck('\\1','\\2','\\3')",
			rtrim($output,'<!--')
		);
	} else {
		$output = parseHtmlUrlRewrite($output, $db_htmifopen);
	}
	header("Content-Type: text/xml;charset=$db_charset");
	echo ObContents("<?xml version=\"1.0\" encoding=\"$db_charset\"?><ajax><![CDATA[" . $output . "]]></ajax>");
	exit();
}

/**
 * �������ʽ����json��ʽ
 *
 * @param  $type
 * @return string
 */
function pwJsonEncode($var) {
	switch (gettype($var)) {
		case 'boolean' :
			return $var ? 'true' : 'false';
		case 'NULL' :
			return 'null';
		case 'integer' :
			return (int) $var;
		case 'double' :
		case 'float' :
			return (float) $var;
		case 'string' :
			return '"' . addslashes(str_replace(array("\n", "\r", "\t"), '', addcslashes($var, '\\"'))) . '"';
		case 'array' :
			if (count($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) {
				$properties = array();
				foreach ($var as $name => $value) {
					$properties[] = pwJsonEncode(strval($name)) . ':' . pwJsonEncode($value);
				}
				return '{' . join(',', $properties) . '}';
			}
			$elements = array_map('pwJsonEncode', $var);
			return '[' . join(',', $elements) . ']';
	}
	return false;
}

//ȫ��ҵ��


/**
 * ��������������
 *
 * ֻ���*unix��������Ч
 *
 * @param int $maxLoadAvg �������ֵ
 * @return boolean �Ƿ񳬹������
 */
function pwLoadAvg($maxLoadAvg) {
	$avgstats = 0;
	if (@file_exists('/proc/loadavg')) {
		if ($fp = @fopen('/proc/loadavg', 'r')) {
			$avgdata = @fread($fp, 6);
			@fclose($fp);
			list($avgstats) = explode(' ', $avgdata);
		}
	}
	if ($avgstats > $maxLoadAvg) {
		return true;
	} else {
		return false;
	}
}

/**
 * CC��������
 *
 * CC�����ᵼ�·��������ع���,����ؿͻ���������д�����־
 *
 * @global int $timestamp
 * @global string $onlineip
 * @global array $pwServer
 * @global string $db_xforwardip
 * @param int $ccLoad ���������ز���
 * @return void
 */
function pwDefendCc($ccLoad) {
	global $timestamp, $onlineip, $pwServer, $db_xforwardip;
	if ($ccLoad == 2 && !empty($pwServer['HTTP_USER_AGENT'])) {
		$userAgent = strtolower($pwServer['HTTP_USER_AGENT']);
		if (str_replace(array('spider', 'google', 'msn', 'yodao', 'yahoo', 'http:'), '', $userAgent) != $userAgent) {
			$ccLoad = 1;
		}
	}
	Cookie('c_stamp', $timestamp, 0);
	$ccTimestamp = GetCookie('c_stamp');
	$ccCrc32 = substr(md5($ccTimestamp . $pwServer['HTTP_REFERER']), 0, 10);
	$ccBanedIp = readover(D_P . 'data/ccbanip.txt');
	if ($ccBanedIp && $ipOffset = strpos("$ccBanedIp\n", "\t$onlineip\n")) {
		$ccLtt = substr($ccBanedIp, $ipOffset - 10, 10);
		$ccCrc32 == $ccLtt && exit('Forbidden, Please turn off CC');
		pwCache::writeover(D_P . 'data/ccbanip.txt', str_replace("\n$ccLtt\t$onlineip", '', $ccBanedIp));
	}
	if (($db_xforwardip || $ccLoad == 2) && ($timestamp - $ccTimestamp > 3 || $timestamp < $ccTimestamp)) {
		$isCc = false;
		if ($fp = @fopen(D_P . 'data/ccip.txt', 'rb')) {
			flock($fp, LOCK_SH);
			$size = 27 * 800;
			fseek($fp, -$size, SEEK_END);
			while (!feof($fp)) {
				$value = explode("\t", fgets($fp, 29));
				if (trim($value[1]) == $onlineip && $ccCrc32 == $value[0]) {
					$isCc = true;
					break;
				}
			}
			fclose($fp);
		}
		if ($isCc) {
			echo 'Forbidden, Please Refresh';
			$banIps = '';
			$ccBanedIp && $banIps .= implode("\n", array_slice(explode("\n", $ccBanedIp), -999));
			$banIps .= "\n" . $ccCrc32 . "\t" . $onlineip;
			pwCache::writeover(D_P . 'data/ccbanip.txt', $banIps);
			exit();
		}
		@filesize(D_P . 'data/ccip.txt') > 27 * 1000 && P_unlink(D_P . 'data/ccip.txt');
		pwCache::writeover(D_P . 'data/ccip.txt', "$ccCrc32\t$onlineip\n", 'ab');
	}
}

//ȫ�ִ���


/**
 * ɾ������ȫ�ֱ�������GPCF��ת��
 *
 * �����ȫ�ֱ���,���վ�㰲ȫ������в.��Ҫ�����ı�����$allowed��˵��
 *
 */
function pwInitGlobals() {
	S::filter();
}

/**
 * ����cookieǰ׺
 *
 * @global string $cookiepre
 * @global string $db_sitehash
 * @return string
 */
function CookiePre() {
	return ($GLOBALS['db_cookiepre']) ? $GLOBALS['db_cookiepre'] : substr(md5($GLOBALS['db_sitehash']), 0, 5);
}

//�ļ�����


/**
 * ɾ���ļ�
 *
 * @param string $fileName �ļ�����·��
 * @return bool
 */
function P_unlink($fileName) {
	return @unlink(S::escapePath($fileName));
}

/**
 * ��ȡ�ļ�
 *
 * @param string $fileName �ļ�����·��
 * @param string $method ��ȡģʽ
 */
function readover($fileName, $method = 'rb') {
	$fileName = S::escapePath($fileName);
	$data = '';
	if ($handle = @fopen($fileName, $method)) {
		flock($handle, LOCK_SH);
		$data = @fread($handle, filesize($fileName));
		fclose($handle);
	}
	return $data;
}

/**
 * д�ļ�
 *
 * @param string $fileName �ļ�����·��
 * @param string $data ����
 * @param string $method ��дģʽ
 * @param bool $ifLock �Ƿ����ļ�
 * @param bool $ifCheckPath �Ƿ����ļ����еġ�..��
 * @param bool $ifChmod �Ƿ��ļ����Ը�Ϊ�ɶ�д
 * @return bool �Ƿ�д��ɹ�   :ע��rb+�������ļ������ص�false,����wb+
 */
function writeover($fileName, $data, $method = 'rb+', $ifLock = true, $ifCheckPath = true, $ifChmod = true) {
	$fileName = S::escapePath($fileName, $ifCheckPath);
	touch($fileName);
	$handle = fopen($fileName, $method);
	$ifLock && flock($handle, LOCK_EX);
	$writeCheck = fwrite($handle, $data);
	$method == 'rb+' && ftruncate($handle, strlen($data));
	fclose($handle);
	$ifChmod && @chmod($fileName, 0777);
	return $writeCheck;
}

/**
 * ������ʱ��У������ļ��޸�ʱ��
 *
 * @global string $db_cvtime
 * @param string $file �ļ�·��
 * @return int �����޸�ʱ��
 */
function pwFilemtime($file) {
	return file_exists($file) ? intval(filemtime($file) + $GLOBALS['db_cvtime'] * 60) : 0;
}

//�ַ�������


/**
 * ���ܡ������ַ���
 *
 * @global string $db_hash
 * @global array $pwServer
 * @param $string �������ַ���
 * @param $action ������ENCODE|DECODE
 * @return string
 */
function StrCode($string, $action = 'ENCODE') {
	$action != 'ENCODE' && $string = base64_decode($string);
	$code = '';
	$key = substr(md5($GLOBALS['pwServer']['HTTP_USER_AGENT'] . $GLOBALS['db_hash']), 8, 18);
	$keyLen = strlen($key);
	$strLen = strlen($string);
	for ($i = 0; $i < $strLen; $i++) {
		$k = $i % $keyLen;
		$code .= $string[$i] ^ $key[$k];
	}
	return ($action != 'DECODE' ? base64_encode($code) : $code);
}

/**
 * �ض��ַ���
 *
 * @global string $db_charset
 * @param string $content ����
 * @param int $length ��ȡ�ֽ���
 * @param string $add �Ƿ��ʡ�Ժţ�Y|N
 * @return string
 */
function substrs($content, $length, $add = 'Y') {
	if (strlen($content) > $length) {
		if ($GLOBALS['db_charset'] != 'utf-8') {
			$cutStr = '';
			for ($i = 0; $i < $length - 1; $i++) {
				$cutStr .= ord($content[$i]) > 127 ? $content[$i] . $content[++$i] : $content[$i];
			}
			$i < $length && ord($content[$i]) <= 127 && $cutStr .= $content[$i];
			return $cutStr . ($add == 'Y' ? ' ..' : '');
		}
		return utf8_trim(substr($content, 0, $length)) . ($add == 'Y' ? ' ..' : '');
	}
	return $content;
}

/**
 * utf8�ַ������뻯
 *
 * @param string $str
 * @return string
 */
function utf8_trim($str) {
	$hex = '';
	$len = strlen($str) - 1;
	for ($i = $len; $i >= 0; $i -= 1) {
		$ch = ord($str[$i]);
		$hex .= " $ch";
		if (($ch & 128) == 0 || ($ch & 192) == 192) {return substr($str, 0, $i);}
	}
	return $str . $hex;
}

/**
 * ��ȡ����ַ���
 *
 * @param int $length �ַ�������
 * @return string
 */
function randstr($length) {
	return substr(md5(num_rand($length)), mt_rand(0, 32 - $length), $length);
}

/**
 * ��ȡ�����
 *
 * @param int $length ������ָ���
 * @return string
 */
function num_rand($length) {
	mt_srand((double) microtime() * 1000000);
	$randVal = mt_rand(1, 9);
	for ($i = 1; $i < $length; $i++) {
		$randVal .= mt_rand(0, 9);
	}
	return $randVal;
}

function getAllowKeysFromArray($array, $allowKeys) {
	if (!is_array($array) || !is_array($allowKeys)) return array();
	$data = array();
	foreach ($array as $key => $value) {
		in_array($key, $allowKeys) && $data[$key] = $value;
	}
	return $data;
}
/**
 * ��������Ϊ�ַ���
 *
 * @param mixed $input ����
 * @param string $indent ����
 * @return string
 */
function pw_var_export($input, $indent = '') {
	switch (gettype($input)) {
		case 'string' :
			return "'" . str_replace(array("\\", "'"), array("\\\\", "\'"), $input) . "'";
		case 'array' :
			$output = "array(\r\n";
			foreach ($input as $key => $value) {
				$output .= $indent . "\t" . pw_var_export($key, $indent . "\t") . ' => ' . pw_var_export($value, $indent . "\t");
				$output .= ",\r\n";
			}
			$output .= $indent . ')';
			return $output;
		case 'boolean' :
			return $input ? 'true' : 'false';
		case 'NULL' :
			return 'NULL';
		case 'integer' :
		case 'double' :
		case 'float' :
			return "'" . (string) $input . "'";
	}
	return 'NULL';
}

/**
 * ����ת��
 *
 * @uses Chinese
 * @param string $str �����ַ���
 * @param string $toEncoding תΪ�±���
 * @param string $fromEncoding ԭ����
 * @param bool $ifMb �Ƿ�ʹ��mb����
 * @return string
 */
function pwConvert($str, $toEncoding, $fromEncoding, $ifMb = true) {
	if (strtolower($toEncoding) == strtolower($fromEncoding)) {return $str;}
	is_object($str) && $str = get_object_vars($str);//fixed: object can't convert, by alacner 2010/09/15
	if (is_array($str)) {
		foreach ($str as $key => $value) {
			is_object($value) && $value = get_object_vars($value);
			$str[$key] = pwConvert($value, $toEncoding, $fromEncoding, $ifMb);
		}
		return $str;
	} else {
		if (function_exists('mb_convert_encoding') && $ifMb) {
			return mb_convert_encoding($str, $toEncoding, $fromEncoding);
		} else {
			static $sConvertor = null;
			!$toEncoding && $toEncoding = 'GBK';
			!$fromEncoding && $fromEncoding = 'GBK';
			if (!isset($sConvertor) && !is_object($sConvertor)) {
				L::loadClass('Chinese', 'utility/lang', false);
				$sConvertor = new Chinese();
			}
			return $sConvertor->Convert($str, $fromEncoding, $toEncoding, !$ifMb);
		}
	}
}

//���鴦��


/**
 * ֵ�Ƿ���������
 *
 * @param $value ֵ
 * @param $stack ����
 * @return bool
 */
function CkInArray($value, $stack) {
	return S::inArray($value, $stack);
}

/**
 * ��������ȡ��һ��
 *
 * @param $array ����
 * @param $offset ���
 * @param $length ����
 * @param $preserve_keys �Ƿ�ԭ������ļ����� PHP4������
 * @return bool
 */
function PwArraySlice($array,$offset,$length = null,$preserve_keys = null) {
	if (!S::isArray($array))  return array();
	if($preserve_keys == true){
		$tempKey = array_keys($array);
		$result = array();
		foreach($tempKey as $k => $v){
			if($k < $offset + $length && $k >= $offset){
				$result[$k] = $array[$v];
			}
		}
		return $result;
	}
	return array_slice($array, $offset, $length);
}

//���ڴ���


/**
 * ��ʽ��ʱ���Ϊ�����ַ���
 *
 * @global string $db_datefm
 * @global string $db_timedf
 * @global string $_datefm
 * @global string $_timedf
 * @param int $timestamp
 * @param string $format
 * @return string
 */
function get_date($timestamp, $format = null) {
	static $sDefaultFormat = null, $sOffset = null;
	if (!isset($sOffset)) {
		global $db_datefm, $db_timedf, $_datefm, $_timedf;
		$sDefaultFormat = $_datefm ? $_datefm : $db_datefm;
		if ($_timedf && $_timedf != '111') {
			$sOffset = $_timedf * 3600;
		} elseif ($db_timedf && $db_timedf != '111') {
			$sOffset = $db_timedf * 3600;
		} else {
			$sOffset = 0;
		}
	}
	empty($format) && $format = $sDefaultFormat;
	return gmdate($format, $timestamp + $sOffset);
}

/**
 * �����ַ���תΪʱ���
 *
 * @global string $db_timedf
 * @param string $dateString
 * @return int
 */
function PwStrtoTime($dateString) {
	global $db_timedf;
	return function_exists('date_default_timezone_set') ? strtotime($dateString) - $db_timedf * 3600 : strtotime($dateString);
}

//����ҵ��


/**
 * ��ȡ����url
 *
 * @global string $attachdir
 * @global string $attachpath
 * @global string $db_ftpweb
 * @global string $attach_url
 * @param string $relativePath ������Ե�ַ
 * @param string|null $type ������ȡ��Χ
 * @param string|null $isThumb �Ƿ�������ͼ
 * @return mixed
 */
function geturl($relativePath, $type = null, $isThumb = null) {
	global $attachdir, $attachpath, $db_ftpweb, $attach_url;
	if ($isThumb) {
		if (file_exists($attachdir . '/thumb/' . $relativePath)) {
			return array($attachpath . '/thumb/' . $relativePath, 'Local');
		} elseif (file_exists($attachdir . '/' . $relativePath)) {
			return array($attachpath . '/' . $relativePath, 'Local');
		} elseif ($db_ftpweb) {
			$relativePath = 'thumb/' . $relativePath;
		}
	}
	if (file_exists($attachdir . '/' . $relativePath)) {return array($attachpath . '/' . $relativePath, 'Local');}
	if ($db_ftpweb && !$attach_url || $type == 'lf') {return array($db_ftpweb . '/' . $relativePath, 'Ftp');}
	if (!$db_ftpweb && !is_array($attach_url)) {return array($attach_url . '/' . $relativePath, 'att');}
	if (!$db_ftpweb && count($attach_url) == 1) {return array($attach_url[0] . '/' . $relativePath, 'att');}
	if ($type == 'show') {return ($db_ftpweb || $attach_url) ? 'imgurl' : 'nopic';}
	if ($db_ftpweb && $fp = @fopen($db_ftpweb . '/' . $relativePath, 'rb')) {
		@fclose($fp);
		return array($db_ftpweb . '/' . $relativePath, 'Ftp');
	}
	if (!empty($attach_url)) {
		foreach ($attach_url as $value) {
			if ($value != $db_ftpweb && ($fp = @fopen($value . '/' . $relativePath, 'rb'))) {
				@fclose($fp);
				return array($value . '/' . $relativePath, 'att');
			}
		}
	}
	return false;
}

//����ҵ��


/**
 * �޳�WindCode
 *
 * @param string $text
 * @return string
 */
function stripWindCode($text) {
	$pattern = array();
	if (strpos($text, "[post]") !== false && strpos($text, "[/post]") !== false) {
		$pattern[] = "/\[post\].+?\[\/post\]/is";
	}
	if (strpos($text, "[img]") !== false && strpos($text, "[/img]") !== false) {
		$pattern[] = "/\[img\].+?\[\/img\]/is";
	}
	if (strpos($text, "[hide=") !== false && strpos($text, "[/hide]") !== false) {
		$pattern[] = "/\[hide=.+?\].+?\[\/hide\]/is";
	}
	if (strpos($text, "[sell") !== false && strpos($text, "[/sell]") !== false) {
		$pattern[] = "/\[sell=.+?\].+?\[\/sell\]/is";
	}
	$pattern[] = "/\[[a-zA-Z]+[^]]*?\]/is";
	$pattern[] = "/\[\/[a-zA-Z]*[^]]\]/is";

	$text = preg_replace($pattern, '', $text);
	return trim($text);
}

//����ҵ�񣬱�������


/**
 * ��ȡ���ӷֱ����
 *
 * @global array $db_tlist
 * @param int $tid ����id
 * @return string
 */
function GetTtable($tid) {
	global $db_tlist;
	if ($db_tlist && is_array($db_tlist)) {
		foreach ($db_tlist as $key => $value) {
			if ($key > 0 && $tid > $value[1]) {return 'pw_tmsgs' . (int) $key;}
		}
	}
	return 'pw_tmsgs';
}

/**
 * ��ȡ�����ֱ����
 *
 * @global array $db_plist
 * @global DB $db
 * @param int|string $postTableId �ظ��ֱ�id��ΪN���Զ�ȡ
 * @param int $tid ����id
 * @return string
 */
function GetPtable($postTableId, $tid = null) {
	if ($GLOBALS['db_plist'] && is_array($plistdb = $GLOBALS['db_plist'])) {
		if ($postTableId == 'N' && !empty($tid)) {
			$postTableId = $GLOBALS['db']->get_value('SELECT ptable FROM pw_threads WHERE tid=' . S::sqlEscape($tid, false));
		}
		if ((int) $postTableId > 0 && array_key_exists($postTableId, $plistdb)) {return 'pw_posts' . $postTableId;}
	}
	return 'pw_posts';
}

/**
 * ��ȡ�Ź��ֱ�
 *
 * @global string $db_pcids
 * @param int $pcid �Ź�id
 * @return string
 */
function GetPcatetable($pcid) {
	global $db_pcids;
	$pcid = (int) $pcid;
	if ($pcid > 0 && trim($db_pcids, ',')) {
		if (strpos("," . $db_pcids . ",", "," . $pcid . ",") !== false) {return 'pw_pcvalue' . $pcid;}
	}
	Showmsg('undefined_action');
}

/**
 * ��ȡ������Ϣ�ֱ�
 *
 * @global string $db_modelids
 * @param int $modelid ������Ϣid
 * @return string
 */
function GetTopcitable($modelid) {
	global $db_modelids;
	$modelid = (int) $modelid;
	if ($modelid > 0 && trim($db_modelids, ',')) {
		if (strpos("," . $db_modelids . ",", "," . $modelid . ",") !== false) {return 'pw_topicvalue' . $modelid;}
	}
	Showmsg('undefined_action');
}

/**
 * ���ݻ�ӷ���ID��ȡ��������ݵ����ݿ����
 * @param int $actmid ��ӷ���ID actmid
 * @param bool $checkTableExists �Ƿ������ݿ�����
 * @param bool $isUserDefinedField �Ƿ�Ϊ�û��Զ��壨��Ĭ�ϣ����ֶ�
 * @return string ���ݿ����
 */
function getActivityValueTableNameByActmid($actmid = '', $checkTableExists = 1, $isUserDefinedField = 1) {
	global $db_actmids;
	if ($actmid && $isUserDefinedField) { //�û��Զ�����ֶ�
		$actmid = (int) $actmid;
		if (!$checkTableExists || ($actmid > 0 && trim($db_actmids, ',') && strpos("," . $db_actmids . ",", "," . $actmid . ",") !== false)) {return 'pw_activityvalue' . $actmid;}
		Showmsg('undefined_action');
	} else { //Ĭ���ֶ�
		return 'pw_activitydefaultvalue';
	}
}

//���ҵ��


/**
 * ��ǰ��¼�û�������Ȩ��
 *
 * @global string $gp_gptype
 * @global int $winduid
 * @global int $groupid
 * @global int $fid
 * @global DB $db
 * @param string $isBM �û��Ƿ�Ϊ����
 * @param string $rightKey ָ��Ҫ��ȡ��Ȩ����
 * @param integer $fid ���FID
 * @return mixed ����ָ��Ȩ��ֵ
 */
function pwRights($isBM = false, $rightKey = '', $fid = false) {
	static $sForumRights = null;

	if ($GLOBALS['gp_gptype'] != 'system' && $GLOBALS['gp_gptype'] != 'special') return false;

	$uid = (int) $GLOBALS['winduid'];
	$gid = (int) $GLOBALS['groupid'];
	$fid === false && $fid = (int) $GLOBALS['fid'];

	if (empty($uid) || empty($gid) || empty($fid)) return false;

	if (!isset($sForumRights[$fid])) {
		$sForumRights[$fid] = $forumRight = array();
		$isUser = false;

		$pwSQL = 'uid=' . S::sqlEscape($uid, false) . 'AND fid=' . S::sqlEscape($fid, false) . "AND gid='0'";
		if ($isBM && $gid != 5) { //��ȡ����Ȩ��
			$pwSQL .= " OR uid='0' AND fid=" . S::sqlEscape($fid, false) . "AND gid IN ('5'," . S::sqlEscape($gid, false) . ") OR uid='0' AND fid='0' AND gid='5'";
		} else {
			$pwSQL .= " OR uid='0' AND fid=" . S::sqlEscape($fid, false) . "AND gid=" . S::sqlEscape($gid, false);
		}
		$query = $GLOBALS['db']->query("SELECT uid,fid,gid,rkey,rvalue FROM pw_permission WHERE ($pwSQL) AND type='systemforum' ORDER BY uid DESC,fid");
		while ($rt = $GLOBALS['db']->fetch_array($query)) {
			if ($rt['uid'] == $uid) { //�û�����Ȩ��
				$sForumRights[$fid][$rt['rkey']] = $rt['rvalue'];
				$isUser = true;
			} elseif ($isUser) { //ȡ�ø���Ȩ��,����
				break;
			} elseif ($isBM && $rt['gid'] && $gid != $rt['gid']) { //����Ȩ��
				$forumRight[$rt['rkey']] = $rt['rvalue'];
			} else {
				$sForumRights[$fid][$rt['rkey']] = $rt['rvalue'];
			}
		}
		if (!$isUser) {
			empty($sForumRights[$fid]) && ($GLOBALS['SYSTEM']['superright'] || $isBM && $gid == 5) && $sForumRights[$fid] = $GLOBALS['SYSTEM'];
			if ($forumRight) { //����Ȩ�޼�Ȩ
				foreach ($forumRight as $key => $value) {
					$sForumRights[$fid][$key] < $value && $sForumRights[$fid][$key] = $value;
				}
			}
		}
	}
	return empty($rightKey) ? $sForumRights[$fid] : $sForumRights[$fid][$rightKey];
}

/**
 * //TODO ʹ���ѶȺܸ�
 *
 * @param $status
 * @param $b
 * @param $getv
 */
function getstatus($status, $b, $getv = 1) {
	return $status >> --$b & $getv;
}

/**
 * �Ƿ��Ǵ�ʼ��
 *
 * @global array $manager
 * @param string $name �û��ʺ�
 * @return bool
 */
function isGM($name) {
	global $manager;
	return S::inArray($name, $manager);
}

/**
 * �ж��û������û���԰��Ĺ���Ȩ��
 *
 * @param string $name �û���
 * @param bool $isBM  �Ƿ�Ϊ����
 * @param string $type ���磺$pwSystemȨ�ޣ�deltpcs�༭Ȩ��
 * @return bool
 */
function userSystemRight($name, $isBM, $type) {
	$isGM = isGM($name);
	$pwSystem = pwRights($isBM);
	if ($isGM || $pwSystem[$type])  return true;
	return false;
}

/**
 * ��ȡ�û���Ϣ
 *
 * @global DB $db
 * @param int $uid
 * @return array
 */
function getUserByUid($uid) {
	$uid = S::int($uid);
	if ($uid < 1) return false;
	if (perf::checkMemcache()){
		$_cacheService = Perf::getCacheService();
		$detail = $_cacheService->get('member_all_uid_' . $uid);
		if ($detail && in_array(SCR, array('index', 'read', 'thread', 'post'))){
			$_singleRight = $_cacheService->get('member_singleright_uid_' . $uid);
			$detail	= ($_singleRight === false) ? false : (array)$detail + (array)$_singleRight;
		}
		if ($detail){
			return $detail && $detail['groupid'] != 0 && isset($detail['md.uid']) ? $detail : false;
		}
		$cache = perf::gatherCache('pw_members');
		if (in_array(SCR, array('index', 'read', 'thread', 'post'))){
			$detail = $cache->getMembersAndMemberDataAndSingleRightByUserId($uid);
		} else {
			$detail = $cache->getAllByUserId($uid, true, true);
		}
		return $detail && $detail['groupid'] != 0 && isset($detail['md.uid']) ? $detail : false;
	}else {
		global $db;
		$sqladd = $sqltab = '';
		if (in_array(SCR, array('index', 'read', 'thread', 'post'))) {
			$sqladd = (SCR == 'post') ? ',md.postcheck,sr.visit,sr.post,sr.reply' : (SCR == 'read' ? ',sr.visit,sr.reply' : ',sr.visit');
			$sqltab = "LEFT JOIN pw_singleright sr ON m.uid=sr.uid";
		}
		$detail = $db->get_one("SELECT m.uid,m.username,m.password,m.safecv,m.email,m.bday,m.oicq,m.groupid,m.memberid,m.groups,m.icon,m.regdate,m.honor,m.timedf,m.style,m.datefm,m.t_num,m.p_num,m.yz,m.newpm,m.userstatus,m.shortcut,m.medals,m.gender,md.lastmsg,md.postnum,md.rvrc,md.money,md.credit,md.currency,md.lastvisit,md.thisvisit,md.onlinetime,md.lastpost,md.todaypost,md.monthpost,md.onlineip,md.uploadtime,md.uploadnum,md.starttime,md.pwdctime,md.monoltime,md.digests,md.f_num,md.creditpop,md.jobnum,md.lastgrab,md.follows,md.fans,md.newfans,md.newreferto,md.newcomment,md.punch,md.bubble,md.newnotice,md.newrequest,md.shafa $sqladd FROM pw_members m LEFT JOIN pw_memberdata md ON m.uid=md.uid $sqltab WHERE m.uid=" . S::sqlEscape($uid) . " AND m.groupid<>'0' AND md.uid IS NOT NULL");
		return $detail;
	}
}


//����ҵ��


/**
 * �õ���������
 *
 * @global string $db_moneyname
 * @global string $db_rvrcname
 * @global string $db_creditname
 * @global string $db_currencyname
 * @global array $_CREDITDB
 * @param string $creditType ��������
 * @return mixed
 */
function pwCreditNames($creditType = null) {
	static $sCreditNames = null;
	if (!isset($sCreditNames)) {
		$sCreditNames = array('money' => $GLOBALS['db_moneyname'], 'rvrc' => $GLOBALS['db_rvrcname'],
			'credit' => $GLOBALS['db_creditname'], 'currency' => $GLOBALS['db_currencyname']);
		foreach ($GLOBALS['_CREDITDB'] as $key => $value) {
			$sCreditNames[$key] = $value[0];
		}
	}
	return isset($creditType) ? $sCreditNames[$creditType] : $sCreditNames;
}

/**
 * ��ȡ���ֵ�λ
 *
 * @global string $db_moneyunit
 * @global string $db_rvrcunit
 * @global string $db_creditunit
 * @global string $db_currencyunit
 * @global string $_CREDITDB
 * @param string $creditType ��������
 * @return string
 */
function pwCreditUnits($creditType = null) {
	static $sCreditUnits = null;
	if (!isset($sCreditUnits)) {
		$sCreditUnits = array('money' => $GLOBALS['db_moneyunit'], 'rvrc' => $GLOBALS['db_rvrcunit'],
			'credit' => $GLOBALS['db_creditunit'], 'currency' => $GLOBALS['db_currencyunit']);
		foreach ($GLOBALS['_CREDITDB'] as $key => $value) {
			$sCreditUnits[$key] = $value[1];
		}
	}
	return isset($creditType) ? $sCreditUnits[$creditType] : $sCreditUnits;
}

//appҵ��


/**
 * ��ȡ�û���Ψһ�ַ���
 *
 * @global string $db_hash
 * @param int $uid
 * @param string $app
 * @param string $add
 */
function appkey($uid, $app = false, $add = false) {
	global $db_hash;
	return substr(md5($uid . $db_hash . ($add ? $add : '') . ($app ? $app : '')), 8, 18);
}

//�û�ҵ��


/**
 * ��������
 *
 * @global array $pwServer
 * @global string $db_hash
 * @param string $pwd ����
 * @return string
 */
function PwdCode($pwd) {
	return md5($GLOBALS['pwServer']['HTTP_USER_AGENT'] . $pwd . $GLOBALS['db_hash']);
}

/**
 * ���cookie�Ƿ����
 *
 * @global int $timestamp
 * @param array $cookieData cookie����
 * @param string $pwdCode �û�˽����Ϣ
 * @param string $cookieName cookie��
 * @param int $expire ��������
 * @param bool $clearCookie ��֤�����Ƿ����cookie
 * @param bool $refreshCookie �Ƿ�ˢ��cookie
 * @return bool
 */
function SafeCheck($cookieData, $pwdCode, $cookieName = 'AdminUser', $expire = 1800, $clearCookie = true , $refreshCookie = true) {
	global $timestamp, $db_cloudgdcode, $keepCloudCaptchaCode,$db_hash;
	if (strtolower($cookieName) == 'cknum' && $db_cloudgdcode) {
		$cloudCaptchaService = L::loadClass('cloudcaptcha', 'utility/captcha');
		list($sessionid, $cloudckfailed) = array(getCookie('cloudcksessionid'), getCookie('cloudckfailed'));
		$cloudckfailed && Cookie('cloudckfailed', '', 0);
		$delflag = ($refreshCookie && !$keepCloudCaptchaCode) ? null : 0;
		if (!$cloudckfailed) return $cloudCaptchaService->checkCode($sessionid, $pwdCode, $delflag);
	}
	if($timestamp - $cookieData[0] > $expire) {
		Cookie($cookieName, '', 0);
		return false;
	} elseif ($cookieData[2] != md5($pwdCode . $cookieData[0] .getHashSegment())) {
		$clearCookie && Cookie($cookieName, '', 0);
		return false;
	}
	if ($refreshCookie) {
		$cookieData[0] = $timestamp;
		$cookieData[2] = md5($pwdCode . $cookieData[0] .getHashSegment());
		Cookie($cookieName, StrCode(implode("\t", $cookieData)));
	}
	return true;
}

/**
 * ����Ƿ�����
 *
 * @global int $db_onlinetime
 * @global int $timestamp
 * @param int $time ʱ���
 * @return bool
 */
function checkOnline($time) {
	global $db_onlinetime, $timestamp;
	if ($time + $db_onlinetime * 1.5 > $timestamp) {return true;}
	return false;
}

//sql��ȫ����װ


/**
 * ���SQL���ı������з�б�߹��ˣ���������ӵ�����
 *
 * @param mixed $var ����ǰ����
 * @param boolean $strip �����Ƿ񾭹�stripslashes����
 * @param boolean $isArray �����Ƿ�Ϊ����
 * @return mixed ���˺���ַ���
 */
function pwEscape($var, $strip = true, $isArray = false) {
	return S::sqlEscape($var, $strip, $isArray);
}

/**
 * ��������ÿ��Ԫ��ֵ���õ��������𣬲��ö�������
 *
 * @param array $array Դ����
 * @param boolean $strip �����Ƿ񾭹�stripslashes����
 * @return string �ϲ����ַ���
 */
function pwImplode($array, $strip = true) {
	return S::sqlImplode($array, $strip);
}

/**
 * ���쵥��¼���ݸ���SQL���
 * ��ʽ: field='value',field='value'
 *
 * @param array $array ���µ�����,��ʽ: array(field1=>'value1',field2=>'value2',field3=>'value3')
 * @param bool $strip �����Ƿ񾭹�stripslashes����
 * @return string SQL���
 */
function pwSqlSingle($array, $strip = true) {
	return S::sqlSingle($array, $strip);
}

/**
 * �����������ݸ���SQL���
 * ��ʽ: ('value1[1]','value1[2]','value1[3]'),('value2[1]','value2[2]','value2[3]')
 *
 * @param array $array ���µ�����,��ʽ: array(array(value1[1],value1[2],value1[3]),array(value2[1],value2[2],value2[3]))
 * @param boolean $strip �����Ƿ񾭹�stripslashes����
 * @return string SQL���
 */
function pwSqlMulti($array, $strip = true) {
	return S::sqlMulti($array, $strip);
}

/**
 * SQL��ѯ��,����LIMIT���
 *
 * @param int $start ��ʼ��¼λ��
 * @param int $num ��ȡ��¼��Ŀ
 * @return string SQL���
 */
function pwLimit($start, $num = false) {
	return S::sqlLimit($start, $num);
}

//·����ȫ


/**
 * ����·���е�Σ���ַ�
 *
 * @param string $fileName �ļ�·��
 * @param bool $ifCheck �Ƿ��顰..��
 * @return string
 */
function Pcv($fileName, $ifCheck = true) {
	return S::escapePath($fileName, $ifCheck);
}

/**
 * ����Ŀ¼·��Σ���ַ�
 *
 * @param string $dir Ŀ¼·��
 * @return string
 */
function pwDirCv($dir) {
	return S::escapeDir($dir);
}

//���ݰ�ȫ


/**
 * �������ݣ���xss����
 *
 * @param mixed $mixed
 * @param bool $isint �Ƿ�������
 * @param bool $istrim �Ƿ���Ҫ���뻯
 * @return mixed
 */
function Char_cv($mixed, $isint = false, $istrim = false) {
	return S::escapeChar($mixed, $isint, $istrim);
}

/**
 * ������
 *
 * @param mixed $var
 * @return mixed
 */
function CheckVar(&$var) {
	S::checkVar($var);
}

/**
 * ��ת��
 *
 * @param mixed $array
 */
function Add_S(&$array) {
	S::slashes($array);
}

//���԰�


/**
 * ��ȡָ�����԰����ĳһ������Ϣ
 *
 * @param string $T ���԰��ļ���
 * @param string $I ָ��������Ϣ
 * @param array $L �������
 * @param bool $M �Ƿ����ģʽ�µ������ļ�
 * @return string
 */
function getLangInfo($T, $I, $L = array(), $M = false) {
	static $lang;
	if (!isset($lang[$T])) {
		if ($M == false) {
			require S::escapePath(GetLang($T));
		} else {
			require S::escapePath(getModeLang($T));
		}
	}
	if (isset($lang[$T][$I])) {
		eval('$I="' . addcslashes($lang[$T][$I], '"') . '";');
	}
	return $I;
}

/**
 * ��ȡ�������԰���Ϣ
 *
 * @param string $T ���԰��ļ���
 * @param string $logtype ����
 * @return string
 */
function GetCreditLang($T, $logtype) {
	static $lang;
	if (!isset($lang[$T])) {
		require S::escapePath(GetLang($T));
	}
	$pop = '';
	if (isset($lang[$T][$logtype])) {
		eval('$pop="' . addcslashes($lang[$T][$logtype], '"') . '";');
	}
	return $pop;
}

/**
 * ��ȡģʽ���԰���Ϣ
 *
 * @param string $lang ���԰��ļ���
 * @param string $EXT ���԰��ļ���չ��
 * @return string
 */
function getModeLang($lang, $EXT = 'php') {
	if (defined('M_P') && file_exists(M_P . "lang/lang_$lang.$EXT")) {
		return M_P . "lang/lang_$lang.$EXT";
	} else {
		return GetLang($lang);
	}
}

//ģ�崦��

/**
 * ��ȡģʽ�µ�ģ���ļ�·��
 *
 * @global string $db_mode
 * @global string $db_tplpath
 * @param string $template ģ����
 * @param string $ext ��չ��
 * @return string
 */
function modeEot($template, $EXT = 'htm') {
	global $db_mode;
	if ($db_mode == 'area') {
		return areaEot($template, $EXT);
	} else {
		$srcTpl = M_P . "template/$template.$EXT";
		$tarTpl = D_P . "data/tplcache/" . $db_mode . '_' . $template . '.' . $EXT;
	}
	if (!file_exists($srcTpl)) {return false;}
	if (pwFilemtime($tarTpl) > pwFilemtime($srcTpl)) {
		return $tarTpl;
	} else {
		return modeTemplate($srcTpl, $tarTpl);
	}
}

function areaEot($template, $EXT = 'htm') {
	global $alias;
	$ifNeedParsePW = 0;
	$srcTpl = getAreaChannelTpl($template, $alias, $EXT);
	$tarTpl = D_P . "data/tplcache/area_" . $alias . '_' . $template . '.' . $EXT;
	if (!file_exists($srcTpl)) return false;
	$srcTplTime = pwFilemtime($srcTpl);
	if ($template == 'main') {
		$ifNeedParsePW = 1;
		$configFile = AREA_PATH . $alias . '/'.PW_PORTAL_CONFIG;
		$srcTplTime = max(pwFilemtime($configFile), $srcTplTime);
	}
	if (pwFilemtime($tarTpl) > $srcTplTime) {return $tarTpl;}
	if ($ifNeedParsePW) {return areaTemplate($alias, $srcTpl, $tarTpl);}
	return modeTemplate($srcTpl, $tarTpl);
}

function getAreaChannelTpl($template, $channel, $EXT = 'htm') {
	$srcTpl = S::escapePath(AREA_PATH . "$channel/$template.$EXT");
	if (!file_exists($srcTpl)) {
		
		$srcTpl = M_P . 'template/' . $template . '.' . $EXT;

		if (!file_exists($srcTpl)) {
			$srcTpl = R_P . 'template/wind/' . $template . '.' . $EXT;
			if (!file_exists($srcTpl)) {
				Showmsg('the template file is not exists');
			}
		}
	}
	return $srcTpl;
}
/**
 * ��ȡ���ӻ�ģ���ļ�
 * @param $sign
 */
function portalEot($sign) {
	$GLOBALS['__pwPortalEot'] = 1;
	$srcTpl = S::escapePath(PORTAL_PATH . $sign . '/'.PW_PORTAL_MAIN);
	$tarTpl = S::escapePath(D_P . "data/tplcache/portal_" . $sign . '.htm');

	$configFile = S::escapePath(PORTAL_PATH . $sign . '/'.PW_PORTAL_CONFIG);
	$srcTplTime = max(pwFilemtime($configFile), pwFilemtime($srcTpl));

	if (pwFilemtime($tarTpl) <= $srcTplTime) {
		$portalPageService = L::loadClass('portalpageservice', 'area');
		$portalPageService->updateInvokesByModuleConfig($sign);

		$file_str = readover($srcTpl);
		$parseTemplate = L::loadClass('parsetemplate', 'area');
		$file_str = $parseTemplate->execute('other', $sign, $file_str);

		pwCache::writeover($tarTpl, $file_str);
	}
	return $tarTpl;
}

/*
 * ������ӻ�������
 */
function portalEcho($sign,$_viewer = '',$name='') {
	$GLOBALS['__pwPortalEot'] = 1;
	global $timestamp;
	extract(pwCache::getData(D_P.'data/bbscache/portal_config.php', false));
	extract(pwCache::getData(D_P.'data/bbscache/portalhtml_config.php', false));
	
	$staticPath = S::escapePath(PORTAL_PATH . $sign .'/index.html');
	$mainFile = S::escapePath(PORTAL_PATH . $sign . '/'.PW_PORTAL_MAIN);
	$configFile = S::escapePath(PORTAL_PATH . $sign . '/'.PW_PORTAL_CONFIG);

	//$staticFileMTime = pwFilemtime($staticPath);
	$staticFileMTime = isset($portalhtml_times[$sign]) ? $portalhtml_times[$sign] : 0;
	$tplFileMTime = max(pwFilemtime($mainFile),pwFilemtime($configFile));
	if (($tplFileMTime>$staticFileMTime || ($GLOBALS['db_portalstatictime'] && $staticFileMTime<$timestamp-$GLOBALS['db_portalstatictime']*60) || !filesize($staticPath) || $portal_staticstate[$sign])) {
		portalStatic($sign,$_viewer,$name);
	}
	$output .= pwCache::getData($staticPath,false,true);

	echo '-->'. $output . '<!--';
}
/**
 * ���¿��ӻ�ҳ��ľ�̬�ļ�
 * @param $sign
 */
function portalStatic($sign,$_viewer = '',$name='') {
	$portalPageService = L::loadClass('portalpageservice', 'area');
	if (!$portalPageService->checkPortal($sign)) {
		if ($name) {
			$portalPageService->addPortalPage(array('sign'=>$sign,'title'=>$name));
		} else {
			Showmsg('����portalEcho���ó��������ñ������ĵ���������������õ���ҳ�������');
		}
	}
	$lockName = 'portal_'.$sign;
	if (!procLock($lockName)) return false;
	$staticPath = S::escapePath(PORTAL_PATH.$sign);
	if (!is_dir($staticPath)) return false;
	$staticPath = S::escapePath(PORTAL_PATH.$sign.'/index.html');
	$otherOutput = ob_get_contents();
	ob_clean();

	$invokeService = L::loadClass('invokeservice', 'area');
	$pageConfig = $invokeService->getEffectPageInvokePieces('other', $sign);
	$tplGetData = L::loadClass('tplgetdata', 'area');
	$tplGetData->init($pageConfig);
	require portalEot($sign);

	$temp = ob_get_contents();
	$temp = str_replace(array('<!--<!---->',"<!---->\r\n",'<!---->','<!-- -->',"\t\t\t"),'',$temp);
	//$success = pwCache::writeover($staticPath, $temp,'wb+');
	$success = pwCache::setData($staticPath, $temp,false,'wb+');
	procUnLock($lockName);
	if (!$success && !$GLOBALS ['db_distribute'] && !pwCache::writeover($staticPath, $temp)&& !is_writable($staticPath)) {	//д����γ���
		ob_end_clean();
		ObStart();
		Showmsg('������'.str_replace(R_P,'',$staticPath).'�ļ�Ϊ��д������ļ������ڣ����½�һ�����ļ�');
	}
	ob_clean();
	$portalPageService->setPortalStaticState($sign,0);
	updateCacheData();
	setPortalHtmlTime($sign);
	if ($otherOutput) echo $otherOutput;
}

function setPortalHtmlTime($sign) {
	global $timestamp;
	require_once(R_P.'admin/cache.php');
	extract(pwCache::getData(D_P.'data/bbscache/portalhtml_config.php', false));
	if (!$portalhtml_times) $portalhtml_times = array();
	$portalhtml_times[$sign] = $timestamp;
	setConfig('portalhtml_times', $portalhtml_times, null,true);
	updatecache_conf('portalhtml', true);
}

function modeTemplate($srcTpl, $tarTpl) {
	$file_str = readover($srcTpl);
	$file_str = tplParsePrint($file_str);
	pwCache::writeover($tarTpl, $file_str);
	return $tarTpl;
}

/**
 * ����������<!--#$condition#-->�ı�ǩ
 *
 */
function tplParsePrint($string) {
	$s = array('/<!--#\s*/', '/\s*#-->/', '/\s*print <<<EOT\s*EOT;\s*/', '/print <<<EOT\s*/', '/\s*EOT;/');
	$e = array("\r\nEOT;\r\n", "\r\nprint <<<EOT\r\n", "\r\n", "print <<<EOT\r\n", "\r\nEOT;");
	return preg_replace($s, $e, $string);
}

function areaTemplate($alias, $srcTpl, $tarTpl) {
	$portalPageService = L::loadClass('portalpageservice', 'area');
	$portalPageService->updateInvokesByModuleConfig($alias);

	$file_str = readover($srcTpl);

	$parseTemplate = L::loadClass('parsetemplate', 'area');
	$file_str = $parseTemplate->execute('channel', $alias, $file_str);

	pwCache::writeover($tarTpl, $file_str);
	return $tarTpl;
}

/**
 * validate page
 * @param int $page
 * @param int $pageCount
 * @return int $page
 */
function validatePage($page, $pageCount) {
	if (empty($page) || $page < 1) {
		$page = 1;
	} elseif ($page > $pageCount) {
		$page = $pageCount;
	}
	return $page;
}

//ģ�����

/**
 * ��ȡģ������
 *
 * @param string $invokeName
 * @param string $title
 * @param int $loopId
 * @return string
 */
function pwTplGetData($invokeName, $title) {
	$GLOBALS['__pwTplGetData'] = true;
	$tplgetdata = L::loadClass('tplgetdata', 'area');
	return $tplgetdata->getData($invokeName, $title);
}

/**
 * ����ģ�建������
 */
function updateCacheData() {
	if ($GLOBALS['__pwTplGetData']) {
		$pw_tplgetdata = L::loadClass('tplgetdata', 'area');
		if ($pw_tplgetdata->updates) {
			$pw_cachedata = L::loadDB('cachedata', 'area');
			$pw_cachedata->updates($pw_tplgetdata->updates);
		}
	}
}
//��ҳ����

function getHashSegment($s = 2){
	global $db_hash;
	$s = intval($s);
	$s > 3 && $s = 0;
	return substr(md5($db_hash), $s * 8,8);
}

/**
 * ���ɷ�ҳhtml
 *
 * @param int $count �ܼ�¼��
 * @param int $page ��ǰҳ
 * @param int $numofpage ��ҳ��
 * @param string $url
 * @param int $max ��ʾҳ��
 * @param string $ajaxCallBack
 * @return string
 */
function numofpage($count, $page, $numofpage, $url, $max = null, $ajaxCallBack = '') {
	list($count, $page, $numofpage, $max) = array(intval($count), intval($page), intval($numofpage), intval($max));
	if ($numofpage <= 1) return '';
	($max && $numofpage > $max) && $numofpage = $max;
	
	$ajaxurl = $ajaxCallBack ? " onclick=\"return $ajaxCallBack(this.href);\"" : '';
	list($url, $mao) = explode('#', $url);
	$mao && $mao = '#' . $mao;
	$pages = '<div class="pages">';
	$preArrow = $nextArrow = $firstPage = $lastPage = '';
	if ($numofpage > 7) {
		list($pre, $next) = array($page - 1, $page + 1);
		$page > 1 && $preArrow = "<a class=\"pages_pre\" href=\"{$url}page={$pre}$mao\"{$ajaxurl}>&#x4E0A;&#x4E00;&#x9875;</a>";
		$page < $numofpage && $nextArrow = "<a class=\"pages_next\" href=\"{$url}page={$next}$mao\"{$ajaxurl}>&#x4E0B;&#x4E00;&#x9875;</a>";		
	}
	$page != 1 && $firstPage = "<a href=\"{$url}page=1$mao\"{$ajaxurl}>" . (($numofpage > 7 && $page - 3 > 1) ? '1...</a>' : '1</a>');
	$page != $numofpage && $lastPage = "<a href=\"{$url}page={$numofpage}$mao\"{$ajaxurl}>" . (($numofpage > 7 && $page + 3 < $numofpage) ? "...$numofpage</a>" : "$numofpage</a>");
	
	list($tmpPages, $preFlag, $nextFlag) = array('', 0, 0);
	$leftStart = ($numofpage - $page >= 3) ? $page - 2 : $page - (5 - ($numofpage - $page));
	for ($i = $leftStart; $i < $page; $i++) {
		if ($i <= 1) continue;
		$tmpPages .= "<a href=\"{$url}page=$i$mao\"{$ajaxurl}>$i</a>";
		$preFlag++;
	}
	$tmpPages .= "<b>$page</b>";
	$nextFlag = 4 - $preFlag + (!$firstPage ? 1 : 0);
	if ($page < $numofpage) {
		for ($i = $page + 1; $i < $numofpage && $i <= $page + $nextFlag; $i++) {
			$tmpPages .= "<a href=\"{$url}page=$i$mao\"{$ajaxurl}>$i</a>";
		}
	}
	$pages .= $preArrow . $firstPage . $tmpPages . $lastPage . $nextArrow;
	$jsString = "var page=(value>$numofpage) ? $numofpage : value; " . ($ajaxurl ? "$ajaxCallBack('{$url}page='+page);" : " location='{$url}page='+page+'{$mao}';") . " return false;";
	$numofpage > 7 && $pages .= "<div class=\"fl\">&#x5230;&#x7B2C;</div><input type=\"text\" size=\"3\" onkeydown=\"javascript: if(event.keyCode==13){var value = parseInt(this.value); $jsString}\"><div class=\"fl\">&#x9875;</div><button onclick=\"javascript:var value = parseInt(this.previousSibling.previousSibling.value); $jsString\">&#x786E;&#x8BA4;</button>";
	$pages .= '</div>';
	return $pages;
}

/**
 * ��ȡ�Ѻõ�ʱ����Ϣ
 *
 * @global int $timestamp
 * @global string $tdtime
 * @param int $time ʱ���
 * @param int $type ����
 * @return array
 */
function getLastDate($time, $type = 1) {
	global $timestamp, $tdtime;
	static $timelang = false;
	if ($timelang == false) {
		$timelang = array('second' => getLangInfo('other', 'second'), 'yesterday' => getLangInfo('other', 'yesterday'),
			'hour' => getLangInfo('other', 'hour'), 'minute' => getLangInfo('other', 'minute'),
			'qiantian' => getLangInfo('other', 'qiantian'));
	}
	$decrease = $timestamp - $time;
	$thistime = PwStrtoTime(get_date($time, 'Y-m-d'));
	$thisyear = PwStrtoTime(get_date($time, 'Y'));
	$thistime_without_day = get_date($time, 'H:i');
	$yeartime = PwStrtoTime(get_date($timestamp, 'Y'));
	$result = get_date($time);
	if ($decrease <= 0) {
		if ($type == 1) {
			return array(get_date($time, 'Y-m-d'), $result);
		} else {
			return array(get_date($time, 'Y-m-d H:i'), $result);
		}
	}
	if ($thistime == $tdtime) {
		if ($type == 1) {
			if ($decrease <= 60) {return array($decrease . $timelang['second'], $result);}
			if ($decrease <= 3600) {
				return array(ceil($decrease / 60) . $timelang['minute'], $result);
			} else {
				return array(ceil($decrease / 3600) . $timelang['hour'], $result);
			}
		} else {
			return array(get_date($time, 'H:i'), $result);
		}
	} elseif ($thistime == $tdtime - 86400) {
		if ($type == 1) {
			return array($timelang['yesterday'] . " " . $thistime_without_day, $result);
		} else {
			return array(get_date($time, 'm-d H:i'), $result);
		}
	} elseif ($thistime == $tdtime - 172800) {
		if ($type == 1) {
			return array($timelang['qiantian'] . " " . $thistime_without_day, $result);
		} else {
			return array(get_date($time, 'm-d H:i'), $result);
		}
	} elseif ($thisyear == $yeartime) {
		if ($type == 1) {
			return array(get_date($time, 'm-d'), $result);
		} else {
			return array(get_date($time, 'm-d H:i'), $result);
		}
	} else {
		if ($type == 1) {
			return array(get_date($time, 'Y-m-d'), $result);
		} else {
			return array(get_date($time, 'Y-m-d H:i'), $result);
		}
	}
}

//���湤��

/**
 * ����ʵ��������
 *
 * @param string $datastore ��������
 * @return PW_Memcache|PW_DBCache
 */
function getDatastore($datastore = null) {
	global $db_datastore;
	$datastore || $datastore = $db_datastore;
	switch (strtolower($datastore)) {
		case 'memcache' :
			$_cache = L::loadClass('Memcache', 'utility');
			break;
		case 'dbcache' :
			$_cache = L::loadClass('DBCache', 'utility');
			break;
		default :
			$_cache = L::loadClass('DBCache', 'utility');
			break;
	}
	return $_cache;
}

//���ҵ��

/**
 * ��ȡ�������
 *
 * @global int $timestamp
 * @global string $db_advertdb
 * @global string $db_mode
 * @global array $_time
 * @param string $advKey ���key
 * @param int $fid ���id
 * @param int $lou ¥��
 * @param string $scr
 * @return array
 */
function pwAdvert($advKey, $fid = 0, $lou = -1, $scr = 0) {
	global $timestamp, $db_advertdb, $db_mode, $_time;
	if (empty($db_advertdb[$advKey])) return false;
	$hours = $_time['hours'] + 1;
	$fid || $fid = $GLOBALS['fid'];
	$scr || $scr = $GLOBALS['SCR'];
	$scr = strtolower($scr);
	$lou = (int) $lou;
	$tmpAdvert = $db_advertdb[$advKey];
	if ($db_advertdb['config'][$advKey] == 'rand') {
		shuffle($tmpAdvert);
	}
	$arrAdvert = array();
	$advert = '';
	foreach ($tmpAdvert as $key => $value) {
		if ($value['stime'] > $timestamp || $value['etime'] < $timestamp || ($value['dtime'] && strpos(",{$value['dtime']},", ",{$hours},") === false) || ($value['mode'] && strpos($value['mode'], $db_mode) === false) || ($value['page'] && (strpos($value['page'], ",$scr,") === false || ($scr == 'read' && $value['page'] == 'thread'))) || ($value['fid'] && $scr != 'index' && strpos(",{$value['fid']},", ",$fid,") === false) || ($value['lou'] && strpos(",{$value['lou']},", ",$lou,") === false)) {
			continue;
		}
		if ((!$value['ddate'] && !$value['dweek']) || ($value['ddate'] && strpos(",{$value['ddate']},", ",{$_time['day']},") !== false) || ($value['dweek'] && strpos(",{$value['dweek']},", ",{$_time['week']},") !== false)) {
			$arrAdvert[] = $value['code'];
			$advert .= is_array($value['code']) ? $value['code']['code'] : $value['code'];
			if ($db_advertdb['config'][$advKey] != 'all') break;
		}
	}
	return array($advert, $arrAdvert);
}

/**
 * ��������ѡ��html
 *
 * @param string $name
 * @param string $value ѡ��ֵ
 * @param array $options ����ѡ��ѡ������
 * @param string $attrs
 * @param string $isempty �Ƿ��п�ѡ��
 * @return string
 */
function formSelect($name, $value = null, $options = array(), $attrs = "", $isEmpty = "") {
	$html = '<select name="' . $name . '"';
	if ($name == "disabled") {
		$html .= ' disabled';
	}
	$html .= ' ' . $attrs . '>';
	if ($isEmpty != '') {
		$html .= '<option value="">' . $isEmpty . '</option>';
	}
	foreach ($options as $k => $v) {
		$html .= '<option value="' . $k . '"';
		if (null !== $value && $k == $value) {
			$html .= ' selected="selected"';
		}
		$html .= '>' . $v . '</option>';
	}
	$html .= '</select>';
	return $html;
}

/**
 * @param array $_define   Ĭ��seo����
 * @param unknown_type $_values	��Ӧ targets ��һ��ֵ
 * @param unknown_type $_targets
 * @return multitype:string
 */
function seoSettings($_define = array(), $_values = array(), $_default = array(), $_targets = array()) {
	global $db_bbsname, $webPageTitle, $metaDescription, $metaKeywords;

	if (empty($_targets)) $_targets = array('{wzmc}', '{bkmc}', '{flmc}', '{tzmc}', '{tmc}', '{wzgy}','{pdmc}');
	if (empty($_default)) $_default = array('title' => '{tzmc} | {bkmc} - {wzmc}', 'descp' => '{wzgy} | {wzmc}',
		'keywords' => '{flmc} , {tzmc} | {bkmc} - {wzmc}');

	if (!empty($_define)) {
		$cTitle = $_define['title'];
		$cDescription = $_define['metaDescription'];
		$cKeywords = $_define['metaKeywords'];
	}

	if (empty($_values[0])) $_values[0] = $db_bbsname;
	/* ���˲��� */
	foreach ($_values as $key => $value) {
		$_values[$key] = empty($value) ? '' : trim(strip_tags($value));
	}

	/*����Ĭ��ֵ*/
	empty($cTitle) && $cTitle = $_default['title'];
	empty($cDescription) && $cDescription = $_default['descp'];
	empty($cKeywords) && $cKeywords = $_default['keywords'];

	/* �������� */
	$webPageTitle = parseSeoTargets($cTitle, $_values, $_targets);
	$metaDescription = parseSeoTargets($cDescription, $_values, $_targets);
	$metaKeywords = parseSeoTargets($cKeywords, $_values, $_targets);

	return array($webPageTitle, $metaDescription, $metaKeywords);
}

/**
 * @param string $_page ��ǰҳ����Ϣ
 * @param string $_definedSeo �Զ���SEO������Ϣ
 * @param string $_fname �������
 * @param string $_types ������Ϣ
 * @param string $_subject ��������
 * @param string $_tags ��ǩ
 * @param string $_summary ժҪ
 */
function bbsSeoSettings($_page = 'index', $_definedSeo = array(), $_fname = '', $_types = '', $_subject = '', $_tags = '', $_summary = '') {
	global $db_bbsname, $db_seoset;
	/* ��վ���ƣ�������ƣ��������ƣ��������ƣ���ǩ���ƣ����¸�Ҫ  */
	$_tags = substr($_tags, 0, strpos($_tags, "\t"));
	$_types = isset($_types) && is_array($_types) ? $_types['name'] : '';
	$_replace = array($db_bbsname, $_fname, $_types, $_subject, $_tags, $_summary);
	/*��ȡSEO������Ϣ  �Զ���->��̨����->Ĭ��*/
	empty($_definedSeo['title']) && $_definedSeo['title'] = $db_seoset['title'][$_page];
	empty($_definedSeo['metaDescription']) && $_definedSeo['metaDescription'] = $db_seoset['metaDescription'][$_page];
	empty($_definedSeo['metaKeywords']) && $_definedSeo['metaKeywords'] = $db_seoset['metaKeywords'][$_page];
	return seoSettings($_definedSeo, $_replace, $_default, $_targets);
}

/**
 * @param string $content
 * @param string $_replace
 * @param string $_targets
 * @return string
 */
function parseSeoTargets($content, $_replace, $_targets) {
	$content = str_replace($_targets, $_replace, $content);
	$content = trim(preg_replace(array('((\s*\,\s*)+)', '((\s*\|\s*)+)', '((\s*\t\s*)+)'), array(
	',', '|', '', ''), $content), ' -,|');
	return $content;
}

/**
 * �ж��û��Ƿ���ǰ̨���ӻ�����Ȩ��
 */
function checkPortalRight() {
	global $db_portal_admins,$manager,$winduid,$windid;
	return S::inArray($windid,$manager) || ($winduid && in_array($winduid, $db_portal_admins));
}

function descriplog($message) {
	$message = str_replace(array("\n \n \n", "\n",'[b]','[/b]'),array('<br />','<br />','<b>','</b>'),$message);
	if (strpos($message,'[/URL]')!==false || strpos($message,'[/url]')!==false) {
		$message = preg_replace("/\[url=([^\[]+?)\](.*?)\[\/url\]/is","<a href=\"\\1\" target=\"_blank\">\\2</a>",$message);
	}
	return $message;
}

function parseHtmlUrlRewrite($html, $flag) {
	return $flag ? preg_replace("/\<a(\s*[^\>]+\s*)href\=([\"|\']?)((index|cate|thread|read|faq|rss)\.php\?[^\"\'>\s]+\s?)[\"|\']?/ies", "Htm_cv('\\3','<a\\1href=\"')", $html) : $html;
}

/**
 * url����
 *
 * @param string $url
 * @param string $tag
 * @return string
 */
function Htm_cv($url, $tag) {
	return stripslashes($tag) . urlRewrite($url) . '"';
}

function urlRewrite($url) {
	global $db_htmifopen, $db_dir, $db_ext;
	if (!$db_htmifopen) return $url;
	$tmppos = strpos($url, '#');
	$add = $tmppos !== false ? substr($url, $tmppos) : '';
	$turl = str_replace(array('.php?', '=', '&amp;', '&', $add), array($db_dir, '-', '-', '-', ''), $url);
	$turl != $url && $turl .= $db_ext;
	return $turl . $add;
}

/**
 * ����htmlspecialchars_decode��������Ϊhtmlspecialchars_decodeֻ��PHP 5.1�汾�����ϲŴ���
 * @param $string
 */
function pwHtmlspecialchars_decode ($string,$decodeTags = true) {
	$string = str_replace('&amp;','&', $string);
	$string =  str_replace(array( '&quot;', '&#039;', '&nbsp;','&#160;'), array('"', "'", ' ',' '), $string);
	$decodeTags && $string = str_replace(array('&lt;', '&gt;','&#61;'),array( '<', '>','='),$string);
	return $string;
}

/**
 * ֻ����һ����htmlspecialchars����
 * @param $string
 */
function pwHtmlspecialchars($string,$decodeTags = false) {
	return str_replace(array('&', '"', "'", '='), array('&amp;', '&quot;', '&#039;', '&#61;'), $string);
}

/*
 * ��ȡǿ����������
 */
function getForceIndex($index) {
	$indexdb = array('idx_postdate' => 'idx_postdate', 'idx_digest' => 'idx_digest', 'idx_uid_categoryid_modifiedtime' => 'idx_uid_categoryid_modifiedtime', 'idx_tid' => 'idx_tid' , 'idx_fid_ifcheck_specialsort_lastpost'=>'idx_fid_ifcheck_specialsort_lastpost');
	return $indexdb[$index];
}

/**
 * ��ʼ�����ݿ�����
 */
function PwNewDB() {
	if (!is_object($GLOBALS['db'])) {
		global $db, $database, $dbhost, $dbuser, $dbpw, $dbname, $PW, $charset, $pconnect;
		require_once S::escapePath(R_P . "require/db_$database.php");
		$db = new DB($dbhost, $dbuser, $dbpw, $dbname, $PW, $charset, $pconnect);
	}
}

/**
 * �����ֻ�
 */
function checkFromWap() {
	static $fromBrower = array('mozilla', 'chrome', 'safari', 'opera', 'm3gate', 'winwap', 'openwave', 'myop');
	$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
	foreach ($fromBrower as $v){
		if(strpos($userAgent,$v) !== false) return false;
	}
	static $fromMobile = array('nokia','sony','ericsson','motorola','samsung','java','philips','panasonic','ktouch','alcatel','lenovo','iphone','android','blackberry','meizu','netfront','symbian','ucweb','windowsce','palmsource','opera mini','opera mobi','cldc','midp','wap','mobile','benq', 'haier'); 
	foreach ($fromMobile as $v) {
		if (strpos($userAgent,$v) !== false) return true;
	}
	return false;
}

function isHeaderWap($fromWap = 0) {
	if (!checkFromWap()) return false;
	Cookie('fromWap',$fromWap);
	if (GetCookie('fromWap')) return false;
	return true;
}
/**
 * �½��ļ���
 */
function pwCreateFolder($path) {
	if (!is_dir($path)) {
		pwCreateFolder(dirname($path));
		@mkdir($path);
		@chmod($path,0777);
		@fclose(@fopen($path.'/index.html','w'));
		@chmod($path.'/index.html',0777);
	}
}

function getDefaultGender($gender) {
	return ($gender == 1) ? 'man' : 'women';
}
//LOADER

class PW_BaseLoader {

	function _loadClass($className, $dir = '', $isGetInstance = true, $classPrefix = 'PW_') {
		static $classes = array();
		$dir = PW_BaseLoader::_formatDir($dir);

		$classToken = $isGetInstance ? $className : $dir . $className; //��������
		if (isset($classes[$classToken])) return $classes[$classToken];

		$classes[$classToken] = true; //Ĭ��ֵ
		$fileDir = R_P . $dir . strtolower($className) . '.class.php';

		if (!$isGetInstance) return (@require_once S::escapePath($fileDir)); //δʵ������ֱ�ӷ���

		$class = $classPrefix . $className;
		if (!class_exists($class)) {

			# pack class start
			if($GLOBALS['db_classfile_compress']){
				$_directory = explode('/',$dir);
				if( isset($_directory[1]) && in_array( $_directory[1], array('framework','gather','forum','job','rate','site','user','utility') ) ){
					$fileDir = pwPack::classPath($fileDir,$class);
				}
			}
			# pack class end

			if (file_exists($fileDir)) require_once S::escapePath($fileDir);
			if (!class_exists($class)) { //�ٴ���֤�Ƿ����class
				$GLOBALS['className'] = $class;
				Showmsg('load_class_error');
			}
		}
		$classes[$classToken] = &new $class(); //ʵ����
		return $classes[$classToken];
	}

	function _loadBaseDB() {
		if (!class_exists('BaseDB')) require_once (R_P . 'lib/base/basedb.php');
	}

	function _formatDir($dir) {
		$dir = trim($dir);
		if ($dir) $dir = trim($dir, "\\/") . '/';
		return $dir;
	}
}

/**
 * ������(����ͨ�����ͨ�������ļ��ļ���)
 */
class L extends PW_BaseLoader {

	/**
	 * ���ļ��ļ������
	 *
	 * @param string $className �������
	 * @param string $dir Ŀ¼��ĩβ����Ҫ'/'
	 * @param boolean $isGetInstance �Ƿ�ʵ����
	 * @return mixed
	 */
	function loadClass($className, $dir = '', $isGetInstance = true) {
		return parent::_loadClass($className, 'lib/' . parent::_formatDir($dir), $isGetInstance);
	}

	/**
	 * dao�ļ��������
	 *
	 * @param string $dbName ���ݿ�����
	 * @param string $dir Ŀ¼
	 * @return mixed
	 */
	function loadDB($dbName, $dir = '') {
		parent::_loadBaseDB();
		return L::loadClass($dbName . 'DB', parent::_formatDir($dir) . 'db');
	}

	function config($var = null, $file = 'config', $dir = 'bbscache', $isStatic = true) {
		static $conf = array();
		$key = $dir . '_' . $file;
		if (!isset($conf[$key])) {
			if (file_exists(D_P . "data/$dir/{$file}.php")) {
				//* include S::escapePath(D_P . "data/$dir/{$file}.php");
				//* $arr = get_defined_vars();
				//* unset($arr['dir'], $arr['file'], $arr['var'], $arr['key'], $arr['conf'], $arr['isStatic']);
				$arr = pwCache::getData(S::escapePath(D_P . "data/$dir/{$file}.php"), false);
				if ($isStatic !== true) {return $var ? $arr[$var] : $arr;}
				$conf[$key] = $arr;
			} else {
				$conf[$key] = array();
			}
		}
		return $var ? $conf[$key][$var] : $conf[$key];
	}

	function reg($var = null) {
		return L::config($var, 'dbreg');
	}

	function style($var = null, $skinco = null, $ispath = false) {
		global $skin, $db_styledb, $db_defaultstyle;
		$skinco && isset($db_styledb[$skinco]) && $skin = $skinco;
		if ($skin && strpos($skin, '..') === false && file_exists(D_P . "data/style/$skin.php") && is_array($db_styledb[$skin]) && $db_styledb[$skin][1] == '1') {

		} elseif ($db_defaultstyle && strpos($db_defaultstyle, '..') === false && file_exists(D_P . "data/style/$db_defaultstyle.php")) {
			$skin = $db_defaultstyle;
		} else {
			$skin = 'wind';
		}
		return !$ispath ? L::config($var, $skin, 'style') : S::escapePath(D_P . 'data/style/' . $skin . '.php');
	}

	function forum($fid) {
		return L::config('foruminfo', 'fid_' . intval($fid), 'forums', false);
	}
}

class M {

	/*
	 * ���͵���/������Ϣ
	 */
	function sendMessage($userId, $usernames, $messageInfo, $shieldType = null, $typeName = null) {
		if ($shieldType) $usernames = M::_getUnShieldUsers($usernames, $shieldType);
		if (!$usernames) return false;

		$messageServer = L::loadClass("message", 'message');
		$typeName = ($typeName) ? $typeName : 'sms_message';
		$typeId = $messageServer->getConst($typeName);

		return $messageServer->sendMessage($userId, $usernames, $messageInfo, $typeId);
	}

	/*
	 * ����֪ͨ ϵͳ֪ͨ/�Ź�֪ͨ/�֪ͨ/Ӧ��֪ͨ
	 */
	function sendNotice($usernames, $messageInfo, $shieldType = 'notice_website', $typeName = null, $userId = '-1') {
		$usernames = M::_getUnShieldUsers($usernames, $shieldType);
		if (!$usernames) return false;

		$messageServer = L::loadClass("message", 'message');
		$typeName = ($typeName) ? $typeName : 'notice_system';

		$typeId = $messageServer->getConst($typeName);
		return $messageServer->sendNotice($userId, $usernames, $messageInfo, $typeId);
	}

	/*
	 * �������� ��������/Ⱥ������/�����/Ӧ������
	 */
	function sendRequest($userId, $usernames, $messageInfo, $shieldType = 'notice_website', $typeName = null) {
		$usernames = M::_getUnShieldUsers($usernames, $shieldType);
		if (!$usernames) return false;

		$messageServer = L::loadClass("message", 'message');
		$typeName = ($typeName) ? $typeName : 'request_friend';
		$typeId = $messageServer->getConst($typeName);
		return $messageServer->sendRequest($userId, $usernames, $messageInfo, $typeId);
	}

	/*
	 * ����Ⱥ����Ϣ
	 */
	function sendGroupMessage($userId, $groupId, $messageInfo, $userIds = array()) {
		$messageServer = L::loadClass("message", 'message');
		return $messageServer->sendGroupMessage($userId, $groupId, $messageInfo, '', $userIds);
	}

	function ifUnShieldThisType($user, $shieldType) {
		$messageServer = L::loadClass("message", 'message');
		return $messageServer->getMessageShield($user, $shieldType);
	}

	/*
	 * ��ȡû�����δ�������Ϣ���û�
	 */
	function _getUnShieldUsers($userNames, $shieldType) {
		if (!$shieldType) return $userNames;
		$messageServer = L::loadClass("message", 'message'); /* @var $messageServer PW_Message */
		$temp = array();
		foreach ($userNames as $user) {
			if (!$messageServer->getMessageShieldByUserName($user, $shieldType)) continue;
			$temp[] = $user;
		}
		return $temp;
	}
}

class template {

	var $bev;

	function template($bev = null) {
		$this->bev = $bev;
	}

	function printEot($template, $EXT = 'htm') {
		
		if (($filepath = $this->bev->getpath($template, $EXT)) !== false) {return S::escapePath($filepath);}
		exit('Can not find ' . $this->bev->getDefaultDir() . $template . '.' . $EXT . ' file');
	}
}
class Error {
	/**
	 * ���һ��������Ϣ
	 * @param $errorInfo	������Ϣ
	 */
	function addError($errorInfo) {
		$pwError = L::loadClass('errors','framework');
		$pwError->addError($errorInfo);
	}
	/**
	 * ���һ��������Ϣ
	 * @param $logInfo
	 */
	function addLog($errorInfo) {
		$pwError = L::loadClass('errors','framework');
		$pwError->addLog($errorInfo);
	}
	/**
	 * ��ʱ����
	 * @param $error ������Ϣ
	 */
	function showError($error, $jumpurl = '') {
		$pwError = L::loadClass('errors','framework');
		$pwError->showError($error,$jumpurl);
	}
	/**
	 * ����Ƿ��д�����Ϣ���еĻ���ʱ����
	 */
	function checkError($jumpurl = '') {
		$pwError = L::loadClass('errors','framework');
		$pwError->checkError($jumpurl);
	}
	/**
	 * ��¼������Ϣ
	 */
	function writeLog() {
		$pwError = L::loadClass('errors','framework');
		$pwError->writeLog();
	}
}
/*
 * �ṹ����ѯ���
 */
class pwQuery{
	/*
	 * ִ����������
	 */
	function insert($tableName, $col_names){
		$GLOBALS['db']->update(pwQuery::insertClause($tableName, $col_names));
		$insert_id =  $GLOBALS['db']->insert_id();
		$insert_id && Perf::gatherQuery ( 'insert', array($tableName), array_merge($col_names,array('insert_id'=>$insert_id)));
		return $insert_id;
	}
	/*
	 * ִ���滻����
	 */
	function replace($tableName, $col_names){
		$GLOBALS['db']->update(pwQuery::replaceClause($tableName, $col_names));
		return $GLOBALS['db']->affected_rows();
	}
	/*
	 * ִ�и��²���
	 */
	function update($tableName, $where_statement = null, $where_conditions = null, $col_names, $expand = null) {
		$GLOBALS['db']->update(pwQuery::updateClause($tableName, $where_statement, $where_conditions, $col_names, $expand));
		return $GLOBALS['db']->affected_rows();
	}
	/*
	 * ִ��ɾ������
	 */
	function delete($tableName, $where_statement = null, $where_conditions = null, $expand = null) {
		$GLOBALS['db']->update(pwQuery::deleteClause($tableName, $where_statement, $where_conditions, $expand ));
		return $GLOBALS['db']->affected_rows();
	}
	/*
	 * ��������insert���,�����������������,��ִ�����ݿ����
	 */
	function insertClause($tableName, $col_names){
		$service = L::loadClass('querybuilder','utility');
		return $service->insertClause($tableName, $col_names);
	}
	/*
	 * �����滻replace���,�����������滻���,��ִ�����ݿ����
	 */
	function replaceClause($tableName, $col_names){
		$service = L::loadClass('querybuilder','utility');
		return $service->replaceClause($tableName, $col_names);
	}
	/*
	 * �������update���,���������ݸ������,��ִ�����ݿ����
	 */
	function updateClause($tableName, $where_statement = null, $where_conditions = null, $col_names, $expand = null) {
		$service = L::loadClass('querybuilder','utility');
		return $service->updateClause($tableName, $where_statement, $where_conditions, $col_names, $expand);
	}
	/*
	 * ����ɾ��delete���,����������ɾ�����,��ִ�����ݿ����
	 */
	function deleteClause($tableName, $where_statement = null, $where_conditions = null, $expand = null) {
		$service = L::loadClass('querybuilder','utility');
		return $service->deleteClause($tableName, $where_statement, $where_conditions, $expand );
	}
	/*
	 * �����ѯ���,���������ݲ�ѯ���,��ִ�����ݿ����
	 */
	function selectClause($tableName, $where_statement = null, $where_conditions = null, $expand = null) {
		$service = L::loadClass('querybuilder','utility');
		return $service->selectClause($tableName, $where_statement, $where_conditions, $expand);
	}
	/*
	 * ����ͨ�ò�ѯ���,���������ݲ�ѯ���,��ִ�����ݿ����
	 */
	function buildClause($format, $parameters) {
		$service = L::loadClass('querybuilder','utility');
		return $service->buildClause($format, $parameters);
	}
}
/*
 * ȫ�־ۺϷ�������
 */
class Perf {
	/*
	 * ȫ�ֻ���ۺ�
	 */
	function gatherCache($cacheName){
		$gatherCache = L::loadClass ( 'gather', 'gather' );
		return $gatherCache->spreadCache ($cacheName);
	}
	/*
	 * ȫ�ֲ�ѯ�ۺ�
	 */
	function gatherQuery($operate, $tableNames, $fields, $expand = array()){
		$gatherQuery = L::loadClass('gather','gather');
		return $gatherQuery->spreadQuery($operate, $tableNames, $fields, $expand);
	}
	/*
	 * ȫ����Ϣ�ۺ�
	 */
	function gatherInfo($gatherName, $information, $defaultName = 'general'){
		$gatherInfo = L::loadClass ( 'gather', 'gather' );
		$gatherInfo->spreadInfo($gatherName, $information, $defaultName);
	}
	/*
	 * ����Ƿ�����װMemcache
	 */
	function checkMemcache(){
		static $isMemcache = null;
		if (! isset ( $isMemcache )) {
			$isMemcache = class_exists ( "Memcache" ) && strtolower ( $GLOBALS ['db_datastore'] ) == 'memcache';
		}
		return $isMemcache;
	}

	function getCacheService(){
		return L::loadClass('cacheservice','utility');
	}
}
/**
 * ȫ��ͨ�ö�ȡ����·����д�������ݷ���
 */
class pwCache {
	/**
	 * ��ȡ����·������
	 * @param string 	$filePath	�ļ�����
	 * @param boolean 	$isPack 	�Ƿ��ѡѹ���ļ�
	 */
	function getPath($filePath, $isPack = false, $withCache = true) {
		if (! $withCache || !$isPack ) {
			return $filePath;
		}
		/**
		if( $GLOBALS['db_filecache_to_memcache'] && Perf::checkMemcache() && in_array ( SCR, array ('index', 'read', 'thread' ))){
			$_cacheService = perf::gatherCache('pw_filecache');
			return $_cacheService->getFileCache($filePath);
		}
		**/
		if ( $GLOBALS['db_cachefile_compress'] && in_array ( SCR, array ('index', 'read', 'thread' ) )) {
			return pwPack::cachePath ( $filePath );
		}
		return $filePath;
	}

	function readover($fileName, $method = 'rb'){
		return readover($fileName, $method);
	}

	function writeover($fileName, $data, $method = 'rb+', $ifLock = true, $ifCheckPath = true, $ifChmod = true){
		return writeover($fileName, $data, $method, $ifLock, $ifCheckPath, $ifChmod);
	}

	function getData($filePath, $isRegister = true, $isReadOver= false ){
		$_service = L::loadClass('cachedistribute','utility');
		return $_service->getData($filePath,$isRegister,$isReadOver);
	}

	/**
	 * д����ͨ�ú���
	 * @param string 		$filePath	�ļ�����
	 * @param string|array 	$data		����
	 * @param boolean 		$isBuild	�Ƿ���Ҫװ��װ
	 * @param string 		$method		��дģʽ
	 * @param boolean		$ifLock		�Ƿ����ļ�
	 */
	function setData($filePath, $data, $isBuild = false, $method = 'rb+', $ifLock = true) {
		$_service = L::loadClass('cachedistribute','utility');
		return $_service->setData($filePath, $data, $isBuild , $method, $ifLock);
	}
	/**
	 * ɾ���ļ����溯��
	 * @param string 		$filePath	�ļ�����
	 */
	function deleteData($filePath) {
		$_service = L::loadClass('cachedistribute','utility');
		return $_service->deleteData($filePath);
	}
}
/*
 * �汾�ļ��������
 */
class pwPack {
	/*
	 * ͨ�û����ļ���ȡ
	 */
	function cachePath($filePath) {
		if( !$GLOBALS['db_cachefile_compress'] || !in_array ( SCR, array ('index', 'read', 'thread' ) ) ){
			return $filePath;
		}
		$_packService = pwPack::getPackService ();
      	return $_packService->loadCachePath ( $filePath );
	}
	/*
	 * ͨ������ļ���ȡ
	 */
	function classPath($filePath, $className) {
		if( !$GLOBALS['db_classfile_compress'] || !in_array ( SCR, array ('index', 'read', 'thread' ) ) ){
			return $filePath;
		}
		static $_packClassFile = null;
		if (! isset ( $_packClassFile )) {
			$_packClassFile = D_P . 'data/package/pack.class.' . SCR . '.php';
			if (is_file ( $_packClassFile )) {
				(! class_exists ( 'BaseDB' )) && require_once (R_P . 'lib/base/basedb.php');
				require_once S::escapePath ( $_packClassFile );
			}
		}
		if ($_packClassFile && class_exists ( $className )) {
			return R_P . 'require/returns.php';
		}
		return $filePath;
	}
	/*
	 * ͨ������ļ�ѹ��
	 */
	function files() {
		if( !in_array ( SCR, array ('index', 'read', 'thread' ) ) ){
			return false;
		}
		$_packService = pwPack::getPackService ();
		if( $GLOBALS['db_cachefile_compress'] ){
			$_packService->packCacheFiles ();
		}
		return true;
	}
	/*
	 * ��ȡ�������
	 */
	function getPackService() {
		static $packService = null;
		if (! isset ( $packService )) {
			require_once R_P.'require/packservice.php';
			$packService = new PW_packService ();
		}
		return $packService;
	}
}
/**
 * 
 * ��̳��չ����
 *
 */
class pwHook{
	/**
	 * ��Ӳ�������ֵ�ĵ���չ
	 * pwHook::runHook('post',array('uid'=>11));
	 * @param string $hookName
	 * @param array $params
	 */
	function runHook($hookName,$params = array()) {
		if (!pwHook::checkHook($hookName)) return false;
		$pwHook = pwHook::_getHook($hookName);
		if ($params) $pwHook->setParams($params);
		$pwHook->runHook();
	}
	/**
	 * ���һ��������ֵ����չ
	 * pwHook::runFilter('filteruid',$winduid,array('uid'=>11));
	 * @param string $hookName
	 * @param unknown_type $result
	 * @param unknown_type $params
	 */
	function runFilter($hookName,$result,$params = array()) {
		if (!pwHook::checkHook($hookName)) return $result;
		$pwHook = pwHook::_getHook($hookName);
		if ($params) $pwHook->setParams($params);
		return $pwHook->runFilter($result);
	}
	/**
	 * �жϸ�hook�Ƿ���
	 * @param string $name
	 * @return bool
	 */
	function checkHook($name) {
		global $db_hookset;
		return isset($db_hookset[$name]) || in_array($name,pwHook::getSystemHooks());
	}
	
	function getSystemHooks() {
		return array(
			'after_login',
			'after_post',
			'after_reply',
		);
	}
	
	function _getHook($name) {
		static $hooks = array();
		if (isset($hooks[$name])) return $hooks[$name];
		L::loadClass('hook','hook',false);
		$hooks[$name] = new PW_Hook($name);
		return $hooks[$name];
	}
}

?>
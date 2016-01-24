<?php
/**
 * ������֤��
 */
!defined('P_W') && exit('Forbidden');

class PW_Audio {
	/**
	 * @var string	$_audioPath 	�����ļ����Ŀ¼'/'��β
	 * @var string  $_code			 ��֤��
	 * @var string	$_audioFormat 	��Ƶ��ʽ
	 * @var string	$_interference 	�Ƿ����, 1-��, 0-��
	 */
	
	var $_audioPath;
	var $_code;
	var $_audioFormat = 'mp3';
	var $_interference = 0;
	
	/**
	 * ���������ļ�·��
	 * @param $audioPath	·��
	 * @return bool			true-�ɹ�, false-ʧ��
	 */
	function setAudioPath($audioPath) {
		if (empty($audioPath)) return false;
		$this->_audioPath = S::escapePath($audioPath);
		return true;
	}
	
	/**
	 * ������֤��
	 * @param $code			��֤��
	 * @return bool			true-�ɹ�, false-ʧ��
	 */
	function setCode($code) {
		if (empty($code)) return false;
		$this->_code = $code;
		return true;
	}
	
	/**
	 * ������Ƶ��ʽ
	 * @param $audioFormat	��Ƶ��ʽ
	 * @return bool			true-�ɹ�, false-ʧ��
	 */
	function setAudioFormat($audioFormat) {
		if (!S::inArray($audioFormat, array('wav', 'mp3'))) return false;
		$this->_audioFormat = $audioFormat;
		return true;
	}
	
	/**
	 * �����Ƿ����
	 * @param $state		1-��, 0-��
	 * @return bool			true-�ɹ�
	 */
	function setInterference($state) {
		if ($state !==1 && $state !== 0) return false;
		$this->_interference = $state;
		return true;
	}
	
	/**
	 * �������
	 */
	function outputAudio() {
		if ($this->_audioFormat == 'wav') {
			$contentType = 'audio/x-wav';
			$type = 'wav';
		} elseif ($this->_audioFormat == 'mp3') {
			$contentType = 'audio/mpeg';
			$type = 'mp3';
		}
		if (empty($contentType)) return false;
		header("Content-type: $contentType");
		$audioContent = ($type == 'wav') ? $this->_getWavAudio() : $this->_getMp3Audio();
		$this->_interference == 1 && $audioContent = $this->_addInterference($audioContent);
		header('Content-Length: ' . strlen($audioContent));
		echo $audioContent;
		exit;
	}
	
	/**
	 * ����wav��ʽ��������֤��
	 * @return $outputData	��������
	 */
	function _getWavAudio() {
		if (empty($this->_code)) return false;
		$audioData = array();
		$totalDataLength = '';
		for ($i = 0; $i < strlen($this->_code); $i++) {
			$wavFile = $this->_audioPath . strtoupper($this->_code[$i]) . '.wav';
			$wavData = readover($wavFile);
			$headerInfo = substr($wavData, 0, 36);
			$data = unpack('Nriffid/Vfilesize/Nfiletype/Nfmtid/Vfmtsize/vformattag/vchannels/Vsamplespersec/Vbytespersec/vblockalign/vbitspersample', $headerInfo);
			$data['filesize'] = $data['filesize'] + 8;
			$trunkLength = $data['fmtsize'] == 18 ? 46 : 44;
			$data['datainfo'] = substr($wavData, $trunkLength);
			if (($position = strpos($data['datainfo'], 'LIST')) !== false) {
				$data['filesize'] = $data['filesize'] - (strlen($data['datainfo']) - $position);
				$data['datainfo'] = substr($data['datainfo'], 0, $position);
			}
			$totalDataLength += strlen($data['datainfo']);
			$audioData[] = $data;
		}
		$outputData = '';
		foreach ($audioData as $key => $value) {
			if ($key == 0) {
				$wavHeader = pack('C4VC4', ord('R'), ord('I'), ord('F'), ord('F'), $totalDataLength + 36, ord('W'), ord('A'), ord('V'), ord('E'));
				$wavHeader .= pack('C4VvvVVvv',
					ord('f'),
					ord('m'),
					ord('t'),
					ord(' '),
					16,
					$value['formattag'],
					$value['channels'],
					$value['samplespersec'],
					$value['bytespersec'],
					$value['blockalign'],
					$value['bitspersample']
				);
				$wavHeader .= pack('C4V', ord('d'), ord('a'), ord('t'), ord('a'), $totalDataLength);
				$outputData .= $wavHeader;
			}
			$outputData .= $value['datainfo'];
		}
		return $outputData;
	}
	
	/**
	 * ����mp3��ʽ��������֤��
	 * @return $outputData	��������
	 */
	function _getMp3Audio() {
		if (empty($this->_code)) return false;
		$outputData = '';
		for ($i = 0; $i < strlen($this->_code); $i++) {
			$wavFile = $this->_audioPath . strtoupper($this->_code[$i]) . '.mp3';
			$wavData = readover($wavFile);
			$outputData .= $wavData;
		}
		return $outputData;
	}
	
	/**
	 * �Ӹ���
	 * @param 	$audioData	��Ƶ����
	 * @return 	$audioData	����������
	 */
	function _addInterference($audioData) {
		if ($this->_audioFormat == 'wav') {
			$startpos = strpos($audioData, 'data') + 8;	//wav��ʽ����Ƶ����
			$startpos += rand(1, 32);
		} elseif ($this->_audioFormat == 'mp3') {
			$startpos = 4;	//û��ID3V2��ǩ
			if (stripos($audioData, 'ID3') !== false) {
				$startpos = 24; //ID3V2ͷ��֡
				($pos = stripos($audioData, '3DI')) !== false && $startpos = $pos + 14; //��׼ȷ�Ļ�ȡ��Ƶ����
			}
		}
		$dataLength = strlen($audioData) - $startpos - 128;	//ĩ128���ֽ���MP3��ʽ��ID3V1��ǩ
		for ($i = $startpos; $i < $dataLength; $i += 256) {
			$ord = ord($audioData[$i]);
			if ($ord < 17 || $ord > 111) continue;
			$audioData[$i] = chr($ord + rand(-16, 16));
		}
		return $audioData;
	}
}
?>
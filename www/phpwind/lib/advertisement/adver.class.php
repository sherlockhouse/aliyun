<?php
!function_exists('readover') && exit('Forbidden');

/**
 * ��������
 * 
 * @package Advertisement
 */
class PW_Adver {
	/**
	 * ���ݿ����Ӷ���
	 * 
	 * @var DB
	 */
	var $_db = null;
	
	function PW_Adver() {
		global $db;
		$this->_db = & $db;
	}
	
	/**
	 * ���ͳ��
	 */
	function statistics() {
		$query = $this->_db->query("SELECT * FROM pw_advert");
		$adverlists = $adverbenchs = $benchs = array();
		$types = array(
			'img' => 0,
			'txt' => 0,
			'flash' => 0,
			'code' => 0
		);
		$status = array(
			'open' => 0,
			'close' => 0
		);
		while ($rs = $this->_db->fetch_array($query)) {
			if ($rs['ckey'] == '') {
				continue;
			}
			$ckey = ($rs['ckey'] != '') ? $rs['ckey'] : 'not.exist'; //����
			if ($rs['type'] == 0) {
				$adverbenchs[$ckey] = $rs;
			} else {
				$adverlists[$ckey][] = $rs;
			}
		}
		
		foreach($adverbenchs as $ckey => $v) { //ͳ����ʽ
			$descrip = ($ckey != 'not.exist') ? $adverbenchs[$ckey]['descrip'] : '���λ������';
			list($benchs[$ckey]['title'], $benchs[$ckey]['descrip']) = explode("~\t~", $descrip);
			$benchs[$ckey]['num'] = 0;
			$benchs[$ckey]['open'] = 0;
			$benchs[$ckey]['close'] = 0;
		}
		foreach($adverlists as $ckey => $advers) {
			$open = $close = 0;
			foreach($advers as $adver) {
				($adver['ifshow']) ? $status['open']++ : $status['close']++;
				$config = unserialize($adver['config']);
				($config['type'] == 'txt') && $types['txt']++;
				($config['type'] == 'img') && $types['img']++;
				($config['type'] == 'code') && $types['code']++;
				($config['type'] == 'flash') && $types['flash']++;
				($adver['ifshow']) ? $open++ : $close++;
			}
			$benchs[$ckey]['num'] = count($advers);
			$benchs[$ckey]['open'] = $open;
			$benchs[$ckey]['close'] = $close;
		}
		return array(
			$status,
			$types,
			$benchs
		);
	}
	
	/**
	 * ��ȡʱ����ڵĹ��
	 * 
	 * @param unknown_type $adverBench
	 * @param int $adverTime
	 */
	function during($adverBench, $adverTime) {
		$current = time();
		$limit = $current + 24 * 3600 * $adverTime;
		$date = date("Y�� m�� d��", $current) . "��" . date("Y�� m�� d��", $limit);
		$query = $this->_db->query("SELECT * FROM pw_advert WHERE type=1 AND ckey=" . S::sqlEscape($adverBench) . " AND stime<=" . $limit);
		$result = $during = array();
		while ($rs = $this->_db->fetch_array($query)) {
			$rs['stime'] = intval(($rs['stime'] > $current) ? $rs['stime'] : $current);
			$rs['etime'] = intval(($rs['etime'] >= $limit) ? $limit : $rs['etime']);
			if ($rs['stime'] > $rs['etime']) {
				continue;
			}
			$result[$rs['id']] = $rs;
		}
		if (!$result) {
			return array(
				'',
				$date
			);
		}
		$index = 0;
		$sortTmp = array();
		$limit = $current + 24 * 3600 * $adverTime;
		$during = $start = $end = array();
		foreach($result as $k => $adver) {
			$result[$k]['stime'] = $t_start = intval(($adver['stime'] > $current) ? $adver['stime'] : $current);
			$result[$k]['etime'] = $t_end = intval(($adver['etime'] > $limit) ? $limit : $adver['etime']);
			$during[$t_start][$k] = $t_end;
			$sortTmp[$k] = $result[$k]['stime'];
		}
		asort($sortTmp);
		$second = array();
		foreach($sortTmp as $k => $startTime) {
			$second[] = array(
				'start' => $result[$k]['stime'],
				'end' => $result[$k]['etime'],
				'id' => $result[$k]['id']
			);
		}
		$tmp = array();
		$save = array();
		$tmpSave = array();
		$i = 0;
		$pre = $second[0];
		$tmpSave[] = $second[$i];
		while ($i < count($second) - 1) {
			if ($second[$i + 1]['start'] > $pre['end']) {
				$tmp[] = $pre;
				$save[] = $tmpSave;
				$tmpSave = array();
				$tmpSave[] = $second[$i + 1];
				$pre = $second[$i + 1];
			} else {
				$pre = array(
					'start' => $pre['start'],
					'end' => max($second[$i + 1]['end'], $pre['end'])
				);
				$tmpSave[] = $second[$i + 1];
			}
			$i++;
		}
		$tmp[] = $pre;
		$save[] = $tmpSave;
		$x = array();
		$j = 0;
		for ($i = 0; $i <= count($tmp) - 1; $i++) {
			$data = array(
				'data' => $save[$i]
			);
			$x[$j] = $tmp[$i] + $data;
			if ($i != count($tmp) - 1) {
				$x[$j + 1] = array(
					'start' => $tmp[$i]['end'],
					'end' => $tmp[$i + 1]['start']
				); /*����*/
			}
			$j+= 2;
		}
		$line = array();
		foreach($x as $k => $v) {
			$days = $this->calculate($v);
			$line[$k]['length'] = 100 * ((count($days) - 1) / $adverTime) . "%";
			$tip = '';
			if ($v['data']) {
				$tip .= "�ù��λ��" . count($v['data']) . "�����<br />";
				$index = 1;
				foreach($v['data'] as $t) {
					$tip .= $index . "��" . $this->getTip($t, $current) . "<br />";
					$index++;
				}
				$line[$k]['status'] = 1;
			} else {
				$tip .= $this->getTip($v, $current) . "<br />";
				$tip .= '������λ����';
				$line[$k]['status'] = 0;
			}
			$line[$k]['tips'] = $tip;
		}
		return array(
			$line,
			$date
		);
	}
	
	/**
	 * ����ʱ��������ļ���
	 * 
	 * @param array $day
	 * @return array
	 */
	function calculate($day) {
		$startDay = $day['start'];
		$endDay = $day['end'];
		$tmp = array();
		while ($startDay <= $endDay) {
			$tmp[] = date("Ymd", $startDay);
			$startDay += 3600 * 24;
		}
		return $tmp;
	}
	
	function getTip($day, $current, $limit) {
		$tip = '';
		$tip .= ($day['start'] > $current) ? date("Y�� m�� d��", $day['start']) : '��ǰʱ��';
		$tip .= ($day['end'] == null) ? $limit : date("��Y�� m�� d��", $day['end']);
		return $tip;
	}
	
	function getAdverBenchs() {
		$benchs = array();
		$query = $this->_db->query("SELECT * FROM pw_advert WHERE type=0");
		while ($rs = $this->_db->fetch_array($query)) {
			list($title) = explode("~\t~", $rs['descrip']);
			$benchs[$rs['ckey']] = $title;
		}
		return $benchs;
	}
	
	function getAdverBenchSelect($select, $name = "adverBench", $id = "adverBench", $empty = true) {
		$benchs = $this->getAdverBenchs();
		return $this->_buildSelect($benchs, $name, $id, $select, $empty);
	}
	
	function getAdverTimeSelect($select, $name = "advertime", $id = "advertime", $empty = true) {
		$times = array(
			'7' => '1������',
			'30' => '1����',
			'90' => '3����',
			'180' => '6����',
			'365' => '1��'
		);
		return $this->_buildSelect($times, $name, $id, $select, $empty);
	}
	
	/**
	 * �ʼ���������Ĭ������
	 */
	function getDefaultAlter() {
		return array(
			'alterstatus' => 1,
			'alterbefore' => 0,
			'alterway' => 1
		);
	}
	
	/**
	 * �������
	 */
	function getType() {
		return array(
			'txt' => '����',
			'img' => 'ͼƬ',
			'code' => '����',
			'flash' => 'Flash'
		);
	}
	
	/**
	 * ��װ�������������
	 */
	function buildTypeSelect($select = '', $name = 'advertype', $id = 'advertype') {
		$types = $this->getType();
		return $this->_buildSelect($types, $name, $id, $select);
	}
	
	/**
	 * ���״̬
	 */
	function getStatus() {
		return array(
			'1' => '����',
			'0' => '�ر�'
		);
	}
	
	/**
	 * ��װ���״̬������
	 */
	function buildStatusSelect($select = '', $name = 'adverstatus', $id = 'adverstatus') {
		$status = $this->getStatus();
		return $this->_buildSelect($status, $name, $id, $select);
	}
	
	/**
	 * ��װ������
	 */
	function _buildSelect($arrays, $name, $id, $select = '', $empty = true) {
		if (!is_array($arrays)) {
			return '';
		}
		$html = '<select name="' . $name . '" id="' . $id . '">';
		$empty && $html.= '<option value=""></option>';
		foreach($arrays as $k => $v) {
			$selected = ($select == $k && $select != null) ? 'selected="selected"' : "";
			$html.= '<option value="' . $k . '" ' . $selected . '>' . $v . '</option>';
		}
		$html.= '</select>';
		return $html;
	}
}

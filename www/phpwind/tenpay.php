<?php
require_once('global.php');

S::gp(array('cmdno','pay_result','date','bargainor_id','transaction_id','sp_billno','total_fee', 'fee_type','attach','sign'));

//* include_once pwCache::getPath(D_P.'data/bbscache/ol_config.php');
pwCache::getData(D_P.'data/bbscache/ol_config.php');

if (!$ol_onlinepay) {
	Showmsg($ol_whycolse);
}
if (!$ol_tenpay || !$ol_tenpaycode) {
	Showmsg('olpay_tenpayerror');
}

$text = "cmdno=$cmdno&pay_result=$pay_result&date=$date&transaction_id=$transaction_id&sp_billno=$sp_billno&total_fee=$total_fee&fee_type=$fee_type&attach=$attach&key=$ol_tenpaycode";
$mac = strtoupper(md5($text));

if ($mac != $sign) {
	Showmsg( "��֤MD5ǩ��ʧ��");
}
if ($ol_tenpay != $bargainor_id ) {
	Showmsg( "������̻���");
}
if ($pay_result != "0" ) {
	Showmsg( "֧��ʧ��");
}

$rt = $db->get_one("SELECT c.*,m.username FROM pw_clientorder c LEFT JOIN pw_members m USING(uid) WHERE order_no=".S::sqlEscape($transaction_id));
if (!$rt) {
	refreshto('userpay.php','ϵͳ��û�����ĳ�ֵ�������޷���ɳ�ֵ��');
}
if ($rt['state'] == 2) {
	refreshto('userpay.php','�ö����Ѿ���ֵ�ɹ���');
}
$rmbrate = $db_creditpay[$rt['paycredit']]['rmbrate'];
!$rmbrate && $rmbrate = 10;
$currency = $rt['price'] * $rmbrate;

require_once(R_P.'require/credit.php');
$credit->addLog('main_olpay',array($rt['paycredit'] => $currency),array(
	'uid'		=> $rt['uid'],
	'username'	=> $rt['username'],
	'ip'		=> $onlineip,
	'number'	=> $rt['price']
));
$credit->set($rt['uid'],$rt['paycredit'],$currency);

$db->update("UPDATE pw_clientorder SET payemail=".S::sqlEscape($buyer_email).",state=2 WHERE order_no=".S::sqlEscape($transaction_id));

M::sendNotice(
	array($rt['username']),
	array(
		'title' => getLangInfo('writemsg','olpay_title'),
		'content' => getLangInfo('writemsg','olpay_content_2',array(
			'currency'	=> $currency,
			'cname'		=> $credit->cType[$rt['paycredit']],
			'number'	=> $rt['number']
		)),
	)
);

require_once(R_P.'require/posthost.php');
$statdb = array(
	'type' =>'tenpay',
	'seller_email' => $_GET['bargainor_id'],
	'trade_no' => $_GET['transaction_id'],
	'total_fee' => $_GET['total_fee'],
	'siteurl' => $db_bbsurl,
);
$getdb = '';
foreach ($statdb as $key => $value) {
	$getdb .= $key."=".urlencode($value)."&";
}
PostHost("http://pay.phpwind.net/pay/stats.php",$getdb,'POST');
refreshto('userpay.php','��ֵ�ɹ���');

function paymsg($url,$msg,$notify = 'success') {
	if (empty($_POST)) {
		refreshto($url,$msg);
	}
	exit($notify);
}
?>
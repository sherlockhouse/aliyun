<?php
!defined('PW_UPLOAD') && exit('Forbidden');
$maxid = $db->get_value("SELECT MAX(id) FROM pw_advert");
$maxid = $maxid < 100 ? 101 : $maxid+1;

$db->update("ALTER TABLE pw_advert AUTO_INCREMENT=$maxid",false);

//$query = $db->query("");//TODO ת��idС��100�ļ�¼
$query = $db->query("SELECT * FROM pw_advert WHERE id<=$maxid AND type=1");
while ($rt = $db->fetch_array($query)) {
	$ads[] = array($rt['type'],$rt['uid'],$rt['ckey'],$rt['stime'],$rt['etime'],$rt['ifshow'],$rt['orderby'],$rt['descrip'],$rt['config']);
}
if ($ads) {
	$db->update("INSERT INTO pw_advert(type,uid,ckey,stime,etime,ifshow,orderby,descrip,config) VALUES ".pwSqlMulti($ads,false));
}
$maxid = $maxid - 1;
$db->update("DELETE FROM pw_advert WHERE id<=$maxid AND type=1");

$arrSQL = array(
	"REPLACE INTO pw_advert VALUES(1, 0, 0, 'Site.Header', 0, 0, 1, 0, 'ͷ�����~	~��ʾ��ҳ���ͷ����һ����ͼƬ��flash��ʽ��ʾ���������ʱϵͳ�����ѡȡһ����ʾ', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(2, 0, 0, 'Site.Footer', 0, 0, 1, 0, '�ײ����~	~��ʾ��ҳ��ĵײ���һ����ͼƬ��flash��ʽ��ʾ���������ʱϵͳ�����ѡȡһ����ʾ', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(3, 0, 0, 'Site.NavBanner1', 0, 0, 1, 0, '����ͨ��[1]~	~��ʾ�������������棬һ����ͼƬ��flash��ʽ��ʾ���������ʱϵͳ�����ѡȡһ����ʾ', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(4, 0, 0, 'Site.NavBanner2', 0, 0, 1, 0, '����ͨ��[2]~	~��ʾ��ͷ��ͨ�����[1]λ�õ�����,��ͨ�����[1]��һ����ʾ,һ��ΪͼƬ���', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(5, 0, 0, 'Site.PopupNotice', 0, 0, 1, 0, '�������[����]~	~��ҳ�����½��Ը����Ĳ㵯����ʾ���˹��������Ҫ����������ش��ڲ���', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(6, 0, 0, 'Site.FloatRand', 0, 0, 1, 0, 'Ư�����[���]~	~�Ը�����ʽ��ҳ�������Ư���Ĺ��', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(7, 0, 0, 'Site.FloatLeft', 0, 0, 1, 0, 'Ư�����[��]~	~�Ը�����ʽ��ҳ�����Ư���Ĺ�棬�׳ƶ������[��]', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(8, 0, 0, 'Site.FloatRight', 0, 0, 1, 0, 'Ư�����[��]~	~�Ը�����ʽ��ҳ���ұ�Ư���Ĺ�棬�׳ƶ������[��]', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(9, 0, 0, 'Mode.TextIndex', 0, 0, 1, 0, '���ֹ��[��̳��ҳ]~	~��ʾ��ҳ��ĵ������棬һ�������ַ�ʽ��ʾ��ÿ��������棬����������������ʾ', 'a:1:{s:7:\"display\";s:3:\"all\";}');",

	"REPLACE INTO pw_advert VALUES(10, 0, 0, 'Mode.Forum.TextRead', 0, 0, 1, 0, '���ֹ��[����ҳ]~	~��ʾ��ҳ��ĵ������棬һ�������ַ�ʽ��ʾ��ÿ��������棬����������������ʾ', 'a:1:{s:7:\"display\";s:3:\"all\";}');",

	"REPLACE INTO pw_advert VALUES(11, 0, 0, 'Mode.Forum.TextThread', 0, 0, 1, 0, '���ֹ��[����ҳ]~	~��ʾ��ҳ��ĵ������棬һ�������ַ�ʽ��ʾ��ÿ��������棬����������������ʾ', 'a:1:{s:7:\"display\";s:3:\"all\";}');",

	"REPLACE INTO pw_advert VALUES(12, 0, 0, 'Mode.Forum.Layer.TidRight', 0, 0, 1, 0, '¥����[�����Ҳ�]~	~�����������Ҳ࣬һ����ͼƬ��������ʾ������������ʱϵͳ�����ѡȡһ����ʾ', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(13, 0, 0, 'Mode.Forum.Layer.TidDown', 0, 0, 1, 0, '¥����[�����·�]~	~�����������·���һ����ͼƬ��������ʾ������������ʱϵͳ�����ѡȡһ����ʾ', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(14, 0, 0, 'Mode.Forum.Layer.TidUp', 0, 0, 1, 0, '¥����[�����Ϸ�]~	~�����������Ϸ���һ����ͼƬ��������ʾ������������ʱϵͳ�����ѡȡһ����ʾ', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(15, 0, 0, 'Mode.Forum.Layer.TidAmong', 0, 0, 1, 0, '¥����[¥���м�]~	~����������¥��֮�䣬һ����ͼƬ��������ʾ������������ʱϵͳ�����ѡȡһ����ʾ', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(16, 0, 0, 'Mode.Layer.Index', 0, 0, 1, 0, '��̳��ҳ�����~	~��������ҳ�����֮�䣬һ����ͼƬ��������ʾ������������ʱϵͳ�����ѡȡһ����ʾ', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(23, 0, 0, 'Mode.Forum.Layer.area.ThreadTop', 0, 0, 1, 0, '�Ż������б�ҳ����~	~�����б�ҳ�Ż�ģʽ���ʱ�����Ϸ��Ĺ��λ', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(24, 0, 0, 'Mode.Forum.Layer.area.ThreadBtm', 0, 0, 1, 0, '�Ż������б�ҳ����~	~�����б�ҳ�Ż�ģʽ���ʱ�����·��Ĺ��λ', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(25, 0, 0, 'Mode.Forum.Layer.area.ReadTop', 0, 0, 1, 0, '�Ż���������ҳ����~	~��������ҳ�Ż�ģʽ���ʱ�����Ϸ��Ĺ��λ', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(26, 0, 0, 'Mode.Forum.Layer.area.ReadBtm', 0, 0, 1, 0, '�Ż���������ҳ����~	~��������ҳ�Ż�ģʽ���ʱ�����·��Ĺ��λ', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO pw_advert VALUES(30, 2, 0, 'Wap.Header', 0, 0, 1, 0, 'WAPͷ��~	~��ʾ��WAPͷ����������ͼƬ�����֣����ڶ������ʱ��ȫ����ʾ', 'a:1:{s:7:\"display\";s:3:\"all\";}');",

	"REPLACE INTO pw_advert VALUES(31, 2, 0, 'Wap.Footer', 0, 0, 1, 0, 'WAP�ײ�~	~��ʾ��WAP�ײ���������ͼƬ�����֣����ڶ������ʱ��ȫ����ʾ', 'a:1:{s:7:\"display\";s:3:\"all\";}');",

	"REPLACE INTO pw_advert VALUES(32, 2, 0, 'Wap.Read.Header', 0, 0, 1, 0, 'WAP�������ݶ���~	~��ʾ��WAP�������ݶ�����������ͼƬ�����֣����ڶ������ʱ��ȫ����ʾ', 'a:1:{s:7:\"display\";s:3:\"all\";}');",

	"REPLACE INTO pw_advert VALUES(33, 2, 0, 'Wap.Read.Footer', 0, 0, 1, 0, 'WAP�������ݵײ�~	~��ʾ��WAP�������ݵײ���������ͼƬ�����֣����ڶ������ʱ��ȫ����ʾ', 'a:1:{s:7:\"display\";s:3:\"all\";}');",

	"REPLACE INTO `pw_advert` (`id`, `type`, `uid`, `ckey`, `stime`, `etime`, `ifshow`, `orderby`, `descrip`, `config`) VALUES
(27, 0, 0, 'Site.Search.Thread.Right', 0, 0, 1, 0, '���������Ҳ���~	~��������ʱ����ʾ��ҳ����Ҳ�', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO `pw_advert` (`id`, `type`, `uid`, `ckey`, `stime`, `etime`, `ifshow`, `orderby`, `descrip`, `config`) VALUES
(28, 0, 0, 'Site.Search.Diary.Right', 0, 0, 1, 0, '������־�Ҳ���~	~������־ʱ����ʾ��ҳ����Ҳ�', 'a:1:{s:7:\"display\";s:4:\"rand\";}');",

	"REPLACE INTO `pw_advert` (`id`, `type`, `uid`, `ckey`, `stime`, `etime`, `ifshow`, `orderby`, `descrip`, `config`) VALUES
(29, 0, 0, 'Site.u.IndexCenter', 0, 0, 1, 0, '����������ҳ���λ~	~', 'a:1:{s:7:\"display\";s:3:\"all\";}')",
);

foreach ($arrSQL as $sql) {
	if (trim($sql)) {
		$db->update($sql);
	}
}

$arrUpdate = array(
	'Site.NavBanner'		=> 'Site.NavBanner1',
	'Mode.Layer.TidRight'	=> 'Mode.Forum.Layer.TidRight',
	'Mode.Layer.TidDown'	=> 'Mode.Forum.Layer.TidDown',
	'Mode.Layer.TidUp'		=> 'Mode.Forum.Layer.TidUp',
	'Mode.Layer.TidAmong'	=> 'Mode.Forum.Layer.TidAmong'
);

foreach ($arrUpdate as $key=>$value) {
	$db->update("UPDATE pw_advert SET ckey=".pwEscape($value,false)."WHERE ckey=".pwEscape($key,false));
}

?>
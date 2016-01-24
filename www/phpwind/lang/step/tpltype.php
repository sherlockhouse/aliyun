<?php
!defined('PW_UPLOAD') && exit('Forbidden');
//732 to 75
//UPDATE pw_block
$db->update("REPLACE INTO `pw_block` (`bid`, `sid`, `func`, `name`, `rang`, `cachetime`, `iflock`) VALUES
(1, 1, 'newsubject', '��������', '', 1800, 1),
(2, 1, 'newreply', '���»ظ�', '', 600, 1),
(3, 1, 'digestsubject', '��������', '', 86400, 1),
(4, 1, 'replysort', '�ظ�����', '', 86400, 1),
(5, 1, 'hitsort', '��������', '', 86400, 1),
(6, 2, 'usersort', '��Ǯ����', 'money', 3000, 1),
(7, 2, 'usersort', '��������', 'rvrc', 3000, 1),
(8, 3, 'forumsort', '���շ���', 'tpost', 600, 1),
(9, 3, 'forumsort', '����������', 'topic', 86400, 1),
(10, 3, 'forumsort', '����������', 'article', 86400, 1),
(11, 4, 'gettags', '���ű�ǩ', 'hot', 86400, 1),
(12, 4, 'gettags', '���±�ǩ', 'new', 86400, 1),
(13, 5, 'newpic', '����ͼƬ', '', 1700, 1),
(15, 6, 'hotactive', '���Ż', '', 86400, 1),
(18, 2, 'usersort', '����ʱ������', 'onlinetime', 86400, 1),
(19, 2, 'usersort', '���շ�������', 'todaypost', 1200, 1),
(20, 2, 'usersort', '�·�������', 'monthpost', 40000, 1),
(21, 2, 'usersort', '��������', 'postnum', 40000, 1),
(22, 2, 'usersort', '����������', 'monoltime', 40000, 1),
(23, 1, 'replysortday', '��������', '', 1800, 1),
(25, 2, 'usersort', '����ֵ����', 'credit', 3000, 1),
(26, 2, 'usersort', '���ױ����а�', 'currency', 3000, 1),
(29, 1, 'highlightsubject', '��������', '', 50000, 1),
(38, 6, 'todayactive', '���ջ', '', 3600, 1),
(41, 6, 'newactive', '���»', '', 1800, 1),
(49, 1, 'replysortweek', '��������', '', 86400, 1),
(47, 1, 'hitsortday', '��������', '', 1800, 1)");

//update pw_stamp
$db->update("REPLACE INTO `pw_stamp` (`sid`, `name`, `stamp`, `init`, `iflock`, `iffid`) VALUES
(1, '��������', 'subject', 1, 1, 1),
(2, '�û�����', 'user', 6, 1, 0),
(3, '�������', 'forum', 9, 1, 0),
(4, '��ǩ����', 'tag', 11, 1, 0),
(5, 'ͼƬ', 'image', 13, 1, 1),
(6, '�', 'active', 41, 1, 1)");

//update pw_tpltype
$db->update("REPLACE INTO `pw_tpltype` (`id`, `type`, `name`) VALUES
(1, 'subject', '������'),
(2, 'image', 'ͼƬ��'),
(3, 'forum', '�����'),
(4, 'user', '�û���'),
(5, 'tag', '��ǩ��'),
(6, 'player', '������')");

?>
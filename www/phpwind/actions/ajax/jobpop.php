<?php
!defined('P_W') && exit('Forbidden');

if (!$db_job_isopen || !$winduid) {
	exit();
}
S::gp(array("v", "job"));
if ($job == "cookie") {
	$v = (in_array($v, array(0, 1))) ? $v : 0; /*����cookie*/
	Cookie("jobpop", $v);
} elseif ($job == "show" || !getCookie("jobpop")) { /*ǿ����ʾ���Զ�����ʾ*/
	Cookie("jobpop", 0);
	$jobService = L::loadclass("job", 'job'); /*���������*/
	$lists = $jobService->jobDisplayController($winduid, $groupid, 'list');
//	$appliedHTML = $jobService->buildApplieds($winduid, $groupid);
	$joblists = $jobService->getAppliedJobs($winduid);
	$jobs = $jobService->buildLists($joblists, 'applied', $winduid, $groupid);
	if ($jobs) {
		$html = '';
		foreach($jobs as $job) {
			$html .= $jobService->buildApplied($job);
		}
		$appliedHTML = $html;
	}
	
	$jobsNum = (int)count($jobs);
	$winddb['jobnum'] = (int)$winddb['jobnum'];
	if ($winddb['jobnum'] !== $jobsNum) {
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
		$userService->update($winduid, array(), array('jobnum' => $jobsNum));
		$winddb['jobnum'] = $jobsNum;
	}
	
	require PrintEot('jobpop');
	ajax_footer();
	exit();
}

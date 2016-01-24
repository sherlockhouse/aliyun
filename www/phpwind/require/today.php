<?php
!function_exists('readover') && exit('Forbidden');
$filename=D_P.'data/bbscache/today.php';
$dbtdsize=100;
if(file_exists($filename)){
	$todaydata=readover($filename);
	if($offset=strpos($todaydata,"\n".$windid."\t")){/*ʹ�þ�ȷƥ�� ������"\n".$windid."\t"*/
		$offset+=1;
		if($fp=@fopen($filename,"rb+")){
			flock($fp,LOCK_EX);
			list($node,$yestime)=nodeinfo($fp,$dbtdsize,$offset);/*�޸�ͷ���*/
			$nowfp=$offset/($dbtdsize+1);
			if("$nowfp"!=$node && $node!=''){
				fputin($fp,$node,$dbtdsize,$nowfp);/*�޸�ͷ���ָ������ݶ�*/
				list($oldprior,$oldnext)=fputin($fp,$nowfp,$dbtdsize,'node',$node);/*�޸���Ҫ���µ�����*/
				if($oldprior!='node'){
					fputin($fp,$oldprior,$dbtdsize,'M',$oldnext);/*�޸�ǰһ���ĺ���*/
				}
				if($oldnext!='NULL' && $oldprior!='node'){
					fputin($fp,$oldnext,$dbtdsize,$oldprior);/*�޸ĺ�һ����ǰ��*/
				}
			}
			fclose($fp);
		}
	}else{
		$offset=filesize($filename);
		if($fp=@fopen($filename,"rb+")){
			flock($fp,LOCK_EX);
			list($node,$yestime)=nodeinfo($fp,$dbtdsize,$offset);
			if($node!=''){/*�޸�ͷ���*/
				$nowfp=$offset/($dbtdsize+1);
				if($node!='NULL') {
					fputin($fp,$node,$dbtdsize,$nowfp);
				}
				if($node!=$nowfp) fputin($fp,$nowfp,$dbtdsize,'node',$node,Y);/*�������*/
			}
			fclose($fp);
		}
	}
}
if($yestime!=$tdtime) {
	//* P_unlink($filename);
	pwCache::deleteData($filename);
	pwCache::setData($filename,str_pad("<?php die;?>\tNULL\t$tdtime\t",$dbtdsize)."\n");/*24Сʱ��ʼ��һ��*/
}
function fputin($fp,$offset,$dbtdsize,$prior='M',$next='M',$ifadd='N')
{
	$offset=$offset*($dbtdsize+1);/*������ת����ָ��ƫ����*/
	fseek($fp,$offset,SEEK_SET);
	if($ifadd=='N'){
		$iddata=fread($fp,$dbtdsize);
		$idarray=explode("\t",$iddata);
		fseek($fp,$offset,SEEK_SET);
	}
	if($next!='M' && $prior!='M'){/*˵����һ�����Ǳ����ĵ����ݶ�.��Ҫ������������Ϣ���и���*/
		global $windid,$timestamp,$onlineip,$winddb;
		$idarray[0]=$windid;$idarray[3]=$winddb['regdate'];
		if($ifadd!='N') $idarray[4]=$timestamp;
		$idarray[5]=$timestamp;$idarray[6]=$onlineip;$idarray[7]=$winddb['postnum'];$idarray[8]=$winddb['rvrc'];
	}
	if($prior=='M') $prior=$idarray[1];
	if($next=='M') $next=$idarray[2];
	$data="$idarray[0]\t$prior\t$next\t$idarray[3]\t$idarray[4]\t$idarray[5]\t$idarray[6]\t$idarray[7]\t$idarray[8]\t";
	$data=str_pad($data,$dbtdsize)."\n";/*����д��*/
	fwrite($fp,$data);
	return array($idarray[1],$idarray[2]);/*�������ݸ���ǰ����һ������һ���*/
}
function nodeinfo($fp,$dbtdsize,$offset)
{
	$offset=$offset/($dbtdsize+1);
	$node=fread($fp,$dbtdsize);
	$nodedb=explode("\t",$node);/*ͷ����ڵڶ������ݶ�*/
	if(is_int($offset)){
		$nodedata=str_pad("<?php die;?>\t$offset\t$nodedb[2]\t",$dbtdsize)."\n";
		fseek($fp,0,SEEK_SET);/*��ָ������ļ���ͷ*/
		fwrite($fp,$nodedata);
		return array($nodedb[1],$nodedb[2]);
	}else{
		return '';
	}
}
?>
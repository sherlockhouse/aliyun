function displayElement(elementId,buttonId,isDisplay) {
	if (undefined == isDisplay && typeof buttonId === 'string') {
		getObj(elementId).style.display = getObj(elementId).style.display == 'none' ? '' : 'none';	
		getObj(buttonId).innerHTML=getObj(elementId).style.display == ''?'������' : '�߼�����';
	} else {
		getObj(elementId).style.display = isDisplay ? '' : 'none';
	}
}

function atccheck()
{
	if(document.FORM.atc_title.value==''){
		alert('����Ϊ��');
		document.FORM.atc_title.focus();
		return false;
	} else if(document.FORM.fid.value==''){
		alert('û��ѡ��������������');
		document.FORM.fid.focus();
		return false;
	}
	_submit();
}

function checkhackset(chars)
{
	if(!confirm("ȷ��Ҫж�ش˲����? �����ж���˴˲��,�����Զ�ɾ���������ļ�����ȷ��!"))
		return false;
	location.href=chars;
}

function level_jump(admin_file)
{
	var URL=document.mod.selectfid.options[document.mod.selectfid.selectedIndex].value;
	location.href=admin_file+"?adminjob=level&action=editgroup&gid="+URL;selectfid='_self';
}

function checkgroupset(chars)
{
	if(!confirm("ȷ��ɾ����? �����ɾ���˴��û���,�뵽��̳�������ݹ�������û�ͷ�λ���!"))
		return false;
	window.location.href=chars;return false;
}

function report_jump(admin_file){
	var URL=document.form1.type.options[document.form1.type.selectedIndex].value;
	location.href=admin_file+"?adminjob=report&type="+URL;
}
// JavaScript Document
B.namespace('editor.insertmusic', function(B){
	//create PopUp
	//document.body.appendChild(B.createElement());
	var insertTrigger;
	B.require('util.dialog', function(B){
		B.util.dialog({
			id: 'B_editor_music',
			pos: ['left','top',-10000, 0],
			reuse: true,
			data: '<div class="B_menu B_p10B">\
	<div style="width:320px;">\
		<div class="B_h B_drag_handle"><a href="javascript://" class="B_menu_adel B_close">��</a>��������</div>\
		<form name="B_editor_musicForm" class="B_tableA">\
			<table width="100%" class="B_mb10">\
				<tbody><tr>\
					<td width="60">��ַ��</td>\
					<td><input name="url" type="text" class="B_input B_fl" size="35"><a class="B_helpA"><i>֧��Mp3 ra rm wma�����ָ�ʽ���ӵ�ַ<br>ʾ��:http://server/filename.mp3</i></a></td>\
				</tr>\
				<tr>\
					<td>���ã�</td>\
					<td><label><input name="autoPlay" type="checkbox">�Զ�����</label></td>\
				</tr>\
			</tbody></table>\
		</form>\
		<div class="B_tac B_p10"><span class="B_btn2"><span><button class="B_sumbit" type="button">�� ��</button></span></span><span class="B_bt2"><span><button class="B_close" type="button">ȡ ��</button></span></span></div>\
	</div>\
</div>',
			callback: function(popup){
				var btn = B.$('#B_editor_music .B_sumbit');
				B.addEvent(btn, 'click', function(){
					var form = document.B_editor_musicForm,
					url = form.url.value;
					autoPlay = form.autoPlay.checked ? 1 : 0,
					str = '[mp3=' + autoPlay + ']' + url + '[/mp3]';
					insertTrigger(str);
					form.reset();
					popup.closep();
				});
			}
		});
		
		//���¼�
	});
	
	
	B.editor.insertmusic = function(elem, fn){
		insertTrigger = fn;
		B.util.dialog({
			id: 'B_editor_music',
			pos: ['leftAlign', 'bottom']
		}, elem);
	}
});
// JavaScript Document
Breeze.namespace('app.post', function(B) {
	var callbackTrigger;
	function code2HTML(str){
		str = str.replace(/</g, '&lt;');
		str = str.replace(/>/g, '&gt;');
		return str.replace(/\r?\n/g, '<br />');
	}
	B.require('util.dialog', function(B){
		B.util.dialog({
			id: 'B_editor_post',
			reuse: true,
			outWin: true,
			pos: ['left','top',-10000, 0],
			data: '<div class="B_menu B_p10B">\
	<div style="width:310px;">\
		<div class="B_h B_drag_handle"><a href="#" class="B_menu_adel B_close">��</a>������������</div>\
		<form name="B_editor_postForm" class="B_tableA mb10">\
			<div class="B_mb5">�����������غ������û���Ҫ�ظ�����������</div>\
			<div id="B_editor_postText" class="B_mb10"><textarea name="content" rows="5" style="width:300px;overflow:auto;line-height:1.5;border:1px solid #ccc;"></textarea>\
			</div>\
		</form>\
		<div class="B_tac"><span class="B_btn2 B_submit"><span><button type="button">�� ��</button></span></span><span class="B_bt2 B_close"><span><button type="button">ȡ ��</button></span></span></div>\
	</div>\
</div>',
			callback: function(popup){
				//���ύ��ť
				var btn = B.$('#B_editor_post .B_submit');
				B.addEvent(btn, 'click', function(){
					var form = document.B_editor_postForm,
					content = code2HTML(form.content.value);

					callbackTrigger('[post]' + content + '[/post]');
					form.reset();
					popup.closep();
				});
			}
		});
		
		//���¼�
	});
	/**
	 * @description ͼƬѡ����
	 * @params {String} Ҫ����ͼƬѡ������Ԫ��
	 * @params {Function} ���ͼƬ������Ļص�����
	 */
	B.app.post = function(elem, fn, editor) {
		iscollapsed = !editor.isSel();
		if(iscollapsed){
			B.util.dialog({id:'B_editor_post',pos:['leftAlign', 'bottom']},elem);
			callbackTrigger = fn;
		}else{
			fn('[post]' + editor.getSelHtml() + '[/post]');
			//editor.pasteHTML('[post]' + editor.getSelText() + '[/post]');
		}
    }
});
/*
* app.insertAttach ģ��
* ��������ģ��
*/
Breeze.namespace('app.insertAttach', function (B) {
	//flash�ؼ�
	var $ = B.query, index = 1;
	var win = window, doc = document,
		defaultConfig = {
			rspHtmlPath: attachConfig.url,
			callback: function () { }
		},
		tattachSelector = {
			id: 'editor-insertTattach',
			load: function (elem, myeditor) {
				var id = this.id;
				B.require('util.dialog', function (B) {
					B.util.dialog({
						pos: ['leftAlign', 'bottom'],
						id: id,
						data: '<div class="B_menu B_p10B">\
	<div style="width:480px;">\
		<div class="B_h B_cc B_drag_handle">\
			<a href="javascript://" class="B_menu_adel B_close" style="margin-top:2px;">��</a>�����ϴ�\
		</div>\
		<!--�����б�ʼ-->\
		<div style="padding-top:5px">\
			<div class="B_mb10 cc">\
				<a id="B_sm_cfg" href="javascript://" class="B_fr">��ʾ���������á�</a>\
				<span id="B_uploader_container"><span id="B_uploader_flash"><span id="uploaderTmpSpan" style="display:none;"><embed style="display:none;" src="images/blank.swf" type="application/x-shockwave-flash" wmode="transparent"/><em class="s2" style="position:relative;top:3px;">���������δ��װflash�����<a href="http://www.adobe.com/go/getflashplayer" target="_blank">�����װ</a></em></span></span></span>\
			</div>\
			<div class="B_file">\
				<div class="cc">\
					<dl style="background:#f7f7f7;">\
						<dt>������&nbsp;<span class="gray">(���������ϴ�<span class="restCount s2"></span>������)</span></dt>\
						<dd>��������</dd>\
						<dd class="B_file_dd">����</dd>\
					</dl>\
					<div id="B_qlist"></div>\
					<!--ѡ���ļ��� ��ʾ���б�-->\
				</div>\
			</div>\
			<div class="B_file_tips">\
				<a class="B_helpA" style="float:right;padding:0 0 0 18px;width:auto;" onclick="event&&(event.returnValue=false);">���ϴ�����<i id="attach_allow_filetype"></i></a>\
				<label><input name="atc_hideatt" type="checkbox" value="1" style="padding:0;margin:-2px 0 0;"> ���ظ������ظ��ɼ���<input type="hidden" name="isAttachOpen" value="1" /></label>\
			</div>\
		<!--����-->\
	</div>\
</div>',
						reuse: true,
						callback: function (popup) {
							B.require('global.uploader',function(){
								uploader.init('uploader',myeditor);
								B.addEvent(B.$('#B_sm_cfg'), 'click', uploader.toggleSelect);
							});
							myeditor.area.appendChild(popup.win);
						}
					}, elem);
				});
			}
		};

    /**
    * @description ͼƬѡ����
    * @params {String} Ҫ��������ѡ������Ԫ��
    * @params {Function} ѡ�񸽼�������Ļص�����
    */
	B.app.insertAttach = function (elem, callback, editor) {
		insertTrigger = callback;
		myeditor = editor;
		if (B.$('#'+tattachSelector.id)){
			B.util.dialog({
				id: tattachSelector.id,
				reuse: true,
				callback:function(){uploader.init('uploader',myeditor);},
				pos: ['leftAlign', 'bottom']
			}, elem);
		} else {
			tattachSelector.load(elem, myeditor);
		}
    }
});

/*
������漰����ͨ��ajax����HTML,�����¼�������InsertAttach��tattachSelector.load()��ʵ����,����colorpicker�е㲻ͬ,�ֿ���html��eventΪ�˸�����ά�����Ķ�
*/
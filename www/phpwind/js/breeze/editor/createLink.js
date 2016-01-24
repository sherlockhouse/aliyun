/*
* app.emotional ģ��
* �������ģ��
*/
Breeze.namespace('editor.createLink', function (B) {
    function CreateLink(text, url, callback, elem) {
        if (typeof text == 'function') {
            callback = text;
            text = url = '';
        } else if (typeof url == 'function') {
            callback = url;
            url = '';
        }

        B.require('util.dialog', function (B) {
            B.util.dialog({
                id: 'B_editor_url',
                reuse: true,
				pos: ['leftAlign', 'bottom'],
                data: '<div class="B_menu B_p10B">\
	                    <div style="width:310px;">\
		                    <div class="B_h B_drag_handle"><a href="#" class="B_menu_adel B_close">��</a>����URL����</div>\
		                    <div class="B_tableA">\
			                    <table width="100%" class="B_mb10" id="B_link_table">\
				                    <tr>\
					                    <td width="60">˵����</td>\
					                    <td><input name="" type="text" class="B_input" size="35" value=""/></td>\
				                    </tr>\
				                    <tr>\
					                    <td>��ַ��</td>\
					                    <td><input name="" type="text" class="B_input" size="35" value="http://"/></td>\
				                    </tr>\
														<tr>\
					                    <td>���ã�</td>\
					                    <td><input name="" id="url-isdownload" type="checkbox" value="1"><label for="url-isdownload">��Ϊ��������</label></td>\
				                    </tr>\
                            <tbody id="B_link_tbody"></tbody>\
			                    </table>\
		                    </div>\
		                    <div class="B_tac B_p10"><span class="B_btn2"><span><button id="btn_submit_url" type="button">�� ��</button></span></span><span class="B_bt2"><span>\                     <button type="button" class="B_close">ȡ ��</button></span></span></div>\
	                    </div>\
                    </div>',
                callback: function (popup) {
                    var table = B.$('#B_link_table'), tbody = B.$('#B_link_tbody');
                    table.rows[1].cells[1].firstChild.value = url||'http://';

					//IE��������
					if (B.UA.ie){
						var _tmp = B.createElement('div');
						_tmp.innerHTML = text;
						text = _tmp.innerText;
					}
                    table.rows[0].cells[1].firstChild.value = text;
                    while (tbody.hasChildNodes()) {
                        tbody.removeChild(tbody.firstChild);
                    }
                    B.$('#url-isdownload').checked = false;
                    B.query('#B_editor_url .B_add_link').addEvent('click', function (e) {
                        var row = tbody.insertRow(tbody.rows.length),
				        cell0 = row.insertCell(0),
				        cell1 = row.insertCell(1),
				        cell2 = row.insertCell(2);
                        cell0.innerHTML = '<input name="" type="text" class="B_input" size="30" value="http://">';
                        cell1.innerHTML = '<input name="" type="text" class="B_input" size="10" value="">';
                        cell2.innerHTML = '<a href="javascript:;" class="s2">ɾ��</a>';
						e.preventDefault();
                    });
                    //���ɾ��,ɾ��һ��������
                    /*B.live('a.s2', 'click', function (e) {
                        e.preventDefault();
                        B.remove(this.parentNode.parentNode);
                    });*/
                    //���ȷ�ϰ�ť������Ҫ��html
                    B.$('#btn_submit_url').onmousedown = function (e) {
                        var rows = table.rows,
                            isdownload = B.$('#url-isdownload').checked,
                            html = '';
                            var url = rows[1].cells[1].firstChild.value,
                                text = rows[0].cells[1].firstChild.value;
														if(text.length == 0){
															text = url;
														}
                            html += '<a href="' + url + (isdownload ? ',1' : '') + '" target="_self">' + text + '</a> ';
                        callback(html);
                        popup.closep();
						return false;
                    }
                }
            }, elem);
        });
    }
    /**queryValue
    * @description url����ģ��
    * @params {String} Ҫ�������ӵ�����(��ѡ)
    * @params {String} Ҫ�������ӵ�URL(��ѡ)
    * @params {Function} �ύ�ɹ���Ļص�����,�ص������Ĳ���Ϊ�������������html
    */
    B.editor.createLink = function (elem, callback, text, url) {
        new CreateLink(text, url, callback, elem);
    }
});

/*
* ajaxForm ģ��
* ʹform�ύ�����ˢ��ʽ��
*/
Breeze.namespace('util.ajaxForm', function (B) {

    function AjaxForm(form, callback) {
        this.form = form;
        this.callback = callback;
        if (!this.form || this.form.tagName !== 'FORM') {//����ΪformԪ��
            return false;
        }
        this._initialize();
    }

    AjaxForm.prototype = {
        _load: function (frame) {
            if (frame.contentDocument) {
                var d = frame.contentDocument;
            } else if (frame.contentWindow) {
                var d = frame.contentWindow.document;
            }
			var data = (typeof d.documentElement != 'undefined') ?
					d.documentElement.textContent :
					d.body.innerHTML;
            this.callback(data);
        },
        _initialize: function () {
            var self = this, form = self.form, callback = self.callback,
                    n = new Date().getTime(),
					f = B.createElement('<iframe style="display:none" src="javascript:void(0);" id="' + n + '" name="' + n + '"></iframe>');

			if (f.attachEvent){
				f.onreadystatechange = function () {
                    if ( f.readyState == "complete" &&  f.src != 'javascript:void(0);' ) {
						self._load(f);
                    }
                }
			} else {
				f.onload = function (){
                    self._load(f);
                }
			}
			document.body.appendChild(f);
/*		            d = document.createElement('div');
            d.innerHTML = 
            document.body.appendChild(d);
            var frame = document.getElementById(n);
			if (frame.attachEvent) {
                frame.onreadystatechange = function () {
                    if (frame.readyState == "complete") {
						if(frame.src != 'javascript:void(0);' )
							self._load(frame);
                    }
                }
            } else {
                frame.onload = function () {
                    self._load(frame);
                }
            }
*/
            form.setAttribute('target', n);
            form.method = 'post';
        }
    }

    /**
    * @description ��ˢ�±�
    * @params {String} Ҫ������ˢ�±���form
    * @params {Function} �ύ�ɹ���Ļص�����,�ص������Ĳ���Ϊ�������������html
    */
    B.util.ajaxForm = function (form, callback) {
        form = typeof form === 'string' ? B.$(form) : form;
        new AjaxForm(form, callback);
    };
});

/*
���Լ���˼·д��һ������AJAX FORM�ύ,��requestģ�����Ѿ�����ajax�����ύ,�����ﲻ����,����ֻ�����Ķ�form���ύ
*/
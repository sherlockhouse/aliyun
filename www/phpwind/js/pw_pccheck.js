/**
 *����֤��
 *ʹ�÷�����Form.validate(Form_name)  Form_name:����nameֵ
 *
 */
~
function()
{
    Form =
    {};
    var getObj = function(s)
    {
        return  document.getElementById(s);
    };
    /**
   *@param string frm ����nameֵ
   */
    Form.validate = function(frm)
    {
        var ale = document[frm].elements;
        var submit = document[frm].onsubmit;
        function _e()
        {	
            submit ? submit() : 0;
			
            var error = 0;
            for (var i = 0,len = ale.length; i < len; i++)
            {
                ale[i].onblur ? ale[i].onblur() : 0;
                if (ale[i].getAttribute("hasError") == 1)
                {
                    error++;
                }
            }
            if (error > 0)
            {
				document[frm].Submit.disabled = false;
				cnt = 0;
                return  !! showDialog('error', '���⡢���ݲ���Ϊ�ջ���������������',2);
            }
        }
        function _DateTotime (str) {
        	var new_str = str.replace(/:/g,'-');
        	new_str = new_str.replace(/ /g,'-');
        	var arr = new_str.split("-");
        	if (arr[3] || arr[4]) {
        		var datum = new Date(Date.UTC(arr[0],arr[1]-1,arr[2],arr[3]-8,arr[4]));
        	} else {
        		var datum = new Date(Date.UTC(arr[0],arr[1]-1,arr[2]));
        	}
        	return datum.getTime()/1000;
        }
        function getCalendarError() {
        	var startTimestamp;
        	var endTimestamp;
        	try {
        		var newserror = getObj('tip_postcate[endtime]');
        		startTimestamp = _DateTotime(getObj('calendar_begintime').value);
        		endTimestamp = _DateTotime(getObj('calendar_endtime').value);
        	}catch(e){}
        	if(startTimestamp > endTimestamp) {
        		newserror.className = "msg error";
        		newserror.innerHTML='��ʼʱ�䲻�����ڽ���ʱ��';
        	}
        }
		/**
		*���ﲻ����attachEvent�����¼�������return  falseҲ���ύ��
		*/
        //document[frm].onsubmit = _e;
		/**
		*������ʾ��Ϣ���ڲ�����
		*@param nodeElement obj �ڵ�
		*/
        function _setNotic(obj, pass, range)
        {
            var a = document.createElement("div");
            //a.style.height = obj.offsetHeight + "px";
            a.id = "tip_" + obj.name;
			var newserror = obj.getAttribute("error");
            if (pass) {
                a.className = "msg pass";
                obj.setAttribute("hasError", 0);
                a.innerHTML = obj.getAttribute("pass") || "&nbsp;";
            } else {
                a.className = "msg error";
                obj.setAttribute("hasError", 1);

				if (obj.value != '') {
					if (newserror == 'email_error' || newserror == 'email_error2') {
						newserror = 'Email��ʽ����';
					} else if (newserror == 'number_error' || newserror == 'number_error2') {
						newserror = '����Ϊ����';
					} else if (newserror == 'numberic_error' || newserror == 'numberic_error2') {
						newserror = '����Ϊ��������';
					} else if (newserror == 'rang_error' || newserror == 'rang_error2') {
						newserror = '�������ͷ�Χ����ȡֵΪ'+range[0]+'-'+range[1];
					}
				} else {
					
					newserror = '';
				}

                a.innerHTML = newserror || "�������";
            }
			
			if (obj.type == 'checkbox') {
				obj.parentNode.insertBefore(a, null);
			} else {
				obj.parentNode.insertBefore(a, obj.nextSibling);
			}
            
        }
        for (var i = 0,len = ale.length; i < len; i++)
        {
            ale[i].onblur = function()
            {
                var getc = this.getAttribute("check");
                if (getc)
                {
                    var a = getObj("tip_" + this.name);
                    a ? a.parentNode.removeChild(a) : 0;
                    var newerror = this.getAttribute("error");
					/**
					*�����checkbox�������checkbox�ĸ������������õ�����check��ķ�Χ���бȽ�
					*/
                    if (this.type == "checkbox")
                    {
                        var allCheckboxs = document.getElementsByName(this.name);
                        var checked = 0;
                        for (var j = 0,
                        lens = allCheckboxs.length; j < lens; j++)
                        {
                            if (allCheckboxs[j].checked)
                            {
                                checked++;
                            }
                        }
                        var chk = this.getAttribute("check");
                        if (!chk) return;
                       
						/**
						*��"-"��������Сֵ�����ֵ
						*/
                        var mm = chk.split("-");
                        var isTrue;
                        mm[0] = Math.floor(mm[0]);
                        mm[1] ? mm[1] = Math.floor(mm[1]) : 0;
                        if (mm[1])
                        {
                            if (checked <= mm[1] && checked >= mm[0])
                            {
                                isTrue = true;
                            } else
                            {
                                isTrue = false;
                            }
                        } else
                        {
                            if (checked >= mm[0])
                            {
                                isTrue = true;
                            } else
                            {
                                isTrue = false;
                            }
                        }
						/**
						*���һ��checkbox���������֤��ʾ
						*/
                        var lastCheckbox = allCheckboxs[allCheckboxs.length - 1];
                        _setNotic(lastCheckbox, isTrue);
                        return  false;
                    }
					/**
					*�������/��ͷ����֤,��ʶ��Ϊ������֤������Ļ�������Ϊ�Ƿ�Χ����֤
					*/
                    if (getc.indexOf("/") != 0)
                    {
                        var range = getc.split("-");
						if (newerror == 'rang_error2' && (this.value == '' || this.value == 0)) {
							isTrue = true;
						} else {
							if (range[0] == 'n' || range[1] == 'n') {
								if (range[0] == 'n' && range[1] != 'n') {
									isTrue = range[1] >= Math.floor(this.value) ? true : false;
								} else if (range[0] != 'n' && range[1] == 'n') {
									isTrue = range[0] <= Math.floor(this.value) ? true : false;
								} else {
									regstr = /^\d+$/;
									isTrue = regstr.test(this.value) ? true : false;
								}
							} else {
								var isTrue=	this.value != '' && range[0] <= Math.floor(this.value) && range[1] >= Math.floor(this.value);
							}
						}
                    } else  {
                       try {
						var r = eval(getc);
						}
						catch (e) {
							var r = new RegExp();
						}
						if (newerror == 'email_error2' && (this.value == '' || this.value == 0)) {
							isTrue = true;
						} else if (newerror == 'number_error2' && (this.value == '' || this.value == 0)) {
							isTrue = true;
						} else if (newerror == 'numberic_error2' && (this.value == '' || this.value == 0)) {
							isTrue = true;
						} else {
							var testValue1 = this.value.split("\n");
							var testValue2 = this.value.split("\r\n");
							var isTrue1 = r.test(testValue1);
							var isTrue2 = r.test(testValue2);
							if (isTrue1 == true || isTrue2 == true) {
								isTrue = true;
							}
						}
                    }
                    _setNotic(this, isTrue, range);
                    getCalendarError();
                }
            }
        }
    };
} ();
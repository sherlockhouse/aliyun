/*
* app.emotional ģ��
* �������ģ��
*/
Breeze.namespace('app.emotional', function (B) {
    var win = window, doc = document, IMGPATH = 'images/post/smile/',
	    defaultConfig = {
	        defaultface: defaultface,
	        face: face,
	        pageNum: 24,
			tabNum:3,
	        triggerType: 'click'
	    },
		myeditor,
    /**
    * �������ѡ��������
    */
	    emotionalSelector = {
	        id: 'breeze-emotional',
	        load: function () {
	            B.require('dom', 'event', function (B) {
					var contain = B.createElement('<div class="B_menu B_p10B" id="' + emotionalSelector.id + '" style="display:none;z-index:2999"></div>'),
					outer = B.createElement('<div style="width:271px"></div>'),
					nav = B.createElement('<div class="B_menu_nav B_cc B_drag_handle"></div>'),
					closeButton = B.createElement('<a style="margin-top: 7px;" class="B_menu_adel" href="javascript:;">��</a>'),
					facePages = B.createElement('<div class="B_face_pages"></div>'),
					tabHead = B.createElement('<ul class="B_cc B_fl" style="width: 210px;"></ul>'),
					df = document.createDocumentFragment();
					/*
					* �����������ǩ�Ա�tab
					*/
					var j=0;
					for (var i in facedb) {
						var navli = B.createElement('<li><a href="javascript:;">' + facedb[i] + '</a></li>');
						navli.className = i == defaultConfig.defaultface ? 'B_tab_trigger current' : 'B_tab_trigger';
						if(++j>defaultConfig.tabNum)navli.style.display = 'none';
						df.appendChild(navli);
						df.innerHTML = '';
					}
					tabHead.appendChild(df);
					nav.appendChild(B.createElement('<a class="B_menu_adel B_close" href="javascript:;">��</a>'));
					if (j > defaultConfig.tabNum) {
						nav.appendChild(B.createElement('<div title="��һҳ" class="B_menu_pre fl">��һҳ</div>'));
						nav.appendChild(B.createElement('<div title="��һҳ" class="B_menu_next fr">��һҳ</div>'));
					}
					nav.appendChild(tabHead);
					outer.appendChild(nav);

					/*
					* ��������ͼƬ
					*/
					for (var i in faces) {//�޷���forEach,��Ϊ�ϰ汾������ԭ��
						if (faces.hasOwnProperty(i)) {
							var faceList = B.createElement('<div class="B_tab_panel"></div>'),
							faceGroup = B.createElement('<ul class="B_face_list B_cc"><ul>'), faceul = [];
							faceList.style.display = i == defaultConfig.defaultface ? 'block' : 'none';
							for (var j in faces[i]) {
								if (faces[i].hasOwnProperty(j)) {
									var li = '<li><img data-code="' + faces[i][j] + '" src="' + IMGPATH + face[faces[i][j]][0] + '" title="' + face[faces[i][j]][1] + '" onclick="" /></li>';
									faceul.push(li);
								}
							}
							faceGroup.innerHTML = faceul.slice(0, defaultConfig.pageNum).join(''); //ֻ��ʾ��һҳ������
							faceList.appendChild(faceGroup);
							outer.appendChild(faceList);

							/*
							* ���������ҳ��ǩ
							*/
							var pageNum = defaultConfig.pageNum, itemCount = faceul.length;
							if (itemCount > pageNum) {//��Ҫ��ҳʱ�Ŵ�����ҳ
								var pageCount = itemCount % pageNum == 0 ? itemCount / pageNum : Math.floor(itemCount / pageNum) + 1;
								var pageGroup = B.createElement('<ul class="B_face_pages B_cc"></ul>');
								for (var i = 0; i < pageCount; i++) {
									var pageEl = B.createElement('<a href="javascript:;">' + (i + 1) + '</a>');
									pageEl.className = i == 0 ? 'current' : "";
									(function (i, pageEl, faceul, faceGroup, pageGroup) {//��ҳ�¼�����
										B.addEvent(pageEl, 'click', function (e) {
											e.halt();
											faceGroup.innerHTML = faceul.slice(i * pageNum, i * pageNum + defaultConfig.pageNum).join('');
											B.removeClass(B.$('.current', pageGroup), 'current');
											B.addClass(pageEl, 'current');
											B.$$('li img', faceGroup).forEach(function(n){
												B.addEvent(n,'click', function (e) {
													insertEmotional(n);
												});
											});
										});
									})(i, pageEl, faceul, faceGroup, pageGroup);
									var li = B.createElement('li');
									li.appendChild(pageEl);
									pageGroup.appendChild(li);
								}
								faceList.appendChild(pageGroup);
							}
						}
					}
					contain.appendChild(outer);
					doc.body.appendChild(contain);
	            });
	        }
	    }
    /**
    * �������
    */
    function hideEmotionalSelector() {
        B.$('#' + emotionalSelector.id).style.display = 'none';
    }

	function insertEmotional(obj) {
		var code = B.attr(obj, 'data-code'),
			src = obj.src;
		//myeditor.saveRng();
		insertTrigger('<img src="'+ src +'" title="'+ obj.title +'" emotion="'+ code +'" >');
		B.UA.gecko && myeditor.focus();
		/*
		if (myeditor.currentMode == 'UBB' && B.UA.ie){
			setTimeout(function(){
				var rng = myeditor.getRng();
				rng.collapse(false);
				rng.select();
			}, 100);
		}
		*/
	}

	function showTab(p) {
		var index = -1;
		var menus = B.$$('#' + emotionalSelector.id + ' .B_tab_trigger');
		for (var i = 0; i < menus.length; i++) {
			if (menus[i].style.display != "none") {
				index = i;
				break;
			}
		}
		index += p;
		if (index < 0 || index + defaultConfig.tabNum > menus.length) return;
		for (i = 0; i < menus.length; i++){
			if (i >= index && i < index + defaultConfig.tabNum){
				menus[i].style.display = "";
			} else{
				menus[i].style.display = "none";
			}
		}
	}

    /**
    * emotional��
    */
    B.require('util.dialog', function (B) {//add event for Emotional
        var selector = '#' + emotionalSelector.id;
        if(!B.$(selector)) {
            emotionalSelector.load();
        }
        /**
        * ��ʾ����ѡ�����
        */
        B.util.dialog({
            id: emotionalSelector.id,
            reuse: true,
            callback:function(popup) {
                /**
                * �ص�����
                */
                B.$$(selector + ' .B_face_list li img').forEach(function(n){
					B.addEvent(n,'mousedown', function (e) {
						insertEmotional(n);
						e.halt();
						//popup.closep();
					});
					
                });
                /**
                * �ر�����¼�
                */
                B.$$(selector + ' .B_menu_adel').forEach(function (n) {
                    B.addEvent(n, 'click', function (e) {
                        e.preventDefault();
                        hideEmotionalSelector();
                    });
                });


                /**
                * ������Ҽ�ͷ�л�tab(��һҳ)
                */
                B.addEvent(B.$(selector + ' .B_menu_pre'), 'click', function (e) {
					showTab(-1);
                    e.preventDefault();
                    //�л�tab

                });

                /**
                * ������Ҽ�ͷ�л�tab(��һҳ)
                */
                B.addEvent(B.$(selector + ' .B_menu_next'), 'click', function (e) {
					showTab(1);
                    e.preventDefault();
                    //�л�tab
                });
                /**
                * �л�tabʱ����
                */
                B.$$(selector + ' .B_tab_trigger').forEach(function (n) {
                    B.addEvent(n, 'click', function (e) {
                        B.$$(selector + ' .B_tab_trigger').forEach(function (n) {
                            B.removeClass(n, 'current');
                        });
                        B.addClass(this, 'current');
						e.preventDefault();
                    });
                });
                
                /**
                * ����tabЧ��
                */
                B.require('util.scrollable', function (B) {
                    B.util.tabs("#" + emotionalSelector.id);
                });
                popup.closep();
            }
        });
		/**
		* @description ����ѡ����
		* @params {le} le
		* @params {Function} �����������������Ļص�����
		*/
		B.app.emotional = function (elem, callback, editor) {
			insertTrigger = callback;
			myeditor = editor;
			B.util.dialog({
				id: emotionalSelector.id,
				pos: ['leftAlign', 'bottom']
			},elem);
		}
    });
});

/*
*PS:����������ϰ汾�ı���ѡ����,����������������󲿷���ɣ�DOM�ṹ����(emotionalSelector��) + �¼�����(Emotional��)
*��ͷ��tab����ʱû��ʵ��,��Ϊtab����в�����,������д���Եù��ܶ�����,����һ��ʵ��
*/
/*
* app.remotepic ģ��
* Զ��ͼƬ����ģ��
*/
Breeze.namespace('app.remotepic', function (B) {
	function closeAll(){
		B.query('.B_menu').css('display', 'none');
	}
	var $ = B.query, index = 1;
	var win = window, doc = document,
		defaultConfig = {
			rspHtmlPath: attachConfig.url,
			callback: function () { }
		},
		tattachSelector = {
			id: 'editor-remotepic',
			hideInter:null,
			pics:{key:[],val:[]},
			load: function (elem, myeditor) {
				var self=this;
				var id = this.id;
				B.require('util.dialog', function (B) {
					B.util.dialog({
						pos: ['leftAlign', 'bottom'],
						id: id,
						data: '<div class="B_menu B_p10B">\
								<div style="width:200px;">\
									<div class="B_h B_cc B_drag_handle">\
										<a href="javascript://" class="B_menu_adel B_close" style="margin-top:2px;">��</a>Զ��ͼƬ����\
									</div>\
									<!--�����б�ʼ-->\
									<div style="padding-top:5px">\
										<div class="B_remote">\
											<div class="cc" style="padding-top:30px;" id="B_remote_cont">\
												<img src="images/loading_r.gif"/><br/><br/>������...\
											</div>\
											<span class="B_remote_tip" id="B_remote_tip"></span>\
										</div>\
									</div>\
									<!--����-->\
								</div>\
							</div>',
						reuse: true,
						callback: function (popup) {
							myeditor.area.appendChild(popup.win);
							var ubb=myeditor.getUBB();
								self.parseHTML(ubb);
						}
					}, elem);
				});
			},
			parseHTML:function(str){
				//��ʼ���ײ���ʾ��Ϣ
				getObj("B_remote_tip").innerHTML="";
				var self=this;
				var pics=this.pics.key;
				var urls=[];
				var tipsStr;
				var s=str.replace(/\[img\]\s*(?=http)(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/img\]/ig,function(all,url){
					//����Զ�̸���
					if(attachConfig&&attachConfig.remoteAttUrl){
						var remoteUrl=attachConfig.remoteAttUrl;
						if(url.indexOf(remoteUrl)>-1){
							return false;
						}
					}
					//end
					pics.push(all);
					urls.push(url);
				});
				if(urls.length<1){
					getObj("B_remote_cont").innerHTML="��Զ��ͼƬ";
					tattachSelector.hideInter=setTimeout(function(){
						getObj(self.id).style.display="none";
					},3000)
					return false;
				}
				self.loadImg(str,urls);
			},
			getPostPics:function(pics){
				var data = '';
				for (var i=0,len=pics.length;i<len;i++){
					if (data) data += '&';
					data += 'urls['+ i +']=' + pics[i];
				}
				if(attachConfig&&attachConfig.type){
					data+="&type="+attachConfig.type;
				}
				if(attachConfig&&attachConfig.postData){
					var postdata=attachConfig.postData;
					for(var i in postdata){
						data+="&"+i+"="+postdata[i];
					}
				}
				return data;
			},
			loadImg:function(str,pics){
					var self=this;
					var mode=myeditor.currentMode;
					getObj("B_remote_cont").innerHTML='<img src="images/loading_r.gif"/><br/><br/>Զ��ͼƬ������,���Ժ�...';
					B.require('global.uploader',function(){
						var availLen=uploader.getRestCount();
						if(availLen<1){
							getObj("B_remote_tip").innerHTML="";
							getObj("B_remote_cont").innerHTML='<span style="color:#f00">���Ѿ��ﵽ��������</span>';
							tattachSelector.hideInter=setTimeout(function(){
									getObj(self.id).style.display="none";
							},3000)
							return false;
						}
						if(pics.length>availLen){
							tipsStr="���������ϴ�����,��������"+availLen+"��";
							//tipsStr="����"+pics.length+"��Զ��ͼƬ��������"+availLen+"��";
							getObj("B_remote_tip").innerHTML=tipsStr;
							pics=pics.slice(0,availLen);
						}
						var data = self.getPostPics(pics);
						
						ajax.send("job.php?action=remotedownload",data,function(){
								var index=0;
								var failNum=0;
								var successNum=0;
								try{
									
									/*var end=ajax.request.responseText.lastIndexOf(",");
									ajax.request.responseText=ajax.request.responseText.substr(0,end);*/
									var json="{"+ajax.request.responseText+"}";
									var json=eval("("+json+")");
									//��ʽ  var json={'80' : ['Chrysanthemum.jpg', '859', 'attachment/Mon_1107/5_1_c32ab805d9c8963.jpg', 'img', '0', '0', '', ''],'81' : ['Desert.jpg', '827', 'attachment/Mon_1107/5_1_39725fd5f4ae645.jpg', 'img', '0', '0', '', ''],'82' : ['Hydrangeas.jpg', '582', 'attachment/Mon_1107/5_1_cfca2e0916365b4.jpg', 'img', '0', '0', '', ''],'83' : ['Jellyfish.jpg', '758', 'attachment/Mon_1107/5_1_742edcbdf53cb76.jpg', 'img', '0', '0', '', ''],'84' : ['Koala.jpg', '763', 'attachment/Mon_1107/5_1_813237795e63c97.jpg', 'img', '0', '0', '', ''],'85' : ['Lighthouse.jpg', '549', 'attachment/Mon_1107/5_1_65124265c1a957a.jpg', 'img', '0', '0', '', ''],'86' : ['Penguins.jpg', '760', 'attachment/Mon_1107/5_1_ff09342db99f3bb.jpg', 'img', '0', '0', '', ''],'87' : ['Tulips.jpg', '607', 'attachment/Mon_1107/5_1_acdf5acb0afe2f2.jpg', 'img', '0', '0', '', '']};
									var editor_remote_holder=document.createElement("div");
									for(var i in json){
										index++;
										//��������
										if(index>availLen){
											return false;
										}
										//���ͼƬ����ʧ��
										if(json[i].length<1){
											self.pics.val.push("");
											failNum++;
											continue;
										}
										//�����ݺ�uploaderͬ��
										uploader.data[i]=json[i];
										self.pics.val.push(json[i][0]);
										str=str.replace(self.pics.key[successNum],"[attachment="+i+"]");
										
										var input=document.createElement("input");
										input.setAttribute("type","hidden");
										input.name="flashatt["+i+"][desc]";
										input.id="tmpRemoteHidden"+i;
										editor_remote_holder.appendChild(input);
										successNum++;
									}
									uploader.amount+=successNum;
									myeditor.area.appendChild(editor_remote_holder);
									
									if(mode=="default"){
										myeditor.setHTML(myeditor.ubb2html(str));
									}else if(mode=="UBB"){
										myeditor.setHTML(str);
									}
									var failStr=failNum>0?"����ʧ��"+failNum+"��<br/>ʧ�ܿ���ԭ��ͼƬ�ߴ�����������������":"";
									getObj("B_remote_cont").innerHTML='<img src="images/success_bg.gif"/><br/><br/>Զ��ͼƬ�������.'+failStr;
									//getObj("B_remote_cont").innerHTML='<img src="images/success_bg.gif"/><br/><br/>Զ��ͼƬ�������.��'+index+'��'+failStr;
									self.pics={key:[],val:[]};
									tattachSelector.hideInter=setTimeout(function(){
										getObj(self.id).style.display="none";
									},3000)
								}catch(e){
									getObj("B_remote_cont").innerHTML="Զ��ͼƬ����ʧ��.<br/>ʧ�ܿ���ԭ��ͼƬ�ߴ�����������������";
									tattachSelector.hideInter=setTimeout(function(){
										getObj(self.id).style.display="none";
									},3000)
								}	
						});
					});	
			}
		};
    /**
    * @description ͼƬѡ����
    * @params {String} Ҫ��������ѡ������Ԫ��
    * @params {Function} ѡ�񸽼�������Ļص�����
    */
	B.app.remotepic = function (elem, callback, editor) {
		insertTrigger = callback;
		myeditor = editor;
		if(tattachSelector.hideInter){
			clearTimeout(tattachSelector.hideInter);
		}
		if (B.$('#'+tattachSelector.id)){
			B.util.dialog({
				id: tattachSelector.id,
				reuse: true,
				callback:function(){
							var ubb=myeditor.getUBB();
								tattachSelector.parseHTML(ubb);
				},
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
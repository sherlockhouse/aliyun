
function FlashPlayer(url,id) {
	return is_ie ? '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="1" height="1" id="'+id+'" name="'+id+'"><param name="movie" value="'+url+'" ></object>' : '<embed src="'+url+'" name="'+id+'" id="'+id+'" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"  type="application/x-shockwave-flash" FlashVars="'+url.replace(/^[^\?]+\?/,'')+'" width="1" height="1"></embed>';
}
/**
 *�������ݻ��档
 ע�⣬�뽫��js��Ҫ����body��ǩ�ڣ����ⷢ�������ݵ����⡣
 ʹ�÷�����Session.set(key,value);
 Session.get(key);
 */
//document.write(FlashPlayer("images/userData.swf","userData"));
var Session = {};

Session.init = function() {
	var obj = document.createElement('div');
	obj.innerHTML = FlashPlayer("images/userData.swf","userData");
	document.body.appendChild(obj);
	Session.obj = document["userData"];
	//alert(Session.obj);
}
/**
 *���滺������
 *@param key ����
 *@param value ��ֵ
 */
Session.set =function(key,value)
{
	Session.obj.set(key,value);
};
/**
 *��ȡ��������
 *@param key ����
 */
Session.get =function(key)
{
	return document.userData.get(key);
};
/**
 *ɾ����������
 *@param key ����
 */
Session.remove =function(key)
{
	document.userData.remove(key);
};
Session.init();
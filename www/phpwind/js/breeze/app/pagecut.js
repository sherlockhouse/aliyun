/*
* app.music ģ��
* Ϻ�����ֲ���ģ��
* ��Ϊ���þɴ��룬�����кܶ�ȫ�ֱ�����
*/
Breeze.namespace('app.pagecut', function (B) {
	B.app.pagecut = function(elem, callback, editor) {
		var code = "\n[###page###]\n";
		editor.pasteHTML(code, "");
	}
});
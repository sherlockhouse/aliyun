/**
 *��;����Ϊ��ie��������Ὣ���ֺͻ��е�Ԫ�ص���һ���ڵ����������±����ڵ��ʱ����ںܶ���������⡣
	ʹ�ô˷���ͳһ�������ֽڵ㡣
 *�����ڵ㷽����
 *ʹ�þ�����
 var a=getObj('nodeID');
 $n(a,0.1) �ӽڵ�
 $n(a,0.2) �ӽڵ���ӽڵ�
 ....	   �ӽڵ���ӽڵ�ġ���
 $n(a,0.9) ���֧��0.9

 $n(a,-0.1)	 ���ڵ�
 $n(a,-0.2)	 ���ڵ�ĸ��ڵ�
 ....
 $n(a,-0.9) ���֧��-0.9

 $n(a,1)	 ��һ���ڵ�
 $n(a,2)	 ��һ���ڵ����һ���ڵ�
 ....

 $n(a,-1)	 ��һ���ڵ�
 $n(a,-2)	 ��һ���ڵ����һ���ڵ�
 ....

 �ۺ�ʹ�ã�

 $n(a,0.3,1,0.2,-1)  �����ӽڵ����һ���ڵ�����ӽڵ����һ���ڵ�

 ע�����ڴ�������ķ������������ı�Ŀǰ�в�֧�����磺$n(a,0.21)��$n(a,1.1)��$n(a,-2.3)��������ֵ��
 */
function findNode(obj)
{
    var argu = [];
    for (var i = 1; i < arguments.length; i++)
    {
        argu.push(arguments[i]);
    }
    var n = obj;
    for (var i = 0; i < argu.length; i++)
    {
        if (argu[i] >= 1)
        {
            for (var j = 0; j < argu[i]; j++)
            {
                n = n.nextSibling;
                while (n && n.nodeType == 3)
                {
                    n = n.nextSibling;
                }
            }
        }
        if (argu[i] <= -1)
        {
            for (var j = 0; j < argu[i] * -1; j++)
            {
                n = n.previousSibling;
                while (n && n.nodeType == 3)
                {
                    n = n.previousSibling;
                }
            }
        }
        if ( - 1 < argu[i] && argu[i] < 0)
        {
            for (var j = 0; j > argu[i] * 10; j--)
            {
                n = n.parentNode;
            }
        }
        if (0 < argu[i] && argu[i] < 1)
        {
            for (var j = 0; j < argu[i] * 10; j++)
            {
                n.firstChild ? n = n.firstChild: 0;
                while (n && n.nodeType == 3)
                {
                    n = n.nextSibling;
                }
            }
        }
    }
    return  n;
};
$n=findNode;
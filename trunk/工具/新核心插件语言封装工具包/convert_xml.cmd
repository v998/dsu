@ECHO OFF
ECHO Name: XML ����ת������ (GBK -^> BIG5/UTF8)
ECHO Author: Monkey ^&^& kookxiang
ECHO Date: 2009/5/23 8:46
ECHO ------------------------------------------------------

set /p plugin=-^> ����������Ŀ¼:
set /p xml=-^> ������ XML �ļ���(.xml ��������):

_convertz\ConvertZ /i:GBK /o:BIG5 "%plugin%"\%xml%.xml "%plugin%"\%xml%_TC_BIG5.xml
_convertz\ConvertZ /i:BIG5 /o:UTF8 "%plugin%"\%xml%_TC_BIG5.xml "%plugin%"\%xml%_TC_UTF8.xml
_convertz\ConvertZ /i:GBK /o:UTF8 "%plugin%"\%xml%.xml "%plugin%"\%xml%_SC_UTF8.xml
copy "%plugin%"\%xml%.xml "%plugin%"\%xml%_SC_GBK.xml
@echo off
TITLE �º��Ĳ�����Է�װ���߰� by kookxiang
echo Name: XML ����ת������ (GBK -^> BIG5\UTF8)
echo Author: kookxiang
echo Date: 2011/4/5 10:57
echo ------------------------------------------------------
echo.

if "%1"=="" (
echo ������ֻ֧���Ϸ�ת�룬���϶�XML����bat�ļ��ϲ��ͷ���꣬������Զ�ת��.
echo ��������˳�...
pause > nul
exit
)
cd /d %0\..
echo ת���ļ� %~d1%~p1%~n1.xml
echo.
copy "%~d1%~p1%~n1.xml" "%~d1%~p1%~n1_SC_GBK.xml" > nul
echo ����ת��(GBK-^>BIG5)����
_convertz\ConvertZ /i:GBK /o:BIG5 "%~d1%~p1%~n1.xml" "%~d1%~p1%~n1_TC_BIG5.xml"
echo ����ת��(GBK-^>UTF8)����
_convertz\ConvertZ /i:GBK /o:UTF8 "%~d1%~p1%~n1.xml" "%~d1%~p1%~n1_SC_UTF8.xml"
echo ����ת��(UTF8-^>BIG5)����
_convertz\ConvertZ /i:BIG5 /o:UTF8 "%~d1%~p1%~n1_TC_BIG5.xml" "%~d1%~p1%~n1_TC_UTF8.xml"
echo ת����ɣ�
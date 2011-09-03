@echo off
TITLE 新核心插件语言封装工具包 by kookxiang
echo Name: XML 编码转换工具 (GBK -^> BIG5\UTF8)
echo Author: kookxiang
echo Date: 2011/4/5 10:57
echo ------------------------------------------------------
echo.

if "%1"=="" (
echo 本程序只支持拖放转码，请拖动XML至此bat文件上并释放鼠标，程序会自动转码.
echo 按任意键退出...
pause > nul
exit
)
cd /d %0\..
echo 转换文件 %~d1%~p1%~n1.xml
echo.
copy "%~d1%~p1%~n1.xml" "%~d1%~p1%~n1_SC_GBK.xml" > nul
echo 正在转换(GBK-^>BIG5)……
_convertz\ConvertZ /i:GBK /o:BIG5 "%~d1%~p1%~n1.xml" "%~d1%~p1%~n1_TC_BIG5.xml"
echo 正在转换(GBK-^>UTF8)……
_convertz\ConvertZ /i:GBK /o:UTF8 "%~d1%~p1%~n1.xml" "%~d1%~p1%~n1_SC_UTF8.xml"
echo 正在转换(UTF8-^>BIG5)……
_convertz\ConvertZ /i:BIG5 /o:UTF8 "%~d1%~p1%~n1_TC_BIG5.xml" "%~d1%~p1%~n1_TC_UTF8.xml"
echo 转换完成！
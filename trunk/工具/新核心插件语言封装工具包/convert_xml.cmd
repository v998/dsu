@ECHO OFF
ECHO Name: XML 编码转换工具 (GBK -^> BIG5/UTF8)
ECHO Author: Monkey ^&^& kookxiang
ECHO Date: 2009/5/23 8:46
ECHO ------------------------------------------------------

set /p plugin=-^> 请输入插件的目录:
set /p xml=-^> 请输入 XML 文件名(.xml 不用输入):

_convertz\ConvertZ /i:GBK /o:BIG5 "%plugin%"\%xml%.xml "%plugin%"\%xml%_TC_BIG5.xml
_convertz\ConvertZ /i:BIG5 /o:UTF8 "%plugin%"\%xml%_TC_BIG5.xml "%plugin%"\%xml%_TC_UTF8.xml
_convertz\ConvertZ /i:GBK /o:UTF8 "%plugin%"\%xml%.xml "%plugin%"\%xml%_SC_UTF8.xml
copy "%plugin%"\%xml%.xml "%plugin%"\%xml%_SC_GBK.xml
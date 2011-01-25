@echo off
TITLE file_list 生成工具
set /p plugin_id=请输入插件id:
cd %plugin_id%/upload
xcopy . .. /l /e > ../file_list
cls
echo 生成完毕，请手工将file_list中的斜杠替换，并对尾部进行处理！
echo 按任意键退出
pause > nul
@echo off
TITLE file_list ���ɹ���
set /p plugin_id=��������id:
cd %plugin_id%/upload
xcopy . .. /l /e > ../file_list
cls
echo ������ϣ����ֹ���file_list�е�б���滻������β�����д���
echo ��������˳�
pause > nul
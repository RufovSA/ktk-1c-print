@echo off

:loop
cls
echo =================================
echo ������� �ࢥ� 1�: ������� v 1.0
echo =================================
echo.
echo 1. �������� 1�: �।���⨥
echo 2. Sigur - ������
echo 3. Sigur - ��ࠢ����� �ࢥ஬
echo 4. ��ᯥ��� �㦡 - IIS
echo 5. ���������஢���� �ࢥ஢ 1� �।�����
echo 6. GitHub Desctop
echo 7. ������ ��㧥�
echo 8. Notepad++
echo 9. ��ᯥ��� �����
echo 10. pgAdmin
echo 0. ��室
echo.

:: ���� ������:
set /p Data="> "

if %Data% EQU 1 (
    cls
    echo ����� 1�: �।���⨥
    echo �������� ��������...
    start "1�: �।���⨥" "C:\Program Files (x86)\1cv8\8.3.21.1302\bin\1cv8.exe"
    pause
)

if %Data% EQU 2 (
    cls
    echo ����� Sigur - ������
    echo �������� ��������...
    start "Sigur - ������" "C:\Program Files (x86)\SIGUR access management\������.exe"
    pause
)

if %Data% EQU 3 (
    cls
    echo ����� Sigur - ��ࠢ����� �ࢥ஬
    echo �������� ��������...
    start "Sigur - ��ࠢ����� �ࢥ஬" "C:\Program Files (x86)\SIGUR access management\��ࠢ����� �ࢥ஬.exe"
    pause
)

if %Data% EQU 4 (
    cls
    echo ����� ��ᯥ��� �㦡 - IIS
    echo �������� ��������...
    start "��ᯥ��� �㦡 - IIS" "C:\Windows\system32\inetsrv\iis.msc"
    pause
)

if %Data% EQU 5 (
    cls
    echo ����� ���������஢���� �ࢥ஢ 1� �।�����
    echo �������� ��������...
    start "���������஢���� �ࢥ஢ 1� �।�����" "C:\Program Files (x86)\1cv8\common\1CV8 Servers.msc"
    pause
)

if %Data% EQU 6 (
    cls
    echo ����� GitHub Desktop
    echo �������� ��������...
    start "GitHubDesktop" "C:\Users\�����������\AppData\Local\GitHubDesktop\GitHubDesktop.exe"
    pause
)

if %Data% EQU 7 (
    cls
    echo ����� ������ ��㧥�
    echo �������� ��������...
    start browser
    pause
)

if %Data% EQU 8 (
    cls
    echo ����� Notepad++
    echo �������� ��������...
    start notepad++
    pause
)

if %Data% EQU 9 (
    cls
    echo ����� ��ᯥ��� �����
    echo �������� ��������...
    start taskmgr
    pause
)

if %Data% EQU 10 (
    cls
    echo ����� pgAdmin
    echo �������� ��������...
    start "pgAdmin4" "C:\Program Files\pgAdmin 4\v6\runtime\pgAdmin4.exe"
    pause
)

if not %Data% EQU 0 (
    goto loop
)

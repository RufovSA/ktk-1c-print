@echo off

:loop
cls
echo =================================
echo Команды сервера 1С: Колледж v 1.0
echo =================================
echo.
echo 1. Запустить 1С: Предприятие
echo 2. Sigur - Клиент
echo 3. Sigur - Управление сервером
echo 4. Диспетчер служб - IIS
echo 5. Администрирование серверов 1С Предприятия
echo 6. GitHub Desctop
echo 7. Яндекс Браузер
echo 8. Notepad++
echo 9. Диспетчер задач
echo 10. pgAdmin
echo 0. Выход
echo.

:: Ввод данных:
set /p Data="> "

if %Data% EQU 1 (
    cls
    echo Запуск 1С: Предприятие
    echo Пожалуйста подождите...
    start "1С: Предприятие" "C:\Program Files (x86)\1cv8\8.3.21.1302\bin\1cv8.exe"
    pause
)

if %Data% EQU 2 (
    cls
    echo Запуск Sigur - Клиент
    echo Пожалуйста подождите...
    start "Sigur - Клиент" "C:\Program Files (x86)\SIGUR access management\Клиент.exe"
    pause
)

if %Data% EQU 3 (
    cls
    echo Запуск Sigur - Управление сервером
    echo Пожалуйста подождите...
    start "Sigur - Управление сервером" "C:\Program Files (x86)\SIGUR access management\Управление сервером.exe"
    pause
)

if %Data% EQU 4 (
    cls
    echo Запуск Диспетчер служб - IIS
    echo Пожалуйста подождите...
    start "Диспетчер служб - IIS" "C:\Windows\system32\inetsrv\iis.msc"
    pause
)

if %Data% EQU 5 (
    cls
    echo Запуск Администрирование серверов 1С Предприятия
    echo Пожалуйста подождите...
    start "Администрирование серверов 1С Предприятия" "C:\Program Files (x86)\1cv8\common\1CV8 Servers.msc"
    pause
)

if %Data% EQU 6 (
    cls
    echo Запуск GitHub Desktop
    echo Пожалуйста подождите...
    start "GitHubDesktop" "C:\Users\Администратор\AppData\Local\GitHubDesktop\GitHubDesktop.exe"
    pause
)

if %Data% EQU 7 (
    cls
    echo Запуск Яндекс Браузер
    echo Пожалуйста подождите...
    start browser
    pause
)

if %Data% EQU 8 (
    cls
    echo Запуск Notepad++
    echo Пожалуйста подождите...
    start notepad++
    pause
)

if %Data% EQU 9 (
    cls
    echo Запуск Диспетчер задач
    echo Пожалуйста подождите...
    start taskmgr
    pause
)

if %Data% EQU 10 (
    cls
    echo Запуск pgAdmin
    echo Пожалуйста подождите...
    start "pgAdmin4" "C:\Program Files\pgAdmin 4\v6\runtime\pgAdmin4.exe"
    pause
)

if not %Data% EQU 0 (
    goto loop
)

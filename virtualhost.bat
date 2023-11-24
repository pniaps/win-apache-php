@echo off

:: BatchGotAdmin
:-------------------------------------
REM  --> Check for permissions
NET SESSION >nul 2>&1

REM --> If error flag set, we do not have admin.
if '%errorlevel%' NEQ '0' (
    echo Requesting administrative privileges...
    goto UACPrompt
) else ( goto gotAdmin )

:UACPrompt
    echo Set UAC = CreateObject^("Shell.Application"^) > "%temp%\getadmin.vbs"
    set params= %*
    echo UAC.ShellExecute "cmd.exe", "/c ""%~s0"" %params:"=""%", "", "runas", 1 >> "%temp%\getadmin.vbs"

    "%temp%\getadmin.vbs"
    del "%temp%\getadmin.vbs"
    exit /B

:gotAdmin
    pushd "%CD%"
    CD /D "%~dp0"
:--------------------------------------


NET SESSION >nul 2>&1
IF %ERRORLEVEL% EQU 0 (
	rem Administrator PRIVILEGES Detected! 
) ELSE (
   echo ######## ########  ########   #######  ########  
   echo ##       ##     ## ##     ## ##     ## ##     ## 
   echo ##       ##     ## ##     ## ##     ## ##     ## 
   echo ######   ########  ########  ##     ## ########  
   echo ##       ##   ##   ##   ##   ##     ## ##   ##   
   echo ##       ##    ##  ##    ##  ##     ## ##    ##  
   echo ######## ##     ## ##     ##  #######  ##     ## 
   echo.
   echo.
   echo ####### ERROR: ADMINISTRATOR PRIVILEGES REQUIRED #########
   echo This script must be run as administrator to work!  
   echo Right click on it and select "Run As Administrator".
   echo ##########################################################
   echo.
   PAUSE
   EXIT /B 1
)
setlocal


echo %PATH% | findstr /R /I /C:"php-.*-x64" > NUL

If %ERRORLEVEL% EQU 1 goto add

set PATH=%PATH:php-7.3-Win32-VC15-x64=php-8.2-Win32-vs16-x64%
set PATH=%PATH:php-7.4-Win32-VC15-x64=php-8.2-Win32-vs16-x64%
set PATH=%PATH:php-8.0-Win32-vs16-x64=php-8.2-Win32-vs16-x64%
set PATH=%PATH:php-8.1-Win32-vs16-x64=php-8.2-Win32-vs16-x64%
goto continue

:add
set PATH=%WAP_SERVER%\php-8.2-Win32-vs16-x64;%PATH%
goto continue


:continue


set PROMPT=$CVIRTUALHOST$F $P$G
cd /d "%WAP_SERVER%"
title win-apache-php virtualhost manager
cmd /K
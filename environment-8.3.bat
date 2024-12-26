@echo off
setlocal
echo %PATH% | findstr /R /I /C:"php-.*-x64" > NUL

If %ERRORLEVEL% EQU 1 goto add

set PATH=%PATH:php-7.0-Win32-VC14-x64=php-8.3-Win32-vs16-x64%
set PATH=%PATH:php-7.1-Win32-VC14-x64=php-8.3-Win32-vs16-x64%
set PATH=%PATH:php-7.2-Win32-VC15-x64=php-8.3-Win32-vs16-x64%
set PATH=%PATH:php-7.3-Win32-VC15-x64=php-8.3-Win32-vs16-x64%
set PATH=%PATH:php-7.4-Win32-VC15-x64=php-8.3-Win32-vs16-x64%
set PATH=%PATH:php-8.0-Win32-vs16-x64=php-8.3-Win32-vs16-x64%
set PATH=%PATH:php-8.1-Win32-vs16-x64=php-8.3-Win32-vs16-x64%
set PATH=%PATH:php-8.2-Win32-vs16-x64=php-8.3-Win32-vs16-x64%
set PATH=%PATH:php-8.4-Win32-vs16-x64=php-8.3-Win32-vs16-x64%
goto continue

:add
set PATH=%WAP_SERVER%\php-8.3-Win32-vs16-x64;%PATH%
goto continue


:continue
cd /d "%WAP_SERVER%"
title win-apache-php PHP 8.3
cmd /K
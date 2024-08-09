@echo off

NET SESSION >nul 2>&1
IF %ERRORLEVEL% EQU 0 (
    ECHO Administrator PRIVILEGES Detected! 
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

SET currentpath=%~dp0
SET currentpath=%currentpath:~0,-1%
cd /d %currentpath%

echo %PATH% | findstr /R /I /C:%currentpath:\=\\%\\php-.*-x64 > NUL

If %ERRORLEVEL% EQU 1 goto add

setx /m PATH "%PATH:php-7.3-Win32-VC15-x64=php-8.3-Win32-vs16-x64%" > NUL 2> NUL
set PATH=%PATH:php-7.3-Win32-VC15-x64=php-8.3-Win32-vs16-x64%

setx /m PATH "%PATH:php-7.4-Win32-VC15-x64=php-8.3-Win32-vs16-x64%" > NUL 2> NUL
set PATH=%PATH:php-7.4-Win32-VC15-x64=php-8.3-Win32-vs16-x64%

setx /m PATH "%PATH:php-8.0-Win32-vs16-x64=php-8.3-Win32-vs16-x64%" > NUL 2> NUL
set PATH=%PATH:php-8.0-Win32-vs16-x64=php-8.3-Win32-vs16-x64%

setx /m PATH "%PATH:php-8.1-Win32-vs16-x64=php-8.3-Win32-vs16-x64%" > NUL 2> NUL
set PATH=%PATH:php-8.1-Win32-vs16-x64=php-8.3-Win32-vs16-x64%

setx /m PATH "%PATH:php-8.2-Win32-vs16-x64=php-8.3-Win32-vs16-x64%" > NUL 2> NUL
set PATH=%PATH:php-8.2-Win32-vs16-x64=php-8.3-Win32-vs16-x64%
goto continue

:add
setx /m PATH "%PATH%;%currentpath%\php-8.3-Win32-vs16-x64" > NUL 2> NUL
set PATH "%PATH%;%currentpath%\php-8.3-Win32-vs16-x64"
goto continue

:continue
setx /m WAP_SERVER %cd:\=/% > NUL
set WAP_SERVER=%cd:\=/%

if not exist ..\web mkdir ..\web
cd ..\web

setx /m WAP_DOCUMENT_ROOT %cd:\=/% > NUL
set WAP_DOCUMENT_ROOT=%cd:\=/%

FOR %%G IN (php53 php54 php55 php56 php70 php71 php72 php73 php74 php80 php81 php82 php83) DO (
	findstr "%%G" C:\Windows\System32\drivers\etc\hosts > NUL || (
		(echo. & echo 127.0.0.1 %%G) >> C:\Windows\System32\drivers\etc\hosts
	)
)


set ERROLEVEL=0
sc query Apache2.4 > NUL
if %ERRORLEVEL% EQU 0 (
	sc stop Apache2.4
	%WAP_SERVER%\Apache-2.4-win64\bin\httpd.exe -k uninstall
	echo.
	echo Old service 'Apache2.4' removed
	echo.
)

set ERROLEVEL=0
sc query win-apache-php > NUL
if %ERRORLEVEL% EQU 1060 (
	%WAP_SERVER%\Apache-2.4-win64\bin\httpd.exe -k install -n win-apache-php
	sc start win-apache-php
	echo.
	echo Service 'win-apache-php' installed and started
	echo.
)

pause
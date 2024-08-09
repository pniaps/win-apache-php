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


FOR %%G IN (php53 php54 php55 php56 php70 php71 php72 php73 php74 php80 php81 php82 php83) DO (
	findstr "%%G" C:\Windows\System32\drivers\etc\hosts > NUL || (
		(echo. & echo 127.0.0.1 %%G) >> C:\Windows\System32\drivers\etc\hosts
	)
)

set ERROLEVEL=0
sc query Apache2.4 > NUL
if %ERRORLEVEL% EQU 0 (
	net stop Apache2.4 > NUL 2> NUL
	%WAP_SERVER%\Apache-2.4-win64\bin\httpd.exe -k uninstall
	%WAP_SERVER%\Apache-2.4-win64\bin\httpd.exe -k install -n win-apache-php
)


@net stop win-apache-php
@echo.
@echo.

git pull
@echo.
@echo.

@net start win-apache-php
@echo.
@echo.

@echo win-apache-php has been updated
@echo.
@pause

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

setx /m PATH "%PATH%;%currentpath%\php-8.1-Win32-vs16-x64"
set PATH "%PATH%;%currentpath%\php-8.1-Win32-vs16-x64"

setx /m WAP_SERVER %cd:\=/%
set WAP_SERVER=%cd:\=/%

cd ..
mkdir web
cd web

setx /m WAP_DOCUMENT_ROOT %cd:\=/%
set WAP_DOCUMENT_ROOT=%cd:\=/%

(echo. & echo 127.0.0.1 php53 php54 php55 php56) >> C:\Windows\System32\drivers\etc\hosts
(echo. & echo 127.0.0.1 php70 php71 php72 php73 php74) >> C:\Windows\System32\drivers\etc\hosts
(echo. & echo 127.0.0.1 php80 php81) >> C:\Windows\System32\drivers\etc\hosts

%WAP_SERVER%\Apache-2.4-win64\bin\httpd.exe -k install

sc start Apache2.4
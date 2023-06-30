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


@net stop Apache2.4
@echo.
@echo.

SET currentpath=%~dp0
SET currentpath=%currentpath:~0,-1%
cd /d %currentpath%
SET CAROOT=%currentpath%
@mkcert-v1.4.4-windows-amd64.exe -install
@mkcert-v1.4.4-windows-amd64.exe -cert-file win-apache-php.pem -key-file win-apache-php-key.pem default localhost php70 php71 php72 php73 php74 php80 php81 php82
@echo.
@echo.
@copy ..\Apache-2.4-win64\conf\extra\httpd-ssl.conf ..\Apache-2.4-win64\conf\configs\

@net start Apache2.4
@echo.
@echo.

@echo certificate has been created.
@echo.
@pause

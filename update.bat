@echo off

@if DEFINED SESSIONNAME (
    @echo.
    @echo You must right click to "Run as administrator"
    @echo Try again
    @echo.
    @pause
    @goto :EOF
)

setx /m PATH "%PATH:php-7.3-Win32-VC15-x64=php-7.4-Win32-VC15-x64%"
set PATH "%PATH:php-7.3-Win32-VC15-x64=php-7.4-Win32-VC15-x64%"


SET currentpath=%~dp0
SET currentpath=%currentpath:~0,-1%
cd /d %currentpath%

@net stop Apache2.4
@echo.
@echo.

git pull
@echo.
@echo.

@net start Apache2.4
@echo.
@echo.

@echo win-apache-php has been updated
@echo.
@pause
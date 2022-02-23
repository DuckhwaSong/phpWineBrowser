@echo off
FOR /F "delims=" %%I IN ('php_bin\php.exe portscan.php') DO set allowPort=%%I

start cmd /c php_bin\php.exe -S localhost:%allowPort% -t webDoc -c php.ini
start /max http://localhost:%allowPort%
::phpWineBrowser.exe
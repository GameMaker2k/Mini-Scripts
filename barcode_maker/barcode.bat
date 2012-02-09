@echo off
IF NOT EXIST "C:\PHP5\php.exe" (
SET /P phpexe=Enter location of php executable: 
)
IF ("%phpexe%")==("") set phpexe="C:\PHP5\php.exe"
%phpexe% -f "%~dp0\barcode.php" %*
set phpexe=
pause
exit
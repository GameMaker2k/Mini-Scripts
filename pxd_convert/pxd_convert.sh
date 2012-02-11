#!/bin/sh
export phpexe="`/usr/bin/which php`"
if  [ "$phpexe" == "" ]; then
	echo -n "Enter location of php executable: "
	read phpexe
fi
if  [ "$phpexe" == "" ]; then
	export phpexe="/usr/bin/php"
fi
$phpexe -f "`dirname $0`/pxd_convert.php" $*
export phpexe=""
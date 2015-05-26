#!/bin/sh
# location of the php binary
if [ ! "$1" = "" ] ; then
    CRONSCRIPT=$1
else
    CRONSCRIPT=cron.php
fi

MODE=""
if [ ! "$2" = "" ] ; then
	MODE=" $2"
fi

PHP_BIN=`which php`

# absolute path to magento installation
INSTALLDIR=`echo $0 | sed 's/cron\.sh//g'`

#	prepend the intallation path if not given an absolute path
if [ "$INSTALLDIR" != "" -a "`expr index $CRONSCRIPT /`" != "1" ];then
    if ! ps auxwww | grep "$INSTALLDIR$CRONSCRIPT$MODE" | grep -v grep 1>/dev/null 2>/dev/null ; then
    	$PHP_BIN $INSTALLDIR$CRONSCRIPT$MODE &
    fi
else
    if  ! ps auxwww | grep "$CRONSCRIPT$MODE" | grep -v grep | grep -v cron.sh 1>/dev/null 2>/dev/null ; then
        $PHP_BIN $CRONSCRIPT$MODE &
    fi
fi

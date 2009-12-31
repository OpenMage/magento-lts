#!/bin/sh
# location of the php binary
if [ ! "$1" = "" ] ; then
CRONSCRIPT=$1
else
CRONSCRIPT=cron.php
fi

PHP_BIN=`which php`

# absolute path to magento installation
if [ "$1" != "" ]; then
    INSTALLDIR=`ps axwww -o command= |grep -v grep| grep cron.sh \
	| awk '{ field = $(NF-1) }; END{ print field }' | sed 's/cron\.sh//g'`
else
    INSTALLDIR=`ps axwww -o command= |grep -v grep| grep cron.sh \
	| awk '{ field = $NF }; END{ print field }' | sed 's/cron\.sh//g'`
fi

#	prepend the intallation path if not given an absolute path
if [ "$INSTALLDIR" != "" -a "`expr index $CRONSCRIPT /`" != "1" ];then
    if ! ps auxwww | grep "$INSTALLDIR""$CRONSCRIPT" | grep -v grep 1>/dev/null 2>/dev/null ; then
	$PHP_BIN "$INSTALLDIR""$CRONSCRIPT" &
    fi
else
    if  ! ps auxwww | grep " $CRONSCRIPT" | grep -v grep | grep -v cron.sh 1>/dev/null 2>/dev/null ; then
        $PHP_BIN $CRONSCRIPT &
    fi
fi

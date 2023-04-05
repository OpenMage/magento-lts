#!/bin/sh
#
# OpenMage
#
# NOTICE OF LICENSE
#
# This source file is subject to the Open Software License (OSL 3.0)
# that is bundled with this package in the file LICENSE.txt.
# It is also available through the world-wide-web at this URL:
# https://opensource.org/licenses/osl-3.0.php
# If you did not receive a copy of the license and are unable to
# obtain it through the world-wide-web, please send an email
# to license@magento.com so we can send you a copy immediately.
#
# @category    Mage
# @package     Mage
# @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
# @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
# @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
#

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

# prepend the installation path if not given an absolute path
if [ "$INSTALLDIR" != "" -a "`expr index $CRONSCRIPT /`" != "1" ];then
    if ! ps auxwww | grep "$INSTALLDIR$CRONSCRIPT$MODE" | grep -v grep 1>/dev/null 2>/dev/null ; then
    	$PHP_BIN $INSTALLDIR$CRONSCRIPT$MODE &
    fi
else
    if  ! ps auxwww | grep "$CRONSCRIPT$MODE" | grep -v grep | grep -v cron.sh 1>/dev/null 2>/dev/null ; then
        $PHP_BIN $CRONSCRIPT$MODE &
    fi
fi

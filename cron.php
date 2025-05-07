<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage
 */

chdir(__DIR__);
require 'app/bootstrap.php';
require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo 'Application is not installed yet, please complete install wizard first.';
    exit;
}

// Only for urls, don't remove this
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);

try {
    Mage::app('admin')->setUseSessionInUrl(false);
} catch (Exception $e) {
    Mage::printException($e);
    exit;
}

umask(0);

$disabledFuncs = array_map('trim', preg_split("/,|\s+/", strtolower(ini_get('disable_functions'))));
$isWinOS = !str_contains(strtolower(PHP_OS), 'darwin') && str_contains(strtolower(PHP_OS), 'win');
$isShellDisabled = in_array('shell_exec', $disabledFuncs)
    || $isWinOS
    || !shell_exec('which expr 2>/dev/null')
    || !shell_exec('which ps 2>/dev/null')
    || !shell_exec('which sed 2>/dev/null');

try {
    if (!$isWinOS) {
        $options = getopt('m::');
        if (isset($options['m'])) {
            if ($options['m'] == 'always') {
                $cronMode = 'always';
            } elseif ($options['m'] == 'default') {
                $cronMode = 'default';
            } else {
                Mage::throwException('Unrecognized cron mode was defined');
            }
        } elseif (!$isShellDisabled) {
            $fileName = escapeshellarg(basename(__FILE__));
            $cronPath = escapeshellarg(__DIR__ . '/cron.sh');

            shell_exec(escapeshellcmd("/bin/sh $cronPath $fileName -mdefault 1") . ' &');
            shell_exec(escapeshellcmd("/bin/sh $cronPath $fileName -malways 1") . ' &');
            exit;
        }
    }

    Mage::getConfig()->init()->loadEventObservers('crontab');
    Mage::app()->addEventArea('crontab');
    if ($isShellDisabled) {
        Mage::dispatchEvent('always');
        Mage::dispatchEvent('default');
    } elseif (isset($cronMode)) {
        Mage::dispatchEvent($cronMode);
    }
} catch (Exception $e) {
    Mage::printException($e);
    exit(1);
}

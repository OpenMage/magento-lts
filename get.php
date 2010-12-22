<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
if (version_compare(phpversion(), '5.2.0', '<')===true) {
    echo  '<div style="font:12px/1.35em arial, helvetica, sans-serif;"><div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;"><h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">Whoops, it looks like you have an invalid PHP version.</h3></div><p>Magento supports PHP 5.2.0 or newer. <a href="http://www.magentocommerce.com/install" target="">Find out</a> how to install</a> Magento using PHP-CGI as a work-around.</p></div>';
    exit;
}

/**
 * Error reporting
 */
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

$ds = DIRECTORY_SEPARATOR;
$ps = PATH_SEPARATOR;
$bp = dirname(__FILE__);

/**
 * Set include path
 */

$paths[] = $bp . $ds . 'app' . $ds . 'code' . $ds . 'local';
$paths[] = $bp . $ds . 'app' . $ds . 'code' . $ds . 'community';
$paths[] = $bp . $ds . 'app' . $ds . 'code' . $ds . 'core';
$paths[] = $bp . $ds . 'lib';

$appPath = implode($ps, $paths);
set_include_path($appPath . $ps . get_include_path());

include_once "Mage/Core/functions.php";
include_once "Varien/Autoload.php";

Varien_Autoload::register();

$request = new Zend_Controller_Request_Http();

$filePath = rtrim($bp, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($request->getPathInfo(),
    DIRECTORY_SEPARATOR);

$scriptFilename = basename(__FILE__);

$filename = str_replace('/media/', '', $request->getPathInfo());

if (file_exists($filePath) && is_readable($filePath)) {
    $transfer = new Varien_File_Transfer_Adapter_Http();
    $transfer->send($filePath);
    exit;
}

$mageFilename = 'app/Mage.php';

if (!file_exists($mageFilename)) {
    echo $mageFilename." was not found";
}

require_once $mageFilename;

umask(0);

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

Mage::init($mageRunCode, $mageRunType, array(), array('Mage_Core'));

$databaseFileSotrage = Mage::getModel('core/file_storage_database');
$databaseFileSotrage->loadByFilename($filename);

if ($databaseFileSotrage->getId()) {
    $directory = dirname($filePath);
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $fp = fopen($filePath, "w");
    if (flock($fp, LOCK_EX | LOCK_NB)) {
        ftruncate($fp, 0);
        fwrite($fp, $databaseFileSotrage->getContent());
    }/* else {
        while (!flock($fp, LOCK_EX | LOCK_NB)) {
            usleep(1000);
        }
    }*/
    flock($fp, LOCK_UN);
    fclose($fp);
}

if (file_exists($filePath) || is_readable($filePath)) {
    $transfer = new Varien_File_Transfer_Adapter_Http();
    $transfer->send($filePath);
    exit;
}

header('HTTP/1.0 404 Not Found');

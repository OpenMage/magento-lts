<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2016-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$start = microtime(true);
/**
 * Error reporting
 */
ini_set('display_errors', '0');

$ds = DIRECTORY_SEPARATOR;
$ps = PATH_SEPARATOR;
$bp = __DIR__;

require $bp . '/app/bootstrap.php';

/**
 * Set include path
 */
$paths = [];
$paths[] = $bp . $ds . 'app' . $ds . 'code' . $ds . 'local';
$paths[] = $bp . $ds . 'app' . $ds . 'code' . $ds . 'community';
$paths[] = $bp . $ds . 'app' . $ds . 'code' . $ds . 'core';
$paths[] = $bp . $ds . 'lib';

$appPath = implode($ps, $paths);
set_include_path($appPath . $ps . get_include_path());
include_once 'Mage/Core/functions.php';
include_once 'Varien/Autoload.php';

Varien_Autoload::register();

/** AUTOLOADER PATCH **/
$autoloaderPath = getenv('COMPOSER_VENDOR_PATH');
if (!$autoloaderPath) {
    $autoloaderPath = dirname($bp) . $ds . 'vendor';
    if (!is_dir($autoloaderPath)) {
        $autoloaderPath = $bp . $ds . 'vendor';
    }
}
require_once $autoloaderPath . $ds . 'autoload.php';
/** AUTOLOADER PATCH **/

$varDirectory = $bp . $ds . Mage_Core_Model_Config_Options::VAR_DIRECTORY;
$configCacheFile = $varDirectory . $ds . 'resource_config.json';
$mediaDirectory = null;
$allowedResources = [];

if (file_exists($configCacheFile) && is_readable($configCacheFile)) {
    $config = json_decode(file_get_contents($configCacheFile), true);

    //checking update time
    if (filemtime($configCacheFile) + $config['update_time'] > time()) {
        $mediaDirectory = trim(str_replace($bp . $ds, '', $config['media_directory']), $ds);
        $allowedResources = array_merge($allowedResources, $config['allowed_resources']);
    }
}

$request = new Zend_Controller_Request_Http();
$pathInfo = str_replace('..', '', ltrim($request->getPathInfo(), '/'));
$filePath = str_replace('/', $ds, rtrim($bp, $ds) . $ds . $pathInfo);
$relativeFilename = '';

if ($mediaDirectory) {
    if (0 !== stripos($pathInfo, $mediaDirectory . '/') || is_dir($filePath)) {
        sendNotFoundPage();
    }

    $relativeFilename = str_replace($mediaDirectory . '/', '', $pathInfo);
    checkResource($relativeFilename, $allowedResources);
    sendFile($filePath);
}

$mageFilename = 'app/Mage.php';
if (!file_exists($mageFilename)) {
    echo $mageFilename . ' was not found';
}

require_once $mageFilename;

umask(0);

/* Store or website code */
$mageRunCode = $_SERVER['MAGE_RUN_CODE'] ?? '';

/* Run store or run website */
$mageRunType = $_SERVER['MAGE_RUN_TYPE'] ?? 'store';

if (empty($mediaDirectory)) {
    Mage::init($mageRunCode, $mageRunType);
} else {
    Mage::init(
        $mageRunCode,
        $mageRunType,
        ['cache' => ['disallow_save' => true]],
        $config['loaded_modules'] ?? ['Mage_Core'],
    );
}

if (!$mediaDirectory) {
    $config = Mage_Core_Model_File_Storage::getScriptConfig();
    $mediaDirectory = str_replace($bp . $ds, '', $config['media_directory']);
    $allowedResources = array_merge($allowedResources, $config['allowed_resources']);

    $relativeFilename = str_replace($mediaDirectory . '/', '', $pathInfo);

    $fp = fopen($configCacheFile, 'w');
    if (flock($fp, LOCK_EX | LOCK_NB)) {
        ftruncate($fp, 0);
        fwrite($fp, json_encode($config));
    }
    flock($fp, LOCK_UN);
    fclose($fp);

    checkResource($relativeFilename, $allowedResources);
}

if (0 !== stripos($pathInfo, $mediaDirectory . '/')) {
    sendNotFoundPage();
}
if (substr_count($relativeFilename, '/') > 10) {
    sendNotFoundPage();
}

// Nothing to do if DB storage is disabled
if (!Mage::helper('core/file_storage_database')->checkDbUsage()) {
    sendNotFoundPage();
}

$localStorage = Mage::getModel('core/file_storage_file');
$remoteStorage = Mage::getModel('core/file_storage_database');
try {
    if ($localStorage->lockCreateFile($relativeFilename)) {
        try {
            $remoteStorage->loadByFilename($relativeFilename);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        if ($remoteStorage->getId()) {
            $localStorage->saveFile($remoteStorage, false);
        } else {
            $localStorage->removeLockedFile($relativeFilename);
        }
    }
    sendFile($filePath);
} catch (Exception $e) {
    Mage::logException($e);
}

sendNotFoundPage();

/**
 * Send 404
 */
function sendNotFoundPage()
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

/**
 * Check resource by whitelist
 *
 * @param string $resource
 */
function checkResource($resource, array $allowedResources)
{
    $isResourceAllowed = false;
    foreach ($allowedResources as $allowedResource) {
        if (0 === stripos($resource, (string) $allowedResource)) {
            $isResourceAllowed = true;
        }
    }

    if (!$isResourceAllowed) {
        sendNotFoundPage();
    }
}

/**
 * Send file to browser
 *
 * @param string $file
 */
function sendFile($file)
{
    if (is_readable($file) && filesize($file) > 0) {
        $transfer = new Varien_File_Transfer_Adapter_Http();
        $transfer->send($file);
        exit;
    }
}

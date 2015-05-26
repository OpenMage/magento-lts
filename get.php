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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
if (version_compare(phpversion(), '5.2.0', '<')===true) {
    echo  '<div style="font:12px/1.35em arial, helvetica, sans-serif;"><div style="margin:0 0 25px 0; '
        . 'border-bottom:1px solid #ccc;"><h3 style="margin:0; font-size:1.7em; font-weight:normal; '
        . 'text-transform:none; text-align:left; color:#2f2f2f;">Whoops, it looks like you have an invalid PHP version.'
        . '</h3></div><p>Magento supports PHP 5.2.0 or newer. <a href="http://www.magentocommerce.com/install" '
        . 'target="">Find out</a> how to install</a> Magento using PHP-CGI as a work-around.</p></div>';
    exit;
}
$start = microtime(true);
/**
 * Error reporting
 */
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 0);

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

include_once 'Mage/Core/functions.php';
include_once 'Varien/Autoload.php';

Varien_Autoload::register();

$varDirectory = $bp . $ds . Mage_Core_Model_Config_Options::VAR_DIRECTORY;

$configCacheFile = $varDirectory . $ds . 'resource_config.json';

$mediaDirectory = null;
$allowedResources = array();

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
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

if (empty($mediaDirectory)) {
    Mage::init($mageRunCode, $mageRunType);
} else {
    Mage::init(
        $mageRunCode,
        $mageRunType,
        array('cache' => array('disallow_save' => true)),
        array('Mage_Core')
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

try {
    $databaseFileSotrage = Mage::getModel('core/file_storage_database');
    $databaseFileSotrage->loadByFilename($relativeFilename);
} catch (Exception $e) {
}
if ($databaseFileSotrage->getId()) {
    $directory = dirname($filePath);
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $fp = fopen($filePath, 'w');
    if (flock($fp, LOCK_EX | LOCK_NB)) {
        ftruncate($fp, 0);
        fwrite($fp, $databaseFileSotrage->getContent());
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}

sendFile($filePath);
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
 * @param array $allowedResources
 */
function checkResource($resource, array $allowedResources)
{
    $isResourceAllowed = false;
    foreach ($allowedResources as $allowedResource) {
        if (0 === stripos($resource, $allowedResource)) {
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
    if (file_exists($file) || is_readable($file)) {
        $transfer = new Varien_File_Transfer_Adapter_Http();
        $transfer->send($file);
        exit;
    }
}

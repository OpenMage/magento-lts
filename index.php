<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (version_compare(phpversion(), '7.0.0', '<')===true) {
    echo  '<div style="font:12px/1.35em arial, helvetica, sans-serif;">
<div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
<h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">
Whoops, it looks like you have an invalid PHP version.</h3></div><p>OpenMage supports PHP 7.0.0 or newer.
<a href="https://www.openmage.org/magento-lts/install.html" target="">Find out</a> how to install</a>
 OpenMage using PHP-CGI as a work-around.</p></div>';
    exit;
}

define('MAGENTO_ROOT', getcwd());

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile = 'maintenance.flag';
$maintenanceIpFile = 'maintenance.ip';


require MAGENTO_ROOT . '/app/bootstrap.php';
require_once $mageFilename;

#Varien_Profiler::enable();

umask(0);

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

if (file_exists($maintenanceFile)) {
    $maintenanceBypass = false;

    if (file_exists($maintenanceIpFile)) {
        /* if maintenanceFile and maintenanceIpFile are set use Mage to get remote IP (in order to respect remote_addr_headers xml config) */
        Mage::init($mageRunCode, $mageRunType);
        $currentIp = Mage::helper('core/http')->getRemoteAddr();
        $allowedIps = explode(',', trim(file_get_contents($maintenanceIpFile)));

        if (in_array($currentIp, $allowedIps)) {
            /* IP address matches, bypass maintenanceMode */
            $maintenanceBypass = true;
        }
    }
    if (!$maintenanceBypass) {
        include_once dirname(__FILE__) . '/errors/503.php';
        exit;
    }
}

Mage::run($mageRunCode, $mageRunType);

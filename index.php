<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage
 */

define('MAGENTO_ROOT', getcwd());

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile = 'maintenance.flag';
$maintenanceIpFile = 'maintenance.ip';


require MAGENTO_ROOT . '/app/bootstrap.php';
require_once $mageFilename;

#Varien_Profiler::enable();

umask(0);

/* Store or website code */
$mageRunCode = $_SERVER['MAGE_RUN_CODE'] ?? '';

/* Run store or run website */
$mageRunType = $_SERVER['MAGE_RUN_TYPE'] ?? 'store';

if (file_exists($maintenanceFile)) {
    $maintenanceBypass = false;

    if (is_readable($maintenanceIpFile)) {
        /* Use Mage to get remote IP (in order to respect remote_addr_headers xml config) */
        Mage::init($mageRunCode, $mageRunType);
        $currentIp = Mage::helper('core/http')->getRemoteAddr();
        $allowedIps = preg_split('/[\ \n\,]+/', file_get_contents($maintenanceIpFile), 0, PREG_SPLIT_NO_EMPTY);
        $maintenanceBypass = in_array($currentIp, $allowedIps, true);
    }
    if (!$maintenanceBypass) {
        include_once __DIR__ . '/errors/503.php';
        exit;
    }

    // remove config cache to make the system check for DB updates
    $config = Mage::app()->getConfig();
    $config->getCache()->remove($config->getCacheId());
}

Mage::run($mageRunCode, $mageRunType);

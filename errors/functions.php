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
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define('MAGE_ERRORS_STORE_REQUEST_KEY', 's');
define('MAGE_ERRORS_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('MAGE_ERRORS_MAGE_PATH', dirname(MAGE_ERRORS_PATH) . DIRECTORY_SEPARATOR);
define('MAGE_ERRORS_CONFIG_FILE', MAGE_ERRORS_PATH . 'config.xml');
define('MAGE_ERRORS_DESIGN_FILE', MAGE_ERRORS_PATH . 'design.xml');

// load configuration and design
$eConfig = simplexml_load_file(MAGE_ERRORS_CONFIG_FILE);
$eDesign = simplexml_load_file(MAGE_ERRORS_DESIGN_FILE);
$store   = 'default';
$skinUrl = mageErrorsGetSkinUrl();
$hostUrl = mageErrorsGetHostUrl();
if (empty($basePath)) {
    $basePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
}
$baseUrl = $hostUrl . $basePath . '/';

// detect store package
if (!empty($_REQUEST[MAGE_ERRORS_STORE_REQUEST_KEY])) {
    if (is_dir(MAGE_ERRORS_PATH . $_REQUEST['s'])) {
        $store = $_REQUEST[MAGE_ERRORS_STORE_REQUEST_KEY];
    }
}

define('MAGE_ERRORS_TEMPLATE_PATH', MAGE_ERRORS_PATH . $store . DIRECTORY_SEPARATOR);

/**
 * Check is valid email format
 *
 * @param string $email
 * @return bool
 */
function checkEmail($email)
{
    return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}

/**
 * Retrieve base host URL without path
 *
 * @return string
 */
function mageErrorsGetHostUrl()
{
    $isSecure = isset($_SERVER['SERVER_PORT']) && (443 == $_SERVER['SERVER_PORT']);
    $url = ($isSecure ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    if (!empty($_SERVER['SERVER_PORT']) && !in_array($_SERVER['SERVER_PORT'], array(80, 433))) {
        $url .= ':' . $_SERVER['SERVER_PORT'];
    }
    return  $url;
}

/**
 * Retrieve skin absolute URL
 *
 * @return string
 */
function mageErrorsGetSkinUrl()
{
    global $eDesign;
    return 'skin/' . (string)$eDesign->skin;
}

/**
 * Send error headers
 *
 * @param int $statusCode
 */
function mageErrorsSendErrorHeaders($statusCode)
{
    $serverProtocol = !empty($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
    switch ($statusCode) {
        case 404:
            $description = 'Not Found';
            break;
        case 503:
            $description = 'Service Unavailable';
            break;
        default:
            $description = '';
            break;
    }

    header(sprintf('%s %s %s', $serverProtocol, $statusCode, $description), true, $statusCode);
    header(sprintf('Status: %s %s', $statusCode, $description), true, $statusCode);
}

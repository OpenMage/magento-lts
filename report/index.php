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

define('CONFIG_FILE', 'config.xml');

$baseUrl    = dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/';
$reportId   = 0;

/**
 * Check defined report id
 */
if (isset($_REQUEST['id'])) {
    $reportId   = $_REQUEST['id'];
    $reportPath = '../var/report/';
    $reportFile = $reportPath . $reportId;

    if (strpos(realpath($reportFile), realpath($reportPath)) !== 0) {
        $reportFile = '';
    }

    if (!file_exists($reportFile) || !is_readable($reportFile)) {
        $reportFile = '';
    }
}
else {
    $reportFile = '';
}

if (isset($_POST['submit']) && $reportId) {
    // empty if for trash action
}
elseif (!$reportFile || !is_file(CONFIG_FILE)) {
    header("Location: " . $baseUrl);
    die();
}

/**
 * Load config
 */
$config = new SimpleXMLElement(implode('', file(CONFIG_FILE)));

if ((string)$config->report->email_address == '' && (string)$config->report->action == 'email') {
    header("Location: " . $baseUrl);
    die();
}

$action = ((string)$config->report->action == '') ? 'print' : (string)$config->report->action;
$trash  = ((string)$config->report->trash == '') ? 'leave' : (string)$config->report->trash;

$showErrorMsg   = false;
$showSendForm   = ($action == 'email') ? true : false;
$showSentMsg    = false;

if ($showSendForm) {
    $firstName  = (isset($_POST['firstname'])) ? trim($_POST['firstname']) : '';
    $lastName   = (isset($_POST['lastname'])) ? trim($_POST['lastname']) : '';
    $email      = (isset($_POST['email'])) ? trim($_POST['email']) : '';
    $telephone  = (isset($_POST['telephone'])) ? trim($_POST['telephone']) : '';
    $comment    = (isset($_POST['comment'])) ? trim(strip_tags($_POST['comment'])) : '';
    $errorHash  = (isset($_POST['error_hash']))? $_POST['error_hash'] : '';
}

if ($action == 'email') {
    if (isset($_POST['submit'])) {
        if (!empty($firstName) && !empty($lastName) && checkEmail($email)) {
            $msg  = "First Name: {$firstName}\n"
                . "Last Name: {$lastName}\n"
                . "E-mail Address: {$email}\n";

            if ($telephone) {
                $msg .= "Telephone: {$telephone}\n";
            }

            if ($comment) {
                $msg .= "Comment: {$comment}\n";
            }

            mail($config->report->email_address,
                $config->report->subject . " [{$reportId}]",
                $msg);

            $showSendForm   = false;
            $showSentMsg    = true;
        }
        else {
            $showErrorMsg = true;
        }
    }
    else {
        $url    = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'not available';
        $time   = gmdate('Y-m-d H:i:s \G\M\T');

        $reportData = unserialize(file_get_contents($reportFile));

        $msg = "URL: {$url}\n"
            . "Time: {$time}\n"
            . "Error:\n{$reportData[0]}\n\n"
            . "Trace:\n{$reportData[1]}";

        mail($config->report->email_address,
            $config->report->subject . " [{$reportId}]",
            $msg);

        if ($trash == 'delete') {
            unlink($reportFile);
        }
    }
}

if ($action == 'print') {
    header("HTTP/1.0 503 Service Unavailable");
    $reportData = unserialize(file_get_contents($reportFile));
}

$design = false;
if (file_exists('design.xml')) {
    $design = simplexml_load_file('design.xml');
}

$store = 'default';
if (isset($_GET['s'])) {
    $skinPath = realpath('skin/');
    $skinFile = realpath($skinPath . DIRECTORY_SEPARATOR . $_GET['s']);

    if ($skinFile && strpos($skinFile, $skinPath) === 0 && is_dir($skinFile)) {
        $store = $_GET['s'];
    }
}

include_once ('skin/' . $store . '/' . ($design ? $design->template : 'index.phtml'));

function checkEmail($email) {
    return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}

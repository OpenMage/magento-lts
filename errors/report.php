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

require_once 'functions.php';

$reportId   = 0;
$reportFile = '';

if (isset($_REQUEST['id'])) {
    $reportId   = $_REQUEST['id'];
    $reportPath = MAGE_ERRORS_MAGE_PATH . 'var/report/';
    $reportFile = $reportPath . $reportId;

    if (strpos(realpath($reportFile), realpath($reportPath)) !== 0) {
        $reportFile = '';
    }

    if (!file_exists($reportFile) || !is_readable($reportFile)) {
        $reportFile = '';
    }
}

if (isset($_POST['submit']) && $reportId) {
    // empty if for trash action
} else if (!$reportFile) {
    header("Location: " . $baseUrl);
    die();
}

if ((string)$eConfig->report->email_address == '' && (string)$eConfig->report->action == 'email') {
    header("Location: " . $baseUrl);
    die();
}

$action = ((string)$eConfig->report->action == '') ? 'print' : (string)$eConfig->report->action;
$trash  = ((string)$eConfig->report->trash == '') ? 'leave' : (string)$eConfig->report->trash;

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
    $pageTitle = 'Error Submission Form';
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

            $subject = sprintf('%s [%s]', (string)$eConfig->report->subject, $reportId);
            @mail((string)$eConfig->report->email_address, $subject, $msg);

            $showSendForm   = false;
            $showSentMsg    = true;
        } else {
            $showErrorMsg = true;
        }
    } else {
        $url    = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'not available';
        $time   = gmdate('Y-m-d H:i:s \G\M\T');

        $reportData = unserialize(file_get_contents($reportFile));

        $msg = "URL: {$url}\n"
            . "Time: {$time}\n"
            . "Error:\n{$reportData[0]}\n\n"
            . "Trace:\n{$reportData[1]}";

        $subject = sprintf('%s [%s]', (string)$eConfig->report->subject, $reportId);
        @mail((string)$eConfig->report->email_address, $subject, $msg);

        if ($trash == 'delete') {
            unlink($reportFile);
        }
    }
} else if ($action == 'print') {
    mageErrorsSendErrorHeaders(503);
    $reportData = unserialize(file_get_contents($reportFile));
    $pageTitle = 'There has been an error processing your request';
}

// load template file
define('MAGE_ERRORS_TEMPLATE_FILE', MAGE_ERRORS_TEMPLATE_PATH . 'report.phtml');
include_once MAGE_ERRORS_TEMPLATE_PATH . 'page.phtml';

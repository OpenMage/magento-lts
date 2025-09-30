<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Errors
 */

if ($_SERVER['SCRIPT_FILENAME'] == __FILE__ && (!isset($_GET['id']) || strlen($_GET['id']) == 0)) {
    die('Missing parameter: id');
}

require_once 'processor.php';

$processor = new Error_Processor();

if (isset($reportData) && is_array($reportData)) {
    $processor->saveReport($reportData);
}

$processor->processReport();

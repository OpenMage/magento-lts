<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
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

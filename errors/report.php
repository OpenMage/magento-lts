<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Errors
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Errors
 */

require_once 'processor.php';

$processor = new Error_Processor();
$processor->process404();

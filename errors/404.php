<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Errors
 */

require_once 'processor.php';

$processor = new Error_Processor();
$processor->process404();

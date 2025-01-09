<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2016-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Carbon\Carbon;

/**
 * Core data helper
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Helper_Date extends Mage_Core_Helper_Abstract
{
    public const DATETIME_INTERNAL_FORMAT   = 'yyyy-MM-dd HH:mm:ss';
    public const DATE_INTERNAL_FORMAT       = 'yyyy-MM-dd';

    public const DATETIME_PHP_FORMAT        = 'Y-m-d H:i:s';
    public const DATE_PHP_FORMAT            = 'Y-m-d';
}

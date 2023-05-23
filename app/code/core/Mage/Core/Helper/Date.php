<?php

declare(strict_types=1);

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
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core Cookie helper
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Helper_Date extends Mage_Core_Helper_Abstract
{
    /**
     * @param string $format
     * @return string
     */
    public static function getDateFormatFromString(string $format): string
    {
        if ($format && defined('Mage_Core_Model_Locale::' . $format)) {
            return constant('Mage_Core_Model_Locale::' . $format);
        }
        return Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM;
    }
}

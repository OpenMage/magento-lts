<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml addresses helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Helper_Addresses extends Mage_Core_Helper_Abstract
{
    public const DEFAULT_STREET_LINES_COUNT = 2;

    protected $_moduleName = 'Mage_Adminhtml';

    /**
     * Check if number of street lines is non-zero
     *
     * @return Mage_Customer_Model_Attribute
     */
    public function processStreetAttribute(Mage_Customer_Model_Attribute $attribute)
    {
        if ($attribute->getScopeMultilineCount() <= 0) {
            $attribute->setScopeMultilineCount(self::DEFAULT_STREET_LINES_COUNT);
        }
        return $attribute;
    }
}

<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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

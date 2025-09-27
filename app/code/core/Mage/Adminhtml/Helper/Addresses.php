<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml addresses helper
 *
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

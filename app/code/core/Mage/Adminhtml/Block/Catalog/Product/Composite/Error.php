<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml block for showing product options fieldsets
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Error extends Mage_Core_Block_Template
{
    /**
     * Returns error message to show what kind of error happened during retrieving of product
     * configuration controls
     *
     * @return string
     */
    public function _toHtml()
    {
        $message = Mage::registry('composite_configure_result_error_message');
        return Mage::helper('core')->jsonEncode(['error' => true, 'message' => $message]);
    }
}

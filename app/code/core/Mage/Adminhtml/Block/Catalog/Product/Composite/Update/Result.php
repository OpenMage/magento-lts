<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml block for result of catalog product composite update
 * Forms response for a popup window for a case when form is directly submitted
 * for single item
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Update_Result extends Mage_Core_Block_Template
{
    /**
     * Forms script response
     *
     * @return string
     */
    public function _toHtml()
    {
        $updateResult = Mage::registry('composite_update_result');
        $resultJson = Mage::helper('core')->jsonEncode($updateResult);
        $jsVarname = $updateResult->getJsVarName();
        return Mage::helper('adminhtml/js')->getScript(sprintf('var %s = %s', $jsVarname, $resultJson));
    }
}

<?php
/**
 * Adminhtml review grid item renderer for item type
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Review_Grid_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Mage_Catalog_Model_Product $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if (is_null($row->getCustomerId())) {
            if ($row->getStoreId() == Mage_Core_Model_App::ADMIN_STORE_ID) {
                return Mage::helper('review')->__('Administrator');
            }
            return Mage::helper('review')->__('Guest');
        }

        if ($row->getCustomerId() > 0) {
            return Mage::helper('review')->__('Customer');
        }

        return '';
    }
}

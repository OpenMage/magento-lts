<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Cms_Page
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $storeId = Mage::app()->getRequest()->getParam('store', 0);
            $collection = Mage::getResourceModel('cms/page_collection')
                ->addFieldToFilter('is_active', 1)
                ->addStoreFilter($storeId)
                ->load();
            $this->_options = $collection->toOptionIdArray();
        }
        return $this->_options;
    }
}

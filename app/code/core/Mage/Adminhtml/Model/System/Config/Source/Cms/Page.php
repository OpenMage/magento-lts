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
    public function toOptionArray()
    {
        $storeCode = Mage::app()->getRequest()->getParam('store');
        $store = Mage::app()->getStore($storeCode);
        $storeId = $store->getId();

        $collection = Mage::getModel('cms/page')->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->addStoreFilter($storeId)
            ->setOrder('title', 'ASC');

        $options = [];

        foreach ($collection as $page) {
            $options[] = [
                'value' => $page->getIdentifier(),
                'label' => $page->getTitle(),
            ];
        }

        return $options;
    }
}

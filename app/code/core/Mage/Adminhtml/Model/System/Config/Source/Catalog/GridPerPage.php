<?php

/**
 * Catalog products per page on Grid mode source
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Catalog_GridPerPage
{
    public function toOptionArray()
    {
        $result = [];
        $perPageValues = Mage::getConfig()->getNode('frontend/catalog/per_page_values/grid');
        $perPageValues = explode(',', $perPageValues);
        foreach ($perPageValues as $option) {
            $result[] = ['value' => $option, 'label' => $option];
        }
        //$result[] = array('value' => 'all', 'label' => Mage::helper('catalog')->__('All'));
        return $result;
    }
}

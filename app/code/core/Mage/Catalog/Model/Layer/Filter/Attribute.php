<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Layer attribute filter
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Abstract
{
    const OPTIONS_ONLY_WITH_RESULTS = 1;

    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'attribute';
    }

    protected  function _getOptionText($optionId)
    {
        return $this->getAttributeModel()->getFrontend()->getOption($optionId);
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->_requestVar);
        $text = $this->_getOptionText($filter);
        if ($filter && $text) {
            /*$entityIds = Mage::getSingleton('catalogindex/attribute')->getFilteredEntities($this->getAttributeModel(), $filter, $this->_getFilterEntityIds());
            if ($entityIds) {
                $this->getLayer()->getProductCollection()
                    ->addFieldToFilter('entity_id', array('in' => $entityIds));

                $this->getLayer()->getState()->addFilter(
                    $this->_createItem($text, $filter)
                );
                $this->_items = array();
            }*/
            Mage::getSingleton('catalogindex/attribute')->applyFilterToCollection(
                $this->getLayer()->getProductCollection(),
                $this->getAttributeModel(),
                $filter
            );
            $this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
            $this->_items = array();
        }
        return $this;
    }

    protected function _initItems()
    {
        $attribute = $this->getAttributeModel();
        $options = $attribute->getFrontend()->getSelectOptions();

        //$optionsCount = Mage::getSingleton('catalogindex/attribute')->getCount($attribute, $this->_getFilterEntityIds());
        $optionsCount = Mage::getSingleton('catalogindex/attribute')->getCount($attribute, $this->_getBaseCollectionSql());
        $this->_requestVar = $attribute->getAttributeCode();

        $items=array();

        foreach ($options as $option) {
            if (strlen($option['value'])) {
                // Check filter type
                if ($attribute->getIsFilterable() == self::OPTIONS_ONLY_WITH_RESULTS) {
                    if (!empty($optionsCount[$option['value']])) {
                        $items[] = Mage::getModel('catalog/layer_filter_item')
                            ->setFilter($this)
                            ->setLabel($option['label'])
                            ->setValue($option['value'])
                            ->setCount($optionsCount[$option['value']]);
                    }
                }
                else {
                    $items[] = Mage::getModel('catalog/layer_filter_item')
                        ->setFilter($this)
                        ->setLabel($option['label'])
                        ->setValue($option['value'])
                        ->setCount(isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0);
                }
            }
        }


        $this->_items = $items;
        return $this;
    }
}

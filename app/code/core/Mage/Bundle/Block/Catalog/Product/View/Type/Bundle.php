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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog bundle product info block
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Block_Catalog_Product_View_Type_Bundle extends Mage_Catalog_Block_Product_View_Abstract
{
    protected $_optionRenderers = array();
    protected $_options = null;

    public function getOptions()
    {
        if (!$this->_options) {
            $this->getProduct()->getTypeInstance()->setStoreFilter($this->getProduct()->getStoreId());

            $optionCollection = $this->getProduct()->getTypeInstance()->getOptionsCollection();

            $selectionCollection = $this->getProduct()->getTypeInstance()->getSelectionsCollection(
                    $this->getProduct()->getTypeInstance()->getOptionsIds()
                );

            $this->_options = $optionCollection->appendSelections($selectionCollection, false, false);
        }
        return $this->_options;
    }

    public function hasOptions()
    {
        $this->getOptions();
        if (empty($this->_options) || !$this->getProduct()->isSalable()) {
            return false;
        }
        return true;
    }

    public function getJsonConfig()
    {
        Mage::app()->getLocale()->getJsPriceFormat();
        $store = Mage::app()->getStore();
        $optionsArray = $this->getOptions();
        $options = array();
        $selected = array();

        foreach ($optionsArray as $_option) {
            if (!$_option->getSelections()) {
                continue;
            }
            $option = array (
                'selections' => array(),
                'isMulti' => ($_option->getType() == 'multi' || $_option->getType() == 'checkbox')
            );

            $selectionCount = count($_option->getSelections());

            foreach ($_option->getSelections() as $_selection) {
                $_qty = !($_selection->getSelectionQty()*1)?'1':$_selection->getSelectionQty()*1;
                $selection = array (
                    'qty' => $_qty,
                    'customQty' => $_selection->getSelectionCanChangeQty(),
                    'price' => Mage::helper('core')->currency($_selection->getFinalPrice(), false, false),
                    'priceValue' => Mage::helper('core')->currency($_selection->getSelectionPriceValue(), false, false),
                    'priceType' => $_selection->getSelectionPriceType(),
                    'tierPrice' => $_selection->getTierPrice()
                );
                $option['selections'][$_selection->getSelectionId()] = $selection;

                if (($_selection->getIsDefault() || ($selectionCount == 1 && $_option->getRequired())) && $_selection->isSalable()) {
                    $selected[$_option->getId()][] = $_selection->getSelectionId();
                }
            }
            $options[$_option->getId()] = $option;
        }

        $config = array(
            'options' => $options,
            'selected' => $selected,
            'bundleId' => $this->getProduct()->getId(),
            'priceFormat' => Mage::app()->getLocale()->getJsPriceFormat(),
            'basePrice' => Mage::helper('core')->currency($this->getProduct()->getPrice(), false, false),
            'priceType' => $this->getProduct()->getPriceType(),
            'specialPrice' => $this->getProduct()->getSpecialPrice()
        );

        return Zend_Json::encode($config);
    }

    public function addRenderer($type, $block)
    {
        $this->_optionRenderers[$type] = $block;
    }

    public function getOptionHtml($option)
    {
        if (!isset($this->_optionRenderers[$option->getType()])) {
            return $this->__('There is no defined renderer for "%s" option type', $option->getType());
        }
        return $this->getLayout()->createBlock($this->_optionRenderers[$option->getType()])
            ->setOption($option)->toHtml();
    }

}
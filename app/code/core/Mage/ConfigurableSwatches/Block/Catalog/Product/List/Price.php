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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_ConfigurableSwatches_Block_Catalog_Product_List_Price extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'configurableswatches/catalog/product/list/price/js.phtml';

    /**
     * Get target product IDs from product collection
     * which was set on block
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function getProducts()
    {
        return $this->getProductCollection();
    }

    /**
     * Get configuration for configurable swatches price change
     *
     * @return string
     */
    public function getJsonConfig()
    {
        /** @var Mage_Catalog_Helper_Product_Type_Composite $compositeProductHelper */
        $compositeProductHelper = $this->helper('catalog/product_type_composite');

        $config = array(
            'generalConfig' => $compositeProductHelper->prepareJsonGeneralConfig()
        );
        foreach ($this->getProducts() as $product) {
            /** @var $product Mage_Catalog_Model_Product */
            if (!$product->getSwatchPrices()) {
                continue;
            }

            $config['products'][$product->getId()] = $compositeProductHelper->prepareJsonProductConfig($product);
            $config['products'][$product->getId()]['swatchPrices'] = $product->getSwatchPrices();

            $responseObject = new Varien_Object();
            Mage::dispatchEvent('catalog_product_view_config', array(
                'response_object' => $responseObject,
                'product' => $product,
            ));
            if (is_array($responseObject->getAdditionalOptions())) {
                foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                    $config['products'][$product->getId()][$option] = $value;
                }
            }
        }
        return $this->helper('core')->jsonEncode($config);
    }

    /**
     * Disable output if all preconditions doesn't meet
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->helper('configurableswatches/list_price')->isEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }

}

<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product options block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_View_Options extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    protected $_optionRenders = [];

    public function __construct()
    {
        parent::__construct();
        $this->addOptionRenderer(
            'default',
            'catalog/product_view_options_type_default',
            'catalog/product/view/options/type/default.phtml'
        );
    }

    /**
     * Retrieve product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if (Mage::registry('current_product')) {
                $this->_product = Mage::registry('current_product');
            } else {
                $this->_product = Mage::getSingleton('catalog/product');
            }
        }
        return $this->_product;
    }

    /**
     * Set product object
     *
     * @param Mage_Catalog_Model_Product|null $product
     * @return $this
     */
    public function setProduct(Mage_Catalog_Model_Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Add option renderer to renderers array
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return $this
     */
    public function addOptionRenderer($type, $block, $template)
    {
        $this->_optionRenders[$type] = [
            'block' => $block,
            'template' => $template,
            'renderer' => null
        ];
        return $this;
    }

    /**
     * Get option render by given type
     *
     * @param string $type
     * @return array
     */
    public function getOptionRender($type)
    {
        return $this->_optionRenders[$type] ?? $this->_optionRenders['default'];
    }

    /**
     * @param string $type
     * @return string
     */
    public function getGroupOfOption($type)
    {
        $group = Mage::getSingleton('catalog/product_option')->getGroupByType($type);

        return $group == '' ? 'default' : $group;
    }

    /**
     * Get product options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->getProduct()->getOptions();
    }

    /**
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getOptions()) {
            return true;
        }
        return false;
    }

    /**
     * Get price configuration
     *
     * @param Mage_Catalog_Model_Product_Option_Value|Mage_Catalog_Model_Product_Option $option
     * @return array
     */
    protected function _getPriceConfiguration($option)
    {
        $data = [];
        $data['price']      = Mage::helper('core')->currency($option->getPrice(true), false, false);
        $data['oldPrice']   = Mage::helper('core')->currency($option->getPrice(false), false, false);
        $data['priceValue'] = $option->getPrice(false);
        $data['type']       = $option->getPriceType();
        $data['excludeTax'] = $price = Mage::helper('tax')->getPrice($option->getProduct(), $data['price'], false);
        $data['includeTax'] = $price = Mage::helper('tax')->getPrice($option->getProduct(), $data['price'], true);
        return $data;
    }

    /**
     * Get json representation of
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = [];

        foreach ($this->getOptions() as $option) {
            /** @var Mage_Catalog_Model_Product_Option $option */
            $priceValue = 0;
            if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                $_tmpPriceValues = [];
                foreach ($option->getValues() as $value) {
                    $id = $value->getId();
                    $_tmpPriceValues[$id] = $this->_getPriceConfiguration($value);
                }
                $priceValue = $_tmpPriceValues;
            } else {
                $priceValue = $this->_getPriceConfiguration($option);
            }
            $config[$option->getId()] = $priceValue;
        }

        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * Get option html block
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @return string
     */
    public function getOptionHtml(Mage_Catalog_Model_Product_Option $option)
    {
        $renderer = $this->getOptionRender(
            $this->getGroupOfOption($option->getType())
        );
        if (is_null($renderer['renderer'])) {
            $renderer['renderer'] = $this->getLayout()->createBlock($renderer['block'])
                ->setTemplate($renderer['template']);
        }
        return $renderer['renderer']
            ->setProduct($this->getProduct())
            ->setOption($option)
            ->toHtml();
    }
}

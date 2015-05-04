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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_ConfigurableSwatches_Block_Catalog_Product_View_Type_Configurable_Swatches extends Mage_Core_Block_Template
{
    protected $_initDone = false;

    /**
     * Determine if the renderer should be used
     *
     * @param Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute
     * @param string $jsonConfig
     * @return bool
     */
    public function shouldRender($attribute, $jsonConfig)
    {
        if (Mage::helper('configurableswatches')->isEnabled()) {
            if (Mage::helper('configurableswatches')->attrIsSwatchType($attribute->getProductAttribute())) {
                $this->_init($jsonConfig);
                return true;
            }
        }
        return false;
    }

    /**
     * Set one-time data on the renderer
     *
     * @param string $jsonConfig
     */
    protected function _init($jsonConfig)
    {
        if (!$this->_initDone) {
            $this->setJsonConfig($jsonConfig);

            $dimHelper = Mage::helper('configurableswatches/swatchdimensions');
            $this->setSwatchInnerWidth(
                $dimHelper->getInnerWidth(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_DETAIL));
            $this->setSwatchInnerHeight(
                $dimHelper->getInnerHeight(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_DETAIL));
            $this->setSwatchOuterWidth(
                $dimHelper->getOuterWidth(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_DETAIL));
            $this->setSwatchOuterHeight(
                $dimHelper->getOuterHeight(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_DETAIL));

            $this->_initDone = true;
        }
    }
}

<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_ConfigurableSwatches_Block_Catalog_Product_View_Type_Configurable_Swatches
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 *
 * @method $this setJsonConfig(string $value)
 * @method $this setSwatchInnerHeight(int $value)
 * @method $this setSwatchInnerWidth(int $value)
 * @method $this setSwatchOuterHeight(int $value)
 * @method $this setSwatchOuterWidth(int $value)
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
                $dimHelper->getInnerWidth(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_DETAIL),
            );
            $this->setSwatchInnerHeight(
                $dimHelper->getInnerHeight(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_DETAIL),
            );
            $this->setSwatchOuterWidth(
                $dimHelper->getOuterWidth(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_DETAIL),
            );
            $this->setSwatchOuterHeight(
                $dimHelper->getOuterHeight(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_DETAIL),
            );

            $this->_initDone = true;
        }
    }
}

<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ConfigurableSwatches
 */

/**
 * Class Mage_ConfigurableSwatches_Block_Catalog_Layer_State_Swatch
 *
 * @package    Mage_ConfigurableSwatches
 *
 * @method int getSwatchInnerHeight()
 * @method int getSwatchInnerWidth()
 * @method string getSwatchUrl()
 * @method $this setJsonConfig(string $value)
 * @method $this setSwatchInnerHeight(int $value)
 * @method $this setSwatchInnerWidth(int $value)
 * @method $this setSwatchOuterHeight(int $value)
 * @method $this setSwatchOuterWidth(int $value)
 * @method $this setSwatchUrl(string $value)
 */
class Mage_ConfigurableSwatches_Block_Catalog_Layer_State_Swatch extends Mage_Core_Block_Template
{
    protected $_initDone = false;

    /**
     * Determine if we should use this block to render a state filter
     *
     * @param Mage_Catalog_Model_Layer_Filter_Item $filter
     * @return bool
     */
    public function shouldRender($filter)
    {
        $helper = Mage::helper('configurableswatches');
        if ($helper->isEnabled() && $filter->getFilter()->hasAttributeModel()) {
            if ($helper->attrIsSwatchType($filter->getFilter()->getAttributeModel())) {
                $this->_init($filter);
                if ($this->getSwatchUrl()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Set one-time data on the renderer
     *
     * @param Mage_Catalog_Model_Layer_Filter_Item $filter
     */
    protected function _init($filter)
    {
        if (!$this->_initDone) {
            $dimHelper = Mage::helper('configurableswatches/swatchdimensions');
            $this->setSwatchInnerWidth(
                $dimHelper->getInnerWidth(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_LAYER),
            );
            $this->setSwatchInnerHeight(
                $dimHelper->getInnerHeight(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_LAYER),
            );
            $this->setSwatchOuterWidth(
                $dimHelper->getOuterWidth(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_LAYER),
            );
            $this->setSwatchOuterHeight(
                $dimHelper->getOuterHeight(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_LAYER),
            );

            $swatchUrl = Mage::helper('configurableswatches/productimg')
                ->getGlobalSwatchUrl(
                    $filter,
                    $this->stripTags($filter->getLabel()),
                    $this->getSwatchInnerWidth(),
                    $this->getSwatchInnerHeight(),
                );
            $this->setSwatchUrl($swatchUrl);

            $this->_initDone = true;
        }
    }
}

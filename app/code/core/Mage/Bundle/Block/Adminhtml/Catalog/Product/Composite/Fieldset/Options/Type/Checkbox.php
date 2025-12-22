<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Bundle option checkbox type renderer
 *
 * @package    Mage_Bundle
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Options_Type_Checkbox extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Checkbox
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->setTemplate('bundle/product/composite/fieldset/options/type/checkbox.phtml');
    }

    /**
     * @param  string $elementId
     * @param  string $containerId
     * @return string
     */
    public function setValidationContainer($elementId, $containerId)
    {
        return '<script type="text/javascript">
            $(\'' . $elementId . "').advaiceContainer = '" . $containerId . '\';
            </script>';
    }
}

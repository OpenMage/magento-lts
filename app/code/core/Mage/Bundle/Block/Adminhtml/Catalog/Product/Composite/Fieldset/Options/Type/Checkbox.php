<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Bundle
 */

/**
 * Bundle option checkbox type renderer
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Options_Type_Checkbox extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Checkbox
{
    /**
     * Set template
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
            $(\'' . $elementId . '\').advaiceContainer = \'' . $containerId . '\';
            </script>';
    }
}

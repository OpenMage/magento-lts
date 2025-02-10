<?php
/**
 * Bundle option multi select type renderer
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Bundle
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Options_Type_Multi extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Multi
{
    /**
     * Set template
     */
    protected function _construct()
    {
        $this->setTemplate('bundle/product/composite/fieldset/options/type/multi.phtml');
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

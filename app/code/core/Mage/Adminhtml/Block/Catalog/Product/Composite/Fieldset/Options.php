<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml block for fieldset of product custom options
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options extends Mage_Catalog_Block_Product_View_Options
{
    public function __construct()
    {
        parent::__construct();
        $this->addOptionRenderer(
            'default',
            'catalog/product_view_options_type_default',
            'catalog/product/composite/fieldset/options/type/default.phtml',
        );
    }

    /**
     * Get option html block
     *
     *
     * @return string
     */
    public function getOptionHtml(Mage_Catalog_Model_Product_Option $option)
    {
        if (!empty($option['file_extension'])) {
            $option['file_extension'] = $this->escapeHtml($option['file_extension']);
        }
        $renderer = $this->getOptionRender(
            $this->getGroupOfOption($option->getType()),
        );
        if (is_null($renderer['renderer'])) {
            $renderer['renderer'] = $this->getLayout()->createBlock($renderer['block'])
                ->setTemplate($renderer['template'])
                ->setSkipJsReloadPrice(1);
        }
        return $renderer['renderer']
            ->setProduct($this->getProduct())
            ->setOption($option)
            ->toHtml();
    }
}

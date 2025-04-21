<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Product Price Template Block
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_Price_Template extends Mage_Core_Block_Abstract
{
    /**
     * Product Price block types cache
     *
     * @var array
     */
    protected $_priceBlockTypes = [];

    /**
     * Retrieve array of Price Block Types
     *
     * Key is price block type name and value is array of
     * template and block strings
     *
     * @return array
     */
    public function getPriceBlockTypes()
    {
        return $this->_priceBlockTypes;
    }

    /**
     * Adding customized price template for product type
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return $this
     */
    public function addPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_priceBlockTypes[$type] = [
                'block'     => $block,
                'template'  => $template,
            ];
        }

        return $this;
    }
}

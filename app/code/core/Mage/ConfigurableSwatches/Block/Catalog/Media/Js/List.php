<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ConfigurableSwatches
 */

/**
 * Class Mage_ConfigurableSwatches_Block_Catalog_Media_Js_List
 *
 * @package    Mage_ConfigurableSwatches
 */
class Mage_ConfigurableSwatches_Block_Catalog_Media_Js_List extends Mage_ConfigurableSwatches_Block_Catalog_Media_Js_Abstract
{
    /**
     * Get target product IDs from product collection
     * which was set on block
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->getProductCollection();
    }

    /**
     * Default to small image type
     *
     * @return string
     */
    public function getImageType()
    {
        $type = parent::getImageType();

        if (empty($type)) {
            $type = Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_SMALL;
        }

        return $type;
    }

    /**
     * instruct small_image image type to be loaded
     *
     * @return array
     */
    protected function _getImageSizes()
    {
        return ['small_image'];
    }

    /**
     * Prevent actual block render if we are disabled, and i.e. via the module
     * config as opposed to the advanced module settings page
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!Mage::getStoreConfigFlag(Mage_ConfigurableSwatches_Helper_Data::CONFIG_PATH_LIST_SWATCH_ATTRIBUTE)) {
            return '';
        }
        return parent::_toHtml();
    }
}

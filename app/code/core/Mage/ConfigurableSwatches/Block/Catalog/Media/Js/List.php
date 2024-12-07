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
 * Class Mage_ConfigurableSwatches_Block_Catalog_Media_Js_List
 *
 * @category   Mage
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

<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Bundle
 */

/**
 * Bundle helper
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_NODE_BUNDLE_PRODUCT_TYPE = 'global/catalog/product/type/bundle';

    protected $_moduleName = 'Mage_Bundle';

    /**
     * Retrieve array of allowed product types for bundle selection product
     *
     * @return array
     */
    public function getAllowedSelectionTypes()
    {
        $config = Mage::getConfig()->getNode(self::XML_NODE_BUNDLE_PRODUCT_TYPE);
        return array_keys($config->allowed_selection_types->asArray());
    }
}

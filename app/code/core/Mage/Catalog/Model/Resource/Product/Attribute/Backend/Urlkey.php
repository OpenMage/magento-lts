<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product url key attribute backend
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Before save
     *
     * @param Mage_Catalog_Model_Product $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();

        $urlKey = $object->getData($attributeName);
        if ($urlKey == '') {
            $urlKey = $object->getName();
        }

        $object->setData($attributeName, $object->formatUrlKey($urlKey));

        return $this;
    }

    /**
     * Refresh product rewrites
     *
     * @param Mage_Catalog_Model_Product $object
     * @return $this
     */
    public function afterSave($object)
    {
        if ($object->dataHasChangedFor($this->getAttribute()->getName())) {
            Mage::getSingleton('catalog/url')->refreshProductRewrites(null, $object, true);
        }
        return $this;
    }
}

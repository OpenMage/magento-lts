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
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog category landing page attribute source
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Category_Attribute_Source_Mode extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Returns all mode options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                [
                    'value' => Mage_Catalog_Model_Category::DM_PRODUCT,
                    'label' => Mage::helper('catalog')->__('Products only'),
                ],
                [
                    'value' => Mage_Catalog_Model_Category::DM_PAGE,
                    'label' => Mage::helper('catalog')->__('Static block only'),
                ],
                [
                    'value' => Mage_Catalog_Model_Category::DM_MIXED,
                    'label' => Mage::helper('catalog')->__('Static block and products'),
                ]
            ];
        }
        return $this->_options;
    }
}

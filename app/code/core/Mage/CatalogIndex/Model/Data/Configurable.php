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
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configurable product data retriever
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Data_Configurable extends Mage_CatalogIndex_Model_Data_Abstract
{
    /**
     * Defines when product type has children
     *
     * @var int[]|bool[]
     */
    protected $_haveChildren = [
        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_TIERS => false,
        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_PRICES => false,
        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_ATTRIBUTES => true,
    ];

    /**
     * Defines when product type has parents
     *
     * @var bool
     */
    protected $_haveParents = false;

    protected function _construct()
    {
        $this->_init('catalogindex/data_configurable');
    }

    /**
      * Retrieve product type code
      *
      * @return string
      */
    public function getTypeCode()
    {
        return Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
    }

    /**
     * Get child link table and field settings
     *
     * @return mixed
     */
    protected function _getLinkSettings()
    {
        return [
                    'table' => 'catalog/product_super_link',
                    'parent_field' => 'parent_id',
                    'child_field' => 'product_id',
        ];
    }
}

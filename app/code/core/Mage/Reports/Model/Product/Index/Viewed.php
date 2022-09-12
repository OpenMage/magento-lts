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
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Viewed Product Index
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Reports_Model_Resource_Product_Index_Viewed _getResource()
 * @method Mage_Reports_Model_Resource_Product_Index_Viewed getResource()
 * @method Mage_Reports_Model_Product_Index_Viewed setVisitorId(int $value)
 * @method Mage_Reports_Model_Product_Index_Viewed setCustomerId(int $value)
 * @method int getProductId()
 * @method Mage_Reports_Model_Product_Index_Viewed setProductId(int $value)
 * @method Mage_Reports_Model_Product_Index_Viewed setStoreId(int $value)
 * @method string getAddedAt()
 * @method Mage_Reports_Model_Product_Index_Viewed setAddedAt(string $value)
 */
class Mage_Reports_Model_Product_Index_Viewed extends Mage_Reports_Model_Product_Index_Abstract
{
    /**
     * Cache key name for Count of product index
     *
     * @var string
     */
    protected $_countCacheKey   = 'product_index_viewed_count';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('reports/product_index_viewed');
    }

    /**
     * Retrieve Exclude Product Ids List for Collection
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        $productIds = [];

        if (Mage::registry('current_product')) {
            $productIds[] = Mage::registry('current_product')->getId();
        }

        return $productIds;
    }
}

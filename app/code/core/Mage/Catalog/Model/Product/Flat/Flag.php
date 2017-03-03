<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Calatog Product Flat Flag Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Flat_Flag extends Mage_Core_Model_Flag
{
    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = 'catalog_product_flat';

    /**
     * Retrieve flag data array
     *
     * @return array
     */
    public function getFlagData()
    {
        $flagData = parent::getFlagData();
        if (!is_array($flagData)) {
            $flagData = array();
            $this->setFlagData($flagData);
        }
        return $flagData;
    }

    /**
     * Returns true if store's flat index has been built.
     *
     * @param int $storeId
     * @return bool
     */
    public function isStoreBuilt($storeId)
    {
        $key = 'is_store_built_' . (int)$storeId;
        $flagData = $this->getFlagData();
        if (!isset($flagData[$key])) {
            $flagData[$key] = false;
            $this->setFlagData($flagData);
        }
        return (bool)$flagData[$key];
    }

    /**
     * Defines whether flat index for specific store has been built.
     *
     * @param int  $storeId
     * @param bool $built
     * @return Mage_Catalog_Model_Product_Flat_Flag
     */
    public function setStoreBuilt($storeId, $built)
    {
        $key = 'is_store_built_' . (int)$storeId;
        $flagData = $this->getFlagData();
        $flagData[$key] = (bool)$built;
        $this->setFlagData($flagData);
        return $this;
    }

    /**
     * Retrieve Catalog Product Flat Data is built flag
     *
     * @return bool
     */
    public function getIsBuilt()
    {
        $flagData = $this->getFlagData();
        if (!isset($flagData['is_built'])) {
            $flagData['is_built'] = false;
            $this->setFlagData($flagData);
        }
        return (bool)$flagData['is_built'];
    }

    /**
     * Set Catalog Product Flat Data is built flag
     *
     * @param bool $flag
     *
     * @return Mage_Catalog_Model_Product_Flat_Flag
     */
    public function setIsBuilt($flag)
    {
        $flagData = $this->getFlagData();
        $flagData['is_built'] = (bool)$flag;
        $this->setFlagData($flagData);
        return $this;
    }

    /**
     * Set Catalog Product Flat Data is built flag
     *
     * @deprecated after 1.7.0.0 use Mage_Catalog_Model_Product_Flat_Flag::setIsBuilt() instead
     *
     * @param bool $flag
     *
     * @return Mage_Catalog_Model_Product_Flat_Flag
     */
    public function setIsBuild($flag)
    {
        $this->setIsBuilt($flag);
        return $this;
    }
}

<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Calatog Product Flat Flag Model
 *
 * @category   Mage
 * @package    Mage_Catalog
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
            $flagData = [];
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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setIsBuild($flag)
    {
        $this->setIsBuilt($flag);
        return $this;
    }
}

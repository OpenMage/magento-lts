<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable link model
 *
 * @category   Mage
 * @package    Mage_Downloadable
 *
 * @method Mage_Downloadable_Model_Resource_Link _getResource()
 * @method Mage_Downloadable_Model_Resource_Link getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Collection getCollection()
 *
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
 * @method int getNumberOfDownloads()
 * @method $this setNumberOfDownloads(int $value)
 * @method bool getIsUnlimited()
 * @method int getIsShareable()
 * @method $this setIsShareable(int $value)
 * @method int getLinkId()
 * @method string getLinkUrl()
 * @method $this setLinkUrl(string $value)
 * @method string getLinkFile()
 * @method $this setLinkFile(string $value)
 * @method string getLinkType()
 * @method $this setLinkType(string $value)
 * @method string getSampleUrl()
 * @method $this setSampleUrl(string $value)
 * @method string getSampleFile()
 * @method $this setSampleFile(string $value)
 * @method string getSampleType()
 * @method $this setSampleType(string $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getStoreTitle()
 * @method float getPrice()
 * @method $this setPrice(float $value)
 * @method Mage_Catalog_Model_Product getProduct()
 * @method $this setProduct(Mage_Catalog_Model_Product $value)
 * @method array getProductWebsiteIds()
 * @method $this setProductWebsiteIds(array $value)
 * @method string getTitle()
 * @method bool getUseDefaultPrice()
 * @method bool getUseDefaultTitle()
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $value)
 * @method float getWebsitePrice()
 */
class Mage_Downloadable_Model_Link extends Mage_Core_Model_Abstract
{
    public const XML_PATH_LINKS_TITLE              = 'catalog/downloadable/links_title';
    public const XML_PATH_DEFAULT_DOWNLOADS_NUMBER = 'catalog/downloadable/downloads_number';
    public const XML_PATH_TARGET_NEW_WINDOW        = 'catalog/downloadable/links_target_new_window';
    public const XML_PATH_CONFIG_IS_SHAREABLE      = 'catalog/downloadable/shareable';

    public const LINK_SHAREABLE_YES    = 1;
    public const LINK_SHAREABLE_NO     = 0;
    public const LINK_SHAREABLE_CONFIG = 2;

    protected function _construct()
    {
        $this->_init('downloadable/link');
        parent::_construct();
    }

    /**
     * Return link files path
     *
     * @return string
     */
    public static function getLinkDir()
    {
        return Mage::getBaseDir();
    }

    /**
     * @inheritDoc
     */
    protected function _afterSave()
    {
        $this->getResource()->saveItemTitleAndPrice($this);
        return parent::_afterSave();
    }

    /**
     * Retrieve base temporary path
     *
     * @return string
     */
    public static function getBaseTmpPath()
    {
        return Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'tmp' . DS . 'links';
    }

    /**
     * Retrieve Base files path
     *
     * @return string
     */
    public static function getBasePath()
    {
        return Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'files' . DS . 'links';
    }

    /**
     * Retrieve base sample temporary path
     *
     * @return string
     */
    public static function getBaseSampleTmpPath()
    {
        return Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'tmp' . DS . 'link_samples';
    }

    /**
     * Retrieve base sample path
     *
     * @return string
     */
    public static function getBaseSamplePath()
    {
        return Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'files' . DS . 'link_samples';
    }

    /**
     * Retrieve links searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        return $this->_getResource()
            ->getSearchableData($productId, $storeId);
    }
}

<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable link model
 *
 * @package    Mage_Downloadable
 *
 * @method Mage_Downloadable_Model_Resource_Link            _getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Collection getCollection()
 * @method int                                              getIsShareable()
 * @method bool                                             getIsUnlimited()
 * @method string                                           getLinkFile()
 * @method int                                              getLinkId()
 * @method string                                           getLinkType()
 * @method string                                           getLinkUrl()
 * @method int                                              getNumberOfDownloads()
 * @method float                                            getPrice()
 * @method Mage_Catalog_Model_Product                       getProduct()
 * @method int                                              getProductId()
 * @method array                                            getProductWebsiteIds()
 * @method Mage_Downloadable_Model_Resource_Link            getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Collection getResourceCollection()
 * @method string                                           getSampleFile()
 * @method string                                           getSampleType()
 * @method string                                           getSampleUrl()
 * @method int                                              getSortOrder()
 * @method int                                              getStoreId()
 * @method string                                           getStoreTitle()
 * @method string                                           getTitle()
 * @method bool                                             getUseDefaultPrice()
 * @method bool                                             getUseDefaultTitle()
 * @method int                                              getWebsiteId()
 * @method float                                            getWebsitePrice()
 * @method $this                                            setIsShareable(int $value)
 * @method $this                                            setLinkFile(string $value)
 * @method $this                                            setLinkType(string $value)
 * @method $this                                            setLinkUrl(string $value)
 * @method $this                                            setNumberOfDownloads(int $value)
 * @method $this                                            setPrice(float $value)
 * @method $this                                            setProduct(Mage_Catalog_Model_Product $value)
 * @method $this                                            setProductId(int $value)
 * @method $this                                            setProductWebsiteIds(array $value)
 * @method $this                                            setSampleFile(string $value)
 * @method $this                                            setSampleType(string $value)
 * @method $this                                            setSampleUrl(string $value)
 * @method $this                                            setSortOrder(int $value)
 * @method $this                                            setStoreId(int $value)
 * @method $this                                            setWebsiteId(int $value)
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

    /**
     * @inheritDoc
     */
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
     * @param  int   $productId
     * @param  int   $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        return $this->_getResource()
            ->getSearchableData($productId, $storeId);
    }
}

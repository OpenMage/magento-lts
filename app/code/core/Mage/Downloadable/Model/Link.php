<?php

declare(strict_types=1);

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
 * @method bool                                             getIsUnlimited()
 * @method Mage_Catalog_Model_Product                       getProduct()
 * @method array                                            getProductWebsiteIds()
 * @method Mage_Downloadable_Model_Resource_Link            getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Collection getResourceCollection()
 * @method bool                                             getUseDefaultPrice()
 * @method bool                                             getUseDefaultTitle()
 * @method $this                                            setProduct(Mage_Catalog_Model_Product $value)
 * @method $this                                            setProductWebsiteIds(array $value)
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

    public function getIsShareable(): int
    {
        return (int) $this->_getData('is_shareable');
    }

    public function getLinkFile(): ?string
    {
        $value = $this->_getData('link_file');
        return $value !== null ? (string) $value : null;
    }

    public function getLinkType(): ?string
    {
        $value = $this->_getData('link_type');
        return $value !== null ? (string) $value : null;
    }

    public function getLinkUrl(): ?string
    {
        $value = $this->_getData('link_url');
        return $value !== null ? (string) $value : null;
    }

    public function getNumberOfDownloads(): ?int
    {
        $value = $this->_getData('number_of_downloads');
        return $value !== null ? (int) $value : null;
    }

    public function getPrice(): float
    {
        return (float) $this->_getData('price');
    }

    public function getProductId(): int
    {
        return (int) $this->_getData('product_id');
    }

    public function getSampleFile(): ?string
    {
        $value = $this->_getData('sample_file');
        return $value !== null ? (string) $value : null;
    }

    public function getSampleType(): ?string
    {
        $value = $this->_getData('sample_type');
        return $value !== null ? (string) $value : null;
    }

    public function getSampleUrl(): ?string
    {
        $value = $this->_getData('sample_url');
        return $value !== null ? (string) $value : null;
    }

    public function getSortOrder(): int
    {
        return (int) $this->_getData('sort_order');
    }

    public function setIsShareable(int $value): static
    {
        return $this->setData('is_shareable', $value);
    }

    public function setLinkFile(?string $value): static
    {
        return $this->setData('link_file', $value);
    }

    public function setLinkType(?string $value): static
    {
        return $this->setData('link_type', $value);
    }

    public function setLinkUrl(?string $value): static
    {
        return $this->setData('link_url', $value);
    }

    public function setNumberOfDownloads(?int $value): static
    {
        return $this->setData('number_of_downloads', $value);
    }

    public function setPrice(float $value): static
    {
        return $this->setData('price', $value);
    }

    public function setProductId(int $value): static
    {
        return $this->setData('product_id', $value);
    }

    public function setSampleFile(?string $value): static
    {
        return $this->setData('sample_file', $value);
    }

    public function setSampleType(?string $value): static
    {
        return $this->setData('sample_type', $value);
    }

    public function setSampleUrl(?string $value): static
    {
        return $this->setData('sample_url', $value);
    }

    public function setSortOrder(int $value): static
    {
        return $this->setData('sort_order', $value);
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
    #[Override]
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

    public function getLinkId(): int
    {
        return (int) $this->_getData('link_id');
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function getWebsiteId(): int
    {
        return (int) $this->_getData('website_id');
    }

    public function getWebsitePrice(): float
    {
        return (float) $this->_getData('website_price');
    }

    public function getStoreTitle(): string
    {
        return (string) $this->_getData('store_title');
    }

    public function getTitle(): string
    {
        return (string) $this->_getData('title');
    }
}

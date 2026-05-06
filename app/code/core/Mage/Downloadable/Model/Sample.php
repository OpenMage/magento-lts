<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable sample model
 *
 * @package    Mage_Downloadable
 *
 * @method Mage_Downloadable_Model_Resource_Sample            _getResource()
 * @method Mage_Downloadable_Model_Resource_Sample_Collection getCollection()
 * @method Mage_Downloadable_Model_Resource_Sample            getResource()
 * @method Mage_Downloadable_Model_Resource_Sample_Collection getResourceCollection()
 * @method string                                             getStoreTitle()
 * @method string                                             getTitle()
 * @method bool                                               getUseDefaultTitle()
 */
class Mage_Downloadable_Model_Sample extends Mage_Core_Model_Abstract
{
    public const XML_PATH_SAMPLES_TITLE = 'catalog/downloadable/samples_title';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('downloadable/sample');
        parent::_construct();
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

    public function getSampleType(): string
    {
        return (string) $this->_getData('sample_type');
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

    public function setProductId(int $value): static
    {
        return $this->setData('product_id', $value);
    }

    public function setSampleFile(?string $value): static
    {
        return $this->setData('sample_file', $value);
    }

    public function setSampleType(string $value): static
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
     * Return sample files path
     *
     * @return string
     */
    public static function getSampleDir()
    {
        return Mage::getBaseDir();
    }

    /**
     * After save process
     *
     * @inheritDoc
     */
    #[Override]
    protected function _afterSave()
    {
        $this->getResource()
            ->saveItemTitle($this);
        return parent::_afterSave();
    }

    /**
     * Retrieve sample URL
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->getSampleUrl()) {
            return $this->getSampleUrl();
        }

        return $this->getSampleFile();
    }

    /**
     * Retrieve base tmp path
     *
     * @return string
     */
    public static function getBaseTmpPath()
    {
        return Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'tmp' . DS . 'samples';
    }

    /**
     * Retrieve sample files path
     *
     * @return string
     */
    public static function getBasePath()
    {
        return Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'files' . DS . 'samples';
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

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }
}

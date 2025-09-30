<?php

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
 * @method Mage_Downloadable_Model_Resource_Sample _getResource()
 * @method Mage_Downloadable_Model_Resource_Sample getResource()
 * @method Mage_Downloadable_Model_Resource_Sample_Collection getCollection()
 *
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method string getSampleUrl()
 * @method $this setSampleUrl(string $value)
 * @method string|null getSampleFile()
 * @method $this setSampleFile(string $value)
 * @method string getSampleType()
 * @method $this setSampleType(string $value)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getStoreTitle()
 * @method string getTitle()
 * @method bool getUseDefaultTitle()
 */
class Mage_Downloadable_Model_Sample extends Mage_Core_Model_Abstract
{
    public const XML_PATH_SAMPLES_TITLE = 'catalog/downloadable/samples_title';

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('downloadable/sample');
        parent::_construct();
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
        } else {
            return $this->getSampleFile();
        }
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

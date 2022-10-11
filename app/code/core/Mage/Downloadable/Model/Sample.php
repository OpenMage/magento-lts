<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable sample model
 *
 * @method Mage_Downloadable_Model_Resource_Sample _getResource()
 * @method Mage_Downloadable_Model_Resource_Sample getResource()
 * @method Mage_Downloadable_Model_Resource_Sample_Collection getCollection()
 *
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method string getSampleUrl()
 * @method $this setSampleUrl(string $value)
 * @method string getSampleFile()
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
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Sample extends Mage_Core_Model_Abstract
{
    const XML_PATH_SAMPLES_TITLE = 'catalog/downloadable/samples_title';

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

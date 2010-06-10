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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable link model
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Link extends Mage_Core_Model_Abstract
{
    const XML_PATH_LINKS_TITLE              = 'catalog/downloadable/links_title';
    const XML_PATH_DEFAULT_DOWNLOADS_NUMBER = 'catalog/downloadable/downloads_number';
    const XML_PATH_TARGET_NEW_WINDOW        = 'catalog/downloadable/links_target_new_window';
    const XML_PATH_CONFIG_IS_SHAREABLE      = 'catalog/downloadable/shareable';

    const LINK_SHAREABLE_YES    = 1;
    const LINK_SHAREABLE_NO     = 0;
    const LINK_SHAREABLE_CONFIG = 2;

    /**
     * Initialize resource model
     *
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
     * Enter description here...
     *
     * @return Mage_Downloadable_Model_Link
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

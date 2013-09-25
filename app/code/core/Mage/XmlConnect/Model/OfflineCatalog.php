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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect offline catalog model
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_OfflineCatalog
{
    /**
     * Category type
     */
    const CATEGORY_TYPE = 'category';

    /**
     * Product type
     */
    const PRODUCT_TYPE = 'product';

    /**
     * Home banners type
     */
    const HOMEBANNERS_TYPE = 'homebanners';

    /**
     * Home page type
     */
    const HOME_TYPE = 'home';

    /**
     * Config type
     */
    const CONFIG_TYPE = 'config';

    /**
     * Run export by type
     *
     * @param string $type
     * @return Mage_XmlConnect_Model_OfflineCatalog
     */
    protected function _runExport($type)
    {
        $exportModel = $this->_getExportModel($type);
        if (null !== $exportModel) {
            $exportModel->exportData();
        }
        return $this;
    }

    /**
     * Export offline catalog data
     *
     * @return Mage_XmlConnect_Model_OfflineCatalog
     */
    public function exportData()
    {
        Mage::helper('xmlconnect/offlineCatalog')->prepareResultDirectory();
        $this->_runExport(self::CATEGORY_TYPE)->_runExport(self::PRODUCT_TYPE)->_runExport(self::HOMEBANNERS_TYPE)
            ->_runExport(self::HOME_TYPE)->_runExport(self::CONFIG_TYPE);
        return $this;
    }

    /**
     * Get export model by type
     *
     * @param string $type
     * @return Mage_Core_Model_Abstract|null
     */
    protected function _getExportModel($type)
    {
        switch ($type) {
            case self::CATEGORY_TYPE:
                return Mage::getModel('xmlconnect/offlineCatalog_category');
                break;
            case self::PRODUCT_TYPE:
                return Mage::getModel('xmlconnect/offlineCatalog_product');
                break;
            case self::HOMEBANNERS_TYPE:
                return Mage::getModel('xmlconnect/offlineCatalog_homebanners');
                break;
            case self::HOME_TYPE:
                return Mage::getModel('xmlconnect/offlineCatalog_home');
                break;
            case self::CONFIG_TYPE:
                return Mage::getModel('xmlconnect/offlineCatalog_config');
                break;
            default:
                return null;
        }
    }
}

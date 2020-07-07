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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product media config
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Media_Config implements Mage_Media_Model_Image_Config_Interface
{
    /**
     * Filesystem directory path of product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseMediaPathAddition()
    {
        return 'catalog' . DS . 'product';
    }

    /**
     * Web-based directory path of product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseMediaUrlAddition()
    {
        return 'catalog/product';
    }

    /**
     * Filesystem directory path of temporary product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseTmpMediaPathAddition()
    {
        return 'tmp' . DS . $this->getBaseMediaPathAddition();
    }

    /**
     * Web-based directory path of temporary product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseTmpMediaUrlAddition()
    {
        return 'tmp/' . $this->getBaseMediaUrlAddition();
    }

    /**
     * @return string
     */
    public function getBaseMediaPath()
    {
        return Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product';
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return Mage::getBaseUrl('media') . 'catalog/product';
    }

    /**
     * @return string
     */
    public function getBaseTmpMediaPath()
    {
        return Mage::getBaseDir('media') . DS . $this->getBaseTmpMediaPathAddition();
    }

    /**
     * @return string
     */
    public function getBaseTmpMediaUrl()
    {
        return Mage::getBaseUrl('media') . $this->getBaseTmpMediaUrlAddition();
    }

    /**
     * @param string $file
     * @return string
     */
    public function getMediaUrl($file)
    {
        $file = $this->_prepareFileForUrl($file);

        if (substr($file, 0, 1) == '/') {
            return $this->getBaseMediaUrl() . $file;
        }

        return $this->getBaseMediaUrl() . '/' . $file;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getMediaPath($file)
    {
        $file = $this->_prepareFileForPath($file);

        if (substr($file, 0, 1) == DS) {
            return $this->getBaseMediaPath() . DS . substr($file, 1);
        }

        return $this->getBaseMediaPath() . DS . $file;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getTmpMediaUrl($file)
    {
        $file = $this->_prepareFileForUrl($file);

        if (substr($file, 0, 1) == '/') {
            $file = substr($file, 1);
        }

        return $this->getBaseTmpMediaUrl() . '/' . $file;
    }

    /**
     * Part of URL of temporary product images
     * relatively to media folder
     *
     * @param string $file
     * @return string
     */
    public function getTmpMediaShortUrl($file)
    {
        $file = $this->_prepareFileForUrl($file);

        if (substr($file, 0, 1) == '/') {
            $file = substr($file, 1);
        }

        return $this->getBaseTmpMediaUrlAddition() . '/' . $file;
    }

    /**
     * Part of URL of product images relatively to media folder
     *
     * @param string $file
     * @return string
     */
    public function getMediaShortUrl($file)
    {
        $file = $this->_prepareFileForUrl($file);

        if (substr($file, 0, 1) == '/') {
            $file = substr($file, 1);
        }

        return $this->getBaseMediaUrlAddition() . '/' . $file;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getTmpMediaPath($file)
    {
        $file = $this->_prepareFileForPath($file);

        if (substr($file, 0, 1) == DS) {
            return $this->getBaseTmpMediaPath() . DS . substr($file, 1);
        }

        return $this->getBaseTmpMediaPath() . DS . $file;
    }

    /**
     * @param string $file
     * @return string
     */
    protected function _prepareFileForUrl($file)
    {
        return str_replace(DS, '/', $file);
    }

    /**
     * @param string $file
     * @return string
     */
    protected function _prepareFileForPath($file)
    {
        return str_replace('/', DS, $file);
    }
}

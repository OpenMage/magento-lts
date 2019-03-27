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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog image helper
 *
 * Helper used to display thumbnail image for category
 * Prepare category image for cache and resize for mobile devices
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Helper_Catalog_Category_Image extends Mage_Catalog_Helper_Image
{
    /**
     * Init
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $attributeName
     * @param string $imageFile
     * @return Mage_XmlConnect_Helper_Catalog_Category_Image
     */
    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        return $this;
    }

    /**
     * Init image helper object
     *
     * @param Mage_Catalog_Model_Abstract $category
     * @param string $attributeName
     * @param string $imageFile
     * @return Mage_XmlConnect_Helper_Catalog_Category_Image
     */
    public function initialize(Mage_Catalog_Model_Abstract $category, $attributeName, $imageFile = null)
    {
        $this->_reset();
        $this->_setModel(Mage::getModel('xmlconnect/catalog_category_image'));
        $this->_getModel()->setDestinationSubdir($attributeName);
        $this->setProduct($category);

        $this->setWatermark(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_image")
        );
        $this->setWatermarkImageOpacity(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_imageOpacity")
        );
        $this->setWatermarkPosition(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_position")
        );
        $this->setWatermarkSize(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_size")
        );

        if ($imageFile) {
            $this->setImageFile($imageFile);
        } else {
            /*
             * add for work original size
             */
            $this->_getModel()->setBaseFile($this->getProduct()->getData($this->_getModel()->getDestinationSubdir()));
        }
        return $this;
    }

    /**
     * Re-write parent to handle an exception if any
     *
     * @return string
     */
    public function __toString()
    {
        try {
            if ($this->getImageFile()) {
                $this->_getModel()->setBaseFile($this->getImageFile());
            } else {
                $this->_getModel()->setBaseFile(
                    $this->getProduct()->getData($this->_getModel()->getDestinationSubdir())
                );
            }

            if ($this->_getModel()->isCached()) {
                return $this->_getModel()->getUrl();
            } else {
                if ($this->_scheduleRotate) {
                    $this->_getModel()->rotate($this->getAngle());
                }

                if ($this->_scheduleResize) {
                    $this->_getModel()->resize();
                }

                if ($this->getWatermark()) {
                    $this->_getModel()->setWatermark($this->getWatermark());
                }

                $url = $this->_getModel()->saveFile()->getUrl();
            }
        } catch(Exception $e) {
            Mage::logException($e);
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
            $params = array('_package' => Mage_Core_Model_Design_Package::DEFAULT_PACKAGE,
                '_theme' => Mage_Core_Model_Design_Package::DEFAULT_THEME);
            $filePath = Mage::getDesign()->getSkinBaseDir($params) . DS . str_replace('/', DS, $this->getPlaceholder());
            $this->_getModel()->setNewFile($filePath);
        }
        return $url;
    }

    /**
     * Get new file path
     *
     * @return string
     */
    public function getNewFile()
    {
        return $this->_getModel()->getNewFile();
    }

    /**
     * Return placeholder image file path
     *
     * @return string
     */
    public function getPlaceholder()
    {
        if (!$this->_placeholder) {
            $attr = $this->_getModel()->getDestinationSubdir();
            $this->_placeholder = 'images/xmlconnect/catalog/category/placeholder/' . $attr . '.jpg';
        }
        return $this->_placeholder;
    }
}

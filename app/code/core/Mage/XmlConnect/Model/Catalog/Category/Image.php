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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog category link model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Catalog_Category_Image extends Mage_Catalog_Model_Product_Image
{
    /**
     * Set file names for base file and new file
     *
     * @param string $file
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setBaseFile($file)
    {
        $this->_isBaseFilePlaceholder = false;

        if (($file) && (0 !== strpos($file, '/', 0))) {
            $file = '/' . $file;
        }
        $baseDir = Mage::getSingleton('xmlconnect/catalog_category_media_config')->getBaseMediaPath();

        if ('/no_selection' == $file) {
            $file = null;
        }
        if ($file) {
            if ((!file_exists($baseDir . $file)) || !$this->_checkMemory($baseDir . $file)) {
                $file = null;
            }
        }
        if (!$file) {
            // check if placeholder defined in config
            $isConfigPlaceholder = Mage::getStoreConfig(
                'catalog/placeholder/' . $this->getDestinationSubdir() . '_placeholder'
            );
            $configPlaceholder   = '/placeholder/' . $isConfigPlaceholder;
            if ($isConfigPlaceholder && file_exists($baseDir . $configPlaceholder)) {
                $file = $configPlaceholder;
            } else {
                // replace file with skin or default skin placeholder
                $skinBaseDir     = Mage::getDesign()->getSkinBaseDir();
                $skinPlaceholder = '/images/xmlconnect/catalog/category/placeholder/' . $this->getDestinationSubdir()
                    . '.jpg';

                $file = $skinPlaceholder;
                if (file_exists($skinBaseDir . $file)) {
                    $baseDir = $skinBaseDir;
                } else {
                    $baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default'));
                    if (!file_exists($baseDir . $file)) {
                        $baseDir = Mage::getDesign()->getSkinBaseDir(
                            array('_theme' => 'default', '_package' => 'base')
                        );
                    }
                }
            }
            $this->_isBaseFilePlaceholder = true;
        }

        $baseFile = $baseDir . $file;

        if ((!$file) || (!file_exists($baseFile))) {
            Mage::throwException(Mage::helper('xmlconnect')->__('Image file was not found.'));
        }

        $this->_baseFile = $baseFile;

        // build new filename (most important params)
        $path = array(Mage::getSingleton('xmlconnect/catalog_category_media_config')->getBaseMediaPath(),
            'cache', Mage::app()->getStore()->getId(), $path[] = $this->getDestinationSubdir());

        if ((!empty($this->_width)) || (!empty($this->_height))) {
            $path[] = "{$this->_width}x{$this->_height}";
        }

        // add misk params as a hash
        $miscParams = array(
            ($this->_keepAspectRatio  ? '' : 'non') . 'proportional',
            ($this->_keepFrame        ? '' : 'no')  . 'frame',
            ($this->_keepTransparency ? '' : 'no')  . 'transparency',
            ($this->_constrainOnly ? 'do' : 'not')  . 'constrainonly',
            $this->_rgbToString($this->_backgroundColor),
            'angle' . $this->_angle,
            'quality' . $this->_quality
        );

        // if has watermark add watermark params to hash
        if ($this->getWatermarkFile()) {
            $miscParams[] = $this->getWatermarkFile();
            $miscParams[] = $this->getWatermarkImageOpacity();
            $miscParams[] = $this->getWatermarkPosition();
            $miscParams[] = $this->getWatermarkWidth();
            $miscParams[] = $this->getWatermarkHeigth();
        }

        $path[] = md5(implode('_', $miscParams));

        // append prepared filename
        $this->_newFile = implode('/', $path) . $file; // the $file contains heading slash

        return $this;
    }

    /**
     * Set new file
     *
     * @param string $filePath
     * @return $this
     */
    public function setNewFile($filePath)
    {
        $this->_newFile = $filePath;
        return $this;
    }

    /**
     * Get relative watermark file path
     * or false if file not found
     *
     * @return string|bool
     */
    protected function _getWatermarkFilePath()
    {
        $filePath = false;

        if (!$file = $this->getWatermarkFile()) {
            return $filePath;
        }

        $baseDir = Mage::getSingleton('xmlconnect/catalog_category_media_config')->getBaseMediaPath();

        if (file_exists($baseDir . '/watermark/stores/' . Mage::app()->getStore()->getId() . $file)) {
            $filePath = $baseDir . '/watermark/stores/' . Mage::app()->getStore()->getId() . $file;
        } elseif (file_exists($baseDir . '/watermark/websites/' . Mage::app()->getWebsite()->getId() . $file)) {
            $filePath = $baseDir . '/watermark/websites/' . Mage::app()->getWebsite()->getId() . $file;
        } elseif (file_exists($baseDir . '/watermark/default/' . $file)) {
            $filePath = $baseDir . '/watermark/default/' . $file;
        } elseif (file_exists($baseDir . '/watermark/' . $file)) {
            $filePath = $baseDir . '/watermark/' . $file;
        } else {
            $baseDir = Mage::getDesign()->getSkinBaseDir();
            if (file_exists($baseDir . $file)) {
                $filePath = $baseDir . $file;
            }
        }

        return $filePath;
    }

    /**
     * Clear catalog cache
     *
     * @return null
     */
    public function clearCache()
    {
        $directory = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS . 'cache' . DS;
        $ioFile = new Varien_Io_File();
        $ioFile->rmdir($directory, true);
    }

    /**
     * Convert array of 3 items (decimal r, g, b) to string of their hex values
     *
     * @param array $rgbArray
     * @return string
     */
    protected function _rgbToString($rgbArray)
    {
        $result = array();
        foreach ($rgbArray as $value) {
            if (null === $value) {
                $result[] = 'null';
            } else {
                $result[] = sprintf('%02s', dechex($value));
            }
        }
        return implode($result);
    }
}

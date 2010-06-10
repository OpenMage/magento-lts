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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product media api V2
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Media_Api_V2 extends Mage_Catalog_Model_Product_Attribute_Media_Api
{
    /**
     * Prepare data to create or update image
     *
     * @param stdClass $data
     * @return array
     */
    protected function _prepareImageData($data)
    {
        $_imageData = array();
        if (isset($data->label)) {
            $_imageData['label'] = $data->label;
        }
        if (isset($data->position)) {
            $_imageData['position'] = $data->position;
        }
        if (isset($data->disabled)) {
            $_imageData['disabled'] = $data->disabled;
        }
        if (isset($data->exclude)) {
            $_imageData['exclude'] = $data->exclude;
        }
        return $_imageData;
    }

    /**
     * Create new image for product and return image filename
     *
     * @param int|string $productId
     * @param array $data
     * @param string|int $store
     * @return string
     */
    public function create($productId, $data, $store = null, $identifierType = null)
    {
        $product = $this->_initProduct($productId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        if (!isset($data->file) || !isset($data->file->mime) || !isset($data->file->content)) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('The image is not specified.'));
        }

        if (!isset($this->_mimeTypes[$data->file->mime])) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid image type.'));
        }

        $fileContent = @base64_decode($data->file->content, true);
        if (!$fileContent) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('The image contents is not valid base64 data.'));
        }

        unset($data->file->content);

        $tmpDirectory = Mage::getBaseDir('var') . DS . 'api' . DS . $this->_getSession()->getSessionId();

        if (isset($data->file->name) && $data->file->name) {
            $fileName  = $data->file->name;
        } else {
            $fileName  = 'image';
        }
        $fileName .= '.' . $this->_mimeTypes[$data->file->mime];

        $ioAdapter = new Varien_Io_File();
        try {
            // Create temporary directory for api
            $ioAdapter->checkAndCreateFolder($tmpDirectory);
            $ioAdapter->open(array('path'=>$tmpDirectory));
            // Write image file
            $ioAdapter->write($fileName, $fileContent, 0666);
            unset($fileContent);

            // Adding image to gallery
            $file = $gallery->getBackend()->addImage(
                $product,
                $tmpDirectory . DS . $fileName,
                null,
                true
            );

            // Remove temporary directory
            $ioAdapter->rmdir($tmpDirectory, true);

            $_imageData = $this->_prepareImageData($data);

            $gallery->getBackend()->updateImage($product, $file, $_imageData);

            if (isset($data->types)) {
                $gallery->getBackend()->setMediaAttribute($product, $data->types, $file);
            }

            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_created', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('not_created', Mage::helper('catalog')->__('Cannot create image.'));
        }

        return $gallery->getBackend()->getRenamedImage($file);
    }

    /**
     * Update image data
     *
     * @param int|string $productId
     * @param string $file
     * @param array $data
     * @param string|int $store
     * @return boolean
     */
    public function update($productId, $file, $data, $store = null, $identifierType = null)
    {
        $product = $this->_initProduct($productId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        if (!$gallery->getBackend()->getImage($product, $file)) {
            $this->_fault('not_exists');
        }

        $_imageData = $this->_prepareImageData($data);

        $gallery->getBackend()->updateImage($product, $file, $_imageData);

        if (isset($data->types) && is_array($data->types)) {
            $oldTypes = array();
            foreach ($product->getMediaAttributes() as $attribute) {
                if ($product->getData($attribute->getAttributeCode()) == $file) {
                     $oldTypes[] = $attribute->getAttributeCode();
                }
            }

            $clear = array_diff($oldTypes, $data->types);

            if (count($clear) > 0) {
                $gallery->getBackend()->clearMediaAttribute($product, $clear);
            }

            $gallery->getBackend()->setMediaAttribute($product, $data->types, $file);
        }

        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_updated', $e->getMessage());
        }

        return true;
    }
}

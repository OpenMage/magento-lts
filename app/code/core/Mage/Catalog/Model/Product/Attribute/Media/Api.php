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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product media api
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Media_Api extends Mage_Catalog_Model_Api_Resource
{
    /**
     * Attribute code for media gallery
     *
     */
    const ATTRIBUTE_CODE = 'media_gallery';

    /**
     * Allowed mime types for image
     *
     * @var array
     */
    protected $_mimeTypes = array(
        'image/jpeg' => 'jpg',
        'image/gif'  => 'gif',
        'image/png'  => 'png'
    );

    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
    }

    /**
     * Retrieve images for product
     *
     * @param int|string $productId
     * @param string|int $store
     * @return array
     */
    public function items($productId, $store = null, $identifierType = null)
    {
        $product = $this->_initProduct($productId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        $galleryData = $product->getData(self::ATTRIBUTE_CODE);

        if (!isset($galleryData['images']) || !is_array($galleryData['images'])) {
            return array();
        }

        $result = array();

        foreach ($galleryData['images'] as &$image) {
            $result[] = $this->_imageToArray($image, $product);
        }

        return $result;
    }

    /**
     * Retrieve image data
     *
     * @param int|string $productId
     * @param string $file
     * @param string|int $store
     * @return array
     */
    public function info($productId, $file, $store = null, $identifierType = null)
    {
        $product = $this->_initProduct($productId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        if (!$image = $gallery->getBackend()->getImage($product, $file)) {
            $this->_fault('not_exists');
        }

        return $this->_imageToArray($image, $product);
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

        if (!isset($data['file']) || !isset($data['file']['mime']) || !isset($data['file']['content'])) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('Image not specified.'));
        }

        if (!isset($this->_mimeTypes[$data['file']['mime']])) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid image type.'));
        }

        $fileContent = @base64_decode($data['file']['content'], true);
        if (!$fileContent) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('Image content is not valid base64 data.'));
        }

        unset($data['file']['content']);

        $tmpDirectory = Mage::getBaseDir('var') . DS . 'api' . DS . $this->_getSession()->getSessionId();

        if (isset($data['file']['name']) && $data['file']['name']) {
            $fileName  = $data['file']['name'];
        } else {
            $fileName  = 'image';
        }
        $fileName .= '.' . $this->_mimeTypes[$data['file']['mime']];

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

            $gallery->getBackend()->updateImage($product, $file, $data);

            if (isset($data['types'])) {
                $gallery->getBackend()->setMediaAttribute($product, $data['types'], $file);
            }

            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_created', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('not_created', Mage::helper('catalog')->__('Can\'t create image.'));
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

        $gallery->getBackend()->updateImage($product, $file, $data);

        if (isset($data['types']) && is_array($data['types'])) {
            $oldTypes = array();
            foreach ($product->getMediaAttributes() as $attribute) {
                if ($product->getData($attribute->getAttributeCode()) == $file) {
                     $oldTypes[] = $attribute->getAttributeCode();
                }
            }

            $clear = array_diff($oldTypes, $data['types']);

            if (count($clear) > 0) {
                $gallery->getBackend()->clearMediaAttribute($product, $clear);
            }

            $gallery->getBackend()->setMediaAttribute($product, $data['types'], $file);
        }

        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_updated', $e->getMessage());
        }

        return true;
    }

    /**
     * Remove image from product
     *
     * @param int|string $productId
     * @param string $file
     * @return boolean
     */
    public function remove($productId, $file, $identifierType = null)
    {
        $product = $this->_initProduct($productId, null, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        if (!$gallery->getBackend()->getImage($product, $file)) {
            $this->_fault('not_exists');
        }

        $gallery->getBackend()->removeImage($product, $file);

        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_removed', $e->getMessage());
        }

        return true;
    }


    /**
     * Retrieve image types (image, small_image, thumbnail, etc...)
     *
     * @param int $setId
     * @return array
     */
    public function types($setId)
    {
        $attributes = Mage::getModel('catalog/product')->getResource()
                ->loadAllAttributes()
                ->getSortedAttributes($setId);

        $result = array();

        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if ($attribute->isInSet($setId)
                && $attribute->getFrontendInput() == 'media_image') {
                if ($attribute->isScopeGlobal()) {
                    $scope = 'global';
                } elseif ($attribute->isScopeWebsite()) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = array(
                    'code'         => $attribute->getAttributeCode(),
                    'scope'        => $scope
                );
            }
        }

        return $result;
    }

    /**
     * Retrieve gallery attribute from product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute|boolean
     */
    protected function _getGalleryAttribute($product)
    {
        $attributes = $product->getTypeInstance(true)
            ->getSetAttributes($product);

        if (!isset($attributes[self::ATTRIBUTE_CODE])) {
            $this->_fault('not_media');
        }

        return $attributes[self::ATTRIBUTE_CODE];
    }

    /**
     * Retrie
     * ve media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    protected function _getMediaConfig()
    {
        return Mage::getSingleton('catalog/product_media_config');
    }

    /**
     * Converts image to api array data
     *
     * @param array $image
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _imageToArray(&$image, $product)
    {
        $result = array(
            'file'      => $image['file'],
            'label'     => $image['label'],
            'position'  => $image['position'],
            'exclude'   => $image['disabled'],
            'url'       => $this->_getMediaConfig()->getMediaUrl($image['file']),
            'types'     => array()
        );


        foreach ($product->getMediaAttributes() as $attribute) {
            if ($product->getData($attribute->getAttributeCode()) == $image['file']) {
                $result['types'][] = $attribute->getAttributeCode();
            }
        }

        return $result;
    }

    /**
     * Retrieve product
     *
     * @param int|string $productId
     * @param string|int $store
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct($productId, $store = null, $identifierType = null)
    {
        $loadByIdOnFalse = false;
        if ($identifierType === null) {
            $identifierType = 'sku';
            $loadByIdOnFalse = true;
        }
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')
                       ->setStoreId($this->_getStoreId($store));
        if ($identifierType == 'sku') {
            $idBySku = $product->getIdBySku($productId);
            if ($idBySku) {
                $productId = $idBySku;
            }
            if ($idBySku || $loadByIdOnFalse) {
                $product->load($productId);
            }
        } elseif ($identifierType == 'id') {
            $product->load($productId);
        }

        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }

        return $product;
    }
} // Class Mage_Catalog_Model_Product_Attribute_Media_Api End

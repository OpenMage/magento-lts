<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product media api
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Media_Api extends Mage_Catalog_Model_Api_Resource
{
    /**
     * Attribute code for media gallery
     *
     */
    public const ATTRIBUTE_CODE = 'media_gallery';

    /**
     * Allowed mime types for image
     *
     * @var array
     */
    protected $_mimeTypes = [
        'image/webp' => 'webp',
        'image/jpeg' => 'jpg',
        'image/gif'  => 'gif',
        'image/png'  => 'png',
    ];

    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
    }

    /**
     * Retrieve images for product
     *
     * @param int|string $productId
     * @param string|int $store
     * @param string|null $identifierType
     * @return array
     */
    public function items($productId, $store = null, $identifierType = null)
    {
        $product = $this->_initProduct($productId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        $galleryData = $product->getData(self::ATTRIBUTE_CODE);

        if (!isset($galleryData['images']) || !is_array($galleryData['images'])) {
            return [];
        }

        $result = [];

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
     * @param string|null $identifierType
     * @return array
     * @throws Mage_Api_Exception
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
     * @param string|null $identifierType
     * @return string
     * @throws Mage_Api_Exception
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function create($productId, $data, $store = null, $identifierType = null)
    {
        $data = $this->_prepareImageData($data);

        $product = $this->_initProduct($productId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        if (!isset($data['file']) || !isset($data['file']['mime']) || !isset($data['file']['content'])) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('The image is not specified.'));
        }

        if (!isset($this->_mimeTypes[$data['file']['mime']])) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid image type.'));
        }

        $fileContent = @base64_decode($data['file']['content'], true);
        if (!$fileContent) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('The image contents is not valid base64 data.'));
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
            $ioAdapter->open(['path' => $tmpDirectory]);
            // Write image file
            $ioAdapter->write($fileName, $fileContent, 0666);
            unset($fileContent);

            // try to create Image object - it fails with Exception if image is not supported
            try {
                $filePath = $tmpDirectory . DS . $fileName;
                Mage::getModel('varien/image', $filePath);
                Mage::getModel('core/file_validator_image')->validate($filePath);
            } catch (Exception $e) {
                // Remove temporary directory
                $ioAdapter->rmdir($tmpDirectory, true);

                throw new Mage_Core_Exception($e->getMessage(), $e->getCode(), $e);
            }

            // Adding image to gallery
            $file = $gallery->getBackend()->addImage(
                $product,
                $tmpDirectory . DS . $fileName,
                null,
                true,
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
     * @param string|null $identifierType
     * @return bool
     * @throws Mage_Api_Exception
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function update($productId, $file, $data, $store = null, $identifierType = null)
    {
        $data = $this->_prepareImageData($data);

        $product = $this->_initProduct($productId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        if (!$gallery->getBackend()->getImage($product, $file)) {
            $this->_fault('not_exists');
        }

        if (isset($data['file']['mime']) && isset($data['file']['content'])) {
            if (!isset($this->_mimeTypes[$data['file']['mime']])) {
                $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid image type.'));
            }

            $fileContent = @base64_decode($data['file']['content'], true);
            if (!$fileContent) {
                $this->_fault('data_invalid', Mage::helper('catalog')->__('Image content is not valid base64 data.'));
            }

            unset($data['file']['content']);

            $ioAdapter = new Varien_Io_File();
            try {
                $fileName = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $file;
                $ioAdapter->open(['path' => dirname($fileName)]);
                $ioAdapter->write(basename($fileName), $fileContent, 0666);
            } catch (Exception $e) {
                $this->_fault('not_created', Mage::helper('catalog')->__('Can\'t create image.'));
            }
        }

        $gallery->getBackend()->updateImage($product, $file, $data);

        if (isset($data['types']) && is_array($data['types'])) {
            $oldTypes = [];
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
     * @param string|null $identifierType
     * @return bool
     * @throws Mage_Api_Exception
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

        $result = [];

        foreach ($attributes as $attribute) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            if ($attribute->isInSet($setId)
                && $attribute->getFrontendInput() == 'media_image'
            ) {
                if ($attribute->isScopeGlobal()) {
                    $scope = 'global';
                } elseif ($attribute->isScopeWebsite()) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = [
                    'code'         => $attribute->getAttributeCode(),
                    'scope'        => $scope,
                ];
            }
        }

        return $result;
    }

    /**
     * Prepare data to create or update image
     *
     * @param array $data
     * @return array
     */
    protected function _prepareImageData($data)
    {
        return $data;
    }

    /**
     * Retrieve gallery attribute from product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Attribute|bool
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
        $result = [
            'file'      => $image['file'],
            'label'     => $image['label'],
            'position'  => $image['position'],
            'exclude'   => $image['disabled'],
            'url'       => $this->_getMediaConfig()->getMediaUrl($image['file']),
            'types'     => [],
        ];

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
     * @param  string $identifierType
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct($productId, $store = null, $identifierType = null)
    {
        $product = Mage::helper('catalog/product')->getProduct($productId, $this->_getStoreId($store), $identifierType);
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }

        return $product;
    }
}

<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product media gallery attribute backend model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Media extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @var array
     */
    protected $_renamedImages = [];

    /**
     * Load attribute data after product loaded
     *
     * @param Mage_Catalog_Model_Product $object
     * @return $this
     */
    public function afterLoad($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = [];
        $value['images'] = [];
        $value['values'] = [];
        $localAttributes = ['label', 'position', 'disabled'];

        foreach ($this->_getResource()->loadGallery($object, $this) as $image) {
            foreach ($localAttributes as $localAttribute) {
                if (is_null($image[$localAttribute])) {
                    $image[$localAttribute . '_use_default'] = true;
                    $image[$localAttribute] = $this->_getDefaultValue($localAttribute, $image);
                } else {
                    $image[$localAttribute . '_use_default'] = false;
                }
            }
            $value['images'][] = $image;
        }

        $object->setData($attrCode, $value);
        return $this;
    }

    /**
     * @param string $key
     * @param array $image
     * @return string
     */
    protected function _getDefaultValue($key, &$image)
    {
        return $image[$key . '_default'] ?? '';
    }

    /**
     * Validate media_gallery attribute data
     *
     * @param Mage_Catalog_Model_Product $object
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function validate($object)
    {
        if ($this->getAttribute()->getIsRequired()) {
            $value = $object->getData($this->getAttribute()->getAttributeCode());
            if ($this->getAttribute()->isValueEmpty($value)) {
                if (!(is_array($value) && count($value) > 0)) {
                    return false;
                }
            }
        }
        if ($this->getAttribute()->getIsUnique()) {
            if (!$this->getAttribute()->getEntity()->checkAttributeUniqueValue($this->getAttribute(), $object)) {
                $label = $this->getAttribute()->getFrontend()->getLabel();
                Mage::throwException(Mage::helper('eav')->__('The value of attribute "%s" must be unique.', $label));
            }
        }

        return true;
    }

    /**
     * @param Mage_Catalog_Model_Product $object
     * @return $this|Mage_Eav_Model_Entity_Attribute_Backend_Abstract|void
     * @throws Zend_Json_Exception
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!is_array($value) || !isset($value['images'])) {
            return;
        }

        if (!is_array($value['images']) && strlen($value['images']) > 0) {
            $value['images'] = Mage::helper('core')->jsonDecode($value['images']);
        }

        if (!isset($value['values'])) {
            $value['values'] = [];
        }

        if (!is_array($value['values']) && strlen($value['values']) > 0) {
            $value['values'] = Mage::helper('core')->jsonDecode($value['values']);
        }

        if (!is_array($value['images'])) {
            $value['images'] = [];
        }

        $clearImages = [];
        $newImages   = [];
        $existImages = [];
        if ($object->getIsDuplicate() != true) {
            foreach ($value['images'] as &$image) {
                if (!empty($image['removed'])) {
                    $clearImages[] = $image['file'];
                } elseif (!isset($image['value_id'])) {
                    $newFile = $this->_moveImageFromTmp($image['file']);
                    $image['new_file'] = $newFile;
                    $newImages[$image['file']] = $image;
                    $this->_renamedImages[$image['file']] = $newFile;
                    $image['file'] = $newFile;
                } else {
                    $existImages[$image['file']] = $image;
                }
            }
        } else {
            // For duplicating we need copy original images.
            $duplicate = [];
            foreach ($value['images'] as &$image) {
                if (!isset($image['value_id'])) {
                    continue;
                }
                $newFile = $this->_copyImage($image['file']);
                $newImages[$image['file']] = [
                    'new_file' => $newFile,
                    'label' => $image['label'],
                ];
                $duplicate[$image['value_id']] = $newFile;
            }
        }

        foreach ($object->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $attrData = $object->getData($mediaAttrCode);

            if (empty($attrData)) {
                continue;
            }

            if (in_array($attrData, $clearImages)) {
                $object->setData($mediaAttrCode, 'no_selection');
            }
        }

        foreach ($value['values'] as $mediaAttrCode => $attrData) {
            if (array_key_exists($attrData, $newImages)) {
                $object->setData($mediaAttrCode, $newImages[$attrData]['new_file']);
                $label = $newImages[$attrData]['label'] === null || !empty($newImages[$attrData]['label_use_default']) ? $newImages[$attrData]['label_default'] : $newImages[$attrData]['label'];
                $object->setData($mediaAttrCode . '_label', $label);
            }

            if (array_key_exists($attrData, $existImages)) {
                $label = $existImages[$attrData]['label'] === null || !empty($existImages[$attrData]['label_use_default']) ? $existImages[$attrData]['label_default'] : $existImages[$attrData]['label'];
                $object->setData($mediaAttrCode . '_label', $label);
            }
        }

        Mage::dispatchEvent('catalog_product_media_save_before', ['product' => $object, 'images' => $value]);
        $object->setData($attrCode, $value);
        return $this;
    }

    /**
     * Retrieve renamed image name
     *
     * @param string $file
     * @return string
     */
    public function getRenamedImage($file)
    {
        return $this->_renamedImages[$file] ?? $file;
    }

    /**
     * @param Mage_Catalog_Model_Product $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract|void
     */
    public function afterSave($object)
    {
        if ($object->getIsDuplicate() == true) {
            $this->duplicate($object);
            return;
        }

        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!is_array($value) || !isset($value['images']) || $object->isLockedAttribute($attrCode)) {
            return;
        }

        $storeId = $object->getStoreId();
        $storeIds = $object->getStoreIds();
        $storeIds[] = Mage_Core_Model_App::ADMIN_STORE_ID;

        // remove current storeId
        $storeIds = array_flip($storeIds);
        unset($storeIds[$storeId]);
        $storeIds = array_keys($storeIds);

        $images = Mage::getResourceModel('catalog/product')
            ->getAssignedImages($object, $storeIds);

        $picturesInOtherStores = [];
        foreach ($images as $image) {
            $picturesInOtherStores[$image['filepath']] = true;
        }

        $toDelete = [];
        foreach ($value['images'] as &$image) {
            if (!empty($image['removed'])) {
                if (isset($image['value_id']) && !isset($picturesInOtherStores[$image['file']])) {
                    $toDelete[] = $image['value_id'];
                }
                continue;
            }

            if (!isset($image['value_id'])) {
                $data = [];
                $data['entity_id']      = $object->getId();
                $data['attribute_id']   = $this->getAttribute()->getId();
                $data['value']          = $image['file'];
                $image['value_id']      = $this->_getResource()->insertGallery($data);
            }

            if ($storeId === 0) {
                $image['label_use_default'] = false;
                $image['position_use_default'] = false;
            } else {
                if (!isset($image['label_use_default'])) {
                    $image['label_use_default'] = null;
                }
                if (!isset($image['position_use_default'])) {
                    $image['position_use_default'] = null;
                }
            }

            $this->_getResource()->deleteGalleryValueInStore($image['value_id'], $object->getStoreId());

            // Add per store labels, position, disabled
            $data = [];
            $data['value_id'] = $image['value_id'];
            $data['label']    = ($image['label'] === null || $image['label_use_default']) ? null : $image['label'];
            $data['position'] = ($image['position'] === null || $image['position_use_default']) ? null : (int) $image['position'];
            $data['disabled'] = (int) $image['disabled'];
            $data['store_id'] = (int) $object->getStoreId();

            $this->_getResource()->insertGalleryValueInStore($data);
        }

        $this->_getResource()->deleteGallery($toDelete);
    }

    /**
     * Add image to media gallery and return new filename
     *
     * @param string                     $file              file path of image in file system
     * @param string|array               $mediaAttribute    code of attribute with type 'media_image',
     *                                                      leave blank if image should be only in gallery
     * @param bool                    $move              if true, it will move source file
     * @param bool                    $exclude           mark image as disabled in product page view
     * @return string
     * @throws Mage_Core_Exception
     */
    public function addImage(
        Mage_Catalog_Model_Product $product,
        $file,
        $mediaAttribute = null,
        $move = false,
        $exclude = true
    ) {
        if (str_contains($file, chr(0))
            || preg_match('#(^|[\\\\/])\.\.($|[\\\\/])#', $file)
        ) {
            throw new Exception('Detected malicious path or filename input.');
        }

        $file = realpath($file);

        if (!$file || !file_exists($file)) {
            Mage::throwException(Mage::helper('catalog')->__('Image does not exist.'));
        }

        Mage::dispatchEvent('catalog_product_media_add_image', ['product' => $product, 'image' => $file]);

        $pathinfo = pathinfo($file);
        if (!isset($pathinfo['extension']) || !in_array(strtolower($pathinfo['extension']), Varien_Io_File::ALLOWED_IMAGES_EXTENSIONS)) {
            Mage::throwException(Mage::helper('catalog')->__('Invalid image file type.'));
        }

        $fileName       = Mage_Core_Model_File_Uploader::getCorrectFileName($pathinfo['basename']);
        $dispretionPath = Mage_Core_Model_File_Uploader::getDispretionPath($fileName);
        $fileName       = $dispretionPath . DS . $fileName;

        $fileName = $this->_getNotDuplicatedFilename($fileName, $dispretionPath);

        $ioAdapter = new Varien_Io_File();
        $ioAdapter->setAllowCreateFolders(true);
        $distanationDirectory = dirname($this->_getConfig()->getTmpMediaPath($fileName));

        try {
            $ioAdapter->open([
                'path' => $distanationDirectory,
            ]);

            /** @var Mage_Core_Helper_File_Storage_Database $storageHelper */
            $storageHelper = Mage::helper('core/file_storage_database');
            if ($move) {
                $ioAdapter->mv($file, $this->_getConfig()->getTmpMediaPath($fileName));

                //If this is used, filesystem should be configured properly
                $storageHelper->saveFile($this->_getConfig()->getTmpMediaShortUrl($fileName));
            } else {
                $ioAdapter->cp($file, $this->_getConfig()->getTmpMediaPath($fileName));

                $storageHelper->saveFile($this->_getConfig()->getTmpMediaShortUrl($fileName));
                $ioAdapter->chmod($this->_getConfig()->getTmpMediaPath($fileName), 0777);
            }
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('catalog')->__('Failed to move file: %s', $e->getMessage()));
        }

        $fileName = str_replace(DS, '/', $fileName);

        $attrCode = $this->getAttribute()->getAttributeCode();
        $mediaGalleryData = $product->getData($attrCode);
        $position = 0;
        if (!is_array($mediaGalleryData)) {
            $mediaGalleryData = [
                'images' => [],
            ];
        }

        foreach ($mediaGalleryData['images'] as &$image) {
            if (isset($image['position']) && $image['position'] > $position) {
                $position = $image['position'];
            }
        }

        $position++;
        $mediaGalleryData['images'][] = [
            'file'     => $fileName,
            'position' => $position,
            'label'    => '',
            'disabled' => (int) $exclude,
        ];

        $product->setData($attrCode, $mediaGalleryData);

        if (!is_null($mediaAttribute)) {
            $this->setMediaAttribute($product, $mediaAttribute, $fileName);
        }

        return $fileName;
    }

    /**
     * Add images with different media attributes.
     * Image will be added only once if the same image is used with different media attributes
     *
     * @param array $fileAndAttributesArray array of arrays of filename and corresponding media attribute
     * @param string $filePath path, where image cand be found
     * @param bool $move if true, it will move source file
     * @param bool $exclude mark image as disabled in product page view
     * @return array array of parallel arrays with original and renamed files
     * @throws Mage_Core_Exception
     */
    public function addImagesWithDifferentMediaAttributes(
        Mage_Catalog_Model_Product $product,
        $fileAndAttributesArray,
        $filePath = '',
        $move = false,
        $exclude = true
    ) {
        $alreadyAddedFiles = [];
        $alreadyAddedFilesNames = [];

        foreach ($fileAndAttributesArray as $key => $value) {
            $keyInAddedFiles = array_search($value['file'], $alreadyAddedFiles, true);
            if ($keyInAddedFiles === false) {
                $savedFileName = $this->addImage($product, $filePath . $value['file'], null, $move, $exclude);
                $alreadyAddedFiles[$key] = $value['file'];
                $alreadyAddedFilesNames[$key] = $savedFileName;
            } else {
                $savedFileName = $alreadyAddedFilesNames[$keyInAddedFiles];
            }

            if (!is_null($value['mediaAttribute'])) {
                $this->setMediaAttribute($product, $value['mediaAttribute'], $savedFileName);
            }
        }

        return ['alreadyAddedFiles' => $alreadyAddedFiles, 'alreadyAddedFilesNames' => $alreadyAddedFilesNames];
    }

    /**
     * Update image in gallery
     *
     * @param string $file
     * @param array $data
     * @return $this
     */
    public function updateImage(Mage_Catalog_Model_Product $product, $file, $data)
    {
        $fieldsMap = [
            'label'    => 'label',
            'position' => 'position',
            'disabled' => 'disabled',
            'exclude'  => 'disabled',
        ];

        $attrCode = $this->getAttribute()->getAttributeCode();

        $mediaGalleryData = $product->getData($attrCode);

        if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
            return $this;
        }

        foreach ($mediaGalleryData['images'] as &$image) {
            if ($image['file'] == $file) {
                foreach ($fieldsMap as $mappedField => $realField) {
                    if (isset($data[$mappedField])) {
                        $image[$realField] = $data[$mappedField];
                    }
                }
            }
        }

        $product->setData($attrCode, $mediaGalleryData);
        return $this;
    }

    /**
     * Remove image from gallery
     *
     * @param string $file
     * @return $this
     */
    public function removeImage(Mage_Catalog_Model_Product $product, $file)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();

        $mediaGalleryData = $product->getData($attrCode);

        if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
            return $this;
        }

        foreach ($mediaGalleryData['images'] as &$image) {
            if ($image['file'] == $file) {
                $image['removed'] = 1;
            }
        }

        $product->setData($attrCode, $mediaGalleryData);

        return $this;
    }

    /**
     * Retrieve image from gallery
     *
     * @param string $file
     * @return array|bool
     */
    public function getImage(Mage_Catalog_Model_Product $product, $file)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $mediaGalleryData = $product->getData($attrCode);
        if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
            return false;
        }

        foreach ($mediaGalleryData['images'] as $image) {
            if ($image['file'] == $file) {
                return $image;
            }
        }

        return false;
    }

    /**
     * Clear media attribute value
     *
     * @param string|array $mediaAttribute
     * @return $this
     */
    public function clearMediaAttribute(Mage_Catalog_Model_Product $product, $mediaAttribute)
    {
        $mediaAttributeCodes = array_keys($product->getMediaAttributes());

        if (is_array($mediaAttribute)) {
            foreach ($mediaAttribute as $attribute) {
                if (in_array($attribute, $mediaAttributeCodes)) {
                    $product->setData($attribute, null);
                }
            }
        } elseif (in_array($mediaAttribute, $mediaAttributeCodes)) {
            $product->setData($mediaAttribute, null);
        }

        return $this;
    }

    /**
     * Set media attribute value
     *
     * @param string|array $mediaAttribute
     * @param string $value
     * @return $this
     */
    public function setMediaAttribute(Mage_Catalog_Model_Product $product, $mediaAttribute, $value)
    {
        $mediaAttributeCodes = array_keys($product->getMediaAttributes());

        if (is_array($mediaAttribute)) {
            foreach ($mediaAttribute as $attribute) {
                if (in_array($attribute, $mediaAttributeCodes)) {
                    $product->setData($attribute, $value);
                }
            }
        } elseif (in_array($mediaAttribute, $mediaAttributeCodes)) {
            $product->setData($mediaAttribute, $value);
        }

        return $this;
    }

    /**
     * Retrieve resource model
     *
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media|object
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('catalog/product_attribute_backend_media');
    }

    /**
     * Retrieve media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('catalog/product_media_config');
    }

    /**
     * Move image from temporary directory to normal
     *
     * @param string $file
     * @return string
     */
    protected function _moveImageFromTmp($file)
    {
        $ioObject = new Varien_Io_File();
        $destDirectory = dirname($this->_getConfig()->getMediaPath($file));
        try {
            $ioObject->open(['path' => $destDirectory]);
        } catch (Exception $e) {
            $ioObject->mkdir($destDirectory, 0777, true);
            $ioObject->open(['path' => $destDirectory]);
        }

        if (strrpos($file, '.tmp') == strlen($file) - 4) {
            $file = substr($file, 0, -4);
        }
        $destFile = $this->_getUniqueFileName($file, $ioObject->dirsep());

        /** @var Mage_Core_Helper_File_Storage_Database $storageHelper */
        $storageHelper = Mage::helper('core/file_storage_database');

        if ($storageHelper->checkDbUsage()) {
            $storageHelper->renameFile(
                $this->_getConfig()->getTmpMediaShortUrl($file),
                $this->_getConfig()->getMediaShortUrl($destFile),
            );

            $ioObject->rm($this->_getConfig()->getTmpMediaPath($file));
            $ioObject->rm($this->_getConfig()->getMediaPath($destFile));
        } else {
            $ioObject->mv(
                $this->_getConfig()->getTmpMediaPath($file),
                $this->_getConfig()->getMediaPath($destFile),
            );
        }

        return str_replace($ioObject->dirsep(), '/', $destFile);
    }

    /**
     * Check whether file to move exists. Getting unique name
     *
     * @param string $file
     * @param string $dirsep
     * @return string
     */
    protected function _getUniqueFileName($file, $dirsep)
    {
        if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
            $destFile = Mage::helper('core/file_storage_database')
                ->getUniqueFilename(
                    Mage::getSingleton('catalog/product_media_config')->getBaseMediaUrlAddition(),
                    $file,
                );
        } else {
            $destFile = dirname($file) . $dirsep
                . Mage_Core_Model_File_Uploader::getNewFileName($this->_getConfig()->getMediaPath($file));
        }

        return $destFile;
    }

    /**
     * Copy image and return new filename.
     *
     * @param string $file
     * @return string
     * @throws Mage_Core_Exception
     */
    protected function _copyImage($file)
    {
        try {
            $ioObject = new Varien_Io_File();
            $destDirectory = dirname($this->_getConfig()->getMediaPath($file));
            $ioObject->open(['path' => $destDirectory]);

            $destFile = $this->_getUniqueFileName($file, $ioObject->dirsep());

            if (!$ioObject->fileExists($this->_getConfig()->getMediaPath($file), true)) {
                throw new Exception();
            }

            if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
                Mage::helper('core/file_storage_database')
                    ->copyFile(
                        $this->_getConfig()->getMediaShortUrl($file),
                        $this->_getConfig()->getMediaShortUrl($destFile),
                    );

                $ioObject->rm($this->_getConfig()->getMediaPath($destFile));
            } else {
                $ioObject->cp(
                    $this->_getConfig()->getMediaPath($file),
                    $this->_getConfig()->getMediaPath($destFile),
                );
            }
        } catch (Exception $e) {
            $file = $this->_getConfig()->getMediaPath($file);
            $io = new Varien_Io_File();
            Mage::throwException(
                Mage::helper('catalog')->__(
                    'Failed to copy file %s. Please, delete media with non-existing images and try again.',
                    $io->getFilteredPath($file),
                ),
            );
        }

        return str_replace($ioObject->dirsep(), '/', $destFile);
    }

    /**
     * @param Mage_Catalog_Model_Product $object
     * @return $this
     */
    public function duplicate($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $mediaGalleryData = $object->getData($attrCode);

        if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
            return $this;
        }

        $this->_getResource()->duplicate(
            $this,
            $mediaGalleryData['duplicate'] ?? [],
            $object->getOriginalId(),
            $object->getId(),
        );

        return $this;
    }

    /**
     * Get filename which is not duplicated with other files in media temporary and media directories
     *
     * @param String $fileName
     * @param String $dispretionPath
     * @return String
     */
    protected function _getNotDuplicatedFilename($fileName, $dispretionPath)
    {
        $fileMediaName = $dispretionPath . DS
                  . Mage_Core_Model_File_Uploader::getNewFileName($this->_getConfig()->getMediaPath($fileName));
        $fileTmpMediaName = $dispretionPath . DS
                  . Mage_Core_Model_File_Uploader::getNewFileName($this->_getConfig()->getTmpMediaPath($fileName));

        if ($fileMediaName != $fileTmpMediaName) {
            if ($fileMediaName != $fileName) {
                return $this->_getNotDuplicatedFilename($fileMediaName, $dispretionPath);
            } elseif ($fileTmpMediaName != $fileName) {
                return $this->_getNotDuplicatedFilename($fileTmpMediaName, $dispretionPath);
            }
        }

        return $fileMediaName;
    }
}

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
 * @package     Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_ConfigurableSwatches_Helper_Productimg
 */
class Mage_ConfigurableSwatches_Helper_Productimg extends Mage_Core_Helper_Abstract
{
    /**
     * This array stores product images and separates them:
     * One group keyed by labels that match attribute values, another for all other images
     *
     * @var array
     */
    protected $_productImagesByLabel = array();

    /**
     * This array stores all possible labels and swatch labels used for associating gallery
     * images with swatches and main image swaps. It's use is for filtering the image gallery.
     *
     * @var array
     */
    protected $_productImageFilters = array();

    const SWATCH_LABEL_SUFFIX = '-swatch';
    const SWATCH_FALLBACK_MEDIA_DIR = 'wysiwyg/swatches';
    const SWATCH_CACHE_DIR = 'catalog/swatches';
    const SWATCH_FILE_EXT = '.png';

    const MEDIA_IMAGE_TYPE_BASE = 'base_image';
    const MEDIA_IMAGE_TYPE_SMALL = 'small_image';

    const SWATCH_DEFAULT_WIDTH = 21;
    const SWATCH_DEFAULT_HEIGHT = 21;


    /**
     * Determine if the passed text matches the label of any of the passed product's images
     *
     * @param string $text
     * @param Mage_Catalog_Model_Product $product
     * @param string $type
     * @return Varien_Object|null
     */
    public function getProductImgByLabel($text, $product, $type = null)
    {
        $this->indexProductImages($product);

        //Get the product's image array and prepare the text
        $images = $this->_productImagesByLabel[$product->getId()];
        $text = Mage_ConfigurableSwatches_Helper_Data::normalizeKey($text);

        $resultImages = array(
            'standard' => isset($images[$text]) ? $images[$text] : null,
            'swatch' => isset($images[$text . self::SWATCH_LABEL_SUFFIX]) ? $images[$text . self::SWATCH_LABEL_SUFFIX]
                : null,
        );

        if (!is_null($type) && array_key_exists($type, $resultImages)) {
            $image = $resultImages[$type];
        } else {
            $image = (!is_null($resultImages['swatch'])) ? $resultImages['swatch'] : $resultImages['standard'];
        }

        return $image;
    }

    /**
     * Create the separated index of product images
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array|null $preValues
     * @return Mage_ConfigurableSwatches_Helper_Data
     */
    public function indexProductImages($product, $preValues = null)
    {
        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return; // we only index images on configurable products
        }

        if (!isset($this->_productImagesByLabel[$product->getId()])) {
            $images = array();
            $searchValues = array();

            if (!is_null($preValues) && is_array($preValues)) { // If a pre-defined list of valid values was passed
                $preValues = array_map('Mage_ConfigurableSwatches_Helper_Data::normalizeKey', $preValues);
                foreach ($preValues as $value) {
                    $searchValues[] = $value;
                }
            } else { // we get them from all config attributes if no pre-defined list is passed in
                $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);

                // Collect valid values of image type attributes
                foreach ($attributes as $attribute) {
                    if (Mage::helper('configurableswatches')->attrIsSwatchType($attribute->getAttributeId())) {
                        foreach ($attribute->getPrices() as $option) { // getPrices returns info on individual options
                            $searchValues[] = Mage_ConfigurableSwatches_Helper_Data::normalizeKey($option['label']);
                        }
                    }
                }
            }

            $mapping = $product->getChildAttributeLabelMapping();
            $mediaGallery = $product->getMediaGallery();
            $mediaGalleryImages = $product->getMediaGalleryImages();

            if (empty($mediaGallery['images']) || empty($mediaGalleryImages)) {
                $this->_productImagesByLabel[$product->getId()] = array();
                return; //nothing to do here
            }

            $imageHaystack = array_map(function ($value) {
                return Mage_ConfigurableSwatches_Helper_Data::normalizeKey($value['label']);
            }, $mediaGallery['images']);

            foreach ($searchValues as $label) {
                $imageKeys = array();
                $swatchLabel = $label . self::SWATCH_LABEL_SUFFIX;

                $imageKeys[$label] = array_search($label, $imageHaystack);
                if ($imageKeys[$label] === false) {
                    $imageKeys[$label] = array_search($mapping[$label]['default_label'], $imageHaystack);
                }

                $imageKeys[$swatchLabel] = array_search($swatchLabel, $imageHaystack);
                if ($imageKeys[$swatchLabel] === false) {
                    $imageKeys[$swatchLabel] = array_search(
                        $mapping[$label]['default_label'] . self::SWATCH_LABEL_SUFFIX, $imageHaystack
                    );
                }

                foreach ($imageKeys as $imageLabel => $imageKey) {
                    if ($imageKey !== false) {
                        $imageId = $mediaGallery['images'][$imageKey]['value_id'];
                        $images[$imageLabel] = $mediaGalleryImages->getItemById($imageId);
                    }
                }
            }
            $this->_productImagesByLabel[$product->getId()] = $images;
        }
    }

    /**
     * Return the appropriate swatch URL for the given value (matches against product's image labels)
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $value
     * @param int $width
     * @param int $height
     * @param $swatchType
     * @param string $fallbackFileExt
     * @return string
     */
    public function getSwatchUrl($product, $value, $width = self::SWATCH_DEFAULT_WIDTH,
         $height = self::SWATCH_DEFAULT_HEIGHT, &$swatchType, $fallbackFileExt = null
    ) {
        $url = '';
        $swatchType = 'none';

        // Get the (potential) swatch image that matches the value
        $image = $this->getProductImgByLabel($value, $product, 'swatch');

        // Check in swatch directory if $image is null
        if (is_null($image)) {
            // Check if file exists in fallback directory
            $fallbackUrl = $this->getGlobalSwatchUrl($product, $value, $width, $height, $fallbackFileExt);
            if (!empty($fallbackUrl)) {
                $url = $fallbackUrl;
                $swatchType = 'media';
            }
        }

        // If we still don't have a URL or matching product image, look for one that matches just
        // the label (not specifically the swatch suffix)
        if (empty($url) && is_null($image)) {
            $image = $this->getProductImgByLabel($value, $product, 'standard');
        }

        if (!is_null($image)) {
            $filename = $image->getFile();
            $swatchImage = $this->_resizeSwatchImage($filename, 'product', $width, $height);
            $swatchType = 'product';
            $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $swatchImage;
        }

        return $url;
    }

    /**
     * Return URL for a matching swatch image from the global directory
     *
     * @param Mage_Catalog_Model_Product|Mage_Catalog_Model_Layer_Filter_Item $object
     * @param string $value
     * @param int $width
     * @param int $height
     * @param string $fileExt
     * @throws Mage_Core_Exception
     * @return string
     */
    public function getGlobalSwatchUrl($object, $value, $width = self::SWATCH_DEFAULT_WIDTH,
        $height = self::SWATCH_DEFAULT_HEIGHT, $fileExt = null
    ) {
        if (is_null($fileExt)) {
            $fileExt = self::SWATCH_FILE_EXT;
        }

        // normalize to all lower case so that value can be used as array key below
        $value = Mage_ConfigurableSwatches_Helper_Data::normalizeKey($value);
        $defaultValue = $value; // default to no fallback value
        if ($object instanceof Mage_Catalog_Model_Layer_Filter_Item) { // fallback for swatches loaded for nav filters
            $source = $object->getFilter()->getAttributeModel()->getFrontend()->getAttribute()->getSource();
            foreach ($source->getAllOptions(false, true) as $option) {
                if ($option['value'] == $object->getValue()) {
                    $defaultValue = Mage_ConfigurableSwatches_Helper_Data::normalizeKey($option['label']);
                    break;
                }
            }
        } elseif ($object instanceof Mage_Catalog_Model_Product) {  // fallback for swatches loaded for product view
            $mapping = $object->getChildAttributeLabelMapping();
            if (isset($mapping[$value]['default_label'])) {
                $defaultValue = $mapping[$value]['default_label'];
            }
        }

        do {
            $filename = Mage::helper('configurableswatches')->getHyphenatedString($value) . $fileExt;
            $swatchImage = $this->_resizeSwatchImage($filename, 'media', $width, $height);
            if (!$swatchImage && $defaultValue == $value) {
                return '';  // no image found and no further fallback
            } elseif (!$swatchImage) {
                $value = $defaultValue; // fallback to default value
            } else {
                break;  // we found an image
            }
        } while (true);

        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $swatchImage;
    }

    /**
     * Performs the resize operation on the given swatch image file and returns a
     * relative path to the resulting image file
     *
     * @param string $filename
     * @param string $tag
     * @param int $width
     * @param int $height
     * @return string
     */
    protected function _resizeSwatchImage($filename, $tag, $width, $height)
    {
        // Form full path to where we want to cache resized version
        $destPathArr = array(
            self::SWATCH_CACHE_DIR,
            Mage::app()->getStore()->getId(),
            $width . 'x' . $height,
            $tag,
            trim($filename, '/'),
        );

        $destPath = implode('/', $destPathArr);

        // Check if cached image exists already
        if (!file_exists(Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . $destPath)) {
            // Check for source image
            if ($tag == 'product') {
                $sourceFilePath = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath() . $filename;
            } else {
                $sourceFilePath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA)
                    . DS . self::SWATCH_FALLBACK_MEDIA_DIR . DS . $filename;
            }

            if (!file_exists($sourceFilePath)) {
                return false;
            }

            // Do resize and save
            $processor = new Varien_Image($sourceFilePath);
            $processor->resize($width, $height);
            $processor->save(Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . $destPath);
            Mage::helper('core/file_storage_database')->saveFile($destPath);
        }

        return $destPath;
    }

    /**
     * Cleans out the swatch image cache dir
     */
    public function clearSwatchesCache()
    {
        $directory = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . self::SWATCH_CACHE_DIR;
        $io = new Varien_Io_File();
        $io->rmdir($directory, true);

        Mage::helper('core/file_storage_database')->deleteFolder($directory);
    }

    /**
     * Determine whether to show an image in the product media gallery
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Varien_Object $image
     * @return bool
     */
    public function filterImageInGallery($product, $image)
    {
        if (!Mage::helper('configurableswatches')->isEnabled()) {
            return true;
        }

        if (!isset($this->_productImageFilters[$product->getId()])) {
            $mapping = call_user_func_array("array_merge_recursive", $product->getChildAttributeLabelMapping());
            $filters = array_unique($mapping['labels']);
            $filters = array_merge($filters, array_map(function ($label) {
                return $label . Mage_ConfigurableSwatches_Helper_Productimg::SWATCH_LABEL_SUFFIX;
            }, $filters));
            $this->_productImageFilters[$product->getId()] = $filters;
        }

        return !in_array(Mage_ConfigurableSwatches_Helper_Data::normalizeKey($image->getLabel()),
            $this->_productImageFilters[$product->getId()]);
    }
}

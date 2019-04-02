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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class implementing the media fallback layer for swatches
 */
class Mage_ConfigurableSwatches_Helper_Mediafallback extends Mage_Core_Helper_Abstract
{
    const MEDIA_GALLERY_ATTRIBUTE_CODE = 'media_gallery';

    /**
     * Set child_attribute_label_mapping on products with attribute label -> product mapping
     * Depends on following product data:
     * - product must have children products attached
     *
     * @param array $parentProducts
     * @deprecated use $this->attachProductChildrenAttributeMapping() instead
     * @param $storeId
     * @return void
     */
    public function attachConfigurableProductChildrenAttributeMapping(array $parentProducts, $storeId)
    {
        return $this->attachProductChildrenAttributeMapping($parentProducts, $storeId);
    }

    /**
     * Set child_attribute_label_mapping on products with attribute label -> product mapping
     * Depends on following product data:
     * - product must have children products attached
     *
     * @param array $parentProducts
     * @param $storeId
     * @param bool $onlyListAttributes
     * @return void
     */
    public function attachProductChildrenAttributeMapping(array $parentProducts, $storeId, $onlyListAttributes = false)
    {
        /** @var  $listSwatchAttr Mage_Eav_Model_Attribute */
        $listSwatchAttr = Mage::helper('configurableswatches/productlist')->getSwatchAttribute();
        $swatchAttributeIds = array();
        if (!$onlyListAttributes) {
            $swatchAttributeIds = Mage::helper('configurableswatches')->getSwatchAttributeIds();
        }
        if ($listSwatchAttr->getId()) {
            $swatchAttributeIds[] = $listSwatchAttr->getId();
        }
        if (empty($swatchAttributeIds)) {
            return;
        }

        $parentProductIds = array();
        /* @var $parentProduct Mage_Catalog_Model_Product */
        foreach ($parentProducts as $parentProduct) {
            $parentProductIds[] = $parentProduct->getId();
        }

        $configAttributes = Mage::getResourceModel('configurableswatches/catalog_product_attribute_super_collection')
            ->addParentProductsFilter($parentProductIds)
            ->attachEavAttributes()
            ->addFieldToFilter('eav_attributes.attribute_id', array('in' => $swatchAttributeIds))
            ->setStoreId($storeId)
        ;

        $optionLabels = array();
        foreach ($configAttributes as $attribute) {
            $optionLabels += $attribute->getOptionLabels();
        }

        // normalize to all lower case before we start using them
        $optionLabels = array_map(function ($value) {
            return array_map('Mage_ConfigurableSwatches_Helper_Data::normalizeKey', $value);
        }, $optionLabels);

        foreach ($parentProducts as $parentProduct) {
            $mapping = array();
            $listSwatchValues = array();
            $listSwatchStockValues = array();

            /* @var $attribute Mage_Catalog_Model_Product_Type_Configurable_Attribute */
            foreach ($configAttributes as $attribute) {
                /* @var $childProduct Mage_Catalog_Model_Product */
                if (!is_array($parentProduct->getChildrenProducts())) {
                    continue;
                }

                foreach ($parentProduct->getChildrenProducts() as $childProduct) {

                    // product has no value for attribute or not available, we can't process it
                    $isInStock = $childProduct->getStockItem()->getIsInStock();
                    if (!$childProduct->hasData($attribute->getAttributeCode())
                        || (!$isInStock && !Mage::helper('cataloginventory')->isShowOutOfStock())) {
                        continue;
                    }
                    $optionId = $childProduct->getData($attribute->getAttributeCode());

                    // if we don't have a default label, skip it
                    if (!isset($optionLabels[$optionId][0])) {
                        continue;
                    }

                    // using default value as key unless store-specific label is present
                    $optionLabel = $optionLabels[$optionId][0];
                    if (isset($optionLabels[$optionId][$storeId])) {
                        $optionLabel = $optionLabels[$optionId][$storeId];
                    }

                    // initialize arrays if not present
                    if (!isset($mapping[$optionLabel])) {
                        $mapping[$optionLabel] = array(
                            'product_ids' => array(),
                        );
                    }
                    $mapping[$optionLabel]['product_ids'][] = $childProduct->getId();
                    $mapping[$optionLabel]['label'] = $optionLabel;
                    $mapping[$optionLabel]['default_label'] = $optionLabels[$optionId][0];
                    $mapping[$optionLabel]['labels'] = $optionLabels[$optionId];

                    if ($attribute->getAttributeId() == $listSwatchAttr->getAttributeId()
                        && !in_array($mapping[$optionLabel]['label'], $listSwatchValues)
                    ) {
                        $listSwatchValues[$optionId]      = $mapping[$optionLabel]['label'];
                        $listSwatchStockValues[$optionId] = $isInStock;
                    }
                } // end looping child products
            } // end looping attributes


            foreach ($mapping as $key => $value) {
                $mapping[$key]['product_ids'] = array_unique($mapping[$key]['product_ids']);
            }

            if (count($listSwatchValues)) {
                $listSwatchValues = array_replace(array_intersect_key($optionLabels, $listSwatchValues),
                    $listSwatchValues);
            }
            $parentProduct->setChildAttributeLabelMapping($mapping)
                ->setListSwatchAttrValues($listSwatchValues)
                ->setListSwatchAttrStockValues($listSwatchStockValues);
        } // end looping parent products
    }

    /**
     * For given product, get configurable images fallback array
     * Depends on following data available on product:
     * - product must have child attribute label mapping attached
     * - product must have media gallery attached which attaches and differentiates local images and child images
     * - product must have child products attached
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $imageTypes - image types to select for child products
     * @return array
     */
    public function getConfigurableImagesFallbackArray(Mage_Catalog_Model_Product $product, array $imageTypes,
        $keepFrame = false
    ) {
        if (!$product->hasConfigurableImagesFallbackArray()) {
            $mapping = $product->getChildAttributeLabelMapping();

            $mediaGallery = $product->getMediaGallery();

            if (!isset($mediaGallery['images'])) {
                return array(); //nothing to do here
            }

            // ensure we only attempt to process valid image types we know about
            $imageTypes = array_intersect(array('image', 'small_image'), $imageTypes);

            $imagesByLabel = array();
            $imageHaystack = array_map(function ($value) {
                return Mage_ConfigurableSwatches_Helper_Data::normalizeKey($value['label']);
            }, $mediaGallery['images']);

            // load images from the configurable product for swapping
            foreach ($mapping as $map) {
                $imagePath = null;

                //search by store-specific label and then default label if nothing is found
                $imageKey = array_search($map['label'], $imageHaystack);
                if ($imageKey === false) {
                    $imageKey = array_search($map['default_label'], $imageHaystack);
                }

                //assign proper image file if found
                if ($imageKey !== false) {
                    $imagePath = $mediaGallery['images'][$imageKey]['file'];
                }

                $imagesByLabel[$map['label']] = array(
                    'configurable_product' => array(
                        Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_SMALL => null,
                        Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE => null,
                    ),
                    'products' => $map['product_ids'],
                );

                if ($imagePath) {
                    $imagesByLabel[$map['label']]['configurable_product']
                        [Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_SMALL] =
                            $this->_resizeProductImage($product, 'small_image', $keepFrame, $imagePath);

                    $imagesByLabel[$map['label']]['configurable_product']
                        [Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE] =
                            $this->_resizeProductImage($product, 'image', $keepFrame, $imagePath);
                }
            }

            $imagesByType = array(
                'image' => array(),
                'small_image' => array(),
            );

            // iterate image types to build image array, normally one type is passed in at a time, but could be two
            foreach ($imageTypes as $imageType) {
                // load image from the configurable product's children for swapping
                /* @var $childProduct Mage_Catalog_Model_Product */
                if ($product->hasChildrenProducts()) {
                    foreach ($product->getChildrenProducts() as $childProduct) {
                        $image = $this->_resizeProductImage($childProduct, $imageType, $keepFrame);
                        if (!$image) {
                            $image = $this->_resizeProductImage($childProduct, 'image', $keepFrame);
                        }

                        if ($image) {
                            $imagesByType[$imageType][$childProduct->getId()] = $image;
                        }
                    }
                }

                // load image from configurable product for swapping fallback
                if ($image = $this->_resizeProductImage($product, $imageType, $keepFrame, null, true)) {
                    $imagesByType[$imageType][$product->getId()] = $image;
                }
            }

            $array = array(
                'option_labels' => $imagesByLabel,
                Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_SMALL => $imagesByType['small_image'],
                Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE => $imagesByType['image'],
            );

            $product->setConfigurableImagesFallbackArray($array);
        }

        return $product->getConfigurableImagesFallbackArray();
    }

    /**
     * Resize specified type of image on the product for use in the fallback and returns the image URL
     * or returns the image URL for the specified image path if present
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $type
     * @param bool $keepFrame
     * @param string $image
     * @param bool $placeholder
     * @return string|bool
     */
    protected function _resizeProductImage($product, $type, $keepFrame, $image = null, $placeholder = false)
    {
        $hasTypeData = $product->hasData($type) && $product->getData($type) != 'no_selection';
        if ($image == 'no_selection') {
            $image = null;
        }
        if ($hasTypeData || $placeholder || $image) {
            $helper = Mage::helper('catalog/image')
                ->init($product, $type, $image)
                ->keepFrame(($hasTypeData || $image) ? $keepFrame : false)  // don't keep frame if placeholder
            ;

            $size = Mage::getStoreConfig(Mage_Catalog_Helper_Image::XML_NODE_PRODUCT_BASE_IMAGE_WIDTH);
            if ($type == 'small_image') {
                $size = Mage::getStoreConfig(Mage_Catalog_Helper_Image::XML_NODE_PRODUCT_SMALL_IMAGE_WIDTH);
            }
            if (is_numeric($size)) {
                $helper->constrainOnly(true)->resize($size);
            }
            return (string)$helper;
        }
        return false;
    }

    /**
     * Groups media gallery images by local images and child images
     *
     * @param Mage_Catalog_Model_Product $product
     * @return void
     */
    public function groupMediaGalleryImages(Mage_Catalog_Model_Product $product)
    {
        $mediaGallery = $product->getMediaGallery();

        if (empty($mediaGallery['images'])) {
            return; //nothing to do here
        }

        $newMediaGalleryImages = array();
        $configurableImages = array();

        foreach ($mediaGallery['images'] as $mediaGalleryImage) {
            if ($mediaGalleryImage['product_id'] == $product->getId()) {
                $newMediaGalleryImages[] = $mediaGalleryImage;
            } else {
                $configurableImages[] = $mediaGalleryImage;
            }
        }

        $mediaGallery['images'] = $newMediaGalleryImages;
        $mediaGallery['configurable_images'] = $configurableImages;

        $product->setMediaGallery($mediaGallery); //reset product media images based on new grouping
    }

    /**
     * For given product set, attach media_gallery attribute values.
     *
     * @param array $products
     * @param int $storeId
     * @return void
     */
    public function attachGallerySetToCollection(array $products, $storeId)
    {
        $productIds = array();
        /* @var $product Mage_Catalog_Model_Product */
        foreach ($products as $product) {
            $productIds[] = $product->getId();
            if (!is_array($product->getChildrenProducts())) {
                continue;
            }
            /* @var $childProduct Mage_Catalog_Model_Product */
            foreach ($product->getChildrenProducts() as $childProduct) {
                $productIds[] = $childProduct->getId();
            }
        }

        $attrCode = self::MEDIA_GALLERY_ATTRIBUTE_CODE;

        /* @var $resourceModel Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media */
        $resourceModel = Mage::getResourceModel('catalog/product_attribute_backend_media');

        $images = $resourceModel->loadGallerySet($productIds, $storeId);

        $relationship = array();
        foreach ($products as $product) {
            $relationship[$product->getId()] = $product->getId();

            if (!is_array($product->getChildrenProducts())) {
                continue;
            }

            /* @var $childProduct Mage_Catalog_Model_Product */
            foreach ($product->getChildrenProducts() as $childProduct) {
                $relationship[$childProduct->getId()] = $product->getId();
            }
        }

        foreach ($images as $image) {
            $productId = $image['product_id'];
            $realProductId = $relationship[$productId];
            $product = $products[$realProductId];

            if (is_null($image['label'])) {
                $image['label'] = $image['label_default'];
            }
            if (is_null($image['position'])) {
                $image['position'] = $image['position_default'];
            }
            if (is_null($image['disabled'])) {
                $image['disabled'] = $image['disabled_default'];
            }

            $value = $product->getData($attrCode);
            if (!$value) {
                $value = array(
                    'images' => array(),
                    'value' => array()
                );
            }

            $value['images'][] = $image;

            $product->setData($attrCode, $value);
        }
    }

    /**
     * Determines which product attributes should be selected
     * when children products are attached to parent products
     *
     * @return array
     */
    protected function _getChildrenProductsAttributes()
    {
        return array(
            'small_image',
            'image',
            'image_label',
            'small_image_label',
            Mage::helper('configurableswatches/productlist')->getSwatchAttribute()->getAttributeCode(),
        );
    }

    /**
     * Attaches children product to each product via
     * ->setChildrenProducts()
     *
     * @param array $products
     * @param int $storeId
     * @return void
     */
    public function attachChildrenProducts(array $products, $storeId)
    {
        $productIds = array();
        /* @var $product Mage_Catalog_Model_Product */
        foreach ($products as $product) {
            $productIds[] = $product->getId();
        }

        $collection = Mage::getResourceModel(
            'configurableswatches/catalog_product_type_configurable_product_collection');

        $collection->setFlag('product_children', true)
            ->addStoreFilter($storeId)
            ->addAttributeToSelect($this->_getChildrenProductsAttributes());
        $collection->addProductSetFilter($productIds);

        $collection->load();

        $mapping = array();
        /* @var $childProduct Mage_Catalog_Model_Product */
        foreach ($collection as $childProduct) {
            foreach ($childProduct->getParentIds() as $parentId) {
                if (!isset($mapping[$parentId])) {
                    $mapping[$parentId] = array();
                }
                $mapping[$parentId][] = $childProduct;
            }
        }

        foreach ($mapping as $parentId => $childrenProducts) {
            $products[$parentId]->setChildrenProducts($childrenProducts);
        }
    }
}

<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class implementing the media fallback layer for swatches
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_ConfigurableSwatches_Helper_Mediafallback extends Mage_Core_Helper_Abstract
{
    public const MEDIA_GALLERY_ATTRIBUTE_CODE = 'media_gallery';

    protected $_moduleName = 'Mage_ConfigurableSwatches';

    /**
     * Set child_attribute_label_mapping on products with attribute label -> product mapping
     * Depends on following product data:
     * - product must have children products attached
     *
     * @param array $parentProducts
     * @deprecated use $this->attachProductChildrenAttributeMapping() instead
     * @param int $storeId
     */
    public function attachConfigurableProductChildrenAttributeMapping(array $parentProducts, $storeId)
    {
        $this->attachProductChildrenAttributeMapping($parentProducts, $storeId);
    }

    /**
     * Set child_attribute_label_mapping on products with attribute label -> product mapping
     * Depends on following product data:
     * - product must have children products attached
     *
     * @param array $parentProducts
     * @param int $storeId
     * @param bool $onlyListAttributes
     */
    public function attachProductChildrenAttributeMapping(array $parentProducts, $storeId, $onlyListAttributes = false)
    {
        /** @var  Mage_Eav_Model_Attribute $listSwatchAttr */
        $listSwatchAttr = Mage::helper('configurableswatches/productlist')->getSwatchAttribute();
        $swatchAttributeIds = [];
        if (!$onlyListAttributes) {
            $swatchAttributeIds = Mage::helper('configurableswatches')->getSwatchAttributeIds();
        }
        if ($listSwatchAttr->getId()) {
            $swatchAttributeIds[] = $listSwatchAttr->getId();
        }
        if (empty($swatchAttributeIds)) {
            return;
        }

        $parentProductIds = [];
        /** @var Mage_Catalog_Model_Product $parentProduct */
        foreach ($parentProducts as $parentProduct) {
            $parentProductIds[] = $parentProduct->getId();
        }

        $configAttributes = Mage::getResourceModel('configurableswatches/catalog_product_attribute_super_collection')
            ->addParentProductsFilter($parentProductIds)
            ->attachEavAttributes()
            ->addFieldToFilter('eav_attributes.attribute_id', ['in' => $swatchAttributeIds])
            ->setStoreId($storeId)
        ;

        $optionLabels = [];
        foreach ($configAttributes as $attribute) {
            $optionLabels += $attribute->getOptionLabels();
        }

        // normalize to all lower case before we start using them
        $optionLabels = array_map(function ($value) {
            return array_map('Mage_ConfigurableSwatches_Helper_Data::normalizeKey', $value);
        }, $optionLabels);

        foreach ($parentProducts as $parentProduct) {
            $mapping = [];
            $listSwatchValues = [];
            $listSwatchStockValues = [];

            /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute */
            foreach ($configAttributes as $attribute) {
                /** @var Mage_Catalog_Model_Product $childProduct */
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
                    $optionLabel = $optionLabels[$optionId][$storeId] ?? $optionLabels[$optionId][0];

                    // initialize arrays if not present
                    if (!isset($mapping[$optionLabel])) {
                        $mapping[$optionLabel] = [
                            'product_ids' => [],
                        ];
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
                $listSwatchValues = array_replace(
                    array_intersect_key($optionLabels, $listSwatchValues),
                    $listSwatchValues
                );
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
     * @param bool $keepFrame
     * @return array
     */
    public function getConfigurableImagesFallbackArray(
        Mage_Catalog_Model_Product $product,
        array $imageTypes,
        $keepFrame = false
    ) {
        if (!$product->hasConfigurableImagesFallbackArray()) {
            $mapping = $product->getChildAttributeLabelMapping();

            $mediaGallery = $product->getMediaGallery();

            if (!isset($mediaGallery['images'])) {
                return []; //nothing to do here
            }

            // ensure we only attempt to process valid image types we know about
            $imageTypes = array_intersect(['image', 'small_image'], $imageTypes);

            $imagesByLabel = [];
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

                $imagesByLabel[$map['label']] = [
                    'configurable_product' => [
                        Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_SMALL => null,
                        Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE => null,
                    ],
                    'products' => $map['product_ids'],
                ];

                if ($imagePath) {
                    $imagesByLabel[$map['label']]['configurable_product']
                        [Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_SMALL] =
                            $this->_resizeProductImage($product, 'small_image', $keepFrame, $imagePath);

                    $imagesByLabel[$map['label']]['configurable_product']
                        [Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE] =
                            $this->_resizeProductImage($product, 'image', $keepFrame, $imagePath);
                }
            }

            $imagesByType = [
                'image' => [],
                'small_image' => [],
            ];

            // iterate image types to build image array, normally one type is passed in at a time, but could be two
            foreach ($imageTypes as $imageType) {
                // load image from the configurable product's children for swapping
                /** @var Mage_Catalog_Model_Product $childProduct */
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

            $array = [
                'option_labels' => $imagesByLabel,
                Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_SMALL => $imagesByType['small_image'],
                Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE => $imagesByType['image'],
            ];

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
     */
    public function groupMediaGalleryImages(Mage_Catalog_Model_Product $product)
    {
        $mediaGallery = $product->getMediaGallery();

        if (empty($mediaGallery['images'])) {
            return; //nothing to do here
        }

        $newMediaGalleryImages = [];
        $configurableImages = [];
        $productId = $product->getId();

        foreach ($mediaGallery['images'] as $mediaGalleryImage) {
            if ($mediaGalleryImage['product_id'] == $productId) {
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
     */
    public function attachGallerySetToCollection(array $products, $storeId)
    {
        $productIds = [];
        /** @var Mage_Catalog_Model_Product $product */
        foreach ($products as $product) {
            $productIds[] = $product->getId();
            if (!is_array($product->getChildrenProducts())) {
                continue;
            }
            foreach ($product->getChildrenProducts() as $childProduct) {
                $productIds[] = $childProduct->getId();
            }
        }

        $attrCode = self::MEDIA_GALLERY_ATTRIBUTE_CODE;

        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media $resourceModel */
        $resourceModel = Mage::getResourceModel('catalog/product_attribute_backend_media');

        $images = $resourceModel->loadGallerySet($productIds, $storeId);

        $relationship = [];
        foreach ($products as $product) {
            $productId = $product->getId();
            $relationship[$productId] = $productId;

            if (!is_array($product->getChildrenProducts())) {
                continue;
            }

            foreach ($product->getChildrenProducts() as $childProduct) {
                $relationship[$childProduct->getId()] = $productId;
            }
        }

        foreach ($images as $image) {
            $realProductId = $relationship[$image['product_id']];
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
                $value = [
                    'images' => [],
                    'value' => []
                ];
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
        return [
            'small_image',
            'image',
            'image_label',
            'small_image_label',
            Mage::helper('configurableswatches/productlist')->getSwatchAttribute()->getAttributeCode(),
        ];
    }

    /**
     * Attaches children product to each product via
     * ->setChildrenProducts()
     *
     * @param array $products
     * @param int $storeId
     */
    public function attachChildrenProducts(array $products, $storeId)
    {
        $productIds = [];
        /** @var Mage_Catalog_Model_Product $product */
        foreach ($products as $product) {
            $productIds[] = $product->getId();
        }

        $collection = Mage::getResourceModel(
            'configurableswatches/catalog_product_type_configurable_product_collection'
        );

        $collection->setFlag('product_children', true)
            ->addStoreFilter($storeId)
            ->addAttributeToSelect($this->_getChildrenProductsAttributes());
        $collection->addProductSetFilter($productIds);

        $collection->load();

        $mapping = [];
        /** @var Mage_Catalog_Model_Product $childProduct */
        foreach ($collection as $childProduct) {
            foreach ($childProduct->getParentIds() as $parentId) {
                if (!isset($mapping[$parentId])) {
                    $mapping[$parentId] = [];
                }
                $mapping[$parentId][] = $childProduct;
            }
        }

        foreach ($mapping as $parentId => $childrenProducts) {
            $products[$parentId]->setChildrenProducts($childrenProducts);
        }
    }
}

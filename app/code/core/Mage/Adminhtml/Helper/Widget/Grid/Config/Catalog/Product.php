<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Catalog Grid Config Advanced Helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Helper_Widget_Grid_Config_Catalog_Product extends Mage_Adminhtml_Helper_Widget_Grid_Config_Abstract
{
    public const CONFIG_PATH_GRID_COLUMNS = 'advanced_grid/%s/columns';
    public const CONFIG_PATH_GRID_CREATED_AT = 'advanced_grid/%s/created_at';
    public const CONFIG_PATH_GRID_UPDATED_AT = 'advanced_grid/%s/updated_at';
    public const CONFIG_PATH_GRID_COLUMN_IMAGE = 'advanced_grid/%s/media_image';
    public const CONFIG_PATH_GRID_COLUMN_IMAGE_WIDTH = 'advanced_grid/%s/media_image_width';

    /**
     * Collection object
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     *
     * return $this
     */
    public function applyAdvancedGridCollection($collection)
    {
        if (!$this->isEnabled()) {
            return $this;
        }

        if ($collection) {
            foreach ($this->getAttributeColumns() as $attributeCode) {
                $collection->addAttributeToSelect($attributeCode);
            }

            foreach ($this->getImageColumns() as $attributeCode) {
                $collection->addAttributeToSelect($attributeCode);
            }

            if ($this->isCreatedAtEnabled()) {
                $collection->addAttributeToSelect('created_at');
            }

            if ($this->isUpdatedAtEnabled()) {
                $collection->addAttributeToSelect('updated_at');
            }
        }

        return $this;
    }

    /**
     * Adminhtml grid widget block
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
     *
     * return $this
     */
    public function applyAdvancedGridColumn($gridBlock)
    {
        $storeId = (int) $gridBlock->getRequest()->getParam('store', 0);
        $_keepDefaultOrder = 'entity_id';

        foreach ($this->getImageColumns() as $attributeCode) {
            /** @var Mage_Eav_Model_Attribute $_attributeEntity */
            $_attributeEntity = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
            $gridBlock->addColumnAfter(
                $attributeCode,
                [
                    'header' => Mage::helper('catalog')->__($_attributeEntity->getFrontendLabel()),
                    'width' => $this->getProductImageWidth(),
                    'type'  => 'productimage',
                    'index' => $attributeCode,
                    'attribute_code' => $attributeCode,
                ],
                $_keepDefaultOrder
            );
            $_keepDefaultOrder = $attributeCode;
        }

        if ($this->isCreatedAtEnabled()) {
            $gridBlock->addColumnAfter(
                'created_at',
                [
                    'header' => Mage::helper('catalog')->__('Created At'),
                    'type'  => 'datetime',
                    'index' => 'created_at',
                    'attribute_code' => 'created_at',
                ],
                $_keepDefaultOrder
            );
            $_keepDefaultOrder = 'created_at';
        }

        if ($this->isUpdatedAtEnabled()) {
            $gridBlock->addColumnAfter(
                'updated_at',
                [
                    'header' => Mage::helper('catalog')->__('Updated At'),
                    'type'  => 'datetime',
                    'index' => 'updated_at',
                    'attribute_code' => 'updated_at',
                ],
                $_keepDefaultOrder
            );
            $_keepDefaultOrder = 'updated_at';
        }

        foreach ($this->getAttributeColumns() as $attributeCode) {
            /** @var Mage_Eav_Model_Entity_Attribute $_attributeEntity */
            $_attributeEntity = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
            switch ($_attributeEntity->getFrontendInput()) {
                case 'price':
                    $_currency = Mage::app()->getStore($storeId)->getBaseCurrency()->getCode();
                    $gridBlock->addColumnAfter(
                        $attributeCode,
                        [
                            'header' => Mage::helper('catalog')->__($_attributeEntity->getFrontendLabel()),
                            'type'  => 'price',
                            'index' => $attributeCode,
                            'attribute_code' => $attributeCode,
                            'currency_code' => $_currency,
                        ],
                        $_keepDefaultOrder
                    );
                    break;
                case 'date':
                    $gridBlock->addColumnAfter(
                        $attributeCode,
                        [
                            'header' => Mage::helper('catalog')->__($_attributeEntity->getFrontendLabel()),
                            'type'  => 'date',
                            'index' => $attributeCode,
                            'attribute_code' => $attributeCode,
                        ],
                        $_keepDefaultOrder
                    );
                    break;
                case 'multiselect':
                case 'select':
                    $_options = [];
                    if ($_attributeEntity->usesSource()) {
                        /** @var Mage_Eav_Model_Entity_Attribute_Source_Table $_sourceModel */
                        $_sourceModel = $_attributeEntity->getSource();
                        $_allOptions = $_sourceModel->getAllOptions(false, true);
                        foreach ($_allOptions as $key => $option) {
                            $_options[$option['value']] = $option['label'];
                        }
                    }
                    $gridBlock->addColumnAfter(
                        $attributeCode,
                        [
                            'header' => Mage::helper('catalog')->__($_attributeEntity->getFrontendLabel()),
                            /* 'width' => '150px', */
                            'type'  => 'options',
                            'index' => $attributeCode,
                            'options' => $_options,
                            'attribute_code' => $attributeCode,
                        ],
                        $_keepDefaultOrder
                    );
                    break;
                default:
                    $gridBlock->addColumnAfter(
                        $attributeCode,
                        [
                            'header' => Mage::helper('catalog')->__($_attributeEntity->getFrontendLabel()),
                            'type'  => 'text',
                            'index' => $attributeCode,
                            'attribute_code' => $attributeCode,
                        ],
                        $_keepDefaultOrder
                    );
                    break;
            }
            $_keepDefaultOrder = $attributeCode;
        }
    }

    /**
     * Get column created_at is enabled
     *
     * @return bool
     */
    public function isCreatedAtEnabled(): bool
    {
        return $this->getStoreConfigGridId(self::CONFIG_PATH_GRID_CREATED_AT);
    }

    /**
     * Get column updated_at is enabled
     *
     * @return bool
     */
    public function isUpdatedAtEnabled(): bool
    {
        return $this->getStoreConfigGridId(self::CONFIG_PATH_GRID_UPDATED_AT);
    }

    /**
     * Get grid enabled for custom columns
     *
     * @return array
     */
    public function getAttributeColumns(): array
    {
        if (!$this->getStoreConfigGridId(self::CONFIG_PATH_GRID_COLUMNS)) {
            return [];
        }
        return explode(',', $this->getStoreConfigGridId(self::CONFIG_PATH_GRID_COLUMNS));
    }

    /**
     * Get grid enabled for custom columns
     *
     * @return array
     */
    public function getImageColumns(): array
    {
        if (!$this->getStoreConfigGridId(self::CONFIG_PATH_GRID_COLUMN_IMAGE)) {
            return [];
        }
        return explode(',', $this->getStoreConfigGridId(self::CONFIG_PATH_GRID_COLUMN_IMAGE));
    }

    /**
     * Get media product image width
     *
     * @return string
     */
    public function getProductImageWidth(): string
    {
        return $this->getStoreConfigGridId(self::CONFIG_PATH_GRID_COLUMN_IMAGE_WIDTH);
    }
}

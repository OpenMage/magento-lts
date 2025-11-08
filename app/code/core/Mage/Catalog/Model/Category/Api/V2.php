<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog category api
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Category_Api_V2 extends Mage_Catalog_Model_Category_Api
{
    /**
     * Retrieve category data
     *
     * @param int $categoryId
     * @param int|string $store
     * @param array $attributes
     * @return array
     */
    public function info($categoryId, $store = null, $attributes = null)
    {
        $category = $this->_initCategory($categoryId, $store);

        // Basic category data
        $result = [];
        $result['category_id'] = $category->getId();

        $result['is_active']   = $category->getIsActive();
        $result['position']    = $category->getPosition();
        $result['level']       = $category->getLevel();

        foreach ($category->getAttributes() as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $attributes)) {
                $result[$attribute->getAttributeCode()] = $category->getDataUsingMethod($attribute->getAttributeCode());
            }
        }

        $result['parent_id']   = $category->getParentId();
        $result['children']           = $category->getChildren();
        $result['all_children']       = $category->getAllChildren();

        return $result;
    }

    /**
     * Create new category
     *
     * @param int $parentId
     * @param array $categoryData
     * @param null|int|string $store
     * @return int
     * @throws Mage_Api_Exception
     * @throws Mage_Core_Exception
     * @throws Mage_Eav_Model_Entity_Attribute_Exception
     */
    public function create($parentId, $categoryData, $store = null)
    {
        $parentCategory = $this->_initCategory($parentId, $store);

        /** @var Mage_Catalog_Model_Category $category */
        $category = Mage::getModel('catalog/category')
            ->setStoreId($this->_getStoreId($store));

        $category->addData(['path' => implode('/', $parentCategory->getPathIds())]);

        $category ->setAttributeSetId($category->getDefaultAttributeSetId());

        foreach ($category->getAttributes() as $attribute) {
            $_attrCode = $attribute->getAttributeCode();
            if ($this->_isAllowedAttribute($attribute)
                && isset($categoryData->$_attrCode)
            ) {
                $category->setData(
                    $attribute->getAttributeCode(),
                    $categoryData->$_attrCode,
                );
            }
        }

        $category->setParentId($parentCategory->getId());
        try {
            $validate = $category->validate();
            if ($validate !== true) {
                foreach ($validate as $code => $error) {
                    if ($error === true) {
                        Mage::throwException(Mage::helper('catalog')->__('Attribute "%s" is required.', $code));
                    } else {
                        Mage::throwException($error);
                    }
                }
            }

            $category->save();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('data_invalid', $mageCoreException->getMessage());
        }

        return $category->getId();
    }

    /**
     * Update category data
     *
     * @param int $categoryId
     * @param array $categoryData
     * @param int|string $store
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function update($categoryId, $categoryData, $store = null)
    {
        $category = $this->_initCategory($categoryId, $store);

        foreach ($category->getAttributes() as $attribute) {
            $_attrCode = $attribute->getAttributeCode();
            if ($this->_isAllowedAttribute($attribute)
                && isset($categoryData->$_attrCode)
            ) {
                $category->setData(
                    $attribute->getAttributeCode(),
                    $categoryData->$_attrCode,
                );
            }
        }

        try {
            $validate = $category->validate();
            if ($validate !== true) {
                foreach ($validate as $code => $error) {
                    if ($error === true) {
                        Mage::throwException(Mage::helper('catalog')->__('Attribute "%s" is required.', $code));
                    } else {
                        Mage::throwException($error);
                    }
                }
            }

            $category->save();
        } catch (Mage_Core_Exception|Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }
}

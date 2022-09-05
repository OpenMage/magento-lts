<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product custom option resource model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product_Option_Value extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table and initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_option_type_value', 'option_type_id');
    }

    /**
     * Proceeed operations after object is saved
     * Save options store data
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_saveValuePrices($object);
        $this->_saveValueTitles($object);

        return parent::_afterSave($object);
    }

    /**
     * Save option value price data
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _saveValuePrices(Mage_Core_Model_Abstract $object)
    {
        $priceTable = $this->getTable('catalog/product_option_type_price');

        $price      = (float)sprintf('%F', $object->getPrice());
        $priceType  = $object->getPriceType();

        if (!$object->getData('scope', 'price')) {
            //save for store_id = 0
            $select = $this->_getReadAdapter()->select()
                ->from($priceTable, 'option_type_id')
                ->where('option_type_id = ?', (int)$object->getId())
                ->where('store_id = ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
            $optionTypeId = $this->_getReadAdapter()->fetchOne($select);

            if ($optionTypeId) {
                if ($object->getStoreId() == '0') {
                    $bind  = [
                        'price'         => $price,
                        'price_type'    => $priceType
                    ];
                    $where = [
                        'option_type_id = ?'    => $optionTypeId,
                        'store_id = ?'          => Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID
                    ];

                    $this->_getWriteAdapter()->update($priceTable, $bind, $where);
                }
            } else {
                $bind  = [
                    'option_type_id'    => (int)$object->getId(),
                    'store_id'          => Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID,
                    'price'             => $price,
                    'price_type'        => $priceType
                ];
                $this->_getWriteAdapter()->insert($priceTable, $bind);
            }
        }

        $scope = (int)Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);

        if ($object->getStoreId() != '0' && $scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE
            && !$object->getData('scope', 'price')) {
            $baseCurrency = Mage::app()->getBaseCurrencyCode();

            $storeIds = Mage::app()->getStore($object->getStoreId())
                ->getWebsite()
                ->getStoreIds();
            if (is_array($storeIds)) {
                foreach ($storeIds as $storeId) {
                    if ($priceType == 'fixed') {
                        $storeCurrency = Mage::app()->getStore($storeId)->getBaseCurrencyCode();
                        $rate = Mage::getModel('directory/currency')->load($baseCurrency)->getRate($storeCurrency);
                        if (!$rate) {
                            $rate = 1;
                        }
                        $newPrice = $price * $rate;
                    } else {
                        $newPrice = $price;
                    }

                    $select = $this->_getReadAdapter()->select()
                        ->from($priceTable, 'option_type_id')
                        ->where('option_type_id = ?', (int)$object->getId())
                        ->where('store_id = ?', (int)$storeId);
                    $optionTypeId = $this->_getReadAdapter()->fetchOne($select);

                    if ($optionTypeId) {
                        $bind  = [
                            'price'         => $newPrice,
                            'price_type'    => $priceType
                        ];
                        $where = [
                            'option_type_id = ?'    => (int)$optionTypeId,
                            'store_id = ?'          => (int)$storeId
                        ];

                        $this->_getWriteAdapter()->update($priceTable, $bind, $where);
                    } else {
                        $bind  = [
                            'option_type_id'    => (int)$object->getId(),
                            'store_id'          => (int)$storeId,
                            'price'             => $newPrice,
                            'price_type'        => $priceType
                        ];

                        $this->_getWriteAdapter()->insert($priceTable, $bind);
                    }
                }// end of foreach()
            }
        } elseif ($scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE && $object->getData('scope', 'price')) {
            $where = [
                'option_type_id = ?'    => (int)$object->getId(),
                'store_id = ?'          => (int)$object->getStoreId(),
            ];
            $this->_getWriteAdapter()->delete($priceTable, $where);
        }
    }

    /**
     * Save option value title data
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _saveValueTitles(Mage_Core_Model_Abstract $object)
    {
        $titleTable = $this->getTable('catalog/product_option_type_title');

        if (!$object->getData('scope', 'title')) {
            $select = $this->_getReadAdapter()->select()
                ->from($titleTable, ['option_type_id'])
                ->where('option_type_id = ?', (int)$object->getId())
                ->where('store_id = ?', 0);
            $optionTypeId = $this->_getReadAdapter()->fetchOne($select);

            if ($optionTypeId) {
                if ($object->getStoreId() == '0') {
                    $where = [
                        'option_type_id = ?'    => (int)$optionTypeId,
                        'store_id = ?'          => Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID
                    ];
                    $bind  = [
                        'title' => $object->getTitle()
                    ];
                    $this->_getWriteAdapter()->update($titleTable, $bind, $where);
                }
            } else {
                $bind  = [
                    'option_type_id'    => (int)$object->getId(),
                    'store_id'          => Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID,
                    'title'             => $object->getTitle()
                ];
                $this->_getWriteAdapter()->insert($titleTable, $bind);
            }
        }

        if ($object->getStoreId() != '0' && !$object->getData('scope', 'title')) {
            $select = $this->_getReadAdapter()->select()
                ->from($titleTable, ['option_type_id'])
                ->where('option_type_id = ?', (int)$object->getId())
                ->where('store_id = ?', (int)$object->getStoreId());
            $optionTypeId = $this->_getReadAdapter()->fetchOne($select);

            if ($optionTypeId) {
                $bind  = [
                    'title' => $object->getTitle()
                ];
                $where = [
                    'option_type_id = ?'    => (int)$optionTypeId,
                    'store_id = ?'          => (int)$object->getStoreId()
                ];
                $this->_getWriteAdapter()->update($titleTable, $bind, $where);
            } else {
                $bind  = [
                    'option_type_id'    => (int)$object->getId(),
                    'store_id'          => (int)$object->getStoreId(),
                    'title'             => $object->getTitle()
                ];
                $this->_getWriteAdapter()->insert($titleTable, $bind);
            }
        } elseif ($object->getData('scope', 'title')) {
            $where = [
                'option_type_id = ?'    => (int)$object->getId(),
                'store_id = ?'          => (int)$object->getStoreId()
            ];
            $this->_getWriteAdapter()->delete($titleTable, $where);
        }
    }

    /**
     * Delete values by option id
     *
     * @param int $optionId
     * @return $this
     */
    public function deleteValue($optionId)
    {
        $statement = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/product_option_type_value'))
            ->where('option_id = ?', $optionId);

        $rowSet = $this->_getReadAdapter()->fetchAll($statement);

        foreach ($rowSet as $optionType) {
            $this->deleteValues($optionType['option_type_id']);
        }

        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            [
                'option_id = ?' => $optionId,
            ]
        );

        return $this;
    }

    /**
     * Delete values by option type
     *
     * @param int $optionTypeId
     */
    public function deleteValues($optionTypeId)
    {
        $condition = [
            'option_type_id = ?' => $optionTypeId
        ];

        $this->_getWriteAdapter()->delete(
            $this->getTable('catalog/product_option_type_price'),
            $condition
        );

        $this->_getWriteAdapter()->delete(
            $this->getTable('catalog/product_option_type_title'),
            $condition
        );
    }

    /**
     * Duplicate product options value
     *
     * @param Mage_Catalog_Model_Product_Option_Value $object
     * @param int $oldOptionId
     * @param int $newOptionId
     * @return Mage_Catalog_Model_Product_Option_Value
     */
    public function duplicate(Mage_Catalog_Model_Product_Option_Value $object, $oldOptionId, $newOptionId)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $readAdapter  = $this->_getReadAdapter();
        $select       = $readAdapter->select()
            ->from($this->getMainTable())
            ->where('option_id = ?', $oldOptionId);
        $valueData = $readAdapter->fetchAll($select);

        $valueCond = [];

        foreach ($valueData as $data) {
            $optionTypeId = $data[$this->getIdFieldName()];
            unset($data[$this->getIdFieldName()]);
            $data['option_id'] = $newOptionId;

            $writeAdapter->insert($this->getMainTable(), $data);
            $valueCond[$optionTypeId] = $writeAdapter->lastInsertId($this->getMainTable());
        }

        unset($valueData);

        foreach ($valueCond as $oldTypeId => $newTypeId) {
            // price
            $priceTable = $this->getTable('catalog/product_option_type_price');
            $columns = [
                new Zend_Db_Expr($newTypeId),
                'store_id', 'price', 'price_type'
            ];

            $select = $readAdapter->select()
                ->from($priceTable, [])
                ->where('option_type_id = ?', $oldTypeId)
                ->columns($columns);
            $insertSelect = $writeAdapter->insertFromSelect(
                $select,
                $priceTable,
                ['option_type_id', 'store_id', 'price', 'price_type']
            );
            $writeAdapter->query($insertSelect);

            // title
            $titleTable = $this->getTable('catalog/product_option_type_title');
            $columns = [
                new Zend_Db_Expr($newTypeId),
                'store_id', 'title'
            ];

            $select = $this->_getReadAdapter()->select()
                ->from($titleTable, [])
                ->where('option_type_id = ?', $oldTypeId)
                ->columns($columns);
            $insertSelect = $writeAdapter->insertFromSelect(
                $select,
                $titleTable,
                ['option_type_id', 'store_id', 'title']
            );
            $writeAdapter->query($insertSelect);
        }

        return $object;
    }
}

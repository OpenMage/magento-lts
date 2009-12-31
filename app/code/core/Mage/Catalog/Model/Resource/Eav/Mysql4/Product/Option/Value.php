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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product custom option resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Value extends Mage_Core_Model_Mysql4_Abstract
{
    protected function  _construct()
    {
        $this->_init('catalog/product_option_type_value', 'option_type_id');
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $priceTable = $this->getTable('catalog/product_option_type_price');
        $titleTable = $this->getTable('catalog/product_option_type_title');

        if (!$object->getData('scope', 'price')) {
            //save for store_id = 0
            $statement = $this->_getReadAdapter()->select()
                ->from($priceTable)
                ->where('option_type_id = '.$object->getId().' AND store_id = ?', 0);
            if ($this->_getReadAdapter()->fetchOne($statement)) {
                if ($object->getStoreId() == '0') {
                    $this->_getWriteAdapter()->update(
                        $priceTable,
                        array(
                            'price' => $object->getPrice(),
                            'price_type' => $object->getPriceType()
                        ),
                        $this->_getWriteAdapter()->quoteInto('option_type_id = '.$object->getId().' AND store_id = ?', 0)
                    );
                }
            } else {
                $this->_getWriteAdapter()->insert(
                    $priceTable,
                    array(
                        'option_type_id' => $object->getId(),
                        'store_id' => 0,
                        'price' => $object->getPrice(),
                        'price_type' => $object->getPriceType()
                    )
                );
            }
        }

        $scope = (int) Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);

        if ($object->getStoreId() != '0' && $scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE
            && !$object->getData('scope', 'price')) {

            $baseCurrency = Mage::app()->getBaseCurrencyCode();

            $storeIds = $object->getProduct()->getStoreIds();
            if (is_array($storeIds)) {
                foreach ($storeIds as $storeId) {
                    if ($object->getPriceType() == 'fixed') {
                        $storeCurrency = Mage::app()->getStore($storeId)->getBaseCurrencyCode();
                        $rate = Mage::getModel('directory/currency')->load($baseCurrency)->getRate($storeCurrency);
                        if (!$rate) {
                            $rate = 1;
                        }
                        $newPrice = $object->getPrice() * $rate;
                    } else {
                        $newPrice = $object->getPrice();
                    }
                    $statement = $this->_getReadAdapter()->select()
                        ->from($priceTable)
                        ->where('option_type_id = '.$object->getId().' AND store_id = ?', $storeId);

                    if ($this->_getReadAdapter()->fetchOne($statement)) {
                        $this->_getWriteAdapter()->update(
                            $priceTable,
                            array(
                                'price' => $newPrice,
                                'price_type' => $object->getPriceType()
                            ),
                            $this->_getWriteAdapter()->quoteInto('option_type_id = '.$object->getId().' AND store_id = ?', $storeId)
                        );
                    } else {
                        $this->_getWriteAdapter()->insert(
                            $priceTable,
                            array(
                                'option_type_id' => $object->getId(),
                                'store_id' => $storeId,
                                'price' => $newPrice,
                                'price_type' => $object->getPriceType()
                            )
                        );
                    }
                }// end of foreach()
            }
        } elseif ($scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE && $object->getData('scope', 'price')) {
            $this->_getWriteAdapter()->delete(
                $priceTable,
                $this->_getWriteAdapter()->quoteInto('option_type_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
            );
        }

        //title
        if (!$object->getData('scope', 'title')) {
            $statement = $this->_getReadAdapter()->select()
                ->from($titleTable)
                ->where('option_type_id = '.$object->getId().' AND store_id = ?', 0);

            if ($this->_getReadAdapter()->fetchOne($statement)) {
                if ($object->getStoreId() == '0') {
                    $this->_getWriteAdapter()->update(
                        $titleTable,
                            array('title' => $object->getTitle()),
                            $this->_getWriteAdapter()->quoteInto('option_type_id='.$object->getId().' AND store_id=?', 0)
                    );
                }
            } else {
                $this->_getWriteAdapter()->insert(
                    $titleTable,
                        array(
                            'option_type_id' => $object->getId(),
                            'store_id' => 0,
                            'title' => $object->getTitle()
                ));
            }
        }

        if ($object->getStoreId() != '0' && !$object->getData('scope', 'title')) {
            $statement = $this->_getReadAdapter()->select()
                ->from($titleTable)
                ->where('option_type_id = '.$object->getId().' AND store_id = ?', $object->getStoreId());

            if ($this->_getReadAdapter()->fetchOne($statement)) {
                $this->_getWriteAdapter()->update(
                    $titleTable,
                        array('title' => $object->getTitle()),
                        $this->_getWriteAdapter()
                            ->quoteInto('option_type_id='.$object->getId().' AND store_id=?', $object->getStoreId()));
            } else {
                $this->_getWriteAdapter()->insert(
                    $titleTable,
                        array(
                            'option_type_id' => $object->getId(),
                            'store_id' => $object->getStoreId(),
                            'title' => $object->getTitle()
                ));
            }
        } elseif ($object->getData('scope', 'title')) {
            $this->_getWriteAdapter()->delete(
                $titleTable,
                $this->_getWriteAdapter()->quoteInto('option_type_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
            );
        }

        return parent::_afterSave($object);
    }

    public function deleteValue($option_id)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('option_id=?', $option_id);

        $statement = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/product_option_type_value'))
            ->where($condition);

        foreach ($this->_getReadAdapter()->fetchAll($statement) as $optionType) {
            $this->deleteValues($optionType['option_type_id']);
        }

        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $condition
        );

        return $this;
    }

    public function deleteValues($option_type_id)
    {
        $childCondition = $this->_getWriteAdapter()->quoteInto('option_type_id=?', $option_type_id);
        $this->_getWriteAdapter()->delete(
            $this->getTable('catalog/product_option_type_price'),
            $childCondition
        );
        $this->_getWriteAdapter()->delete(
            $this->getTable('catalog/product_option_type_title'),
            $childCondition
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
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('option_id=?', $oldOptionId);
        $valueData = $this->_getReadAdapter()->fetchAll($select);

        $valueCond = array();

        foreach ($valueData as $data) {
            $optionTypeId = $data[$this->getIdFieldName()];
            unset($data[$this->getIdFieldName()]);
            $data['option_id'] = $newOptionId;

            $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
            $valueCond[$optionTypeId] = $this->_getWriteAdapter()->lastInsertId();
        }

        unset($valueData);

        foreach ($valueCond as $oldTypeId => $newTypeId) {
            // price
            $table = $this->getTable('catalog/product_option_type_price');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newTypeId . ', `store_id`, `price`, `price_type`'
                . 'FROM `' . $table . '` WHERE `option_type_id`=' . $oldTypeId;
            $this->_getWriteAdapter()->query($sql);

            // title
            $table = $this->getTable('catalog/product_option_type_title');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newTypeId . ', `store_id`, `title`'
                . 'FROM `' . $table . '` WHERE `option_type_id`=' . $oldTypeId;
            $this->_getWriteAdapter()->query($sql);
        }

        return $object;
    }
}
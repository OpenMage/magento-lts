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
 * Catalog product custom option resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option extends Mage_Core_Model_Mysql4_Abstract
{
    protected function  _construct()
    {
        $this->_init('catalog/product_option', 'option_id');
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $priceTable = $this->getTable('catalog/product_option_price');
        $titleTable = $this->getTable('catalog/product_option_title');

        //better to check param 'price' and 'price_type' for saving. If there is not price scip saving price
        if ($object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_AREA
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_FILE
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DATE
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DATE_TIME
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_TIME
        ) {

            //save for store_id = 0
            if (!$object->getData('scope', 'price')) {
                $statement = $this->_getReadAdapter()->select()
                    ->from($priceTable)
                    ->where('option_id = '.$object->getId().' AND store_id = ?', 0);
                if ($this->_getReadAdapter()->fetchOne($statement)) {
                    if ($object->getStoreId() == '0') {
                        $this->_getWriteAdapter()->update(
                            $priceTable,
                            array(
                                'price' => $object->getPrice(),
                                'price_type' => $object->getPriceType()
                            ),
                            $this->_getWriteAdapter()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', 0)
                        );
                    }
                } else {
                    $this->_getWriteAdapter()->insert(
                        $priceTable,
                        array(
                            'option_id' => $object->getId(),
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
                                $rate=1;
                            }
                            $newPrice = $object->getPrice() * $rate;
                        } else {
                            $newPrice = $object->getPrice();
                        }
                        $statement = $this->_getReadAdapter()->select()
                            ->from($priceTable)
                            ->where('option_id = '.$object->getId().' AND store_id = ?', $storeId);

                        if ($this->_getReadAdapter()->fetchOne($statement)) {
                            $this->_getWriteAdapter()->update(
                                $priceTable,
                                array(
                                    'price' => $newPrice,
                                    'price_type' => $object->getPriceType()
                                ),
                                $this->_getWriteAdapter()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', $storeId)
                            );
                        } else {
                            $this->_getWriteAdapter()->insert(
                                $priceTable,
                                array(
                                    'option_id' => $object->getId(),
                                    'store_id' => $storeId,
                                    'price' => $newPrice,
                                    'price_type' => $object->getPriceType()
                                )
                            );
                        }
                    }// end foreach()
                }
            } elseif ($scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE && $object->getData('scope', 'price')) {
                $this->_getWriteAdapter()->delete(
                    $priceTable,
                    $this->_getWriteAdapter()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
                );
            }
        }

        //title
        if (!$object->getData('scope', 'title')) {
            $statement = $this->_getReadAdapter()->select()
                ->from($titleTable)
                ->where('option_id = '.$object->getId().' and store_id = ?', 0);

            if ($this->_getReadAdapter()->fetchOne($statement)) {
                if ($object->getStoreId() == '0') {
                    $this->_getWriteAdapter()->update(
                        $titleTable,
                            array('title' => $object->getTitle()),
                            $this->_getWriteAdapter()->quoteInto('option_id='.$object->getId().' AND store_id=?', 0)
                    );
                }
            } else {
                $this->_getWriteAdapter()->insert(
                    $titleTable,
                        array(
                            'option_id' => $object->getId(),
                            'store_id' => 0,
                            'title' => $object->getTitle()
                ));
            }
        }

        if ($object->getStoreId() != '0' && !$object->getData('scope', 'title')) {
            $statement = $this->_getReadAdapter()->select()
                ->from($titleTable)
                ->where('option_id = '.$object->getId().' and store_id = ?', $object->getStoreId());

            if ($this->_getReadAdapter()->fetchOne($statement)) {
                $this->_getWriteAdapter()->update(
                    $titleTable,
                        array('title' => $object->getTitle()),
                        $this->_getWriteAdapter()
                            ->quoteInto('option_id='.$object->getId().' AND store_id=?', $object->getStoreId()));
            } else {
                $this->_getWriteAdapter()->insert(
                    $titleTable,
                        array(
                            'option_id' => $object->getId(),
                            'store_id' => $object->getStoreId(),
                            'title' => $object->getTitle()
                ));
            }
        } elseif ($object->getData('scope', 'title')) {
            $this->_getWriteAdapter()->delete(
                $titleTable,
                $this->_getWriteAdapter()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
            );
        }

        return parent::_afterSave($object);
    }

    public function deletePrices($option_id)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('option_id=?', $option_id);

        $this->_getWriteAdapter()->delete(
            $this->getTable('catalog/product_option_price'),
            $condition);

        return $this;
    }

    public function deleteTitles($option_id)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('option_id=?', $option_id);

        $this->_getWriteAdapter()->delete(
            $this->getTable('catalog/product_option_title'),
            $condition);

        return $this;
    }

    /**
     * Duplicate custom options for product
     *
     * @param Mage_Catalog_Model_Product_Option $object
     * @param int $oldProductId
     * @param int $newProductId
     * @return Mage_Catalog_Model_Product_Option
     */
    public function duplicate(Mage_Catalog_Model_Product_Option $object, $oldProductId, $newProductId)
    {
        $write  = $this->_getWriteAdapter();
        $read   = $this->_getReadAdapter();

        $optionsCond = array();
        $optionsData = array();

        // read and prepare original product options
        $select = $read->select()
            ->from($this->getTable('catalog/product_option'))
            ->where('product_id=?', $oldProductId);
        $query = $read->query($select);
        while ($row = $query->fetch()) {
            $optionsData[$row['option_id']] = $row;
            $optionsData[$row['option_id']]['product_id'] = $newProductId;
            unset($optionsData[$row['option_id']]['option_id']);
        }

        // insert options to duplicated product
        foreach ($optionsData as $oId => $data) {
            $write->insert($this->getMainTable(), $data);
            $optionsCond[$oId] = $write->lastInsertId();
        }

        // copy options prefs
        foreach ($optionsCond as $oldOptionId => $newOptionId) {
            // title
            $table = $this->getTable('catalog/product_option_title');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newOptionId . ', `store_id`, `title`'
                . 'FROM `' . $table . '` WHERE `option_id`=' . $oldOptionId;
            $this->_getWriteAdapter()->query($sql);

            // price
            $table = $this->getTable('catalog/product_option_price');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newOptionId . ', `store_id`, `price`, `price_type`'
                . 'FROM `' . $table . '` WHERE `option_id`=' . $oldOptionId;
            $this->_getWriteAdapter()->query($sql);

            $object->getValueInstance()->duplicate($oldOptionId, $newOptionId);
        }

        return $object;
    }

    /**
     * Retrieve option searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        $searchData = array();
        // retrieve options title
        $select = $this->_getReadAdapter()->select()
            ->from(array('option' => $this->getMainTable()), null)
            ->join(
                array('option_title_default' => $this->getTable('catalog/product_option_title')),
                'option_title_default.option_id=option.option_id AND option_title_default.store_id=0',
                array())
            ->joinLeft(
                array('option_title_store' => $this->getTable('catalog/product_option_title')),
                'option_title_store.option_id=option.option_id AND option_title_store.store_id=' . intval($storeId),
                array('title' => 'IFNULL(option_title_store.title, option_title_default.title)'))
            ->where('option.product_id=?', $productId);
        if ($titles = $this->_getReadAdapter()->fetchCol($select)) {
            $searchData = array_merge($searchData, $titles);
        }

        //select option type titles
        $select = $this->_getReadAdapter()->select()
            ->from(array('option' => $this->getMainTable()), null)
            ->join(
                array('option_type' => $this->getTable('catalog/product_option_type_value')),
                'option_type.option_id=option.option_id',
                array())
            ->join(
                array('option_title_default' => $this->getTable('catalog/product_option_type_title')),
                'option_title_default.option_type_id=option_type.option_type_id AND option_title_default.store_id=0',
                array())
            ->joinLeft(
                array('option_title_store' => $this->getTable('catalog/product_option_type_title')),
                'option_title_store.option_type_id=option_type.option_type_id AND option_title_store.store_id=' . intval($storeId),
                array('title' => 'IFNULL(option_title_store.title, option_title_default.title)'))
            ->where('option.product_id=?', $productId);
        if ($titles = $this->_getReadAdapter()->fetchCol($select)) {
            $searchData = array_merge($searchData, $titles);
        }

        return $searchData;
    }
}

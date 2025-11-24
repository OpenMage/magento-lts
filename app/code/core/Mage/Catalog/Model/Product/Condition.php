<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Class Mage_Catalog_Model_Product_Condition
 *
 * @package    Mage_Catalog
 *
 * @method string getPkFieldName()
 * @method string getTable()
 * @method $this setPkFieldName(string $fieldName)
 * @method $this setTable(string $tableName)
 */
class Mage_Catalog_Model_Product_Condition extends Varien_Object implements Mage_Catalog_Model_Product_Condition_Interface
{
    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return $this
     */
    public function applyToCollection($collection)
    {
        if ($this->getTable() && $this->getPkFieldName()) {
            $collection->joinTable(
                $this->getTable(),
                $this->getPkFieldName() . '=entity_id',
                ['affected_product_id' => $this->getPkFieldName()],
            );
        }

        return $this;
    }

    /**
     * @param Magento_Db_Adapter_Pdo_Mysql $dbAdapter
     * @return string|Varien_Db_Select
     */
    public function getIdsSelect($dbAdapter)
    {
        if ($this->getTable() && $this->getPkFieldName()) {
            return $dbAdapter->select()
                ->from($this->getTable(), $this->getPkFieldName());
        }

        return '';
    }
}

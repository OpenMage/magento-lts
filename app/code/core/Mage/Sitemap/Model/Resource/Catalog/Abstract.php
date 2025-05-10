<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sitemap
 */

/**
 * Sitemap resource catalog collection model
 *
 * @package    Mage_Sitemap
 */
abstract class Mage_Sitemap_Model_Resource_Catalog_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Collection Zend Db select
     *
     * @var Zend_Db_Select
     */
    protected $_select;

    /**
     * Attribute cache
     *
     * @var array
     */
    protected $_attributesCache = [];

    /**
     * Catalog factory instance
     *
     * @var Mage_Catalog_Model_Factory
     */
    protected $_factory;

    /**
     * Initialize factory instance
     */
    public function __construct(array $args = [])
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('catalog/factory');
        parent::__construct();
    }

    /**
     * Retrieve catalog collection
     *
     * @param int $storeId
     * @return array
     */
    abstract public function getCollection($storeId);

    /**
     * Add attribute to filter
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param mixed $value
     * @param string $type
     * @return Zend_Db_Select|false
     */
    protected function _addFilter($storeId, $attributeCode, $value, $type = '=')
    {
        if (!isset($this->_attributesCache[$attributeCode])) {
            $this->_loadAttribute($attributeCode);
        }

        $attribute = $this->_attributesCache[$attributeCode];

        if (!$this->_select instanceof Zend_Db_Select) {
            return false;
        }

        switch ($type) {
            case '=':
                $conditionRule = '=?';
                break;
            case 'in':
                $conditionRule = ' IN(?)';
                break;
            default:
                return false;
        }

        if ($attribute['backend_type'] == 'static') {
            $this->_select->where('main_table.' . $attributeCode . $conditionRule, $value);
        } else {
            $this->_select->join(
                ['t1_' . $attributeCode => $attribute['table']],
                'main_table.entity_id=t1_' . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.store_id=0',
                [],
            )
                ->where('t1_' . $attributeCode . '.attribute_id=?', $attribute['attribute_id']);

            if ($attribute['is_global']) {
                $this->_select->where('t1_' . $attributeCode . '.value' . $conditionRule, $value);
            } else {
                $ifCase = $this->_select->getAdapter()->getCheckSql(
                    't2_' . $attributeCode . '.value_id > 0',
                    't2_' . $attributeCode . '.value',
                    't1_' . $attributeCode . '.value',
                );
                $this->_select->joinLeft(
                    ['t2_' . $attributeCode => $attribute['table']],
                    $this->_getWriteAdapter()->quoteInto(
                        't1_' . $attributeCode . '.entity_id = t2_' . $attributeCode . '.entity_id AND t1_'
                            . $attributeCode . '.attribute_id = t2_' . $attributeCode . '.attribute_id AND t2_'
                            . $attributeCode . '.store_id = ?',
                        $storeId,
                    ),
                    [],
                )
                ->where('(' . $ifCase . ')' . $conditionRule, $value);
            }
        }

        return $this->_select;
    }

    /**
     * Prepare catalog object
     *
     * @return Varien_Object
     */
    protected function _prepareObject(array $row)
    {
        $entity = new Varien_Object();
        $entity->setId($row[$this->getIdFieldName()]);
        $entity->setUrl($this->_getEntityUrl($row, $entity));
        return $entity;
    }

    /**
     * Load and prepare entities
     *
     * @return array
     */
    protected function _loadEntities()
    {
        $entities = [];
        $query = $this->_getWriteAdapter()->query($this->_select);
        while ($row = $query->fetch()) {
            $entity = $this->_prepareObject($row);
            $entities[$entity->getId()] = $entity;
        }
        return $entities;
    }

    /**
     * Retrieve entity url
     *
     * @param array $row
     * @param Varien_Object $entity
     * @return string
     */
    abstract protected function _getEntityUrl($row, $entity);

    /**
     * Loads attribute by given attribute_code
     *
     * @param string $attributeCode
     * @return Mage_Sitemap_Model_Resource_Catalog_Abstract
     */
    abstract protected function _loadAttribute($attributeCode);
}

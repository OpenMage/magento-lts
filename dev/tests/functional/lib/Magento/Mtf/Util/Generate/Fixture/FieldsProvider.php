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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Mtf\Util\Generate\Fixture;

/**
 * Fixture fields provider.
 */
class FieldsProvider implements FieldsProviderInterface
{
    /**
     * @constructor
     */
    public function __construct()
    {
        $this->initMage();
    }

    /**
     * Collect fields for the entity based on its type.
     *
     * @param array $fixture
     * @return array
     */
    public function getFields(array $fixture)
    {
        $method = $fixture['type'] . 'CollectFields';
        if (!method_exists($this, $method)) {
            return [];
        }

        return $this->$method($fixture);
    }

    /**
     * Collect fields for the entity with eav type.
     *
     * @param array $fixture
     * @return array
     */
    protected function eavCollectFields(array $fixture)
    {
        $entity = $fixture['entity_type'];
        $collection = \Mage::getSingleton('eav/config')->getEntityType($entity)->getAttributeCollection();
        $attributes = [];
        foreach ($collection as $attribute) {
            if (isset($fixture['product_type'])) {
                $applyTo = $attribute->getApplyTo();
                if (!empty($applyTo) && !in_array($fixture['product_type'], $applyTo)) {
                    continue;
                }
            }

            /** @var \Mage_Eav_Model_Attribute $attribute */
            $code = $attribute->getAttributeCode();
            $attributes[$code] = array(
                'attribute_code' => $code,
                'backend_type' => $attribute->getBackendType(),
                'is_required' => $attribute->getIsRequired(),
                'default_value' => $attribute->getDefaultValue(),
                'input' => $attribute->getFrontendInput()
            );
        }

        return $attributes;
    }

    /**
     * Collect fields for the entity with table type.
     *
     * @param array $fixture
     * @return array
     */
    protected function tableCollectFields(array $fixture)
    {
        return $this->flatCollectFields($fixture);
    }

    /**
     * Collect fields for the entity with flat type.
     *
     * @param array $fixture
     * @return array
     */
    protected function flatCollectFields(array $fixture)
    {
        $entityType = $fixture['entity_type'];
        $fields = $this->getConnection()->describeTable($this->retrieveTableName($entityType));

        $attributes = [];
        foreach ($fields as $code => $field) {
            $attributes[$code] = array(
                'attribute_code' => $code,
                'backend_type' => $field['DATA_TYPE'],
                'is_required' => ($field['PRIMARY'] || $field['IDENTITY']),
                'default_value' => $field['DEFAULT'],
                'input' => ''
            );
        }

        return $attributes;
    }

    /**
     * Collect fields for the entity with composite type.
     *
     * @param array $fixture
     * @return array
     */
    protected function compositeCollectFields(array $fixture)
    {
        $entityTypes = $fixture['entities'];

        $connection = $this->getConnection();
        $fields = [];
        foreach ($entityTypes as $entityType) {
            $fields = array_merge($fields, $connection->describeTable($this->retrieveTableName($entityType)));
        }

        $attributes = [];
        foreach ($fields as $code => $field) {
            $attributes[$code] = [
                'attribute_code' => $code,
                'backend_type' => $field['DATA_TYPE'],
                'is_required' => ($field['PRIMARY'] || $field['IDENTITY']),
                'default_value' => $field['DEFAULT'],
                'input' => ''
            ];
        }

        return $attributes;
    }

    /**
     * Mage init.
     *
     * @return void
     */
    protected function initMage()
    {
        require_once realpath(__DIR__ . "/../../../../../../../../../app/Mage.php");
        \Mage::app('default');
    }

    /**
     * Get DB connection.
     *
     * @return \Magento_Db_Adapter_Pdo_Mysql
     */
    protected function getConnection()
    {
        return \Mage::getSingleton('core/resource')->getConnection('core_write');
    }

    /**
     * Get DB table name with prefix.
     *
     * @param string $entity
     * @return string
     */
    protected function retrieveTableName($entity)
    {
        return \Mage::getSingleton('core/resource')->getTableName($entity);
    }

    /**
     * Check connection to DB.
     *
     * @return bool
     */
    public function checkConnection()
    {
        return $this->getConnection();
    }
}

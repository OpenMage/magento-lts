<?php

/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rule
 */
class Mage_Rule_Model_Resource_Rule_Condition_SqlBuilder
{
    /**
     * Database adapter
     * @var Varien_Db_Adapter_Interface
     */
    protected $_adapter;

    public function __construct(array $config = [])
    {
        $this->_adapter = $config['adapter'] ?? Mage::getSingleton('core/resource')->getConnection(Mage_Core_Model_Resource::DEFAULT_READ_RESOURCE);
    }

    /**
     * Convert operator for sql where
     *
     * @param string $field
     * @param string $operator
     * @param string|array $value
     * @return string
     */
    public function getOperatorCondition($field, $operator, $value)
    {
        switch ($operator) {
            case '!=':
            case '>=':
            case '<=':
            case '>':
            case '<':
                $selectOperator = sprintf('%s?', $operator);
                break;
            case '{}':
            case '!{}':
                if (preg_match('/^.*(category_id)$/', $field) && is_array($value)) {
                    $selectOperator = ' IN (?)';
                } else {
                    $selectOperator = ' LIKE ?';
                }
                if (substr($operator, 0, 1) == '!') {
                    $selectOperator = ' NOT' . $selectOperator;
                }
                break;

            case '[]':
            case '![]':
            case '()':
            case '!()':
                $selectOperator = 'FIND_IN_SET(?,' . $this->_adapter->quoteIdentifier($field) . ')';
                if (substr($operator, 0, 1) == '!') {
                    $selectOperator = 'NOT ' . $selectOperator;
                }
                break;

            default:
                $selectOperator = '=?';
                break;
        }
        $field = $this->_adapter->quoteIdentifier($field);

        if (is_array($value) && in_array($operator, ['==', '!=', '>=', '<=', '>', '<', '{}', '!{}'])) {
            $results = [];
            foreach ($value as $v) {
                $results[] = $this->_adapter->quoteInto("{$field}{$selectOperator}", $v);
            }
            $result = implode(' AND ', $results);
        } elseif (in_array($operator, ['()', '!()', '[]', '![]'])) {
            if (!is_array($value)) {
                $value = [$value];
            }

            $results = [];
            foreach ($value as $v) {
                $results[] = $this->_adapter->quoteInto("{$selectOperator}", $v);
            }
            $result = implode(in_array($operator, ['()', '!()']) ? ' OR ' : ' AND ', $results);
        } else {
            $result = $this->_adapter->quoteInto("{$field}{$selectOperator}", $value);
        }
        return $result;
    }
}

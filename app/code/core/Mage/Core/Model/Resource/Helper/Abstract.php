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
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract resource helper class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Resource_Helper_Abstract
{
    /**
     * Read adapter instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_readAdapter;

    /**
     * Write adapter instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_writeAdapter;

    /**
     * Resource helper module prefix
     *
     * @var string
     */
    protected $_modulePrefix;

    /**
     * Initialize resource helper instance
     *
     * @param string $module
     */
    public function __construct($module)
    {
        $this->_modulePrefix = (string)$module;
    }

    /**
     * Retrieve connection for read data
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getReadAdapter()
    {
        if ($this->_readAdapter === null) {
            $this->_readAdapter = $this->_getConnection('read');
        }

        return $this->_readAdapter;
    }

    /**
     * Retrieve connection for write data
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getWriteAdapter()
    {
        if ($this->_writeAdapter === null) {
            $this->_writeAdapter = $this->_getConnection('write');
        }

        return $this->_writeAdapter;
    }

    /**
     * Retrieves connection to the resource
     *
     * @param string $name
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getConnection($name)
    {
        $connection = sprintf('%s_%s', $this->_modulePrefix, $name);
        /** @var $resource Mage_Core_Model_Resource */
        $resource   = Mage::getSingleton('core/resource');

        return $resource->getConnection($connection);
    }

    /**
     * Escapes value, that participates in LIKE, with '\' symbol.
     * Note: this func cannot be used on its own, because different RDMBS may use different default escape symbols,
     * so you should either use addLikeEscape() to produce LIKE construction, or add escape symbol on your own.
     *
     * By default escapes '_', '%' and '\' symbols. If some masking symbols must not be escaped, then you can set
     * appropriate options in $options.
     *
     * $options can contain following flags:
     * - 'allow_symbol_mask' - the '_' symbol will not be escaped
     * - 'allow_string_mask' - the '%' symbol will not be escaped
     * - 'position' ('any', 'start', 'end') - expression will be formed so that $value will be found at position within string,
     *     by default when nothing set - string must be fully matched with $value
     *
     * @param string $value
     * @param array $options
     * @return string
     */
    public function escapeLikeValue($value, $options = array())
    {
        $value = str_replace('\\', '\\\\', $value);

        $from = array();
        $to = array();
        if (empty($options['allow_symbol_mask'])) {
            $from[] = '_';
            $to[] = '\_';
        }
        if (empty($options['allow_string_mask'])) {
            $from[] = '%';
            $to[] = '\%';
        }
        if ($from) {
            $value = str_replace($from, $to, $value);
        }

        if (isset($options['position'])) {
            switch ($options['position']) {
                case 'any':
                    $value = '%' . $value . '%';
                    break;
                case 'start':
                    $value = $value . '%';
                    break;
                case 'end':
                    $value = '%' . $value;
                    break;
            }
        }

        return $value;
    }

    /**
     * Escapes, quotes and adds escape symbol to LIKE expression.
     * For options and escaping see escapeLikeValue().
     *
     * @param string $value
     * @param array $options
     * @return Zend_Db_Expr
     *
     * @see escapeLikeValue()
     */
    abstract public function addLikeEscape($value, $options = array());

    /**
     * Returns case insensitive LIKE construction.
     * For options and escaping see escapeLikeValue().
     *
     * @param string $field
     * @param string $value
     * @param array $options
     * @return Zend_Db_Expr
     *
     * @see escapeLikeValue()
     */
    public function getCILike($field, $value, $options = array())
    {
        $quotedField = $this->_getReadAdapter()->quoteIdentifier($field);
        return new Zend_Db_Expr($quotedField . ' LIKE ' . $this->addLikeEscape($value, $options));
    }

    /**
     * Converts old pre-MMDB column definition for MySQL to new cross-db column DDL definition.
     * Used to convert data from 3rd party extensions that hasn't been updated to MMDB style yet.
     *
     * E.g. Converts type 'varchar(255)' to array('type' => Varien_Db_Ddl_Table::TYPE_TEXT, 'length' => 255)
     *
     * @param array $column
     * @return array
     */
    public function convertOldColumnDefinition($column)
    {
        // Match type and size - e.g. varchar(100) or decimal(12,4) or int
        $matches    = array();
        $definition = trim($column['type']);
        if (!preg_match('/([^(]*)(\\((.*)\\))?/', $definition, $matches)) {
            throw Mage::exception(
                'Mage_Core',
                Mage::helper('core')->__("Wrong old style column type definition: {$definition}.")
            );
        }

        $length = null;
        $proposedLength = (isset($matches[3]) && strlen($matches[3])) ? $matches[3] : null;
        switch (strtolower($matches[1])) {
            case 'bool':
                $length = null;
                $type = Varien_Db_Ddl_Table::TYPE_BOOLEAN;
                break;
            case 'char':
            case 'varchar':
            case 'tinytext':
                $length = $proposedLength;
                if (!$length) {
                    $length = 255;
                }
                $type = Varien_Db_Ddl_Table::TYPE_TEXT;
                break;
            case 'text':
                $length = $proposedLength;
                if (!$length) {
                    $length = '64k';
                }
                $type = Varien_Db_Ddl_Table::TYPE_TEXT;
                break;
            case 'mediumtext':
                $length = $proposedLength;
                if (!$length) {
                    $length = '16M';
                }
                $type = Varien_Db_Ddl_Table::TYPE_TEXT;
                break;
            case 'longtext':
                $length = $proposedLength;
                if (!$length) {
                    $length = '4G';
                }
                $type = Varien_Db_Ddl_Table::TYPE_TEXT;
                break;
            case 'blob':
                $length = $proposedLength;
                if (!$length) {
                    $length = '64k';
                }
                $type = Varien_Db_Ddl_Table::TYPE_BLOB;
                break;
            case 'mediumblob':
                $length = $proposedLength;
                if (!$length) {
                    $length = '16M';
                }
                $type = Varien_Db_Ddl_Table::TYPE_BLOB;
                break;
            case 'longblob':
                $length = $proposedLength;
                if (!$length) {
                    $length = '4G';
                }
                $type = Varien_Db_Ddl_Table::TYPE_BLOB;
                break;
            case 'tinyint':
            case 'smallint':
                $type = Varien_Db_Ddl_Table::TYPE_SMALLINT;
                break;
            case 'mediumint':
            case 'int':
                $type = Varien_Db_Ddl_Table::TYPE_INTEGER;
                break;
            case 'bigint':
                $type = Varien_Db_Ddl_Table::TYPE_BIGINT;
                break;
            case 'float':
                $type = Varien_Db_Ddl_Table::TYPE_FLOAT;
                break;
            case 'decimal':
            case 'numeric':
                $length = $proposedLength;
                $type = Varien_Db_Ddl_Table::TYPE_DECIMAL;
                break;
            case 'datetime':
                $type = Varien_Db_Ddl_Table::TYPE_DATETIME;
                break;
            case 'timestamp':
            case 'time':
                $type = Varien_Db_Ddl_Table::TYPE_TIMESTAMP;
                break;
            case 'date':
                $type = Varien_Db_Ddl_Table::TYPE_DATE;
                break;
            default:
                throw Mage::exception(
                    'Mage_Core',
                    Mage::helper('core')->__("Unknown old style column type definition: {$definition}.")
                );
        }

        $result = array(
            'type'     => $type,
            'length'   => $length,
            'unsigned' => $column['unsigned'],
            'nullable' => $column['is_null'],
            'default'  => $column['default'],
            'identity' => stripos($column['extra'], 'auto_increment') !== false
        );

        /**
         * Process the case when 'is_null' prohibits null value, and 'default' proposed to be null.
         * It just means that default value not specified, and we must remove it from column definition.
         */
        if (false === $column['is_null'] && null === $column['default']) {
            unset($result['default']);
        }

        return $result;
    }
}

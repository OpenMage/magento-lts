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
 * @category    Varien
 * @package     Varien_Db
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Data Definition for table
 *
 * @category    Varien
 * @package     Varien_Db
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Db_Ddl_Table
{
    /**
     * Types of columns
     */
    const TYPE_BOOLEAN          = 'boolean';
    const TYPE_SMALLINT         = 'smallint';
    const TYPE_INTEGER          = 'integer';
    const TYPE_BIGINT           = 'bigint';
    const TYPE_FLOAT            = 'float';
    const TYPE_NUMERIC          = 'numeric';
    const TYPE_DECIMAL          = 'decimal';
    const TYPE_DATE             = 'date';
    const TYPE_TIMESTAMP        = 'timestamp'; // Capable to support date-time from 1970 + auto-triggers in some RDBMS
    const TYPE_DATETIME         = 'datetime'; // Capable to support long date-time before 1970
    const TYPE_TEXT             = 'text';
    const TYPE_BLOB             = 'blob'; // Used for back compatibility, when query param can't use statement options
    const TYPE_VARBINARY        = 'varbinary'; // A real blob, stored as binary inside DB

    // Deprecated column types, support is left only in MySQL adapter.
    const TYPE_TINYINT          = 'tinyint';        // Internally converted to TYPE_SMALLINT
    const TYPE_CHAR             = 'char';           // Internally converted to TYPE_TEXT
    const TYPE_VARCHAR          = 'varchar';        // Internally converted to TYPE_TEXT
    const TYPE_LONGVARCHAR      = 'longvarchar';    // Internally converted to TYPE_TEXT
    const TYPE_CLOB             = 'cblob';          // Internally converted to TYPE_TEXT
    const TYPE_DOUBLE           = 'double';         // Internally converted to TYPE_FLOAT
    const TYPE_REAL             = 'real';           // Internally converted to TYPE_FLOAT
    const TYPE_TIME             = 'time';           // Internally converted to TYPE_TIMESTAMP
    const TYPE_BINARY           = 'binary';         // Internally converted to TYPE_BLOB
    const TYPE_LONGVARBINARY    = 'longvarbinary';  // Internally converted to TYPE_BLOB

    /**
     * Default and maximal TEXT and BLOB columns sizes we can support for different DB systems.
     */
    const DEFAULT_TEXT_SIZE     = 1024;
    const MAX_TEXT_SIZE         = 2147483648;
    const MAX_VARBINARY_SIZE    = 2147483648;

    /**
     * Default values for timestampses - fill with current timestamp on inserting record, on changing and both cases
     */
    const TIMESTAMP_INIT_UPDATE = 'TIMESTAMP_INIT_UPDATE';
    const TIMESTAMP_INIT        = 'TIMESTAMP_INIT';
    const TIMESTAMP_UPDATE      = 'TIMESTAMP_UPDATE';

    /**
     * Actions used for foreign keys
     */
    const ACTION_CASCADE        = 'CASCADE';
    const ACTION_SET_NULL       = 'SET NULL';
    const ACTION_NO_ACTION      = 'NO ACTION';
    const ACTION_RESTRICT       = 'RESTRICT';
    const ACTION_SET_DEFAULT    = 'SET DEFAULT';

    /**
     * Name of table
     *
     * @var string
     */
    protected $_tableName;

    /**
     * Schema name
     *
     * @var string
     */
    protected $_schemaName;

    /**
     * Comment for Table
     *
     * @var string
     */
    protected $_tableComment;

    /**
     * Column descriptions for a table
     *
     * Is an associative array keyed by the uppercase column name
     * The value of each array element is an associative array
     * with the following keys:
     *
     * COLUMN_NAME      => string; column name
     * COLUMN_POSITION  => number; ordinal position of column in table
     * DATA_TYPE        => string; constant datatype of column
     * DEFAULT          => string; default expression of column, null if none
     * NULLABLE         => boolean; true if column can have nulls
     * LENGTH           => number; length of CHAR/VARCHAR/INT
     * SCALE            => number; scale of NUMERIC/DECIMAL
     * PRECISION        => number; precision of NUMERIC/DECIMAL
     * UNSIGNED         => boolean; unsigned property of an integer type
     * PRIMARY          => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
     * IDENTITY         => integer; true if column is auto-generated with unique values
     * COMMENT          => string; column description
     *
     * @var array
     */
    protected $_columns         = array();

    /**
     * Index descriptions for a table
     *
     * Is an associative array keyed by the uppercase index name
     * The value of each array element is an associative array
     * with the following keys:
     *
     * INDEX_NAME       => string; index name
     * COLUMNS          => array; array of index columns
     * TYPE             => string; Optional special index type
     *
     * COLUMNS is an associative array keyed by the uppercase column name
     * The value of each COLUMNS array element is an associative array
     * with the following keys:
     *
     * NAME             => string; The column name
     * SIZE             => int|null; Length of index column (always null if index is unique)
     * POSITION         => int; Position in index
     *
     * @var array
     */
    protected $_indexes         = array();

    /**
     * Foreign key descriptions for a table
     *
     * Is an associative array keyed by the uppercase foreign key name
     * The value of each array element is an associative array
     * with the following keys:
     *
     * FK_NAME          => string; The foreign key name
     * COLUMN_NAME      => string; The column name in table
     * REF_TABLE_NAME   => string; Reference table name
     * REF_COLUMN_NAME  => string; Reference table column name
     * ON_DELETE        => string; Integrity Actions, default NO ACTION
     * ON_UPDATE        => string; Integrity Actions, default NO ACTION
     *
     * Valid Integrity Actions:
     * CASCADE | SET NULL | NONE | RESTRICT | SET DEFAULT
     *
     * @var array
     */
    protected $_foreignKeys     = array();

    /**
     * Additional table options
     *
     * @var array
     */
    protected $_options         = array(
        'type'          => 'INNODB',
        'charset'       => 'utf8',
        'collate'       => 'utf8_general_ci',

    );

    /**
     * Set table name
     *
     * @param string $name
     * @return Varien_Db_Ddl_Table
     */
    public function setName($name)
    {
        $this->_tableName = $name;
        if ($this->_tableComment === null) {
            $this->_tableComment = $name;
        }
        return $this;
    }

    /**
     * Set schema name
     *
     * @param string $name
     * @return Varien_Db_Ddl_Table
     */
    public function setSchema($name)
    {
        $this->_schemaName = $name;
        return $this;
    }

    /**
     * Set comment for table
     *
     * @param string $comment
     * @return Varien_Db_Ddl_Table
     */
    public function setComment($comment)
    {
        $this->_tableComment = $comment;
        return $this;
    }

    /**
     * Retrieve name of table
     *
     * @throws Zend_Db_Exception
     * @return string
     */
    public function getName()
    {
        if (is_null($this->_tableName)) {
            throw new Zend_Db_Exception('Table name is not defined');
        }
        return $this->_tableName;
    }

    /**
     * Get schema name
     *
     * @return string|null
     */
    public function getSchema()
    {
        return $this->_schemaName;
    }

    /**
     * Return comment for table
     *
     * @return string
     */
    public function getComment()
    {
        return $this->_tableComment;
    }

    /**
     * Adds column to table.
     *
     * $options contains additional options for columns. Supported values are:
     * - 'unsigned', for number types only. Default: FALSE.
     * - 'precision', for numeric and decimal only. Default: taken from $size, if not set there then 0.
     * - 'scale', for numeric and decimal only. Default: taken from $size, if not set there then 10.
     * - 'default'. Default: not set.
     * - 'nullable'. Default: TRUE.
     * - 'primary', add column to primary index. Default: do not add.
     * - 'primary_position', only for column in primary index. Default: count of primary columns + 1.
     * - 'identity' or 'auto_increment'. Default: FALSE.
     *
     * @param string $name the column name
     * @param string $type the column data type
     * @param string|int|array $size the column length
     * @param array $options array of additional options
     * @param string $comment column description
     * @throws Zend_Db_Exception
     * @return Varien_Db_Ddl_Table
     */
    public function addColumn($name, $type, $size = null, $options = array(), $comment = null)
    {
        $position           = count($this->_columns);
        $default            = false;
        $nullable           = true;
        $length             = null;
        $scale              = null;
        $precision          = null;
        $unsigned           = false;
        $primary            = false;
        $primaryPosition    = 0;
        $identity           = false;

        // Convert deprecated types
        switch ($type) {
            case self::TYPE_CHAR:
            case self::TYPE_VARCHAR:
            case self::TYPE_LONGVARCHAR:
            case self::TYPE_CLOB:
                $type = self::TYPE_TEXT;
                break;
            case self::TYPE_TINYINT:
                $type = self::TYPE_SMALLINT;
                break;
            case self::TYPE_DOUBLE:
            case self::TYPE_REAL:
                $type = self::TYPE_FLOAT;
                break;
            case self::TYPE_TIME:
                $type = self::TYPE_TIMESTAMP;
                break;
            case self::TYPE_BINARY:
            case self::TYPE_LONGVARBINARY:
                $type = self::TYPE_BLOB;
                break;
        }

        // Prepare different properties
        switch ($type) {
            case self::TYPE_BOOLEAN:
                break;

            case self::TYPE_SMALLINT:
            case self::TYPE_INTEGER:
            case self::TYPE_BIGINT:
                if (!empty($options['unsigned'])) {
                    $unsigned = true;
                }

                break;

            case self::TYPE_FLOAT:
                if (!empty($options['unsigned'])) {
                    $unsigned = true;
                }
                break;

            case self::TYPE_DECIMAL:
            case self::TYPE_NUMERIC:
                $match      = array();
                //For decimal(M,D), M must be >= D
                $precision  = 10;
                $scale      = 0;
                // parse size value
                if (is_array($size)) {
                    if (count($size) == 2) {
                        $size       = array_values($size);
                        $precision  = $size[0];
                        $scale      = $size[1];
                    }
                } else if (preg_match('#^(\d+),(\d+)$#', $size, $match)) {
                    $precision  = $match[1];
                    $scale      = $match[2];
                }
                // check options
                if (isset($options['precision'])) {
                    $precision = $options['precision'];
                }

                if (isset($options['scale'])) {
                    $scale = $options['scale'];
                }

                if (!empty($options['unsigned'])) {
                    $unsigned = true;
                }
                break;
            case self::TYPE_DATE:
            case self::TYPE_DATETIME:
            case self::TYPE_TIMESTAMP:
                break;
            case self::TYPE_TEXT:
            case self::TYPE_BLOB:
            case self::TYPE_VARBINARY:
                $length = $size;
                break;
            default:
                throw new Zend_Db_Exception('Invalid column data type "' . $type . '"');
        }

        if (array_key_exists('default', $options)) {
            $default = $options['default'];
        }
        if (array_key_exists('nullable', $options)) {
            $nullable = (bool)$options['nullable'];
        }
        if (!empty($options['primary'])) {
            $primary = true;
            if (isset($options['primary_position'])) {
                $primaryPosition = (int)$options['primary_position'];
            } else {
                $primaryPosition = 0;
                foreach ($this->_columns as $v) {
                    if ($v['PRIMARY']) {
                        $primaryPosition ++;
                    }
                }
            }
        }
        if (!empty($options['identity']) || !empty($options['auto_increment'])) {
            $identity = true;
        }

        if ($comment === null) {
            $comment = ucfirst($name);
        }

        $upperName = strtoupper($name);
        $this->_columns[$upperName] = array(
            'COLUMN_NAME'       => $name,
            'COLUMN_TYPE'       => $type,
            'COLUMN_POSITION'   => $position,
            'DATA_TYPE'         => $type,
            'DEFAULT'           => $default,
            'NULLABLE'          => $nullable,
            'LENGTH'            => $length,
            'SCALE'             => $scale,
            'PRECISION'         => $precision,
            'UNSIGNED'          => $unsigned,
            'PRIMARY'           => $primary,
            'PRIMARY_POSITION'  => $primaryPosition,
            'IDENTITY'          => $identity,
            'COMMENT'           => $comment
        );

        return $this;
    }

    /**
     * Add Foreign Key to table
     *
     * @param string $fkName        the foreign key name
     * @param string $column        the foreign key column name
     * @param string $refTable      the reference table name
     * @param string $refColumn     the reference table column name
     * @param string $onDelete      the action on delete row
     * @param string $onUpdate      the action on update
     * @throws Zend_Db_Exception
     * @return Varien_Db_Ddl_Table
     */
    public function addForeignKey($fkName, $column, $refTable, $refColumn, $onDelete = null, $onUpdate = null)
    {
        $upperName = strtoupper($fkName);

        // validate column name
        if (!isset($this->_columns[strtoupper($column)])) {
            throw new Zend_Db_Exception('Undefined column "' . $column . '"');
        }

        switch ($onDelete) {
            case self::ACTION_CASCADE:
            case self::ACTION_RESTRICT:
            case self::ACTION_SET_DEFAULT:
            case self::ACTION_SET_NULL:
                break;
            default:
                $onDelete = self::ACTION_NO_ACTION;
        }

        switch ($onUpdate) {
            case self::ACTION_CASCADE:
            case self::ACTION_RESTRICT:
            case self::ACTION_SET_DEFAULT:
            case self::ACTION_SET_NULL:
                break;
            default:
                $onUpdate = self::ACTION_NO_ACTION;
        }

        $this->_foreignKeys[$upperName] = array(
            'FK_NAME'           => $fkName,
            'COLUMN_NAME'       => $column,
            'REF_TABLE_NAME'    => $refTable,
            'REF_COLUMN_NAME'   => $refColumn,
            'ON_DELETE'         => $onDelete,
            'ON_UPDATE'         => $onUpdate
        );

        return $this;
    }

    /**
     * Add index to table
     *
     * @param string $indexName     the index name
     * @param array|string $columns array of columns or column string
     * @param array $options        array of additional options
     * @return Varien_Db_Ddl_Table
     */
    public function addIndex($indexName, $fields, $options = array())
    {
        $idxType    = Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX;
        $position   = 0;
        $columns    = array();
        if (!is_array($fields)) {
            $fields = array($fields);
        }

        foreach ($fields as $columnData) {
            $columnSize = null;
            $columnPos  = $position;
            if (is_string($columnData)) {
                $columnName = $columnData;
            } else if (is_array($columnData)) {
                if (!isset($columnData['name'])) {
                    throw new Zend_Db_Exception('Invalid index column data');
                }

                $columnName = $columnData['name'];
                if (!empty($columnData['size'])) {
                    $columnSize = (int)$columnData['size'];
                }
                if (!empty($columnData['position'])) {
                    $columnPos = (int)$columnData['position'];
                }
            } else {
                continue;
            }

            $columns[strtoupper($columnName)] = array(
                'NAME'      => $columnName,
                'SIZE'      => $columnSize,
                'POSITION'  => $columnPos
            );

            $position ++;
        }

        if (empty($columns)) {
            throw new Zend_Db_Exception('Columns for index are not defined');
        }

        if (!empty($options['type'])) {
            $idxType = $options['type'];
        }

        $this->_indexes[strtoupper($indexName)] = array(
            'INDEX_NAME'    => $indexName,
            'COLUMNS'       => $this->_normalizeIndexColumnPosition($columns),
            'TYPE'          => $idxType
        );

        return $this;
    }

    /**
     * Retrieve array of table columns
     *
     * @param bool $normalized
     * @see $this->_columns
     * @return array
     */
    public function getColumns($normalized = true)
    {
        if ($normalized) {
            return $this->_normalizeColumnPosition($this->_columns);
        }
        return $this->_columns;
    }

    /**
     * Set column, formatted according to DDL Table format, into columns structure
     *
     * @param array $column
     * @see $this->_columns
     * @return Varien_Db_Ddl_Table
     */
    public function setColumn($column)
    {
        $upperName = strtoupper($column['COLUMN_NAME']);
        $this->_columns[$upperName] = $column;
        return $this;
    }

    /**
     * Retrieve array of table indexes
     *
     * @see $this->_indexes
     * @return array
     */
    public function getIndexes()
    {
        return $this->_indexes;
    }

    /**
     * Retrieve array of table foreign keys
     *
     * @see $this->_foreignKeys
     * @return array
     */
    public function getForeignKeys()
    {
        return $this->_foreignKeys;
    }

    /**
     * Set table option
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    public function setOption($key, $value)
    {
        $this->_options[$key] = $value;
        return $this;
    }

    /**
     * Retrieve table option value by option name
     * Return null if option does not exits
     *
     * @param string $key
     * @return mixed
     */
    public function getOption($key)
    {
        if (!isset($this->_options[$key])) {
            return null;
        }
        return $this->_options[$key];
    }

    /**
     * Retrieve array of table options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Index column position comparison function
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortIndexColumnPosition($a, $b)
    {
        return $a['POSITION'] - $b['POSITION'];
    }

    /**
     * table column position comparison function
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortColumnPosition($a, $b)
    {
        return $a['COLUMN_POSITION'] - $b['COLUMN_POSITION'];
    }

    /**
     * Normalize positon of index columns array
     *
     * @param array $columns
     * @return array
     */
    protected function _normalizeIndexColumnPosition($columns)
    {
        uasort($columns, array($this, '_sortIndexColumnPosition'));
        $position = 0;
        foreach (array_keys($columns) as $columnId) {
            $columns[$columnId]['POSITION'] = $position;
            $position ++;
        }
        return $columns;
    }

    /**
     * Normalize positon of table columns array
     *
     * @param array $columns
     * @return array
     */
    protected function _normalizeColumnPosition($columns)
    {
        uasort($columns, array($this, '_sortColumnPosition'));
        $position = 0;
        foreach (array_keys($columns) as $columnId) {
            $columns[$columnId]['COLUMN_POSITION'] = $position;
            $position ++;
        }
        return $columns;
    }
}

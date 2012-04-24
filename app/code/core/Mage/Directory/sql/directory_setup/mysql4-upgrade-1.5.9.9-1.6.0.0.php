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
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('directory/country_region_name'),
    'FK_DIRECTORY_REGION_NAME_REGION'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('directory/country_format'),
    'COUNTRY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('directory/country_region'),
    'FK_REGION_COUNTRY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('directory/country_region_name'),
    'FK_DIRECTORY_REGION_NAME_REGION'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('directory/currency_rate'),
    'FK_CURRENCY_RATE_TO'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('directory/country') => array(
        'columns' => array(
            'country_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Country Id in ISO-2'
            ),
            'iso2_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'nullable'  => false,
                'comment'   => 'Country ISO-2 format'
            ),
            'iso3_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'nullable'  => false,
                'comment'   => 'Country ISO-3'
            )
        ),
        'comment' => 'Directory Country'
    ),
    $installer->getTable('directory/country_format') => array(
        'columns' => array(
            'country_format_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Country Format Id'
            ),
            'country_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'nullable'  => false,
                'comment'   => 'Country Id in ISO-2'
            ),
            'type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 30,
                'nullable'  => false,
                'comment'   => 'Country Format Type'
            ),
            'format' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Country Format'
            )
        ),
        'comment' => 'Directory Country Format'
    ),
    $installer->getTable('directory/country_region') => array(
        'columns' => array(
            'region_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Region Id'
            ),
            'country_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 4,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Country Id in ISO-2'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Region code'
            ),
            'default_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Region Name'
            )
        ),
        'comment' => 'Directory Country Region'
    ),
    $installer->getTable('directory/country_region_name') => array(
        'columns' => array(
            'locale' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 8,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Locale'
            ),
            'region_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Region Id'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Region Name'
            )
        ),
        'comment' => 'Directory Country Region Name'
    ),
    $installer->getTable('directory/currency_rate') => array(
        'columns' => array(
            'currency_from' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Currency Code Convert From'
            ),
            'currency_to' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Currency Code Convert To'
            ),
            'rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 12,
                'precision' => 24,
                'nullable'  => false,
                'default'   => '0.000000000000',
                'comment'   => 'Currency Conversion Rate'
            )
        ),
        'comment' => 'Directory Currency Rate'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('directory/country_format'),
    $installer->getIdxName(
        'directory/country_format',
        array('country_id', 'type'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('country_id', 'type'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('directory/country_region'),
    $installer->getIdxName('directory/country_region', array('country_id')),
    array('country_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('directory/country_region_name'),
    $installer->getIdxName('directory/country_region_name', array('region_id')),
    array('region_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('directory/currency_rate'),
    $installer->getIdxName('directory/currency_rate', array('currency_to')),
    array('currency_to')
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('directory/country_region_name', 'region_id', 'directory/country_region', 'region_id'),
    $installer->getTable('directory/country_region_name'),
    'region_id',
    $installer->getTable('directory/country_region'),
    'region_id'
);

$installer->endSetup();

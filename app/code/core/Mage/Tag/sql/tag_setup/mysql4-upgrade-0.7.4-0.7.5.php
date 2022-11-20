<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('tag/tag'), 'first_store_id', "smallint(5) UNSIGNED NOT NULL DEFAULT '0'");

$groupedTags = $installer->getConnection()->select()
    ->from($installer->getTable('tag/relation'))->group('tag_id')->order('created_at ASC');
$select = $installer->getConnection()->select()
    ->reset()
    ->joinInner(
        ['relation_table' => new Zend_Db_Expr("({$groupedTags->__toString()})")],
        'relation_table.tag_id = main_table.tag_id',
        null
    )
    ->columns(['first_store_id' => 'store_id']);

$updateSql = $select->crossUpdateFromSelect(['main_table' => $installer->getTable('tag/tag')]);
$installer->getConnection()->query($updateSql);

$installer->endSetup();

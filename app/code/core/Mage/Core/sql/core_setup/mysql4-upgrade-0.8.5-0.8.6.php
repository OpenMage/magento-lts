<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('core_website'), 'is_default', 'tinyint(1) unsigned default 0');
$select = $installer->getConnection()->select()
    ->from($installer->getTable('core_website'))
    ->where('website_id > ?', 0)
    ->order('website_id')
    ->limit(1);
$row = $installer->getConnection()->fetchRow($select);

if ($row) {
    $whereBind = $installer->getConnection()->quoteInto('website_id=?', $row['website_id']);
    $installer->getConnection()->update(
        $installer->getTable('core_website'),
        ['is_default' => 1],
        $whereBind,
    );
}

$installer->endSetup();

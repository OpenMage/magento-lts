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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Core Install
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$installer->run("
ALTER TABLE `{$installer->getTable('core_url_rewrite')}`
    DROP `entity_id`,
    DROP `type`,
    ADD `is_system` tinyint(1) unsigned default '1' AFTER `target_path`,
    DROP INDEX `store_id`,
    ADD INDEX `FK_CORE_URL_REWRITE_STORE` (`store_id`),
    DROP INDEX `id_path`,
    ADD UNIQUE `UNQ_PATH` (`store_id`, `id_path`, `is_system`),
    DROP INDEX `request_path`,
    ADD UNIQUE `UNQ_REQUEST_PATH` (`store_id`, `request_path`),
    DROP INDEX `target_path`,
    ADD INDEX `IDX_TARGET_PATH` (`store_id`, `target_path`);
DROP TABLE IF EXISTS `{$installer->getTable('core_url_rewrite_tag')}`;
");
$installer->endSetup();

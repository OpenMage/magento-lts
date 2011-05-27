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
 * @package     Mage_Cms
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('cms_block')}
    CHANGE `store_id` `store_id` smallint(5) unsigned NULL DEFAULT '0';
ALTER TABLE {$this->getTable('cms_block')}
    ADD CONSTRAINT `FK_CMS_BLOCK_STORE` FOREIGN KEY (`store_id`)
    REFERENCES {$this->getTable('core_store')} (`store_id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL;
");
$installer->run("
ALTER TABLE {$this->getTable('cms_page')}
    CHANGE `store_id` `store_id` smallint(5) unsigned NULL DEFAULT '0';
ALTER TABLE {$this->getTable('cms_page')}
    ADD CONSTRAINT `FK_CMS_PAGE_STORE` FOREIGN KEY (`store_id`)
    REFERENCES {$this->getTable('core_store')} (`store_id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL;
");

$installer->endSetup();

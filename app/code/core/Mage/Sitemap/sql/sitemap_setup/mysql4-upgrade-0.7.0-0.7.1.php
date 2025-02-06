<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Sitemap
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('sitemap')}
    CHANGE `store_id` `store_id` smallint(5) unsigned NOT NULL;
ALTER TABLE {$this->getTable('sitemap')}
    ADD CONSTRAINT `FK_SITEMAP_STORE` FOREIGN KEY (`store_id`)
    REFERENCES {$this->getTable('core_store')} (`store_id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;
");

$installer->endSetup();

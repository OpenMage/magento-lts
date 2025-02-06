<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Rating
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('rating_title')}
    DROP FOREIGN KEY `FK_RATING_TITLE`;
");

$installer->run("
ALTER TABLE {$this->getTable('rating_title')}
    ADD CONSTRAINT `FK_RATING_TITLE` FOREIGN KEY (`rating_id`)
    REFERENCES {$this->getTable('rating')} (`rating_id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;
");

$installer->endSetup();

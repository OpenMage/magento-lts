<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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

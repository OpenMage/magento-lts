<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('report_event')}
    DROP INDEX `event_type_id`,
    ADD INDEX `IDX_EVENT_TYPE` (`event_type_id`);
ALTER TABLE {$this->getTable('report_event')}
    ADD CONSTRAINT `FK_REPORT_EVENT_TYPE` FOREIGN KEY (`event_type_id`)
    REFERENCES {$this->getTable('report_event_types')} (`event_type_id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;
ALTER TABLE {$this->getTable('report_event')}
    DROP INDEX `subject_id`,
    ADD INDEX `IDX_SUBJECT` (`subject_id`);
ALTER TABLE {$this->getTable('report_event')}
    DROP INDEX `object_id`,
    ADD INDEX `IDX_OBJECT` (`object_id`);
ALTER TABLE {$this->getTable('report_event')}
    DROP INDEX `subtype`,
    ADD INDEX `IDX_SUBTYPE` (`subtype`);
ALTER TABLE {$this->getTable('report_event')}
    DROP INDEX `store_id`;
ALTER TABLE {$this->getTable('report_event')}
    CHANGE `store_id` `store_id` smallint(5) unsigned NOT NULL;
ALTER TABLE {$this->getTable('report_event')}
    ADD CONSTRAINT `FK_REPORT_EVENT_STORE` FOREIGN KEY (`store_id`)
    REFERENCES {$this->getTable('core_store')} (`store_id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;
");
$installer->endSetup();

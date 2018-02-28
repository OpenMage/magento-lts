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
 * @category    Mage
 * @package     Mage_Reports
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * FOREIGN KEY update
 *
 * @category   Mage
 * @package    Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

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

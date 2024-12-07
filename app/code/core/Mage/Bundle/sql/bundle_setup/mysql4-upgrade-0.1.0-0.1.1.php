<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('catalog_product_bundle_option')}
    ADD CONSTRAINT `FK_CATALOG_PRODUCT_BUNDLE_OPTION_PARENT` FOREIGN KEY (`parent_id`)
    REFERENCES {$this->getTable('catalog_product_entity')} (`entity_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;
ALTER TABLE {$this->getTable('catalog_product_bundle_option_value')}
    ADD CONSTRAINT `FK_CATALOG_PRODUCT_BUNDLE_OPTION_VALUE_OPTION` FOREIGN KEY (`option_id`)
    REFERENCES {$this->getTable('catalog_product_bundle_option')} (`option_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;
ALTER TABLE {$this->getTable('catalog_product_bundle_selection')}
    ADD CONSTRAINT `FK_CATALOG_PRODUCT_BUNDLE_SELECTION_OPTION` FOREIGN KEY (`option_id`)
    REFERENCES {$this->getTable('catalog_product_bundle_option')} (`option_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_CATALOG_PRODUCT_BUNDLE_SELECTION_PRODUCT` FOREIGN KEY (`product_id`)
    REFERENCES {$this->getTable('catalog_product_entity')} (`entity_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;
");

$installer->endSetup();

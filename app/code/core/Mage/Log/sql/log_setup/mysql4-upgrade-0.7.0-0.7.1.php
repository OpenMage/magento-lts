<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->run("
ALTER TABLE {$this->getTable('log_summary')} ADD `store_id` SMALLINT( 5 ) UNSIGNED NOT NULL AFTER `summary_id` ;
ALTER TABLE {$this->getTable('log_customer')} ADD `store_id` SMALLINT( 5 ) UNSIGNED NOT NULL ;
ALTER TABLE {$this->getTable('log_visitor')} ADD `store_id` SMALLINT( 5 ) UNSIGNED NOT NULL ;
");

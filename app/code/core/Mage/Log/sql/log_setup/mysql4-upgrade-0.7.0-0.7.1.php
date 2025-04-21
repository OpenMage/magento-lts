<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

$this->run("
ALTER TABLE {$this->getTable('log_summary')} ADD `store_id` SMALLINT( 5 ) UNSIGNED NOT NULL AFTER `summary_id` ;
ALTER TABLE {$this->getTable('log_customer')} ADD `store_id` SMALLINT( 5 ) UNSIGNED NOT NULL ;
ALTER TABLE {$this->getTable('log_visitor')} ADD `store_id` SMALLINT( 5 ) UNSIGNED NOT NULL ;
");

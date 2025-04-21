<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

$this->run("
ALTER TABLE {$this->getTable('log_summary')} ADD `store_id` SMALLINT( 5 ) UNSIGNED NOT NULL AFTER `summary_id` ;
ALTER TABLE {$this->getTable('log_customer')} ADD `store_id` SMALLINT( 5 ) UNSIGNED NOT NULL ;
ALTER TABLE {$this->getTable('log_visitor')} ADD `store_id` SMALLINT( 5 ) UNSIGNED NOT NULL ;
");

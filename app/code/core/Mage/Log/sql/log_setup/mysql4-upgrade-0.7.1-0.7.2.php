<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

$this->run("
truncate table `{$this->getTable('log_quote')}`;
truncate table `{$this->getTable('log_url')}`;
truncate table `{$this->getTable('log_url_info')}`;
truncate table `{$this->getTable('log_visitor')}`;
truncate table `{$this->getTable('log_visitor_info')}`;
");

<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$installer->addAttribute('order', 'edit_increment', ['type' => 'int']);
$installer->addAttribute('order', 'original_increment_id', ['type' => 'varchar']);

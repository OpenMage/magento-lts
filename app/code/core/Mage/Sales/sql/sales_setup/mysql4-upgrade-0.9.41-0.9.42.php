<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

$this->startSetup();
$this->addAttribute('order', 'x_forwarded_for', ['type' => 'varchar']);
$this->endSetup();

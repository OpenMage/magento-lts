<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */
$this->startSetup();
$this->addAttribute('order', 'x_forwarded_for', ['type' => 'varchar']);
$this->endSetup();

<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 */
$this->startSetup();
$this->addAttribute('order', 'x_forwarded_for', ['type' => 'varchar']);
$this->endSetup();

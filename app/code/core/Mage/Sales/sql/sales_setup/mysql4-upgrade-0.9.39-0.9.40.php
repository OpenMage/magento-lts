<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 */
$this->startSetup();
$this->removeAttribute('order', 'giftcert_code');
$this->removeAttribute('order', 'giftcert_amount');
$this->removeAttribute('order', 'base_giftcert_amount');
$this->endSetup();

<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

$this->startSetup();
$this->removeAttribute('order', 'giftcert_code');
$this->removeAttribute('order', 'giftcert_amount');
$this->removeAttribute('order', 'base_giftcert_amount');
$this->endSetup();

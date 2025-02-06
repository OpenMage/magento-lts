<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_PaypalUk
 */

/**
 * PayPal Express (Payflow Edition) implementation for payment method instances
 *
 * @category   Mage
 * @package    Mage_PaypalUk
 */
class Mage_PaypalUk_Model_Express_Pro extends Mage_PaypalUk_Model_Pro
{
    /**
     * Api model type
     *
     * @var string
     */
    protected $_apiType = 'paypaluk/api_express_nvp';
}

<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_PaypalUk
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * PayPal Express (Payflow Edition) implementation for payment method instances
 *
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

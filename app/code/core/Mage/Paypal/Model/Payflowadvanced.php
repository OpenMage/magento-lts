<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payments Advanced gateway model
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Payflowadvanced extends Mage_Paypal_Model_Payflowlink
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = Mage_Paypal_Model_Config::METHOD_PAYFLOWADVANCED;

    /**
     * Type of block that generates method form
     *
     * @var string
     */
    protected $_formBlockType = 'paypal/payflow_advanced_form';

    /**
     * Type of block that displays method information
     *
     * @var string
     */
    protected $_infoBlockType = 'paypal/payflow_advanced_info';

    /**
     * Controller for callback urls
     *
     * @var string
     */
    protected $_callbackController = 'payflowadvanced';
}

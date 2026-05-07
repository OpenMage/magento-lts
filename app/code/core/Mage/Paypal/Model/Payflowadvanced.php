<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Payments Advanced gateway model
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Payflowadvanced extends Mage_Paypal_Model_Payflowlink
{
    /**
     * @inerhitDoc
     */
    protected $_code = Mage_Paypal_Model_Config::METHOD_PAYFLOWADVANCED;

    /**
     * Type of block that generates method form
     *
     * @inerhitDoc
     */
    protected $_formBlockType = 'paypal/payflow_advanced_form';

    /**
     * Type of block that displays method information
     *
     * @inerhitDoc
     */
    protected $_infoBlockType = 'paypal/payflow_advanced_info';

    /**
     * Controller for callback urls
     *
     * @var string
     */
    protected $_callbackController = 'payflowadvanced';
}

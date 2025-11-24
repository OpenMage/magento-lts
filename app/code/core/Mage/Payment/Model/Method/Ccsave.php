<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

/**
 * @package    Mage_Payment
 */
class Mage_Payment_Model_Method_Ccsave extends Mage_Payment_Model_Method_Cc
{
    protected $_code        = 'ccsave';

    protected $_canSaveCc   = true;

    protected $_formBlockType = 'payment/form_ccsave';

    protected $_infoBlockType = 'payment/info_ccsave';
}

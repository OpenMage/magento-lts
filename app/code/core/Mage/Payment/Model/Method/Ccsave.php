<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Payment
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Payment
 */
class Mage_Payment_Model_Method_Ccsave extends Mage_Payment_Model_Method_Cc
{
    protected $_code        = 'ccsave';
    protected $_canSaveCc   = true;
    protected $_formBlockType = 'payment/form_ccsave';
    protected $_infoBlockType = 'payment/info_ccsave';
}
